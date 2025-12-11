<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Document;
use Laravel\Sanctum\Sanctum;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that unauthenticated users cannot access admin dashboard
     */
    public function test_unauthenticated_user_cannot_access_admin_dashboard(): void
    {
        $response = $this->getJson('/api/admin/dashboard');

        $response->assertStatus(401);
    }

    /**
     * Test that regular users cannot access admin dashboard
     */
    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/admin/dashboard');

        $response->assertStatus(403);
    }

    /**
     * Test that admin users can access admin dashboard
     */
    public function test_admin_user_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/admin/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'metrics' => [
                'total_users',
                'total_documents',
            ],
            'recent_users',
            'recent_documents',
        ]);
    }

    /**
     * Test admin dashboard returns correct metrics
     */
    public function test_admin_dashboard_returns_correct_metrics(): void
    {
        // Create admin user
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        // Create test data
        $users = User::factory()->count(5)->create(['role' => 'user']);
        $documents = Document::factory()->count(3)->create(['user_id' => $users[0]->id]);

        $response = $this->getJson('/api/admin/dashboard');

        $response->assertStatus(200);
        $response->assertJson([
            'metrics' => [
                'total_users' => 6, // 5 users + 1 admin
                'total_documents' => 3,
            ],
        ]);
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

        $response = $this->postJson('/api/login', [
            'email' => 'mahekarim@gmail.com',
            'password' => '123456',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user',
            'token',
        ]);
    }

    /**
     * Test admin login with incorrect credentials
     */
    public function test_admin_login_with_incorrect_credentials(): void
    {
        $admin = User::factory()->create([
            'email' => 'mahekarim@gmail.com',
            'password' => bcrypt('123456'),
            'role' => 'admin',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'mahekarim@gmail.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }
}
