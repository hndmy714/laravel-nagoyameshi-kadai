<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    //indexアクション（レビュー一覧ページ）
    //未ログインのユーザーは会員側のレビュー一覧ページにアクセスできない
    public function test_guest_cannot_access_review_index(): void
    {
        $response = $this->get(route('reviews.index'));
        $response->assertRedirect(route('login'));
    }

    //ログイン済みの無料会員は会員側のレビュー一覧ページにアクセスできる
    public function test_free_user_can_access_review_index()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('reviews.index'));
        $response->assertStatus(200);
    }

    //ログイン済みの有料会員はレビュー一覧ページにアクセスできる
    public function test_premium_user_can_access_review_index()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1RdwlxDfDiYheqQcIIfxbFu6')->create('pm_card_visa');

        $response = $this->actingAs($user)->get(route('reviews.index'));
        $response->assertStatus(200);
    }

    //ログイン済みの管理者は会員側のレビュー一覧ページにアクセスできない
    public function test_admin_cannot_access_review_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $this->actingAs($admin, 'admin');

        $response = $this->get(route('reviews.index'));
        $response->assertRedirect(route('admin.home'));
    }

    //createアクション（レビュー投稿ページ）
    //未ログインのユーザーは会員側のレビュー投稿ページにアクセスできない
    public function test_guest_cannot_access_review_create()
    {
        $response = $this->get(route('reviews.index'));
        $response->assertRedirect(route('login'));
    }

    //ログイン済みの無料会員は会員側のレビュー投稿ページにアクセスできない
    public function test_free_user_cannot_access_review_create()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('reviews.index'));
        $response->assertRederect(route('subscription.create'));
    }

    //ログイン済みの有料会員は会員側のレビュー投稿ページにアクセスできる
    public function test_premium_user_can_access_review_create()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1RdwlxDfDiYheqQcIIfxbFu6')->create('pm_card_visa');

        $response = $this->actingAs($user)->get(route('reviews.create'));
        $response->assertStatus(200);
    }

    //ログイン済みの管理者は会員側のレビュー投稿ページにアクセスできない
    public function test_admin_cannot_access_review_create()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $this->actingAs($admin, 'admin');

        $response = $this->get(route('reviews.create'));
        $response->assertRedirect(route('admin.home'));
    }

    //storeアクション（レビュー投稿機能）
    //未ログインのユーザーはレビューを投稿できない
    public function test_guest_cannot_post_review_store()
    {
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];

        $response = $this->post(route('reviews.store', $request_parameter));
        $response->assertRedirect(route('login'));
    }

    //ログイン済みの無料会員はレビューを投稿できない
    public function test_free_user_cannot_post_review_store()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];

        $response = $this->post(route('reviews.store', $request_parameter));
        $response->assertRederect(route('subscription.create'));
    }

    //ログイン済みの有料会員はレビューを投稿できる
    public function test_premium_user_can_post_review_store()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1RdwlxDfDiYheqQcIIfxbFu6')->create('pm_card_visa');

        $response = $this->actingAs($user)->post(route('reviews.store'));
        $response->assertStatus(200);
    }

    //ログイン済みの管理者はレビューを投稿できない
    public function test_admin_cannot_post_review_store()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];

        $this->actingAs($admin, 'admin');

        $response = $this->post(route('reviews.store', $request_parameter));
        $response->assertRedirect(route('admin.home'));
    }
}
