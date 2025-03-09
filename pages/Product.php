<?php
include "../authentication/authenticated.php";
include("../Connection/database.php"); // Include the database connection

$limit = 5; // Number of records per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$searchQuery = "";
$params = [];
$sql = "SELECT * FROM product_view";

// Check if a search query is provided
if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
    $searchQuery = "%" . $_GET['search_query'] . "%";
    $sql .= " WHERE product_name LIKE ? OR product_description LIKE ?";
    $params = [$searchQuery, $searchQuery];
}

// Append ORDER BY and pagination
$sql .= " ORDER BY PID LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

// Prepare and execute main query
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param(str_repeat("s", count($params) - 2) . "ii", ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get total records for pagination
$countQuery = "SELECT COUNT(*) AS total FROM product_view";
if (!empty($searchQuery)) {
    $countQuery .= " WHERE product_name LIKE ? OR product_description LIKE ?";
}

$countStmt = $conn->prepare($countQuery);
if (!empty($searchQuery)) {
    $countStmt->bind_param("ss", $searchQuery, $searchQuery);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sidebar</title>

    <!-- External CSS & Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- External JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../Designs/employee.css">
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
                    <a href="AdminDashboard.php">
                        <li><i class="fas fa-chart-line"></i> <span>Dashboard</span></li>
                    </a>
                    <a href="Employee.php">
                        <li><i class="fas fa-users"></i> <span>Employee</span></li>
                    </a>
                    <a href="Roles.php">
                        <li><i class="bi bi-person-lines-fill"></i> <span>Roles</span></li>
                    </a>
                    <a href="Supplier.php">
                        <li><i class="bi bi-building"></i> <span>Suppliers</span></li>
                    </a>

                     <li class="dropdown" onclick="toggleDropdown(this,event)">
                        <i class="bi bi-view-stacked"></i>
                        <span class="dropdown-text">Items</span>
                        <i class="fas fa-chevron-right arrow-icon"></i>
                        <ul class="dropdown-menu text-truncate">
                            <a class="text-truncate" href="product.php">
                                <li>Products</li>
                            </a>
                            <a class="text-truncate" href="category.php">
                                <li>Categories</li>
                            </a>
                            <a class="text-truncate" href="add_ons.php">
                                <li>Add Ons</li>
                            </a>
                        </ul>
                    </li>

                    <li class="dropdown" onclick="toggleDropdown(this,event)">
                    <i class="fas fa-chart-pie"></i> <span>Reports</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                    <ul class="dropdown-menu text-truncate">
                        <a class="text-truncate" href="Transaction.php">
                            <li>Transaction History</li>
                        </a>
                        <a class="text-truncate" href="Stock-In_History.php">
                            <li>Stock in History</li>
                        </a>
                        <a class="text-truncate" href="Sales_History.php">
                            <li>Sales History</li>
                        </a>
                    </ul>
                </li>
                </ul>

                <ul class="settings-container">
                    <h6 class="menu-title text-truncate px-3">Appearance</h6>
                    <li class="toggle-item">
                        <div class="toggle-switch" onclick="toggleDarkMode()"></div>
                    </li>
                    <a href="../handlers/logout_handler.php">
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
                        <h3 class="mt-3 page-title">Manage Product</h3>
                    </div>
                </div>


                <!-- Search & Button -->
                <div class="row">
                    <div class="col d-flex align-items-center justify-content-between p-0">
                        <!-- Search Box -->
                        <div class="search-box">
                            <form class="d-flex align-items-center" method="GET" action="Product.php">

                                <i class="fas fa-search"></i>

                                <input class="form-control search-input " type="search" name="search_query"
                                    placeholder="Search anything..." aria-label="Search"
                                    value="<?php echo isset($_GET['search_query']) && $_GET['search_query'] !== '' ? $_GET['search_query'] : ''; ?>"
                                    onfocus="if(this.value==='') { this.value=''; }"
                                    onblur="if(this.value==='') { this.value=''; }">
                                <button class="btn btn-search ms-2" type="submit">Search</button>
                            </form>
                        </div>
                        <!-- Right-aligned container -->
                        <div class="d-flex align-items-center justify-content-end gap-3">
                            <div class="d-flex align-items-center">
                                <!-- ADD EMPLOYEE BUTTON -->
                                <button type="button"
                                    class="employee-details d-flex justify-content-center align-items-center "
                                    data-bs-toggle="modal" data-bs-target="#infoModal2">
                                    <i class="bi bi-plus-square mx-2"></i>
                                    <span class="text-center">Add Product</span>
                                </button>

                                <!-- Add Product Modal -->
                                <div class="modal fade" id="infoModal2" tabindex="-1" aria-labelledby="infoModalLabel2"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="productForm" action="../Handlers/addProduct_handler.php"
                                                    method="POST" enctype="multipart/form-data">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label for="product_name">Product Name:</label>
                                                            <input type="text" id="product_name" name="product_name"
                                                                class="form-control" required>

                                                            <label for="CID" class="mt-3">Category:</label>
                                                            <select id="CID" name="CID" class="form-control" required>
                                                                <option value="" disabled selected>Select Category</option>
                                                                <?php
                                                                $query = "SELECT * FROM categories";
                                                                $result = $conn->query($query);
                                                                while ($row = $result->fetch_assoc()) {
                                                                    echo '<option value="' . $row['CID'] . '">' . $row['category_name'] . '</option>';
                                                                }
                                                                ?>
                                                            </select>

                                                            <label for="price" class="mt-3">Price:</label>
                                                            <input type="number" id="price" name="price"
                                                                class="form-control" required>


                                                        </div>

                                                        <div class="col-6">


                                                            <label for="supplier">Supplier:</label>
                                                            <select id="supplier" name="SID" class="form-control mb-3"
                                                                required>
                                                                <option value="">Select Supplier</option>
                                                                <?php
                                                                $query2 = "SELECT * FROM suppliers";
                                                                $result2 = $conn->query($query2);
                                                                while ($row2 = $result2->fetch_assoc()) {
                                                                    echo '<option value="' . $row2['SID'] . '">' . $row2['company_name'] . '</option>';
                                                                }
                                                                ?>
                                                            </select>

                                                           
                                                        </div>

                                                        <div class="col-12">
                                                            <label for="product_description" class="mt-3">Product
                                                                Description:</label>
                                                            <textarea id="product_description"
                                                                name="product_description" class="form-control" rows="3"
                                                                required></textarea>

                                                            <label class="form-label mt-3">Upload Image:</label>
                                                            <input name="img" type="file" class="form-control-file"
                                                                accept="image/*" required>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="bi bi-check-circle"></i> Submit
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <?php
                $limit = 5; // Number of records per page
                $page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                $offset = ($page - 1) * $limit;

                $searchQuery = "";
                $params = [];
                $types = "";
                $sql = "SELECT * FROM product_view";

                // If a search query is provided, modify the SQL
                if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
                    $searchQuery = "%" . $_GET['search_query'] . "%";
                    $sql .= " WHERE product_name LIKE ? OR product_description LIKE ?";
                    $params = [$searchQuery, $searchQuery];
                    $types = "ss"; // Two string parameters
                }

                // Append ORDER BY and pagination
                $sql .= " ORDER BY PID LIMIT ? OFFSET ?";
                $params[] = $limit;
                $params[] = $offset;
                $types .= "ii"; // Two integer parameters for LIMIT & OFFSET
                
                // Prepare and execute query
                $stmt = $conn->prepare($sql);
                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();

                // Count total records for pagination
                $countQuery = "SELECT COUNT(*) AS total FROM product_view";
                if (!empty($searchQuery)) {
                    $countQuery .= " WHERE product_name LIKE ? OR product_description LIKE ?";
                }
                $countStmt = $conn->prepare($countQuery);
                if (!empty($searchQuery)) {
                    $countStmt->bind_param("ss", $searchQuery, $searchQuery);
                }
                $countStmt->execute();
                $countResult = $countStmt->get_result();
                $totalRecords = $countResult->fetch_assoc()['total'];
                $totalPages = ceil($totalRecords / $limit);
                ?>

                <div class="table-responsive mt-4"> <!-- Added 'mt-4' for spacing -->
                    <table class="table">
                        <thead class="table-header">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Product Name</th>
                                <th scope="col">Category</th>
                                <th scope="col">Price</th>
                                <th scope="col">Supplier</th>
                                <th scope="col">Details</th>
                                <th scope="col">Date Added</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-body">
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['PID']; ?></td>
                                        <td><?php echo $row['product_name']; ?></td>
                                        <td><?php echo $row['category_name']; ?></td>
                                        <td><?php echo $row['price']; ?></td>
                                        <td><?php echo $row['company_name']; ?></td>
                                        <td> <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#infoModal<?php echo $row['PID']; ?>">
                                                See More&nbsp;<i class="fa-solid fa-ellipsis"></i>
                                            </button></td>
                                            <td><?php echo date('F j, Y g:i A', strtotime($row['date'])); ?></td>

                                        <td>
                                            <form action="../Handlers/deleteProduct_handler.php" method="POST"
                                                onsubmit="return confirmUpdate2()">
                                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editmodal_<?php echo $row['PID']; ?>">
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>
                                                <input type="hidden" name="PID"
                                                    value="<?php echo htmlspecialchars($row['PID']); ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Modal for Product Details -->
                                    <div class="modal fade" id="infoModal<?php echo $row['PID']; ?>" tabindex="-1"
                                        aria-labelledby="infoModalLabel<?php echo $row['PID']; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="infoModalLabel<?php echo $row['PID']; ?>">
                                                        Product Details
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-12 mb-3 text-center">
                                                            <img class="prod img-fluid"
                                                                src="data:image/jpeg;base64,<?php echo base64_encode($row['image']); ?>"
                                                                style="max-height: 100px; width: 100px; object-fit: cover;">
                                                        </div>
                                                        <div class="col-12">
                                                            <h5 class="mt-3 mb-4 text-center">
                                                                <?php echo $row['product_name']; ?>
                                                            </h5>
                                                            <p><strong>Category:</strong> <?php echo $row['category_name']; ?>
                                                            </p>
                                                            <p><strong>Price:</strong> <?php echo $row['price']; ?></p>
                                                            <p><strong>Supplier:</strong> <?php echo $row['company_name']; ?>
                                                            </p>
                                                            <p><strong>Description:</strong>
                                                                <?php echo $row['product_description']; ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Update Product Modal -->
                                    <div class="modal fade" id="editmodal_<?php echo $row['PID']; ?>" tabindex="-1"
                                        aria-labelledby="editModalLabel_<?php echo $row['PID']; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel_<?php echo $row['PID']; ?>">
                                                        Update Product</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="updateProductForm" action="../HANDLERS/updateProduct_handler.php"
                                                        method="POST" enctype="multipart/form-data">
                                                        <input type="hidden" name="PID" value="<?php echo $row['PID']; ?>">

                                                        <div class="row">
                                                            <!-- Left Column -->
                                                            <div class="col-md-12">
                                                                <label for="product_name">Product Name:</label>
                                                                <input type="text" id="product_name" name="product_name"
                                                                    class="form-control"
                                                                    value="<?php echo htmlspecialchars($row['product_name']); ?>"
                                                                    required>

                                                                <label for="CID" class="mt-3">Category:</label>
                                                                <select id="CID" name="CID" class="form-control" required>
                                                                    <option value="">Select Category</option>
                                                                    <?php 
                                                                            $categories = $conn->query("SELECT CID, category_name FROM categories ORDER BY category_name");

                                                                    foreach ($categories as $cat) {
                                                                        $selected = ($cat['CID'] == $row['CID']) ? "selected" : "";
                                                                        echo '<option value="' . $cat['CID'] . '" ' . $selected . '>' . $cat['category_name'] . '</option>';
                                                                    } ?>
                                                                </select>

                                                                <label for="price" class="mt-3">Price:</label>
                                                                <input type="text" id="price" name="price" class="form-control"
                                                                    value="<?php echo htmlspecialchars($row['price']); ?>"
                                                                    required>
                                                            </div>

                                                            <!-- Right Column -->
                                                            <div class="col-md-6">
                                                                <label for="supplier">Supplier:</label>
                                                                <select id="supplier" name="SID" class="form-control" required>
                                                                    <option value="">Select Supplier</option>
                                                                    
                                                                    <?php
                                                                  $query2 = "SELECT * FROM suppliers";
                                                                  $suppliers = $conn->query($query2);
                                                                    foreach ($suppliers as $sup) {

                                                                        $selected = ($sup['SID'] == $row['SID']) ? "selected" : "";
                                                                        echo '<option value="' . $sup['SID'] . '" ' . $selected . '>' . $sup['company_name'] . '</option>';
                                                                    } ?>
                                                                </select>

                                                               
                                                            </div>

                                                            <!-- Full Width -->
                                                            <div class="col-12">
                                                                <label for="product_description" class="mt-3">Product
                                                                    Description:</label>
                                                                <textarea id="product_description" name="product_description"
                                                                    class="form-control" rows="3"
                                                                    required><?php echo htmlspecialchars($row['product_description']); ?></textarea>

                                                               
                                                            </div>
                                                        </div>

                                                        <!-- Modal Footer -->
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="bi bi-check-circle"></i> Update
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">No products found.</td>
                                </tr>
                            <?php endif; ?>
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








                <!-- CUSTOM JS -->
                <script>
                    function confirmUpdate() {
                        return confirm("Are you sure you want to update this product's information?");
                    }
                    function confirmUpdate2() {
                        return confirm("Are you sure you want to delete this product's information?");
                    }

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

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
                    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
                    crossorigin="anonymous">
                    </script>
                <!-- Bootstrap JS (required for modals) -->
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>