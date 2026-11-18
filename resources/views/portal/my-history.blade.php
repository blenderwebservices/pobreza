@extends('portal.layout')

@section('title', 'Mi Panel y Control de Usuario · SIPP')

@section('content')
    <section class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span class="badge" style="background: var(--accent);">
                    Rol Activo: {{ \App\Models\User::roleLabels()[$user->role] ?? $user->role }}
                </span>
                <h1 style="margin: 0.4rem 0 0.2rem 0;">{{ $user->name }}</h1>
                <div style="color: var(--text-muted); font-size: 0.92rem;">
                    {{ $user->email }} · {{ $user->institution ?? 'Sin institución asignada' }}
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="/admin" class="btn btn-primary">
                    Abrir Panel Administrativo FilamentPHP (/admin) ↗
                </a>
                <a href="{{ route('calculator.index') }}" class="btn btn-outline">
                    + Nueva Evaluación
                </a>
                <form action="{{ route('portal.logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-outline">Cerrar Sesión</button>
                </form>
            </div>
        </div>
    </section>

    <section class="card" aria-labelledby="my-evals-heading">
        <h2 id="my-evals-heading" style="margin-block-start: 0;">
            Evaluaciones Socioeconómicas Registradas ({{ $evaluations->count() }})
        </h2>

        <div tabindex="0" role="region" aria-labelledby="user-evals-caption" class="table-wrapper">
            <table style="--label-2: 'Año'; --label-3: 'IP General'; --label-4: 'IP Femenina'; --label-5: 'IP Masculina'; --label-6: 'Acción';">
                <caption id="user-evals-caption">Historial de evaluaciones del usuario o institución</caption>
                <thead>
                    <tr>
                        <th scope="col">Hogar / Sujeto</th>
                        <th scope="col">Año</th>
                        <th scope="col">IP General</th>
                        <th scope="col">IP Femenina</th>
                        <th scope="col">IP Masculina</th>
                        <th scope="col">Análisis Temporal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($evaluations as $ev)
                        <tr>
                            <th scope="row">{{ $ev->household?->name ?? 'Hogar' }}</th>
                            <td>{{ $ev->year }}</td>
                            <td><strong>{{ number_format($ev->poverty_index_general, 1) }} pts</strong> ({{ $ev->level_slug_general }})</td>
                            <td style="color: #be123c;">{{ number_format($ev->poverty_index_female, 1) }} pts</td>
                            <td style="color: #0369a1;">{{ number_format($ev->poverty_index_male, 1) }} pts</td>
                            <td>
                                <a href="{{ route('temporal.index', ['household_id' => $ev->household_id]) }}">
                                    Ver Pronóstico →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
