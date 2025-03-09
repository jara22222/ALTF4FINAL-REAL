<?php
// Database connection
include "../authentication/authenticated.php";
include '../Connection/Database.php';

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$searchQuery = "";

if (!empty($_GET['search_query'])) {
  $searchQuery = "%" . $_GET['search_query'] . "%";
  $stmt = $conn->prepare("SELECT * FROM order_summary_view WHERE OrderID LIKE ? OR CashierName LIKE ? LIMIT ? OFFSET ?");
  $stmt->bind_param("ssii", $searchQuery, $searchQuery, $limit, $offset);
  $stmt->execute();
  $result = $stmt->get_result();

  // Count total rows
  $stmtTotal = $conn->prepare("SELECT COUNT(*) FROM order_summary_view WHERE OrderID LIKE ? OR CashierName LIKE ?");
  $stmtTotal->bind_param("ss", $searchQuery, $searchQuery);
  $stmtTotal->execute();
  $stmtTotal->bind_result($totalRows);
  $stmtTotal->fetch();
  $stmtTotal->close();
} else {
  $result = $conn->query("SELECT * FROM order_summary_view ORDER BY OrderID LIMIT $limit OFFSET $offset");
  $totalRowsResult = $conn->query("SELECT COUNT(*) AS total FROM order_summary_view");
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
    <link rel="stylesheet" href="../designs/stylecashiermanageproduct.css">
</head>  <link rel="stylesheet" href="../designs/stylecashiermanageproduct.css">

<body>
    

    <div class="container-fluid vh-100">

        <!-- Sidebar -->
        <div class="col-md-2 sidebar d-flex flex-column justify-content-between align-items-center">
            <div class="logo-container">
                <img src="../Images_Cashier/sidebar_logo.png" class="logo-img" alt="Admin">
            </div>
            <div class="iconsSidebar d-flex flex-column justify-content-center align-items-center flex-grow-1">
                <a href="manageproducts.php" class="manageProduct" title="Manage Products"><i class="fas fa-box"></i></a>
                <a href="transactionHistory.php" class="transactionHistory" title="Transaction History"><i class="fa-solid fa-receipt"></i></a>
                <a href="../pages/Cashierdashboard.php" class="orderList" title="Order Page"><i class="fas fa-list-ul"></i></a>
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
                <h3 class="mt-3 menu-title">Transaction History</h3>
            </div>
        </div>
        

    <!-- Search & Button -->
    <div class="row">
        <div class="col d-flex align-items-center justify-content-between p-0">
            <!-- Search Box -->
            <div class="search-box">
            <form class="d-flex align-items-center" method="GET" action="transactionHistory.php">

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

    

    <div class="table-responsive mt-4 ms-0 "> <!-- Added 'mt-4' for spacing -->
        <table class="table">
            <thead class="table-header">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">CashierName</th>
                    <th scope="col">Total Amount</th>
                    <th scope="col">Details</th>
                    <th scope="col">Date</th>

                </tr>
            </thead>
            <tbody class="table-body">
            <?php if ($result->num_rows > 0) : ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row['OrderID']; ?></td>
                    <td><?php echo $row['CashierName']; ?></td>
                    <td>P&nbsp;<?php echo $row['TotalAmount']; ?></td>

                    <td> <button type="button" class="btn btn-info btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#infoModal<?php echo $row['OrderID']; ?>">
                            See More&nbsp;<i class="fa-solid fa-ellipsis"></i>
                        </button></td>
                    
                    
 <td><?php echo date('F j, Y g:i A', strtotime($row['OrderDate'])); ?></td>
                    
                </tr>

                  <!-- Info Modal -->
                  <div class="modal fade" id="infoModal<?php echo $row['OrderID']; ?>" tabindex="-1"
                            aria-labelledby="infoModalLabel<?php echo $row['OrderID']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="infoModalLabel<?php echo $row['OrderID']; ?>">Transaction Details
                                  </h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <div class="row">
                                    <div class="col-12">
                                      <h5 class="mt-3 mb-4 text-center">
                                        <?php echo htmlspecialchars($row['CashierName']); ?></h5>
                                      <p><strong>Products:</strong> <?php echo htmlspecialchars($row['ordered_products']); ?>
                                      </p>
                                      <p><strong>Add_ons:</strong> <?php echo htmlspecialchars($row['ordered_addons']); ?>
                                      </p>
                                      <p><strong>AmountPaid:</strong> <?php echo htmlspecialchars($row['AmountPaid']); ?>
                                      </p>
                                      <p><strong>PaymentMethod:</strong> <?php echo htmlspecialchars($row['PaymentMethod']); ?>
                                      </p>
                                     
                                    </div>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                              </div>
                            </div>
                          </div>

                         

                <?php endwhile; ?>
        <?php else : ?>
            <tr><td colspan="7">No transaction found.</td></tr>
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