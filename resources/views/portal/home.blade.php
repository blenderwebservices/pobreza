@extends('portal.layout')

@section('title', 'Inicio · Sistema de Cálculo y Proyección de Pobreza (SIPP)')

@section('content')
    <section class="card" style="background: linear-gradient(135deg, #0f172a 0%, #134e4a 100%); color: #ffffff; border: none; padding: 2.25rem;">
        <div class="grid-2" style="align-items: center;">
            <div>
                <span class="badge" style="background: rgba(255,255,255,0.18); margin-block-end: 0.75rem;">
                    Índice Multidimensional Progresivo · Escala 0 a 100 (Tipo IMC)
                </span>
                <h1 style="font-size: clamp(1.75rem, 3vw, 2.5rem); margin: 0 0 0.75rem 0; line-height: 1.2;">
                    Cálculo, Análisis Temporal y Pronóstico de Pobreza con Marco de Política Pública
                </h1>
                <p style="color: #cbd5e1; font-size: 1.05rem; margin-block-end: 1.5rem;">
                    Evalúe el nivel de pobreza de individuos, hogares y grupos comunitarios de forma tan intuitiva como una calculadora de Índice de Masa Corporal (IMC), con desglose de <strong>pobreza general, femenina y masculina</strong>, evolución multi-anual y recomendaciones técnicas ideales.
                </p>
                <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                    <a href="{{ route('calculator.index') }}" class="btn btn-primary" style="background: #14b8a6; color: #042f2e;">
                        Calcular Índice de Pobreza Ahora →
                    </a>
                    <a href="{{ route('temporal.index') }}" class="btn btn-outline" style="background: rgba(255,255,255,0.1); color: #fff; border-color: rgba(255,255,255,0.35);">
                        Ver Series Históricas y Pronósticos
                    </a>
                    <a href="/admin" class="btn btn-outline" style="background: transparent; color: #99f6e4; border-color: #99f6e4;">
                        Panel Administrativo FilamentPHP
                    </a>
                </div>
            </div>

            <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.18); border-radius: var(--radius); padding: 1.5rem;">
                <h2 style="margin-block-start: 0; font-size: 1.15rem; color: #99f6e4;">
                    Panorama Nacional Evaluado ({{ $latestYear }})
                </h2>
                <div class="grid-3" style="gap: 0.75rem; margin-block-end: 1rem;">
                    <div style="background: rgba(15,23,42,0.55); padding: 0.9rem; border-radius: 0.5rem;">
                        <div style="font-size: 0.8rem; color: #cbd5e1;">IP General Promedio</div>
                        <div style="font-size: 1.65rem; font-weight: 800;">{{ number_format($avgGeneral, 1) }}</div>
                        <div style="font-size: 0.78rem; color: #5eead4;">Escala 0–100 pts</div>
                    </div>
                    <div style="background: rgba(15,23,42,0.55); padding: 0.9rem; border-radius: 0.5rem;">
                        <div style="font-size: 0.8rem; color: #fda4af;">IP Pobreza Femenina</div>
                        <div style="font-size: 1.65rem; font-weight: 800; color: #fecdd3;">{{ number_format($avgFemale, 1) }}</div>
                        <div style="font-size: 0.78rem; color: #fda4af;">Brecha +{{ number_format($avgFemale - $avgMale, 1) }} pts</div>
                    </div>
                    <div style="background: rgba(15,23,42,0.55); padding: 0.9rem; border-radius: 0.5rem;">
                        <div style="font-size: 0.8rem; color: #bae6fd;">IP Pobreza Masculina</div>
                        <div style="font-size: 1.65rem; font-weight: 800; color: #e0f2fe;">{{ number_format($avgMale, 1) }}</div>
                        <div style="font-size: 0.78rem; color: #bae6fd;">Segmento Masculino</div>
                    </div>
                </div>
                <p style="margin: 0; font-size: 0.88rem; color: #e2e8f0;">
                    <strong>Clasificación Promedio Actual:</strong> {{ $levelGeneral['name'] }} ({{ $householdsCount }} hogares longitudinales · {{ $evaluationsCount }} evaluaciones históricas).
                </p>
            </div>
        </div>
    </section>

    <section class="card" aria-labelledby="levels-heading">
        <div style="display: flex; justify-content: space-between; align-items: baseline; flex-wrap: wrap; gap: 0.5rem;">
            <h2 id="levels-heading" style="margin-block-start: 0;">
                Escala Progresiva del Índice de Pobreza (No Binaria · Simplicidad Tipo IMC)
            </h2>
            <span style="color: var(--text-muted); font-size: 0.9rem;">
                0.0 puntos = Bienestar Pleno · 100.0 puntos = Privación Extrema Total
            </span>
        </div>

        <div class="bmi-scale-bar" role="img" aria-label="Escala visual de 5 niveles progresivos de pobreza desde Muy Baja hasta Muy Alta">
            <div class="bmi-segment" style="background: #15803d;">0–19.9 · Muy Baja</div>
            <div class="bmi-segment" style="background: #0369a1;">20–39.9 · Baja</div>
            <div class="bmi-segment" style="background: #b45309;">40–59.9 · Moderada</div>
            <div class="bmi-segment" style="background: #c2410c;">60–79.9 · Alta</div>
            <div class="bmi-segment" style="background: #991b1b;">80–100 · Muy Alta</div>
        </div>

        <div class="grid-5" style="margin-block-start: 1.25rem;">
            @foreach ($levels as $lvl)
                <article style="border: 1px solid var(--border); border-block-start: 5px solid {{ $lvl->badge_hex }}; border-radius: 0.6rem; padding: 1rem; background: var(--surface);">
                    <div style="font-size: 0.78rem; font-weight: 700; color: {{ $lvl->badge_hex }}; text-transform: uppercase;">
                        {{ $lvl->bmi_analogy_label }}
                    </div>
                    <h3 style="margin: 0.35rem 0 0.5rem 0; font-size: 1.02rem;">
                        {{ $lvl->name }}
                    </h3>
                    <p style="font-size: 0.86rem; color: var(--text-muted); margin-block-end: 0.65rem;">
                        {{ $lvl->description }}
                    </p>
                    <div style="font-size: 0.82rem; background: var(--surface-alt); padding: 0.6rem; border-radius: 0.4rem;">
                        <strong>Implicaciones:</strong> {{ $lvl->implications }}
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="grid-3">
        <article class="card">
            <h3 style="margin-block-start: 0;">1. Cuestionario e Importación ENIGH</h3>
            <p style="color: var(--text-muted); font-size: 0.94rem;">
                Capture datos directamente mediante nuestro cuestionario multidimensional o importe lotes masivos CSV provenientes de la Encuesta Nacional de Ingresos y Gastos de los Hogares (ENIGH).
            </p>
            <a href="{{ route('import.index') }}" class="btn btn-outline">Ir a Importación Masiva →</a>
        </article>

        <article class="card">
            <h3 style="margin-block-start: 0;">2. Segmentación por Género Intrahogar</h3>
            <p style="color: var(--text-muted); font-size: 0.94rem;">
                Mida de forma simultánea la pobreza general del hogar, la pobreza femenina (incorporando autonomía de ingresos y carga de cuidados) y la pobreza masculina.
            </p>
            <a href="{{ route('calculator.index') }}" class="btn btn-outline">Abrir Calculadora Segmentada →</a>
        </article>

        <article class="card">
            <h3 style="margin-block-start: 0;">3. Pronósticos y Política Pública</h3>
            <p style="color: var(--text-muted); font-size: 0.94rem;">
                Evalúe múltiples años, identifique si la situación ha mejorado o empeorado, proyecte los próximos 3 años y consulte el marco técnico ideal de política pública.
            </p>
            <a href="{{ route('temporal.index') }}" class="btn btn-outline">Explorar Pronósticos →</a>
        </article>
    </section>
@endsection
