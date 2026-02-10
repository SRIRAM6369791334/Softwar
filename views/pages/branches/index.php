<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Branch Management</h2>
    <a href="/branches/create" class="btn btn-primary">+ Add New Branch</a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
    <?php foreach ($branches as $branch): ?>
        <div class="card" style="position: relative;">
            <?php if ($branch['id'] == $current_branch_id): ?>
                <div style="position: absolute; top: 1rem; right: 1rem; background: #00f3ff; color: #000; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.8rem; font-weight: bold; z-index: 2;">
                    ACTIVE
                </div>
            <?php endif; ?>

            <?php if (!empty($branch['background_url'])): ?>
                <div style="height: 100px; background-image: url('<?= $branch['background_url'] ?>'); background-size: cover; background-position: center; border-radius: 8px 8px 0 0; margin: -1.5rem -1.5rem 1rem -1.5rem; opacity: 0.6;"></div>
            <?php endif; ?>
            
            <h3 style="margin-bottom: 0.5rem; color: #00f3ff;"><?= htmlspecialchars($branch['name']) ?></h3>
            
            <?php if ($branch['location']): ?>
                <p style="color: #999; margin-bottom: 1rem;">
                    📍 <?= htmlspecialchars($branch['location']) ?>
                </p>
            <?php endif; ?>

            <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(0, 243, 255, 0.05); border-radius: 4px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.9rem;">
                    <div>
                        <div style="color: #999;">Today's Sales</div>
                        <div style="color: #00f3ff; font-weight: bold;">₹<?= number_format($branch['today_sales'], 2) ?></div>
                    </div>
                    <div>
                        <div style="color: #999;">Staff</div>
                        <div style="color: #fff; font-weight: bold;"><?= $branch['staff_count'] ?></div>
                    </div>
                    <div>
                        <div style="color: #999;">Products</div>
                        <div style="color: #fff; font-weight: bold;"><?= $branch['product_count'] ?></div>
                    </div>
                    <div>
                        <div style="color: #999;">Status</div>
                        <div style="color: <?= $branch['is_active'] ? '#0f0' : '#f00' ?>; font-weight: bold;">
                            <?= $branch['is_active'] ? 'Active' : 'Inactive' ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($branch['manager_name']): ?>
                <p style="color: #999; font-size: 0.85rem; margin-bottom: 1rem;">
                    👤 Manager: <?= htmlspecialchars($branch['manager_name']) ?>
                </p>
            <?php endif; ?>

            <div style="display: flex; gap: 0.5rem;">
                <?php if ($branch['id'] != $current_branch_id): ?>
                    <button onclick="switchBranch(<?= $branch['id'] ?>)" class="btn btn-primary" style="flex: 1;">
                        Switch to <?= htmlspecialchars($branch['name']) ?>
                    </button>
                <?php endif; ?>
                <a href="/branches/edit/<?= $branch['id'] ?>" class="btn" style="flex: 1;">✎ Edit</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
function switchBranch(branchId) {
    if (!confirm('Switch to this branch? All data will be filtered by this branch.')) {
        return;
    }

    if (typeof GlobalLoader !== 'undefined') GlobalLoader.start();

    fetch('/branches/switch/' + branchId, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Failed to switch branch');
        }
    })
    .catch(err => {
        alert('Error switching branch');
        console.error(err);
    });
}
</script>
