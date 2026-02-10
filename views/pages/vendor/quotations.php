<div class="quotations">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
        <div>
            <h1>Price Quotations</h1>
            <p style="color: var(--text-dim);">Propose price changes for your products. Changes require admin approval.</p>
        </div>
        <button onclick="document.getElementById('quoteModal').style.display='flex'" class="btn">+ Propose Price Change</button>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Old Price</th>
                    <th>New Price</th>
                    <th>Status</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quotations as $q): ?>
                <tr>
                    <td><?= date('d M Y', strtotime($q['created_at'])) ?></td>
                    <td style="font-weight: 600;"><?= htmlspecialchars($q['product_name']) ?></td>
                    <td style="color: var(--text-dim);">₹<?= number_format($q['current_price'], 2) ?></td>
                    <td style="color: var(--accent-color); font-weight: 600;">₹<?= number_format($q['proposed_price'], 2) ?></td>
                    <td><span class="badge badge-<?= $q['status'] ?>"><?= ucfirst($q['status']) ?></span></td>
                    <td style="font-size: 0.85rem; color: var(--text-dim);"><?= htmlspecialchars($q['admin_note'] ?: '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Simple Quote Modal -->
<div id="quoteModal" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.8); z-index: 999; justify-content: center; align-items: center;">
    <div class="card" style="width: 400px;">
        <h3>Propose New Price</h3>
        <form action="/vendor/quotations" method="POST" style="margin-top: 1.5rem;">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; margin-bottom: 5px;">Product</label>
                <select name="product_id" required style="width: 100%; padding: 10px; background: var(--bg-color); border: 1px solid var(--border); color: #fff;">
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= $p['sku'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; margin-bottom: 5px;">Proposed Unit Price</label>
                <input type="number" name="proposed_price" step="0.01" required style="width: 100%; padding: 10px; background: var(--bg-color); border: 1px solid var(--border); color: #fff;">
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn" style="flex: 1;">Submit Proposal</button>
                <button type="button" onclick="document.getElementById('quoteModal').style.display='none'" class="btn" style="background: var(--border); flex: 1;">Cancel</button>
            </div>
        </form>
    </div>
</div>
