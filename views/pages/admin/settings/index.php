<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>System Settings</h1>
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success py-1 px-3 mb-0">Settings Saved!</div>
        <?php endif; ?>
    </div>

    <form action="/admin/settings/update" method="POST">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                    <?php 
                    $first = true;
                    foreach($groupedSettings as $group => $settings): 
                    ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= $first ? 'active' : '' ?>" id="<?= $group ?>-tab" data-bs-toggle="tab" data-bs-target="#<?= $group ?>" type="button" role="tab">
                                <?= ucfirst($group) ?>
                            </button>
                        </li>
                    <?php 
                    $first = false;
                    endforeach; 
                    ?>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="settingsTabsContent">
                    <?php 
                    $first = true;
                    foreach($groupedSettings as $group => $settings): 
                    ?>
                        <div class="tab-pane fade <?= $first ? 'show active' : '' ?>" id="<?= $group ?>" role="tabpanel">
                            <div class="row">
                                <?php foreach($settings as $setting): ?>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><?= htmlspecialchars($setting['label']) ?></label>
                                        
                                        <?php if($setting['input_type'] == 'boolean'): ?>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="<?= $setting['setting_key'] ?>" value="1" <?= $setting['setting_value'] == '1' ? 'checked' : '' ?>>
                                                <label class="form-check-label text-muted">Enable/Disable</label>
                                            </div>

                                        <?php elseif($setting['input_type'] == 'color'): ?>
                                            <input type="color" class="form-control form-control-color" name="<?= $setting['setting_key'] ?>" value="<?= htmlspecialchars($setting['setting_value']) ?>" title="Choose your color">

                                        <?php elseif($setting['input_type'] == 'textarea'): ?>
                                            <textarea class="form-control" name="<?= $setting['setting_key'] ?>" rows="3"><?= htmlspecialchars($setting['setting_value']) ?></textarea>

                                        <?php elseif($setting['input_type'] == 'number'): ?>
                                            <input type="number" class="form-control" name="<?= $setting['setting_key'] ?>" value="<?= htmlspecialchars($setting['setting_value']) ?>">

                                        <?php elseif($setting['setting_key'] == 'smtp_pass'): ?>
                                            <input type="password" class="form-control" name="<?= $setting['setting_key'] ?>" value="<?= htmlspecialchars($setting['setting_value']) ?>">

                                        <?php else: ?>
                                            <input type="text" class="form-control" name="<?= $setting['setting_key'] ?>" value="<?= htmlspecialchars($setting['setting_value']) ?>">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php 
                    $first = false;
                    endforeach; 
                    ?>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Save Changes
                </button>
            </div>
        </div>
    </form>
</div>
