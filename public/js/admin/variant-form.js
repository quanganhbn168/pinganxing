/* global $, document, window, Swal */
(function () {
  'use strict';

  var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
  });

  var MAX_ATTRS = 3;
  var DEFAULT_STOCK = 1;

  function moneyToNumber(v) {
    if (v == null) return '';
    var s = String(v).replace(/[^\d.,-]/g, '').replace(/\./g, '');
    var parts = s.split(',');
    if (parts.length > 1) return parts[0] + '.' + parts.slice(1).join('');
    return s;
  }

  function canonicalKey(pairs) {
    return pairs
      .slice()
      .sort(function (a, b) { return (a.attrId - b.attrId) || (a.valueId - b.valueId); })
      .map(function (p) { return p.attrId + ':' + p.valueId; })
      .join('|');
  }
  
  function cartesian(arrays) {
    if (!arrays.length) return [];
    return arrays.reduce(function (acc, cur) {
      var res = [];
      acc.forEach(function (a) { cur.forEach(function (c) { res.push(a.concat([c])); }); });
      return res;
    }, [[]]);
  }

  function uniqPush(arr, val) { if (arr.indexOf(val) === -1) arr.push(val); }

  $(function () {
    var $wrap = $('#variants_wrap');
    if ($wrap.length === 0) return;

    var token = $('meta[name="csrf-token"]').attr('content');
    if (token) $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': token } });

    var ATTR_MASTER = [];
    try {
      ATTR_MASTER = JSON.parse($wrap.attr('data-attributes') || '[]');
    } catch (e) { console.error("Lỗi parse data-attributes JSON:", e); }

    ATTR_MASTER.forEach(function (a) {
      a.id = Number(a.id);
      a.name = a.name || a.value || '';
      (a.values || []).forEach(function (v) {
        v.id = Number(v.id);
        v.name = v.name || v.value || '';
        if (typeof v.attribute_id !== 'undefined') v.attribute_id = Number(v.attribute_id);
      });
    });

    var attrIndex   = new Map(ATTR_MASTER.map(function (a) { return [a.id, a]; }));
    var valuesIndex = new Map(ATTR_MASTER.map(function (a) { return [a.id, (a.values || []).slice()]; }));
    var valueToAttr = new Map();
    ATTR_MASTER.forEach(function (a) {
      (a.values || []).forEach(function (v) {
        valueToAttr.set(v.id, { attrId: a.id, attrName: a.name, valueName: v.name });
      });
    });

    var $attrSelect = $('#attribute-select2');
    var $blocks     = $('#attr-value-blocks');
    var $tbody      = $('#variants_tbody');
    var $bulk       = $('#bulkbar');
    var $checkAll   = $('#check_all');

    var excluded        = new Set();
    var rowState        = new Map();
    var idByKey         = new Map();
    var initialHydrated = false;

    $('#has_variants').on('change', function () {
      $wrap.toggleClass('d-none', !this.checked);
    });

    $attrSelect.empty();
    ATTR_MASTER.forEach(function (a) {
      $attrSelect.append(new Option(a.name, a.id, false, false));
    });

    $attrSelect.select2({
      width: '100%',
      placeholder: $attrSelect.data('placeholder') || '--- Chọn tối đa 3 thuộc tính ---',
      maximumSelectionLength: MAX_ATTRS,
      closeOnSelect: false,
      language: { maximumSelected: function () { return 'Chỉ chọn tối đa ' + MAX_ATTRS + ' thuộc tính'; } }
    });

    $attrSelect.on('change', function() {
        snapshotRows();
        renderValueBlocks();
        regenerateTable();
    });
    
    function renderValueBlocks(preselected) {
        var selectedAttrIds = ($attrSelect.val() || []).map(Number);
        var renderedAttrIds = [];

        $blocks.find('.variants__block').each(function () {
            var $block = $(this);
            var blockAttrId = Number($block.data('attr-id'));
            if (selectedAttrIds.includes(blockAttrId)) {
                renderedAttrIds.push(blockAttrId);
            } else {
                $block.remove();
            }
        });

        selectedAttrIds.forEach(function (id) {
            if (renderedAttrIds.includes(id)) {
                return;
            }

            var attr = attrIndex.get(id) || { name: 'Thuộc tính' };
            var $block = $(
                '<div class="variants__block" data-attr-id="' + id + '">' +
                '<label class="variants__block-title">' + (attr.name || 'Thuộc tính') + '</label>' +
                '<select class="variants__value-select" multiple ' +
                'data-attr-id="' + id + '" data-placeholder="Chọn/nhập giá trị (Enter để thêm)">' +
                '</select>' +
                '<small class="text-muted">Không thấy giá trị? Gõ tên rồi Enter để thêm mới.</small>' +
                '</div>'
            );
            $blocks.append($block);
            var $sel = $block.find('.variants__value-select');

            (valuesIndex.get(id) || []).forEach(function (v) {
                $sel.append(new Option(v.name, v.id, false, false));
            });

            $sel.select2({
                width: '100%',
                multiple: true,
                tags: true,
                tokenSeparators: [','],
                placeholder: $sel.data('placeholder') || 'Chọn/nhập giá trị',
                createTag: function (params) {
                    var term = (params.term || '').trim();
                    if (!term) return null;
                    return { id: '__new__:' + term, text: term, isNew: true };
                },
                templateResult: function (d) {
                    return d.isNew ? $('<span>Thêm mới: "' + d.text + '"</span>') : d.text;
                }
            });

            $sel.on('select2:select', function (e) {
                var data = e.params && e.params.data;
                if (!data || !data.isNew) return;
                var name = data.text;
                var attrId = Number($sel.data('attr-id'));
                $.post('/admin/ajax/attributes/' + attrId + '/values', { name: name, value: name })
                    .done(function (res) {
                        var temp = data.id;
                        $sel.find('option[value="' + temp + '"]').remove();
                        var real = new Option(res.text, res.id, true, true);
                        $sel.append(real).trigger('change');
                        var arr = valuesIndex.get(attrId) || [];
                        if (!arr.find(function (v) { return Number(v.id) === Number(res.id); })) {
                            arr.push({ id: Number(res.id), name: res.text });
                            valuesIndex.set(attrId, arr);
                            valueToAttr.set(Number(res.id), {
                                attrId: attrId,
                                attrName: (attrIndex.get(attrId) || {}).name || '',
                                valueName: res.text
                            });
                        }
                    })
                    .fail(function (xhr) {
                        $sel.find('option[value="' + data.id + '"]').remove();
                        $sel.trigger('change');
                        var message = 'Không thể tạo giá trị mới. Vui lòng thử lại.';
                        if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.name) {
                            message = xhr.responseJSON.errors.name[0];
                        }
                        Toast.fire({ icon: 'error', title: message });
                    });
            });

            if (preselected && preselected[id] && preselected[id].length) {
                var vals = preselected[id].map(String);
                $sel.val(vals).trigger('change');
            } else {
                $sel.val([]).trigger('change');
            }

            $sel.on('change', function() {
              snapshotRows();
              regenerateTable();
            });
        });
    }function currentAttrValues() {
      var result = [];
      var attrIds = ($attrSelect.val() || []).map(Number);
      for (var i = 0; i < attrIds.length; i++) {
        var id = attrIds[i];
        var $sel = $blocks.find('.variants__value-select[data-attr-id="' + id + '"]');
        var valIds = ($sel.val() || []).map(Number);
        var attr = attrIndex.get(id);
        var all = new Map((valuesIndex.get(id) || []).map(function (v) {
          return [Number(v.id), v.name];
        }));
        if (valIds.length === 0) return [];
        var items = valIds.map(function (vId) {
          return {
            attrId: id,
            attrName: attr ? attr.name || '' : "",
            valueId: vId,
            valueName: all.get(vId) || ""
          };
        });
        result.push(items);
      }
      return result;
    }

    function snapshotRows() {
      var map = new Map();
      $tbody.find("tr[data-key]").each(function () {
        var $tr = $(this);
        var key = String($tr.data("key"));
        map.set(key, {
          id: $tr.find(".v-id").val() || "",
          sku: $tr.find(".v-sku").val() || "",
          price: $tr.find(".v-price").val() || "",
          compare: $tr.find(".v-compare").val() || "",
          stock: $tr.find(".v-stock").val() || "",
          isDefault: $tr.find(".v-default-radio").prop("checked") ? 1 : 0
        });
      });
      rowState = map;
    }

    function regenerateTable() {
      var layers = currentAttrValues();
      var combos = cartesian(layers);
      var rows = [];
      for (var i = 0; i < combos.length; i++) {
        var combo = combos[i];
        var key = canonicalKey(combo);
        if (excluded.has(key)) continue;
        var display = combo.map(function (p) { return p.valueName; }).join(" / ");
        var old = rowState.get(key) || {};
        var knownId = idByKey.get(key) || old.id || "";
        rows.push({
          key: key,
          id: knownId,
          display: display,
          sku: old.sku || "",
          price: old.price || "",
          compare: old.compare || "",
          stock: old.stock || String(DEFAULT_STOCK),
          isDefault: old.isDefault || 0
        });
      }
      if (!rows.some(function (r) { return r.isDefault === 1; }) && rows.length > 0) {
        rows[0].isDefault = 1;
      }
      $tbody.empty();
      rows.forEach(function (r) {
        var namePrefix = "variants[" + r.key + "]";
        var $tr = $(
          '<tr data-key="' + r.key + '">' +
          '<td><input type="checkbox" class="row-check"></td>' +
          '<td class="v-opt">' + r.display + '</td>' +
          '<td>' +
          '<input type="hidden" class="v-id" name="' + namePrefix + '[id]" value="' + (r.id || "") + '">' +
          '<input type="text" class="form-control v-sku" name="' + namePrefix + '[sku]" value="' + (r.sku || "") + '">' +
          '</td>' +
          '<td class="text-right">' +
          '<input type="text" class="form-control v-price" name="' + namePrefix + '[price]" value="' + (r.price || "") + '" required>' +
          '</td>' +
          '<td class="text-right">' +
          '<input type="text" class="form-control v-compare" name="' + namePrefix + '[compare_at_price]" value="' + (r.compare || "") + '">' +
          '</td>' +
          '<td class="text-right">' +
          '<input type="number" min="0" class="form-control v-stock" name="' + namePrefix + '[stock]" value="' + (r.stock || "") + '">' +
          '</td>' +
          '<td class="text-center">' +
          '<input type="radio" class="v-default-radio" name="variants_default_key" value="' + r.key + '" ' + (r.isDefault ? "checked" : "") + '>' +
          '<input type="hidden" class="v-is-default" name="' + namePrefix + '[is_default]" value="' + (r.isDefault ? 1 : 0) + '">' +
          '</td>' +
          '<td class="text-center">' +
          '<button type="button" class="btn btn-sm btn-danger v-delete">&times;</button>' +
          '</td>' +
          '</tr>'
        );
        $tbody.append($tr);
      });
      updateBulkBar();
    }

    $tbody.on('click', '.v-delete', function () {
      var key = String($(this).closest('tr').data('key'));
      excluded.add(key);
      regenerateTable();
    });

    $tbody.on('change', '.v-default-radio', function () {
      var key = String($(this).val());
      $tbody.find('.v-is-default').val('0');
      $tbody.find('tr[data-key="' + key + '"] .v-is-default').val('1');
    });
    $tbody.on('change', '.v-sku, .v-price, .v-compare, .v-stock', function () {
        var $input = $(this);
        var $tr = $input.closest('tr');
        var key = String($tr.data('key'));

        var state = rowState.get(key) || {
            id: $tr.find(".v-id").val() || "",
            isDefault: $tr.find(".v-default-radio").prop("checked") ? 1 : 0
        };

        state.sku = $tr.find('.v-sku').val() || "";
        state.price = $tr.find('.v-price').val() || "";
        state.compare = $tr.find('.v-compare').val() || "";
        state.stock = $tr.find('.v-stock').val() || "";
        
        rowState.set(key, state);
    });
    function updateBulkBar() {
      var any = $tbody.find('.row-check:checked').length > 0;
      $bulk.toggleClass('d-none', !any);
      if (!any) $checkAll.prop('checked', false);
    }

    $tbody.on('change', '.row-check', updateBulkBar);

    $checkAll.on('change', function () {
      $tbody.find('.row-check').prop('checked', this.checked);
      updateBulkBar();
    });

    $('#bulk_apply').on('click', function () {
      var $rows = $tbody.find('.row-check:checked').closest('tr');
      var p = moneyToNumber($('#bulk_price').val());
      var c = moneyToNumber($('#bulk_compare').val());
      var s = $('#bulk_stock').val();
      $rows.each(function () {
        if (p) $(this).find('.v-price').val(p);
        if (c) $(this).find('.v-compare').val(c);
        if (s !== '') $(this).find('.v-stock').val(s);
      });
    });

    $('#bulk_clear').on('click', function () {
      $tbody.find('.row-check').prop('checked', false);
      updateBulkBar();
    });

    var oldInputJson = $wrap.attr('data-old-input') || '';
    var existingJson = $wrap.attr('data-existing-variants') || '';
    var hydrationData = null;
    var isHydratingFromOldInput = false;

    if (oldInputJson) {
        try {
            hydrationData = JSON.parse(oldInputJson);
            isHydratingFromOldInput = true;
        } catch (e) { console.error("Lỗi parse old input JSON:", e); }
    } else if (existingJson) {
        try {
            hydrationData = JSON.parse(existingJson);
        } catch (e) { console.error("Lỗi parse existing variants JSON:", e); }
    }

    if (hydrationData && !initialHydrated) {
        var usedAttrIds = [];
        var preValsMap = {};

        if (isHydratingFromOldInput) {
            var oldVariants = hydrationData.variants || {};
            Object.keys(oldVariants).forEach(function (canonicalKey) {
                var pairs = canonicalKey.split('|');
                pairs.forEach(function (pair) {
                    var ids = pair.split(':');
                    var attrId = Number(ids[0]), valueId = Number(ids[1]);
                    if (attrId && valueId) {
                        uniqPush(usedAttrIds, attrId);
                        if (!preValsMap[attrId]) preValsMap[attrId] = [];
                        uniqPush(preValsMap[attrId], valueId);
                    }
                });
            });
            Object.entries(oldVariants).forEach(function([key, data]) {
                 rowState.set(key, {
                    id: data.id || '', sku: data.sku || '', price: data.price || '',
                    compare: data.compare_at_price || '', stock: data.stock || '',
                    isDefault: (hydrationData.variants_default_key === key) ? 1 : 0
                });
            });
        } else {
            hydrationData.forEach(function (variantData) {
                var valueIds = variantData.values || [];
                var pairsForCanonicalKey = [];

                valueIds.forEach(function(vId) {
                    var info = valueToAttr.get(vId);
                    if (info) {
                        uniqPush(usedAttrIds, info.attrId);
                        if (!preValsMap[info.attrId]) preValsMap[info.attrId] = [];
                        uniqPush(preValsMap[info.attrId], vId);
                        pairsForCanonicalKey.push({ attrId: info.attrId, valueId: vId });
                    }
                });
                
                var key = canonicalKey(pairsForCanonicalKey);
                if (key) {
                    idByKey.set(key, variantData.id || '');
                    rowState.set(key, {
                      id: variantData.id || '',
                      sku: variantData.sku || '',
                      price: (variantData.price != null) ? String(variantData.price) : '',
                      compare: (variantData.compare_at_price != null) ? String(variantData.compare_at_price) : '',
                      stock: (variantData.stock != null) ? String(variantData.stock) : String(DEFAULT_STOCK),
                      isDefault: variantData.is_default ? 1 : 0
                    });
                }
            });
        }

        if (usedAttrIds.length) {
            $attrSelect.val(usedAttrIds.map(String));
            $attrSelect.trigger('change.select2');
            renderValueBlocks(preValsMap);
            regenerateTable();
        } else {
            regenerateTable();
        }
        initialHydrated = true;
        
    } else if (!initialHydrated) {
        renderValueBlocks();
        regenerateTable();
    }
  });
})();