<h1>Shift Roster (<?= date('M d', strtotime($weekStart)) ?> - <?= date('M d', strtotime($weekStart . ' +6 days')) ?>)</h1>

<div class="card" style="margin-bottom: 20px;">
    <strong>Staff Members</strong> (Drag to calendar)
    <div style="display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap;">
        <?php foreach($users as $user): ?>
            <div class="draggable-user" draggable="true" data-id="<?= $user['id'] ?>" data-name="<?= htmlspecialchars($user['full_name']) ?>" 
                 style="padding: 5px 10px; background: #161b22; border: 1px solid #30363d; border-radius: 4px; cursor: grab;">
                <?= htmlspecialchars($user['full_name']) ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="card" style="overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="width: 100px;">Time</th>
                <?php for($i=0; $i<7; $i++): $d = date('Y-m-d', strtotime($weekStart . " +$i days")); ?>
                    <th><?= date('D d/m', strtotime($d)) ?></th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?php 
            $slots = ['09:00', '13:00', '17:00']; 
            foreach($slots as $slot): 
            ?>
            <tr>
                <td style="border: 1px solid #30363d; padding: 10px; color: #8b949e;"><?= $slot ?></td>
                <?php for($i=0; $i<7; $i++): $d = date('Y-m-d', strtotime($weekStart . " +$i days")); ?>
                    <td class="roster-slot" data-date="<?= $d ?>" data-time="<?= $slot ?>" 
                        style="border: 1px solid #30363d; height: 80px; vertical-align: top; padding: 5px;"
                        ondrop="drop(event)" ondragover="allowDrop(event)">
                        
                        <?php foreach($shifts as $shift): 
                            $shiftDate = date('Y-m-d', strtotime($shift['start_time']));
                            $shiftTime = date('H:i', strtotime($shift['start_time']));
                            if($shiftDate == $d && $shiftTime == $slot):
                        ?>
                            <div class="shift-item" style="background: var(--accent-dim); color: var(--accent-color); padding: 4px; font-size: 0.8rem; margin-bottom: 2px; border-radius: 3px; cursor: pointer;">
                                <?= htmlspecialchars($shift['full_name']) ?>
                                <span onclick="deleteShift(<?= $shift['id'] ?>)" style="float: right; color: #da3633;">&times;</span>
                            </div>
                        <?php endif; endforeach; ?>
                    </td>
                <?php endfor; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
}

document.querySelectorAll('.draggable-user').forEach(d => {
    d.addEventListener('dragstart', (e) => {
        e.dataTransfer.setData('userId', d.dataset.id);
        e.dataTransfer.setData('userName', d.dataset.name);
    });
});

function drop(ev) {
    ev.preventDefault();
    if (!ev.target.classList.contains('roster-slot')) return;

    var userId = ev.dataTransfer.getData("userId");
    var userName = ev.dataTransfer.getData("userName");
    
    var date = ev.target.dataset.date;
    var time = ev.target.dataset.time;
    
    // Save to backend
    fetch('/admin/employee/roster/save', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            user_id: userId,
            start_time: date + ' ' + time + ':00',
            end_time: date + ' ' + (parseInt(time.split(':')[0]) + 4) + ':00:00' // +4 hours default
        })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) window.location.reload();
    });
}

function deleteShift(id) {
    if(confirm('Delete this shift?')) {
        fetch('/admin/employee/roster/delete/' + id)
        .then(res => res.json())
        .then(d => { if(d.success) window.location.reload(); });
    }
}
</script>
