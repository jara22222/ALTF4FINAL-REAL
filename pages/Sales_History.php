<?php
include "../authentication/authenticated.php";
include("../Connection/database.php"); // Include the database connection

// Pagination settings
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$searchQuery = "";

if (!empty($_GET['search_query'])) {
    $searchQuery = "%" . $_GET['search_query'] . "%";
    // Search for dates formatted as "March 9" or other fields
    $stmt = $conn->prepare("
        SELECT sum(TotalAmount) as sales, DATE_FORMAT(OrderDate, '%M %e, %Y') as sales_date 
        FROM orders 
        WHERE DATE_FORMAT(OrderDate, '%M %e') LIKE ? 
        GROUP BY sales_date 
        ORDER BY sales_date DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("sii", $searchQuery, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    // Count total rows for search
    $stmtTotal = $conn->prepare("
        SELECT COUNT(DISTINCT DATE_FORMAT(OrderDate, '%M %e, %Y')) AS total 
        FROM orders 
        WHERE DATE_FORMAT(OrderDate, '%M %e') LIKE ?
    ");
    $stmtTotal->bind_param("s", $searchQuery);
    $stmtTotal->execute();
    $stmtTotal->bind_result($totalRows);
    $stmtTotal->fetch();
    $stmtTotal->close();
} else {
    // Non-search query
    $result = $conn->query("
        SELECT sum(TotalAmount) as sales, DATE_FORMAT(OrderDate, '%M %e, %Y') as sales_date 
        FROM orders 
        GROUP BY sales_date 
        ORDER BY sales_date DESC 
        LIMIT $limit OFFSET $offset
    ");
    $totalRowsResult = $conn->query("SELECT COUNT(DISTINCT DATE_FORMAT(OrderDate, '%M %e, %Y')) AS total FROM orders");
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
    <link rel="stylesheet" href="../Designs/employee.css">
    <link rel="stylesheet" href="../Designs/style.css">

    <!-- External JS -->
    <script src="../Designs/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<style>
    .page-header-1,
    .page-header-2,
    .page-header-3 {
        display: none;
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            text-align: center;
        }

        .page-header-1,
        .page-header-2,
        .page-header-3 {
            display: block !important;
            text-align: center;
            width: 100%;
            margin-bottom: 10px;
        }

        .page-header-1 {
            font-size: 22px;
            font-weight: bold;
        }

        .page-header-2,
        .page-header-3 {
            font-size: 16px;
        }

        .sidebar,
        .search-box,
        .btn,
        .modal,
        .pagination,
        .page-title {
            display: none !important;
        }

        .table-container {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .content {
            display: block;
            width: 100%;
        }

        table {
            width: 95% !important;
            border-collapse: collapse;
            margin: 0 auto;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid black;
            text-align: center;
            white-space: nowrap;
        }
    }
</style>

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
                    <div class="col d-flex align-items-center justify-content-between p-0">
                        <h3 class="mt-3 page-title">Sales History</h3>
                    </div>
                </div>

                <!-- Search & Button -->
                <div class="row">
                    <div class="col d-flex align-items-center justify-content-between p-0">
                        <!-- Search Box -->
                        <div class="search-box">
                            <form class="d-flex align-items-center" method="GET" action="Sales_History.php">
                                <i class="fas fa-search"></i>
                                <input class="form-control search-input" type="search" name="search_query" placeholder="Search by date (e.g., March 9)..." aria-label="Search" value="<?php echo isset($_GET['search_query']) && $_GET['search_query'] !== '' ? $_GET['search_query'] : ''; ?>">
                                <button class="btn btn-search ms-2" type="submit">Search</button>
                            </form>
                        </div>

                        <!-- Right-aligned container -->
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-end justify-content-end mt-3 mb-3">
                                <button id="print" class="btn btn-search ms-2 gap-2" type="submit"><i class="fa-solid fa-print"></i>Print</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PRINT ONLY -->
                <div class="page-header-1">BLACKSNOW CAFE</div>
                <div class="page-header-2">Emilio Jacinto St. Davao City, Philippines</div>
                <div class="page-header-3">Sales History</div>

                <div class="table-responsive mt-4">
                    <table class="table">
                        <thead class="table-header">
                            <tr>
                                <th scope="col">Total Sales</th>
                                <th scope="col">Sales Date</th>
                            </tr>
                        </thead>
                        <tbody class="table-body">
                            <?php if ($result->num_rows > 0) : ?>
                                <?php while ($row = $result->fetch_assoc()) : ?>
                                    <tr>
                                        <td><?php echo '₱ '. $row['sales']; ?></td>
                                        <td><?php echo $row['sales_date']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="2">No transaction found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?search_query=<?php echo urlencode($_GET['search_query'] ?? ''); ?>&page=<?php echo $page - 1; ?>">Previous</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?search_query=<?php echo urlencode($_GET['search_query'] ?? ''); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php } ?>
                            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?search_query=<?php echo urlencode($_GET['search_query'] ?? ''); ?>&page=<?php echo $page + 1; ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- CUSTOM JS -->
    <script>
        function confirmUpdate() {
            return confirm("Are you sure you want to update this category's information?");
        }

        function confirmUpdate2() {
            return confirm("Are you sure you want to delete this category's information?");
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

        // PRINT
        const printBtn = document.getElementById('print');
        printBtn.addEventListener('click', function() {
            print();
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- Bootstrap JS (required for modals) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html> 