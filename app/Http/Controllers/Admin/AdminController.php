<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function dashboard()
    {
        $totalArticles = Article::count();
        $publishedArticles = Article::where('status', 'published')->count();
        $draftArticles = Article::where('status', 'draft')->count();
        $pendingModeration = Article::where('status', 'draft')->count();
        $totalUsers = User::where('role', 'user')->count();
        $totalViews = Article::sum('view_count');
        $totalComments = Comment::count();

        // artikel yang dipublikasikan per hari selama 30 hari terakhir
        $articlesPerDay = Article::where('status', 'published')
            ->where('published_at', '>=', Carbon::now()->subDays(30))
            ->greopBy('data')
            ->get([
                DB::raw('DATE(published_at) as date'),
                DB::raw('COUNT(*) as count')
            ])
            ->pluck('count', 'date')
            ->toArray();

        // artikel yang paling banyak dilihat
        $topArticles = Article::where('status', 'published')
            ->orderBy('view_count', 'desc')
            ->take(5)
            ->with('user', 'category')
            ->get();

        // artikel tertunda terbaru
        $latestPendingArticles = Article::where('status', 'draft')
            ->latest()
            ->take(5)
            ->with('user', 'category')
            ->get();

        // komentar terbaru
        $recentComments = Comment::where('is_approved', false)
            ->latest()
            ->take(5)
            ->with('user', 'article')
            ->get();

        // kategori teratas
        $topCategories = Category::withCount(['articles' => function ($query) {
            $query->where('status', 'published');
        }])
            ->orderBy('articles_count', 'desc')
            ->take(5)
            ->get();

        // pendaftaran pengguna per hari selama 30 hari terakhir
        $registrationsPerDay = User::where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            ])
            ->pluck('count', 'date')
            ->toArray();

        return view('admin.dashboard', compact(
            'totalArticles',
            'publishedArticles',
            'draftArticles',
            'pendingModeration',
            'totalUsers',
            'totalViews',
            'totalComments',
            'articlesPerDay',
            'topArticles',
            'latestPendingArticles',
            'recentComments',
            'topCategories',
            'registrationsPerDay'
        ));
    }

    /**
     * Display article analytics.
     */
    public function articleAnalytics()
    {
        // artikel dengan jumlah tampilan
        $articles = Article::where('status', 'published')
            ->with('user', 'category')
            ->orderBy('view_count', 'desc')
            ->get();

        // total tampilan per kategori
        $viewsByCategory = Category::withSum(['articles' => function ($query) {
            $query->where('status', 'published');
        }], 'view_count')
            ->orderBy('articles_view_count_sum', 'desc')
            ->get();

        // tampilan per hari untuk 30 hari terakhir
        $viewsPerDay = Article::where('status', 'published')
            ->where('published_at', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('DATE(published_at) as date'),
                DB::raw('SUM(view_count) as total_views')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total_views', 'date')
            ->toArray();

        // total artikel per penulis
        $articlesByAuthor = User::withCount(['articles' => function ($query) {
            $query->where('status', 'published');
        }])
            ->where('role', 'user')
            ->orderBy('articles_count', 'desc')
            ->take(10)
            ->get();

        return view('admin.articles.analytics', compact(
            'articles',
            'viewsByCategory',
            'viewsPerDay',
            'articlesByAuthor'
        ));
    }

    /**
     * Display the admin activity logs.
     */
    public function activityLogs()
    {
        $logs = DB::table('activity_logs')
            ->join('users', 'activity_logs.user_id', '=', 'users.id')
            ->select('activity_logs.*', 'users.name', 'users.email')
            ->orderBy('activity_logs.created_at', 'desc')
            ->paginate(15);

        return view('admin.activity-logs', compact('logs'));
    }

    /**
     * Handle error logging.
     */
    public function errorLogs()
    {
        return view('admin.error-logs');
    }
}
