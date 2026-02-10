<div class="container" style="max-width: 500px;">
    <h1>Edit Tax Group</h1>
    
    <div class="card mt-4">
        <div class="card-body">
            <form action="/admin/taxes/update/<?= $tax['id'] ?>" method="POST">
                <div class="mb-3">
                    <label class="form-label">Group Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($tax['name']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Tax Rate (%)</label>
                    <input type="number" step="0.01" name="rate" class="form-control" value="<?= $tax['rate'] ?>" required>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="/admin/taxes" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Tax Group</button>
                </div>
            </form>
        </div>
    </div>
</div>
