<div class="container-fluid">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>⚡ Automation Workflows</h1>
        <a href="/admin/workflows/create" class="btn btn-primary">+ New Workflow</a>
    </div>

    <div class="row">
        <?php foreach($workflows as $wf): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100" style="border-left: 4px solid var(--accent-color);">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($wf['name']) ?></h5>
                    <div class="badge bg-secondary mb-2">Trigger: <?= htmlspecialchars($wf['trigger_event']) ?></div>
                    <p class="card-text text-muted"><?= htmlspecialchars($wf['description']) ?></p>
                    
                    <div style="margin-top: 1rem;">
                        <span class="badge <?= $wf['is_active'] ? 'bg-success' : 'bg-danger' ?>">
                            <?= $wf['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="/admin/workflows/edit/<?= $wf['id'] ?>" class="btn btn-primary w-100">Configure Actions</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if(empty($workflows)): ?>
        <div class="col-12 text-center text-muted py-5">
            <p>No workflows defined yet.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
