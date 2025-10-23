<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    /**
     * Store a new comment (nested resource)
     * 
     * Route: POST /articles/{article}/comments
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
            'parent_id.exists' => 'Parent comment not found.',
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
     * Delete a comment (nested resource)
     * 
     * Route: DELETE /articles/{article}/comments/{comment}
     */
    public function destroy(Article $article, Comment $comment): RedirectResponse
    {
        // Verify comment belongs to article
        if ($comment->article_id !== $article->id) {
            return redirect()->back()
                ->with('error', 'Comment does not belong to this article.');
        }

        try {
            DB::transaction(function () use ($comment) {
                
                // Delete replies first (cascade)
                $comment->replies()->delete();
                
                // Delete comment
                $comment->delete();

                Log::info('Comment deleted', [
                    'comment_id' => $comment->id,
                    'article_id' => $comment->article_id,
                ]);
            });

            return redirect()->back()
                ->with('success', 'Comment deleted successfully! 🗑️');

        } catch (\Exception $e) {
            Log::error('Failed to delete comment', [
                'error' => $e->getMessage(),
                'comment_id' => $comment->id,
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete comment.');
        }
    }
}