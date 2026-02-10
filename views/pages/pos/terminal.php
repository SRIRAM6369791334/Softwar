<head>
    <meta name="csrf-token" content="<?= \App\Core\Auth::generateCsrfToken() ?>">
</head>
<div class="pos-wrapper">
    <!-- Left Panel: Billing Area -->
    <div class="billing-panel">
        <div class="search-bar">
            <input type="text" id="productSearch" placeholder="SCAN BARCODE OR TYPE NAME (F1)..." autofocus autocomplete="off">
            <div id="searchResults" class="search-dropdown"></div>
        </div>

        <!-- ... (Rest of HTML structure unchanged) ... -->

<script>
    let cart = [];
    const searchInput = document.getElementById('productSearch');
    const resultBox = document.getElementById('searchResults');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // ... (Shortcuts unchanged) ...

    // --- Search Logic ---
    let debounceTimer;
    searchInput.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        const query = e.target.value.trim();
        if(query.length < 2) { resultBox.style.display = 'none'; return; }

        debounceTimer = setTimeout(() => {
            fetch(`/pos/search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    resultBox.innerHTML = '';
                    if(data.length === 0) {
                        resultBox.innerHTML = '<div style="padding:10px; color:orange">No item found</div>';
                    } else {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'search-item';
                            // XSS Fix: Use textContent/innerText for user values
                            const nameSpan = document.createElement('span');
                            nameSpan.textContent = `${item.name} (${item.sku})`;
                            
                            const priceSpan = document.createElement('span');
                            priceSpan.textContent = `₹${item.sale_price}`;
                            
                            div.appendChild(nameSpan);
                            div.appendChild(priceSpan);
                            
                            div.onclick = () => addToCart(item);
                            resultBox.appendChild(div);
                        });
                        // Auto-select first result processing if barcode
                        if(data.length === 1 && query.length > 5) {
                            addToCart(data[0]);
                        }
                    }
                    resultBox.style.display = 'block';
                });
        }, 200);
    });

    function addToCart(item) {
        // ... (addToCart logic unchanged) ...
        // Check if exists
        const existing = cart.find(c => c.batch_id === item.batch_id);
        if(existing) {
            existing.qty++;
        } else {
            cart.push({
                id: item.id,
                batch_id: item.batch_id,
                name: item.name, // Potential XSS source if rendered unsafe later
                sku: item.sku,
                sale_price: parseFloat(item.sale_price),
                tax_percent: parseFloat(item.tax_percent),
                qty: 1
            });
        }
        
        searchInput.value = '';
        resultBox.style.display = 'none';
        searchInput.focus();
        renderCart();
    }

    function renderCart() {
        const tbody = document.getElementById('cartBody');
        tbody.innerHTML = '';
        
        let subTotal = 0;
        let taxTotal = 0;
        let grandTotal = 0;

        cart.forEach((item, index) => {
            const lineBase = item.sale_price * item.qty;
            const taxAmt = (lineBase * item.tax_percent) / 100;
            const lineTotal = lineBase + taxAmt;

            item.tax_amount = taxAmt; 
            item.line_total = lineTotal;

            subTotal += lineBase;
            taxTotal += taxAmt;
            grandTotal += lineTotal;

            const tr = document.createElement('tr');
            
            // Safe Render for Name
            const tdIndex = document.createElement('td'); tdIndex.textContent = index + 1;
            
            const tdItem = document.createElement('td'); 
            const nameBold = document.createElement('div'); nameBold.textContent = item.name;
            const skuSmall = document.createElement('small'); skuSmall.textContent = item.sku;
            tdItem.appendChild(nameBold); tdItem.appendChild(skuSmall);

            const tdPrice = document.createElement('td'); tdPrice.textContent = item.sale_price.toFixed(2);
            
            const tdQty = document.createElement('td');
            const qtyInput = document.createElement('input');
            qtyInput.type = 'number';
            qtyInput.value = item.qty;
            qtyInput.style.cssText = "width:50px; padding:2px;";
            qtyInput.onchange = (e) => updateQty(index, e.target.value);
            tdQty.appendChild(qtyInput);

            const tdTax = document.createElement('td'); 
            tdTax.innerHTML = `${taxAmt.toFixed(2)} <span style="font-size:0.7em">(${item.tax_percent}%)</span>`; // Safe, numbers only

            const tdTotal = document.createElement('td'); tdTotal.textContent = lineTotal.toFixed(2);

            const tdAction = document.createElement('td');
            const btn = document.createElement('button');
            btn.textContent = 'X';
            btn.style.cssText = "color:red; background:none; border:none; cursor:pointer;";
            btn.onclick = () => removeItem(index);
            tdAction.appendChild(btn);

            tr.appendChild(tdIndex);
            tr.appendChild(tdItem);
            tr.appendChild(tdPrice);
            tr.appendChild(tdQty);
            tr.appendChild(tdTax);
            tr.appendChild(tdTotal);
            tr.appendChild(tdAction);

            tbody.appendChild(tr);
        });

        document.getElementById('lblSubTotal').innerText = subTotal.toFixed(2);
        document.getElementById('lblTax').innerText = taxTotal.toFixed(2);
        document.getElementById('lblGrandTotal').innerText = grandTotal.toFixed(2);
    }

    // ... (updateQty, removeItem, clearCart unchanged) ...

    function updateQty(index, val) {
        if(val <= 0) { removeItem(index); return; }
        cart[index].qty = parseFloat(val);
        renderCart();
    }

    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function clearCart() {
        if(confirm("Clear current bill?")) {
            cart = [];
            renderCart();
        }
    }

    function processCheckout() {
        if(cart.length === 0) { alert("Cart is empty"); return; }
        
        const subTotal = parseFloat(document.getElementById('lblSubTotal').innerText);
        const taxTotal = parseFloat(document.getElementById('lblTax').innerText);
        const grandTotal = parseFloat(document.getElementById('lblGrandTotal').innerText);

        const payload = {
            items: cart,
            subTotal: subTotal,
            taxTotal: taxTotal,
            grandTotal: grandTotal,
            csrf_token: csrfToken // Include Token
        };

        const btn = document.querySelector('.btn-pay');
        btn.disabled = true;
        btn.innerText = "PROCESSING...";

        fetch('/pos/checkout', {
            method: 'POST',
            body: JSON.stringify(payload),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken 
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                // Update: API Standard Response wraps data
                const invoiceNo = data.data ? data.data.invoice_no : data.invoice_no;
                const invoiceId = data.data ? data.data.invoice_id : data.invoice_id;
                
                alert('Transaction Complete! Invoice: ' + invoiceNo);
                cart = [];
                renderCart();
                // Optionally print
                // window.open('/print/invoice/' + (invoiceId), '_blank');
            } else {
                alert('Error: ' + data.message);
            }
            btn.disabled = false;
            btn.innerText = "[F10] CHECKOUT & PRINT";
            searchInput.focus();
        });
    }
</script>
