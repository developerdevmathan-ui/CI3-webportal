<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('user/partials/header', array('page_title' => $page_title)); ?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4 p-md-5">
                <span class="badge text-bg-warning mb-3">Checkout Cancelled</span>
                <h1 class="h2 mb-2">Payment was not completed</h1>
                <p class="text-muted">No money was captured. You can review the cart and try again whenever you are ready.</p>

                <?php if ($order): ?>
                    <div class="alert alert-secondary">
                        Order #<?php echo (int) $order['id']; ?> is currently marked as <?php echo html_escape($order['status']); ?>.
                    </div>
                <?php endif; ?>

                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-primary" href="<?php echo site_url('cart'); ?>">Back to Cart</a>
                    <a class="btn btn-outline-primary" href="<?php echo site_url('products'); ?>">Browse Products</a>
                    <a class="btn btn-outline-secondary" href="<?php echo site_url('user/dashboard'); ?>">User Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('user/partials/footer'); ?>
