<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo html_escape($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Invoice Details</h1>
            <p class="text-muted mb-0">Review the invoice generated for your completed order.</p>
        </div>
        <a class="btn btn-outline-secondary" href="<?php echo site_url('user/invoices'); ?>">Back</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <table class="table mb-0">
                <tbody>
                    <tr><th>Invoice Number</th><td><?php echo html_escape($invoice['invoice_number']); ?></td></tr>
                    <tr><th>Order ID</th><td><?php echo (int) $invoice['order_id']; ?></td></tr>
                    <tr><th>Items</th><td><?php echo html_escape($invoice['product_name']); ?></td></tr>
                    <tr><th>Amount</th><td>$<?php echo number_format((float) $invoice['amount'], 2); ?></td></tr>
                    <tr><th>Order Status</th><td><?php echo html_escape(ucfirst($invoice['order_status'])); ?></td></tr>
                    <tr><th>Issued At</th><td><?php echo html_escape($invoice['created_at']); ?></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>
