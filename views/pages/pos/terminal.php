<div class="pos-wrapper">
    <!-- Left Panel: Billing Area -->
    <div class="billing-panel">
        <div class="search-bar">
            <input type="text" id="productSearch" placeholder="SCAN BARCODE OR TYPE NAME (F1)..." autofocus autocomplete="off">
            <div id="searchResults" class="search-dropdown"></div>
        </div>

        <div class="cart-table-container">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Item</th>
                        <th style="width: 100px;">Price</th>
                        <th style="width: 80px;">Qty</th>
                        <th style="width: 80px;">Tax</th>
                        <th style="width: 100px;">Total</th>
                        <th style="width: 50px;">X</th>
                    </tr>
                </thead>
                <tbody id="cartBody">
                    <!-- JS Rows Here -->
                </tbody>
            </table>
        </div>
        
        <div class="message-log" id="msgLog" style="color:#888; font-size: 0.8rem; padding: 10px;">
            System Ready. Press F1 to search.
        </div>
    </div>

    <!-- Right Panel: Totals & Actions -->
    <div class="totals-panel">
        <div class="total-row">
            <span>Sub Total</span>
            <span id="lblSubTotal">0.00</span>
        </div>
        <div class="total-row">
            <span>Tax GST</span>
            <span id="lblTax">0.00</span>
        </div>
        
        <div class="grand-total-box">
            <small>GRAND TOTAL</small>
            <div id="lblGrandTotal">0.00</div>
        </div>

        <div class="action-buttons">
            <button class="btn-pos btn-pay" onclick="processCheckout()">[F10] CHECKOUT & PRINT</button>
            <button class="btn-pos btn-hold">[F4] HOLD BILL</button>
            <button class="btn-pos btn-cancel" onclick="clearCart()">[ESC] CANCEL</button>
        </div>
        
        <div style="margin-top: 2rem;">
            <h3>Shortcuts</h3>
            <ul style="font-size: 0.8rem; color: #888; padding-left: 1.2rem;">
                <li><strong>F1</strong> : Focus Search</li>
                <li><strong>F2</strong> : Edit Qty</li>
                <li><strong>Del</strong> : Remove Item</li>
                <li><strong>F10</strong> : Finish Bill</li>
            </ul>
        </div>
    </div>
</div>

