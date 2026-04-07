<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('admin/partials/header', array('page_title' => $page_title, 'active_nav' => $active_nav)); ?>

<div class="card page-card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">All Receipts</h1>
                <p class="text-muted mb-0">Receipts created automatically after successful payments.</p>
            </div>
        </div>

        <?php if (empty($receipts)): ?>
            <div class="alert alert-info mb-0">No receipts found.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Receipt #</th>
                            <th>User</th>
                            <th>Items</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($receipts as $receipt): ?>
                            <tr>
                                <td><?php echo html_escape($receipt['receipt_number']); ?></td>
                                <td><?php echo html_escape($receipt['user_name']); ?><br><small class="text-muted"><?php echo html_escape($receipt['user_email']); ?></small></td>
                                <td><?php echo html_escape($receipt['product_name']); ?></td>
                                <td>$<?php echo number_format((float) $receipt['amount'], 2); ?></td>
                                <td><?php echo html_escape($receipt['created_at']); ?></td>
                                <td class="text-end"><a class="btn btn-sm btn-primary" href="<?php echo site_url('admin/receipts/view/'.(int) $receipt['id']); ?>">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $this->load->view('admin/partials/footer'); ?>
