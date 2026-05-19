document.addEventListener('DOMContentLoaded', function () {

    // ===== GLOBAL: DELETE RECORD (works even inside tables) =====
    window.deleteRecord = function (url, message) {
        if (confirm(message || 'Are you sure you want to delete this record? This action cannot be undone.')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            var token = document.createElement('input');
            token.type = 'hidden'; token.name = '_token';
            token.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(token);

            var method = document.createElement('input');
            method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
            form.appendChild(method);

            document.body.appendChild(form);
            form.submit();
        }
    };

    // ===== GLOBAL: POST ACTION (for confirm buttons like receive, complete) =====
    window.postAction = function (url, message, extraData) {
        if (!message || confirm(message)) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            var token = document.createElement('input');
            token.type = 'hidden'; token.name = '_token';
            token.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(token);

            if (extraData) {
                Object.keys(extraData).forEach(function (key) {
                    var f = document.createElement('input');
                    f.type = 'hidden'; f.name = key; f.value = extraData[key];
                    form.appendChild(f);
                });
            }

            document.body.appendChild(form);
            form.submit();
        }
    };

    // ===== SIDEBAR SUBMENUS =====
    var menuToggle = document.getElementById('mobile-menu-toggle');
    var sidebar = document.getElementById('sidebar');
    var backdrop = document.getElementById('sidebar-backdrop');
    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('mobile-open');
        if (backdrop) backdrop.classList.remove('show');
    }
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function () {
            sidebar.classList.toggle('mobile-open');
            if (backdrop) backdrop.classList.toggle('show');
        });
    }
    if (backdrop) backdrop.addEventListener('click', closeSidebar);

    document.querySelectorAll('.nav-link.has-submenu').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            var id = this.getAttribute('data-submenu');
            var sub = document.getElementById(id);
            this.classList.toggle('open');
            if (sub) sub.classList.toggle('open');
        });
    });
    // Auto-open active submenu
    document.querySelectorAll('.nav-sub-link.active').forEach(function (activeLink) {
        var sub = activeLink.closest('.nav-submenu');
        if (sub) {
            sub.classList.add('open');
            var parent = document.querySelector('[data-submenu="' + sub.id + '"]');
            if (parent) parent.classList.add('open');
        }
    });

    // ===== AUTO-DISMISS ALERTS =====
    document.querySelectorAll('.alert[data-auto-dismiss]').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity .5s ease';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 500);
        }, 5000);
    });

    // ===== PAYMENT METHOD — show "description" field when Other is selected =====
    var paymentSelect = document.getElementById('payment_method');
    var paymentDesc   = document.getElementById('payment-other-wrap');
    if (paymentSelect && paymentDesc) {
        function togglePaymentDesc() {
            paymentDesc.style.display = paymentSelect.value === 'Other' ? 'block' : 'none';
        }
        paymentSelect.addEventListener('change', togglePaymentDesc);
        togglePaymentDesc(); // init
    }

    // ===== FORMAT RUPIAH =====
    window.formatRupiah = function (num) {
        return 'Rp ' + Math.round(num).toLocaleString('id-ID');
    };

    // ===== SALE/PURCHASE ITEM ROWS =====
    var itemContainer = document.getElementById('item-rows');
    var addItemBtn    = document.getElementById('add-item-btn');
    var itemTemplate  = document.getElementById('item-row-template');

    if (addItemBtn && itemContainer && itemTemplate) {
        var rowIndex = itemContainer.querySelectorAll('.item-row').length;

        addItemBtn.addEventListener('click', function () {
            var clone = itemTemplate.content.cloneNode(true);
            clone.querySelectorAll('[name]').forEach(function (el) {
                el.name = el.name.replace('__IDX__', rowIndex);
            });
            clone.querySelectorAll('[id]').forEach(function (el) {
                el.id = el.id.replace('__IDX__', rowIndex);
            });
            itemContainer.appendChild(clone);
            rowIndex++;
            bindItemRow(itemContainer.querySelector('.item-row:last-child'));
            updateGrandTotal();
        });
    }

    // Bind existing rows on load
    if (itemContainer) {
        itemContainer.querySelectorAll('.item-row').forEach(bindItemRow);
        updateGrandTotal();
    }

    function bindItemRow(row) {
        if (!row) return;
        var productSelect = row.querySelector('.product-select');
        var qtyInput      = row.querySelector('.qty-input');
        var priceInput    = row.querySelector('.price-input');
        var totalInput    = row.querySelector('.line-total');
        var removeBtn     = row.querySelector('.remove-item');

        function recalcLine() {
            var qty   = parseFloat(qtyInput ? qtyInput.value : 0) || 0;
            var price = parseFloat(priceInput ? priceInput.value : 0) || 0;
            var total = qty * price;
            if (totalInput) totalInput.value = total.toFixed(0);
            updateGrandTotal();
        }

        if (productSelect) {
            productSelect.addEventListener('change', function () {
                var opt = this.options[this.selectedIndex];
                if (priceInput && opt && opt.dataset.price) {
                    priceInput.value = opt.dataset.price;
                }
                recalcLine();
            });
        }
        if (qtyInput)   qtyInput.addEventListener('input', recalcLine);
        if (priceInput) priceInput.addEventListener('input', recalcLine);
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                row.remove();
                updateGrandTotal();
            });
        }
    }

    function updateGrandTotal() {
        var subtotal = 0;
        document.querySelectorAll('.line-total').forEach(function (el) {
            subtotal += parseFloat(el.value) || 0;
        });

        var discountEl  = document.getElementById('discount');
        var subtotalEl  = document.getElementById('display-subtotal');
        var grandTotalEl= document.getElementById('display-total');
        var hiddenTotal = document.getElementById('hidden-total');
        var hiddenSub   = document.getElementById('hidden-subtotal');

        if (subtotalEl)  subtotalEl.textContent = formatRupiah(subtotal);
        if (hiddenSub)   hiddenSub.value = subtotal;
        var discount = discountEl ? (parseFloat(discountEl.value) || 0) : 0;
        var grand = Math.max(0, subtotal - discount);
        if (grandTotalEl) grandTotalEl.textContent = formatRupiah(grand);
        if (hiddenTotal)  hiddenTotal.value = grand;
    }

    var discEl = document.getElementById('discount');
    if (discEl) discEl.addEventListener('input', updateGrandTotal);

    // ===== AUTO-GENERATE EMPLOYEE CODE PREVIEW =====
    var positionInput = document.getElementById('position_input');
    if (positionInput) {
        positionInput.addEventListener('input', function () {
            // code is auto-generated server-side, nothing needed here
        });
    }

    // ===== SERVICE ORDER: Mark Done — Service Cost modal =====
    var markDoneForm = document.getElementById('mark-done-form');
    if (markDoneForm) {
        markDoneForm.addEventListener('submit', function (e) {
            var cost = document.getElementById('service_cost_input');
            if (cost && parseFloat(cost.value) <= 0) {
                e.preventDefault();
                alert('Please enter the service cost before marking as Done.');
            }
        });
    }
});