<style>
    .pos-wrapper {
        display: flex;
        height: calc(100vh - 120px); /* Adjust for topbar/padding */
        gap: 20px;
    }
    .billing-panel {
        flex: 3;
        display: flex;
        flex-direction: column;
        background: #161b22;
        border: 1px solid #30363d;
        border-radius: 6px;
        overflow: hidden;
    }
    .totals-panel {
        flex: 1;
        background: #161b22;
        border: 1px solid #30363d;
        border-radius: 6px;
        padding: 20px;
        display: flex;
        flex-direction: column;
    }
    
    .search-bar { padding: 15px; border-bottom: 1px solid #30363d; position: relative; }
    #productSearch {
        width: 100%; font-size: 1.2rem; padding: 12px;
        background: #0d1117; color: #fff; border: 1px solid #00f3ff;
        border-radius: 4px; font-family: monospace;
    }
    .search-dropdown {
        position: absolute; top: 60px; left: 15px; right: 15px;
        background: #21262d; border: 1px solid #30363d;
        max-height: 300px; overflow-y: auto; z-index: 100;
        display: none;
    }
    .search-item {
        padding: 10px; border-bottom: 1px solid #30363d; cursor: pointer;
        display: flex; justify-content: space-between;
    }
    .search-item:hover, .search-item.selected { background: #00f3ff; color: #000; }

    .cart-table-container { flex: 1; overflow-y: auto; }
    .cart-table { width: 100%; border-collapse: collapse; }
    .cart-table th { background: #21262d; padding: 10px; text-align: left; font-size: 0.8rem; position: sticky; top:0; }
    .cart-table td { padding: 8px 10px; border-bottom: 1px solid #30363d; }
    .cart-table tr:nth-child(even) { background: rgba(255,255,255,0.02); }

    .grand-total-box {
        background: #000; border: 2px solid #2ea043;
        color: #2ea043; text-align: right; padding: 20px;
        margin: 20px 0; border-radius: 8px;
    }
    #lblGrandTotal { font-size: 2.5rem; font-weight: bold; }
    
    .btn-pos {
        width: 100%; padding: 15px; margin-bottom: 10px;
        border: none; border-radius: 4px; cursor: pointer;
        font-weight: bold; font-size: 1rem; text-transform: uppercase;
    }
    .btn-pay { background: #2ea043; color: #fff; }
    .btn-hold { background: #d29922; color: #000; }
    .btn-cancel { background: #da3633; color: #fff; }
</style>

<script>
    let cart = [];
    const searchInput = document.getElementById('productSearch');
    const resultBox = document.getElementById('searchResults');
    
    // --- Keyboard Shortcuts ---
    document.addEventListener('keydown', (e) => {
        if(e.key === 'F1') { e.preventDefault(); searchInput.focus(); }
        if(e.key === 'F10') { e.preventDefault(); processCheckout(); }
        if(e.key === 'Escape') { e.preventDefault(); clearCart(); }
    });

    // --- Search Logic ---
    let debounceTimer;
    searchInput.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        const query = e.target.value.trim();
        if(query.length < 2) { resultBox.style.display = 'none'; return; }

        debounceTimer = setTimeout(() => {
            fetch(`/pos/search?q=${query}`)
                .then(res => res.json())
                .then(data => {
                    resultBox.innerHTML = '';
                    if(data.length === 0) {
                        resultBox.innerHTML = '<div style="padding:10px; color:orange">No item found</div>';
                    } else {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'search-item';
                            div.innerHTML = `<span>${item.name} (${item.sku})</span><span>₹${item.sale_price}</span>`;
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
        // Check if exists
        const existing = cart.find(c => c.batch_id === item.batch_id);
        if(existing) {
            existing.qty++;
        } else {
            cart.push({
                id: item.id,
                batch_id: item.batch_id,
                name: item.name,
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
            // Calculations
            // Price usually is inclusive or exclusive. Assuming Exclusive for calculation logic wrapper here,
            // or we will calculate tax amount derived.
            // Let's assume Sale Price is BASE PRICE for simplicity of math display, 
            // OR Sale Price is MRP. Real POS logic maps these strictly.
            // Using: SalePrice = Unit Price before Tax.
            
            const lineBase = item.sale_price * item.qty;
            const taxAmt = (lineBase * item.tax_percent) / 100;
            const lineTotal = lineBase + taxAmt;

            item.tax_amount = taxAmt; // store for checkout
            item.line_total = lineTotal;

            subTotal += lineBase;
            taxTotal += taxAmt;
            grandTotal += lineTotal;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.name}<br><small>${item.sku}</small></td>
                <td>${item.sale_price.toFixed(2)}</td>
                <td>
                    <input type="number" value="${item.qty}" style="width:50px; padding:2px;" 
                           onchange="updateQty(${index}, this.value)">
                </td>
                <td>${taxAmt.toFixed(2)} <span style="font-size:0.7em">(${item.tax_percent}%)</span></td>
                <td>${lineTotal.toFixed(2)}</td>
                <td><button onclick="removeItem(${index})" style="color:red; background:none; border:none; cursor:pointer;">X</button></td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById('lblSubTotal').innerText = subTotal.toFixed(2);
        document.getElementById('lblTax').innerText = taxTotal.toFixed(2);
        document.getElementById('lblGrandTotal').innerText = grandTotal.toFixed(2);
    }

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
            grandTotal: grandTotal
        };

        const btn = document.querySelector('.btn-pay');
        btn.disabled = true;
        btn.innerText = "PROCESSING...";

        fetch('/pos/checkout', {
            method: 'POST',
            body: JSON.stringify(payload),
            headers: {'Content-Type': 'application/json'}
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                alert(`Transaction Success!\nInvoice: ${data.invoice_no}\n(Printer would trigger here)`);
                cart = [];
                renderCart();
            } else {
                alert("Error: " + data.message);
            }
            btn.disabled = false;
            btn.innerText = "[F10] CHECKOUT & PRINT";
            searchInput.focus();
        });
    }
</script>
