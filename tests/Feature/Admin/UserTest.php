<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;


class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_admin_useres_index()
    {
        $response = $this->get('/admin/users');
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_access_admin_user_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertRedirect('admin/login');
    }

    public function test_admin_user_can_access_admin_users_index()
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);

        $response = $this->actingAs($adminUser,'admin')->get('/admin/users');
        $response->assertStatus(200);
    }

    public function test_guest_user_cannot_access_admin_user_show()
    {
        $response = $this->get('/admin/users/1');
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_access_admin_user_show()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.users.show',$user));
        $response->assertRedirect('admin/login');
    }

    public function test_admin_user_can_access_admin_user_show()
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);

        $user = User::factory()->create();

        $response = $this->actingAs($adminUser,'admin')->get(route('admin.users.show',$user));
        $response->assertStatus(200);
    }
}
