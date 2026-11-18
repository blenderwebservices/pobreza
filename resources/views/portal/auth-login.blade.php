@extends('portal.layout')

@section('title', 'Iniciar Sesión · Control de Usuarios SIPP')

@section('content')
    <div class="grid-2" style="max-inline-size: 960px; margin-inline: auto; align-items: start;">
        <section class="card" aria-labelledby="login-heading">
            <h1 id="login-heading" style="margin-block-start: 0; font-size: 1.5rem;">Iniciar Sesión en SIPP</h1>
            <p style="color: var(--text-muted); font-size: 0.92rem;">
                Acceda como <strong>Individuo</strong>, <strong>Investigador / Institución</strong> o <strong>Administrador</strong> para gestionar evaluaciones y acceder al panel FilamentPHP.
            </p>

            @if ($errors->any())
                <div style="background: var(--danger-bg); color: var(--danger); padding: 0.85rem; border-radius: 0.5rem; margin-block-end: 1rem;">
                    @foreach ($errors->all() as $error)
                        <div><span role="img" aria-label="Error">❌</span> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('portal.login.submit') }}" method="POST">
                @csrf
                <div class="field">
                    <label for="email">Correo Electrónico <span aria-hidden="true">*</span></label>
                    <span id="email-hint" class="hint">Formato: usuario@institucion.gob.mx</span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', 'admin@pobreza.gob.mx') }}"
                        required
                        autocomplete="username"
                        autocorrect="off"
                        autocapitalize="none"
                        spellcheck="false"
                        aria-describedby="email-hint"
                        data-error="email-error"
                    >
                    <div id="email-error" class="error-msg">
                        <span role="img" aria-label="Error">❌</span> Ingrese un correo electrónico válido (ej. usuario@dominio.mx).
                    </div>
                </div>

                <div class="field">
                    <div style="display: flex; justify-content: space-between; align-items: center; inline-size: 100%;">
                        <label for="password">Contraseña <span aria-hidden="true">*</span></label>
                        <button
                            id="toggle-password"
                            type="button"
                            aria-pressed="false"
                            aria-label="Mostrar contraseña"
                            aria-describedby="toggle-warning"
                            style="background: none; border: none; color: var(--accent); font-weight: 600; cursor: pointer; padding: 0.25rem;"
                        >
                            Mostrar
                        </button>
                    </div>
                    <span id="toggle-warning" class="visually-hidden">Advertencia: esto mostrará su contraseña en la pantalla.</span>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        value="password123"
                        required
                        autocomplete="current-password"
                        data-error="password-error"
                    >
                    <div id="password-error" class="error-msg">
                        <span role="img" aria-label="Error">❌</span> Ingrese su contraseña de acceso.
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.5rem; margin-block-end: 1.25rem;">
                    <input type="checkbox" id="remember" name="remember" value="1" checked>
                    <label for="remember">Mantener sesión activa en este dispositivo</label>
                </div>

                <button type="submit" class="btn btn-primary" style="inline-size: 100%;">
                    Ingresar a la Plataforma
                </button>
            </form>
        </section>

        <section class="card" aria-labelledby="demo-accounts-heading">
            <h2 id="demo-accounts-heading" style="margin-block-start: 0; font-size: 1.25rem;">
                Cuentas de Prueba por Rol de Usuario
            </h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Seleccione cualquier perfil para autocompletar las credenciales (contraseña: <code>password123</code>):
            </p>

            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <button type="button" class="btn btn-outline demo-fill-btn" data-email="admin@pobreza.gob.mx" style="justify-content: flex-start; text-align: start;">
                    <div>
                        <strong>1. Administradora General (Control Total + Usuarios)</strong><br>
                        <span style="font-size: 0.82rem; color: var(--text-muted);">admin@pobreza.gob.mx · Acceso completo a /admin y gestión de roles</span>
                    </div>
                </button>

                <button type="button" class="btn btn-outline demo-fill-btn" data-email="investigador@pobreza.gob.mx" style="justify-content: flex-start; text-align: start;">
                    <div>
                        <strong>2. Investigador / Institución (Análisis y ENIGH)</strong><br>
                        <span style="font-size: 0.82rem; color: var(--text-muted);">investigador@pobreza.gob.mx · Series temporales, pronósticos y políticas</span>
                    </div>
                </button>

                <button type="button" class="btn btn-outline demo-fill-btn" data-email="ciudadano@pobreza.gob.mx" style="justify-content: flex-start; text-align: start;">
                    <div>
                        <strong>3. Individuo / Hogar Ciudadano (Estatus Personal)</strong><br>
                        <span style="font-size: 0.82rem; color: var(--text-muted);">ciudadano@pobreza.gob.mx · Seguimiento de su propio hogar</span>
                    </div>
                </button>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    const toggleBtn = document.getElementById('toggle-password');
    const pwdInput = document.getElementById('password');
    if (toggleBtn && pwdInput) {
        toggleBtn.addEventListener('click', () => {
            const shown = pwdInput.type === 'text';
            pwdInput.type = shown ? 'password' : 'text';
            toggleBtn.setAttribute('aria-pressed', String(!shown));
            toggleBtn.textContent = shown ? 'Mostrar' : 'Ocultar';
        });
    }

    document.querySelectorAll('.demo-fill-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            const emailInput = document.getElementById('email');
            if (emailInput && pwdInput) {
                emailInput.value = btn.dataset.email;
                pwdInput.value = 'password123';
                emailInput.focus();
            }
        });
    });
</script>
@endpush
