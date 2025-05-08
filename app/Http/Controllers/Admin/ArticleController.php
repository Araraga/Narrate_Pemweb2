<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Display a listing of the articles.
     */
    public function index()
    {
        $articles = Article::with('user', 'category')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.articles.index', compact('articles'));
    }

    /**
     * Display a listing of the draft articles.
     */
    public function drafts()
    {
        $articles = Article::where('status', 'draft')
            ->with('user', 'category')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.articles.drafts', compact('articles'));
    }

    /**
     * Display the moderation queue.
     */
    public function moderationQueue()
    {
        $articles = Article::where('status', 'draft')
            ->with('user', 'category')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.articles.moderation', compact('articles'));
    }

    /**
     * Show the form for creating a new article.
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.articles.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created article in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'status' => 'required|in:draft,published,archived',
        ]);

        $article = new Article();
        $article->user_id = Auth::id();
        $article->category_id = $request->category_id;
        $article->title = $request->title;
        $article->slug = Str::slug($request->title) . '-' . Str::random(5);
        $article->content = $request->content;
        $article->excerpt = $request->excerpt;
        $article->status = $request->status;

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('featured-images', 'public');
            $article->featured_image = $path;
        }

        if ($request->status === 'published') {
            $article->published_at = now();
        }

        $article->save();

        if ($request->has('tags')) {
            $article->tags()->attach($request->tags);
        }

        // Catat aktivitas pembuatan artikel
        activity_log('article_create', 'Created article: ' . $article->title, $request);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article created successfully.');
    }

    /**
     * Display the specified article.
     */
    public function show(Article $article)
    {
        return view('admin.articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified article.
     */
    public function edit(Article $article)
    {
        $categories = Category::all();
        $tags = Tag::all();
        $selectedTags = $article->tags->pluck('id')->toArray();

        return view('admin.articles.edit', compact('article', 'categories', 'tags', 'selectedTags'));
    }

    /**
     * Update the specified article in storage.
     */
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'status' => 'required|in:draft,published,archived',
        ]);

        // Jika mengubah dari draft menjadi terpublikasi
        $wasDraft = $article->status === 'draft' && $request->status === 'published';

        $article->category_id = $request->category_id;
        $article->title = $request->title;

        // hanya mengubah slug jika judul artikel berubah
        if ($article->title !== $request->title) {
            $article->slug = Str::slug($request->title) . '-' . Str::random(5);
        }

        $article->content = $request->content;
        $article->excerpt = $request->excerpt;
        $article->status = $request->status;

        if ($request->hasFile('featured_image')) {
            // menghapus gambar unggulan lama
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }

            $path = $request->file('featured_image')->store('featured-images', 'public');
            $article->featured_image = $path;
        }

        // jika status artikel berubah menjadi diterbitkan
        if ($wasDraft) {
            $article->published_at = now();
        }

        $article->save();

        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        } else {
            $article->tags()->detach();
        }

        // mencatat aktivitas pembaruan artikel
        $actionDetail = $wasDraft ? 'Published article: ' : 'Updated article: ';
        activity_log('article_update', $actionDetail . $article->title, $request);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article updated successfully.');
    }

    /**
     * Approve the specified article.
     */
    public function approve(Article $article)
    {
        if ($article->status !== 'draft') {
            return redirect()->back()->with('error', 'Only draft articles can be approved.');
        }

        $article->status = 'published';
        $article->published_at = now();
        $article->save();

        // mencatat aktivitas artikel yang disetujui
        activity_log('article_approve', 'Approved article: ' . $article->title);

        return redirect()->route('admin.articles.moderation')
            ->with('success', 'Article approved and published successfully.');
    }

    /**
     * Reject the specified article.
     */
    public function reject(Request $request, Article $article)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if ($article->status !== 'draft') {
            return redirect()->back()->with('error', 'Only draft articles can be rejected.');
        }

        // mencatat aktivitas penolakan artikel
        activity_log('article_reject', 'Rejected article: ' . $article->title . '. Reason: ' . $request->rejection_reason, $request);

        return redirect()->route('admin.articles.moderation')
            ->with('success', 'Article rejection feedback sent to the author.');
    }

    /**
     * Remove the specified article from storage.
     */
    public function destroy(Article $article)
    {
        // menghapus gambar unggulan kalau ada
        if ($article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
        }

        $title = $article->title;
        $article->delete();

        // mencatat aktivitas penghapusan artikel
        activity_log('article_delete', 'Deleted article: ' . $title);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article deleted successfully.');
    }
}
