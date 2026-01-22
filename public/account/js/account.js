$.ajaxSetup({
headers: {
  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
}
});


$(document).ready(function() { $('.select2').select2({ searchInputPlaceholder: '...' }); });


/* =======================
   VN MONEY INPUT HELPERS
======================= */
function vnMoneyToDigits(str) {
  return (str || '').toString().replace(/[^\d]/g, '');
}
function formatVnMoneyDigits(digits) {
  digits = (digits || '').toString().replace(/[^\d]/g, '');
  if (!digits) return '';
  return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}


/* =======================
   EVENTS
======================= */
// Chặn ký tự lạ
$(document).on('keydown', '.actual-cost-input', function(e) {
  const allow = ['Backspace','Delete','Tab','Enter','Escape','ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Home','End'];
  if ((e.ctrlKey || e.metaKey) && ['a','c','v','x'].includes(e.key.toLowerCase())) return;
  if (allow.includes(e.key)) return;
  if (/^\d$/.test(e.key)) return;
  e.preventDefault();
});

// Format khi gõ + lưu raw
$(document).on('input', '.actual-cost-input', function() {
  const el = this;
  const oldVal = el.value;
  const oldPos = el.selectionStart || 0;

  const digits = vnMoneyToDigits(oldVal);
  const newVal = formatVnMoneyDigits(digits);

  el.value = newVal;
  el.dataset.raw = digits;

  const diffLen = newVal.length - oldVal.length;
  const newPos = Math.max(0, oldPos + diffLen);
  try { el.setSelectionRange(newPos, newPos); } catch (e) {}
});

// Blur => save
$(document).on('blur', '.actual-cost-input', function() {
  const digits = vnMoneyToDigits(this.value);
  this.value = formatVnMoneyDigits(digits);
  this.dataset.raw = digits;

  saveActualCosts($(this));
});

// Enter => blur => save
$(document).on('keydown', '.actual-cost-input', function(e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    $(this).blur();
  }
});

// Init format cho input có sẵn
$(function() {
  $('.actual-cost-input').each(function() {
    const digits = vnMoneyToDigits(this.value);
    this.value = formatVnMoneyDigits(digits);
    this.dataset.raw = digits;
  });
});


/* =======================
   Cập nhật chi phí thực tế
======================= */
function saveActualCosts($input) {
  const url = $input.data('url');
  if (!url) return;

  const token = $('meta[name="csrf-token"]').attr('content');

  const rawDigits = ($input[0].dataset.raw || vnMoneyToDigits($input.val()) || '');
  const numberVal = rawDigits ? parseInt(rawDigits, 10) : 0;

  const last = parseInt($input.data('last') || 0, 10);
  if (numberVal === last) return;

  $input.prop('disabled', true).addClass('is-loading');

  $.ajax({
    url: url,
    type: 'POST',
    data: { _token: token, actual_costs: numberVal },
    success: function(res) {
      if (!res || !res.ok) {
        showToast?.('error', res?.message || 'Lỗi cập nhật');

        // rollback
        $input.val(formatVnMoneyDigits(String(last)));
        $input[0].dataset.raw = String(last);
        return;
      }

      const actual = parseInt(res.task?.actual_costs || 0, 10);

      // update input + last
      $input.val(formatVnMoneyDigits(String(actual)));
      $input[0].dataset.raw = String(actual);
      $input.data('last', actual);

      // update refund + extra
      const $row = $input.closest('tr');
      $row.find('.js-refund-money').text(res.task?.refund_money_formatted ?? '0');
      $row.find('.js-extra-money').text(res.task?.extra_money_formatted ?? '0');

      // (optional) đổi màu theo giá trị
      const refundVal = parseFloat(res.task?.refund_money || 0);
      const extraVal  = parseFloat(res.task?.extra_money || 0);

      $row.find('.js-refund-money')
        .toggleClass('text-success', refundVal > 0)
        .toggleClass('text-muted', refundVal <= 0);

      $row.find('.js-extra-money')
        .toggleClass('text-danger', extraVal > 0)
        .toggleClass('text-muted', extraVal <= 0);

      showToast?.('success', res.message || 'Đã lưu');
    },
    error: function(xhr) {
      const msg = xhr?.responseJSON?.message || 'Lỗi server';
      showToast?.('error', msg);

      // rollback
      $input.val(formatVnMoneyDigits(String(last)));
      $input[0].dataset.raw = String(last);
    },
    complete: function() {
      $input.prop('disabled', false).removeClass('is-loading');
    }
  });
}



