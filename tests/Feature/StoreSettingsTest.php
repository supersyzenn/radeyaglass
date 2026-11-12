<?php

namespace Tests\Feature;

use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_or_change_settings(): void
    {
        $this->get('/admin/pengaturan')->assertRedirect('/admin/login');
        $this->patch('/admin/pengaturan', ['name' => 'Hacked'])->assertRedirect('/admin/login');
        $this->assertDatabaseCount('store_settings', 0);
    }

    public function test_admin_can_save_contacts_and_about_with_normalized_whatsapp(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get('/admin/pengaturan')->assertOk();
        $this->patch('/admin/pengaturan', ['name' => 'Radeya Glass', 'email' => 'radeya@example.com', 'whatsapp' => '0812-3456-7890', 'about' => 'Informasi asli toko.'])
            ->assertRedirect('/admin/pengaturan')->assertSessionHasNoErrors();
        $this->assertSame('6281234567890', StoreSetting::current()->whatsapp);
        $this->get('/kontak')->assertOk()->assertSee('mailto:radeya@example.com', false)->assertSee('wa.me/6281234567890', false)->assertDontSee('/admin');
        $this->get('/tentang')->assertOk()->assertSee('Informasi asli toko.')->assertDontSee('/admin');
    }

    public function test_empty_contacts_are_allowed_but_invalid_contacts_are_rejected(): void
    {
        $this->actingAs(User::factory()->create());
        $this->patch('/admin/pengaturan', ['name' => 'Radeya Glass', 'email' => '', 'whatsapp' => '', 'about' => ''])->assertSessionHasNoErrors();
        $this->patch('/admin/pengaturan', ['name' => 'Radeya Glass', 'email' => 'bukan-email', 'whatsapp' => 'bukan-nomor'])->assertSessionHasErrors(['email', 'whatsapp']);
        $this->get('/kontak')->assertOk()->assertDontSee('mailto:')->assertDontSee('wa.me/');
    }
}
