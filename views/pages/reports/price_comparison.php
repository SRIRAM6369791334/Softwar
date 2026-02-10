<h2>💰 Price Comparison Engine</h2>

<!-- Product Search -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="form-group" style="position: relative;">
        <label>Search Product</label>
        <input type="text" id="productSearch" class="form-control" placeholder="Type product name or SKU..." autocomplete="off">
        <div id="searchResults" style="position: absolute; top: 100%; left: 0; right: 0; background: var(--bg-color); border: 1px solid var(--border-color); border-top: none; z-index: 1000; display: none;"></div>
    </div>
</div>

<?php if ($product): ?>
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Main Analysis -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
                <div>
                    <h3 style="color: var(--hud-neon-blue); margin-bottom: 5px;"><?= htmlspecialchars($product['name']) ?></h3>
                    <div style="color: var(--text-dim); font-size: 0.9rem;">SKU: <?= htmlspecialchars($product['sku']) ?></div>
                </div>
                <?php 
                    // Find lowest price
                    $minPrice = 999999999;
                    $bestVendor = '';
                    foreach($comparisonData as $row) {
                        if($row['last_price'] < $minPrice) {
                            $minPrice = $row['last_price'];
                            $bestVendor = $row['vendor_name'];
                        }
                    }
                ?>
                <div style="text-align: right;">
                    <div style="font-size: 0.8rem; color: var(--text-dim);">BEST PRICE</div>
                    <div style="font-size: 1.5rem; color: var(--hud-neon-green); font-weight: bold;">₹<?= number_format($minPrice, 2) ?></div>
                    <div style="font-size: 0.8rem; color: var(--text-color);">from <?= htmlspecialchars($bestVendor) ?></div>
                </div>
            </div>

            <!-- Price Visualization (CSS Chart) -->
            <h4 style="border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin-bottom: 1rem;">Vendor Price Analysis</h4>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php 
                    $maxVal = 0;
                    foreach($comparisonData as $row) $maxVal = max($maxVal, $row['avg_price']);
                ?>
                <?php foreach ($comparisonData as $row): ?>
                    <?php 
                        $width = ($row['avg_price'] / $maxVal) * 100;
                        $isBest = $row['last_price'] == $minPrice;
                        $color = $isBest ? 'var(--hud-neon-green)' : 'var(--hud-neon-blue)';
                    ?>
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 5px;">
                            <span><?= htmlspecialchars($row['vendor_name']) ?></span>
                            <span>₹<?= number_format($row['avg_price'], 2) ?> (Avg)</span>
                        </div>
                        <div style="background: rgba(255,255,255,0.05); height: 8px; border-radius: 4px; overflow: hidden;">
                            <div style="width: <?= $width ?>%; height: 100%; background: <?= $color ?>; border-radius: 4px;"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-dim); margin-top: 2px;">
                            <span>Last: ₹<?= number_format($row['last_price'], 2) ?></span>
                            <span><?= date('M j, Y', strtotime($row['last_purchase_date'])) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Purchase History Table -->
        <div class="card">
            <h4 style="margin-bottom: 1rem;">Recent History</h4>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 8px;">Date</th>
                            <th style="padding: 8px;">Vendor</th>
                            <th style="padding: 8px; text-align: right;">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $po): ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td style="padding: 8px; color: var(--text-dim);"><?= date('d/m/y', strtotime($po['created_at'])) ?></td>
                                <td style="padding: 8px;"><?= htmlspecialchars($po['vendor_name']) ?></td>
                                <td style="padding: 8px; text-align: right; color: var(--hud-neon-blue);">₹<?= number_format($po['unit_price'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php else: ?>
    <div style="text-align: center; color: var(--text-dim); padding: 4rem;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
        <h3>Search for a product to compare prices</h3>
        <p>Analyze historical data to find the best deals.</p>
    </div>
<?php endif; ?>

<script>
    const searchInput = document.getElementById('productSearch');
    const resultsBox = document.getElementById('searchResults');

    let debounceTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value;

        if (query.length < 2) {
            resultsBox.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`/reports/price-comparison?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    resultsBox.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.style.padding = '10px';
                            div.style.cursor = 'pointer';
                            div.style.borderBottom = '1px solid var(--border-color)';
                            div.innerHTML = `
                                <div style="color: var(--text-color); font-weight: bold;">${item.name}</div>
                                <div style="color: var(--text-dim); font-size: 0.8rem;">${item.sku}</div>
                            `;
                            div.onmouseover = () => div.style.background = 'rgba(0, 243, 255, 0.1)';
                            div.onmouseout = () => div.style.background = 'transparent';
                            div.onclick = () => {
                                window.location.href = `/reports/price-comparison?product_id=${item.id}`;
                            };
                            resultsBox.appendChild(div);
                        });
                        resultsBox.style.display = 'block';
                    } else {
                        resultsBox.innerHTML = '<div style="padding: 10px; color: var(--text-dim);">No products found</div>';
                        resultsBox.style.display = 'block';
                    }
                });
        }, 300);
    });

    // Close on click outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
            resultsBox.style.display = 'none';
        }
    });
</script>
