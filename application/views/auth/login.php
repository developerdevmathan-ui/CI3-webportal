<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo html_escape($page_title); ?></title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: linear-gradient(135deg, #f8fafc, #dbeafe); color: #0f172a; }
        .shell { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { width: 100%; max-width: 420px; background: #ffffff; border-radius: 18px; box-shadow: 0 20px 45px rgba(15, 23, 42, 0.12); padding: 32px; }
        .eyebrow { color: #2563eb; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin: 0 0 12px; }
        h1 { margin: 0 0 10px; font-size: 28px; }
        p { margin: 0 0 20px; color: #475569; line-height: 1.5; }
        label { display: block; margin: 16px 0 6px; font-weight: 600; }
        input { width: 100%; box-sizing: border-box; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 15px; }
        input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12); }
        button { width: 100%; margin-top: 20px; border: none; border-radius: 10px; background: #2563eb; color: #ffffff; padding: 13px 16px; font-size: 15px; font-weight: 700; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        .notice, .validation-box { border-radius: 10px; padding: 12px 14px; margin: 14px 0; font-size: 14px; }
        .notice.error, .validation-box { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .notice.success { background: #ecfdf5; color: #166534; border: 1px solid #bbf7d0; }
        .field-error { color: #b91c1c; font-size: 13px; margin-top: 6px; }
        .links { margin-top: 20px; display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
        .links a { color: #2563eb; text-decoration: none; font-weight: 600; }
        @media (max-width: 480px) {
            .card { padding: 24px; }
            .links { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="card">
            <h1><?php echo html_escape($heading); ?></h1>
            <p><?php echo html_escape($description); ?></p>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="notice error"><?php echo html_escape($this->session->flashdata('error')); ?></div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="notice success"><?php echo html_escape($this->session->flashdata('success')); ?></div>
            <?php endif; ?>

            <?php if (validation_errors()): ?>
                <div class="validation-box">Please correct the highlighted fields and try again.</div>
            <?php endif; ?>

            <?php echo form_open($login_action); ?>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="<?php echo set_value('email'); ?>" placeholder="name@example.com" required>
                <?php echo form_error('email'); ?>

                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="Enter your password" required>
                <?php echo form_error('password'); ?>

                <button type="submit"><?php echo html_escape($submit_label); ?></button>
            <?php echo form_close(); ?>

            <div class="links">
                <a href="<?php echo html_escape($alternate_url); ?>"><?php echo html_escape($alternate_label); ?></a>
                <?php if ($show_register_link): ?>
                    <a href="<?php echo site_url('register'); ?>">Create an account</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
