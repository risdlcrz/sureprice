<?php
$employees = [
    ['status' => 'Pending', 'document_number' => 'DOC001', 'justification' => 'Office supplies needed', 'client' => 'Acme Corp', 'date' => '2025-04-01', 'estimated_amount' => '$2,000'],
    ['status' => 'Approved', 'document_number' => 'DOC002', 'justification' => 'Warehouse expansion', 'client' => 'Beta Ltd', 'date' => '2025-04-03', 'estimated_amount' => '$50,000'],
    ['status' => 'Rejected', 'document_number' => 'DOC003', 'justification' => 'Software upgrade', 'client' => 'Delta Inc', 'date' => '2025-04-05', 'estimated_amount' => '$15,000'],
    ['status' => 'Pending', 'document_number' => 'DOC004', 'justification' => 'Client meeting expenses', 'client' => 'Gamma LLC', 'date' => '2025-04-10', 'estimated_amount' => '$3,500'],
];

$filter = $_GET['status'] ?? 'All';
$filtered_employees = ($filter === 'All') ? $employees : array_filter($employees, fn($e) => $e['status'] === $filter);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document Approval</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./Styles/projectapp.css">

</head>
<body>

    <div class="sidebar">
        <?php include './INCLUDE/header_project.php'; ?>
    </div>

    <div class="content">
        <h1 class="text-center my-4">Project Approval Management</h1>

        <div class="top-controls">
            <div class="filter-buttons">
                <a href="?status=All"><i class="fas fa-th-list"></i> All</a>
                <a href="?status=Pending"><i class="fas fa-hourglass-half"></i> Pending</a>
                <a href="?status=Approved"><i class="fas fa-check-circle"></i> Approved</a>
                <a href="?status=Rejected"><i class="fas fa-times-circle"></i> Rejected</a>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Document Number</th>
                    <th>Justification</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Estimated Amount</th>
                    <th>Actions</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filtered_employees as $doc): ?>
                    <tr>
                        <td><?= htmlspecialchars($doc['document_number']) ?></td>
                        <td><?= htmlspecialchars($doc['justification']) ?></td>
                        <td><?= htmlspecialchars($doc['client']) ?></td>
                        <td><?= htmlspecialchars($doc['date']) ?></td>
                        <td><?= htmlspecialchars($doc['estimated_amount']) ?></td>
                        <td>
                            <?php if ($doc['status'] === 'Pending'): ?>
                                <button class="action-btn approve-btn"><i class="fas fa-check"></i> Approve</button>
                                <button class="action-btn reject-btn"><i class="fas fa-times"></i> Reject</button>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($doc['status'] === 'Pending'): ?>
                                <span class="badge badge-pending"><i class="fas fa-hourglass-half"></i> Pending</span>
                            <?php elseif ($doc['status'] === 'Approved'): ?>
                                <span class="badge badge-approved"><i class="fas fa-check-circle"></i> Approved</span>
                            <?php elseif ($doc['status'] === 'Rejected'): ?>
                                <span class="badge badge-rejected"><i class="fas fa-times-circle"></i> Rejected</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
