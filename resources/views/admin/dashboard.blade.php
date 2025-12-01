@extends('layouts.admin')

@section('title', 'Dashboard')
@section('content_header', 'Tổng quan hệ thống')

@push('css')
<style>
    /* Hiệu ứng Skeleton Loading */
    .skeleton {
        background: #e0e0e0;
        border-radius: 4px;
        animation: shimmer 1.5s infinite linear;
        background: linear-gradient(to right, #eff1f3 4%, #e2e2e2 25%, #eff1f3 36%);
        background-size: 1000px 100%;
    }
    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }
    .sk-text { height: 20px; width: 60%; margin-bottom: 5px; }
    .sk-number { height: 30px; width: 40%; }
    .sk-chart { height: 300px; width: 100%; }
    .sk-table-row { height: 40px; width: 100%; margin-bottom: 10px; }
    
    /* Ẩn nội dung thật khi đang load */
    .loading .real-content { display: none; }
    .loaded .skeleton-content { display: none; }
</style>
@endpush

@section('content')
<div class="container-fluid loading" id="dashboardData">
    
    {{-- 1. BỐN CARD THỐNG KÊ --}}
    <div class="row">
        @foreach(['primary' => 'Sản phẩm', 'success' => 'Bài viết', 'warning' => 'Thành viên', 'danger' => 'Ứng tuyển'] as $color => $title)
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-{{ $color }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ $color }} text-uppercase mb-1">{{ $title }}</div>
                            {{-- Skeleton --}}
                            <div class="skeleton-content">
                                <div class="skeleton sk-number"></div>
                            </div>
                            {{-- Real Data --}}
                            <div class="real-content h5 mb-0 font-weight-bold text-gray-800" id="count-{{ Str::slug($title) }}">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-{{ $color == 'primary' ? 'box' : ($color == 'success' ? 'newspaper' : ($color == 'warning' ? 'users' : 'file-contract')) }} fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- 2. BIỂU ĐỒ (CHARTS) --}}
    <div class="row">
        {{-- Area Chart --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tổng quan nội dung</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 320px;">
                        <div class="skeleton-content h-100 w-100 skeleton"></div>
                        <canvas id="myAreaChart" class="real-content"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pie Chart --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tỷ lệ hồ sơ</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2" style="height: 280px;">
                        <div class="skeleton-content h-100 w-100 skeleton rounded-circle"></div>
                        <canvas id="myPieChart" class="real-content"></canvas>
                    </div>
                    <div class="mt-4 text-center small real-content">
                        <span class="mr-2"><i class="fas fa-circle text-primary"></i> Chờ duyệt</span>
                        <span class="mr-2"><i class="fas fa-circle text-success"></i> Đạt</span>
                        <span class="mr-2"><i class="fas fa-circle text-info"></i> Phỏng vấn</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. DANH SÁCH ỨNG TUYỂN MỚI --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Ứng tuyển mới nhất</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Ứng viên</th>
                            <th>Vị trí</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody id="recentAppliesTable">
                        {{-- Skeleton Rows --}}
                        @for($i=0; $i<5; $i++)
                        <tr class="skeleton-content">
                            <td><div class="skeleton sk-text"></div></td>
                            <td><div class="skeleton sk-text"></div></td>
                            <td><div class="skeleton sk-text" style="width: 80%"></div></td>
                            <td><div class="skeleton sk-text" style="width: 50%"></div></td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('js')
{{-- Import Chart.js (AdminLTE thường có sẵn, nếu chưa thì thêm CDN) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {
        // Gọi API lấy số liệu
        $.ajax({
            url: '{{ route("admin.dashboard.stats") }}', // Tạo route này trỏ về DashboardController@stats
            method: 'GET',
            success: function(res) {
                // Giả lập độ trễ 1 xíu cho người dùng thấy hiệu ứng skeleton "cho đã"
                setTimeout(() => {
                    renderData(res);
                    $('#dashboardData').removeClass('loading').addClass('loaded');
                }, 800);
            },
            error: function() {
                console.error('Lỗi tải dashboard');
            }
        });

        function renderData(data) {
            // 1. Fill Cards
            $('#count-san-pham').text(new Intl.NumberFormat().format(data.counts.products));
            $('#count-bai-viet').text(new Intl.NumberFormat().format(data.counts.posts));
            $('#count-thanh-vien').text(new Intl.NumberFormat().format(data.counts.users));
            $('#count-ung-tuyen').text(new Intl.NumberFormat().format(data.counts.applies));

            // 2. Fill Table
            let html = '';
            data.recent_applies.forEach(item => {
                let badge = item.status == 'pending' ? 'warning' : 'success';
                html += `
                    <tr>
                        <td class="font-weight-bold">${item.name}</td>
                        <td>${item.position}</td>
                        <td class="small text-muted">${item.date}</td>
                        <td><span class="badge badge-${badge}">${item.status}</span></td>
                    </tr>
                `;
            });
            $('#recentAppliesTable').append(html);

            // 3. Draw Charts
            initAreaChart(data.chart);
            initPieChart();
        }

        function initAreaChart(chartData) {
            var ctx = document.getElementById("myAreaChart");
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: "Sản phẩm",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: chartData.products,
                    }, {
                        label: "Bài viết",
                        lineTension: 0.3,
                        borderColor: "#1cc88a",
                        pointRadius: 3,
                        pointBackgroundColor: "#1cc88a",
                        data: chartData.posts,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
                    scales: {
                        xAxes: [{ gridLines: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 7 } }],
                        yAxes: [{ ticks: { maxTicksLimit: 5, padding: 10 }, gridLines: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] } }],
                    },
                    legend: { display: true },
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        titleMarginBottom: 10,
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                    }
                }
            });
        }

        function initPieChart() {
            var ctx = document.getElementById("myPieChart");
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ["Chờ duyệt", "Đạt", "Phỏng vấn"],
                    datasets: [{
                        data: [55, 30, 15], // Giả lập tỷ lệ
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                        hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: { display: false },
                    cutoutPercentage: 80,
                },
            });
        }
    });
</script>
@endpush