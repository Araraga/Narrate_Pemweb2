<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Admin auth routes
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/article-analytics', [AdminController::class, 'articleAnalytics'])->name('article.analytics');
    Route::get('/activity-logs', [AdminController::class, 'activityLogs'])->name('activity.logs');
    Route::get('/error-logs', [AdminController::class, 'errorLogs'])->name('error.logs');

    // Auth dan profile
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::get('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('password.form');
    Route::get('/change-password', [AuthController::class, 'changePassword'])->name('password.update');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Articles
    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::get('/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/article/{article}', [ArticleController::class, 'show'])->name('articles.show');
    Route::get('/articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::get('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::get('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');

    // Article Moderation
    Route::get('/articles/drafts', [ArticleController::class, 'drafts'])->name('articles.drafts');
    Route::get('/moderation', [ArticleController::class, 'moderationQueue'])->name('articles.moderation');
    Route::get('/articles/{article}/approve', [ArticleController::class, 'approve'])->name('articles.approve');
    Route::get('/articles/{article}/reject', [ArticleController::class, 'reject'])->name('articles.reject');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Tags
    Route::resource('tags', TagController::class);

    // Comments
    Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::get('/comments/pending', [CommentController::class, 'pending'])->name('comments.pending');
    Route::put('/comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
    Route::put('/comments/{comment}/reject', [CommentController::class, 'reject'])->name('comments.reject');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Userds
    Route::resource('users', UserController::class);
});
