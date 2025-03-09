<?php
include '../dashboardCashier/Database/Database.php';
session_start();

// Pagination settings
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$searchQuery = "";

if (!empty($_GET['search_query'])) {
  $searchQuery = "%" . $_GET['search_query'] . "%";
  $stmt = $conn->prepare("SELECT * FROM stock_in_history WHERE StockID LIKE ? OR product_name LIKE ? LIMIT ? OFFSET ?");
  $stmt->bind_param("ssii", $searchQuery, $searchQuery, $limit, $offset);
  $stmt->execute();
  $result = $stmt->get_result();

  // Count total rows
  $stmtTotal = $conn->prepare("SELECT COUNT(*) FROM stock_in_history WHERE StockID LIKE ? OR product_name LIKE ?");
  $stmtTotal->bind_param("ss", $searchQuery, $searchQuery);
  $stmtTotal->execute();
  $stmtTotal->bind_result($totalRows);
  $stmtTotal->fetch();
  $stmtTotal->close();
} else {
  $result = $conn->query("SELECT * FROM stock_in_history ORDER BY StockID LIMIT $limit OFFSET $offset");
  $totalRowsResult = $conn->query("SELECT COUNT(*) AS total FROM stock_in_history");
  $totalRows = $totalRowsResult->fetch_assoc()['total'];
}

$totalPages = max(1, ceil($totalRows / $limit)); // Avoid division by zero
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blacksnow Cafe | Manage Products</title>

    <!-- External CSS & Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Bootstrap & jQuery (Required for Date Range Picker) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- External JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../DesignsCashier/stylecashiermanageproduct.css">
</head>

<body>
    

    <div class="container-fluid vh-100">
        <!-- NAVBAR -->
        <div class="container-fluid mt-3">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link" href="manageproducts.php" onclick="setActiveTab(event)">Manage Product</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="manageaddons.php" onclick="setActiveTab(event)">Stock-In History</a>
                </li>
            </ul>
        </div>


        <!-- Sidebar -->
        <div class="col-md-2 sidebar d-flex flex-column justify-content-between align-items-center">
            <div class="logo-container">
                <img src="../Images_Cashier/sidebar_logo.png" class="logo-img" alt="Admin">
            </div>
            <div class="iconsSidebar d-flex flex-column justify-content-center align-items-center flex-grow-1">
                <a href="manageproducts.php" class="manageProduct" title="Manage Products"><i class="fas fa-box"></i></a>
                <a href="transactionHistory.php" class="transactionHistory" title="Transaction History"><i class="fa-solid fa-receipt"></i></a>
                <a href="../dashboardCashier/Cashierdashboard.php" class="orderList" title="Order Page"><i class="fas fa-list-ul"></i></a>
            </div>
            <div class="profile-container text-center">
                <img src="../Images_Cashier/girl.jpg" class="profile-img" alt="Admin">
            </div>
        </div>

        <!-- Main Content -->
<div class="d-flex flex-column col main-content">
    <div class="col p-4 content">
        <div class="row">
            <div class="col d-flex align-items-center justify-content-between p-0">
                <h3 class="mt-3 menu-title">Stock-In History</h3>
            </div>
        </div>
        

    <!-- Search & Button -->
    <div class="row">
        <div class="col d-flex align-items-center justify-content-between p-0">
            <!-- Search Box -->
            <div class="search-box">
            <form class="d-flex align-items-center" method="GET" action="Stock-InHistory.php">

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
                   
                   

                </div>
            </div>
        </div>
    </div>

    

    <div class="table-responsive mt-4 ms-0"> <!-- Added 'mt-4' for spacing -->
        <table class="table">
            <thead class="table-header">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">Quantity Added</th>
                    <th scope="col">AddedBy</th>
                    <th scope="col">Date</th>

                </tr>
            </thead>
            <tbody class="table-body">
            <?php if ($result->num_rows > 0) : ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row['StockID']; ?></td>
                    <td><?php echo $row['product_name']; ?></td>
                    <td><?php echo $row['QuantityAdded']; ?></td>

                    <td><?php echo $row['AddedBy']; ?></td>
                    
                    <td><?php echo $row['DateAdded']; ?></td>

                    
                </tr>

                 
                         

                <?php endwhile; ?>
        <?php else : ?>
            <tr><td colspan="7">No Stock-In found.</td></tr>
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
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
                crossorigin="anonymous">
        </script>
            <!-- Bootstrap JS (required for modals) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>