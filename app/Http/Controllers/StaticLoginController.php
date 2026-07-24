<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class StaticLoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        try {
            $client = Http::acceptJson()->timeout(config('set-modelli.login_api.timeout'));

            if (! config('set-modelli.login_api.verify_ssl')) {
                $client = $client->withoutVerifying();
            }

            $response = $client->post(config('set-modelli.login_api.url'), [
                    'api_token' => config('set-modelli.login_api.shared_secret'),
                    'action' => 'login',
                    'username' => $credentials['username'],
                    'password' => $credentials['password'],
                    'app_name' => config('set-modelli.login_api.app_name'),
                ]);
        } catch (\Illuminate\Http\Client\ConnectionException) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors(['username' => 'Servizio di autenticazione non disponibile.']);
        }

        if (! $response->successful() || $response->json('success') !== true) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors(['username' => 'Credenziali non valide.']);
        }

        $request->session()->regenerate();
        $request->session()->put('set_modelli_user', [
            'id' => $response->json('user_id'),
            'username' => $response->json('username', $credentials['username']),
            'email' => $response->json('email'),
            'operatore' => $response->json('operatore'),
        ]);

        return redirect()->route('lots.edit');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}