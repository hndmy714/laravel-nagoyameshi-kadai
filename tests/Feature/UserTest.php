<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（会員情報ページ）
    // 未ログインのユーザーは会員情報ページにアクセスできない
    public function test_guest_cannot_access_user_index(): void
    {
        $response = $this->get(route('user.index'));
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの一般ユーザーは会員情報ページにアクセスできる
    public function test_user_can_access_user_index()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('user.index'));
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の会員情報ページにアクセスできない
    public function test_admin_cannot_access_user_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $this->actingAs($admin, 'admin');

        $response = $this->get(route('user.index'));
        $response->assertRedirect(route('admin.home'));
    }


    // editアクション（会員情報編集ページ）
    // 未ログインのユーザーは会員側の会員情報編集ページにアクセスできない
    public function test_guest_cannot_access_user_edit()
    {
        $response = $this->get(route('user.edit', ['user' => 1]));
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの一般ユーザーは会員側の他人の会員情報編集ページにアクセスできない
    public function test_user_cannot_access_other_users_edit()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('user.edit', $otherUser));
        $response->assertRedirect(route('user.index'));
    }

    // ログイン済みの一般ユーザーは会員側の自身の会員情報編集ページにアクセスできる
    public function test_user_can_access_own_edit()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('user.edit', $user->id));

        $response->assertOk()
            ->assertViewIs('user.edit')
            ->assertViewHas('user', $user);
    }

    // ログイン済みの管理者は会員側の会員情報編集ページにアクセスできない
    public function test_admin_cannot_access_user_edit_page()
    {
        $user = User::factory()->create();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $this->actingAs($admin, 'admin');

        $response = $this->get(route('user.edit', $user));

        $response->assertRedirect(route('admin.home'));
    }


    // updateアクション（会員情報更新機能）
    // 未ログインのユーザーは会員情報を更新できない
    public function test_guest_cannot_update_user()
    {
        $user = User::factory()->create();
        $updateData = ['name' => 'updated_name'];

        $response = $this->patch(route('user.update', $user->id), $updateData);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('users', $updateData);
    }

    // ログイン済みの一般ユーザーは他人の会員情報を更新できない
    public function test_user_cannot_update_other_users()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $updateData = User::factory()->create()->toArray();

        $this->actingAs($user);

        $response = $this->patch(route('user.update', $otherUser->id), $updateData);

        $this->assertDatabaseMissing('users', $updateData);

        $response->assertRedirect(route('user.index'));
    }

    // ログイン済みの一般ユーザーは自身の会員情報を更新できる
    public function test_regular_user_can_update_own()
    {
        $user = User::factory()->create();

        $updateData = [
            'name' => 'テスト更新',
            'kana' => 'テストコウシン',
            'email' => 'test.update@example.com',
            'postal_code' => '1234567',
            'address' => 'テスト更新',
            'phone_number' => '0123456789',
            'birthday' => '20150319',
            'occupation' => 'テスト更新'
        ];

        $response = $this->actingAs($user)->patch(route('user.update', $user->id), $updateData);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'テスト更新',
            'kana' => 'テストコウシン',
            'email' => 'test.update@example.com',
            'postal_code' => '1234567',
            'address' => 'テスト更新',
            'phone_number' => '0123456789',
            'birthday' => '20150319',
            'occupation' => 'テスト更新'
        ]);

        $response->assertRedirect(route('user.index'));
    }

    // ログイン済みの管理者は会員情報を更新できない
    public function test_admin_cannot_update_user()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $this->actingAs($admin, 'admin');

        $user = User::factory()->create();
        $updateData = ['name' => '名前更新'];

        $response = $this->patch(route('user.update', $user->id), $updateData);

        $response->assertRedirect(route('admin.home'));
        $this->assertDatabaseMissing('users', ['id' => $user->id, 'name' => '名前更新']);
    }
}
