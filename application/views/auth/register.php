<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo html_escape($page_title); ?></title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: linear-gradient(135deg, #eff6ff, #f8fafc); color: #0f172a; }
        .shell { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { width: 100%; max-width: 460px; background: #ffffff; border-radius: 18px; box-shadow: 0 20px 45px rgba(15, 23, 42, 0.12); padding: 32px; }
        .eyebrow { color: #0f766e; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin: 0 0 12px; }
        h1 { margin: 0 0 10px; font-size: 28px; }
        p { margin: 0 0 20px; color: #475569; line-height: 1.5; }
        label { display: block; margin: 16px 0 6px; font-weight: 600; }
        input { width: 100%; box-sizing: border-box; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 15px; }
        input:focus { outline: none; border-color: #0f766e; box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.12); }
        button { width: 100%; margin-top: 20px; border: none; border-radius: 10px; background: #0f766e; color: #ffffff; padding: 13px 16px; font-size: 15px; font-weight: 700; cursor: pointer; }
        button:hover { background: #115e59; }
        .notice { border-radius: 10px; padding: 12px 14px; margin: 14px 0; font-size: 14px; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .field-error { color: #b91c1c; font-size: 13px; margin-top: 6px; }
        .links { margin-top: 20px; display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
        .links a { color: #0f766e; text-decoration: none; font-weight: 600; }
        @media (max-width: 480px) {
            .card { padding: 24px; }
            .links { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="card">
            <h1>Create your account</h1>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="notice"><?php echo html_escape($this->session->flashdata('error')); ?></div>
            <?php endif; ?>

            <?php echo form_open('register'); ?>
                <label for="name">Full name</label>
                <input id="name" type="text" name="name" value="<?php echo set_value('name'); ?>" placeholder="John Doe" required>
                <?php echo form_error('name'); ?>

                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="<?php echo set_value('email'); ?>" placeholder="name@example.com" required>
                <?php echo form_error('email'); ?>

                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="Minimum 8 characters" required>
                <?php echo form_error('password'); ?>

                <label for="password_confirm">Confirm password</label>
                <input id="password_confirm" type="password" name="password_confirm" placeholder="Retype your password" required>
                <?php echo form_error('password_confirm'); ?>

                <button type="submit">Create Account</button>
            <?php echo form_close(); ?>

            <div class="links">
                <a href="<?php echo site_url('login'); ?>">User login</a>
                <a href="<?php echo site_url('admin/login'); ?>">Admin login</a>
            </div>
        </div>
    </div>
</body>
</html>
