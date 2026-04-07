<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$cart = $this->session->userdata(MY_Controller::CART_SESSION_KEY);
$cart_count = 0;

if (is_array($cart))
{
    foreach ($cart as $cart_item)
    {
        $cart_count += isset($cart_item['quantity']) ? (int) $cart_item['quantity'] : 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo html_escape($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8fafc; }
        .user-brand { font-weight: 700; letter-spacing: 0.02em; }
        .page-card { border: 0; border-radius: 1rem; box-shadow: 0 0.75rem 1.5rem rgba(15, 23, 42, 0.08); }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand user-brand" href="<?php echo site_url('user/dashboard'); ?>">User Portal</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNav" aria-controls="userNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="userNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link <?php echo isset($active_nav) && $active_nav === 'dashboard' ? 'active' : ''; ?>" href="<?php echo site_url('user/dashboard'); ?>">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link <?php echo isset($active_nav) && $active_nav === 'products' ? 'active' : ''; ?>" href="<?php echo site_url('products'); ?>">Products</a></li>
                <li class="nav-item"><a class="nav-link <?php echo isset($active_nav) && $active_nav === 'cart' ? 'active' : ''; ?>" href="<?php echo site_url('cart'); ?>">Cart <span class="badge rounded-pill text-bg-light ms-1"><?php echo (int) $cart_count; ?></span></a></li>
                <li class="nav-item"><a class="nav-link <?php echo isset($active_nav) && $active_nav === 'invoices' ? 'active' : ''; ?>" href="<?php echo site_url('user/invoices'); ?>">Invoices</a></li>
                <li class="nav-item"><a class="nav-link <?php echo isset($active_nav) && $active_nav === 'receipts' ? 'active' : ''; ?>" href="<?php echo site_url('user/receipts'); ?>">Receipts</a></li>
            </ul>
            <div class="d-flex align-items-center gap-3 text-white">
                <span class="small">Signed in as <?php echo html_escape($this->session->userdata('name')); ?></span>
                <a class="btn btn-outline-light btn-sm" href="<?php echo site_url('logout'); ?>">Logout</a>
            </div>
        </div>
    </div>
</nav>
<main class="py-4">
    <div class="container">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo html_escape($this->session->flashdata('success')); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo html_escape($this->session->flashdata('error')); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
