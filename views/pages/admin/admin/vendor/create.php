<h1>Create Vendor</h1>

<div class="card" style="max-width: 600px;">
    <form action="/admin/vendors/store" method="POST">
        <div class="form-group">
            <label>Vendor Name</label>
            <input type="text" name="name" required placeholder="e.g. Global Suppliers Inc.">
        </div>
        
        <div class="form-group">
            <label>Email Address (Login ID)</label>
            <input type="email" name="email" required placeholder="vendor@example.com">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Initial password">
        </div>

        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" placeholder="+91 98765 43210">
        </div>

        <button type="submit" class="btn btn-primary">Create Vendor Account</button>
    </form>
</div>
