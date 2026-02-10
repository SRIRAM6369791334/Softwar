<h1>Inbox</h1>

<?php foreach($messages as $msg): ?>
<div class="card" style="border-left: 4px solid <?= $msg['is_urgent'] ? '#da3633' : '#238636' ?>;">
    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
        <strong style="font-size: 1.1rem;"><?= htmlspecialchars($msg['title']) ?></strong>
        <span style="color: #8b949e; font-size: 0.8rem;"><?= date('M d, h:i A', strtotime($msg['created_at'])) ?></span>
    </div>
    <div style="color: #c9d1d9; margin-bottom: 10px;">
        <?= nl2br(htmlspecialchars($msg['message'])) ?>
    </div>
    <div style="font-size: 0.8rem; color: #8b949e;">
        From: <?= htmlspecialchars($msg['sender']) ?>
    </div>
</div>
<?php endforeach; ?>

<?php if(empty($messages)): ?>
    <div style="text-align: center; color: #8b949e; margin-top: 50px;">No messages</div>
<?php endif; ?>
