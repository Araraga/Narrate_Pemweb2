<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of all comments.
     */
    public function index()
    {
        $comments = Comment::with(['user', 'article'])
            ->latest()
            ->paginate(15);
            
        return view('admin.comments.index', compact('comments'));
    }
    
    /**
     * Display a listing of pending comments.
     */
    public function pending()
    {
        $comments = Comment::with(['user', 'article'])
            ->where('is_approved', false)
            ->latest()
            ->paginate(15);
            
        return view('admin.comments.pending', compact('comments'));
    }
    
    /**
     * Approve the specified comment.
     */
    public function approve(Comment $comment)
    {
        $comment->is_approved = true;
        $comment->save();
        
        // Log the comment approval
        activity_log('comment_approve', 'Approved comment ID: ' . $comment->id);
        
        return redirect()->back()->with('success', 'Comment approved successfully.');
    }
    
    /**
     * Reject the specified comment.
     */
    public function reject(Comment $comment)
    {
        $comment->is_approved = false;
        $comment->save();
        
        // Log the comment rejection
        activity_log('comment_reject', 'Rejected comment ID: ' . $comment->id);
        
        return redirect()->back()->with('success', 'Comment rejected successfully.');
    }
    
    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Comment $comment)
    {
        // Delete all replies to this comment first
        Comment::where('parent_id', $comment->id)->delete();
        
        // Then delete the comment itself
        $comment->delete();
        
        // Log the comment deletion
        activity_log('comment_delete', 'Deleted comment ID: ' . $comment->id);
        
        return redirect()->back()->with('success', 'Comment and its replies deleted successfully.');
    }
}