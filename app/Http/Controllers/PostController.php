<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'content' => 'required|string',
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $post = Post::create([
                'user_id' => $user->id,
                'author' => $user->name,
                'content' => $request->content,
            ]);

            return response()->json($post, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function index()
    {
        $user = auth()->user(); // Get the currently authenticated user
        $posts = Post::all();
        return response()->json(['user_id' => $user->id, 'posts' => $posts], 200);
    }
    

    public function show($id)
    {
        try {
            $post = Post::findOrFail($id);
            return response()->json(['post' => $post], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Post not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $post = Post::findOrFail($id);

            // Check if the authenticated user is the owner of the post
            if ($post->user_id !== $user->id) {
                return response()->json(['error' => 'Forbidden'], 403);
            }

            $post->update($request->all());
            return response()->json(['post' => $post], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $post = Post::findOrFail($id);

            // Check if the authenticated user is the owner of the post
            if ($post->user_id !== $user->id) {
                return response()->json(['error' => 'Forbidden'], 403);
            }

            $post->delete();
            return response()->json(null, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
