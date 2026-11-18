@extends('portal.layout')

@section('title', 'Registro de Usuario · SIPP')

@section('content')
    <section class="card" style="max-inline-size: 640px; margin-inline: auto;" aria-labelledby="register-heading">
        <h1 id="register-heading" style="margin-block-start: 0; font-size: 1.5rem;">Crear Cuenta en SIPP</h1>
        <p style="color: var(--text-muted); font-size: 0.92rem;">
            Regístrese como individuo para evaluar y dar seguimiento a su hogar, o como investigador/institución para analizar cohortes multi-anuales.
        </p>

        <form action="{{ route('portal.register.submit') }}" method="POST">
            @csrf
            <div class="field">
                <label for="name">Nombre Completo <span aria-hidden="true">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" autocomplete="name" required data-error="err-name">
                <div id="err-name" class="error-msg">
                    <span role="img" aria-label="Error">❌</span> Ingrese su nombre completo.
                </div>
            </div>

            <div class="field">
                <label for="email">Correo Electrónico <span aria-hidden="true">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email" autocorrect="off" autocapitalize="none" spellcheck="false" required data-error="err-reg-email">
                <div id="err-reg-email" class="error-msg">
                    <span role="img" aria-label="Error">❌</span> Ingrese un correo electrónico válido.
                </div>
            </div>

            <div class="field">
                <span style="font-weight: 600;">Perfil de Usuario Objetivo <span aria-hidden="true">*</span></span>
                <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-block-start: 0.25rem;">
                    <div style="display: inline-flex; align-items: center; gap: 0.45rem;">
                        <input type="radio" id="role_ind" name="role" value="individuo" checked>
                        <label for="role_ind">Individuo / Hogar Personal</label>
                    </div>
                    <div style="display: inline-flex; align-items: center; gap: 0.45rem;">
                        <input type="radio" id="role_inv" name="role" value="investigador">
                        <label for="role_inv">Investigador / Institución Académica o Pública</label>
                    </div>
                </div>
            </div>

            <div class="field">
                <label for="institution">Institución u Organización (Opcional)</label>
                <input type="text" id="institution" name="institution" value="{{ old('institution') }}" autocomplete="organization">
            </div>

            <div class="grid-2" style="gap: 1rem;">
                <div class="field">
                    <label for="password">Contraseña (mínimo 8 caracteres) <span aria-hidden="true">*</span></label>
                    <input type="password" id="password" name="password" minlength="8" autocomplete="new-password" required data-error="err-pwd">
                    <div id="err-pwd" class="error-msg">
                        <span role="img" aria-label="Error">❌</span> La contraseña debe tener al menos 8 caracteres.
                    </div>
                </div>
                <div class="field">
                    <label for="password_confirmation">Confirmar Contraseña <span aria-hidden="true">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" minlength="8" autocomplete="new-password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="inline-size: 100%;">
                Crear Cuenta e Iniciar
            </button>
        </form>
    </section>
@endsection
