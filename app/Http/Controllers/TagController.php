<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use App\Constants\HttpStatus;

class TagController extends Controller
{
    // Получить все теги
    public function index(Request $request)
    {
        $user = $request->user();
        $tags = $user->tags()->get();

        return response()->json($tags, HttpStatus::OK);
    }

    // Создать новый тег
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|min:3|max:20'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'ошибка валидации',
                'errors' => $e->errors()
            ], HttpStatus::BAD_REQUEST);
        }

        $user = $request->user();

        $tag = $user->tags()->create([
            'title' => $request->title,
        ]);

        return response()->json($tag, HttpStatus::CREATED);
    }

    // Найти по id
    public function findById(Request $request, $id)
    {
        $user = $request->user();
        $tag = $user->tags()->find($id);

        if (!$tag) {
            return response()->json(['message' => 'Тэг не найден'], HttpStatus::NOT_FOUND);
        }

        return response()->json($tag, HttpStatus::OK);
    }

    // Обновить тег
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $tag = $user->tags()->find($id);

        if (!$tag) {
            return response()->json(['message' => 'Тэг не найден или нет доступа'], HttpStatus::NOT_FOUND);
        }

        try {
            $request->validate([
                'title' => 'required|string|min:3|max:20'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'ошибка валидации',
                'errors' => $e->errors()
            ], HttpStatus::BAD_REQUEST);
        }

        $tag->update($request->only('title'));

        return response()->json($tag, HttpStatus::OK);
    }

    // Удалить тег
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $tag = $user->tags()->find($id);

        if (!$tag) {
            return response()->json(['message' => 'Тэг не найден или нет доступа'], HttpStatus::NOT_FOUND);
        }

        // Удалить связи с задачами
        DB::table('task_tag')->where('tag_id', $id)->delete();

        $tag->delete();

        return response()->json(['message' => 'Тэг удалён'], HttpStatus::OK);
    }
}
