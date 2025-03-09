<?php
include "../authentication/authenticated.php";
include("../Connection/database.php"); // Include the database connection


$limit = 5; // Number of records per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$searchQuery = "";
$params = [];
$sql = "SELECT * FROM employee_details";

if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
  $searchQuery = "%" . $_GET['search_query'] . "%";
  $sql .= " WHERE fullname LIKE ? OR roleName LIKE ?";
  $params = [$searchQuery, $searchQuery];
}

$sql .= " ORDER BY EID LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $conn->prepare($sql);

if (!empty($params)) {
  $stmt->bind_param(str_repeat("s", count($params) - 2) . "ii", ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Get total records for pagination
$countQuery = "SELECT COUNT(*) AS total FROM employee_details ";
if (!empty($searchQuery)) {
  $countQuery .= " WHERE fullname LIKE ? OR roleName LIKE ?";
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

                <li><i class="fas fa-chart-pie"></i> <span>Reports</span></li>
                <a href="Transaction.php">  <li><i class="fas fa-wallet"></i> <span>Transactions</span></li></a>

              <a href="Stock-In_History.php">  <li><i class="fas fa-wallet"></i> <span>Stock-In History</span></li></a>
            </ul>

            <ul class="settings-container">
                <h6 class="menu-title text-truncate px-3">Appearance</h6>
                <li class="toggle-item">
                    <div class="toggle-switch" onclick="toggleDarkMode()"></div>
                </li>
                  <a href="../login.php">
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
    <div class="main-content px-0">
        <div class="content">
            <div class="container-fluid px-0 mt-3">
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

                <div class="row ">
                    <div class="col d-flex align-items-center justify-content-between p-0">
                        <h3 class="mt-3 page-title">Manage Employee</h3>
                    </div>
                </div>


                <!-- Search & Button -->
                <div class="row">
                    <div class="col d-flex align-items-center justify-content-between p-0">
                        <!-- Search Box -->
                        <div class="search-box">
            <form class="d-flex align-items-center" method="GET" action="Employee.php">

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
                                <form id="employeeForm" action="../Handlers/addEmployee_handler.php" method="POST"
                                    enctype="multipart/form-data">
                                    <button type="button"
                                        class="employee-details d-flex justify-content-center align-items-center"
                                        data-bs-toggle="modal" data-bs-target="#addemployee">
                                        <i class="bi bi-plus-square mx-2"></i>
                                        <span class="text-center">Add Employee</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive mt-4">
                    <table class="table">
                        <thead class="table-header">
                            <tr>
                                <th>ID</th>
                                <th>Fullname</th>
                                <th>Position</th>
                                <th>Details</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-body">
                            <?php
                            

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?= $row['EID']; ?></td>
                                        <td><?= htmlspecialchars($row['fullname']); ?></td>
                                        <td><?= htmlspecialchars($row['roleName']); ?></td>
                                        <td class="d-flex justify-content-center">
                                            <button type="button" class="btn btn-primary justify-content-center"
                                                data-bs-toggle="modal" data-bs-target="#detailsModal<?= $row['EID']; ?>">
                                                See More
                                            </button>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editModal<?= $row['EID']; ?>">
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>
                                                <!-- Delete Button -->
                                                <form action="../Handlers/deleteEmployee_handler.php" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this role?');">
                                                    <input type="hidden" name="EID"
                                                        value="<?php echo htmlspecialchars($row['EID']); ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Details Modal -->
                                    <div class="modal fade" id="detailsModal<?= $row['EID']; ?>" tabindex="-1"
                                        aria-labelledby="detailsModalLabel<?= $row['EID']; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="detailsModalLabel<?= $row['EID']; ?>">Employee
                                                        Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-12 mb-3 profile text-center">
                                                            <img class="profile-size img-fluid"
                                                                src="data:image/jpeg;base64,<?= base64_encode($row['profile']); ?>"
                                                                alt="Profile Picture">
                                                        </div>
                                                        <div class="col-12 empDetails">
                                                            <h5 class="mb-4 text-center empName">
                                                                <?= htmlspecialchars($row['fullname']); ?>
                                                            </h5>
                                                            <p><strong>Position:</strong>
                                                                <?= htmlspecialchars($row['roleName']); ?></p>
                                                            <p><strong>Email:</strong> <?= htmlspecialchars($row['email']); ?>
                                                            </p>
                                                            <p><strong>Phone:</strong>
                                                                <?= htmlspecialchars($row['phone_num']); ?></p>
                                                            <p><strong>Address:</strong>
                                                                <?= htmlspecialchars($row['street'] . " " . $row['city'] . " " . $row['province'] . " " . $row['zipcode']); ?>
                                                            </p>
                                                            <p><strong>Joined Date:</strong>
                                                                <?= htmlspecialchars($row['date_hired']); ?></p>
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

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal<?= $row['EID']; ?>" tabindex="-1"
                                        aria-labelledby="editModalLabel<?= $row['EID']; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel<?= $row['EID']; ?>">Edit Employee
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="../Handlers/updateEmployee_handler.php" method="POST"
                                                        enctype="multipart/form-data">
                                                        <input type="hidden" name="EID" value="<?= $row['EID']; ?>">

                                                        <div class="row">
                                                            <!-- Left Column -->
                                                            <div class="col-md-6">
                                                                <?php
                                                                // Split the 'fullname' column into an array
                                                                $nameParts = explode(' ', $row['fullname']);

                                                                // Extract first, middle, and last names
                                                                $firstName = isset($nameParts[0]) ? $nameParts[0] : '';
                                                                $middleName = isset($nameParts[1]) ? $nameParts[1] : '';
                                                                $lastName = isset($nameParts[2]) ? $nameParts[2] : '';
                                                                ?>

                                                                <label for="fn">First Name:</label>
                                                                <input type="text" id="fn" name="fn" class="form-control"
                                                                    value="<?php echo $firstName; ?>" required>

                                                                <label for="mid" class="mt-3">Middle Name:</label>
                                                                <input type="text" id="mid" name="mid" class="form-control"
                                                                    value="<?php echo $middleName; ?>">

                                                                <label for="ln" class="mt-3">Last Name:</label>
                                                                <input type="text" id="ln" name="ln" class="form-control"
                                                                    value="<?php echo $lastName; ?>" required>


                                                                <label for="birthday_<?= $row['EID']; ?>"
                                                                    class="mt-3">Birthday:</label>
                                                                <input type="date" id="birthday_<?= $row['EID']; ?>"
                                                                    name="birthday" class="form-control"
                                                                    value="<?= $row['bday']; ?>" required>

                                                                <label for="gender_<?= $row['EID']; ?>"
                                                                    class="mt-3">Gender:</label>
                                                                <select id="gender_<?= $row['EID']; ?>" name="gender"
                                                                    class="form-control mb-3" required>
                                                                    <option value="M" <?= $row['gender'] === 'M' ? 'selected' : ''; ?>>Male</option>
                                                                    <option value="F" <?= $row['gender'] === 'F' ? 'selected' : ''; ?>>Female</option>
                                                                    <option value="N/A" <?= $row['gender'] === 'N/A' ? 'selected' : ''; ?>>Other</option>
                                                                </select>
                                                            </div>

                                                            <!-- Right Column -->
                                                            <div class="col-md-6">
                                                                <label for="email_<?= $row['EID']; ?>">Email:</label>
                                                                <input type="email" id="email_<?= $row['EID']; ?>" name="email"
                                                                    class="form-control"
                                                                    value="<?= htmlspecialchars($row['email']); ?>" required>

                                                                <label for="phone_num<?= $row['EID']; ?>"
                                                                    class="mt-3">Phone:</label>
                                                                <input type="text" id="phone_<?= $row['EID']; ?>"
                                                                    name="phone_num" class="form-control"
                                                                    value="<?= htmlspecialchars($row['phone_num']); ?>"
                                                                    required>

                                                                <label for="street_<?= $row['EID']; ?>"
                                                                    class="mt-3">Street:</label>
                                                                <input type="text" id="street_<?= $row['EID']; ?>" name="street"
                                                                    class="form-control"
                                                                    value="<?= htmlspecialchars($row['street']); ?>" required>
                                                                <label for="city" class="mt-3">City:</label>
                                                                <input type="text" id="city" name="city" class="form-control"
                                                                    value="<?php echo $row['city']; ?>" required>

                                                                <label for="province" class="mt-3">Province:</label>
                                                                <input type="text" id="province" name="province"
                                                                    class="form-control" value="<?php echo $row['province']; ?>"
                                                                    required>

                                                                <label for="zipcode" class="mt-3">Zip Code:</label>
                                                                <input type="text" id="zipcode" name="zipcode"
                                                                    class="form-control mb-3"
                                                                    value="<?php echo $row['zipcode']; ?>" required>

                                                                <label for="role" class="mt-3">Role:</label>
                                                                <select id="role" name="role" class="form-control mb-3"
                                                                    required>
                                                                    <?php
                                                                    $query2 = "SELECT * FROM roles";
                                                                    $result2 = $conn->query($query2);
                                                                    while ($row3 = $result2->fetch_assoc()) {
                                                                        $selected = ($row['roleName'] == $row3['rolename']) ? 'selected' : '';
                                                                        echo '<option value="' . $row3['RID'] . '" ' . $selected . '>' . $row3['rolename'] . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>

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
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="5" class="text-center">No employees found.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>


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



        <!-- MODALS -->

        <!-- Add Employee Modal -->
        <div class="modal fade" id="addemployee" tabindex="-1" aria-labelledby="infoModalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEmployeeModalLabel">Add Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="employeeForm" action="../Handlers/addEmployee_handler.php" method="POST"
                            enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-5">
                                    <label for="fn">First Name:</label>
                                    <input type="text" id="fn" name="fn" class="form-control" required>

                                    <label for="ln" class="mt-3">Last Name:</label>
                                    <input type="text" id="ln" name="ln" class="form-control" required>

                                    <label for="mid" class="mt-3">Middle Name:</label>
                                    <input type="text" id="mid" name="mid" class="form-control">

                                    <label for="birthday" class="mt-3">Birthday:</label>
                                    <input type="date" id="birthday" name="birthday" class="form-control" required>

                                    <label for="age" class="mt-3">Age:</label>
                                    <input type="number" id="age" name="age" class="form-control" required>

                                    <label for="username" class="mt-3">Username:</label>
                                    <input type="text" id="username" name="username" class="form-control" required>

                                    <label for="gender" class="mt-3">Gender:</label>
                                    <select id="gender" name="gender" class="form-control mb-3" required>
                                        <option value="">Select Gender</option>
                                        <option value="M">Male</option>
                                        <option value="F">Female</option>
                                        <option value="N/A">Other</option>
                                    </select>
                                </div>

                                <div class="col-6">
                                    <label for="email">Email:</label>
                                    <input type="email" id="email" name="email" class="form-control" required>

                                    <label for="phone_num" class="mt-3">Phone Number:</label>
                                    <input type="text" id="phonenumber" name="phone_num" class="form-control" required>

                                    <label for="street">Street:</label>
                                    <input type="text" id="street" name="street" class="form-control" required>

                                    <label for="city" class="mt-3">City:</label>
                                    <input type="text" id="city" name="city" class="form-control" required>

                                    <label for="province" class="mt-3">Province:</label>
                                    <input type="text" id="province" name="province" class="form-control" required>

                                    <label for="zipcode" class="mt-3">Zip Code:</label>
                                    <input type="text" id="zipcode" name="zipcode" class="form-control mb-3" required>

                                    <label for="roles" class="mt-3">Role:</label>
                                    <select id="role" name="role" class="form-control mb-3" required>
                                        <option value="">Select Role</option>
                                        <?php
                                        $query2 = "SELECT * FROM roles";
                                        $result2 = $conn->query($query2);
                                        while ($row3 = $result2->fetch_assoc()) {
                                            echo '<option value="' . $row3['RID'] . '">' . $row3['rolename'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <label class="form-label mb-2 fonts-2">File Upload</label>
                                <input name="img" type="file" class="form-control-file" accept="image/*" required>
                            </div>

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
    </div>
    </div>

    <!-- CUSTOM JS -->
    <script>
        function toggleDarkMode() {
            document.body.classList.toggle("dark-mode");
        }

        function toggleDropdown(element) {
        }
    </script>

    <script>
        document.getElementById("employeeForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
            this.submit();
        });

        document.getElementById('employeeForm').addEventListener('submit', function (event) {
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
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
    <!-- Bootstrap JS (required for modals) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>