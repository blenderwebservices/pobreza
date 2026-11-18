<?php

namespace Tests\Feature;

use App\Models\Household;
use App\Models\PovertyEvaluation;
use App\Models\User;
use App\Services\MassiveImportService;
use App\Services\PovertyCalculatorService;
use App\Services\PovertyForecastService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PovertyApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_public_portal_pages_load_successfully(): void
    {
        $this->get('/')->assertStatus(200)->assertSee('Escala Progresiva del Índice de Pobreza');
        $this->get('/calculadora')->assertStatus(200)->assertSee('Calculadora Multidimensional del Índice de Pobreza');
        $this->get('/analisis-temporal')->assertStatus(200)->assertSee('Módulo de Análisis Temporal Dinámico y Pronósticos');
        $this->get('/importar')->assertStatus(200)->assertSee('Módulo de Importación Masiva de Estudios Oficiales (ENIGH)');
        $this->get('/login')->assertStatus(200);
        $this->get('/registro')->assertStatus(200);
    }

    public function test_bmi_style_calculator_classifies_progressive_levels_and_segments_gender(): void
    {
        $calculator = app(PovertyCalculatorService::class);

        // Caso de pobreza alta con brecha de género femenina
        $highPoverty = $calculator->calculate([
            'zone_type' => 'rural',
            'total_members' => 5,
            'female_members' => 3,
            'male_members' => 2,
            'dependent_children' => 2,
            'female_headed' => true,
            'monthly_income_female' => 1100,
            'monthly_income_male' => 2900,
            'monthly_expense_food' => 3800,
            'monthly_expense_housing_services' => 1200,
            'monthly_expense_health_education' => 800,
            'female_unpaid_care_hours_weekly' => 45,
            'male_unpaid_care_hours_weekly' => 8,
            'housing_floor_wall_deprivation' => 80,
            'housing_overcrowding_deprivation' => 75,
            'basic_services_deprivation' => 85,
            'education_lag_deprivation' => 70,
            'health_security_deprivation' => 85,
            'food_insecurity_deprivation' => 80,
        ]);

        $this->assertGreaterThanOrEqual(60.0, $highPoverty['poverty_index_general']);
        $this->assertGreaterThan($highPoverty['poverty_index_male'], $highPoverty['poverty_index_female']);
        $this->assertContains($highPoverty['level_general']['slug'], ['alta', 'muy_alta']);
        $this->assertNotEmpty($highPoverty['recommended_policies']);

        // Caso de pobreza muy baja
        $lowPoverty = $calculator->calculate([
            'zone_type' => 'urbana',
            'total_members' => 2,
            'female_members' => 1,
            'male_members' => 1,
            'dependent_children' => 0,
            'female_headed' => false,
            'monthly_income_female' => 22000,
            'monthly_income_male' => 22000,
            'monthly_expense_food' => 6000,
            'monthly_expense_housing_services' => 8000,
            'monthly_expense_health_education' => 4000,
            'female_unpaid_care_hours_weekly' => 12,
            'male_unpaid_care_hours_weekly' => 12,
            'housing_floor_wall_deprivation' => 0,
            'housing_overcrowding_deprivation' => 0,
            'basic_services_deprivation' => 0,
            'education_lag_deprivation' => 0,
            'health_security_deprivation' => 0,
            'food_insecurity_deprivation' => 0,
        ]);

        $this->assertLessThan(20.0, $lowPoverty['poverty_index_general']);
        $this->assertEquals('muy_baja', $lowPoverty['level_general']['slug']);
    }

    public function test_temporal_forecast_service_detects_improving_and_worsening_trends(): void
    {
        $forecaster = app(PovertyForecastService::class);

        $improvingHousehold = Household::where('folio_code', 'HOG-CDMX-001')->firstOrFail();
        $improvingAnalysis = $forecaster->analyzeAndForecast($improvingHousehold->evaluations, 'general', 3);

        $this->assertLessThan(0, $improvingAnalysis['slope']);
        $this->assertStringContainsString('Mejorando', $improvingAnalysis['trend_status']);
        $this->assertCount(3, $improvingAnalysis['forecasts']);

        $worseningHousehold = Household::where('folio_code', 'HOG-CHIS-002')->firstOrFail();
        $worseningAnalysis = $forecaster->analyzeAndForecast($worseningHousehold->evaluations, 'general', 3);

        $this->assertGreaterThan(0, $worseningAnalysis['slope']);
        $this->assertStringContainsString('Empeorando', $worseningAnalysis['trend_status']);
    }

    public function test_massive_enigh_csv_import_processes_households_and_evaluations(): void
    {
        $importer = app(MassiveImportService::class);
        $csv = MassiveImportService::sampleCsvContent();

        $batch = $importer->importCsvString($csv, 'ENIGH Prueba Automatizada', 'test_enigh.csv');

        $this->assertEquals('completado', $batch->status);
        $this->assertEquals(7, $batch->processed_rows);
        $this->assertDatabaseHas('households', ['folio_code' => 'ENIGH-OAX-101']);
    }

    public function test_user_roles_and_filament_admin_access_control(): void
    {
        $admin = User::where('email', 'admin@pobreza.gob.mx')->firstOrFail();
        $researcher = User::where('email', 'investigador@pobreza.gob.mx')->firstOrFail();
        $citizen = User::where('email', 'ciudadano@pobreza.gob.mx')->firstOrFail();

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($researcher->isResearcher());
        $this->assertFalse($citizen->isAdmin());

        $this->actingAs($admin)->get('/admin')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/users')->assertStatus(200);

        $this->flushSession();
        $this->actingAs($citizen)->get('/admin/users')->assertStatus(403);
        $this->actingAs($citizen)->get('/mi-historial')->assertStatus(200);
    }
}
