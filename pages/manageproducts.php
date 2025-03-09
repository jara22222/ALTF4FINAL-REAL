<?php
include "../authentication/authenticated.php";
include '../Connection/Database.php';


// Get the selected category from the dropdown
$category = isset($_GET['category']) ? $_GET['category'] : 'All Categories';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Modify the query to filter by category and search term
$query = "SELECT * FROM product_view WHERE 1";

// Filter by category
if ($category !== 'All Categories') {
    $query .= " AND category_name = '$category'";
}

// Filter by search term
if (!empty($searchTerm)) {
    $query .= " AND product_name LIKE '%$searchTerm%'";
}

// Order the products by type
$query .= " ORDER BY category_name";

$products = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blacksnow Cafe | Manage Products</title>

    <!-- External CSS & Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
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
</head>

<body>
    <div class="container-fluid vh-100">
        <!-- NAVBAR -->
        <div class="container-fluid mt-3">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="manageproducts.php" onclick="setActiveTab(event)">Manage
                        Product</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../pages/stock-InHistory.php" onclick="setActiveTab(event)">Stock-in History</a>
                </li>
            </ul>
        </div>

        <!-- Sidebar -->
        <div class="col-md-2 sidebar d-flex flex-column justify-content-between align-items-center">
            <div class="logo-container">
                <img src="../Images_Cashier/sidebar_logo.png" class="logo-img" alt="Admin">
            </div>
            <div class="iconsSidebar d-flex flex-column justify-content-center align-items-center flex-grow-1">
                <a href="manageproducts.php" class="manageProduct" title="Manage Products"><i
                        class="fas fa-box"></i></a>
                <a href="transactionHistory.php" class="transactionHistory" title="Transaction History"><i
                        class="fa-solid fa-receipt"></i></a>
                <a href="../pages/Cashierdashboard.php" class="orderList" title="Order Page"><i class="fas fa-list-ul"></i></a>
            </div>
            <div class="profile-container text-center">
                <img src="../Images_Cashier/girl.jpg" class="profile-img" alt="Admin">
            </div>
        </div>

        <!-- Main Content -->
        <div class="d-flex flex-column col main-content ">
            <div class="col p-4 content ">
                <div class="row">
                    <div class="col d-flex align-items-center justify-content-between p-0">
                        <h3 class="mt-3 menu-title">Stock-In </h3>
                        <div class="d-flex align-items-center justify-content-end">

                            <!-- Dropdown Filter -->
                            <div class="dropdown  ">
                                <button class="btn  dropdown-toggle filter-dropdown " type="button"
                                    id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-filter"></i>
                                    <span>Categories</span>
                                </button>
                                <ul class="dropdown-menu " aria-labelledby="categoryDropdown">
                                    <li><a class="dropdown-item"
                                            href="?category=All Categories&search=<?php echo $searchTerm; ?>">All
                                            Categories</a></li>
                                    <li><a class="dropdown-item"
                                            href="?category=Drink&search=<?php echo $searchTerm; ?>">Drinks</a></li>
                                    <li><a class="dropdown-item"
                                            href="?category=Meal&search=<?php echo $searchTerm; ?>">Rice Meals</a>
                                    </li>
                                    <li><a class="dropdown-item"
                                            href="?category=Pastry&search=<?php echo $searchTerm; ?>">Pastries</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search & Button -->
                <div class="row">
                    <div class="col d-flex align-items-center justify-content-between p-0">

                        <!-- Display success message -->
                <?php if (!empty($_SESSION['success'])): ?>
                  <script>
                    alert("<?php echo $_SESSION['success']; ?>");
                  </script>
                  <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <!-- Display errors -->
                <?php if (!empty($_SESSION['errors'])): ?>
                  <div class="alert alert-danger">
                    <?php echo $_SESSION['errors']; ?>
                    <?php unset($_SESSION['errors']); ?>
                  </div>
                <?php endif; ?>

                        <form method="get" action="">

                            <!-- Search Box -->
                            <div class="search-box">

                                <i class="fas fa-search"></i>
                                <input type="text" name="search" value="<?php echo $searchTerm; ?>"
                                    placeholder="Search Products">
                                <input type="hidden" name="category" value="<?php echo $category; ?>">
                                <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive ">
            <table class="table">
                <thead class="table-header">
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Availability</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Date</th>
                        <th>Added By</th>
                        <th>Add Stock</th>
                    </tr>
                </thead>
                <?php
                if ($products->num_rows > 0) {
                    while ($product = $products->fetch_assoc()) {
                        ?>
                        <tbody class="table-body">
                            <tr>
                                <td><?php echo $product['product_name']; ?></td>
                                <td><?php echo $product['category_name']; ?></td>
                                <td>
                                <div class="form-check form-switch custom-switch"> 
    <input class="form-check-input" type="checkbox" id="customSwitch" 
        <?php echo ($product['product_qty'] == 0) ? 'disabled' : 'checked'; ?>>
</div>

                                </td>
                                <td><?php echo $product['sprice']; ?></td>
                                <td><?php echo $product['product_qty']; ?></td>
                                <td><?php echo date('F j, Y g:i A', strtotime($product['date'])); ?></td>
                                <td>Admin</td>
                                <td class="btn-table">
                                    <button class="btn btn-success btn-sm btn-edit" data-bs-toggle="modal"
                                        data-bs-target="#editModal<?php echo $product['PID']; ?>">
                                        <i class="fa-solid fa-plus "></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>



                        <!-- Modal EDIT -->
                        <div class="modal fade" id="editModal<?php echo $product['PID']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $product['PID']; ?>"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h2 class="modal-title" id="editModalLabel<?php echo $product['PID']; ?>">Edit Stock Item</h2>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <form class="add-form" action="../handlers/stock_handler.php" method="POST">
                                            <!-- Item Name (Read-Only) -->
                                            <div class="mb-3">
                                                <label for="itemName" class="form-label">Item Name</label>

                                                <input type="hidden" value="<?php echo $product['PID']; ?>" name="PID">
                                                <input type="text" id="itemName" class="form-control"
                                                    value="<?php echo $product['product_name']; ?>" readonly>
                                            </div>

                                            <!-- Category (Read-Only) -->
                                            <div class="mb-3">
                                                <label for="category" class="form-label">Category</label>
                                                <input type="text" id="category" class="form-control" value="<?php echo $product['category_name']; ?>" readonly>
                                            </div>

                                            <!-- Price (Read-Only) -->
                                            <div class="mb-3">
                                                <label for="price" class="form-label">Price</label>
                                                <input type="text" id="price" class="form-control" value="<?php echo $product['sprice']; ?>" readonly>
                                            </div>

                                            <!-- Stock (Editable) -->
                                            <div class="mb-3">
                                                <label for="stock" class="form-label">Stock</label>
                                                <input type="number" name="stock" id="stock" class="form-control"
                                                    placeholder="Enter stock quantity" value="">
                                            </div>

                                            <!-- Modal Actions -->
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php }
                } ?>
            </table>
        </div>
    </div>
    </div>







    <!-- JAVASCRIPT -->
    <script>
        $(document).ready(function () {
            // Initialize Date Range Picker
            $('#daterange').daterangepicker({
                autoUpdateInput: false, // Prevents auto-filling before selection
                locale: {
                    cancelLabel: 'Clear',
                    format: 'MM/DD/YYY' // Adjust format as needed
                }
            });

            // Show calendar when clicking the input field or the icon
            $('#calendar-icon').click(function () {
                $('#daterange').focus(); // Opens the date picker when the icon is clicked
            });

            // Update input when dates are selected
            $('#daterange').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYY') + ' - ' + picker.endDate.format('MM/DD/YYY'));
            });

            // Clear input when 'Clear' is clicked
            $('#daterange').on('cancel.daterangepicker', function () {
                $(this).val('');
            });
        });

        // Navbar Active Class

        function setActiveTab(event) {
            let clickedTab = event.currentTarget;

            // Check if the clicked tab is already active
            if (clickedTab.classList.contains("active")) {
                clickedTab.classList.remove("active"); // Remove active state (back to default)
            } else {
                // Remove 'active' class from all tabs
                document.querySelectorAll(".nav-tabs .nav-link").forEach(tab => {
                    tab.classList.remove("active");
                });

                // Add 'active' class to the clicked tab
                clickedTab.classList.add("active");
            }
        }

    </script>


</body>

</html>