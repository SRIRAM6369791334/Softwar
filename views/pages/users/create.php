<div class="container">
    <h1>Create New Employee</h1>
    <form action="/users/store" method="POST">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Contact Email (for notifications)</label>
                <input type="email" name="email" class="form-control">
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Role</label>
                <select name="role_id" class="form-select">
                    <?php foreach($roles as $role): ?>
                        <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label">Assian to Branch</label>
                <select name="branch_id" class="form-select">
                    <?php foreach($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>"><?= $branch['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Create User</button>
    </form>
</div>
