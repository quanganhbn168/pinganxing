<?php

namespace App\Livewire\WorkOrder;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\WorkOrder;
use App\Models\WorkOrderComment;
use App\Models\CommentAttachment;
use App\Models\TaskReport;
use App\Models\Admin;
use Illuminate\Support\Facades\Storage;

class WorkOrderDiscussion extends Component
{
    use WithFileUploads;

    public WorkOrder $workOrder;
    public $feedItems = []; // Combined comments + reports
    
    // Form data
    public $newComment = '';
    public $attachments = [];
    
    // Mention
    public $mentionSearch = '';
    public $mentionResults = [];
    public $showMentionDropdown = false;

    protected $listeners = ['refreshComments' => 'loadFeed'];

    public function mount(WorkOrder $workOrder)
    {
        $this->workOrder = $workOrder;
        $this->loadFeed();
        
        // Mark mentions as read when user views this page
        $this->markMentionsAsRead();
    }

    public function loadFeed()
    {
        // Load comments
        $comments = $this->workOrder->comments()
            ->with(['author', 'attachments', 'mentions.admin'])
            ->get()
            ->map(function ($comment) {
                return [
                    'type' => 'comment',
                    'id' => 'comment-' . $comment->id,
                    'data' => $comment,
                    'created_at' => $comment->created_at,
                    'author' => $comment->author,
                ];
            });

        // Load task reports from all tasks in this work order
        $taskIds = $this->workOrder->tasks()->pluck('id');
        $reports = TaskReport::whereIn('task_id', $taskIds)
            ->with(['reporter', 'task', 'images', 'items', 'returnedItems'])
            ->get()
            ->map(function ($report) {
                return [
                    'type' => 'report',
                    'id' => 'report-' . $report->id,
                    'data' => $report,
                    'created_at' => $report->created_at,
                    'author' => $report->reporter,
                ];
            });

        // Load spawned tasks (tasks that have parent_task_id set)
        // Tương thích với database cũ: kiểm tra column tồn tại
        $spawnedTasks = collect();
        if (\Schema::hasColumn('tasks', 'parent_task_id')) {
            $spawnedTasks = $this->workOrder->tasks()
                ->whereNotNull('parent_task_id')
                ->with(['performer', 'parentTask', 'createdByWorker'])
                ->get()
                ->map(function ($task) {
                    return [
                        'type' => 'task_spawn',
                        'id' => 'spawn-' . $task->id,
                        'data' => $task,
                        'created_at' => $task->created_at,
                        'author' => $task->createdByWorker ?? null,
                    ];
                });
        }

        // Merge all and sort by created_at descending (newest first)
        $allItems = collect()
            ->concat($comments)
            ->concat($reports)
            ->concat($spawnedTasks);
            
        $this->feedItems = $allItems
            ->sortByDesc('created_at')
            ->values()
            ->toArray();
    }

    // Alias for backward compatibility
    public function loadComments()
    {
        $this->loadFeed();
    }

    /**
     * Tìm kiếm user để @mention
     */
    public function searchMentions($query)
    {
        if (strlen($query) < 1) {
            $this->mentionResults = [];
            $this->showMentionDropdown = false;
            return;
        }

        $this->mentionResults = Admin::getMentionableForWorkOrder($this->workOrder)
            ->filter(function ($admin) use ($query) {
                return str_contains(strtolower($admin->name), strtolower($query));
            })
            ->take(5)
            ->values();
        
        $this->showMentionDropdown = count($this->mentionResults) > 0;
    }

    /**
     * Chọn user để mention
     */
    public function selectMention($adminId)
    {
        $admin = Admin::find($adminId);
        if ($admin) {
            // Thêm mention format vào content
            $this->dispatch('insert-mention', [
                'id' => $admin->id,
                'name' => $admin->name,
            ]);
        }
        
        $this->showMentionDropdown = false;
        $this->mentionResults = [];
    }

    /**
     * Gửi comment mới
     */
    public function sendComment()
    {
        // Validate
        if (empty(trim($this->newComment)) && count($this->attachments) === 0) {
            session()->flash('error', 'Vui lòng nhập nội dung hoặc đính kèm file.');
            return;
        }

        // Validate file sizes
        foreach ($this->attachments as $file) {
            $ext = strtolower($file->getClientOriginalExtension());
            $type = CommentAttachment::getTypeFromExtension($ext);
            $maxSize = CommentAttachment::getMaxSizeForType($type);
            
            if ($file->getSize() > $maxSize) {
                $maxMB = $maxSize / 1024 / 1024;
                session()->flash('error', "File {$file->getClientOriginalName()} vượt quá {$maxMB}MB.");
                return;
            }
        }

        // Create comment
        $comment = WorkOrderComment::create([
            'work_order_id' => $this->workOrder->id,
            'admin_id' => auth('admin')->id(),
            'content' => $this->newComment,
        ]);

        // Save attachments
        foreach ($this->attachments as $file) {
            $ext = strtolower($file->getClientOriginalExtension());
            $type = CommentAttachment::getTypeFromExtension($ext);
            $path = $file->store('comments/' . date('Y-m'), 'public');
            
            CommentAttachment::create([
                'work_order_comment_id' => $comment->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $type,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        // Parse and create mentions
        $comment->parseMentions();

        // Reset form
        $this->newComment = '';
        $this->attachments = [];
        
        // Reload comments
        $this->loadComments();
    }

    /**
     * Xóa comment (chỉ owner hoặc admin)
     */
    public function deleteComment($commentId)
    {
        $comment = WorkOrderComment::find($commentId);
        
        if (!$comment) return;
        
        if (!$comment->canDelete(auth('admin')->user())) {
            session()->flash('error', 'Bạn không có quyền xóa bình luận này.');
            return;
        }

        // Delete attachments from storage
        foreach ($comment->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $comment->delete();
        $this->loadComments();
        
        session()->flash('comment_success', 'Đã xóa bình luận.');
    }

    /**
     * Xóa attachment
     */
    public function removeAttachment($index)
    {
        if (isset($this->attachments[$index])) {
            unset($this->attachments[$index]);
            $this->attachments = array_values($this->attachments);
        }
    }

    /**
     * Đánh dấu đã đọc các mentions trong WO này
     */
    protected function markMentionsAsRead()
    {
        $userId = auth('admin')->id();
        
        // Lấy tất cả comment IDs của WO này
        $commentIds = $this->workOrder->comments()->pluck('id');
        
        // Mark as read
        \App\Models\CommentMention::whereIn('work_order_comment_id', $commentIds)
            ->where('admin_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function render()
    {
        return view('livewire.work-order.work-order-discussion');
    }
}
