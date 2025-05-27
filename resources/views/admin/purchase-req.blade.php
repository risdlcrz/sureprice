<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Requisition Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./Styles/projectreq.css">

</head>
<body>

    <div class="sidebar">
        <?php include './INCLUDE/header_project.php'; ?>
    </div>

    <div class="content">
        <h1 class="text-center my-4">Purchase Requisition Form</h1>

        <form class="form-section">
            <div class="row">
                <div class="col-md-6">
                    <label for="department" class="form-label">Department/Location</label>
                    <input type="text" class="form-control" id="department" name="department">
                </div>
                <div class="col-md-6">
                    <label for="request-date" class="form-label">Date of Request</label>
                    <input type="date" class="form-control" id="request-date" name="request_date">
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <label for="first-name" class="form-label">Requester First Name</label>
                    <input type="text" class="form-control" id="first-name" name="first_name">
                </div>
                <div class="col-md-3">
                    <label for="last-name" class="form-label">Requester Last Name</label>
                    <input type="text" class="form-control" id="last-name" name="last_name">
                </div>
                <div class="col-md-3">
                    <label for="email" class="form-label">Requester Email</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>
                <div class="col-md-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone">
                </div>
            </div>

            <hr>

            <h5>Item Details</h5>
            <table class="table table-bordered" id="materials-table">
                <thead>
                    <tr>
                        <th style="width: 40px;"><button type="button" class="btn btn-secondary btn-sm" onclick="addRow()"><i class="fas fa-plus"></i></button></th>
                        <th>Item Description</th>
                        <th>Quantity</th>
                        <th>Unit of Measure</th>
                        <th>Other Specifications</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td><input type="text" name="item_description[]" class="form-control"></td>
                        <td><input type="number" name="quantity[]" class="form-control"></td>
                        <td><input type="text" name="unit[]" class="form-control"></td>
                        <td><input type="text" name="specs[]" class="form-control"></td>
                    </tr>
                </tbody>
            </table>

            <div class="mb-3">
                <label for="purpose" class="form-label">Purpose of the Request</label>
                <textarea class="form-control" id="purpose" name="purpose" rows="5"></textarea>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="urgency" name="urgency">
                <label class="form-check-label" for="urgency">
                    Mark as urgent
                </label>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit</button>
                <a href="project-dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Menu</a>
            </div>
        </form>
    </div>

    <script src="./Script/projectreq.js"></script>

</body>
</html>
