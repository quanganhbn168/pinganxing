@props(['content' => null])

@php
    $headings = [];
    
    if (!empty($content)) {
        // Regex tương tự Helper: bắt h2, h3, h4
        $pattern = '/<h([2-4]).*?>(.*?)<\/h\1>/usi';
        
        if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $level = $match[1]; // Lấy cấp độ (2, 3, 4) để indent lùi dòng nếu cần
                $text = $match[2];  // Nội dung
                
                // Xử lý text
                $cleanText = strip_tags(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
                $slug = \Illuminate\Support\Str::slug($cleanText);
                
                $headings[] = [
                    'level' => $level,
                    'text' => $cleanText,
                    'slug' => $slug
                ];
            }
        }
    }
@endphp

{{-- DEBUG: Nếu không thấy TOC, hãy bỏ comment dòng dưới để xem $headings có dữ liệu không --}}
{{-- @dump($headings) --}}

@if (!empty($headings))
    
    {{-- A. DESKTOP --}}
    <nav class="table-of-contents d-none d-lg-block">
        <h3 class="toc-title">☰ Mục lục</h3>
        <ol>
            @foreach ($headings as $heading)
                {{-- Thêm class để th thụt đầu dòng nếu là h3, h4 --}}
                <li class="toc-level-{{ $heading['level'] }}" style="margin-left: {{ ($heading['level'] - 2) * 15 }}px">
                    <a href="#{{ $heading['slug'] }}">{{ $heading['text'] }}</a>
                </li>
            @endforeach
        </ol>
    </nav>

    {{-- B. MOBILE --}}
    <div class="toc-mobile-wrapper d-lg-none">
        <button type="button" class="toc-fab" id="toc-fab-button">
            <i class="fa-solid fa-list-ul"></i>
        </button>

        <div class="toc-modal-overlay" id="toc-modal-overlay">
            <div class="toc-modal-content">
                <div class="toc-header">
                    <h3 class="toc-title">Mục lục</h3>
                    <button type="button" class="toc-close-button" id="toc-close-button">&times;</button>
                </div>
                <div class="toc-list">
                    <ol>
                        @foreach ($headings as $heading)
                            <li style="margin-left: {{ ($heading['level'] - 2) * 15 }}px">
                                <a class="toc-link" href="#{{ $heading['slug'] }}">{{ $heading['text'] }}</a>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @push('css')
    <style>
        /* CSS giữ nguyên như cũ */
        .table-of-contents { border: 1px solid #e0e0e0; padding: 15px; background: #f9f9f9; border-radius: 5px; margin-bottom: 25px; }
        .table-of-contents .toc-title { font-weight: bold; margin: 0 0 10px 0; font-size: 1.1em; text-transform: uppercase; }
        .table-of-contents ol { padding-left: 20px; margin: 0; }
        .table-of-contents ol li { margin-bottom: 8px; }
        .table-of-contents ol li a { text-decoration: none; color: #333; font-size: 14px; }
        .table-of-contents ol li a:hover { color: #007bff; text-decoration: underline; }
        
        /* Mobile styles */
        @media (max-width: 991px) {
            .toc-fab { position: fixed; bottom: 80px; left: 20px; width: 45px; height: 45px; background-color: #007bff; color: white; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; font-size: 18px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); z-index: 9999; cursor: pointer; transition: transform 0.2s; }
            .toc-fab:active { transform: scale(0.9); }
            .toc-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 10000; display: none; align-items: center; justify-content: center; }
            .toc-modal-overlay.is-visible { display: flex; }
            .toc-modal-content { background: white; width: 85%; max-width: 400px; max-height: 70vh; border-radius: 10px; display: flex; flex-direction: column; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
            .toc-header { padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
            .toc-list { padding: 15px; overflow-y: auto; }
            .toc-close-button { background: none; border: none; font-size: 24px; cursor: pointer; padding: 0 10px; }
        }
    </style>
    @endpush

    @push('js')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const fab = document.getElementById('toc-fab-button');
        const overlay = document.getElementById('toc-modal-overlay');
        const closeBtn = document.getElementById('toc-close-button');
        const links = document.querySelectorAll('.toc-link');

        if(fab && overlay && closeBtn) {
            const toggleModal = (show) => {
                if(show) {
                    overlay.classList.add('is-visible');
                    document.body.style.overflow = 'hidden';
                } else {
                    overlay.classList.remove('is-visible');
                    document.body.style.overflow = '';
                }
            };
            fab.addEventListener('click', () => toggleModal(true));
            closeBtn.addEventListener('click', () => toggleModal(false));
            overlay.addEventListener('click', (e) => {
                if(e.target === overlay) toggleModal(false);
            });
            links.forEach(link => {
                link.addEventListener('click', () => toggleModal(false));
            });
        }
    });
    </script>
    @endpush
@else
    {{-- Fallback: Nếu đang dev thì hiện thông báo này để biết là content không có thẻ h2-h4 --}}
    {{-- <div class="alert alert-warning">Không tìm thấy thẻ Heading nào trong bài viết.</div> --}}
@endif