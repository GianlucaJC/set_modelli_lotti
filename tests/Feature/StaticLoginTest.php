<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StaticLoginTest extends TestCase
{
    public function test_the_login_page_is_displayed(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Set Modelli Lotti');
    }

    public function test_valid_api_credentials_create_a_session(): void
    {
        Http::fake([
            'https://localhost:8012/api_user_liof/api_login.php' => Http::response([
                'success' => true,
                'user_id' => 42,
                'username' => 'admin.liof',
                'email' => 'admin@liofilchem.net',
                'operatore' => 'Admin Liofilchem',
            ]),
        ]);

        $this->post('/login', [
            'username' => 'admin.liof',
            'password' => 'Password123!',
        ])->assertRedirect('/lotti')
            ->assertSessionHas('set_modelli_user.username', 'admin.liof');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://localhost:8012/api_user_liof/api_login.php'
                && $request['action'] === 'login'
                && $request['username'] === 'admin.liof'
                && $request['app_name'] === 'set_modelli_lotti';
        });
    }

    public function test_lots_page_requires_a_login(): void
    {
        $this->get('/lotti')->assertRedirect('/login');
    }
}