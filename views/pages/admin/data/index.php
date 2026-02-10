<div class="container-fluid">
    <h1>Data Tools & Integrity</h1>
    
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success py-2 px-3"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>
    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger py-2 px-3"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="row mt-4">
        <!-- Database Backup -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">💾 Database Backup</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Create a full SQL dump of the current database state.</p>
                    <div class="alert alert-info py-2" style="font-size: 0.9em;">
                        Includes schema and data for all tables.
                    </div>
                </div>
                <div class="card-footer">
                    <a href="/admin/backup/download" class="btn btn-primary w-100">
                        <i class="bi bi-download"></i> Download .SQL
                    </a>
                </div>
            </div>
        </div>

        <!-- Bulk Price Updater -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">💰 Bulk Price Updater</h5>
                </div>
                <div class="card-body">
                    <form action="/admin/data/prices" method="POST" onsubmit="return confirm('Are you sure? This will affect multiple products.')">
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="all">All Categories</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Action</label>
                            <div class="input-group">
                                <select name="type" class="form-select" style="max-width: 120px;">
                                    <option value="increase">Increase</option>
                                    <option value="decrease">Decrease</option>
                                </select>
                                <span class="input-group-text">by</span>
                                <input type="number" name="percentage" class="form-control" placeholder="10" min="1" max="100" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">Apply Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Inventory Reset -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">⚠️ Inventory Reset</h5>
                </div>
                <div class="card-body">
                    <p class="text-danger small"><strong>WARNING:</strong> This will set stock quantity to 0 for all selected items. This action cannot be undone.</p>
                    <form action="/admin/data/reset" method="POST" onsubmit="return confirm('DANGER: Are you absolutely sure you want to zero out stock?')">
                        <div class="mb-3">
                            <label class="form-label">Target Branch</label>
                            <select name="branch_id" class="form-select" required>
                                <option value="" disabled selected>Select Branch</option>
                                <?php foreach($branches as $branch): ?>
                                    <option value="<?= $branch['id'] ?>"><?= htmlspecialchars($branch['name']) ?></option>
                                <?php endforeach; ?>
                                <option value="all" class="text-danger fw-bold">ALL BRANCHES</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Admin Password Confirmation</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Zero Out Stock</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
