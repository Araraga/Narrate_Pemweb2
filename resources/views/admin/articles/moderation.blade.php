@extends('admin.layouts.app')

@section('title', 'Article Moderation')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Article Moderation</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Article Moderation</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-shield-check me-1"></i>
            Articles Pending Moderation
        </div>
        <div class="card-body">
            @if($articles->isEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No articles are currently pending moderation.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($articles as $article)
                            <tr>
                                <td>{{ $article->id }}</td>
                                <td>{{ Str::limit($article->title, 50) }}</td>
                                <td>{{ $article->user->name }}</td>
                                <td>{{ $article->category->name }}</td>
                                <td>{{ $article->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#previewModal{{ $article->id }}" title="Preview">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $article->id }}" title="Approve">
                                            <i class="bi bi-check-lg"></i>
                                        </a>
                                        <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $article->id }}" title="Reject">
                                            <i class="bi bi-x-lg"></i>
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
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $articles->links() }}
                </div>
                
                <!-- Preview Modals -->
                @foreach($articles as $article)
                <div class="modal fade modal-lg" id="previewModal{{ $article->id }}" tabindex="-1" aria-labelledby="previewModalLabel{{ $article->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="previewModalLabel{{ $article->id }}">Preview Article</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <div>
                                        <strong>Author:</strong> {{ $article->user->name }}<br>
                                        <strong>Category:</strong> {{ $article->category->name }}<br>
                                        <strong>Submitted:</strong> {{ $article->created_at->format('M d, Y H:i') }}
                                    </div>
                                    <div>
                                        @if($article->tags->isNotEmpty())
                                            <strong>Tags:</strong> 
                                            @foreach($article->tags as $tag)
                                                <span class="badge bg-secondary">{{ $tag->name }}</span>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                
                                <h3>{{ $article->title }}</h3>
                                
                                @if($article->featured_image)
                                    <div class="text-center my-3">
                                        <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" class="img-fluid rounded">
                                    </div>
                                @endif
                                
                                @if($article->excerpt)
                                    <div class="lead mb-4">{{ $article->excerpt }}</div>
                                @endif
                                
                                <div class="article-content">
                                    {!! $article->content !!}
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $article->id }}" data-bs-dismiss="modal">Approve</button>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $article->id }}" data-bs-dismiss="modal">Reject</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Approve Modal -->
                <div class="modal fade" id="approveModal{{ $article->id }}" tabindex="-1" aria-labelledby="approveModalLabel{{ $article->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="approveModalLabel{{ $article->id }}">Approve Article</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to approve and publish the article <strong>"{{ $article->title }}"</strong>?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form action="{{ route('admin.articles.approve', $article) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success">Approve & Publish</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Reject Modal -->
                <div class="modal fade" id="rejectModal{{ $article->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $article->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="rejectModalLabel{{ $article->id }}">Reject Article</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('admin.articles.reject', $article) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <p>Please provide a reason for rejecting the article <strong>"{{ $article->title }}"</strong>:</p>
                                    <div class="mb-3">
                                        <label for="rejection_reason" class="form-label">Rejection Reason</label>
                                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required></textarea>
                                        <div class="form-text">This feedback will be sent to the author.</div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Reject Article</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-info-circle me-1"></i>
            Moderation Guidelines
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-check2-circle text-success me-2"></i>Content Quality</h5>
                            <p class="card-text">Ensure articles are well-written, factually accurate, and provide value to readers. Check for proper grammar, spelling, and formatting.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-shield text-primary me-2"></i>Editorial Policy</h5>
                            <p class="card-text">Articles must adhere to our editorial policy, including standards for objectivity, fairness, and balanced reporting on sensitive topics.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Legal Compliance</h5>
                            <p class="card-text">Verify articles don't contain plagiarism, copyright infringement, defamation, or other legal issues that could expose us to liability.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .article-content img {
        max-width: 100%;
        height: auto;
    }
    
    .modal-lg {
        max-width: 800px;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable with custom options
        $('.datatable').DataTable({
            order: [[4, 'desc']], // Sort by submission date (descending)
            pageLength: 10,
            language: {
                search: "Search articles:",
                lengthMenu: "Show _MENU_ articles per page",
                info: "Showing _START_ to _END_ of _TOTAL_ articles",
                infoEmpty: "Showing 0 to 0 of 0 articles",
                infoFiltered: "(filtered from _MAX_ total articles)"
            }
        });
    });
</script>
@endsection