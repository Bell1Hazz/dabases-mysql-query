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
        $query = Article::with(['user', 'category', 'tags']);

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
        $categories = Category::withCount('articles')->get();

        return view('articles.index', compact('articles', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('articles.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * 🖼️ IMAGE UPLOAD: Auto-handle dengan nama unique
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
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Max 2MB
            'read_time' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        try {
            $article = DB::transaction(function () use ($request, $validated) {
                
                // 🖼️ HANDLE IMAGE UPLOAD
                $imagePath = null;
                if ($request->hasFile('image')) {
                    // Generate unique filename: timestamp + random string
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $filename = time() . '_' . Str::random(10) . '.' . $extension;
                    
                    // Store di storage/app/public/articles
                    $imagePath = $request->file('image')->storeAs(
                        'articles',
                        $filename,
                        'public'
                    );
                }

                // Create Article
                $article = Article::create([
                    'user_id' => $validated['user_id'],
                    'category_id' => $validated['category_id'],
                    'title' => $validated['title'],
                    'date' => $validated['date'],
                    'summary' => $validated['summary'],
                    'content' => $validated['content'],
                    'image' => $imagePath, // Store path: articles/filename.jpg
                    'read_time' => $validated['read_time'],
                ]);

                // Attach Tags
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
            // Rollback & delete uploaded image if transaction fails
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
        $article->incrementViews();
        $article->load(['user', 'category', 'tags', 'comments.user', 'comments.replies.user']);
        
        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article): View
    {
        $categories = Category::all();
        $tags = Tag::all();
        $article->load('tags');
        return view('articles.edit', compact('article', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * 🖼️ IMAGE UPLOAD: Replace gambar lama dengan baru (optional)
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Optional saat update
            'read_time' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        try {
            DB::transaction(function () use ($request, $article, $validated) {
                
                $oldImagePath = $article->image;

                // 🖼️ HANDLE IMAGE UPLOAD (jika ada upload baru)
                if ($request->hasFile('image')) {
                    // Generate unique filename
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $filename = time() . '_' . Str::random(10) . '.' . $extension;
                    
                    // Upload new image
                    $newImagePath = $request->file('image')->storeAs(
                        'articles',
                        $filename,
                        'public'
                    );

                    // Delete old image
                    if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                        Storage::disk('public')->delete($oldImagePath);
                    }

                    $validated['image'] = $newImagePath;
                } else {
                    // Keep old image
                    $validated['image'] = $oldImagePath;
                }

                // Update Article
                $article->update([
                    'user_id' => $validated['user_id'],
                    'category_id' => $validated['category_id'],
                    'title' => $validated['title'],
                    'date' => $validated['date'],
                    'summary' => $validated['summary'],
                    'content' => $validated['content'],
                    'image' => $validated['image'],
                    'read_time' => $validated['read_time'],
                ]);

                // Sync Tags
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
     * 
     * 🖼️ IMAGE: Delete gambar saat article dihapus
     */
    public function destroy(Article $article): RedirectResponse
    {
        try {
            DB::transaction(function () use ($article) {
                
                $imagePath = $article->image;
                
                // Delete Tags & Comments
                $article->tags()->detach();
                $article->comments()->delete();

                // Delete Article
                $article->delete();

                // 🖼️ DELETE IMAGE from storage
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