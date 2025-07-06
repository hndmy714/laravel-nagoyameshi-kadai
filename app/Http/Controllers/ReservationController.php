<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Reservation;

class ReservationController extends Controller
{
    //indexアクション（予約一覧ページ）
    public function index(Request $request, Restaurant $restaurant)
    {
        $user = Auth::user();

        $reservations = Reservation::where('user_id', Auth::id())->orderBy('reserved_datetime', 'desc')->paginate(15);

        return view('reservations.index', compact('restaurant', 'reservations'));
    }

    //createアクション（予約ページ）
    public function create(Restaurant $restaurant)
    {
        return view('reservations.create', compact('restaurant'));
    }

    //storeアクション（予約機能）
    public function store(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'reservation_date' => 'required|date_format:Y-m-d',
            'reservation_time' => 'required|date_format:H:i',
            'number_of_people' => 'required|numeric|between:1,50',
        ]);

        $reservation = new Reservation();
        $reservation->reserved_datetime = $request->input('reservation_date') . ' ' . $request->input('reservation_time');
        $reservation->number_of_people = $request->input('number_of_people');
        $reservation->restaurant_id = $restaurant->id;
        $reservation->user_id = $request->user()->id;
        $reservation->save();

        return redirect()->route('reservations.index')->with('flash_message', '予約が完了しました。');
    }

    //destroyアクション（予約キャンセル機能）
    public function destroy(Restaurant $restaurant, Reservation $reservation)
    {
        $user = Auth::user();

        if ($reservation->user_id !== $user->id) {
            return redirect()->route('reservations.index', $restaurant)->with('error_message', '不正なアクセスです。');
        } else {
            $reservation->delete();

            return redirect()->route('reservations.index', $restaurant)->with('flash_message', '予約をキャンセルしました。');
        }
    }
}
