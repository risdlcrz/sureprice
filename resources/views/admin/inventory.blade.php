<?php
// Database connection
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = "";     // Default XAMPP password
$dbname = "inventory_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create table if not exists (safety check)
$createTable = "CREATE TABLE IF NOT EXISTS materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    total_stock INT NOT NULL,
    available_stock INT NOT NULL,
    threshold INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (!$conn->query($createTable)) {
    die("Error creating table: " . $conn->error);
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'create':
                    echo json_encode(createMaterial($conn));
                    break;
                case 'update':
                    echo json_encode(updateMaterial($conn));
                    break;
                case 'delete':
                    echo json_encode(deleteMaterial($conn));
                    break;
                default:
                    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}

// CRUD Functions
function createMaterial($conn) {
    $required = ['name', 'total_stock', 'available_stock'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    $name = $_POST['name'];
    $total_stock = (int)$_POST['total_stock'];
    $available_stock = (int)$_POST['available_stock'];
    $threshold = isset($_POST['threshold']) ? (int)$_POST['threshold'] : 10;
    
    $stmt = $conn->prepare("INSERT INTO materials (name, total_stock, available_stock, threshold) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siii", $name, $total_stock, $available_stock, $threshold);
    
    if ($stmt->execute()) {
        return ['status' => 'success', 'message' => 'Material created successfully', 'id' => $stmt->insert_id];
    } else {
        throw new Exception("Error creating material: " . $stmt->error);
    }
}

function updateMaterial($conn) {
    $required = ['id', 'name', 'total_stock', 'available_stock'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    $id = (int)$_POST['id'];
    $name = $_POST['name'];
    $total_stock = (int)$_POST['total_stock'];
    $available_stock = (int)$_POST['available_stock'];
    $threshold = isset($_POST['threshold']) ? (int)$_POST['threshold'] : 10;
    
    $stmt = $conn->prepare("UPDATE materials SET name=?, total_stock=?, available_stock=?, threshold=? WHERE id=?");
    $stmt->bind_param("siiii", $name, $total_stock, $available_stock, $threshold, $id);
    
    if ($stmt->execute()) {
        return ['status' => 'success', 'message' => 'Material updated successfully'];
    } else {
        throw new Exception("Error updating material: " . $stmt->error);
    }
}

function deleteMaterial($conn) {
    if (empty($_POST['id'])) {
        throw new Exception("Missing material ID");
    }

    $id = (int)$_POST['id'];
    
    $stmt = $conn->prepare("DELETE FROM materials WHERE id=?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        return ['status' => 'success', 'message' => 'Material deleted successfully'];
    } else {
        throw new Exception("Error deleting material: " . $stmt->error);
    }
}

function getMaterials($conn) {
    $result = $conn->query("SELECT * FROM materials ORDER BY name");
    
    if (!$result) {
        throw new Exception("Error fetching materials: " . $conn->error);
    }
    
    $materials = [];
    while ($row = $result->fetch_assoc()) {
        $materials[] = $row;
    }
    
    return $materials;
}

// Get all materials for display
try {
    $materials = getMaterials($conn);
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- Rest of your HTML remains the same -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inventory Monitoring</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="./Styles/inventory.css">
</head>
<body>
  <div class="sidebar">
    <?php include './INCLUDE/header.php'; ?>
  </div>

  <div class="content">
    <h1 class="text-center my-4">Inventory Monitoring</h1>

    <!-- Add New Material Button and Modal -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
      <i class="fas fa-plus"></i> Add New Material
    </button>

    <!-- Add Material Modal -->
    <div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addMaterialModalLabel">Add New Material</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="createMaterialForm">
              <div class="mb-3">
                <label for="materialName" class="form-label">Material Name</label>
                <input type="text" class="form-control" id="materialName" name="name" required>
              </div>
              <div class="mb-3">
                <label for="totalStock" class="form-label">Total Stock</label>
                <input type="number" class="form-control" id="totalStock" name="total_stock" required>
              </div>
              <div class="mb-3">
                <label for="availableStock" class="form-label">Available Stock</label>
                <input type="number" class="form-control" id="availableStock" name="available_stock" required>
              </div>
              <div class="mb-3">
                <label for="threshold" class="form-label">Alert Threshold</label>
                <input type="number" class="form-control" id="threshold" name="threshold" value="10">
              </div>
              <input type="hidden" name="action" value="create">
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="submitForm('createMaterialForm')">Save Material</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Material Modal -->
    <div class="modal fade" id="editMaterialModal" tabindex="-1" aria-labelledby="editMaterialModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editMaterialModalLabel">Edit Material</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="editMaterialForm">
              <input type="hidden" id="editId" name="id">
              <div class="mb-3">
                <label for="editMaterialName" class="form-label">Material Name</label>
                <input type="text" class="form-control" id="editMaterialName" name="name" required>
              </div>
              <div class="mb-3">
                <label for="editTotalStock" class="form-label">Total Stock</label>
                <input type="number" class="form-control" id="editTotalStock" name="total_stock" required>
              </div>
              <div class="mb-3">
                <label for="editAvailableStock" class="form-label">Available Stock</label>
                <input type="number" class="form-control" id="editAvailableStock" name="available_stock" required>
              </div>
              <div class="mb-3">
                <label for="editThreshold" class="form-label">Alert Threshold</label>
                <input type="number" class="form-control" id="editThreshold" name="threshold">
              </div>
              <input type="hidden" name="action" value="update">
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="submitForm('editMaterialForm')">Update Material</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteMaterialModal" tabindex="-1" aria-labelledby="deleteMaterialModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteMaterialModalLabel">Confirm Deletion</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to delete this material?
            <form id="deleteMaterialForm">
              <input type="hidden" id="deleteId" name="id">
              <input type="hidden" name="action" value="delete">
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" onclick="submitForm('deleteMaterialForm')">Delete</button>
          </div>
        </div>
      </div>
    </div>

    <div class="dashboard-box">
      <div class="stock-bar-container">
        <h5>Inventory Condition (Real-Time)</h5>
        <canvas id="inventoryStatusChart" height="150"></canvas>
        <small class="text-muted">Red: Critical | Yellow: Low | Blue: Stable | Green: Excess</small>
      </div>

      <div class="separator-line"></div>

      <div class="notifications card shadow-sm border-0">
        <div class="card-body">
          <h5 class="card-title mb-3">
            <i class="fas fa-bell text-warning me-2"></i>Inventory Alerts
          </h5>
          <div class="d-flex justify-content-between mb-2">
            <div><i class="fas fa-box-open text-danger me-1"></i> <strong>Out of Stock:</strong></div>
            <span id="outOfStockCount" class="badge bg-danger rounded-pill">0 SKUs</span>
          </div>
          <div class="d-flex justify-content-between mb-3">
            <div><i class="fas fa-exclamation-triangle text-warning me-1"></i> <strong>Low Stock:</strong></div>
            <span id="lowStockCount" class="badge bg-warning text-dark rounded-pill">0 SKUs</span>
          </div>
          <h6 class="mb-2 text-muted">Critical Items</h6>
          <ul class="list-group small" id="criticalItemList"></ul>
          <h6 class="mb-2 text-muted">Low Stock Items</h6>
          <ul class="list-group small" id="lowStockItemList"></ul>
        </div>
      </div>
    </div>

    <div class="inventory-controls">
      <button id="toggleAll" class="btn-transparent" title="Toggle All">
        <i class="fas fa-toggle-on"></i> Toggle All
      </button>
      <input type="text" class="form-control" placeholder="Search Product" style="max-width: 200px;">
      <select class="form-select" style="max-width: 150px;">
        <option value="">Filter</option>
        <option>Paint</option>
        <option>Brush</option>
        <option>Tape</option>
      </select>
      <button class="btn-transparent"><i class="fas fa-bell"></i> Alert</button>
      <button class="btn-transparent"><i class="fas fa-undo"></i> Restock List</button>
    </div>

    <table class="table table-bordered bg-white">
      <thead>
        <tr>
          <th>Product</th>
          <th>Total Stock</th>
          <th>Available Stock</th>
          <th>Threshold</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($materials as $material): 
          $status = '';
          if ($material['available_stock'] == 0) {
              $status = 'Out of Stock';
          } elseif ($material['available_stock'] <= $material['threshold']) {
              $status = 'Low Stock';
          } else {
              $status = 'In Stock';
          }
        ?>
        <tr>
          <td><?php echo htmlspecialchars($material['name']); ?></td>
          <td><?php echo $material['total_stock']; ?></td>
          <td><?php echo $material['available_stock']; ?></td>
          <td><?php echo $material['threshold']; ?></td>
          <td>
            <span class="badge <?php 
              echo $status == 'Out of Stock' ? 'bg-danger' : 
                   ($status == 'Low Stock' ? 'bg-warning text-dark' : 'bg-success');
            ?>">
              <?php echo $status; ?>
            </span>
          </td>
          <td>
            <button class="btn btn-sm btn-primary" onclick="editMaterial(
              '<?php echo $material['id']; ?>',
              '<?php echo htmlspecialchars($material['name'], ENT_QUOTES); ?>',
              '<?php echo $material['total_stock']; ?>',
              '<?php echo $material['available_stock']; ?>',
              '<?php echo $material['threshold']; ?>'
            )">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-danger" onclick="confirmDelete('<?php echo $material['id']; ?>')">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./Script/inventory.js"></script>
  <script>
    function submitForm(formId) {
      const form = document.getElementById(formId);
      const formData = new FormData(form);
      
      fetch('<?php echo $_SERVER['PHP_SELF']; ?>', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          alert(data.message);
          location.reload();
        } else {
          alert(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    }
    
    function editMaterial(id, name, totalStock, availableStock, threshold) {
      document.getElementById('editId').value = id;
      document.getElementById('editMaterialName').value = name;
      document.getElementById('editTotalStock').value = totalStock;
      document.getElementById('editAvailableStock').value = availableStock;
      document.getElementById('editThreshold').value = threshold;
      
      const modal = new bootstrap.Modal(document.getElementById('editMaterialModal'));
      modal.show();
    }
    
    function confirmDelete(id) {
      document.getElementById('deleteId').value = id;
      const modal = new bootstrap.Modal(document.getElementById('deleteMaterialModal'));
      modal.show();
    }
  </script>
</body>
</html>