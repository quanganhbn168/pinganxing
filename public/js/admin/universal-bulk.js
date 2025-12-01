const UniversalBulk = {
    init() {
        this.cacheDom();
        this.bindEvents();
    },

    cacheDom() {
        this.$checkAll = $('#checkAll');
        this.$container = $('#bulkActionContainer');
        this.$countSpan = $('#bulkCount');
        this.$form = $('#universalBulkForm');
        this.$actionInput = $('#universalActionInput');
        this.$idsDiv = $('#universalIdsInput');
    },

    bindEvents() {
        const self = this;

        $(document).on('change', '#checkAll', function(e) {
            $('.check-item').prop('checked', e.target.checked);
            self.updateUI();
        });

        $(document).on('change', '.check-item', function() {
            const allChecked = $('.check-item').length > 0 && $('.check-item').length === $('.check-item:checked').length;
            $('#checkAll').prop('checked', allChecked);
            self.updateUI();
        });
    },

    updateUI() {
        const count = $('.check-item:checked').length;
        this.$countSpan.text(count);
        
        if (count > 0) {
            this.$container.removeClass('d-none').addClass('d-inline-block');
        } else {
            this.$container.removeClass('d-inline-block').addClass('d-none');
        }
    },

    confirm(actionType) {
        const count = $('.check-item:checked').length;
        if (count === 0) return;

        let title = 'Xác nhận thao tác?';
        let text = `Áp dụng cho ${count} mục đã chọn.`;
        let btnText = 'Đồng ý';
        let btnColor = '#3085d6';

        if (actionType === 'delete') {
            title = 'Xóa vĩnh viễn?';
            text = `Bạn sắp xóa ${count} mục. Hành động này không thể hoàn tác!`;
            btnText = 'Xóa ngay';
            btnColor = '#d33';
        }

        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: btnColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: btnText,
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit(actionType);
            }
        });
    },

    // [BỔ SUNG] Hàm này cần thiết cho nút xóa lẻ trên từng dòng
    confirmOne(id, actionType) {
        let title = 'Xác nhận?';
        let btnColor = '#3085d6';

        if(actionType === 'delete') {
            title = 'Xóa mục này?';
            btnColor = '#d33';
        }

        Swal.fire({
            title: title,
            text: "Hành động không thể hoàn tác!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: btnColor,
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                // Reset form
                this.$idsDiv.empty();
                this.$actionInput.val(actionType);
                // Append 1 ID
                this.$idsDiv.append(`<input type="hidden" name="ids[]" value="${id}">`);
                // Submit luôn
                this.$form.submit();
            }
        });
    },

    submit(actionType) {
        // --- [FIX LỖI Ở ĐÂY] ---
        const self = this; // Lưu lại 'this' (UniversalBulk) vào biến 'self'

        this.$actionInput.val(actionType);
        this.$idsDiv.empty();

        $('.check-item:checked').each(function() {
            // Trong này 'this' là cái thẻ <input>
            // Nên phải dùng 'self' để gọi đến $idsDiv
            self.$idsDiv.append(`<input type="hidden" name="ids[]" value="${$(this).val()}">`);
        });

        this.$form.submit();
    }
};

$(document).ready(function() {
    // Chạy init nếu có checkbox trên trang
    if($('#checkAll').length > 0 || $('.check-item').length > 0) {
        UniversalBulk.init();
    }
});