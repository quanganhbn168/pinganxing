

<div class="mb-10">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 uppercase">Đánh giá khách hàng</h2>
    
    @if($totalComments > 0)
    <div class="flex flex-col md:flex-row gap-8 items-center bg-gray-50 dark:bg-gray-800/80 p-6 rounded-xl border border-gray-100 dark:border-gray-700">
        {{-- Số sao trung bình --}}
        <div class="text-center md:w-1/3 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-200 dark:border-gray-700 pb-6 md:pb-0">
            <div class="text-6xl font-extrabold text-gray-900 dark:text-white mb-2">{{ $averageRating }}</div>
            <div class="flex text-yellow-400 text-xl mb-2">
                @for($i=1; $i<=5; $i++)
                    @if($i <= floor($averageRating))
                        <i class="fas fa-star"></i>
                    @elseif($i - 0.5 <= $averageRating)
                        <i class="fas fa-star-half-alt"></i>
                    @else
                        <i class="far fa-star"></i>
                    @endif
                @endfor
            </div>
            <div class="text-sm text-gray-500 font-medium">{{ $totalComments }} lượt đánh giá</div>
        </div>

        {{-- Thanh biểu đồ --}}
        <div class="w-full md:w-2/3 space-y-3">
            @for($i=5; $i>=1; $i--)
                @php 
                    $ptct = $totalComments > 0 ? round(($ratingCounts[$i] / $totalComments) * 100) : 0;
                @endphp
                <div class="flex items-center gap-3">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400 w-12">{{ $i }} sao</div>
                    <div class="flex-grow bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                        <div class="bg-yellow-400 h-2.5 rounded-full" style="width: {{ $ptct }}%"></div>
                    </div>
                    <div class="text-sm text-gray-500 w-10 text-right">{{ $ptct }}%</div>
                </div>
            @endfor
        </div>
    </div>
    @endif
</div>

<div class="space-y-6">
    @if(isset($comments) && $comments->count() > 0)
        @foreach($comments as $comment)
            <div class="border-b border-gray-100 dark:border-gray-700 pb-6 mb-6 last:border-0 last:pb-0 last:mb-0">
                <div class="flex items-center justify-between mb-2">
                    <h5 class="font-bold text-gray-900 dark:text-white flex items-center">
                        <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center mr-3 text-sm font-bold">
                            {{ mb_substr($comment->name, 0, 1) }}
                        </div>
                        {{ $comment->name }}
                    </h5>
                    <span class="text-sm text-gray-500"><i class="fas fa-clock mr-1"></i> {{ $comment->created_at->diffForHumans() }}</span>
                </div>
                
                @if($comment->rating)
                <div class="flex text-yellow-400 mb-3 text-sm ml-11">
                    @for($i=1; $i<=5; $i++)
                        <i class="fas fa-star {{ $i <= $comment->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                    @endfor
                </div>
                @endif
                
                <p class="text-gray-700 dark:text-gray-300 ml-11">{{ $comment->content }}</p>

                {{-- Hiển thị câu trả lời (Repy) --}}
                @if($comment->replies && $comment->replies->count() > 0)
                    <div class="mt-4 ml-11 pl-4 border-l-2 border-brand-200 dark:border-brand-700 space-y-4">
                        @foreach($comment->replies as $reply)
                            <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <h6 class="font-bold text-brand-600 dark:text-brand-400 text-sm flex items-center">
                                        <i class="fas fa-user-shield mr-2"></i> {{ $reply->name }} (Quản trị viên)
                                    </h6>
                                    <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $reply->content }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <div class="text-center py-10 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-dashed border-gray-200 dark:border-gray-700">
            <i class="fas fa-comments text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
            <p class="text-gray-500 dark:text-gray-400 font-medium">Chưa có bình luận nào. Hãy là người đầu tiên đánh giá!</p>
        </div>
    @endif
</div>
