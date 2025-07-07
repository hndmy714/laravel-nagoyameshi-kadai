<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Term;
use Illuminate\Support\Facades\Hash;

class TermTest extends TestCase
{
    use RefreshDatabase;

    //未ログインのユーザーは会員側の利用規約ページにアクセスできる
    public function test_guest_can_access_term_index()
    {
        $term = Term::factory()->create();

        $response = $this->get(route('terms.index'));
        $response->assertStatus(200);
    }

    //ログイン済みの一般ユーザーは会員側の利用規約ページにアクセスできる
    public function test_user_can_access_term_index()
    {
        $user = User::factory()->create();
        $term = Term::factory()->create();

        $response = $this->actingAs($user)->get(route('terms.index'));
        $response->assertStatus(200);
    }

    //ログイン済みの管理者は会員側の利用規約ページにアクセスできない
    public function test_admin_cannot_access_term_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('terms.index'));
        $response->assertRedirect(route('admin.home'));
    }
}
