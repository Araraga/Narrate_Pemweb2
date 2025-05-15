@extends('admin.layouts.app')

@section('title', 'Article Analytics')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Article Analytics</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Article Analytics</li>
    </ol>
    
    <!-- Quick Stats -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-5 fw-bold">{{ number_format($articles->sum('view_count')) }}</div>
                            <div>Total Views</div>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-eye"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-5 fw-bold">{{ number_format($articles->count()) }}</div>
                            <div>Total Articles</div>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-5 fw-bold">{{ number_format($articles->average('view_count')) }}</div>
                            <div>Avg. Views Per Article</div>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-bar-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-5 fw-bold">{{ $viewsByCategory->count() }}</div>
                            <div>Active Categories</div>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-folder"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-graph-up me-1"></i>
                    Views Trend (Last 30 Days)
                </div>
                <div class="card-body">
                    <canvas id="viewsTrendChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-pie-chart me-1"></i>
                    Views by Category
                </div>
                <div class="card-body">
                    <canvas id="categoryViewsChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-trophy me-1"></i>
                    Top 10 Authors
                </div>
                <div class="card-body">
                    <canvas id="authorArticlesChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-bar-chart me-1"></i>
                    Article Performance Metrics
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Most Recent Article</h6>
                                    @if($articles->isNotEmpty())
                                        @php $recentArticle = $articles->sortByDesc('published_at')->first(); @endphp
                                        <h5 class="mb-2">{{ Str::limit($recentArticle->title, 50) }}</h5>
                                        <div class="small text-muted mb-2">{{ $recentArticle->user->name }} • {{ $recentArticle->published_at->format('M d, Y') }}</div>
                                        <div class="d-flex align-items-center">
                                            <span class="fs-5 fw-bold text-primary me-2">{{ number_format($recentArticle->view_count) }}</span>
                                            <span class="text-muted">views</span>
                                        </div>
                                    @else
                                        <p class="card-text">No data available</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Top Category</h6>
                                    @if($viewsByCategory->isNotEmpty())
                                        @php $topCategory = $viewsByCategory->sortByDesc('articles_view_count_sum')->first(); @endphp
                                        <h5 class="mb-2">{{ $topCategory->name }}</h5>
                                        <div class="d-flex align-items-center">
                                            <span class="fs-5 fw-bold text-primary me-2">{{ number_format($topCategory->articles_view_count_sum) }}</span>
                                            <span class="text-muted">total views</span>
                                        </div>
                                    @else
                                        <p class="card-text">No data available</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Top Author</h6>
                                    @if($articlesByAuthor->isNotEmpty())
                                        @php $topAuthor = $articlesByAuthor->first(); @endphp
                                        <h5 class="mb-2">{{ $topAuthor->name }}</h5>
                                        <div class="d-flex align-items-center">
                                            <span class="fs-5 fw-bold text-primary me-2">{{ $topAuthor->articles_count }}</span>
                                            <span class="text-muted">published articles</span>
                                        </div>
                                    @else
                                        <p class="card-text">No data available</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Articles Table -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-table me-1"></i>
            Top Articles by Views
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover datatable">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Views</th>
                            <th>Published</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articles->sortByDesc('view_count')->take(20) as $article)
                        <tr>
                            <td>{{ Str::limit($article->title, 50) }}</td>
                            <td>{{ $article->user->name }}</td>
                            <td>{{ $article->category->name }}</td>
                            <td>{{ number_format($article->view_count) }}</td>
                            <td>{{ $article->published_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.articles.show', $article) }}" class="btn btn-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Views by Category Table -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-grid-3x3-gap me-1"></i>
            Performance by Category
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover datatable">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Total Articles</th>
                            <th>Total Views</th>
                            <th>Avg. Views Per Article</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($viewsByCategory->sortByDesc('articles_view_count_sum') as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->articles_count ?? 0 }}</td>
                            <td>{{ number_format($category->articles_view_count_sum ?? 0) }}</td>
                            <td>{{ $category->articles_count ? number_format($category->articles_view_count_sum / $category->articles_count) : 0 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Process data for charts
    const viewsPerDay = @json($viewsPerDay);
    const categoriesData = @json($viewsByCategory);
    const authorsData = @json($articlesByAuthor);
    
    // Prepare dates for last 30 days
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
    
    // Prepare chart data
    const prepareViewsData = () => {
        return dates.map(date => viewsPerDay[date] || 0);
    };
    
    // Views Trend Chart
    const viewsTrendCtx = document.getElementById('viewsTrendChart');
    new Chart(viewsTrendCtx, {
        type: 'line',
        data: {
            labels: dates.map(date => {
                const d = new Date(date);
                return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            }),
            datasets: [{
                label: 'Article Views',
                data: prepareViewsData(),
                fill: true,
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 2,
                tension: 0.1,
                pointRadius: 3,
                pointBackgroundColor: 'rgba(13, 110, 253, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false,
                },
                legend: {
                    display: true,
                    position: 'top',
                }
            },
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
    
    // Category Views Chart
    const categoryViewsCtx = document.getElementById('categoryViewsChart');
    new Chart(categoryViewsCtx, {
        type: 'pie',
        data: {
            labels: categoriesData.map(category => category.name),
            datasets: [{
                data: categoriesData.map(category => category.articles_view_count_sum || 0),
                backgroundColor: [
                    'rgba(13, 110, 253, 0.7)',
                    'rgba(25, 135, 84, 0.7)',
                    'rgba(220, 53, 69, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(13, 202, 240, 0.7)',
                    'rgba(111, 66, 193, 0.7)',
                    'rgba(214, 51, 132, 0.7)',
                    'rgba(102, 16, 242, 0.7)',
                    'rgba(253, 126, 20, 0.7)',
                    'rgba(32, 201, 151, 0.7)'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${context.label}: ${value.toLocaleString()} views (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    
    // Author Articles Chart
    const authorArticlesCtx = document.getElementById('authorArticlesChart');
    new Chart(authorArticlesCtx, {
        type: 'bar',
        data: {
            labels: authorsData.map(author => author.name),
            datasets: [{
                label: 'Published Articles',
                data: authorsData.map(author => author.articles_count),
                backgroundColor: 'rgba(25, 135, 84, 0.7)',
                borderColor: 'rgba(25, 135, 84, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    
    $(document).ready(function() {
        $('.datatable').DataTable({
            pageLength: 10,
            responsive: true
        });
    });
</script>
@endsection="card-body">