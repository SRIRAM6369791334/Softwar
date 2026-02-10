<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h6 class="text-muted text-uppercase mb-1">Workflow Configuration</h6>
            <h1><?= htmlspecialchars($workflow['name']) ?></h1>
            <span class="badge bg-primary">Trigger: <?= $workflow['trigger_event'] ?></span>
        </div>
        <a href="/admin/workflows" class="btn btn-secondary">Back to List</a>
    </div>

    <div class="row">
        <!-- Actions List -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Workflow Actions</h5>
                </div>
                <div class="card-body p-0">
                    <?php if(empty($actions)): ?>
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-diagram-2 display-4"></i>
                            <p class="mt-2">No actions configured yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach($actions as $index => $action): 
                                $payload = json_decode($action['action_payload'], true);
                            ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-secondary rounded-circle me-3" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                        <?= $index + 1 ?>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-capitalize"><?= str_replace('_', ' ', $action['action_type']) ?></h6>
                                        <small class="text-muted">
                                            <?php if($action['action_type'] == 'send_email'): ?>
                                                Template: <?= $payload['template_key'] ?? 'N/A' ?> | To: <?= $payload['recipient_field'] ?? 'N/A' ?>
                                            <?php elseif($action['action_type'] == 'create_notification'): ?>
                                                Title: <?= $payload['title'] ?? 'N/A' ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                                <a href="/admin/workflows/actions/delete/<?= $action['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remove this action?')">×</a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Add Action Sidebar -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">+ Add Action</h5>
                </div>
                <div class="card-body">
                    <form action="/admin/workflows/actions/add/<?= $workflow['id'] ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Action Type</label>
                            <select name="action_type" class="form-select" id="actionType" onchange="toggleFields()">
                                <option value="send_email">Send Email</option>
                                <option value="create_notification">Create Notification</option>
                            </select>
                        </div>

                        <!-- Email Fields -->
                        <div id="emailFields">
                            <div class="mb-3">
                                <label class="form-label">Template Key</label>
                                <input type="text" name="template_key" class="form-control" placeholder="e.g. email_welcome">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Recipient Field</label>
                                <input type="text" name="recipient_field" class="form-control" placeholder="e.g. email" value="email">
                                <div class="form-text">Field name from trigger context</div>
                            </div>
                        </div>

                        <!-- Notification Fields -->
                        <div id="notiFields" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" placeholder="Alert Title">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea name="message" class="form-control" rows="2" placeholder="Message content..."></textarea>
                                <div class="form-text">Use {name} for dynamic values</div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Add Action</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFields() {
    const type = document.getElementById('actionType').value;
    document.getElementById('emailFields').style.display = type === 'send_email' ? 'block' : 'none';
    document.getElementById('notiFields').style.display = type === 'create_notification' ? 'block' : 'none';
}
</script>
