<div class="move-stock">
    <div style="margin-bottom: 2rem;">
        <a href="/inventory/transfers" style="color: #8b949e; text-decoration: none;">← Back to Transfers</a>
        <h1>Move Stock to Another Branch</h1>
    </div>

    <div class="card" style="max-width: 600px;">
        <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
            <button type="button" class="btn btn-mode active" id="btn-direct" onclick="setMode('direct')" style="flex:1">Direct Transfer</button>
            <button type="button" class="btn btn-mode" id="btn-request" onclick="setMode('request')" style="flex:1">Request Stock (IBPO)</button>
        </div>

        <form action="/inventory/transfers/store" method="POST">
            <input type="hidden" name="type" id="transfer_type" value="direct">
            
            <div id="branch_selector_container" class="form-group">
                <label id="branch_label">Destination Branch</label>
                <select name="to_branch_id" id="target_branch_id" required>
                    <option value="">-- Select Branch --</option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>"><?= htmlspecialchars($branch['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <!-- For Request mode, we'll swap from/to labels -->
                <input type="hidden" name="from_branch_id" id="source_branch_id" value="">
            </div>

            <div class="form-group">
                <label>Product to Move</label>
                <select name="product_id" id="product_id" required onchange="loadBatches(this.value)">
                    <option value="">-- Select Product --</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= $p['sku'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Source Batch (Current Branch)</label>
                <select name="batch_id" id="batch_id" required onchange="updateMaxQty()">
                    <option value="">Select product first...</option>
                </select>
                <small id="stock_info" style="color: var(--accent-color); display: block; margin-top: 5px;"></small>
            </div>

            <div class="form-group">
                <label>Quantity to Move</label>
                <input type="number" name="qty" id="qty" step="0.01" min="0.01" required>
            </div>

            <div class="form-group">
                <label>Remarks</label>
                <textarea name="remarks" rows="2" style="width:100%; padding:10px; background:#0d1117; border:1px solid var(--border-color); color:#fff; border-radius:4px;"></textarea>
            </div>

            <div style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="width: 100%;">Initiate Transfer</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentBatches = [];

function setMode(mode) {
    const typeInput = document.getElementById('transfer_type');
    typeInput.value = mode;
    
    // UI Toggles
    document.querySelectorAll('.btn-mode').forEach(b => b.classList.remove('active'));
    document.getElementById('btn-' + mode).classList.add('active');
    
    const branchLabel = document.getElementById('branch_label');
    const batchGroup = document.getElementById('batch_id').closest('.form-group');
    const branchSelect = document.getElementById('target_branch_id');
    const sourceBranchInput = document.getElementById('source_branch_id');

    if (mode === 'request') {
        branchLabel.innerText = "Request Stock From (Source)";
        batchGroup.style.display = 'none';
        document.getElementById('batch_id').required = false;
        // In request mode, the selected branch IS 'from_branch_id'
        branchSelect.name = "from_branch_id";
        sourceBranchInput.name = "to_branch_id"; // Not used but keeps logic saner
    } else {
        branchLabel.innerText = "Send Stock To (Destination)";
        batchGroup.style.display = 'block';
        document.getElementById('batch_id').required = true;
        branchSelect.name = "to_branch_id";
    }
}

function loadBatches(productId) {
    if (!productId) return;
    const batchSelect = document.getElementById('batch_id');
    batchSelect.innerHTML = '<option>Loading batches...</option>';
    
    fetch('/inventory/transfers/batches/' + productId)
        .then(res => res.json())
        .then(data => {
            currentBatches = data;
            batchSelect.innerHTML = '<option value="">-- Select Batch --</option>';
            data.forEach(batch => {
                batchSelect.innerHTML += `<option value="${batch.id}">Batch: ${batch.batch_no} | Stock: ${batch.stock_qty} | Exp: ${batch.expiry_date}</option>`;
            });
        });
}

function updateMaxQty() {
    const batchId = document.getElementById('batch_id').value;
    const qtyInput = document.getElementById('qty');
    const info = document.getElementById('stock_info');
    
    const batch = currentBatches.find(b => b.id == batchId);
    if (batch) {
        qtyInput.max = batch.stock_qty;
        info.innerText = 'Max available: ' + batch.stock_qty;
    } else {
        info.innerText = '';
    }
}
</script>
