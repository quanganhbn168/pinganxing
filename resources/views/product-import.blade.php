<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Import sản phẩm</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 text-slate-900">
<div class="mx-auto max-w-[1600px] p-6 space-y-6">

    <div class="rounded-2xl bg-white p-6 shadow-sm border">
        <h1 class="text-2xl font-bold">Import sản phẩm</h1>
        <p class="mt-1 text-sm text-slate-500">
            Upload file ZIP gồm <b>products.json</b> và thư mục <b>images</b>.
            Hệ thống sẽ preview trước ảnh, giá, bảo hành, thông số kỹ thuật rồi mới import vào database.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-2xl bg-white p-5 shadow-sm border">
            <label class="text-sm font-semibold">Brand</label>

            <select id="brand" class="mt-2 w-full rounded-xl border px-3 py-2 bg-white">
                <option value="atp">ATP</option>
                <option value="hikvision">Hikvision</option>
                <option value="dahua">Dahua</option>
                <option value="ruijie-reyee">Ruijie Reyee</option>
                <option value="other">Khác</option>
            </select>

            <p class="mt-2 text-xs text-slate-500">
                Brand sẽ dùng làm thư mục ảnh:
                <code class="rounded bg-slate-100 px-1">public/images/products/{brand}</code>
            </p>
        </div>

        <div class="md:col-span-2 rounded-2xl bg-white p-5 shadow-sm border">
            <label class="text-sm font-semibold">File ZIP</label>

            <input
                id="file"
                type="file"
                accept=".zip"
                class="mt-2 w-full rounded-xl border px-3 py-2 bg-white"
            >

            <p id="fileName" class="mt-2 text-sm text-slate-500"></p>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-5 shadow-sm border space-y-4">
        <div class="flex flex-wrap items-center gap-3">
            <button
                id="previewBtn"
                class="rounded-xl bg-slate-900 px-5 py-2.5 text-white font-medium hover:bg-slate-700 disabled:opacity-50"
            >
                Đọc dữ liệu
            </button>

            <button
                id="importBtn"
                class="hidden rounded-xl bg-blue-600 px-5 py-2.5 text-white font-medium hover:bg-blue-500 disabled:opacity-50"
            >
                Import vào database
            </button>

            <button
                id="resetBtn"
                class="rounded-xl border px-5 py-2.5 font-medium hover:bg-slate-50"
            >
                Reset
            </button>

            <span id="status" class="text-sm text-slate-500"></span>
        </div>

        <div class="flex flex-wrap gap-5">
            <label class="inline-flex items-center gap-2 text-sm">
                <input id="onlyHasImage" type="checkbox" class="rounded border-slate-300">
                Chỉ import sản phẩm có ảnh
            </label>

            <label class="inline-flex items-center gap-2 text-sm">
                <input id="onlyHasSpecs" type="checkbox" class="rounded border-slate-300">
                Chỉ import sản phẩm có thông số
            </label>
        </div>
    </div>

    <div id="summary" class="hidden grid grid-cols-1 md:grid-cols-4 xl:grid-cols-7 gap-4"></div>

    <div id="toolsPanel" class="hidden rounded-2xl bg-white p-5 shadow-sm border">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="text-sm font-semibold">Tìm kiếm</label>
                <input
                    id="searchInput"
                    type="text"
                    placeholder="Mã, tên, thông số..."
                    class="mt-2 w-full rounded-xl border px-3 py-2"
                >
            </div>

            <div>
                <label class="text-sm font-semibold">Lọc ảnh</label>
                <select id="imageFilter" class="mt-2 w-full rounded-xl border px-3 py-2 bg-white">
                    <option value="all">Tất cả</option>
                    <option value="has_image">Có ảnh</option>
                    <option value="no_image">Thiếu ảnh</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-semibold">Lọc thông số</label>
                <select id="specFilter" class="mt-2 w-full rounded-xl border px-3 py-2 bg-white">
                    <option value="all">Tất cả</option>
                    <option value="has_specs">Có thông số</option>
                    <option value="no_specs">Thiếu thông số</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-semibold">Hiển thị</label>
                <select id="limitSelect" class="mt-2 w-full rounded-xl border px-3 py-2 bg-white">
                    <option value="50">50 sản phẩm</option>
                    <option value="100" selected>100 sản phẩm</option>
                    <option value="200">200 sản phẩm</option>
                    <option value="999999">Tất cả trong preview</option>
                </select>
            </div>
        </div>
    </div>

    <div id="previewPanel" class="hidden rounded-2xl bg-white shadow-sm border overflow-hidden">
        <div class="border-b p-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold">Preview sản phẩm</h2>
                <p class="text-sm text-slate-500">
                    Kiểm tra ảnh, mã, tên, giá, bảo hành và thông số kỹ thuật trước khi import.
                </p>
            </div>

            <div id="visibleCount" class="text-sm text-slate-500"></div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="p-3 text-left">Ảnh</th>
                    <th class="p-3 text-left">Mã</th>
                    <th class="p-3 text-left">Tên sản phẩm</th>
                    <th class="p-3 text-left">Danh mục</th>
                    <th class="p-3 text-left">Sheet</th>
                    <th class="p-3 text-right">Giá</th>
                    <th class="p-3 text-left">BH</th>
                    <th class="p-3 text-left">Tình trạng</th>
                    <th class="p-3 text-left min-w-[420px]">Thông số kỹ thuật</th>
                    <th class="p-3 text-left">Trạng thái</th>
                </tr>
                </thead>

                <tbody id="productsBody"></tbody>
            </table>
        </div>
    </div>

