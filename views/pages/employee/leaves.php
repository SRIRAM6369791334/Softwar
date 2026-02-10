<h1>My Leaves</h1>

<div class="card">
    <h3>Request Leave</h3>
    <form action="/employee/leaves/request" method="POST">
        <label style="display: block; margin-bottom: 5px;">Leave Type</label>
        <select name="type" required style="width: 100%; padding: 10px; margin-bottom: 15px; background: #0d1117; color: white; border: 1px solid #30363d; border-radius: 6px;">
            <option value="sick">Sick Leave</option>
            <option value="casual">Casual Leave</option>
            <option value="earned">Earned Leave</option>
            <option value="unpaid">Unpaid Leave</option>
        </select>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <div>
                <label style="display: block; margin-bottom: 5px;">Start Date</label>
                <input type="date" name="start_date" required style="width: 100%; padding: 10px; margin-bottom: 15px; background: #0d1117; color: white; border: 1px solid #30363d; border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px;">End Date</label>
                <input type="date" name="end_date" required style="width: 100%; padding: 10px; margin-bottom: 15px; background: #0d1117; color: white; border: 1px solid #30363d; border-radius: 6px;">
            </div>
        </div>

        <label style="display: block; margin-bottom: 5px;">Reason</label>
        <textarea name="reason" required rows="3" style="width: 100%; padding: 10px; margin-bottom: 15px; background: #0d1117; color: white; border: 1px solid #30363d; border-radius: 6px;"></textarea>

        <button type="submit" class="btn">Submit Request</button>
    </form>
</div>

<h3>History</h3>
<?php foreach($leaves as $leave): ?>
<div class="card">
    <div style="display: flex; justify-content: space-between;">
        <div>
            <strong><?= ucfirst($leave['type']) ?> Leave</strong><br>
            <span style="color: #8b949e; font-size: 0.9rem;">
                <?= date('M d', strtotime($leave['start_date'])) ?> - <?= date('M d', strtotime($leave['end_date'])) ?>
            </span>
        </div>
        <div>
            <span style="padding: 5px 10px; border-radius: 12px; font-size: 0.8rem; background: 
                <?= $leave['status'] == 'approved' ? '#238636' : ($leave['status'] == 'rejected' ? '#da3633' : '#9e6a03') ?>;">
                <?= ucfirst($leave['status']) ?>
            </span>
        </div>
    </div>
</div>
<?php endforeach; ?>
