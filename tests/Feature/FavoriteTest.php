<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Review;
use Illuminate\Support\Facades\Hash;


class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    //indexアクション（お気に入り一覧ページ）
    //未ログインのユーザーは会員側のお気に入り一覧ページにアクセスできない
    public function test_guest_cannot_access_favorite_index()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('favorites.index', $restaurant));

        $response->assertRedirect(route('login'));
    }

    //ログイン済みの無料会員は会員側のお気に入り一覧ページにアクセスできない
    public function test_free_user_cannot_access_favorite_index()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->get(route('favorites.index', $restaurant));
        $response->assertRedirect(route('subscription.create'));
    }

    //ログイン済みの有料会員は会員側のお気に入り一覧ページにアクセスできる
    public function test_premium_user_can_access_favorite_index()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1RdwlxDfDiYheqQcIIfxbFu6')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get(route('favorites.index'));
        $response->assertStatus(200);
    }

    //ログイン済みの管理者は会員側のお気に入り一覧ページにアクセスできない
    public function test_admin_cannot_access_favorite_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('favorites.index', $restaurant));
        $response->assertRedirect(route('admin.home'));
    }

    //storeアクション（お気に入り追加機能）
    //未ログインのユーザーはお気に入りに追加できない
    public function test_guest_cannot_add_favorite_store()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->post(route('favorites.store', $restaurant));
        $response->assertRedirect(route('login'));
    }

    //ログイン済みの無料会員はお気に入りに追加できない
    public function test_free_user_cannot_add_favorite_store()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->post(route('favorites.store', $restaurant));
        $response->assertRedirect(route('subscription.create'));
    }

    //ログイン済みの有料会員はお気に入りに追加できる
    public function test_premium_user_can_add_favorite_store()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1RdwlxDfDiYheqQcIIfxbFu6')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();



        $response = $this->actingAs($user)->post(route('favorites.store', ['restaurant_id' => $restaurant->id]), [
            'restaurant_id' => $restaurant->id,
        ]);

        $this->assertDatabaseHas('restaurant_user', [
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
        ]);
        $response->assertStatus(302);
    }

    //ログイン済みの管理者はお気に入りに追加できない
    public function test_admin_cannot_add_favorite_store()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('favorites.store', $restaurant));
        $response->assertRedirect(route('admin.home'));
    }

    //destroyアクション（お気に入り解除機能）
    //未ログインのユーザーはお気に入りを解除できない
    public function test_guest_cannot_delete_favorite_destroy()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->delete(route('favorites.destroy', $restaurant));
        $response->assertRedirect(route('login'));
    }

    //ログイン済みの無料会員はお気に入りを解除できない
    public function test_free_user_cannot_delete_favorite_destroy()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->delete(route('favorites.destroy', $restaurant));
        $response->assertRedirect(route('subscription.create'));
    }

    //ログイン済みの有料会員はお気に入りを解除できる
    public function test_premium_user_can_delete_favorite_destroy()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1RdwlxDfDiYheqQcIIfxbFu6')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->delete(route('favorites.store', ['restaurant_id' => $restaurant->id]), [
            'restaurant_id' => $restaurant->id,
        ]);

        $this->assertDatabaseMissing('restaurant_user', [
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
        ]);
        $response->assertStatus(302);
    }

    //ログイン済みの管理者はお気に入りを解除できない
    public function test_admin_cannot_delete_favorite_destroy()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin, 'admin')->delete(route('favorites.destroy', $restaurant));
        $response->assertRedirect(route('admin.home'));
    }
}
