<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('user/partials/header', array('page_title' => $page_title)); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4 p-md-5">
                <div class="mb-4">
                    <span class="badge text-bg-success mb-3">Stripe Checkout</span>
                    <h1 class="h2 mb-2">Payment confirmation</h1>
                    <?php if ($payment_status === 'paid'): ?>
                        <p class="text-muted mb-0">Your payment was verified and the order has been marked as paid.</p>
                    <?php else: ?>
                        <p class="text-muted mb-0">Stripe returned you successfully, but final payment confirmation is still pending. The webhook will update the order securely.</p>
                    <?php endif; ?>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table">
                        <tbody>
                            <tr><th scope="row">Order ID</th><td><?php echo (int) $order['id']; ?></td></tr>
                            <tr><th scope="row">Items</th><td><?php echo html_escape($order['product_name']); ?></td></tr>
                            <tr><th scope="row">Order Status</th><td><?php echo html_escape(ucfirst($order['status'])); ?></td></tr>
                            <tr><th scope="row">Amount</th><td>$<?php echo number_format((float) $order['total_amount'], 2); ?></td></tr>
                            <?php if ($payment): ?>
                                <tr><th scope="row">Stripe Session</th><td><?php echo html_escape($payment['stripe_session_id']); ?></td></tr>
                                <tr><th scope="row">Payment Status</th><td><?php echo html_escape($payment['payment_status']); ?></td></tr>
                            <?php endif; ?>
                            <?php if ($invoice): ?>
                                <tr><th scope="row">Invoice</th><td><a href="<?php echo site_url('user/invoices/view/'.(int) $invoice['id']); ?>"><?php echo html_escape($invoice['invoice_number']); ?></a></td></tr>
                            <?php endif; ?>
                            <?php if ($receipt): ?>
                                <tr><th scope="row">Receipt</th><td><a href="<?php echo site_url('user/receipts/view/'.(int) $receipt['id']); ?>"><?php echo html_escape($receipt['receipt_number']); ?></a></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ( ! empty($order_items)): ?>
                    <div class="table-responsive mb-4">
                        <table class="table">
                            <thead>
                                <tr><th>Product</th><th>Quantity</th><th>Unit Price</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td><?php echo html_escape(isset($item['product_name']) ? $item['product_name'] : $order['product_name']); ?></td>
                                        <td><?php echo (int) $item['quantity']; ?></td>
                                        <td>$<?php echo number_format((float) $item['price'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-primary" href="<?php echo site_url('products'); ?>">Continue Shopping</a>
                    <a class="btn btn-outline-primary" href="<?php echo site_url('cart'); ?>">View Cart</a>
                    <a class="btn btn-outline-primary" href="<?php echo site_url('user/invoices'); ?>">My Invoices</a>
                    <a class="btn btn-outline-secondary" href="<?php echo site_url('user/dashboard'); ?>">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('user/partials/footer'); ?>
