<div class="container" style="max-width: 600px;">
    <h1>Create New Workflow</h1>
    
    <div class="card mt-4">
        <div class="card-body">
            <form action="/admin/workflows/store" method="POST">
                <div class="mb-3">
                    <label class="form-label">Workflow Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g., Welcome New Employee" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Trigger Event</label>
                    <select name="trigger_event" class="form-select" required>
                        <option value="user_created">User Created</option>
                        <option value="low_stock">Low Stock Detected</option>
                        <option value="invoice_generated">Invoice Generated</option>
                        <option value="shift_started">Shift Started</option>
                    </select>
                    <div class="form-text">When should this workflow run?</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="/admin/workflows" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create & Configure Actions</button>
                </div>
            </form>
        </div>
    </div>
</div>
