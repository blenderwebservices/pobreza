@extends('portal.layout')

@section('title', 'Importación Masiva ENIGH · SIPP')

@section('content')
    <div style="margin-block-end: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="margin: 0 0 0.35rem 0;">Módulo de Importación Masiva de Estudios Oficiales (ENIGH)</h1>
            <p style="margin: 0; color: var(--text-muted);">
                Cargue microdatos provenientes de la Encuesta Nacional de Ingresos y Gastos de los Hogares (ENIGH) u otros censos institucionales para procesar el Índice de Pobreza general, femenino y masculino por lote.
            </p>
        </div>
        <a href="{{ route('import.template') }}" class="btn btn-outline">
            ⬇ Descargar Plantilla CSV Oficial (ENIGH)
        </a>
    </div>

    <div class="grid-2" style="align-items: start;">
        <section class="card" aria-labelledby="import-form-heading">
            <h2 id="import-form-heading" style="margin-block-start: 0;">Cargar Lote de Datos Oficiales</h2>

            @if ($errors->any())
                <div style="background: var(--danger-bg); color: var(--danger); padding: 0.9rem; border-radius: 0.5rem; margin-block-end: 1rem; border: 1px solid #fecaca;">
                    @foreach ($errors->all() as $err)
                        <div><span role="img" aria-label="Error">❌</span> {{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('import.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="field">
                    <label for="source_name">Nombre del Estudio o Encuesta Oficial <span aria-hidden="true">*</span></label>
                    <input type="text" id="source_name" name="source_name" value="Encuesta Nacional de Ingresos y Gastos de los Hogares (ENIGH 2025)" required data-error="err-src">
                    <div id="err-src" class="error-msg">
                        <span role="img" aria-label="Error">❌</span> Indique el nombre oficial del estudio o fuente estadística.
                    </div>
                </div>

                <div class="field">
                    <label for="csv_file">Subir Archivo CSV (Opcional si pega los datos abajo)</label>
                    <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt">
                </div>

                <div class="field">
                    <label for="csv_content">O Pegar Microdatos CSV Directamente</label>
                    <span id="hint-csv" class="hint">Se precarga una muestra oficial ENIGH multi-anual lista para importar</span>
                    <textarea id="csv_content" name="csv_content" rows="10" aria-describedby="hint-csv" style="font-family: ui-monospace, monospace; font-size: 0.84rem;">{{ $sampleCsv }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="inline-size: 100%;">
                    Procesar Lote Masivo y Calcular Índices
                </button>
            </form>
        </section>

        <section class="card" aria-labelledby="batches-heading">
            <h2 id="batches-heading" style="margin-block-start: 0;">Historial de Lotes Importados</h2>

            <div tabindex="0" role="region" aria-labelledby="batches-caption" class="table-wrapper">
                <table style="--label-2: 'Estudio Oficial'; --label-3: 'Filas'; --label-4: 'IP General'; --label-5: 'IP Femenina';">
                    <caption id="batches-caption">Lotes oficiales procesados en el sistema</caption>
                    <thead>
                        <tr>
                            <th scope="col">Código Lote</th>
                            <th scope="col">Estudio Oficial</th>
                            <th scope="col">Filas</th>
                            <th scope="col">IP General</th>
                            <th scope="col">IP Femenina</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($batches as $b)
                            <tr>
                                <th scope="row">{{ $b->batch_code }}</th>
                                <td>{{ $b->source_name }}</td>
                                <td>{{ $b->processed_rows }}</td>
                                <td><strong>{{ number_format((float) ($b->summary['avg_general'] ?? 0), 1) }} pts</strong></td>
                                <td style="color: #be123c; font-weight: 600;">{{ number_format((float) ($b->summary['avg_female'] ?? 0), 1) }} pts</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
