<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'content' => 'required|string',
            ]);

            // Get authenticated user
            $user = Auth::user();
            Log::info('Authenticated user:', ['user' => $user]); // Debugging log
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Create post with user's name as the author
            $post = Post::create([
                'user_id' => $user->id,
                'author' => $user->name,
                'content' => $request->content,
            ]);

            return response()->json($post, 201);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error creating post: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
    public function index()
    {
        return Post::all();
    }
}
