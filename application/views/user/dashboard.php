<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('user/partials/header', array('page_title' => 'User Dashboard', 'active_nav' => 'dashboard')); ?>

<div class="card page-card mb-4 text-white" style="background: linear-gradient(135deg, #0f766e, #2563eb);">
    <div class="card-body p-4 p-md-5">
        <h1 class="display-6">Hello, <?php echo html_escape($current_user['name']); ?></h1>
        <p class="mb-0">You can browse stocked products, manage your cart, pay through Stripe Checkout, and access your invoices and receipts after successful payment.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6 col-xl-3">
        <div class="card page-card h-100">
            <div class="card-body">
                <h2 class="h5">Account Details</h2>
                <p class="text-muted mb-0">Email: <?php echo html_escape($current_user['email']); ?><br>Role: <?php echo html_escape($current_user['role']); ?><br>Member since: <?php echo html_escape($current_user['created_at']); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card page-card h-100">
            <div class="card-body">
                <h2 class="h5">Shopping</h2>
                <p class="text-muted">Browse products and add them to your cart before checkout.</p>
                <a href="<?php echo site_url('products'); ?>">Browse products</a><br>
                <a href="<?php echo site_url('cart'); ?>">Open cart</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card page-card h-100">
            <div class="card-body">
                <h2 class="h5">Recent Orders</h2>
                <?php if (empty($recent_orders)): ?>
                    <p class="text-muted mb-0">You have not created any orders yet.</p>
                <?php else: ?>
                    <?php $recent_order = $recent_orders[0]; ?>
                    <p class="text-muted mb-0">Latest order: #<?php echo (int) $recent_order['id']; ?><br>Items: <?php echo html_escape($recent_order['product_name']); ?><br>Status: <?php echo html_escape($recent_order['status']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card page-card h-100">
            <div class="card-body">
                <h2 class="h5">Documents</h2>
                <p class="text-muted">View invoices and receipts generated after successful payments.</p>
                <a href="<?php echo site_url('user/invoices'); ?>">My invoices</a><br>
                <a href="<?php echo site_url('user/receipts'); ?>">My receipts</a>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('user/partials/footer'); ?>
