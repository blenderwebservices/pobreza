@extends('portal.layout')

@section('title', 'Calculadora de Índice de Pobreza (Tipo IMC) · SIPP')

@section('content')
    <div style="margin-block-end: 1.5rem;">
        <h1 style="margin: 0 0 0.35rem 0;">Calculadora Multidimensional del Índice de Pobreza (Simplicidad Tipo IMC)</h1>
        <p style="margin: 0; color: var(--text-muted);">
            Complete el cuestionario socioeconómico para obtener el Índice de Pobreza (0–100), su clasificación progresiva no binaria, la segmentación por género (general, femenina y masculina) y el marco técnico de política pública recomendado.
        </p>
    </div>

    @if ($savedEvaluationId)
        <div class="alert-status" role="status">
            ✅ La evaluación fue guardada exitosamente en la base de datos histórica (ID #{{ $savedEvaluationId }}). Puede consultar su proyección multi-anual en <a href="{{ route('temporal.index') }}">Análisis Temporal y Pronósticos</a>.
        </div>
    @endif

    <div class="grid-2" style="align-items: start;">
        <!-- Columna 1: Resultados Tipo IMC + Segmentación + Políticas -->
        <div>
            <section class="card" aria-labelledby="bmi-result-heading" style="border-block-start: 6px solid {{ $result['level_general']['badge_hex'] }};">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                    <h2 id="bmi-result-heading" style="margin: 0; font-size: 1.25rem;">
                        Resultado del Índice de Pobreza (IP)
                    </h2>
                    <span class="badge" style="background: {{ $result['level_general']['badge_hex'] }};">
                        {{ $result['level_general']['bmi_analogy_label'] }}
                    </span>
                </div>

                <div style="display: flex; align-items: baseline; gap: 1rem; margin-block: 1rem;">
                    <div style="font-size: 3.25rem; font-weight: 900; line-height: 1; color: {{ $result['level_general']['badge_hex'] }};">
                        {{ number_format($result['poverty_index_general'], 1) }}
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 1.2rem;">
                            {{ $result['level_general']['name'] }}
                        </div>
                        <div style="color: var(--text-muted); font-size: 0.9rem;">
                            Ingreso per cápita: <strong>${{ number_format($result['per_capita_income'], 2) }} MXN</strong> · Relación Ingreso/Gasto: <strong>{{ number_format($result['income_to_expense_ratio'], 2) }}x</strong>
                        </div>
                    </div>
                </div>

                <!-- Indicador Visual Tipo IMC -->
                <div style="position: relative; margin-block: 1.25rem 1.75rem;">
                    <div class="bmi-scale-bar">
                        <div class="bmi-segment" style="background: #15803d;">0–19.9<br>Muy Baja</div>
                        <div class="bmi-segment" style="background: #0369a1;">20–39.9<br>Baja</div>
                        <div class="bmi-segment" style="background: #b45309;">40–59.9<br>Moderada</div>
                        <div class="bmi-segment" style="background: #c2410c;">60–79.9<br>Alta</div>
                        <div class="bmi-segment" style="background: #991b1b;">80–100<br>Muy Alta</div>
                    </div>
                    <div style="position: absolute; inset-inline-start: clamp(2%, {{ $result['poverty_index_general'] }}%, 96%); inset-block-start: -8px; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 0.75rem; font-weight: 800; padding: 0.15rem 0.45rem; border-radius: 0.35rem; box-shadow: 0 2px 6px rgba(0,0,0,0.25);">
                        ▼ {{ number_format($result['poverty_index_general'], 1) }}
                    </div>
                </div>

                <div style="background: var(--surface-alt); border-radius: 0.5rem; padding: 1rem; margin-block-end: 1rem;">
                    <h3 style="margin: 0 0 0.35rem 0; font-size: 0.98rem;">¿Qué significa este nivel de pobreza?</h3>
                    <p style="margin: 0 0 0.65rem 0; font-size: 0.92rem;">
                        {{ $result['level_general']['description'] }}
                    </p>
                    <h3 style="margin: 0 0 0.35rem 0; font-size: 0.98rem;">Implicaciones Socioeconómicas Directas</h3>
                    <p style="margin: 0; font-size: 0.92rem; color: var(--text-muted);">
                        {{ $result['level_general']['implications'] }}
                    </p>
                </div>

                <!-- Segmentación General, Femenina y Masculina -->
                <h3 style="margin: 1.25rem 0 0.65rem 0; font-size: 1.05rem;">
                    Segmentación Intrahogar por Género
                </h3>
                <div class="grid-3" style="gap: 0.75rem;">
                    <div style="border: 1px solid var(--border); border-inline-start: 4px solid {{ $result['level_general']['badge_hex'] }}; padding: 0.85rem; border-radius: 0.5rem;">
                        <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Pobreza General</div>
                        <div style="font-size: 1.5rem; font-weight: 800;">{{ number_format($result['poverty_index_general'], 1) }} pts</div>
                        <div style="font-size: 0.8rem; color: {{ $result['level_general']['badge_hex'] }}; font-weight: 700;">
                            {{ $result['level_general']['name'] }}
                        </div>
                    </div>

                    <div style="border: 1px solid var(--border); border-inline-start: 4px solid {{ $result['level_female']['badge_hex'] }}; padding: 0.85rem; border-radius: 0.5rem;">
                        <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Pobreza Femenina</div>
                        <div style="font-size: 1.5rem; font-weight: 800;">{{ number_format($result['poverty_index_female'], 1) }} pts</div>
                        <div style="font-size: 0.8rem; color: {{ $result['level_female']['badge_hex'] }}; font-weight: 700;">
                            {{ $result['level_female']['name'] }}
                        </div>
                    </div>

                    <div style="border: 1px solid var(--border); border-inline-start: 4px solid {{ $result['level_male']['badge_hex'] }}; padding: 0.85rem; border-radius: 0.5rem;">
                        <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Pobreza Masculina</div>
                        <div style="font-size: 1.5rem; font-weight: 800;">{{ number_format($result['poverty_index_male'], 1) }} pts</div>
                        <div style="font-size: 0.8rem; color: {{ $result['level_male']['badge_hex'] }}; font-weight: 700;">
                            {{ $result['level_male']['name'] }}
                        </div>
                    </div>
                </div>
            </section>

            <!-- Recomendaciones Técnicas de Política Pública -->
            <section class="card" aria-labelledby="policies-heading">
                <h2 id="policies-heading" style="margin-block-start: 0; font-size: 1.2rem;">
                    Marco Técnico Ideal de Política Pública y Mitigación
                </h2>
                <p style="color: var(--text-muted); font-size: 0.88rem; margin-block-end: 1rem;">
                    Acciones técnicas requeridas según el nivel de pobreza y las brechas detectadas, formuladas como estándar ideal de intervención independientemente de la voluntad política coyuntural.
                </p>

                @foreach ($result['recommended_policies'] as $policy)
                    <article style="border: 1px solid var(--border); border-radius: 0.5rem; padding: 1rem; margin-block-end: 0.85rem; background: var(--surface);">
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-block-end: 0.35rem;">
                            <strong style="color: var(--accent); font-size: 0.98rem;">{{ $policy['title'] }}</strong>
                            <span class="badge" style="background: #334155; font-size: 0.72rem;">
                                Prioridad {{ strtoupper($policy['priority']) }} · Segmento: {{ strtoupper($policy['target_segment']) }}
                            </span>
                        </div>
                        <p style="margin: 0.35rem 0; font-size: 0.9rem;">
                            <strong>Acción Técnica:</strong> {{ $policy['technical_action'] }}
                        </p>
                        <p style="margin: 0.35rem 0; font-size: 0.85rem; color: var(--text-muted);">
                            <strong>Justificación Técnica:</strong> {{ $policy['ideal_framework_rationale'] }}
                        </p>
                        <div style="font-size: 0.82rem; font-weight: 700; color: var(--success);">
                            Impacto esperado: {{ $policy['expected_impact'] }}
                        </div>
                    </article>
                @endforeach
            </section>
        </div>

        <!-- Columna 2: Cuestionario Integrado Multidimensional -->
        <form action="{{ route('calculator.calculate') }}" method="POST" aria-labelledby="questionnaire-heading">
            @csrf
            <h2 id="questionnaire-heading" class="visually-hidden">Cuestionario Socioeconómico Multidimensional</h2>

            <fieldset>
                <legend>1. Datos del Hogar o Individuo y Segmentación Demográfica</legend>

                <div class="field">
                    <label for="household_id">Vincular a un Hogar Existente (Opcional para seguimiento multi-anual)</label>
                    <select id="household_id" name="household_id">
                        <option value="">-- Crear nuevo registro o calcular en modo libre --</option>
                        @foreach ($households as $hh)
                            <option value="{{ $hh->id }}" @selected(($input['household_id'] ?? '') == $hh->id)>
                                {{ $hh->folio_code }} · {{ $hh->name }} ({{ $hh->state }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid-2" style="gap: 1rem;">
                    <div class="field">
                        <label for="household_name">Nombre del Hogar o Persona <span aria-hidden="true">*</span></label>
                        <input type="text" id="household_name" name="household_name" value="{{ $input['household_name'] }}" required data-error="err-hh-name">
                        <div id="err-hh-name" class="error-msg">
                            <span role="img" aria-label="Error">❌</span> Ingrese el nombre identificativo del hogar o persona.
                        </div>
                    </div>

                    <div class="field">
                        <label for="year">Año del Periodo Evaluado <span aria-hidden="true">*</span></label>
                        <input type="number" id="year" name="year" min="1990" max="2100" value="{{ $input['year'] }}" required data-error="err-year">
                        <div id="err-year" class="error-msg">
                            <span role="img" aria-label="Error">❌</span> Indique un año válido entre 1990 y 2100.
                        </div>
                    </div>
                </div>

                <div class="grid-2" style="gap: 1rem;">
                    <div class="field">
                        <label for="state">Entidad Federativa <span aria-hidden="true">*</span></label>
                        <input type="text" id="state" name="state" value="{{ $input['state'] }}" required>
                    </div>
                    <div class="field">
                        <label for="municipality">Municipio / Alcaldía <span aria-hidden="true">*</span></label>
                        <input type="text" id="municipality" name="municipality" value="{{ $input['municipality'] }}" required>
                    </div>
                </div>

                <!-- 2 mutually exclusive options -> visible radio buttons (modern-web-guidance: forms) -->
                <div class="field">
                    <span style="font-weight: 600;">Ámbito Geográfico <span aria-hidden="true">*</span></span>
                    <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-block-start: 0.25rem;">
                        <div style="display: inline-flex; align-items: center; gap: 0.45rem;">
                            <input type="radio" id="zone_urbana" name="zone_type" value="urbana" @checked(($input['zone_type'] ?? 'urbana') === 'urbana')>
                            <label for="zone_urbana">Urbana (Línea Bienestar: \$4,560/mes)</label>
                        </div>
                        <div style="display: inline-flex; align-items: center; gap: 0.45rem;">
                            <input type="radio" id="zone_rural" name="zone_type" value="rural" @checked(($input['zone_type'] ?? '') === 'rural')>
                            <label for="zone_rural">Rural (Línea Bienestar: \$3,290/mes)</label>
                        </div>
                    </div>
                </div>

                <div class="grid-2" style="gap: 1rem;">
                    <div class="field">
                        <label for="total_members">Total de Integrantes <span aria-hidden="true">*</span></label>
                        <input type="number" id="total_members" name="total_members" min="1" max="30" value="{{ $input['total_members'] }}" required>
                    </div>
                    <div class="field">
                        <label for="dependent_children">Menores o Dependientes <span aria-hidden="true">*</span></label>
                        <input type="number" id="dependent_children" name="dependent_children" min="0" max="25" value="{{ $input['dependent_children'] }}" required>
                    </div>
                    <div class="field">
                        <label for="female_members">Integrantes Mujeres <span aria-hidden="true">*</span></label>
                        <input type="number" id="female_members" name="female_members" min="0" max="30" value="{{ $input['female_members'] }}" required>
                    </div>
                    <div class="field">
                        <label for="male_members">Integrantes Hombres <span aria-hidden="true">*</span></label>
                        <input type="number" id="male_members" name="male_members" min="0" max="30" value="{{ $input['male_members'] }}" required>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" id="female_headed" name="female_headed" value="1" @checked(!empty($input['female_headed']))>
                    <label for="female_headed">Hogar con jefatura económica o responsabilidad principal femenina</label>
                </div>
            </fieldset>

            <fieldset>
                <legend>2. Proporción de Ingresos, Gastos y Trabajo de Cuidados (MXN / Mes)</legend>

                <div class="grid-2" style="gap: 1rem;">
                    <div class="field">
                        <label for="monthly_income_female">Ingreso Mensual Aportado por Mujeres (\$) <span aria-hidden="true">*</span></label>
                        <span id="hint-inc-f" class="hint">Sueldos, ventas, apoyos o pensiones de mujeres</span>
                        <input type="number" step="0.01" min="0" id="monthly_income_female" name="monthly_income_female" value="{{ $input['monthly_income_female'] }}" aria-describedby="hint-inc-f" required>
                    </div>
                    <div class="field">
                        <label for="monthly_income_male">Ingreso Mensual Aportado por Hombres (\$) <span aria-hidden="true">*</span></label>
                        <span id="hint-inc-m" class="hint">Sueldos, ventas, apoyos o pensiones de hombres</span>
                        <input type="number" step="0.01" min="0" id="monthly_income_male" name="monthly_income_male" value="{{ $input['monthly_income_male'] }}" aria-describedby="hint-inc-m" required>
                    </div>
                </div>

                <div class="grid-3" style="gap: 0.85rem;">
                    <div class="field">
                        <label for="monthly_expense_food">Gasto en Alimentos (\$) <span aria-hidden="true">*</span></label>
                        <input type="number" step="0.01" min="0" id="monthly_expense_food" name="monthly_expense_food" value="{{ $input['monthly_expense_food'] }}" required>
                    </div>
                    <div class="field">
                        <label for="monthly_expense_housing_services">Gasto Vivienda y Servicios (\$) <span aria-hidden="true">*</span></label>
                        <input type="number" step="0.01" min="0" id="monthly_expense_housing_services" name="monthly_expense_housing_services" value="{{ $input['monthly_expense_housing_services'] }}" required>
                    </div>
                    <div class="field">
                        <label for="monthly_expense_health_education">Gasto Salud y Educación (\$) <span aria-hidden="true">*</span></label>
                        <input type="number" step="0.01" min="0" id="monthly_expense_health_education" name="monthly_expense_health_education" value="{{ $input['monthly_expense_health_education'] }}" required>
                    </div>
                </div>

                <div class="grid-2" style="gap: 1rem;">
                    <div class="field">
                        <label for="female_unpaid_care_hours_weekly">Horas Semanales Cuidado No Remunerado (Mujeres) <span aria-hidden="true">*</span></label>
                        <input type="number" min="0" max="112" id="female_unpaid_care_hours_weekly" name="female_unpaid_care_hours_weekly" value="{{ $input['female_unpaid_care_hours_weekly'] }}" required>
                    </div>
                    <div class="field">
                        <label for="male_unpaid_care_hours_weekly">Horas Semanales Cuidado No Remunerado (Hombres) <span aria-hidden="true">*</span></label>
                        <input type="number" min="0" max="112" id="male_unpaid_care_hours_weekly" name="male_unpaid_care_hours_weekly" value="{{ $input['male_unpaid_care_hours_weekly'] }}" required>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>3. Condiciones Físicas de la Vivienda y Carencias Sociales (Escala 0 = Óptimo a 100 = Carencia Crítica)</legend>

                <div class="grid-2" style="gap: 1rem;">
                    <div class="field">
                        <label for="housing_floor_wall_deprivation">Precariedad en Pisos, Techos y Muros (0–100)</label>
                        <span id="hint-flr" class="hint">0 = Concreto/firme, 50 = Lámina/regular, 100 = Tierra/desecho</span>
                        <input type="number" min="0" max="100" id="housing_floor_wall_deprivation" name="housing_floor_wall_deprivation" value="{{ $input['housing_floor_wall_deprivation'] }}" aria-describedby="hint-flr" required>
                    </div>
                    <div class="field">
                        <label for="housing_overcrowding_deprivation">Nivel de Hacinamiento (0–100)</label>
                        <span id="hint-ovr" class="hint">0 = ≤1.5 pers/cuarto, 50 = 2.5 pers/cuarto, 100 = >4 pers/cuarto</span>
                        <input type="number" min="0" max="100" id="housing_overcrowding_deprivation" name="housing_overcrowding_deprivation" value="{{ $input['housing_overcrowding_deprivation'] }}" aria-describedby="hint-ovr" required>
                    </div>
                    <div class="field">
                        <label for="basic_services_deprivation">Carencia en Agua, Drenaje y Electricidad (0–100)</label>
                        <input type="number" min="0" max="100" id="basic_services_deprivation" name="basic_services_deprivation" value="{{ $input['basic_services_deprivation'] }}" required>
                    </div>
                    <div class="field">
                        <label for="education_lag_deprivation">Rezago Educativo Familiar (0–100)</label>
                        <input type="number" min="0" max="100" id="education_lag_deprivation" name="education_lag_deprivation" value="{{ $input['education_lag_deprivation'] }}" required>
                    </div>
                    <div class="field">
                        <label for="health_security_deprivation">Carencia de Salud y Seguridad Social (0–100)</label>
                        <input type="number" min="0" max="100" id="health_security_deprivation" name="health_security_deprivation" value="{{ $input['health_security_deprivation'] }}" required>
                    </div>
                    <div class="field">
                        <label for="food_insecurity_deprivation">Inseguridad Alimentaria (0–100)</label>
                        <input type="number" min="0" max="100" id="food_insecurity_deprivation" name="food_insecurity_deprivation" value="{{ $input['food_insecurity_deprivation'] }}" required>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.5rem; margin-block-end: 1.25rem; padding: 0.75rem; background: var(--surface-alt); border-radius: 0.5rem;">
                    <input type="checkbox" id="save_record" name="save_record" value="1" checked>
                    <label for="save_record" style="font-weight: 600;">
                        Guardar esta evaluación en la serie histórica para cálculo de tendencias y pronósticos
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="inline-size: 100%; font-size: 1.05rem;">
                    Calcular Índice de Pobreza y Emitir Recomendaciones
                </button>
            </fieldset>
        </form>
    </div>
@endsection
