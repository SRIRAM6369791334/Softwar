<div class="container" style="max-width: 600px;">
    <h1>Post Open Shift</h1>
    
    <div class="card mt-4">
        <div class="card-body">
            <form action="/admin/employee/open-shifts/store" method="POST">
                <div class="form-group">
                    <label>Start Time</label>
                    <input type="datetime-local" name="start_time" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>End Time</label>
                    <input type="datetime-local" name="end_time" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Shift Type</label>
                    <select name="type" class="form-control">
                        <option value="morning">Morning</option>
                        <option value="afternoon">Afternoon</option>
                        <option value="night">Night</option>
                        <option value="general">General</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Notes (Optional)</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Additional details..."></textarea>
                </div>

                <div style="display: flex; justify-content: end; gap: 1rem; margin-top: 2rem;">
                    <a href="/admin/employee/open-shifts" class="btn btn-mode">Cancel</a>
                    <button type="submit" class="btn btn-primary">Post Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>
