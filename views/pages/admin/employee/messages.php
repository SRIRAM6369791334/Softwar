<h1>Internal Messenger</h1>

<div class="card" style="max-width: 600px; margin-bottom: 20px;">
    <h3>Send New Message</h3>
    <form action="/admin/employee/messages" method="POST">
        <label style="display: block; margin-bottom: 5px;">Title</label>
        <input type="text" name="title" required placeholder="Short subject" style="margin-bottom: 15px;">

        <label style="display: block; margin-bottom: 5px;">Message</label>
        <textarea name="message" rows="4" required style="width: 100%; padding: 10px; background: #0d1117; color: white; border: 1px solid #30363d; border-radius: 6px; margin-bottom: 15px;"></textarea>
        
        <div style="margin-bottom: 15px;">
            <label>
                <input type="checkbox" name="is_urgent" style="width: auto;"> Mark as Urgent 🚨
            </label>
        </div>

        <button type="submit" class="btn btn-primary">Broadcast to All Staff</button>
    </form>
</div>

<h3>Recent Broadcasts</h3>
<?php foreach($history as $msg): ?>
<div class="card" style="margin-bottom: 10px; padding: 15px;">
    <strong><?= htmlspecialchars($msg['title']) ?></strong> <span style="font-size: 0.8rem; color: #8b949e;"><?= date('d M H:i', strtotime($msg['created_at'])) ?></span>
    <div><?= htmlspecialchars($msg['message']) ?></div>
</div>
<?php endforeach; ?>
