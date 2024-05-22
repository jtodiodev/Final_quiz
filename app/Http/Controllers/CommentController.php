<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;


class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string',
        ]);

        $user = Auth::user();
        $comment = Comment::create([
            'post_id' => $request->post_id,
            'user_id' => $user->id,
            'author' => $user->name,
            'content' => $request->content,
        ]);

        return response()->json($comment, 201);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== $user->id && $comment->post->user_id !== $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $comment->delete();
        return response()->json(null, 200);
    }

    public function index()
    {
        $posts = Post::with('comments')->get();
        $user_id = auth()->id();
        return response()->json(['posts' => $posts, 'user_id' => $user_id]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = Comment::findOrFail($id);
        $user = Auth::user();

        if ($comment->user_id !== $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $comment->content = $request->content;
        $comment->save();

        return response()->json($comment, 200);
    }
}
