<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('TalentFlow Recruitment');
        $response->assertSee('Alamat Email');
        $response->assertSee('Kata Sandi');
        $response->assertDontSee('Pilih Role untuk Masuk Instan');
    }

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee('Pendaftaran Akun Kandidat');
        $response->assertSee('Nama Depan');
        $response->assertSee('Nomor HP / WhatsApp');
    }

    public function test_candidate_can_register_and_is_redirected_to_dashboard(): void
    {
        $payload = [
            'first_name' => 'Budi',
            'last_name' => 'Santoso',
            'email' => 'budi.santoso@example.com',
            'phone_number' => '08123456789',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $response = $this->post(route('register'), $payload);

        $response->assertRedirect(route('kadidat.dashboard'));
        $this->assertAuthenticated();

        // Ensure User and Candidate records are created
        $this->assertDatabaseHas('users', [
            'email' => 'budi.santoso@example.com',
            'name' => 'Budi Santoso',
        ]);

        $this->assertDatabaseHas('candidates', [
            'email' => 'budi.santoso@example.com',
            'first_name' => 'Budi',
            'last_name' => 'Santoso',
        ]);
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'admin@recruitment.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_incorrect_password(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'admin@recruitment.test',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
