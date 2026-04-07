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
            <h1 class="h2 mb-1">My Receipts</h1>
            <p class="text-muted mb-0">Receipts generated for successful payments.</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="<?php echo site_url('user/invoices'); ?>">My Invoices</a>
            <a class="btn btn-outline-secondary" href="<?php echo site_url('user/dashboard'); ?>">Dashboard</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <?php if (empty($receipts)): ?>
                <div class="alert alert-info mb-0">No receipts are available yet.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Receipt #</th>
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
                                    <td><?php echo html_escape($receipt['product_name']); ?></td>
                                    <td>$<?php echo number_format((float) $receipt['amount'], 2); ?></td>
                                    <td><?php echo html_escape($receipt['created_at']); ?></td>
                                    <td class="text-end"><a class="btn btn-sm btn-primary" href="<?php echo site_url('user/receipts/view/'.(int) $receipt['id']); ?>">View</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
</body>
</html>
