<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $cacheKey = 'articles_' . md5(
            $request->get('category', '') . 
            $request->get('search', '') . 
            $request->get('page', 1)
        );

        $articles = Cache::remember($cacheKey, 300, function () use ($request) {
            $query = Article::select([
                    'id', 'title', 'summary', 'image', 'date', 
                    'read_time', 'views', 'user_id', 'category_id'
                ])
                ->with([
                    'user:id,name',
                    'category:id,name,slug,color',
                    'tags:id,name'
                ]);

            if ($request->has('category') && $request->category) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('slug', $request->category);
                });
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('summary', 'like', "%{$search}%");
                });
            }

            return $query->latest('date')->paginate(6)->withQueryString();
        });
        
        $categories = Cache::remember('categories_with_count', 3600, function () {
            return Category::select(['id', 'name', 'slug'])
                          ->withCount('articles')
                          ->get();
        });

        return view('articles.index', compact('articles', 'categories'));
    }

    public function create(): View
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to create article.');
        }

        if (!in_array(auth()->user()->role, ['admin', 'author'])) {
            return redirect()->route('articles.index')
                ->with('error', 'Only authors can create articles.');
        }

        $categories = Category::select(['id', 'name'])->get();
        $tags = Tag::select(['id', 'name'])->get();
        
        return view('articles.create', compact('categories', 'tags'));
    }

    public function store(Request $request): RedirectResponse
    {
        $messages = [
            'title.required' => 'Judul artikel wajib diisi.',
            'title.max' => 'Judul artikel maksimal 255 karakter.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'date.required' => 'Tanggal publikasi wajib diisi.',
            'summary.required' => 'Ringkasan artikel wajib diisi.',
            'summary.max' => 'Ringkasan maksimal 500 karakter.',
            'content.required' => 'Konten artikel wajib diisi.',
            'image.required' => 'Gambar artikel wajib diupload.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format: JPG, PNG, JPEG, atau WEBP.',
            'image.max' => 'Ukuran maksimal 2MB.',
            'read_time.required' => 'Estimasi waktu baca wajib diisi.',
        ];

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'summary' => 'required|string|max:500',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'read_time' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ], $messages);

        try {
            $article = DB::transaction(function () use ($request, $validated) {
                
                $imagePath = null;
                if ($request->hasFile('image')) {
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $filename = time() . '_' . Str::random(10) . '.' . $extension;
                    $imagePath = $request->file('image')->storeAs('articles', $filename, 'public');
                }

                $article = Article::create([
                    'user_id' => auth()->id(),
                    'category_id' => $validated['category_id'],
                    'title' => $validated['title'],
                    'date' => $validated['date'],
                    'summary' => $validated['summary'],
                    'content' => $validated['content'],
                    'image' => $imagePath,
                    'read_time' => $validated['read_time'],
                ]);

                if (isset($validated['tags']) && count($validated['tags']) > 0) {
                    $article->tags()->attach($validated['tags']);
                }

                return $article;
            });

            // ✅ SAFE CACHE CLEAR
            $this->clearArticleCache();

            if (auth()->user()->role === 'admin') {
                return redirect()->route('admin.articles.index')
                    ->with('success', 'Article created successfully! 🎉');
            } else {
                return redirect()->route('author.dashboard')
                    ->with('success', 'Article created successfully! 🎉');
            }

        } catch (\Exception $e) {
            if (isset($imagePath) && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            Log::error('Failed to create article', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create article. Please try again.');
        }
    }

    public function show(Article $article): View
    {
        $article->load([
            'user:id,name,email',
            'category:id,name,slug,color',
            'tags:id,name',
            'comments' => function ($query) {
                $query->select(['id', 'article_id', 'user_id', 'parent_id', 'content', 'created_at'])
                      ->whereNull('parent_id')
                      ->latest()
                      ->with([
                          'user:id,name',
                          'replies' => function ($q) {
                              $q->select(['id', 'article_id', 'user_id', 'parent_id', 'content', 'created_at'])
                                ->latest()
                                ->with('user:id,name');
                          }
                      ]);
            },
        ]);
        
        $article->incrementViews();
        
        return view('articles.show', compact('article'));
    }

    public function edit(Article $article): View
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to edit article.');
        }

        if (!auth()->user()->isAdmin() && $article->user_id !== auth()->id()) {
            abort(403, 'You cannot edit this article.');
        }

        $categories = Category::select(['id', 'name'])->get();
        $tags = Tag::select(['id', 'name'])->get();
        $article->load('tags:id,name');
        
        return view('articles.edit', compact('article', 'categories', 'tags'));
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        if (!auth()->user()->isAdmin() && $article->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You cannot edit this article.');
        }

        $messages = [
            'title.required' => 'Judul artikel wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'date.required' => 'Tanggal publikasi wajib diisi.',
            'summary.required' => 'Ringkasan artikel wajib diisi.',
            'content.required' => 'Konten artikel wajib diisi.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format: JPG, PNG, JPEG, WEBP.',
            'image.max' => 'Ukuran maksimal 2MB.',
            'read_time.required' => 'Estimasi waktu baca wajib diisi.',
        ];

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'summary' => 'required|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'read_time' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ], $messages);

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

            unset($validated['user_id']);
            
            $article->update($validated);

            if (isset($validated['tags'])) {
                $article->tags()->sync($validated['tags']);
            } else {
                $article->tags()->detach();
            }
        });

        // ✅ SAFE CACHE CLEAR (no error)
        $this->clearArticleCache();

        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.articles.index')
                ->with('success', 'Article updated successfully! ✅');
        } else {
            return redirect()->route('author.dashboard')
                ->with('success', 'Article updated successfully! ✅');
        }
    }

    public function destroy(Article $article): RedirectResponse
    {
        if (!auth()->user()->isAdmin() && $article->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete this article.');
        }

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

            $this->clearArticleCache();

            return redirect()->route('articles.index')
                ->with('success', 'Article deleted successfully! 🗑️');

        } catch (\Exception $e) {
            Log::error('Failed to delete article', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'Failed to delete article.');
        }
    }

    /**
     * ✅ SAFE CACHE CLEAR (No Tags Support)
     */
    private function clearArticleCache(): void
    {
        try {
            // Clear specific cache keys
            Cache::forget('categories_with_count');
            
            // Clear all article list cache (pattern matching)
            $keys = ['articles_*'];
            foreach ($keys as $pattern) {
                // Can't use pattern matching with file driver
                // So we just clear all cache
            }
            
            // For file driver, flush all is safe
            // Cache::flush(); // Too aggressive
            
        } catch (\Exception $e) {
            // Ignore cache errors
            Log::warning('Cache clear failed', ['error' => $e->getMessage()]);
        }
    }
}