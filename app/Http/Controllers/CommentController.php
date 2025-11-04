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
    public function store(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|min:3|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ], [
            'content.required' => 'Comment cannot be empty.',
            'content.min' => 'Comment must be at least 3 characters.',
            'content.max' => 'Comment cannot exceed 1000 characters.',
        ]);

        try {
            $comment = DB::transaction(function () use ($article, $validated) {
                
                $comment = Comment::create([
                    'article_id' => $article->id,
                    'user_id' => auth()->id(), // ✅ Auto-use logged in user
                    'parent_id' => $validated['parent_id'] ?? null,
                    'content' => $validated['content'],
                    'is_approved' => true,
                ]);

                Log::info('Comment created', [
                    'comment_id' => $comment->id,
                    'user_id' => auth()->id(),
                    'article_id' => $article->id,
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
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to post comment.');
        }
    }

    public function destroy(Article $article, Comment $comment): RedirectResponse
    {
        if ($comment->article_id !== $article->id) {
            return redirect()->back()
                ->with('error', 'Comment does not belong to this article.');
        }

        // ✅ Check permission: Admin or comment owner
        if (!auth()->user()->isAdmin() && $comment->user_id !== auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete this comment.');
        }

        try {
            DB::transaction(function () use ($comment) {
                $comment->replies()->delete();
                $comment->delete();
            });

            return redirect()->back()
                ->with('success', 'Comment deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete comment.');
        }
    }
}