/* =======================
   Submit tạo task mới
======================= */
$(document).on('submit', '#createTaskForm', function(e) {
    e.preventDefault();

    const $form = $(this);
    const $btn  = $('#btnCreateTask');

    // đổi tiền VN sang digits trước khi gửi
    const digits = vnMoneyToDigits($('#expected_costs').val());
    $('#expected_costs').val(digits ? digits : '0');

    $btn.prop('disabled', true);

    $.ajax({
      url: $form.attr('action'),
      type: 'POST',
      data: $form.serialize(),
      dataType: 'json',
      success: function(res) {
        if (!res || !res.ok) {
          showToast?.('error', res?.message || 'Thêm mới thất bại');
          return;
        }

        // đóng modal (tuỳ bạn)
        $('#newProjectModal').modal('hide');

        // redirect về đúng trang hiện tại (giữ filter + page)
        window.location.href = res.redirect || "{{ url()->full() }}";
      },
      error: function(xhr) {
        const msg = xhr?.responseJSON?.message || 'Lỗi server';
        showToast?.('error', msg);

        // nếu muốn show lỗi validate chi tiết:
        // console.log(xhr?.responseJSON?.errors);
      },
      complete: function() {
        $btn.prop('disabled', false);
      }
    });
  });


/*=========== DUYỆT MARKETING ================*/
$(function () {
  // CSRF cho toàn bộ ajax
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      'X-Requested-With': 'XMLHttpRequest'
    }
  });

  // delegation để bắt event cả khi table render lại
  $(document).on('change', '.active-toggle-approved', function () {
    const checkbox = $(this);
    const approved = checkbox.is(':checked') ? 1 : 0;
    const url = checkbox.data('url');

    $.ajax({
      url: url,
      type: 'POST',
      data: { approved: approved },

      success: function (res) {
        if (res && res.success) {

          const $row   = checkbox.closest('tr');
          const $badge = $row.find('.js-approved-badge'); // <-- lấy đúng badge trong dòng

          const isApproved = (parseInt(res.approved, 10) === 1);

          if (isApproved) {
            $badge.removeClass('btn-warning').addClass('btn-success').text('Đã duyệt');
          } else {
            $badge.removeClass('btn-success').addClass('btn-warning').text('Chờ duyệt');
          }

          if (typeof showToast === 'function') showToast('success', res.message || 'Đã lưu');

          // (nếu server trả extra/refund thì update luôn)
          if (res.task) {
            if (res.task.refund_money_formatted !== undefined) {
              $row.find('.js-refund-money').text(res.task.refund_money_formatted);
            }
            if (res.task.extra_money_formatted !== undefined) {
              $row.find('.js-extra-money').text(res.task.extra_money_formatted);
            }
          }

        } else {
          if (typeof showToast === 'function') showToast('error', (res && res.message) || 'Thất bại');
          checkbox.prop('checked', !checkbox.is(':checked'));
        }
      },


      error: function (xhr) {
        // Lấy message từ JSON trả về (ví dụ 403)
        const msg =
          (xhr.responseJSON && xhr.responseJSON.message) ||
          'Thất bại';

        if (typeof showToast === 'function') showToast('error', msg);
        checkbox.prop('checked', !checkbox.is(':checked'));
      }
    });
  });
});



/*=========== nút tất toán chi phí task ================*/
$(document).on('change', '.js-toggle-settled', function () {
  const cb = $(this);
  const url = cb.data('url');
  const settled = cb.is(':checked') ? 1 : 0;

  cb.prop('disabled', true);

  $.post(url, { settled })
    .done(function (res) {
      if (res && (res.ok === true || res.success === true)) {
        // optional: update UI theo response
        // ví dụ: res.task.refund_money_formatted / extra_money_formatted
        const $row = cb.closest('tr');

        if (res.task) {
          if (res.task.refund_money_formatted !== undefined) {
            $row.find('.js-refund-money').text(res.task.refund_money_formatted);
          }
          if (res.task.extra_money_formatted !== undefined) {
            $row.find('.js-extra-money').text(res.task.extra_money_formatted);
          }
        }

        if (typeof showToast === 'function') {
          showToast('success', res.message || (settled ? 'Đã tất toán' : 'Đã hủy tất toán'));
        }
      } else {
        cb.prop('checked', !cb.is(':checked')); // revert
        if (typeof showToast === 'function') {
          showToast('error', (res && res.message) || 'Thất bại');
        }
      }
    })
    .fail(function (xhr) {
      cb.prop('checked', !cb.is(':checked')); // revert
      const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Thất bại';
      if (typeof showToast === 'function') showToast('error', msg);
    })
    .always(function () {
      cb.prop('disabled', false);
    });
});

