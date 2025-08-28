/* global $, document, window */
(function ($) {
  'use strict';

  var ProductValidator = {
    form: null,
    submitButton: null,
    productId: null,

    init: function (formSelector) {
      this.form = $(formSelector);
      if (this.form.length === 0) return;

      this.submitButton = this.form.find('button[type="submit"]');
      this.productId = this.form.data('product-id') || null;

      this.form.attr('novalidate', true);
      this.form.on('submit', this.handleSubmit.bind(this));
    },

    moneyToNumber: function(value) {
        if (typeof value !== 'string' || value.trim() === '') {
            return NaN;
        }
        return Number(value.replace(/[^\d,-]/g, '').replace(',', '.'));
    },

    handleSubmit: function (e) {
      e.preventDefault();
      
      // Vô hiệu hóa nút bấm ngay khi bắt đầu xử lý
      this.submitButton.prop('disabled', true);
      var originalButtonText = this.submitButton.filter('[name="action"][value="save"]').html() || 'Lưu';

      var syncErrors = this.validateSync();

      if (syncErrors.length > 0) {
        this.showAllErrors(syncErrors);
        var $firstErrorElement = syncErrors[0].element;
        var $scrollToElement = $firstErrorElement;

        if ($firstErrorElement.hasClass('select2-hidden-accessible')) {
            $scrollToElement = $firstErrorElement.next('.select2-container');
        }

        if ($scrollToElement && $scrollToElement.length > 0 && $scrollToElement.offset()) {
            $('html, body').animate({
                scrollTop: $scrollToElement.offset().top - 100
            }, 500);
        }
        
        // Kích hoạt lại nút nếu có lỗi đồng bộ
        this.submitButton.prop('disabled', false);
        return;
      }

      this.validateAsync(originalButtonText); // Truyền originalButtonText vào đây
    },

    validateSync: function () {
      this.clearAllErrors();
      var errors = [];
      var self = this;

      function checkRequired(selector, message) {
        var el = self.form.find(selector);
        if (!el.val() || el.val().trim() === '' || el.val() === '0') {
          errors.push({ element: el, message: message });
          return false;
        }
        return true;
      }

      function checkNumeric(selector, message) {
        var el = self.form.find(selector);
        var val = el.val();
        if (val && isNaN(self.moneyToNumber(val))) {
          errors.push({ element: el, message: message });
          return false;
        }
        return true;
      }
      
      checkRequired('#name', 'Tên sản phẩm là bắt buộc.');
      checkRequired('#code', 'Mã sản phẩm là bắt buộc.');
      checkRequired('#category_id', 'Vui lòng chọn danh mục sản phẩm.');
      
      if (!this.productId) {
         var imageInput = this.form.find('input[name="image"]');
         if (imageInput.length && !imageInput.val() && !this.form.find('.hidden-image-data[name="image"]').val()) {
           errors.push({ element: imageInput, message: 'Ảnh đại diện là bắt buộc.' });
         }
      }

      var hasVariants = $('#has_variants').is(':checked');
      if (hasVariants) {
        var selectedAttrs = $('#attribute-select2').val() || [];
        if (selectedAttrs.length === 0) {
            errors.push({ element: $('#attribute-select2'), message: 'Bạn phải chọn ít nhất một thuộc tính.' });
        } else if (selectedAttrs.length > 3) {
             errors.push({ element: $('#attribute-select2'), message: 'Bạn chỉ được chọn tối đa 3 thuộc tính.' });
        }

        if ($('#variants_tbody tr').length === 0 && selectedAttrs.length > 0) {
          errors.push({ element: $('#attribute-select2'), message: 'Sản phẩm có biến thể phải có ít nhất một phiên bản được tạo.' });
        } else {
          $('#variants_tbody tr').each(function () {
            var $row = $(this);
            var $priceInput = $row.find('.v-price');
            var $comparePriceInput = $row.find('.v-compare');
            var $stockInput = $row.find('.v-stock');

            var priceVal = self.moneyToNumber($priceInput.val());
            var comparePriceVal = self.moneyToNumber($comparePriceInput.val());
            var stockVal = self.moneyToNumber($stockInput.val());

            if (isNaN(priceVal)) {
              errors.push({ element: $priceInput, message: 'Giá bán là bắt buộc và phải là số.' });
            } else if (priceVal < 0) {
              errors.push({ element: $priceInput, message: 'Giá bán không được âm.' });
            }

            if (!isNaN(comparePriceVal)) {
                 if (comparePriceVal < 0) {
                    errors.push({ element: $comparePriceInput, message: 'Giá so sánh không được âm.' });
                 }
                 else if (comparePriceVal < priceVal) {
                    errors.push({ element: $comparePriceInput, message: 'Giá so sánh phải lớn hơn hoặc bằng giá bán.' });
                 }
            }

            if (!isNaN(stockVal)) {
                if (stockVal < 0) {
                    errors.push({ element: $stockInput, message: 'Tồn kho không được âm.' });
                } else if (stockVal % 1 !== 0) {
                    errors.push({ element: $stockInput, message: 'Tồn kho phải là số nguyên.' });
                }
            }
          });
        }
      } else {
        var priceEl = self.form.find('#price');
        var priceDiscountEl = self.form.find('#price_discount');

          checkNumeric('#price_discount', 'Giá bán phải là số.');

        if (priceEl.val() && priceEl.val().trim() !== '') {
          if (checkNumeric('#price', 'Giá so sánh phải là số.')) {
            var priceVal = self.moneyToNumber(priceEl.val());
            var priceDiscountVal = self.moneyToNumber(priceDiscountEl.val());
            
            if (!isNaN(priceVal) && !isNaN(priceDiscountVal)) {
              if (priceVal < priceDiscountVal) {
                errors.push({ element: priceEl, message: 'Giá so sánh phải lớn hơn hoặc bằng giá bán.' });
              }
            }
          }
        }
      }

      return errors;
    },

    validateAsync: function (originalButtonText) {
        var self = this;
        // Nút đã bị vô hiệu hóa ở handleSubmit, giờ chỉ cần thêm spinner
        this.submitButton.html('<i class="fa fa-spinner fa-spin"></i> Đang kiểm tra...');

        var dataToCheck = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            productId: this.productId,
            code: $('#code').val()
        };

        var skus = [];
        if ($('#has_variants').is(':checked')) {
            $('.v-sku').each(function() {
                if ($(this).val()) {
                    skus.push({
                        id: $(this).closest('tr').find('.v-id').val(),
                        sku: $(this).val()
                    });
                }
            });
        }
        dataToCheck.skus = skus;

        $.ajax({
            url: this.form.data('validate-url'),
            type: 'POST',
            data: dataToCheck,
            dataType: 'json'
        })
        .done(function(response) {
            if (response.success) {
                // ĐÁNH DẤU FORM SẮP SUBMIT
                self.form.data('is-submitting', true);
                self.form.off('submit');
                self.form.get(0).submit();
            } else {
                var serverErrors = [];
                if (response.errors.code) {
                    serverErrors.push({ element: $('#code'), message: response.errors.code[0] });
                }
                if (response.errors.skus) {
                    for (const [sku, message] of Object.entries(response.errors.skus)) {
                        $('.v-sku').each(function() {
                            if ($(this).val() === sku) {
                                serverErrors.push({ element: $(this), message: message });
                            }
                        });
                    }
                }
                self.showAllErrors(serverErrors);
            }
        })
        .fail(function() {
            alert('Đã có lỗi xảy ra khi kiểm tra dữ liệu. Vui lòng thử lại.');
        })
        .always(function() {
            // [SỬA LẠI] - Luôn reset nút bấm NẾU form không được submit đi
            if (!self.form.data('is-submitting')) {
                 self.submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    },

    showError: function (element, message) {
        var $el = $(element);
        $el.addClass('is-invalid');

        if ($el.hasClass('select2-hidden-accessible')) {
            $el.next('.select2-container').addClass('is-invalid');
        }

        var errorEl = $el.siblings('.invalid-feedback');
        if (errorEl.length === 0) {
            errorEl = $('<div class="invalid-feedback"></div>');
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.next('.select2-container').after(errorEl);
            } else {
                $el.after(errorEl);
            }
        }
        errorEl.text(message).show();
    },

    showAllErrors: function(errors) {
        var self = this;
        errors.forEach(function(err) {
            self.showError(err.element, err.message);
        });
    },

    clearAllErrors: function () {
        this.form.find('.is-invalid').removeClass('is-invalid');
        this.form.find('.invalid-feedback').hide().text('');
    }
  };

  $(document).ready(function () {
    ProductValidator.init('#product_form');
  });

})(jQuery);