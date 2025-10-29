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
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // ✅ OPTIMIZED: Select only needed columns
        $query = Article::select([
                'id', 
                'title', 
                'summary', 
                'image', 
                'date', 
                'read_time', 
                'views',
                'user_id',
                'category_id'
            ])
            ->with([
                'user:id,name',  // ✅ Select specific columns
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
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $articles = $query->latest('date')->paginate(6)->withQueryString();
        
        // ✅ Select specific columns untuk categories
        $categories = Category::select(['id', 'name', 'slug'])
                             ->withCount('articles')
                             ->get();

        return view('articles.index', compact('articles', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // ✅ Select only needed columns
        $categories = Category::select(['id', 'name'])->get();
        $tags = Tag::select(['id', 'name'])->get();
        
        return view('articles.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $messages = [
            'title.required' => 'Judul artikel wajib diisi.',
            'title.max' => 'Judul artikel maksimal 255 karakter.',
            'user_id.required' => 'Penulis harus dipilih.',
            'user_id.exists' => 'Penulis yang dipilih tidak valid.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'date.required' => 'Tanggal publikasi wajib diisi.',
            'date.date' => 'Format tanggal tidak valid.',
            'summary.required' => 'Ringkasan artikel wajib diisi.',
            'summary.max' => 'Ringkasan maksimal 500 karakter.',
            'content.required' => 'Konten artikel wajib diisi.',
            'image.required' => 'Gambar artikel wajib diupload.',
            'image.image' => 'File yang diupload harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat JPG, PNG, JPEG, atau WEBP.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'read_time.required' => 'Estimasi waktu baca wajib diisi.',
            'tags.array' => 'Format tags tidak valid.',
            'tags.*.exists' => 'Tag yang dipilih tidak valid.',
        ];

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
        ], $messages);

        try {
            $article = DB::transaction(function () use ($request, $validated) {
                
                $imagePath = null;
                if ($request->hasFile('image')) {
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $filename = time() . '_' . Str::random(10) . '.' . $extension;
                    
                    $imagePath = $request->file('image')->storeAs(
                        'articles',
                        $filename,
                        'public'
                    );
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

                if (isset($validated['tags']) && count($validated['tags']) > 0) {
                    $article->tags()->attach($validated['tags']);
                }

                Log::info('Article created with image', [
                    'article_id' => $article->id,
                    'image_path' => $imagePath,
                ]);

                return $article;
            });

            return redirect()->route('articles.show', $article)
                ->with('success', 'Article created successfully! 🎉');

        } catch (\Exception $e) {
            if (isset($imagePath) && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            Log::error('Failed to create article', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create article. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article): View
    {
        // ✅ OPTIMIZED: Eager load dengan specific columns
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article): View
    {
        $categories = Category::select(['id', 'name'])->get();
        $tags = Tag::select(['id', 'name'])->get();
        $article->load('tags:id,name');
        
        return view('articles.edit', compact('article', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article): RedirectResponse
    {
        $messages = [
            'title.required' => 'Judul artikel wajib diisi.',
            'title.max' => 'Judul artikel maksimal 255 karakter.',
            'user_id.required' => 'Penulis harus dipilih.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'date.required' => 'Tanggal publikasi wajib diisi.',
            'summary.required' => 'Ringkasan artikel wajib diisi.',
            'summary.max' => 'Ringkasan maksimal 500 karakter.',
            'content.required' => 'Konten artikel wajib diisi.',
            'image.image' => 'File yang diupload harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat JPG, PNG, JPEG, atau WEBP.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'read_time.required' => 'Estimasi waktu baca wajib diisi.',
        ];

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
        ], $messages);

        try {
            DB::transaction(function () use ($request, $article, $validated) {
                
                $oldImagePath = $article->image;

                if ($request->hasFile('image')) {
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $filename = time() . '_' . Str::random(10) . '.' . $extension;
                    
                    $newImagePath = $request->file('image')->storeAs(
                        'articles',
                        $filename,
                        'public'
                    );

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

                Log::info('Article updated', [
                    'article_id' => $article->id,
                    'image_changed' => $request->hasFile('image'),
                ]);
            });

            return redirect()->route('articles.show', $article)
                ->with('success', 'Article updated successfully! ✅');

        } catch (\Exception $e) {
            Log::error('Failed to update article', [
                'error' => $e->getMessage(),
                'article_id' => $article->id,
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update article. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
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

                Log::info('Article deleted with image', [
                    'article_id' => $article->id,
                    'image_deleted' => $imagePath,
                ]);
            });

            return redirect()->route('articles.index')
                ->with('success', 'Article deleted successfully! 🗑️');

        } catch (\Exception $e) {
            Log::error('Failed to delete article', [
                'error' => $e->getMessage(),
                'article_id' => $article->id,
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete article. Please try again.');
        }
    }
}