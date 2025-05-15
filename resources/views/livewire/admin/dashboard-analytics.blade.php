<div>
    <!-- Timeframe Selector -->
    <div class="d-flex justify-content-end mb-4">
        <div class="btn-group" role="group">
            <button type="button" class="btn {{ $timeframe == '7' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('timeframe', '7')">Last 7 Days</button>
            <button type="button" class="btn {{ $timeframe == '30' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('timeframe', '30')">Last 30 Days</button>
            <button type="button" class="btn {{ $timeframe == '90' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('timeframe', '90')">Last 90 Days</button>
        </div>
    </div>
    
    <!-- Summary Stats -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-5 fw-bold">{{ number_format($statsData['totalArticles']) }}</div>
                            <div>Total Articles</div>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('admin.articles.index') }}">View Details</a>
                    <div class="small text-white"><i class="bi bi-arrow-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-5 fw-bold">{{ number_format($statsData['pendingModeration']) }}</div>
                            <div>Pending Moderation</div>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('admin.articles.moderation') }}">View Details</a>
                    <div class="small text-white"><i class="bi bi-arrow-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-5 fw-bold">{{ number_format($statsData['totalUsers']) }}</div>
                            <div>Total Users</div>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('admin.users.index') }}">View Details</a>
                    <div class="small text-white"><i class="bi bi-arrow-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-5 fw-bold">{{ number_format($statsData['totalViews']) }}</div>
                            <div>Total Article Views</div>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-eye"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('admin.article.analytics') }}">View Details</a>
                    <div class="small text-white"><i class="bi bi-arrow-right"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-bar-chart-line me-1"></i>
                        Articles Published
                    </div>
                    <div class="text-muted small">
                        Last {{ $timeframe }} days
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="articlesChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-bar-chart-line me-1"></i>
                        New User Registrations
                    </div>
                    <div class="text-muted small">
                        Last {{ $timeframe }} days
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="usersChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Views Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-graph-up me-1"></i>
                        Article Views Trend
                    </div>
                    <div class="text-muted small">
                        Last {{ $timeframe }} days
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="viewsChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Articles Moderation and Top Articles -->
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
    
    <!-- Categories and Recent Comments -->
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
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Define charts
        let articlesChart = null;
        let usersChart = null;
        let viewsChart = null;
        
        // Create or update charts when data changes
        document.addEventListener('livewire:initialized', () => {
            createOrUpdateCharts(@json($chartData));
            
            @this.on('chartDataUpdated', (data) => {
                createOrUpdateCharts(data);
            });
        });
        
        function createOrUpdateCharts(chartData) {
            const dates = chartData.dates.map(date => {
                const d = new Date(date);
                return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            });
            
            // Articles Chart
            const articlesCtx = document.getElementById('articlesChart');
            if (articlesChart) {
                articlesChart.data.labels = dates;
                articlesChart.data.datasets[0].data = chartData.articles;
                articlesChart.update();
            } else {
                articlesChart = new Chart(articlesCtx, {
                    type: 'bar',
                    data: {
                        labels: dates,
                        datasets: [{
                            label: 'Articles Published',
                            data: chartData.articles,
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
            }
            // Users Chart
            const usersCtx = document.getElementById('usersChart');
            if (usersChart) {
                usersChart.data.labels = dates;
                usersChart.data.datasets[0].data = chartData.users;
                usersChart.update();
            } else {
                usersChart = new Chart(usersCtx, {
                    type: 'line',
                    data: {
                        labels: dates,
                        datasets: [{
                            label: 'New Users',
                            data: chartData.users,
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
            }
            
            // Views Chart
            const viewsCtx = document.getElementById('viewsChart');
            if (viewsChart) {
                viewsChart.data.labels = dates;
                viewsChart.data.datasets[0].data = chartData.views;
                viewsChart.update();
            } else {
                viewsChart = new Chart(viewsCtx, {
                    type: 'line',
                    data: {
                        labels: dates,
                        datasets: [{
                            label: 'Article Views',
                            data: chartData.views,
                            fill: true,
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            borderColor: 'rgba(220, 53, 69, 1)',
                            borderWidth: 2,
                            tension: 0.1,
                            pointRadius: 3,
                            pointBackgroundColor: 'rgba(220, 53, 69, 1)',
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
            }
        }
    </script>
    @endpush
</div>