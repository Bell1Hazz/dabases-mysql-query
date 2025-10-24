<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminArticleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Article::with(['user', 'category', 'tags']);

        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        $articles = $query->latest()->paginate(10);
        $categories = Category::all();

        return view('admin.articles.index', compact('articles', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::all();
        $tags = Tag::all();
        $users = User::whereIn('role', ['author', 'admin'])->get();
        
        return view('admin.articles.create', compact('categories', 'tags', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'summary' => 'required|string|max:500',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'read_time' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        try {
            $article = DB::transaction(function () use ($request, $validated) {
                
                $imagePath = null;
                if ($request->hasFile('image')) {
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $filename = time() . '_' . Str::random(10) . '.' . $extension;
                    $imagePath = $request->file('image')->storeAs('articles', $filename, 'public');
                }

                $article = Article::create([
                    'user_id' => $validated['user_id'],
                    'category_id' => $validated['category_id'],
                    'title' => $validated['title'],
                    'date' => $validated['date'],
                    'summary' => $validated['summary'],
                    'content' => $validated['content'],
                    'image' => $imagePath,
                    'read_time' => $validated['read_time'],
                ]);

                if (isset($validated['tags'])) {
                    $article->tags()->attach($validated['tags']);
                }

                return $article;
            });

            return redirect()->route('admin.articles.index')
                ->with('success', 'Article created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create article.');
        }
    }

    public function edit(Article $article): View
    {
        $categories = Category::all();
        $tags = Tag::all();
        $users = User::whereIn('role', ['author', 'admin'])->get();
        $article->load('tags');
        
        return view('admin.articles.edit', compact('article', 'categories', 'tags', 'users'));
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'summary' => 'required|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'read_time' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        try {
            DB::transaction(function () use ($request, $article, $validated) {
                
                $oldImagePath = $article->image;

                if ($request->hasFile('image')) {
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $filename = time() . '_' . Str::random(10) . '.' . $extension;
                    $newImagePath = $request->file('image')->storeAs('articles', $filename, 'public');

                    if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                        Storage::disk('public')->delete($oldImagePath);
                    }

                    $validated['image'] = $newImagePath;
                } else {
                    $validated['image'] = $oldImagePath;
                }

                $article->update($validated);

                if (isset($validated['tags'])) {
                    $article->tags()->sync($validated['tags']);
                } else {
                    $article->tags()->detach();
                }
            });

            return redirect()->route('admin.articles.index')
                ->with('success', 'Article updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update article.');
        }
    }

    public function destroy(Article $article): RedirectResponse
    {
        try {
            DB::transaction(function () use ($article) {
                $imagePath = $article->image;
                
                $article->tags()->detach();
                $article->comments()->delete();
                $article->delete();

                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            });

            return redirect()->route('admin.articles.index')
                ->with('success', 'Article deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete article.');
        }
    }
}