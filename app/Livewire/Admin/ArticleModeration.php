<?php

namespace App\Livewire\Admin;

use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ArticleModeration extends Component
{
    use WithPagination;
    
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $status = 'draft';
    
    // Moderation properties
    public $selectedArticle = null;
    public $rejectionReason = '';
    public $showPreviewModal = false;
    public $showApproveModal = false;
    public $showRejectModal = false;
    
    protected $listeners = [
        'refresh' => '$refresh'
    ];
    
    protected $rules = [
        'rejectionReason' => 'required|string|min:10|max:1000',
    ];
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingStatus()
    {
        $this->resetPage();
    }
    
    public function selectArticle($articleId)
    {
        $this->selectedArticle = Article::with(['user', 'category', 'tags'])->findOrFail($articleId);
        $this->rejectionReason = '';
    }
    
    public function openPreviewModal($articleId)
    {
        $this->selectArticle($articleId);
        $this->showPreviewModal = true;
    }
    
    public function openApproveModal($articleId)
    {
        $this->selectArticle($articleId);
        $this->showApproveModal = true;
    }
    
    public function openRejectModal($articleId)
    {
        $this->selectArticle($articleId);
        $this->showRejectModal = true;
    }
    
    public function closeModals()
    {
        $this->showPreviewModal = false;
        $this->showApproveModal = false;
        $this->showRejectModal = false;
        $this->selectedArticle = null;
        $this->rejectionReason = '';
    }
    
    public function approveArticle()
    {
        if (!$this->selectedArticle) {
            return;
        }
        
        $this->selectedArticle->status = 'published';
        $this->selectedArticle->published_at = now();
        $this->selectedArticle->save();
        
        // Log the article approval activity
        activity_log('article_approve', 'Approved article: ' . $this->selectedArticle->title);
        
        $this->closeModals();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Article has been approved and published successfully.'
        ]);
    }
    
    public function rejectArticle()
    {
        $this->validate();
        
        if (!$this->selectedArticle) {
            return;
        }
        
        // In a real application, you would send a notification to the author
        // with the rejection reason
        
        // Log the article rejection activity
        activity_log('article_reject', 'Rejected article: ' . $this->selectedArticle->title . '. Reason: ' . $this->rejectionReason);
        
        $this->closeModals();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Article rejection feedback has been sent to the author.'
        ]);
    }
    
    public function render()
    {
        $query = Article::query()
            ->where('status', $this->status)
            ->when($this->search, function ($query) {
                $query->where(function ($subquery) {
                    $subquery->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('content', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('category', function ($categoryQuery) {
                            $categoryQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->with(['user', 'category'])
            ->orderBy($this->sortField, $this->sortDirection);
        
        $articles = $query->paginate($this->perPage);
        
        return view('livewire.admin.article-moderation', [
            'articles' => $articles,
        ]);
    }
}