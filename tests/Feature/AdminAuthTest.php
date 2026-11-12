<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_separate_admin_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk()->assertSee('Masuk');
        $this->get('/register')->assertNotFound();
    }

    public function test_valid_login_and_logout_protect_the_dashboard(): void
    {
        $user = User::factory()->create();
        $this->post('/admin/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
        $this->get('/admin')->assertOk()->assertSee('Dashboard');
        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_invalid_credentials_are_rejected_and_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', ['email' => 'tidakada@example.com', 'password' => 'wrong'])
                ->assertSessionHasErrors('email');
        }
        $this->post('/admin/login', ['email' => 'tidakada@example.com', 'password' => 'wrong'])->assertStatus(429);
        $this->assertGuest();
    }

    public function test_admin_command_creates_hashed_password_without_public_registration(): void
    {
        $this->artisan('admin:create', ['--name' => 'Pemilik', '--email' => 'pemilik@example.com'])
            ->expectsQuestion('Kata sandi (minimal 12 karakter)', 'Rahasia-aman-12345')
            ->expectsQuestion('Ulangi kata sandi', 'Rahasia-aman-12345')->assertExitCode(0);
        $user = User::where('email', 'pemilik@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('Rahasia-aman-12345', $user->password));
        $this->artisan('admin:create', ['--name' => 'Duplikat', '--email' => 'pemilik@example.com'])->assertExitCode(1);
    }
}
