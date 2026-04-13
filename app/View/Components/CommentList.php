<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CommentList extends Component
{
    public $comments;
    public $totalComments;
    public $averageRating;
    public $ratingCounts;

    /**
     * Create a new component instance.
     */
    public function __construct($comments)
    {
        $this->comments = $comments;
        
        $this->totalComments = $comments ? $comments->count() : 0;
        $this->averageRating = 0;
        $this->ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

        if ($this->totalComments > 0) {
            $this->averageRating = round($comments->avg('rating'), 1);
            foreach ($comments as $c) {
                if ($c->rating >= 1 && $c->rating <= 5) {
                    $this->ratingCounts[round($c->rating)]++;
                }
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.comment-list');
    }
}
