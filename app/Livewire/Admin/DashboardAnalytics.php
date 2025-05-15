<?php

namespace App\Livewire\Admin;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardAnalytics extends Component
{
    public $timeframe = '30'; // Default to 30 days
    public $chartData = [];
    public $statsData = [];

    protected $listeners = [
        'refresh' => '$refresh'
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function updatedTimeframe()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Get basic stats
        $this->statsData = [
            'totalArticles' => Article::count(),
            'publishedArticles' => Article::where('status', 'published')->count(),
            'draftArticles' => Article::where('status', 'draft')->count(),
            'pendingModeration' => Article::where('status', 'draft')->count(),
            'totalUsers' => User::where('role', 'user')->count(),
            'totalViews' => Article::sum('view_count'),
            'totalComments' => Comment::count(),
        ];

        // Get time period
        $startDate = Carbon::now()->subDays($this->timeframe);

        // Get articles published per day
        $articlesPerDay = Article::where('status', 'published')
            ->where('published_at', '>=', $startDate)
            ->groupBy('date')
            ->get([
                DB::raw('DATE(published_at) as date'),
                DB::raw('COUNT(*) as count')
            ])
            ->pluck('count', 'date')
            ->toArray();

        // Get user registrations per day
        $registrationsPerDay = User::where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            ])
            ->pluck('count', 'date')
            ->toArray();

        // Get views per day
        $viewsPerDay = Article::where('status', 'published')
            ->where('published_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(published_at) as date'),
                DB::raw('SUM(view_count) as total_views')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total_views', 'date')
            ->toArray();

        // Get the date range
        $dateRange = [];
        for ($i = 0; $i < intval($this->timeframe); $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dateRange[$date] = $date;
        }
        $dateRange = array_reverse($dateRange);

        // Format chart data
        $this->chartData = [
            'dates' => array_values($dateRange),
            'articles' => $this->prepareChartData($dateRange, $articlesPerDay),
            'users' => $this->prepareChartData($dateRange, $registrationsPerDay),
            'views' => $this->prepareChartData($dateRange, $viewsPerDay),
        ];

        // Get top articles
        $this->topArticles = Article::where('status', 'published')
            ->orderBy('view_count', 'desc')
            ->take(5)
            ->with('user', 'category')
            ->get();

        // Get latest pending articles
        $this->latestPendingArticles = Article::where('status', 'draft')
            ->latest()
            ->take(5)
            ->with('user', 'category')
            ->get();

        // Get recent comments
        $this->recentComments = Comment::where('is_approved', false)
            ->latest()
            ->take(5)
            ->with('user', 'article')
            ->get();

        // Get top categories
        $this->topCategories = Category::withCount(['articles' => function ($query) {
            $query->where('status', 'published');
        }])
            ->orderBy('articles_count', 'desc')
            ->take(5)
            ->get();
    }

    /**
     * Prepare chart data for a given date range.
     */
    private function prepareChartData($dateRange, $data)
    {
        return array_map(function ($date) use ($data) {
            return $data[$date] ?? 0;
        }, $dateRange);
    }

    public function render()
    {
        return view('livewire.admin.dashboard-analytics', [
            'topArticles' => $this->topArticles,
            'latestPendingArticles' => $this->latestPendingArticles,
            'recentComments' => $this->recentComments,
            'topCategories' => $this->topCategories,
        ]);
    }
}
