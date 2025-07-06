<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Review;
use App\Models\Reservation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Support\Carbon;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    //indexアクション（予約一覧ページ）
    //未ログインのユーザーは会員側の予約一覧ページにアクセスできない
    public function test_guest_cannot_access_reservation_index()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->get(route('restaurants.reservations.index', $reservation));
        $response->assertRedirect(route('login'));
    }

    //ログイン済みの無料会員は会員側の予約一覧ページにアクセスできない
    public function test_free_user_cannot_access_reservation_index()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get(route('restaurants.reservations.index', $restaurant));
        $response->assertRedirect(route('subscription.create'));
    }

    //ログイン済みの有料会員は会員側の予約一覧ページにアクセスできる
    public function test_premium_user_can_access_reservation_index()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1RdwlxDfDiYheqQcIIfxbFu6')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get(route('restaurants.reservations.index', $restaurant));
        $response->assertStatus(200);
    }

    //ログイン済みの管理者は会員側の予約一覧ページにアクセスできない
    public function test_admin_cannot_access_reservation_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reservations.index', $restaurant));
        $response->assertRedirect(route('admin.home'));
    }

    //createアクション（予約ページ）
    //未ログイン済みのユーザーは会員側の予約ページにアクセスできない
    public function test_guest_cannot_access_reservation_create()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('restaurants.reservations.create', $restaurant));
        $response->assertRedirect(route('login'));
    }

    //ログイン済みの無料会員は会員側の予約ページにアクセスできない
    public function test_free_user_cannot_access_reservation_create()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get(route('restaurants.reservations.create', $restaurant));
        $response->assertRedirect(route('subscription.create'));
    }

    //ログイン済みの有料会員は会員側の予約ページにアクセスできる
    public function test_premium_user_can_access_reservation_create()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1RdwlxDfDiYheqQcIIfxbFu6')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();

        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get(route('restaurants.reservations.create', [$restaurant->id, $reservation]));
        $response->assertStatus(200);
    }

    //ログイン済みの管理者は会員側の予約ページにアクセスできない
    public function test_admin_cannot_access_reservation_create()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reservations.create', $restaurant));
        $response->assertRedirect(route('admin.home'));
    }

    //storeアクション（予約機能）
    //未ログインのユーザーは予約できない
    public function test_guest_cannot_access_reservation_store()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $reservation = new Reservation();
        $reservation->reserved_datetime = now();
        $reservation->number_of_people = fake()->numberBetween(1, 50);
        $reservation->restaurant_id = $restaurant->id;
        $reservation->user_id = $user->id;
        $reservation->save();

        $response = $this->post(route('restaurants.reservations.store', $restaurant));
        $response->assertRedirect(route('login'));
    }

    //ログイン済みの無料会員は予約できない
    public function test_free_user_cannot_access_reservation_store()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $reservation = new Reservation();
        $reservation->reserved_datetime = now();
        $reservation->number_of_people = fake()->numberBetween(1, 50);
        $reservation->restaurant_id = $restaurant->id;
        $reservation->user_id = $user->id;
        $reservation->save();

        $response = $this->actingAs($user)->post(route('restaurants.reservations.store', $restaurant));
        $response->assertRedirect(route('subscription.create'));
    }

    //ログイン済みの有料会員は予約できる
    public function test_premium_user_can_access_reservation_store()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1RdwlxDfDiYheqQcIIfxbFu6')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->post(route('restaurants.reservations.store', $restaurant), [
            'reservation_date' => '2024-01-01',
            'reservation_time' => '00:00',
            'number_of_people' => 10,
        ]);

        $this->assertDatabaseHas('reservations', ['reserved_datetime' => '2024-01-01 00:00', 'number_of_people' => 10]);
        $response->assertRedirect(route('reservations.index'));
    }

    //ログイン済みの管理者は予約できない
    public function test_admin_cannot_access_reservation_store()
    {
        $user = User::factory()->create();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $reservation = new Reservation();
        $reservation->reserved_datetime = now();
        $reservation->number_of_people = fake()->numberBetween(1, 50);
        $reservation->restaurant_id = $restaurant->id;
        $reservation->user_id = $user->id;
        $reservation->save();

        $response = $this->actingAs($admin, 'admin')->post(route('restaurants.reservations.store', $restaurant));
        $response->assertRedirect(route('admin.home'));
    }

    //destroyアクション（予約キャンセル機能）
    //未ログインのユーザーは予約をキャンセルできない
    public function test_guest_cannot_delete_reservation_destroy()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $reservation = new Reservation();
        $reservation->reserved_datetime = now();
        $reservation->number_of_people = fake()->numberBetween(1, 50);
        $reservation->restaurant_id = $restaurant->id;
        $reservation->user_id = $user->id;
        $reservation->save();

        $response = $this->delete(route('restaurants.reservations.destroy', [$restaurant, $reservation]));
        $response->assertRedirect(route('login'));
    }

    //ログイン済みの無料会員は予約をキャンセルできない
    public function test_free_user_cannot_delete_reservation_destroy()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $reservation = new Reservation();
        $reservation->reserved_datetime = now();
        $reservation->number_of_people = fake()->numberBetween(1, 50);
        $reservation->restaurant_id = $restaurant->id;
        $reservation->user_id = $user->id;
        $reservation->save();

        $response = $this->actingAs($user)->delete(route('restaurants.reservations.destroy', [$restaurant, $reservation]));
        $response->assertRedirect(route('subscription.create'));
    }

    //ログイン済みの有料会員は他人の予約をキャンセルできない
    public function test_premium_user_cannot_delete_other_reservation_destroy()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1RdwlxDfDiYheqQcIIfxbFu6')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();
        $otherUser = User::factory()->create();

        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($user)->delete(route('reservations.destroy', $reservation));
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);
        $response->assertRedirect(route('reservations.index'));
    }

    //ログイン済みの有料会員は自身の予約をキャンセルできる
    public function test_premium_user_can_delete_reservation_destroy()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1RdwlxDfDiYheqQcIIfxbFu6')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();

        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete(route('reservations.destroy', $reservation));

        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
        $response->assertRedirect(route('reservations.index'));
    }
    //ログイン済みの管理者は予約をキャンセルできない
    public function test_admin_cannot_delete_reservation_destroy()
    {
        $user = User::factory()->create();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $reservation = new Reservation();
        $reservation->reserved_datetime = now();
        $reservation->number_of_people = fake()->numberBetween(1, 50);
        $reservation->restaurant_id = $restaurant->id;
        $reservation->user_id = $user->id;
        $reservation->save();

        $response = $this->actingAs($admin, 'admin')->delete(route('restaurants.reservations.destroy', [$restaurant->id, $reservation]));
        $response->assertRedirect(route('admin.home'));
    }
}
