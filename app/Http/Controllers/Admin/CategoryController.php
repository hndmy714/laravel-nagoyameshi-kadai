<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;

class CategoryController extends Controller
{
    //indexアクション（カテゴリ一覧ページ）
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        if ($keyword) {
            $categories = Category::where('name', 'like', "%{$keyword}%")->paginate(15);
        } else {
            $categories = Category::paginate(15);
        }

        $total = $categories->total();

        return view('admin.categories.index', compact('categories', 'total', 'keyword'));
    }

    //storeアクション（カテゴリ登録機能）
    public function store(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $category = new Category();
        $category->name = $request->input('name');
        
        $category->save();

        return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを登録しました。');
    }

    //updateアクション（カテゴリ更新機能）
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $category->name = $request->input('name');

        $category->save();

        return redirect()->route('admin.categories.index', $category)->with('flash_message', 'カテゴリを編集しました。');
    }

    //destroyアクション（カテゴリ削除機能）
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを削除しました。');
    }
}
