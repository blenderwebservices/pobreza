<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\ImportBatch;
use App\Models\PovertyEvaluation;
use App\Models\PovertyLevel;
use App\Services\MassiveImportService;
use App\Services\PovertyCalculatorService;
use App\Services\PovertyForecastService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PovertyPortalController extends Controller
{
    public function home(PovertyCalculatorService $calculator, PovertyForecastService $forecaster): View
    {
        $levels = PovertyLevel::orderBy('display_order')->get();
        $latestYear = (int) (PovertyEvaluation::max('year') ?? date('Y'));
        $latestEvaluations = PovertyEvaluation::with('household')->where('year', $latestYear)->get();

        $avgGeneral = round((float) $latestEvaluations->avg('poverty_index_general'), 1);
        $avgFemale = round((float) $latestEvaluations->avg('poverty_index_female'), 1);
        $avgMale = round((float) $latestEvaluations->avg('poverty_index_male'), 1);

        $forecastOverview = $forecaster->analyzeAndForecast(
            PovertyEvaluation::orderBy('year')->get(),
            'general',
            3
        );

        return view('portal.home', [
            'levels' => $levels,
            'latestYear' => $latestYear,
            'avgGeneral' => $avgGeneral,
            'avgFemale' => $avgFemale,
            'avgMale' => $avgMale,
            'levelGeneral' => $calculator->classifyScore($avgGeneral),
            'householdsCount' => Household::count(),
            'evaluationsCount' => PovertyEvaluation::count(),
            'forecastOverview' => $forecastOverview,
        ]);
    }

    public function calculator(Request $request, PovertyCalculatorService $calculator): View
    {
        $defaults = [
            'folio_code' => 'HOG-' . date('Y') . '-105',
            'household_name' => auth()->check() ? ('Hogar de ' . auth()->user()->name) : 'Hogar de Estudio Demo',
            'state' => 'Ciudad de México',
            'municipality' => 'Iztapalapa',
            'zone_type' => 'urbana',
            'year' => (int) date('Y'),
            'total_members' => 4,
            'female_members' => 2,
            'male_members' => 2,
            'dependent_children' => 1,
            'female_headed' => 1,
            'monthly_income_female' => 4800,
            'monthly_income_male' => 6400,
            'monthly_expense_food' => 5200,
            'monthly_expense_housing_services' => 3300,
            'monthly_expense_health_education' => 1700,
            'female_unpaid_care_hours_weekly' => 34,
            'male_unpaid_care_hours_weekly' => 10,
            'housing_floor_wall_deprivation' => 25,
            'housing_overcrowding_deprivation' => 35,
            'basic_services_deprivation' => 20,
            'education_lag_deprivation' => 30,
            'health_security_deprivation' => 45,
            'food_insecurity_deprivation' => 35,
        ];

        $input = array_merge($defaults, $request->old() ?: $request->all());
        $input['monthly_income_total'] = (float) ($input['monthly_income_female'] ?? 0) + (float) ($input['monthly_income_male'] ?? 0);
        $input['monthly_expense_total'] = (float) ($input['monthly_expense_food'] ?? 0)
            + (float) ($input['monthly_expense_housing_services'] ?? 0)
            + (float) ($input['monthly_expense_health_education'] ?? 0);

        $result = session('calculation_result') ?? $calculator->calculate($input);
        $levels = PovertyLevel::orderBy('display_order')->get();
        $households = Household::orderBy('name')->get();

        return view('portal.calculator', [
            'input' => $input,
            'result' => $result,
            'levels' => $levels,
            'households' => $households,
            'savedEvaluationId' => session('saved_evaluation_id'),
        ]);
    }

    public function calculate(Request $request, PovertyCalculatorService $calculator): RedirectResponse
    {
        $validated = $request->validate([
            'household_id' => ['nullable', 'exists:households,id'],
            'household_name' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:120'],
            'municipality' => ['required', 'string', 'max:120'],
            'zone_type' => ['required', 'in:urbana,rural'],
            'year' => ['required', 'integer', 'min:1990', 'max:2100'],
            'total_members' => ['required', 'integer', 'min:1', 'max:30'],
            'female_members' => ['required', 'integer', 'min:0', 'max:30'],
            'male_members' => ['required', 'integer', 'min:0', 'max:30'],
            'dependent_children' => ['required', 'integer', 'min:0', 'max:25'],
            'female_headed' => ['nullable', 'boolean'],
            'monthly_income_female' => ['required', 'numeric', 'min:0'],
            'monthly_income_male' => ['required', 'numeric', 'min:0'],
            'monthly_expense_food' => ['required', 'numeric', 'min:0'],
            'monthly_expense_housing_services' => ['required', 'numeric', 'min:0'],
            'monthly_expense_health_education' => ['required', 'numeric', 'min:0'],
            'female_unpaid_care_hours_weekly' => ['required', 'numeric', 'min:0', 'max:112'],
            'male_unpaid_care_hours_weekly' => ['required', 'numeric', 'min:0', 'max:112'],
            'housing_floor_wall_deprivation' => ['required', 'integer', 'min:0', 'max:100'],
            'housing_overcrowding_deprivation' => ['required', 'integer', 'min:0', 'max:100'],
            'basic_services_deprivation' => ['required', 'integer', 'min:0', 'max:100'],
            'education_lag_deprivation' => ['required', 'integer', 'min:0', 'max:100'],
            'health_security_deprivation' => ['required', 'integer', 'min:0', 'max:100'],
            'food_insecurity_deprivation' => ['required', 'integer', 'min:0', 'max:100'],
            'save_record' => ['nullable', 'boolean'],
        ]);

        $validated['female_headed'] = $request->boolean('female_headed');
        $validated['monthly_income_total'] = (float) $validated['monthly_income_female'] + (float) $validated['monthly_income_male'];
        $validated['monthly_expense_total'] = (float) $validated['monthly_expense_food']
            + (float) $validated['monthly_expense_housing_services']
            + (float) $validated['monthly_expense_health_education'];

        $result = $calculator->calculate($validated);
        $savedEvaluationId = null;

        if ($request->boolean('save_record')) {
            $household = ! empty($validated['household_id'])
                ? Household::findOrFail($validated['household_id'])
                : Household::create([
                    'user_id' => auth()->id(),
                    'folio_code' => 'HOG-' . strtoupper(substr(md5(uniqid('', true)), 0, 6)),
                    'name' => $validated['household_name'],
                    'subject_type' => $validated['total_members'] > 1 ? 'hogar' : 'individuo',
                    'state' => $validated['state'],
                    'municipality' => $validated['municipality'],
                    'zone_type' => $validated['zone_type'],
                    'total_members' => $validated['total_members'],
                    'female_members' => $validated['female_members'],
                    'male_members' => $validated['male_members'],
                    'dependent_children' => $validated['dependent_children'],
                    'female_headed' => $validated['female_headed'],
                ]);

            $evaluation = PovertyEvaluation::updateOrCreate(
                [
                    'household_id' => $household->id,
                    'year' => $validated['year'],
                    'quarter' => 'T4',
                ],
                [
                    'user_id' => auth()->id(),
                    'evaluation_source' => 'cuestionario',
                    'monthly_income_total' => $validated['monthly_income_total'],
                    'monthly_income_female' => $validated['monthly_income_female'],
                    'monthly_income_male' => $validated['monthly_income_male'],
                    'monthly_expense_total' => $validated['monthly_expense_total'],
                    'monthly_expense_food' => $validated['monthly_expense_food'],
                    'monthly_expense_housing_services' => $validated['monthly_expense_housing_services'],
                    'monthly_expense_health_education' => $validated['monthly_expense_health_education'],
                    'female_unpaid_care_hours_weekly' => $validated['female_unpaid_care_hours_weekly'],
                    'male_unpaid_care_hours_weekly' => $validated['male_unpaid_care_hours_weekly'],
                    'housing_floor_wall_deprivation' => $validated['housing_floor_wall_deprivation'],
                    'housing_overcrowding_deprivation' => $validated['housing_overcrowding_deprivation'],
                    'basic_services_deprivation' => $validated['basic_services_deprivation'],
                    'education_lag_deprivation' => $validated['education_lag_deprivation'],
                    'health_security_deprivation' => $validated['health_security_deprivation'],
                    'food_insecurity_deprivation' => $validated['food_insecurity_deprivation'],
                ]
            );

            $savedEvaluationId = $evaluation->id;
        }

        return redirect()
            ->route('calculator.index')
            ->withInput($validated)
            ->with('calculation_result', $result)
            ->with('saved_evaluation_id', $savedEvaluationId);
    }

    public function temporal(Request $request, PovertyForecastService $forecaster, PovertyCalculatorService $calculator): View
    {
        $households = Household::withCount('evaluations')->orderBy('name')->get();
        $selectedHouseholdId = $request->query('household_id');
        $segment = in_array($request->query('segment'), ['general', 'femenina', 'masculina'], true)
            ? $request->query('segment')
            : 'general';
        $horizon = max(1, min(5, (int) $request->query('horizon', 3)));

        $selectedHousehold = $selectedHouseholdId ? Household::find($selectedHouseholdId) : null;

        $evaluationsQuery = PovertyEvaluation::with('household')->orderBy('year');
        if ($selectedHousehold) {
            $evaluationsQuery->where('household_id', $selectedHousehold->id);
        }

        $evaluations = $evaluationsQuery->get();
        $analysis = $forecaster->analyzeAndForecast($evaluations, $segment, $horizon);
        $analysisGeneral = $forecaster->analyzeAndForecast($evaluations, 'general', $horizon);
        $analysisFemale = $forecaster->analyzeAndForecast($evaluations, 'femenina', $horizon);
        $analysisMale = $forecaster->analyzeAndForecast($evaluations, 'masculina', $horizon);

        $latestScore = ! empty($analysis['historical'])
            ? end($analysis['historical'])['score']
            : 45.0;
        $currentLevel = $calculator->classifyScore($latestScore);

        return view('portal.temporal', [
            'households' => $households,
            'selectedHousehold' => $selectedHousehold,
            'segment' => $segment,
            'horizon' => $horizon,
            'analysis' => $analysis,
            'analysisGeneral' => $analysisGeneral,
            'analysisFemale' => $analysisFemale,
            'analysisMale' => $analysisMale,
            'currentLevel' => $currentLevel,
        ]);
    }

    public function importPage(): View
    {
        $batches = ImportBatch::with('user')->latest()->take(10)->get();

        return view('portal.import', [
            'batches' => $batches,
            'sampleCsv' => MassiveImportService::sampleCsvContent(),
        ]);
    }

    public function processImport(Request $request, MassiveImportService $importer): RedirectResponse
    {
        $validated = $request->validate([
            'source_name' => ['required', 'string', 'max:255'],
            'csv_file' => ['nullable', 'file', 'mimes:csv,txt', 'max:4096'],
            'csv_content' => ['nullable', 'string'],
        ]);

        $csvString = '';
        $filename = 'lote_enigh_manual.csv';

        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $csvString = (string) file_get_contents($file->getRealPath());
            $filename = $file->getClientOriginalName();
        } elseif (! empty($validated['csv_content'])) {
            $csvString = $validated['csv_content'];
        } else {
            return back()->withErrors(['csv_content' => 'Proporcione un archivo CSV o pegue los datos del estudio oficial.']);
        }

        $batch = $importer->importCsvString(
            $csvString,
            $validated['source_name'],
            $filename,
            auth()->id()
        );

        return redirect()
            ->route('import.index')
            ->with('status', "Lote {$batch->batch_code} importado exitosamente: {$batch->processed_rows} evaluaciones procesadas.");
    }

    public function downloadTemplate(): Response
    {
        return response(MassiveImportService::sampleCsvContent(), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_enigh_pobreza.csv"',
        ]);
    }
}
