<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('user/partials/header', array('page_title' => $page_title, 'active_nav' => 'cart')); ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-1">Checkout</h1>
                        <p class="text-muted mb-0">Confirm your cart and continue to Stripe Checkout.</p>
                    </div>
                    <a class="btn btn-outline-secondary" href="<?php echo site_url('cart'); ?>">Back to Cart</a>
                </div>

                <?php echo form_open('cart/update'); ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Unit Price</th>
                                    <th>Available</th>
                                    <th style="width: 160px;">Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): ?>
                                    <tr>
                                        <td><?php echo html_escape($item['name']); ?></td>
                                        <td>$<?php echo number_format((float) $item['price'], 2); ?></td>
                                        <td><?php echo (int) $item['stock']; ?></td>
                                        <td>
                                            <input class="form-control" type="number" name="quantities[<?php echo (int) $item['product_id']; ?>]" min="0" max="<?php echo (int) $item['stock']; ?>" value="<?php echo (int) $item['quantity']; ?>">
                                        </td>
                                        <td>$<?php echo number_format((float) $item['item_total'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-outline-primary">Update Quantities</button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card page-card">
            <div class="card-body">
                <h2 class="h5 mb-3">Order Summary</h2>
                <div class="d-flex justify-content-between mb-2">
                    <span>Items</span>
                    <strong><?php echo count($cart_items); ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span>Total</span>
                    <strong>$<?php echo number_format((float) $cart_total, 2); ?></strong>
                </div>
                <?php echo form_open('cart/checkout'); ?>
                    <button type="submit" class="btn btn-primary w-100">Pay with Stripe</button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('user/partials/footer'); ?>
