<div>
    <!-- Card with filter controls -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-filter me-1"></i> Filters
                </div>
                <div class="d-flex">
                    <div class="btn-group btn-group-sm me-2">
                        <button type="button" class="btn {{ $status === 'draft' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('status', 'draft')">
                            Pending ({{ App\Models\Article::where('status', 'draft')->count() }})
                        </button>
                        <button type="button" class="btn {{ $status === 'published' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('status', 'published')">
                            Published
                        </button>
                        <button type="button" class="btn {{ $status === 'archived' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('status', 'archived')">
                            Archived
                        </button>
                    </div>
                    <select class="form-select form-select-sm" wire:model.live="perPage">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search articles..." wire:model.live.debounce.500ms="search">
                        @if($search)
                            <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content card -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-shield-check me-1"></i>
            {{ $status === 'draft' ? 'Articles Pending Moderation' : ($status === 'published' ? 'Published Articles' : 'Archived Articles') }}
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th wire:click="sortBy('title')" class="cursor-pointer">
                                Title 
                                @if($sortField === 'title')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('user_id')" class="cursor-pointer">
                                Author
                                @if($sortField === 'user_id')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('category_id')" class="cursor-pointer">
                                Category
                                @if($sortField === 'category_id')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('created_at')" class="cursor-pointer">
                                Submitted
                                @if($sortField === 'created_at')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @endif
                            </th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $article)
                            <tr wire:key="article-{{ $article->id }}">
                                <td>{{ $article->id }}</td>
                                <td>{{ Str::limit($article->title, 50) }}</td>
                                <td>{{ $article->user->name }}</td>
                                <td>{{ $article->category->name }}</td>
                                <td>{{ $article->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-primary" wire:click="openPreviewModal({{ $article->id }})" title="Preview">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        
                                        @if($status === 'draft')
                                            <button class="btn btn-success" wire:click="openApproveModal({{ $article->id }})" title="Approve">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button class="btn btn-danger" wire:click="openRejectModal({{ $article->id }})" title="Reject">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        @endif
                                        
                                        <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0 text-muted">No articles found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($articles->hasPages())
            <div class="card-footer">
                {{ $articles->links() }}
            </div>
        @endif
    </div>

    <!-- Preview Modal -->
    @if($selectedArticle)
        <div class="modal fade @if($showPreviewModal) show @endif" tabindex="-1" style="@if($showPreviewModal) display: block; @else display: none; @endif" 
             wire:click.self="closeModals()">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Preview Article</h5>
                        <button type="button" class="btn-close" wire:click="closeModals()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <strong>Author:</strong> {{ $selectedArticle->user->name }}<br>
                                <strong>Category:</strong> {{ $selectedArticle->category->name }}<br>
                                <strong>Submitted:</strong> {{ $selectedArticle->created_at->format('M d, Y H:i') }}
                            </div>
                            <div>
                                @if($selectedArticle->tags->isNotEmpty())
                                    <strong>Tags:</strong> 
                                    @foreach($selectedArticle->tags as $tag)
                                        <span class="badge bg-secondary">{{ $tag->name }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        
                        <h3>{{ $selectedArticle->title }}</h3>
                        
                        @if($selectedArticle->featured_image)
                            <div class="text-center my-3">
                                <img src="{{ asset('storage/' . $selectedArticle->featured_image) }}" alt="{{ $selectedArticle->title }}" class="img-fluid rounded">
                            </div>
                        @endif
                        
                        @if($selectedArticle->excerpt)
                            <div class="lead mb-4">{{ $selectedArticle->excerpt }}</div>
                        @endif
                        
                        <div class="article-content">
                            {!! $selectedArticle->content !!}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals()">Close</button>
                        @if($status === 'draft')
                            <button type="button" class="btn btn-success" wire:click="$set('showPreviewModal', false); $set('showApproveModal', true)">Approve</button>
                            <button type="button" class="btn btn-danger" wire:click="$set('showPreviewModal', false); $set('showRejectModal', true)">Reject</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Approve Modal -->
    @if($selectedArticle)
        <div class="modal fade @if($showApproveModal) show @endif" tabindex="-1" style="@if($showApproveModal) display: block; @else display: none; @endif"
             wire:click.self="closeModals()">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Approve Article</h5>
                        <button type="button" class="btn-close" wire:click="closeModals()"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to approve and publish the article <strong>"{{ $selectedArticle->title }}"</strong>?</p>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            This article will be immediately published and visible to all users.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals()">Cancel</button>
                        <button type="button" class="btn btn-success" wire:click="approveArticle()">
                            <i class="bi bi-check-lg me-1"></i> Approve & Publish
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Reject Modal -->
    @if($selectedArticle)
        <div class="modal fade @if($showRejectModal) show @endif" tabindex="-1" style="@if($showRejectModal) display: block; @else display: none; @endif"
             wire:click.self="closeModals()">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Article</h5>
                        <button type="button" class="btn-close" wire:click="closeModals()"></button>
                    </div>
                    <div class="modal-body">
                        <p>Please provide a reason for rejecting the article <strong>"{{ $selectedArticle->title }}"</strong>:</p>
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">Rejection Reason</label>
                            <textarea class="form-control @error('rejectionReason') is-invalid @enderror" id="rejection_reason" wire:model="rejectionReason" rows="4"></textarea>
                            <div class="form-text">This feedback will be sent to the author.</div>
                            @error('rejectionReason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals()">Cancel</button>
                        <button type="button" class="btn btn-danger" wire:click="rejectArticle()">
                            <i class="bi bi-x-lg me-1"></i> Reject Article
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Backdrop -->
    @if($showPreviewModal || $showApproveModal || $showRejectModal)
        <div class="modal-backdrop fade show"></div>
    @endif
    
    <!-- Toast notification for actions -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="notificationToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-bell me-2"></i>
                <strong class="me-auto" id="notificationTitle">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="notificationMessage"></div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('notify', (data) => {
                const toast = document.getElementById('notificationToast');
                const toastBody = document.getElementById('notificationMessage');
                const toastTitle = document.getElementById('notificationTitle');
                
                if (data.type === 'success') {
                    toast.classList.add('bg-light');
                    toast.classList.remove('bg-danger');
                    toastTitle.textContent = 'Success';
                } else if (data.type === 'error') {
                    toast.classList.add('bg-danger');
                    toast.classList.remove('bg-light');
                    toastTitle.textContent = 'Error';
                }
                
                toastBody.textContent = data.message;
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
            });
        });
    </script>
    @endpush
</div>