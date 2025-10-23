<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    /**
     * Store a new comment
     */
    public function store(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string|min:3|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ], [
            'user_id.required' => 'Please select a user.',
            'user_id.exists' => 'Selected user is invalid.',
            'content.required' => 'Comment cannot be empty.',
            'content.min' => 'Comment must be at least 3 characters.',
            'content.max' => 'Comment cannot exceed 1000 characters.',
        ]);

        try {
            $comment = DB::transaction(function () use ($article, $validated) {
                
                $comment = Comment::create([
                    'article_id' => $article->id,
                    'user_id' => $validated['user_id'],
                    'parent_id' => $validated['parent_id'] ?? null,
                    'content' => $validated['content'],
                    'is_approved' => true,
                ]);

                Log::info('Comment created', [
                    'comment_id' => $comment->id,
                    'article_id' => $article->id,
                    'is_reply' => isset($validated['parent_id']),
                ]);

                return $comment;
            });

            $message = isset($validated['parent_id']) 
                ? 'Reply posted successfully! 💬'
                : 'Comment posted successfully! 💬';

            return redirect()->route('articles.show', $article)
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Failed to create comment', [
                'error' => $e->getMessage(),
                'article_id' => $article->id,
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to post comment. Please try again.');
        }
    }

    /**
     * Delete a comment
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        try {
            DB::transaction(function () use ($comment) {
                
                $articleId = $comment->article_id;
                
                // Delete replies first (cascade)
                $comment->replies()->delete();
                
                // Delete comment
                $comment->delete();

                Log::info('Comment deleted', [
                    'comment_id' => $comment->id,
                    'article_id' => $articleId,
                ]);
            });

            return redirect()->back()
                ->with('success', 'Comment deleted successfully! 🗑️');

        } catch (\Exception $e) {
            Log::error('Failed to delete comment', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete comment.');
        }
    }
}