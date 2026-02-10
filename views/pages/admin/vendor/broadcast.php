<div class="admin-broadcast">
    <h1>Supplier Broadcast</h1>
    <p style="color: var(--text-dim); margin-bottom: 2rem;">Send important updates (holiday closures, stock requests, policy changes) to all vendors.</p>

    <div class="card" style="max-width: 600px;">
        <form action="/admin/vendor/broadcast" method="POST">
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label>Notification Title</label>
                <input type="text" name="title" required style="width: 100%; padding: 10px; background: var(--bg-color); border: 1px solid var(--border); color: #fff;">
            </div>
            <div class="form-group" style="margin-bottom: 2rem;">
                <label>Message Content</label>
                <textarea name="message" rows="6" required style="width: 100%; padding: 10px; background: var(--bg-color); border: 1px solid var(--border); color: #fff;"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Post to Vendor Dashboards</button>
        </form>
    </div>
</div>
