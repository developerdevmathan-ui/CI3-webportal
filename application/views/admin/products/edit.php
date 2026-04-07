<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('admin/partials/header', array('page_title' => $page_title, 'active_nav' => $active_nav)); ?>

<div class="card page-card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Edit Product</h1>
                <p class="text-muted mb-0">Update product details while keeping the module clean and maintainable.</p>
            </div>
            <a class="btn btn-outline-secondary" href="<?php echo site_url('admin/products'); ?>">Back to Products</a>
        </div>

        <?php echo form_open('admin/products/edit/'.(int) $product['id']); ?>
            <div class="mb-3">
                <label class="form-label" for="name">Product Name</label>
                <input class="form-control" id="name" type="text" name="name" value="<?php echo set_value('name', $product['name']); ?>" maxlength="150" required>
                <?php echo form_error('name'); ?>
            </div>

            <div class="mb-3">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" maxlength="1000" required><?php echo set_value('description', $product['description']); ?></textarea>
                <?php echo form_error('description'); ?>
            </div>

            <div class="mb-4">
                <label class="form-label" for="price">Price</label>
                <input class="form-control" id="price" type="number" name="price" min="0.01" step="0.01" value="<?php echo set_value('price', number_format((float) $product['price'], 2, '.', '')); ?>" required>
                <?php echo form_error('price'); ?>
            </div>

            <div class="mb-4">
                <label class="form-label" for="stock">Available Stock</label>
                <input class="form-control" id="stock" type="number" name="stock" min="0" step="1" value="<?php echo set_value('stock', (int) $product['stock']); ?>" required>
                <?php echo form_error('stock'); ?>
            </div>

            <button type="submit" class="btn btn-primary">Update Product</button>
        <?php echo form_close(); ?>
    </div>
</div>

<?php $this->load->view('admin/partials/footer'); ?>
