<?php
include("../Connection/database.php"); // Include the database connection 
session_start();

// Pagination settings
$limit = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$searchQuery = "";

if (!empty($_GET['search_query'])) {
    $searchQuery = "%" . $_GET['search_query'] . "%";
    $stmt = $conn->prepare("SELECT * FROM supplier_addresses WHERE SID LIKE ? OR company_name LIKE ? LIMIT ? OFFSET ?");
    $stmt->bind_param("ssii", $searchQuery, $searchQuery, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    // Count total rows
    $stmtTotal = $conn->prepare("SELECT COUNT(*) FROM supplier_addresses WHERE SID LIKE ? OR company_name LIKE ?");
    $stmtTotal->bind_param("ss", $searchQuery, $searchQuery);
    $stmtTotal->execute();
    $stmtTotal->bind_result($totalRows);
    $stmtTotal->fetch();
    $stmtTotal->close();
} else {
    $result = $conn->query("SELECT * FROM supplier_addresses ORDER BY SID LIMIT $limit OFFSET $offset");
    $totalRowsResult = $conn->query("SELECT COUNT(*) AS total FROM supplier_addresses");
    $totalRows = $totalRowsResult->fetch_assoc()['total'];
}

$totalPages = max(1, ceil($totalRows / $limit)); // Avoid division by zero
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sidebar</title>

    <!-- External CSS & Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- External JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../Designs/supplier.css">
    <link rel="stylesheet" href="../Designs/style.css">

    <!-- External JS -->
    <script src="../Designs/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="container-fluid vh-100">

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-container">
            <img src="../Images/sidebar_logo.png" class="logo-img" alt="Admin">
            <div class="logo-details">
                <h5 class="brand">Blacksnow Café</h5>
            </div>
        </div>

        <div class="container menu-container">
            <ul>
                <h6 class="menu-title">Actions</h6>
                <a href="AdminDashboard.php"><li><i class="fas fa-chart-line"></i> <span>Dashboard</span></li></a>
                <a href="Employee.php"><li><i class="fas fa-users"></i> <span>Employee</span></li></a>
                <a href="Roles.php"><li><i class="bi bi-person-lines-fill"></i> <span>Roles</span></li></a>
                <a href="../Pages/supplier.php"><li><i class="bi bi-building"></i> <span>Suppliers</span></li></a>

                <li class="dropdown" onclick="toggleDropdown(this,event)">
                    <i class="bi bi-view-stacked"></i>
                    <span class="dropdown-text">Items</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                    <ul class="dropdown-menu">
                        <a class="text-truncate" href="../Pages/product.php"><li>Products</li></a>
                        <a class="text-truncate" href="../Pages/category.php"><li>Categories</li></a>
                        <a class="text-truncate" href="../Pages/add_ons.php"><li>Add Ons</li></a>
                    </ul>
                </li>

                <li><i class="fas fa-chart-pie"></i> <span>Reports</span></li>
                <a href="Transaction.php">  <li><i class="fas fa-wallet"></i> <span>Transactions</span></li></a>

              <a href="Stock-In_History.php">  <li><i class="fas fa-wallet"></i> <span>Stock-In History</span></li></a>
            </ul>

            <ul class="settings-container">
                <h6 class="menu-title text-truncate px-3">Appearance</h6>
                <li class="toggle-item">
                    <div class="toggle-switch" onclick="toggleDarkMode()"></div>
                </li>
                <a href="../index.php">
                        <li><i class="fas fa-sign-out-alt"></i> <span>Log out</span></li>
                    </a>
            </ul>

            <div class="profile-container">
                <img src="../Images/girl.jpg" class="profile-img" alt="Admin">
                <div class="profile-details">
                    <h5 class="name">Name Admin</h5>
                    <h6 class="role">Administrator</h6>
                </div>
            </div>
        </div>
    </div>

<!-- Main Content -->
<div class="d-flex flex-column col main-content">
    <div class="col p-4 content">
        <div class="row">

                <?php if (isset($_SESSION['errors'])): ?>
                    <div class="alert alert-danger">
                        <?php
                        echo $_SESSION['errors'];
                        unset($_SESSION['errors']); // Clear the message after displaying
                        ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php
                        echo $_SESSION['success'];
                        unset($_SESSION['success']); // Clear the message after displaying
                        ?>
                    </div>
                <?php endif; ?>

            <div class="col d-flex align-items-center justify-content-between p-0">
                <h3 class="mt-3 page-title">Manage Supplier Information</h3>
            </div>
        </div>
        
    <!-- Search & Button -->
    <div class="row">
        <div class="col d-flex align-items-center justify-content-between p-0">
           <!-- Search Box -->
           <div class="search-box">
            <form class="d-flex align-items-center" method="GET" action="Supplier.php">

            <i class="fas fa-search"></i>

              <input class="form-control search-input " type="search" name="search_query"
                placeholder="Search anything..." aria-label="Search"
                value="<?php echo isset($_GET['search_query']) && $_GET['search_query'] !== '' ? $_GET['search_query'] : ''; ?>"
                onfocus="if(this.value==='') { this.value=''; }" onblur="if(this.value==='') { this.value=''; }">
              <button class="btn btn-search ms-2" type="submit">Search</button>
            </form>
          </div>

            <!-- Right-aligned container -->
            <div class="d-flex align-items-center justify-content-end gap-3">
                <div class="d-flex align-items-center">
                    <!-- ADD EMPLOYEE BUTTON -->
                    <form id="supplierForm" action="../Handlers/addSupplier_handler.php" method="POST" >
                        <button type="button" class="employee-details d-flex justify-content-center align-items-center" 
                        data-bs-toggle="modal" data-bs-target="#infoModal2">
                            <i class="bi bi-plus-square mx-2"></i>
                            <span class="text-center">Add Supplier</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

<!-- Table -->
<div class="table-responsive mt-4"> <!-- Added 'mt-4' for spacing -->
    <table class="table">
        <thead class="table-header">
            <tr>
                <th>ID</th>
                <th>Business Name</th>
                <th>Details</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody class="table-body">
            <?php
            include '../Connection/database.php';

            $query = "
                SELECT s.SID, s.company_name, s.contact_number, s.email, s.license_number, 
                a.street, a.city, a.province, a.zipcode
                FROM suppliers s
                LEFT JOIN address a ON s.SID = a.SID;
                ";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?= $row['SID']; ?></td>
                        <td><?= htmlspecialchars($row['company_name']); ?></td>
                        <td class="d-flex justify-content-center">
                            <button type="button" class="btn btn-primary justify-content-center" data-bs-toggle="modal" data-bs-target="#infoModal<?= $row['SID']; ?>">
                                See More
                            </button>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['SID']; ?>">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <!-- Delete Button -->
                                <form action="../Handlers/deleteSupplier_handler.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                                    <input type="hidden" name="SID" value="<?= htmlspecialchars($row['SID']); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Supplier Details Modal -->
                    <div class="modal fade" id="infoModal<?= $row['SID']; ?>" tabindex="-1" aria-labelledby="infoModalLabel<?= $row['SID']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <!-- Modal Header -->
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold text-start" id="infoModalLabel<?= $row['SID']; ?>">Supplier Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <!-- Modal Body -->
                                <div class="modal-body text-start">
                                    <div class="container-input row">
                                        <div class="col-12">
                                            <h5 class="mt-3 mb-3 fw-semibold text-primary text-center"><?= htmlspecialchars($row['company_name']); ?></h5>
                                            <p class="mb-2"><strong>Contact Number:</strong> <?= htmlspecialchars($row['contact_number']); ?></p>
                                            <p class="mb-2"><strong>Email:</strong> <?= htmlspecialchars($row['email']); ?></p>
                                            <p class="mb-4"><strong>License Number:</strong> <?= htmlspecialchars($row['license_number']); ?></p>

                                            <!-- Address Details -->
                                            <?php if (!empty($row['street'])): ?>
                                            <hr class="my-3">
                                            <h5 class="fw-semibold text-secondary text-center">Address Details</h5>
                                            <p class="mb-2"><strong>Street:</strong> <?= htmlspecialchars($row['street']); ?></p>
                                            <p class="mb-2"><strong>City:</strong> <?= htmlspecialchars($row['city']); ?></p>
                                            <p class="mb-2"><strong>Province:</strong> <?= htmlspecialchars($row['province']); ?></p>
                                            <p class="mb-2"><strong>Zipcode:</strong> <?= htmlspecialchars($row['zipcode']); ?></p>
                                            <?php else: ?>
                                            <p class="text-muted mt-3">No address available</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Footer -->
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Supplier Modal -->
                    <div class="modal fade edit-modal" id="editModal<?= $row['SID']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $row['SID']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <!-- Modal Header -->
                                <div class="modal-header">
                                    <h5 class="modal-title">Update Supplier</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <!-- Modal Body -->
                                <div class="modal-body">
                                    <form class="edit-supplier-form" action="../Handlers/updateSupplier_handler.php" method="POST">
                                        <input type="hidden" name="SID" value="<?= $row['SID']; ?>">
                                        <div class="container">
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label for="company_name">Company Name:</label>
                                                    <input type="text" id="company_name" name="company_name" class="form-control" value="<?= htmlspecialchars($row['company_name']); ?>" required>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label for="contact_number">Contact Number:</label>
                                                    <input type="text" id="contact_number" name="contact_number" class="form-control" value="<?= htmlspecialchars($row['contact_number']); ?>" required>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label for="email">Email:</label>
                                                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']); ?>" required>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label for="license_number">License Number:</label>
                                                    <input type="text" id="license_number" name="license_number" class="form-control" value="<?= htmlspecialchars($row['license_number']); ?>" required>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label for="street">Street:</label>
                                                    <input type="text" id="street" name="street" class="form-control" value="<?= htmlspecialchars($row['street']); ?>" required>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label for="city">City:</label>
                                                    <input type="text" id="city" name="city" class="form-control" value="<?= htmlspecialchars($row['city']); ?>" required>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label for="province">Province:</label>
                                                    <input type="text" id="province" name="province" class="form-control" value="<?= htmlspecialchars($row['province']); ?>" required>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label for="zipcode">Zip Code:</label>
                                                    <input type="text" id="zipcode" name="zipcode" class="form-control" value="<?= htmlspecialchars($row['zipcode']); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Modal Footer -->
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success"><i class="bi bi-pencil-square"></i> Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="4" class="text-center text-muted">No suppliers found.</td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
                    <!-- Pagination -->
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link"
                                href="?search_query=<?php echo $_GET['search_query'] ?? ''; ?>&page=<?php echo $page - 1; ?>">Previous</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link"
                                    href="?search_query=<?php echo $_GET['search_query'] ?? ''; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php } ?>
                            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link"
                                href="?search_query=<?php echo $_GET['search_query'] ?? ''; ?>&page=<?php echo $page + 1; ?>">Next</a>
                            </li>
                        </ul>
                    </nav>

                    <script>
                        function confirmUpdate() {
                            return confirm("Are you sure you want to update this supplier?");
                        }

                        function confirmDelete() {
                            return confirm("Are you sure you want to delete this supplier?");
                        }
                    </script>
    </div>



    <!-- MODALS -->
    <!-- Add Supplier Modal -->
    <div class="modal fade" id="infoModal2" tabindex="-1" aria-labelledby="infoModalLabel2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="infoModalLabel2">Add Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <form id="supplierForm" action="../Handlers/addSupplier_handler.php" method="POST">

                    <div class="container-inputs row"> <!-- Added 'row' for bootstrap grid -->
                    <!-- Left Column -->
                    <div class="col-12 mb-3">
                        <label for="company_name" class="form-label">Company Name:</label>
                        <input type="text" id="company_name" name="company_name" class="form-control" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="contact_number" class="form-label">Contact Number:</label>
                        <input type="text" id="contact_number" name="contact_number" class="form-control" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="license_number" class="form-label">License Number:</label>
                        <input type="text" id="license_number" name="license_number" class="form-control" required>
                    </div>

                    <!-- Right Column -->
                    <div class="col-12 mb-3">
                        <label for="street" class="form-label">Street:</label>
                        <input type="text" id="street" name="street" class="form-control" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="city" class="form-label">City:</label>
                        <input type="text" id="city" name="city" class="form-control" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="province" class="form-label">Province:</label>
                        <input type="text" id="province" name="province" class="form-control" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="zipcode" class="form-label">Zip Code:</label>
                        <input type="text" id="zipcode" name="zipcode" class="form-control" required>
                    </div>
                </div>
                

                    <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Submit
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>


<!-- CUSTOM JS -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Validation for Add Supplier Form
            const form = document.querySelector("#supplierForm");
            const email = document.querySelector("#email");
            const phone = document.querySelector("#contact_number");
            const license = document.querySelector("#license_number");

            form.addEventListener("submit", function (event) {
            let errors = [];

            // Email validation
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email.value)) {
                errors.push("Please enter a valid email address.");
            }

            // Phone number validation (Allows +63XXXXXXXXXX or 09XXXXXXXXX)
            const phonePattern = /^(?:\+63\d{10}|09\d{9})$/;
            if (!phonePattern.test(phone.value)) {
                errors.push("Phone number must be in the format: +639XXXXXXXXX or 09XXXXXXXXX.");
            }

            // License number validation (at least 5 alphanumeric characters)
            const licensePattern = /^[a-zA-Z0-9]{5,}$/;
            if (!licensePattern.test(license.value)) {
                errors.push("License number must be at least 5 alphanumeric characters.");
            }

            // Show error messages if any
            if (errors.length > 0) {
                event.preventDefault(); // Prevent form submission
                alert(errors.join("\n")); // Show errors as an alert
            }
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            // Validation for Edit Supplier Forms
            document.querySelectorAll(".edit-supplier-form").forEach(function (form) {
            form.addEventListener("submit", function (event) {
                let errors = [];

                const email = form.querySelector("input[name='email']");
                const phone = form.querySelector("input[name='contact_number']");
                const license = form.querySelector("input[name='license_number']");

                // Email validation
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email.value)) {
                errors.push("Please enter a valid email address.");
                }

                // Phone number validation (Allows +63XXXXXXXXXX or 09XXXXXXXXX)
                const phonePattern = /^(?:\+63\d{10}|09\d{9})$/;
                if (!phonePattern.test(phone.value)) {
                errors.push("Phone number must be in the format: +639XXXXXXXXX or 09XXXXXXXXX.");
                }

                // License number validation (at least 5 alphanumeric characters)
                const licensePattern = /^[a-zA-Z0-9]{5,}$/;
                if (!licensePattern.test(license.value)) {
                errors.push("License number must be at least 5 alphanumeric characters.");
                }

                // Show error messages if any
                if (errors.length > 0) {
                event.preventDefault(); // Prevent form submission
                alert(errors.join("\n")); // Show errors as an alert
                }
            });
            });
        });

        // Toggle Dark Mode
        function toggleDarkMode() {
            document.body.classList.toggle("dark-mode");
        }

        // Toggle Dropdown Menu
        function toggleDropdown(element) {
            element.classList.toggle("active");
        }


        document.getElementById("supplierForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
            this.submit();
        });

        document.getElementById('supplierForm').addEventListener('submit', function (event) {
            const requiredFields = ['fn', 'ln', 'birthday', 'age', 'username', 'gender', 'email', 'phonenumber', 'street', 'city', 'province', 'zipcode', 'role'];
            let isValid = true;

            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if (!input || !input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                event.preventDefault();
                alert('Please fill in all required fields.');
            
            }
        });
        </script>


</body>
</html> 