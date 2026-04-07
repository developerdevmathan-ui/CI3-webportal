<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('user/partials/header', array('page_title' => $page_title, 'active_nav' => 'cart')); ?>

<div class="card page-card">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">My Cart</h1>
                <p class="text-muted mb-0">Review your selected products before proceeding to checkout.</p>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="<?php echo site_url('products'); ?>">Continue Shopping</a>
                <?php if ( ! empty($cart_items)): ?>
                    <a class="btn btn-primary" href="<?php echo site_url('cart/checkout'); ?>">Proceed to Checkout</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($cart_items)): ?>
            <div class="alert alert-info mb-0">Your cart is empty. Add products to begin checkout.</div>
        <?php else: ?>
            <?php echo form_open('cart/update'); ?>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Available Stock</th>
                                <th style="width: 160px;">Quantity</th>
                                <th>Total</th>
                                <th class="text-end">Remove</th>
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
                                    <td class="text-end">
                                        <button type="submit" formaction="<?php echo site_url('cart/remove/'.(int) $item['product_id']); ?>" formmethod="post" class="btn btn-sm btn-outline-danger">Remove</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Grand Total</th>
                                <th>$<?php echo number_format((float) $cart_total, 2); ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <button type="submit" class="btn btn-outline-primary">Update Cart</button>
            <?php echo form_close(); ?>
        <?php endif; ?>
    </div>
</div>

<?php $this->load->view('user/partials/footer'); ?>
