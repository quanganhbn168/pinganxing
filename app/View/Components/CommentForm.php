<?php

namespace App\View\Components;

use App\Models\SampleReview;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CommentForm extends Component
{
    public $commentable;
    public $type;
    public $groupedSampleReviews;

    /**
     * Create a new component instance.
     */
    public function __construct($commentable, $type = 'post')
    {
        $this->commentable = $commentable;
        $this->type = $type;

        // Lấy tất cả mẫu đánh giá đang kích hoạt, sắp xếp theo sort_order
        $samples = SampleReview::where('is_active', true)
                               ->orderBy('sort_order', 'asc')
                               ->get();

        // Nhóm theo rating (từ 1 đến 5)
        $grouped = [];
        foreach ($samples as $sample) {
            $grouped[$sample->rating][] = $sample->content;
        }

        // Nếu DB rỗng, fallback về mảng mặc định để UI không bị trống
        if (empty($grouped)) {
            $grouped = [
                '5' => [
                    'Tuyệt vời, sản phẩm/dịch vụ đúng như mô tả!',
                    'Chất lượng gia công sắc nét, độ hoàn thiện cao.',
                    'Nhân viên tư vấn siêu nhiệt tình và chu đáo.',
                    'Rất hài lòng, sẽ còn tiếp tục ủng hộ!'
                ],
                '4' => [
                    'Sản phẩm tốt, giao hàng khá nhanh.',
                    'Chất lượng ổn trong tầm giá.',
                ],
                '3' => [
                    'Chất lượng bình thường, tạm ổn.',
                ],
                '2' => [
                    'Sản phẩm chưa được như kỳ vọng.',
                ],
                '1' => [
                    'Rất tệ, hoàn toàn thất vọng.',
                ]
            ];
        }

        $this->groupedSampleReviews = $grouped;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.comment-form');
    }
}
