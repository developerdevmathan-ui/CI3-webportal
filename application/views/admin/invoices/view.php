<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('admin/partials/header', array('page_title' => $page_title, 'active_nav' => $active_nav)); ?>

<div class="card page-card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Invoice Details</h1>
                <p class="text-muted mb-0">Admin view of a generated invoice.</p>
            </div>
            <a class="btn btn-outline-secondary" href="<?php echo site_url('admin/invoices'); ?>">Back</a>
        </div>

        <table class="table mb-0">
            <tbody>
                <tr><th>Invoice Number</th><td><?php echo html_escape($invoice['invoice_number']); ?></td></tr>
                <tr><th>Order ID</th><td><?php echo (int) $invoice['order_id']; ?></td></tr>
                <tr><th>User</th><td><?php echo html_escape($invoice['user_name']); ?> (<?php echo html_escape($invoice['user_email']); ?>)</td></tr>
                <tr><th>Items</th><td><?php echo html_escape($invoice['product_name']); ?></td></tr>
                <tr><th>Amount</th><td>$<?php echo number_format((float) $invoice['amount'], 2); ?></td></tr>
                <tr><th>Order Status</th><td><?php echo html_escape(ucfirst($invoice['order_status'])); ?></td></tr>
                <tr><th>Issued At</th><td><?php echo html_escape($invoice['created_at']); ?></td></tr>
            </tbody>
        </table>
    </div>
</div>

<?php $this->load->view('admin/partials/footer'); ?>
