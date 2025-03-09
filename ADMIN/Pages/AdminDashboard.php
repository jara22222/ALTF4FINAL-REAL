<?php
include '../Connection/database.php'; // Ensure database connection is included

// Initialize the $search variable
$search = isset($_GET['search']) ? $_GET['search'] : '';

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Query to count total rows (for pagination)
$countQuery = "SELECT COUNT(DISTINCT o.PID) as totalRows
               FROM ordered_items o
               JOIN products p ON o.PID = p.PID
               JOIN categories c ON p.CID = c.CID
               WHERE p.product_name LIKE ? ";

$countStmnt = $conn->prepare($countQuery);
if ($countStmnt) {
    $searchTerm = "%$search%";
    $countStmnt->bind_param("s", $searchTerm);
    $countStmnt->execute();
    $countResult = $countStmnt->get_result();
    $totalRows = $countResult->fetch_assoc()['totalRows'];
    $totalPages = ceil($totalRows / $limit); // Calculate total pages
}

// Query to fetch paginated data
$query = "SELECT o.PID, p.product_name,c.category_name, SUM(qty) solds, p.price, SUM(o.qty * p.price) total_income
          FROM ordered_items o
          JOIN products p ON o.PID = p.PID
          JOIN categories c ON p.CID = c.CID
          WHERE p.product_name LIKE ?
          GROUP BY o.PID
          LIMIT ? OFFSET ?";

$stmnt = $conn->prepare($query);
if ($stmnt) {
    $searchTerm = "%$search%"; // Now $search is defined
    $stmnt->bind_param("sii", $searchTerm, $limit, $offset);
    $stmnt->execute();
    $result = $stmnt->get_result();
}
?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard Sidebar</title>

        <!-- External CSS & Fonts -->
         <script src="../DESIGNS/data-section.js"></script>
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- External JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../designs/style.css">

        <!-- External JS -->

        <script src="../Designs/script.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
        <script src="https://unpkg.com/htmx.org@1.9.6"></script>





    </head>

    <body>
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
                        <ul class="dropdown-menu">
                            <a class="text-truncate" href="Product.php">
                                <li>Products</li>
                            </a>
                            <a class="text-truncate" href="Category.php">
                                <li>Categories</li>
                            </a>
                            <a class="text-truncate" href="Add_ons.php">
                                <li>Add Ons</li>
                            </a>
                        </ul>
                    </li>

                    <li><i class="fas fa-chart-pie"></i> <span>Reports</span></li>
                    <a href="Transaction.php">
                        <li><i class="fas fa-wallet"></i> <span>Transactions</span></li>
                    </a>

                    <a href="Stock-In_History.php">
                        <li><i class="fas fa-wallet"></i> <span>Stock-In History</span></li>
                    </a>



                </ul>

                <ul class="settings-container">
                    <h6 class="menu-title text-truncate px-3">Appearance</h6>
                    <li class="toggle-item">
                        <div class="toggle-switch" onclick="toggleDarkMode()"></div>
                    </li>
                    <li><i class="fas fa-sign-out-alt"></i> <span>Logout</span></li>
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
        <div class="main-content px-0">
            <div class="content">
                <div class="container-fluid px-0 mt-3">
                    <div class="row mx-3 p-2 d-flex">
                        <div class="col">
                            <p class="h3 fw-bold">Welcome to the Dashboard</p>
                            <small class="text-muted">Overview of product and sales summary</small>
                        </div>
                        <div class="col d-flex justify-content-end align-items-end gap-2">
                            <button class="btn btn-outline-secondary"><small>Print</small><i class="bi bi-box-arrow-in-down"></i></button>
                        </div>
                    </div>
                    <hr>

                    <div class="row mx-3 p-2 d-flex">
                    <div class="row">
                        
                            <div class="col stat d-flex stat border shadow-lg p-4">
                                <?php $stmnt = $conn->query("SELECT SUM(qty) unit_sold from ordered_items;");
                                $row = $stmnt->fetch_assoc();
                                ?>
                                <div class="w-100">
                                    <p class="h6 fw-light font-monospace text-secondary">Unit Sold</p>
                                    <p class="display-6 fw-bold font-monospace text-center"><?php echo $row["unit_sold"]?></p>
                                </div>
                            </div>

                        </div>
                    </div>
                
                    <div class="row mx-5 my-5 rounded border pt-5">
                        
                    <div class="d-flex align-items-end">
                                <i class="bi bi-activity text-primary fs-4"></i>&nbsp;&nbsp;<h5>Peromance Charts</h5>
                            </div>


                            <div class="row">
    <div class="col d-flex border-bottom">
        <button class="btn btn-outline-secondary border active" data-section="chart-1">Sales Trends</button>
        <button class="btn btn-outline-secondary border" data-section="chart-2">Product Performance</button>
    </div>
</div>

