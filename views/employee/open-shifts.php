<div class="container-fluid">
    <h1>🔄 Available Shifts</h1>
    <p style="color: #8b949e;">Claim unclaimed shifts to earn extra hours</p>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>
    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="row mt-4">
        <?php foreach($shifts as $shift): ?>
        <div class="col-md-6 mb-3">
            <div class="card" style="border-left: 3px solid var(--accent-color);">
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <h3 style="margin: 0; color: var(--accent-color);">
                                <?= date('M d, Y', strtotime($shift['start_time'])) ?>
                            </h3>
                            <p style="font-size: 1.2rem; margin: 0.5rem 0;">
                                <?= date('H:i', strtotime($shift['start_time'])) ?> - 
                                <?= date('H:i', strtotime($shift['end_time'])) ?>
                            </p>
                            <span class="badge" style="background: var(--accent-dim); color: var(--accent-color);">
                                <?= ucfirst($shift['type']) ?> Shift
                            </span>
                            <?php if($shift['notes']): ?>
                                <p style="margin-top: 1rem; color: #8b949e;">
                                    <?= htmlspecialchars($shift['notes']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <a href="/employee/open-shifts/claim/<?= $shift['id'] ?>" 
                           class="btn btn-primary"
                           onclick="return confirm('Claim this shift?')">
                            Claim
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if(empty($shifts)): ?>
        <div class="card">
            <div class="card-body text-center" style="padding: 3rem;">
                <h3 style="color: #8b949e;">No shifts available right now</h3>
                <p>Check back later for new opportunities</p>
            </div>
        </div>
    <?php endif; ?>
</div>
