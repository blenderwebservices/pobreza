<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\PovertyEvaluation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthPortalController extends Controller
{
    public function showLogin(): View
    {
        return view('portal.auth-login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (! auth()->user()->is_active) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Su cuenta se encuentra desactivada por el administrador.',
                ]);
            }

            return redirect()->intended(route('portal.my-history'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Las credenciales ingresadas no coinciden con nuestros registros.',
            ]);
    }

    public function showRegister(): View
    {
        return view('portal.auth-register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:individuo,investigador'],
            'institution' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'institution' => $validated['institution'] ?: ($validated['role'] === 'investigador' ? 'Investigación Independiente' : 'Hogar Ciudadano'),
            'is_active' => true,
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('portal.my-history')
            ->with('status', 'Cuenta creada exitosamente. Bienvenido al Sistema de Cálculo y Proyección de Pobreza.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.home');
    }

    public function myHistory(): View
    {
        $user = auth()->user();

        $evaluations = PovertyEvaluation::with('household')
            ->when(! $user->isResearcher(), fn ($q) => $q->where('user_id', $user->id))
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->limit(25)
            ->get();

        $households = Household::withCount('evaluations')
            ->when(! $user->isResearcher(), fn ($q) => $q->where('user_id', $user->id))
            ->orderBy('name')
            ->get();

        return view('portal.my-history', [
            'user' => $user,
            'evaluations' => $evaluations,
            'households' => $households,
        ]);
    }
}
