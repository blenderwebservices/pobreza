@extends('portal.layout')

@section('title', 'Análisis Temporal y Pronósticos · SIPP')

@section('content')
    <div style="margin-block-end: 1.5rem;">
        <h1 style="margin: 0 0 0.35rem 0;">Módulo de Análisis Temporal Dinámico y Pronósticos a Futuro</h1>
        <p style="margin: 0; color: var(--text-muted);">
            Evalúe la evolución histórica multi-anual de un individuo, hogar o cohorte, verifique si la situación ha mejorado o empeorado y proyecte la trayectoria futura bajo escenario inercial vs. mitigación con política pública.
        </p>
    </div>

    <!-- Filtros de Serie Temporal (wrapped in <search> per modern-web-guidance: forms) -->
    <search class="card">
        <form action="{{ route('temporal.index') }}" method="GET" aria-label="Filtros de serie histórica y pronóstico">
            <div class="grid-3" style="align-items: end;">
                <div class="field" style="margin-block-end: 0;">
                    <label for="household_id">Hogar, Individuo o Cohorte Evaluada</label>
                    <select id="household_id" name="household_id">
                        <option value="">Promedio General de Todos los Hogares Registrados</option>
                        @foreach ($households as $hh)
                            <option value="{{ $hh->id }}" @selected(optional($selectedHousehold)->id === $hh->id)>
                                {{ $hh->folio_code }} — {{ $hh->name }} ({{ $hh->state }}, {{ $hh->evaluations_count }} años)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field" style="margin-block-end: 0;">
                    <label for="segment">Segmentación de Análisis</label>
                    <select id="segment" name="segment">
                        <option value="general" @selected($segment === 'general')>Índice de Pobreza General del Hogar</option>
                        <option value="femenina" @selected($segment === 'femenina')>Pobreza Femenina Intrahogar</option>
                        <option value="masculina" @selected($segment === 'masculina')>Pobreza Masculina Intrahogar</option>
                    </select>
                </div>

                <div style="display: flex; gap: 0.75rem; align-items: center;">
                    <div class="field" style="margin-block-end: 0; flex: 1;">
                        <label for="horizon">Horizonte Pronóstico</label>
                        <select id="horizon" name="horizon">
                            <option value="2" @selected($horizon === 2)>Próximos 2 años</option>
                            <option value="3" @selected($horizon === 3)>Próximos 3 años</option>
                            <option value="5" @selected($horizon === 5)>Próximos 5 años</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-block-start: 1.4rem;">
                        Actualizar Proyección
                    </button>
                </div>
            </div>
        </form>
    </search>

    <!-- Diagnóstico de Tendencia y Narrativa Prospectiva -->
    <section class="card" aria-labelledby="trend-diagnosis-heading">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; margin-block-end: 1rem;">
            <h2 id="trend-diagnosis-heading" style="margin: 0;">
                Diagnóstico Longitudinal: {{ $selectedHousehold ? $selectedHousehold->name : 'Cohorte Agregada Nacional' }}
            </h2>
            <span class="badge" style="background: {{ $analysis['slope'] <= -0.6 ? '#15803d' : ($analysis['slope'] >= 0.6 ? '#b91c1c' : '#b45309') }};">
                Tendencia: {{ $analysis['trend_status'] }} (Pendiente anual: {{ $analysis['slope'] > 0 ? '+' : '' }}{{ number_format($analysis['slope'], 2) }} pts/año)
            </span>
        </div>

        <p style="font-size: 1.02rem; background: var(--surface-alt); padding: 1rem 1.25rem; border-inline-start: 4px solid var(--accent); border-radius: 0.4rem; margin-block-end: 1.25rem;">
            {{ $analysis['narrative'] }}
        </p>

        <!-- Gráfica SVG Responsiva de Evolución Histórica + Pronóstico -->
        @php
            $hist = $analysis['historical'];
            $fc = $analysis['forecasts'];
            $allPoints = [];
            foreach ($hist as $h) {
                $allPoints[] = ['label' => (string) $h['year'], 'inertial' => $h['score'], 'mitigated' => $h['score'], 'is_forecast' => false];
            }
            foreach ($fc as $f) {
                $allPoints[] = ['label' => $f['year'] . '*', 'inertial' => $f['inertial_score'], 'mitigated' => $f['mitigated_score'], 'is_forecast' => true];
            }
            $totalPts = max(1, count($allPoints));
        @endphp

        @if ($totalPts > 1)
            <div style="border: 1px solid var(--border); border-radius: 0.6rem; padding: 1.25rem; background: #fff; margin-block-end: 1.5rem;">
                <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; margin-block-end: 0.75rem; font-size: 0.88rem;">
                    <strong>Curva de Evolución Histórica y Pronóstico (* Años Proyectados)</strong>
                    <div style="display: flex; gap: 1.25rem; flex-wrap: wrap;">
                        <span><strong style="color: #0f766e;">●──</strong> Trayectoria Histórica e Inercial</span>
                        <span><strong style="color: #16a34a;">●- -</strong> Escenario con Mitigación de Política Pública</span>
                    </div>
                </div>

                <svg viewBox="0 0 800 260" style="inline-size: 100%; block-size: auto; max-block-size: 300px;" role="img" aria-label="Gráfica de tendencia histórica y pronóstico del índice de pobreza">
                    <!-- Líneas guía horizontales (Escala 0 a 100) -->
                    @foreach ([0, 20, 40, 60, 80, 100] as $yVal)
                        @php $yPos = 220 - ($yVal * 1.9); @endphp
                        <line x1="55" y1="{{ $yPos }}" x2="770" y2="{{ $yPos }}" stroke="#e2e8f0" stroke-width="1" />
                        <text x="45" y="{{ $yPos + 4 }}" text-anchor="end" font-size="11" fill="#64748b">{{ $yVal }}</text>
                    @endforeach

                    @php
                        $stepX = 700 / max(1, $totalPts - 1);
                        $polyInertial = [];
                        $polyMitigated = [];
                        foreach ($allPoints as $idx => $pt) {
                            $cx = round(60 + ($idx * $stepX), 1);
                            $cyI = round(220 - ($pt['inertial'] * 1.9), 1);
                            $cyM = round(220 - ($pt['mitigated'] * 1.9), 1);
                            $polyInertial[] = "{$cx},{$cyI}";
                            $polyMitigated[] = "{$cx},{$cyM}";
                        }
                    @endphp

                    <polyline fill="none" stroke="#16a34a" stroke-width="3" stroke-dasharray="6,4" points="{{ implode(' ', $polyMitigated) }}" />
                    <polyline fill="none" stroke="#0f766e" stroke-width="3.5" points="{{ implode(' ', $polyInertial) }}" />

                    @foreach ($allPoints as $idx => $pt)
                        @php
                            $cx = round(60 + ($idx * $stepX), 1);
                            $cyI = round(220 - ($pt['inertial'] * 1.9), 1);
                            $cyM = round(220 - ($pt['mitigated'] * 1.9), 1);
                        @endphp
                        <circle cx="{{ $cx }}" cy="{{ $cyI }}" r="5.5" fill="{{ $pt['is_forecast'] ? '#d97706' : '#0f766e' }}" />
                        <text x="{{ $cx }}" y="{{ $cyI - 10 }}" text-anchor="middle" font-size="11" font-weight="bold" fill="#0f172a">
                            {{ number_format($pt['inertial'], 1) }}
                        </text>
                        @if ($pt['is_forecast'])
                            <circle cx="{{ $cx }}" cy="{{ $cyM }}" r="4.5" fill="#16a34a" />
                            <text x="{{ $cx }}" y="{{ $cyM + 16 }}" text-anchor="middle" font-size="10.5" font-weight="bold" fill="#15803d">
                                {{ number_format($pt['mitigated'], 1) }}
                            </text>
                        @endif
                        <text x="{{ $cx }}" y="245" text-anchor="middle" font-size="12" font-weight="600" fill="#334155">
                            {{ $pt['label'] }}
                        </text>
                    @endforeach
                </svg>
            </div>
        @endif

        <!-- Tabla Responsiva Accesible (modern-web-guidance: responsive-table) -->
        <div tabindex="0" role="region" aria-labelledby="temporal-table-caption" class="table-wrapper">
            <table style="--label-2: 'IP General'; --label-3: 'IP Femenina'; --label-4: 'IP Masculina'; --label-5: 'Ingreso Promedio'; --label-6: 'Gasto Promedio';">
                <caption id="temporal-table-caption">
                    Serie Histórica Evaluada y Comparativa por Segmento de Género
                </caption>
                <thead>
                    <tr>
                        <th scope="col">Año</th>
                        <th scope="col">IP General</th>
                        <th scope="col">IP Femenina</th>
                        <th scope="col">IP Masculina</th>
                        <th scope="col">Ingreso Mensual</th>
                        <th scope="col">Gasto Mensual</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($analysisGeneral['historical'] as $h)
                        <tr>
                            <th scope="row">{{ $h['year'] }} (Histórico)</th>
                            <td><strong>{{ number_format($h['general'], 1) }} pts</strong></td>
                            <td style="color: #be123c; font-weight: 600;">{{ number_format($h['female'], 1) }} pts</td>
                            <td style="color: #0369a1; font-weight: 600;">{{ number_format($h['male'], 1) }} pts</td>
                            <td>\${{ number_format($h['income_avg'], 2) }}</td>
                            <td>\${{ number_format($h['expense_avg'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <!-- Pronóstico Futuro Detallado -->
    <section class="card" aria-labelledby="forecast-cards-heading">
        <h2 id="forecast-cards-heading" style="margin-block-start: 0;">
            Pronóstico Prospectivo a {{ $horizon }} Años (Escenario Inercial vs. Mitigación Técnica)
        </h2>
        <div class="grid-3">
            @foreach ($analysis['forecasts'] as $fcItem)
                <article style="border: 1px solid var(--border); border-radius: 0.6rem; padding: 1.25rem; background: var(--surface);">
                    <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 700;">
                        PROYECCIÓN AÑO {{ $fcItem['year'] }}
                    </div>
                    <div style="margin-block: 0.75rem; padding-block-end: 0.75rem; border-block-end: 1px dashed var(--border);">
                        <div style="font-size: 0.82rem; color: var(--text-muted);">Pronóstico Inercial (Sin cambios):</div>
                        <div style="font-size: 1.75rem; font-weight: 800; color: {{ $fcItem['inertial_badge_hex'] }};">
                            {{ number_format($fcItem['inertial_score'], 1) }} pts
                        </div>
                        <span class="badge" style="background: {{ $fcItem['inertial_badge_hex'] }};">
                            {{ $fcItem['inertial_level_name'] }}
                        </span>
                    </div>
                    <div>
                        <div style="font-size: 0.82rem; color: var(--text-muted);">Pronóstico con Política de Mitigación:</div>
                        <div style="font-size: 1.45rem; font-weight: 800; color: {{ $fcItem['mitigated_badge_hex'] }};">
                            {{ number_format($fcItem['mitigated_score'], 1) }} pts
                        </div>
                        <div style="font-size: 0.84rem; font-weight: 600; color: var(--success);">
                            → {{ $fcItem['mitigated_level_name'] }}
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
