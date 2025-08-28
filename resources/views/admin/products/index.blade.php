@extends('layouts.admin')

@section('title', 'Danh sách Sản phẩm')
@section('content_header_title', 'Quản lý Sản phẩm')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Danh sách Sản phẩm</h3>
        <div class="card-tools">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm mới
            </a>
        </div>
    </div>
    <div class="card-body">
        <table id="products-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th style="width: 70px">Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Trạng thái</th>
                    <th>Hiện trang chủ</th>
                    <th style="width: 120px">Hành động</th>
                </tr>
            </thead>
            <tbody>
                {{-- Dữ liệu sẽ được load bằng Ajax --}}
            </tbody>
            {{-- Thêm tfoot để chứa ô lọc --}}
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Trạng thái</th>
                    <th>Hiện trang chủ</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush

@push('js')
    <script src="{{ asset('vendor/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

    <script>
        $(function () {
            // --- KHỞI TẠO DATATABLE ---
            var table = $('#products-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.products.data") }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'image', name: 'image', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'category', name: 'category.name' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'is_home', name: 'is_home', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                language: { url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json" },
                // Thêm bộ lọc vào footer
                initComplete: function () {
                    this.api().columns([2, 3]).every(function () {
                        var column = this;
                        var input = $('<input type="text" class="form-control form-control-sm" placeholder="Lọc...">')
                            .appendTo($(column.footer()).empty())
                            .on('keyup change clear', function () {
                                if (column.search() !== this.value) {
                                    column.search(this.value).draw();
                                }
                            });
                    });
                }
            });

            // --- XỬ LÝ BOOLEAN TOGGLE ---
            // Dùng event delegation cho các nút được tạo bởi AJAX
            $('#products-table tbody').on('click', '.boolean-toggle', async function () {
                const span = $(this);
                const payload = {
                    _token: '{{ csrf_token() }}',
                    model: span.data('model'),
                    id: span.data('id'),
                    field: span.data('field'),
                };

                try {
                    const response = await fetch('{{ route("admin.toggle") }}', {
                        method: 'GET',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const json = await response.json();

                    if (json.success) {
                        const newValue = json.value;
                        span.removeClass('badge-success badge-danger')
                            .addClass(newValue ? 'badge-success' : 'badge-danger')
                            .text(newValue ? 'Hiện' : 'Ẩn');
                        toastr.success('Cập nhật trạng thái thành công!');
                    } else {
                        toastr.error(json.error || 'Đã xảy ra lỗi.');
                    }
                } catch (error) {
                    toastr.error('Không kết nối được đến máy chủ.');
                }
            });

            // --- XỬ LÝ NÚT XÓA VỚI SWEETALERT ---
            // Dùng event delegation
            $('#products-table tbody').on('click', '.btn-delete', function () {
                var productId = $(this).data('id');
                var deleteUrl = `/admin/products/${productId}`; // Tạo URL xóa

                Swal.fire({
                    title: 'Bạn có chắc chắn?',
                    text: "Bạn sẽ không thể hoàn tác hành động này!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Vâng, xóa nó!',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'POST', // Dùng POST và giả mạo DELETE
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Đã xóa!', response.message, 'success');
                                    table.ajax.reload(); // Tải lại bảng sau khi xóa
                                } else {
                                    Swal.fire('Lỗi!', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Lỗi!', 'Không thể kết nối đến máy chủ.', 'error');
                            }
                        });
                    }
                })
            });
        });
    </script>
@endpush