@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    {{-- Summary Stats --}}
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-5 fw-bold">{{ number_format($totalArticles) }}</div>
                            <div>Total Artikel</div>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a  class="small text-white stretched-link" href="{{ route('admin.user.index') }}">View Details</a>
                    <div class="small text-white"><i class="bi bi-arrow-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-5 fw-bold">{{ number_format($totalViews) }}</div>
                            <div>Total Artikel Views</div>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-eye"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('admin.article.analytics') }}" >View Details</a>
                    <div class="small text-white"><i class="bi bi-arrow-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-bar-chart-line me-1"></i>
                    Articles Published Last 30 Days
                </div>
                <div class="card-body">
                    <canvas id="articlesChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-bar-chart-line me-1"></i>
                    New Users Last 30 Days
                </div>
                <div class="card-body">
                    <canvas id="usersChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Articles Moderation and Top Articles --}}
    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-hourglass-split me-1"></i>
                    Articles Pending Moderation
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestPendingArticles as $article)
                                <tr>
                                    <td>{{ Str::limit($article->title, 30) }}</td>
                                    <td>{{ $article->user->name }}</td>
                                    <td>{{ $article->category->name }}</td>
                                    <td>{{ $article->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.articles.show', $article) }}" class="btn btn-primary" title="View"><i class="bi bi-eye"></i></a>
                                            <form action="{{ route('admin.articles.approve', $article) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-success" title="Approve"><i class="bi bi-check-lg"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No pending articles</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('admin.articles.moderation') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-trophy me-1"></i>
                    Top Viewed Articles
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Views</th>
                                    <th>Published</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topArticles as $article)
                                <tr>
                                    <td>{{ Str::limit($article->title, 30) }}</td>
                                    <td>{{ $article->category->name }}</td>
                                    <td>{{ number_format($article->view_count) }}</td>
                                    <td>{{ $article->published_at->format('M d, Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No articles found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('admin.article.analytics') }}" class="btn btn-sm btn-primary">View Analytics</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Categories and Recent Comments --}}
    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-folder me-1"></i>
                    Top Categories
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Articles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCategories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->articles_count }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center">No categories found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-primary">Manage Categories</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-chat-left-text me-1"></i>
                    Recent Comments Pending Approval
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Article</th>
                                    <th>Comment</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentComments as $comment)
                                <tr>
                                    <td>{{ $comment->user->name }}</td>
                                    <td>{{ Str::limit($comment->article->title, 20) }}</td>
                                    <td>{{ Str::limit($comment->content, 30) }}</td>
                                    <td>{{ $comment->created_at->format('M d, Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No pending comments</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('admin.comments.pending') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    const articlesData = @json($articlesPerDay);
    const usersData = @json($registrationsPerDay);
    
    // Data 30 hari terakhir
    const getDates = () => {
        const dates = [];
        for (let i = 30; i >= 0; i--) {
            const date = new Date();
            date.setDate(date.getDate() - i);
            const formattedDate = date.toISOString().split('T')[0];
            dates.push(formattedDate);
        }
        return dates;
    };
    
    const dates = getDates();
    
    // chart data
    const prepareChartData = (sourceData) => {
        return dates.map(date => sourceData[date] || 0);
    };
    
    // membuat artikel chart
    const articlesCtx = document.getElementById('articlesChart');
    new Chart(articlesCtx, {
        type: 'bar',
        data: {
            labels: dates.map(date => {
                const d = new Date(date);
                return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            }),
            datasets: [{
                label: 'Articles Published',
                data: prepareChartData(articlesData),
                backgroundColor: 'rgba(0, 123, 255, 0.5)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    
    // membuat user chart
    const usersCtx = document.getElementById('usersChart');
    new Chart(usersCtx, {
        type: 'line',
        data: {
            labels: dates.map(date => {
                const d = new Date(date);
                return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            }),
            datasets: [{
                label: 'New Users',
                data: prepareChartData(usersData),
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endsection 