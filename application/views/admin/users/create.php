<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('admin/partials/header', array('page_title' => $page_title, 'active_nav' => $active_nav)); ?>

<div class="card page-card">
    <div class="card-body">
        <div class="mb-4">
            <h1 class="h3 mb-1">Create User</h1>
            <p class="text-muted mb-0">Administrators can provision both admin and user accounts from this panel.</p>
        </div>

        <?php echo form_open('admin/users/create'); ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="name">Full Name</label>
                    <input class="form-control" id="name" type="text" name="name" value="<?php echo set_value('name'); ?>" maxlength="100" required>
                    <?php echo form_error('name'); ?>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="role">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="">Select role</option>
                        <?php foreach ($roles as $value => $label): ?>
                            <option value="<?php echo html_escape($value); ?>" <?php echo set_select('role', $value); ?>><?php echo html_escape($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('role'); ?>
                </div>

                <div class="col-12">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control" id="email" type="email" name="email" value="<?php echo set_value('email'); ?>" maxlength="255" required>
                    <?php echo form_error('email'); ?>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-control" id="password" type="password" name="password" required>
                    <?php echo form_error('password'); ?>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="password_confirm">Confirm Password</label>
                    <input class="form-control" id="password_confirm" type="password" name="password_confirm" required>
                    <?php echo form_error('password_confirm'); ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-4">Create User</button>
        <?php echo form_close(); ?>
    </div>
</div>

<?php $this->load->view('admin/partials/footer'); ?>