</div>

<script>
    let sessionId = null;
    let allProducts = [];
    let importPollTimer = null;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const fileInput = document.getElementById('file');
    const fileName = document.getElementById('fileName');
    const brandInput = document.getElementById('brand');

    const previewBtn = document.getElementById('previewBtn');
    const importBtn = document.getElementById('importBtn');
    const resetBtn = document.getElementById('resetBtn');

    const statusEl = document.getElementById('status');
    const summaryEl = document.getElementById('summary');
    const toolsPanel = document.getElementById('toolsPanel');
    const previewPanel = document.getElementById('previewPanel');
    const productsBody = document.getElementById('productsBody');
    const visibleCount = document.getElementById('visibleCount');

    const onlyHasImage = document.getElementById('onlyHasImage');
    const onlyHasSpecs = document.getElementById('onlyHasSpecs');

    const searchInput = document.getElementById('searchInput');
    const imageFilter = document.getElementById('imageFilter');
    const specFilter = document.getElementById('specFilter');
    const limitSelect = document.getElementById('limitSelect');

    fileInput.addEventListener('change', function () {
        fileName.innerText = this.files[0] ? 'Đã chọn: ' + this.files[0].name : '';
    });

    previewBtn.addEventListener('click', async function () {
        const file = fileInput.files[0];

        if (! file) {
            alert('Anh chọn file ZIP trước nhé.');
            return;
        }

        const formData = new FormData();
        formData.append('brand', brandInput.value);
        formData.append('file', file);

        setStatus('Đang đọc file ZIP...');
        previewBtn.disabled = true;
        importBtn.classList.add('hidden');

        try {
            const res = await fetch('{{ route('product-import.preview') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await res.json();

            if (! res.ok) {
                throw new Error(data.message || 'Lỗi đọc dữ liệu');
            }

            sessionId = data.session_id;
            allProducts = data.products || [];

            renderSummary(data.summary);
            renderProducts();

            toolsPanel.classList.remove('hidden');
            previewPanel.classList.remove('hidden');
            importBtn.classList.remove('hidden');

            setStatus('Đọc dữ liệu xong. Anh kiểm tra preview rồi bấm import.');

        } catch (error) {
            alert(error.message);
            setStatus('Có lỗi khi đọc dữ liệu.');
        } finally {
            previewBtn.disabled = false;
        }
    });

    importBtn.addEventListener('click', async function () {
        if (! sessionId) {
            alert('Chưa có phiên preview.');
            return;
        }

        const ok = confirm('Import dữ liệu này vào database nhé anh?');

        if (! ok) {
            return;
        }

        setStatus('Đang import vào database...');
        importBtn.disabled = true;

        try {
            const res = await fetch('{{ route('product-import.confirm') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    brand: brandInput.value,
                    only_has_image: onlyHasImage.checked,
                    only_has_specs: onlyHasSpecs.checked,
                }),
            });

            const data = await res.json();

            if (! res.ok) {
                throw new Error(data.message || 'Lỗi import');
            }

            if (! data.queued) {
                throw new Error(data.message || 'Không đưa được import vào hàng đợi');
            }

            setStatus(data.message || 'Đã đưa import vào hàng đợi.');
            pollImportStatus(data.status_url);

        } catch (error) {
            alert(error.message);
            setStatus('Có lỗi khi import.');
            importBtn.disabled = false;
        }
    });

    async function pollImportStatus(statusUrl) {
        if (importPollTimer) {
            clearTimeout(importPollTimer);
        }

        try {
            const res = await fetch(statusUrl, {
                headers: {
                    'Accept': 'application/json',
                },
            });

            const data = await res.json();

            if (! res.ok) {
                throw new Error(data.message || 'Không đọc được trạng thái import');
            }

            const processed = Number(data.processed || 0);
            const total = Number(data.total || 0);
            const progress = total > 0 ? ` (${processed}/${total})` : '';

            setStatus(`${data.message || 'Đang import...'}${progress}`);

            if (data.state === 'finished') {
                const result = data.result || {};
                const message = `Import xong: ${result.imported || 0} sản phẩm. Tạo mới: ${result.created || 0}. Cập nhật: ${result.updated || 0}. Bỏ qua: ${result.skipped || 0}. Lỗi: ${result.errors || 0}. Media mới: ${result.created_media || 0}. Ảnh thiếu file: ${result.missing_images || 0}.`;
                setStatus(message);
                alert(message);
                importBtn.disabled = false;
                return;
            }

            if (data.state === 'failed') {
                const message = data.message || 'Import thất bại.';
                setStatus(message);
                alert(message);
                importBtn.disabled = false;
                return;
            }

            importPollTimer = setTimeout(() => pollImportStatus(statusUrl), 2500);
        } catch (error) {
            setStatus('Đang chờ worker xử lý hoặc chưa đọc được trạng thái. Thử lại sau 5 giây...');
            importPollTimer = setTimeout(() => pollImportStatus(statusUrl), 5000);
        }
    }

    resetBtn.addEventListener('click', function () {
        if (importPollTimer) {
            clearTimeout(importPollTimer);
            importPollTimer = null;
        }

        sessionId = null;
        allProducts = [];

        fileInput.value = '';
        fileName.innerText = '';
        statusEl.innerText = '';

        summaryEl.classList.add('hidden');
        toolsPanel.classList.add('hidden');
        previewPanel.classList.add('hidden');
        importBtn.classList.add('hidden');

        summaryEl.innerHTML = '';
        productsBody.innerHTML = '';
        visibleCount.innerText = '';

        searchInput.value = '';
        imageFilter.value = 'all';
        specFilter.value = 'all';
        limitSelect.value = '100';
    });

    searchInput.addEventListener('input', renderProducts);
    imageFilter.addEventListener('change', renderProducts);
    specFilter.addEventListener('change', renderProducts);
    limitSelect.addEventListener('change', renderProducts);

    function renderSummary(summary) {
        summaryEl.classList.remove('hidden');

        summaryEl.innerHTML = `
            ${statCard('Tổng sản phẩm', summary.total_products)}
            ${statCard('Có ảnh', summary.products_with_images)}
            ${statCard('Thiếu ảnh', summary.products_without_images)}
            ${statCard('Tổng ảnh', summary.total_images)}
            ${statCard('Có thông số', summary.products_with_specs)}
            ${statCard('Thiếu thông số', summary.products_without_specs)}
            ${statCard('Nguồn', summary.source_file || '-')}
        `;
    }

    function statCard(title, value) {
        return `
            <div class="rounded-2xl bg-white p-5 shadow-sm border">
                <div class="text-sm text-slate-500">${escapeHtml(title)}</div>
                <div class="mt-1 text-xl font-bold break-words">${escapeHtml(value || 0)}</div>
            </div>
        `;
    }

    function renderProducts() {
        let products = [...allProducts];

        const keyword = searchInput.value.trim().toLowerCase();
        const imgFilter = imageFilter.value;
        const spFilter = specFilter.value;
        const limit = Number(limitSelect.value || 100);

        if (keyword) {
            products = products.filter(product => {
                const text = [
                    product.code,
                    product.name,
                    product.category,
                    product.sheet,
                    product.warranty,
                    product.status,
                    product.specifications,
                ].join(' ').toLowerCase();

                return text.includes(keyword);
            });
        }

        if (imgFilter === 'has_image') {
            products = products.filter(product => product.images && product.images.length > 0);
        }

        if (imgFilter === 'no_image') {
            products = products.filter(product => !product.images || product.images.length === 0);
        }

        if (spFilter === 'has_specs') {
            products = products.filter(product => product.specifications);
        }

        if (spFilter === 'no_specs') {
            products = products.filter(product => !product.specifications);
        }

        const totalAfterFilter = products.length;
        products = products.slice(0, limit);

        visibleCount.innerText = `Đang hiển thị ${products.length}/${totalAfterFilter} sản phẩm đã lọc.`;

        productsBody.innerHTML = products.map(product => {
            const image = product.images && product.images.length ? product.images[0] : null;

            const imageHtml = image && image.preview_url
                ? `<img src="${escapeAttr(image.preview_url)}" class="h-16 w-16 rounded-xl border object-contain bg-white">`
                : `<div class="h-16 w-16 rounded-xl border bg-slate-50 flex items-center justify-center text-xs text-slate-400">No img</div>`;

            const price = product.retail_price || product.price;

            const imageBadge = image
                ? `<span class="rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700">Có ảnh</span>`
                : `<span class="rounded-full bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700">Thiếu ảnh</span>`;

            const specBadge = product.specifications
                ? `<span class="rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700">Có thông số</span>`
                : `<span class="rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700">Thiếu thông số</span>`;

            return `
                <tr class="border-t hover:bg-slate-50 align-top">
                    <td class="p-3">${imageHtml}</td>

                    <td class="p-3 font-semibold whitespace-nowrap">
                        ${escapeHtml(product.code || '-')}
                    </td>

                    <td class="p-3 min-w-[320px]">
                        <div class="font-medium">${escapeHtml(product.name || '-')}</div>
                    </td>

                    <td class="p-3 whitespace-nowrap">
                        ${escapeHtml(product.category || '-')}
                    </td>

                    <td class="p-3 whitespace-nowrap">
                        ${escapeHtml(product.sheet || '-')}
                    </td>

                    <td class="p-3 text-right whitespace-nowrap">
                        ${formatMoney(price)}
                    </td>

                    <td class="p-3 whitespace-nowrap">
                        ${escapeHtml(product.warranty || '-')}
                    </td>

                    <td class="p-3 whitespace-nowrap">
                        ${escapeHtml(product.status || '-')}
                    </td>

                    <td class="p-3 min-w-[420px]">
                        <div class="max-h-28 overflow-hidden whitespace-pre-line text-slate-700 leading-6">
                            ${escapeHtml(product.specifications || '-')}
                        </div>
                    </td>

                    <td class="p-3 space-y-1 whitespace-nowrap">
                        <div>${imageBadge}</div>
                        <div>${specBadge}</div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function formatMoney(value) {
        if (! value) {
            return '-';
        }

        const number = Number(value);

        if (Number.isNaN(number)) {
            return escapeHtml(String(value));
        }

        return new Intl.NumberFormat('vi-VN').format(number) + ' đ';
    }

    function setStatus(message) {
        statusEl.innerText = message;
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function escapeAttr(value) {
        return escapeHtml(value);
    }
</script>
</body>
</html>