/*=========== DUYỆT đóng tiền marketing ================*/
document.addEventListener('change', function (e) {
  const el = e.target;
  if (!el.classList.contains('active-toggle-updatePaid')) return;

  const url = el.dataset.url;
  const paid = el.checked ? 1 : 0;              // 1 = HOLD, 0 = RELEASE
  const oldState = !el.checked;                 // rollback UI nếu hủy/lỗi

  const rank = parseInt(el.dataset.rank || '0', 10);
  const isMine = parseInt(el.dataset.mine || '0', 10) === 1;
  const sameDept = parseInt(el.dataset.samedept || '0', 10) === 1;

  // ===== RULE UI (theo yêu cầu bạn đã chốt) =====
  // rank2: chỉ HOLD nếu cùng department_id, không RELEASE
  if (rank === 2) {
    if (paid === 0) { el.checked = true; showCenterError('Bạn không được hủy giữ tiền (RELEASE).'); return; }
    if (!sameDept)  { el.checked = false; showCenterError('Bạn chỉ được giữ tiền cho tác vụ cùng phòng ban (department).'); return; }
  }
  // rank3: chỉ HOLD task của mình, không RELEASE
  if (rank === 3) {
    if (paid === 0) { el.checked = true; showCenterError('Bạn không được hủy giữ tiền (RELEASE).'); return; }
    if (!isMine)    { el.checked = false; showCenterError('Bạn chỉ được giữ tiền (HOLD) tác vụ của mình.'); return; }
  }

  // ===== Confirm trước khi gọi API =====
  const confirmTitle = paid ? 'Xác nhận đóng tiền (HOLD)?' : 'Xác nhận nhả giữ (RELEASE)?';
  const confirmText  = paid
    ? 'Bạn có chắc muốn đóng tiền cho tác vụ này không?'
    : 'Bạn có chắc muốn nhả giữ tiền cho tác vụ này không?';

  Swal.fire({
    icon: 'question',
    title: confirmTitle,
    text: confirmText,
    showCancelButton: true,
    confirmButtonText: paid ? 'Đóng tiền' : 'Nhả giữ',
    cancelButtonText: 'Không',
    reverseButtons: true
  }).then((result) => {
    if (!result.isConfirmed) {
      el.checked = oldState; // người dùng bấm Hủy -> rollback UI
      return;
    }

    fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json',
      },
      body: JSON.stringify({ paid })
    })
    .then(async (res) => {
      const data = await res.json().catch(() => ({}));
      if (!res.ok || data.status === false) throw new Error(data.message || 'Có lỗi xảy ra');

      showToast('success', data.message || 'Thành công');

      // update UI trong cùng dòng
      const tr = el.closest('tr');
      const badgeEl = tr?.querySelector('.hold-badge');
      if (badgeEl) {
        badgeEl.classList.toggle('text-success', paid === 1);
        badgeEl.classList.toggle('text-danger',  paid === 0);
      }


      // update số dư menu (nếu backend trả về data.wallet.balance)
      if (data.wallet && typeof data.wallet.balance !== 'undefined') {
        const menuBalanceEl = document.getElementById('menuBalance');
        if (menuBalanceEl) {
          menuBalanceEl.textContent = Number(data.wallet.balance || 0).toLocaleString('vi-VN');
        }
        const menuHeldEl = document.getElementById('menuHeld');
    if (menuHeldEl) menuHeldEl.textContent = Number(data.wallet.held_balance||0).toLocaleString('vi-VN');
      }

    })
    .catch(err => {
      el.checked = oldState; // rollback khi lỗi
      showCenterError(err.message || 'Có lỗi xảy ra, vui lòng thử lại!');
    });
  });
});



/*=========== Xuất file excel ================*/
(function () {

  function sanitizeCellText(td) {
    let text = '';

    // Nếu cell có input/select/textarea -> lấy value
    const input = td.querySelector('input, select, textarea');
    if (input) {
      text = (input.value ?? '').toString();
    } else {
      // Nếu có data-export -> ưu tiên
      const v = td.getAttribute('data-export');
      text = v !== null ? v.toString() : (td.innerText ?? '').toString();
    }

    text = text.trim();

    // ===== CHỈ XỬ LÝ CỘT TIỀN =====
    // <td class="money">1.000.000 ₫</td>
    if (td.classList.contains('money')) {
      text = text
        .replace(/[₫đ]/gi, '')     // bỏ ký tự ₫ đ
        .replace(/VNĐ/gi, '')      // bỏ VNĐ
        .replace(/[.,\s]/g, '');   // bỏ . , khoảng trắng
    }

    return text;
  }

  function buildCleanTable(originalTable) {
    const clone = originalTable.cloneNode(true);

    // Bỏ các cột / ô không export
    clone.querySelectorAll('.no-export').forEach(el => el.remove());

    // Bỏ button / icon
    clone.querySelectorAll(
      'button, a.btn, .btn, .tio-edit, .tio-delete, .dropdown, .avatar, img'
    ).forEach(el => el.remove());

    // Convert tất cả cell thành text sạch
    clone.querySelectorAll('td, th').forEach(cell => {
      const val = sanitizeCellText(cell);
      cell.innerHTML = '';
      cell.textContent = val;
    });

    return clone;
  }

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.js-export-excel');
    if (!btn) return;

    const selector = btn.getAttribute('data-table');
    const filename = btn.getAttribute('data-filename') || 'export.xlsx';
    if (!selector) return;

    const table = document.querySelector(selector);
    if (!table) return;

    const cleanTable = buildCleanTable(table);

    const wb = XLSX.utils.table_to_book(cleanTable, { sheet: "Sheet1" });
    XLSX.writeFile(wb, filename);
  });

})();



/*=========== DUYỆT MARKETING ================*/