<!-- Set 1: Sales Trends -->

  <?php
  $stmnt = $conn->query("Select SUM(o.qty) orders,p.product_name from ordered_items o 
JOIN products p on o.PID = p.PID GROUP by p.product_name;");
        $products=[];
        $orders =[];    
        if($stmnt){     
             while($row = $stmnt->fetch_assoc()){
          $products[] = $row["product_name"];
          $orders[] = $row["orders"];
             } 
        }
        
        ?>

    <div class="row mx-0 chart-section chart-1 p-0 d-flex justify-content-center align-items-center">
        <div class="col border">
            <canvas id="barChart" class="w-100"></canvas>
            <input type="text" id="products" value='<?php echo json_encode($products); ?>' hidden>
            <input type="text" id="orders" value='<?php echo json_encode($orders); ?>' hidden>
        </div>

        <?php
        $stmnt = $conn->query("Select sum(TotalAmount)total_sales from orders");
        if($stmnt){     
             $row = $stmnt->fetch_assoc(); 
        }
        ?>

        <div class="col border">
            <canvas id="lineChart" class="w-100"></canvas>
            <h3 id="totalSales">Total Sales: 0</h3>
            <input id="value" value="<?php echo $row['total_sales']?>" type="text" hidden>
            <p>Yesterday's Sales: <span id="yesterdaySales">0</span></p>
        </div>
    </div>


<!-- Set 2: Product Performance -->


  <?php
  $stmnt = $conn->query("Select SUM(o.qty) orders,p.product_name from ordered_items o 
JOIN products p on o.PID = p.PID GROUP by p.product_name;");
        $products=[];
        $values =[];    
        if($stmnt){     
             while($row = $stmnt->fetch_assoc()){
          $products[] = $row["product_name"];
          $values[] = $row["orders"];
             } 
        }
        
        ?>

    <div class="row mx-0 p-0 chart-section chart-2">
    <div class="col-md-12 border d-flex justify-content-center align-items-center" 
        >
        <canvas id="pieChart" class="h-75 w-50"></canvas>
        <input type="text" id="products" value='<?php echo json_encode($products); ?>' hidden>
        <input type="text" id="values" value='<?php echo json_encode($values); ?>' hidden>
    </div>
</div>



      

                    </div>

                    <div class="row mx-5 my-5 rounded border">

                    <div class="my-3"> 
                        
                </div>
                        
                    <div class="d-flex align-items-end">
                                <i class="bi bi-activity text-primary fs-4"></i>&nbsp;&nbsp;<h5>Data Tables</h5>
                            </div>
            


                            <div class="row mx-0 p-0 px-3 justify-content-between rounded d-md-flex">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="">Entries</label>&nbsp;
                                    <select id="entries" class="form-select w-auto d-inline" onchange="changeEntries()">
                                        <option value="" selected disabled>Select entry</option>
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="15">15</option>
                                    </select>
                     <div class="container">
                            <div class="row justify-content-center">
                                <div class="col d-flex justify-content-end align-items-center">
                        <div class="search-container position-relative">
                            <form class="d-flex align-items-center" method="GET" action="">
                                <i class="fas fa-search search-icon"></i>
                                <input class="form-control search-input ps-5" type="search" name="search" placeholder="Search product..."
                                    aria-label="Search" value="<?php echo htmlspecialchars($search); ?>">
                                <button class="btn btn-search ms-2" type="submit">Search</button>
                            </form>
                        </div>
                    </div>
                            </div>
                            </div>
                        </div>
                            </div>
                            <div class="row mx-0 table-section my-5 p-0 d-flex justify-content-center align-items-center text-center">
            <table class="table table-responsive table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Sold Today</th>
                        <th>Price</th>
                        <th>Total Product Income</th>
                    </tr>
                </thead>
                            <tbody id="table-body">
                                <?php if (isset($result) && $result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row["PID"]); ?></td>
                                            <td><?php echo htmlspecialchars($row["product_name"]); ?></td>
                                            <td><?php echo htmlspecialchars($row["category_name"]); ?></td>
                                            <td><?php echo htmlspecialchars($row["solds"]); ?></td>
                                            <td>₱ <?php echo number_format($row["price"], 2); ?></td>
                                            <td>₱ <?php echo number_format($row["total_income"], 2); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align:center;">No data found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                            <hr>
                            <div class="d-flex justify-content-end">
                               <nav aria-label="Page navigation example">
    <ul class="pagination">
        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?>&search=<?php echo urlencode($search); ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Previous</span>
            </a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?>&search=<?php echo urlencode($search); ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Next</span>
            </a>
        </li>
    </ul>
</nav>
                            </div>
                                                    
                            </div>   
                            
                            
                    </div>


                
            </div>
            
        </div>

        <script>
            function toggleDarkMode() {
                document.body.classList.toggle("dark-mode");
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

<script>

    function changeEntries() {
    const entries = document.getElementById("entries").value; // Get the selected value
    const search = "<?php echo htmlspecialchars($search); ?>"; // Get the current search term
    const page = "<?php echo $page; ?>"; // Get the current page

    // Construct the new URL with the updated limit
    const newUrl = `?page=${page}&limit=${entries}&search=${encodeURIComponent(search)}`;

    // Redirect to the new URL
    window.location.href = newUrl;
}
  document.addEventListener("DOMContentLoaded", function () {
    let buttons = document.querySelectorAll("[data-section]");
    let sections = document.querySelectorAll(".chart-section");

    if (buttons.length === 0 || sections.length === 0) {
        console.error("Buttons or sections are missing!");
        return;
    }

    // Hide all sections initially
    sections.forEach(section => section.classList.add("d-none"));

    // Show the first section by default
    let firstSection = document.querySelector(`.${buttons[0].getAttribute("data-section")}`);
    if (firstSection) firstSection.classList.remove("d-none");

    // Add event listeners to buttons
    buttons.forEach(button => {
        button.addEventListener("click", function () {
            // Remove active class from all buttons
            buttons.forEach(btn => btn.classList.remove("active"));
            this.classList.add("active");

            // Hide all sections
            sections.forEach(section => section.classList.add("d-none"));

            // Show the clicked section
            let targetSection = document.querySelector(`.${this.getAttribute("data-section")}`);
            if (targetSection) targetSection.classList.remove("d-none");
        });
    });
});

</script>



    </body>
    </html>
