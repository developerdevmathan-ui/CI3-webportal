<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('admin/partials/header', array('page_title' => $page_title, 'active_nav' => $active_nav)); ?>

<div class="card page-card">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Products</h1>
                <p class="text-muted mb-0">Create, edit, and remove products available in the portal.</p>
            </div>
            <a class="btn btn-primary" href="<?php echo site_url('admin/products/create'); ?>">Create Product</a>
        </div>

        <?php if (empty($products)): ?>
            <div class="alert alert-info mb-0">No products found. Add your first product to get started.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo (int) $product['id']; ?></td>
                                <td><?php echo html_escape($product['name']); ?></td>
                                <td><?php echo html_escape($product['description']); ?></td>
                                <td>$<?php echo number_format((float) $product['price'], 2); ?></td>
                                <td><?php echo (int) $product['stock']; ?></td>
                                <td><?php echo html_escape($product['created_at']); ?></td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a class="btn btn-sm btn-outline-primary" href="<?php echo site_url('admin/products/edit/'.(int) $product['id']); ?>">Edit</a>
                                        <?php echo form_open('admin/products/delete/'.(int) $product['id'], array('class' => 'd-inline')); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this product?');">Delete</button>
                                        <?php echo form_close(); ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $this->load->view('admin/partials/footer'); ?>
