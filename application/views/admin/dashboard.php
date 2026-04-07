<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('admin/partials/header', array('page_title' => $page_title, 'active_nav' => $active_nav)); ?>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card page-card stat-card bg-primary text-white">
            <div class="card-body">
                <p class="text-uppercase small mb-2">Total Users</p>
                <h2 class="display-6 mb-0"><?php echo (int) $stats['users']; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card page-card stat-card bg-success text-white">
            <div class="card-body">
                <p class="text-uppercase small mb-2">Total Products</p>
                <h2 class="display-6 mb-0"><?php echo (int) $stats['products']; ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h1 class="h3 mb-1">Admin Dashboard</h1>
                        <p class="text-muted mb-0">Manage portal users and products from one place.</p>
                    </div>
                    <a class="btn btn-primary" href="<?php echo site_url('admin/products/create'); ?>">Add Product</a>
                </div>

                <h2 class="h5 mt-4">Recent Products</h2>

                <?php if (empty($latest_products)): ?>
                    <p class="text-muted mb-0">No products have been created yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                     <tr>
                                         <th>Name</th>
                                         <th>Price</th>
                                         <th>Stock</th>
                                         <th>Created</th>
                                     </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($latest_products as $product): ?>
                                     <tr>
                                         <td><?php echo html_escape($product['name']); ?></td>
                                         <td>$<?php echo number_format((float) $product['price'], 2); ?></td>
                                         <td><?php echo (int) $product['stock']; ?></td>
                                         <td><?php echo html_escape($product['created_at']); ?></td>
                                     </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card page-card">
            <div class="card-body">
                <h2 class="h5">Quick Actions</h2>
                <div class="d-grid gap-2 mt-3">
                    <a class="btn btn-outline-primary" href="<?php echo site_url('admin/users/create'); ?>">Create User</a>
                    <a class="btn btn-outline-success" href="<?php echo site_url('admin/products'); ?>">Manage Products</a>
                    <a class="btn btn-outline-secondary" href="<?php echo site_url('user/dashboard'); ?>">Open User Area</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/partials/footer'); ?>
