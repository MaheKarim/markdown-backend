<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Document;

class AdminWebTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that admin login page is accessible
     */
    public function test_admin_login_page_is_accessible(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Admin Login');
    }

    /**
     * Test admin login with correct credentials
     */
    public function test_admin_login_with_correct_credentials(): void
    {
        $admin = User::factory()->create([
            'email' => 'mahekarim@gmail.com',
            'password' => bcrypt('123456'),
            'role' => 'admin',
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'mahekarim@gmail.com',
            'password' => '123456',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * Test admin login with incorrect credentials
     */
    public function test_admin_login_with_incorrect_credentials(): void
    {
        User::factory()->create([
            'email' => 'mahekarim@gmail.com',
            'password' => bcrypt('123456'),
            'role' => 'admin',
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'mahekarim@gmail.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /**
     * Test that regular users cannot login to admin panel
     */
    public function test_regular_user_cannot_login_to_admin_panel(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /**
     * Test that unauthenticated users cannot access dashboard
     */
    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/admin/login');
    }

    /**
     * Test that regular users cannot access dashboard
     */
    public function test_regular_user_cannot_access_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertRedirect('/admin/login');
    }

    /**
     * Test that admin users can access dashboard
     */
    public function test_admin_user_can_access_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
        $response->assertSee('Total Users');
        $response->assertSee('Total Documents');
    }

    /**
     * Test dashboard displays correct metrics
     */
    public function test_dashboard_displays_correct_metrics(): void
    {
        // Create admin user
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Create test data
        $users = User::factory()->count(5)->create(['role' => 'user']);
        $documents = Document::factory()->count(3)->create(['user_id' => $users[0]->id]);
        
        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('6'); // Total users (5 + 1 admin)
        $response->assertSee('3'); // Total documents
    }

    /**
     * Test admin logout
     */
    public function test_admin_logout(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $this->actingAs($admin);
        $this->assertAuthenticatedAs($admin);
        
        $response = $this->post('/admin/logout');
        
        $response->assertRedirect('/admin/login');
        $this->assertGuest();
    }
}
