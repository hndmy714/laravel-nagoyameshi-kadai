<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;

class CategoryTest extends TestCase
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

    //indexアクション（カテゴリ一覧ページ）
    //未ログインのユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_guest_cannot_access_admin_categories_index()
    {
        $response = $this->get(route('admin.categories.index'));
        $response->assertRedirect(route('admin.login'));
    }

    //ログイン済みの一般ユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_non_admin_user_cannot_access_admin_categories_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.categories.index'));
        $response->assertRedirect(route('admin.login'));
    }

    //ログイン済みの管理者は管理者側のカテゴリ一覧ページにアクセスできる
    public function test_admin_user_can_access_admin_categories_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.categories.index'));
        $response->assertStatus(200);
    }

    //storeアクション（カテゴリ登録機能）
    //未ログインのユーザーはカテゴリを登録できない
    public function test_guest_cannot_access_admin_categories_store()
    {
        $category = Category::factory()->create();

        $response = $this->get(route('admin.categories.store'));

        $response->assertRedirect(route('admin.login'));
    }

    //ログイン済みの一般ユーザーはカテゴリを登録できない
    public function test_user_cannot_access_admin_categories_store()
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.categories.store'));

        $response->assertRedirect(route('admin.login'));
    }

    //ログイン済みの管理者はカテゴリを登録できる
    public function test_admin_can_access_admin_categories_store()
    {
        $category = Category::factory()->create();
        
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.categories.store'));

        $response->assertStatus(200);
    }

    //updateアクション（カテゴリ更新機能）
    //未ログインのユーザーはカテゴリを更新できない
    public function test_guest_cannot_access_admin_categories_update()
    {
        $category = Category::factory()->create();

        $response = $this->get(route('admin.categories.store'));

        $response->assertRedirect(route('admin.login'));
    }
    //ログイン済みの一般ユーザーはカテゴリを更新できない
    public function test_user_cannot_access_admin_categories_update()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.categories.store'));

        $response->assertRedirect(route('admin.login'));
    }
    //ログイン済みの管理者はカテゴリを更新できる
    public function test_admin_can_access_admin_categories_update()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $category = Category::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.categories.store'));

        $response->assertStatus(200);
    }

    //destroyアクション（カテゴリ削除機能）
    //未ログインのユーザーはカテゴリを削除できない
    public function test_guest_cannot_access_admin_categories_destroy()
    {
        $category = Category::factory()->create();

        $response = $this->get(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.login'));
    }
    //ログイン済みの一般ユーザーはカテゴリを削除できない
    public function test_user_cannot_access_admin_categories_destroy()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.login'));
    }
    //ログイン済みの管理者はカテゴリを削除できる
    public function test_admin_can_access_admin_categories_destroy()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $category = Category::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.categories.store'));

        $response->assertStatus(200);
    }
}
