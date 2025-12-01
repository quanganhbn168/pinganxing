@props(['list' => []])

@if (!empty($list))
    {{-- 1. CSS Inline cho gọn (Bạn có thể đưa ra file css riêng) --}}
    @push('css')
    <style>
        /* Desktop */
        .toc-box { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
        .toc-box h4 { font-size: 16px; font-weight: 700; margin-bottom: 10px; text-transform: uppercase; }
        .toc-list { list-style: none; padding-left: 0; margin: 0; }
        .toc-list li a { display: block; padding: 5px 0; color: #333; text-decoration: none; font-size: 14px; transition: color 0.2s; }
        .toc-list li a:hover { color: #007bff; }
        /* Indent cho h3, h4 */
        .toc-indent-3 { padding-left: 15px; font-size: 13px; border-left: 2px solid #eee; }
        .toc-indent-4 { padding-left: 30px; font-size: 13px; border-left: 2px solid #eee; }

        /* Mobile Floating Button */
        .toc-fab-btn { position: fixed; bottom: 20px; left: 20px; width: 50px; height: 50px; background: #007bff; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.3); z-index: 9999; border: none; cursor: pointer; }
        
        /* Mobile Modal */
        .toc-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 10000; display: none; align-items: center; justify-content: center; }
        .toc-overlay.active { display: flex; }
        .toc-modal { background: #fff; width: 90%; max-width: 400px; max-height: 70vh; border-radius: 10px; display: flex; flex-direction: column; overflow: hidden; animation: slideUp 0.3s ease; }
        .toc-modal-head { padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #f1f1f1; }
        .toc-modal-body { padding: 15px; overflow-y: auto; }
        .toc-close { background: none; border: none; font-size: 24px; cursor: pointer; }
        
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>
    @endpush

    {{-- 2. GIAO DIỆN DESKTOP --}}
    <div class="d-none d-lg-block toc-box sticky-top" style="top: 100px; z-index: 1;">
        <h4><i class="fa-solid fa-list"></i> Mục lục</h4>
        <ul class="toc-list">
            @foreach($list as $item)
                <li class="toc-indent-{{ $item['level'] }}">
                    <a href="#{{ $item['slug'] }}">{{ $item['text'] }}</a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- 3. GIAO DIỆN MOBILE --}}
    <div class="d-block d-lg-none">
        <button class="toc-fab-btn" id="js-toc-btn">
            <i class="fa-solid fa-list-ul font-size-20"></i>
        </button>

        <div class="toc-overlay" id="js-toc-overlay">
            <div class="toc-modal">
                <div class="toc-modal-head">
                    <h3 class="m-0 font-weight-bold" style="font-size:18px">Mục lục</h3>
                    <button class="toc-close" id="js-toc-close">&times;</button>
                </div>
                <div class="toc-modal-body">
                    <ul class="toc-list">
                        @foreach($list as $item)
                            <li class="toc-indent-{{ $item['level'] }}">
                                <a href="#{{ $item['slug'] }}" class="js-toc-link">{{ $item['text'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. JAVASCRIPT --}}
    @push('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const btn = document.getElementById('js-toc-btn');
            const overlay = document.getElementById('js-toc-overlay');
            const close = document.getElementById('js-toc-close');
            const links = document.querySelectorAll('.js-toc-link');

            if(btn && overlay && close) {
                function toggleToc(show) {
                    overlay.classList.toggle('active', show);
                    document.body.style.overflow = show ? 'hidden' : '';
                }

                btn.addEventListener('click', () => toggleToc(true));
                close.addEventListener('click', () => toggleToc(false));
                overlay.addEventListener('click', (e) => {
                    if(e.target === overlay) toggleToc(false);
                });
                links.forEach(link => {
                    link.addEventListener('click', () => toggleToc(false));
                });
            }
        });
    </script>
    @endpush
@endif