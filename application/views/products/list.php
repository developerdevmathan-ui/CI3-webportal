<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('user/partials/header', array('page_title' => $page_title, 'active_nav' => 'products')); ?>

<style>
    .hero { background: linear-gradient(135deg, #0f766e, #2563eb); color: #fff; border-radius: 1rem; }
    .product-card { border: 0; border-radius: 1rem; box-shadow: 0 0.75rem 1.5rem rgba(15, 23, 42, 0.08); }
</style>

<section class="hero p-4 p-md-5 mb-4">
    <h1 class="display-6 mb-2">Available Products</h1>
    <p class="mb-0">Browse products, review stock availability, and add items to your cart before checkout.</p>
</section>

<div class="row g-4">
    <?php if (empty($products)): ?>
        <div class="col-12">
            <div class="alert alert-info mb-0">No products are available yet.</div>
        </div>
    <?php endif; ?>

    <?php foreach ($products as $product): ?>
        <div class="col-md-6 col-xl-4">
            <div class="card product-card h-100">
                <div class="card-body d-flex flex-column">
                    <h2 class="h4"><?php echo html_escape($product['name']); ?></h2>
                    <p class="text-muted flex-grow-1"><?php echo nl2br(html_escape($product['description'])); ?></p>
                    <div class="small text-muted mb-3">Available stock: <strong><?php echo (int) $product['stock']; ?></strong></div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <strong class="fs-5">$<?php echo number_format((float) $product['price'], 2); ?></strong>
                        <?php echo form_open('cart/add/'.(int) $product['id'], array('class' => 'd-flex gap-2 align-items-center')); ?>
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary" <?php echo (int) $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                                <?php echo (int) $product['stock'] <= 0 ? 'Out of Stock' : 'Add to Cart'; ?>
                            </button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php $this->load->view('user/partials/footer'); ?>
