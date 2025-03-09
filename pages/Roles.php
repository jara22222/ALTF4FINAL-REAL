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
    <link rel="stylesheet" href="../Designs/role.css">
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
                <a href="employee.php"><li><i class="fas fa-users"></i> <span>Employee</span></li></a>
                <a href="Roles.php"><li><i class="bi bi-person-lines-fill"></i> <span>Roles</span></li></a>
                <a href="supplier.php"><li><i class="bi bi-building"></i> <span>Suppliers</span></li></a>

                <li class="dropdown" onclick="toggleDropdown(this,event)">
                    <i class="bi bi-view-stacked"></i>
                    <span class="dropdown-text">Items</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                    <ul class="dropdown-menu">
                        <a class="text-truncate" href="product.php"><li>Products</li></a>
                        <a class="text-truncate" href="category.php"><li>Categories</li></a>
                        <a class="text-truncate" href="add_ons.php"><li>Add Ons</li></a>
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
                <h3 class="mt-3 page-title">Manage Roles</h3>
            </div>
        </div>
        

    <!-- Search & Button -->
    <div class="row">
        <div class="col d-flex align-items-center justify-content-between p-0">
           <!-- Search Box -->
           <div class="search-box">
            <form class="d-flex align-items-center" method="GET" action="Roles.php">

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
                    <form id="roleForm" action="../Handlers/addRole_handler.php" method="POST" >
                        <button type="button" class="employee-details d-flex justify-content-center align-items-center" 
                            data-bs-toggle="modal" data-bs-target="#infoModal2">
                            <i class="bi bi-plus-square mx-2"></i>
                            <span class="text-center">Add Role</span>
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <?php
    include("../Connection/database.php"); // Include your database connection

    // Fetch data from the database
    $query = "SELECT * FROM roles"; // Replace 'roles' with your table name
    $result = $conn->query($query);

    if ($result->num_rows > 0): ?>
        <!-- Table -->
        <div class="table-responsive mt-4">
            <table class="table">
                <thead class="table-header">
                    <tr>
                        <th>ID</th>
                        <th>Position</th>
                        <th>Details</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['RID']); ?></td>
                        <td><?php echo htmlspecialchars($row['rolename']); ?></td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <!-- See More Button -->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#infoModal<?php echo htmlspecialchars($row['RID']); ?>">
                                    See More
                                </button>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <!-- Edit Button -->
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo htmlspecialchars($row['RID']); ?>">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <!-- Delete Button -->
                                <form action="../Handlers/deleteRole_handler.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                    <input type="hidden" name="RID" value="<?php echo htmlspecialchars($row['RID']); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Info Modal -->
                    <div class="modal fade" id="infoModal<?php echo htmlspecialchars($row['RID']); ?>" tabindex="-1" aria-labelledby="infoModalLabel<?php echo htmlspecialchars($row['RID']); ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="infoModalLabel<?php echo htmlspecialchars($row['RID']); ?>">Role Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Role ID:</strong> <?php echo htmlspecialchars($row['RID']); ?></p>
                                    <p><strong>Role Name:</strong> <?php echo htmlspecialchars($row['rolename']); ?></p>
                                    <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?php echo htmlspecialchars($row['RID']); ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo htmlspecialchars($row['RID']); ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel<?php echo htmlspecialchars($row['RID']); ?>">Edit Role</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="../Handlers/updateRole_handler.php" method="POST">
                                        <input type="hidden" name="RID" value="<?php echo htmlspecialchars($row['RID']); ?>">

                                        <div class="mb-3">
                                            <label for="rolename<?php echo htmlspecialchars($row['RID']); ?>" class="form-label">Role Name:</label>
                                            <input type="text" id="rolename<?php echo htmlspecialchars($row['RID']); ?>" name="rolename" class="form-control" value="<?php echo htmlspecialchars($row['rolename']); ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description<?php echo htmlspecialchars($row['RID']); ?>" class="form-label">Description:</label>
                                            <textarea id="description<?php echo htmlspecialchars($row['RID']); ?>" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($row['description']); ?></textarea>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
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
        </div>
    <?php else: ?>
    <p>No roles found.</p>
<?php endif; ?>



    <!-- MODALS -->

    <!-- Add Employee -->
    <div class="modal fade" id="infoModal2" tabindex="-1" aria-labelledby="infoModalLabel2" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <h5 class="modal-title" id="infoModalLabel2">Role Add</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
            <form action="../Handlers/addRole_handler.php" method="POST" onsubmit="return confirmUpdate()">
            <div class="row">
                <div class="col-12">
                <!-- Position Input -->
                <div class="mb-3">
                    <label for="position" class="form-label">Position:</label>
                    <input type="text" id="position" name="position" class="form-control" required>
                </div>

                <!-- Description Input -->
                <div class="mb-3">
                    <label for="description" class="form-label">Description:</label>
                    <input type="text" id="description" name="description" class="form-control" required>
                </div>
                </div>
            </div>

            <!-- Hidden Input -->
            <input type="hidden" name="RID" value="<?php echo $row['RID']; ?>">
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success">
            <i class="bi bi-pencil-square"></i> Submit
            </button>
        </div>
            </form>
        </div>
    </div>
    </div>


    <!-- CUSTOM JS -->
    <script>
        document.getElementById('roleForm').addEventListener('submit', function (event) {
            const requiredFields = ['position', 'description'];
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

        function toggleDropdown(element, event) {
            element.classList.toggle("active");

            let dropdownMenu = element.querySelector(".dropdown-menu");
            if (dropdownMenu) {
                dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
            }

            let arrowIcon = element.querySelector(".arrow-icon");
            if (arrowIcon) {
                arrowIcon.classList.toggle("rotated");
            }
        }
    </script>


</body>
</html>