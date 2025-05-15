<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Display a listing of the tags.
     */
    public function index()
    {
        $tags = Tag::withCount('articles')->orderBy('name')->paginate(15);
        
        return view('admin.tags.index', compact('tags'));
    }
    
    /**
     * Show the form for creating a new tag.
     */
    public function create()
    {
        return view('admin.tags.create');
    }
    
    /**
     * Store a newly created tag in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags',
        ]);
        
        $tag = new Tag();
        $tag->name = $request->name;
        $tag->slug = Str::slug($request->name);
        $tag->save();
        
        // Log the tag creation
        activity_log('tag_create', 'Created tag: ' . $tag->name, $request);
        
        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag created successfully.');
    }
    
    /**
     * Display the specified tag.
     */
    public function show(Tag $tag)
    {
        $articles = $tag->articles()->with(['user', 'category'])->latest()->paginate(10);
        
        return view('admin.tags.show', compact('tag', 'articles'));
    }
    
    /**
     * Show the form for editing the specified tag.
     */
    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }
    
    /**
     * Update the specified tag in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
        ]);
        
        $tag->name = $request->name;
        
        // Only update slug if name has changed
        if ($tag->isDirty('name')) {
            $tag->slug = Str::slug($request->name);
        }
        
        $tag->save();
        
        // Log the tag update
        activity_log('tag_update', 'Updated tag: ' . $tag->name, $request);
        
        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag updated successfully.');
    }
    
    /**
     * Remove the specified tag from storage.
     */
    public function destroy(Tag $tag)
    {
        $name = $tag->name;
        
        // Detach the tag from all articles
        $tag->articles()->detach();
        
        $tag->delete();
        
        // Log the tag deletion
        activity_log('tag_delete', 'Deleted tag: ' . $name);
        
        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag deleted successfully.');
    }
}