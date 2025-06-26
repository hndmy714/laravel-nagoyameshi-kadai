<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    //createアクション（有料プラン登録ページ）
    public function create()
    {
        $intent = Auth::user()->createSetupIntent();

        return view('subscription.create', compact('intent'));
    }

    //storeアクション（有料プラン登録機能）
    public function store(Request $request)
    {
        $request->user()->newSubscription('premium_plan', 'price_1RdwlxDfDiYheqQcIIfxbFu6')->create($request->paymentMethodId);

        return redirect()->route('home')->with('flash_message', '有料プランへの登録が完了しました。');
    }

    //editアクション（お支払い方法編集ページ）
    public function edit()
    {
        $user = Auth::user();
        $intent = $user->createSetupIntent();

        return view('subscription.edit', compact('user', 'intent'));
    }

    //updateアクション（お支払い方法更新機能）
    public function update(Request $request)
    {
        $request->user()->updateDefaultPaymentMethod($request->paymentMethodId);

        return redirect()->route('home')->with('flash_message', 'お支払い方法を変更しました。');
    }

    //cancelアクション（有料プラン解約ページ）
    public function cancel()
    {
        return view('subscription.cancel');
    }

    //destroyアクション（有料プラン解約機能）
    public function destroy(Request $request)
    {
        $request->user()->subscription('premium_plan')->cancelNow();

        return redirect()->route('home')->with('flash_message', '有料プランを解約しました。');
    }
}
