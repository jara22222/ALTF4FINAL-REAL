<?php
include '../Connection/Database.php';
SESSION_START();

$drinks = $conn->query("SELECT * FROM product_view WHERE category_name = 'Drink' AND product_qty  != 0");
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blacksnow Cafe | Order Page</title>

    <!-- External CSS & Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- External JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../designs/stylecashierdashboard.css">

    <style>
    .modal {
        z-index: 1050 !important;
    }

    .modal-backdrop {
        z-index: 1040 !important;
        background-color: rgba(0, 0, 0, 0.5);
        /* Optional: Adjust transparency */
    }

    /* Print-specific styles */
    @media print {
        body {
            width: 58mm;
            margin: 0;
            padding: 0;
        }

        .mdoal-body-receipt {
            width: 100%;
            padding: 0;
        }
    }
    </style>
</head>

<body>


    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column justify-content-between align-items-center">
        <!-- Logo-->
        <div class="logo-container">
            <img src="../Images_Cashier/sidebar_logo.png" class="logo-img" alt="Admin">
        </div>

        <!-- Icons Container -->
        <div class="iconsSidebar d-flex flex-column justify-content-center align-items-center flex-grow-1">
            <a href="../sidebarCashier/manageproducts.php" class="manageProduct" title="Manage Products"><i
                    class="fas fa-box"></i></a>
            <a href="../sidebarCashier/transactionHistory.php" class="transactionHistory" title="Transaction History"><i
                    class="fa-solid fa-receipt"></i></a>
            <a href="Cashierdashboard.php" class="orderList" title="Order Page"><i class="fas fa-list-ul"></i></a>
        </div>

        <!-- Profile Image-->
        <div class="profile-container text-center">
            <img src="../Images_Cashier/girl.jpg" class="profile-img" alt="Admin">
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid vh-100 p-0">
        <div class="row h-100 p-0 mx-5">
            <!-- Main Content -->
            <div class="col-md-8 content">
                <h3 class="mt-3 menu-title">Order</h3>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search for foods, drinks, etc.">
                </div>

                <!-- Category Tabs -->
                <ul class="nav nav-tabs mt-4">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#drinks">Drinks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#pastries">Pastries</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#meals">Meals</a>
                    </li>
                </ul>


                <div class="tab-content mt-4">
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

                    <!-- Drinks Section -->
                    <div class="tab-pane fade show active" id="drinks">
                        <div class="row">
                            <div class="product-container">
                                <?php
                                if($drinks->num_rows > 0) {
                                    while ($drink = $drinks->fetch_assoc()) {
                                        ?>
                                <div class="col mb-3">
                                    <input type="hidden" name="PID" value="<?php echo $drink['PID']; ?>">
                                    <div class="food-card" data-bs-toggle="modal"
                                        data-bs-target="#DrinksfoodModal<?php echo $drink['PID']; ?>"
                                        style="cursor: pointer;">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($drink['image']); ?>"
                                            alt="Product Image" style="width: 50%;">
                                        <div class="food-title"><?php echo $drink['product_name']; ?></div>
                                        <div class="food-price"><?php echo $drink['sprice']; ?></div>
                                    </div>
                                </div>

                                <!-- COFFEE MODAL -->
                                <div class="modal fade" id="DrinksfoodModal<?php echo $drink['PID']; ?>" tabindex="-1"
                                    aria-labelledby="DrinksfoodModalLabel<?php echo $drink['PID']; ?>"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="DrinksfoodModalLabel<?php echo $drink['PID']; ?>">Drinks Options
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body align-text-center">
                                                <h6>Sizes</h6>
                                                <div class="btn-container">
                                                    <?php
                                                            $s = $drink['PID'];
                                                            $sizes = $conn->prepare("SELECT * FROM product_view WHERE PID = ? AND product_qty != 0");
                                                            $sizes->bind_param("s", $s);
                                                            $sizes->execute();
                                                            $sizes = $sizes->get_result();
                                                            if ($sizes->num_rows > 0) {
                                                                while ($size = $sizes->fetch_assoc()) {
                                                                    ?>
                                                    <button class="btn-modal size-btn" data-size="Small"
                                                        data-price="<?php echo $size['sprice']; ?>">Small
                                                        (₱<?php echo $size['sprice']; ?>)
                                                    </button>
                                                    <button class="btn-modal size-btn" data-size="Medium"
                                                        data-price="<?php echo $size['mprice']; ?>">Medium
                                                        (₱<?php echo $size['mprice']; ?>)
                                                    </button>
                                                    <button class="btn-modal size-btn" data-size="Large"
                                                        data-price="<?php echo $size['lprice']; ?>">Large
                                                        (₱<?php echo $size['lprice']; ?>)
                                                    </button>
                                                    <?php }
                                                            } ?>
                                                </div>

                                                <h6 class="mt-3">Add-Ons</h6>
                                                <div class="btn-container">
                                                    <?php
                                                            $addOns = $conn->query("SELECT * FROM adds_on WHERE CID = 'CD-001'");
                                                            if ($addOns->num_rows > 0) {
                                                                while ($addOn = $addOns->fetch_assoc()) {
                                                                    ?>
                                                    <button class="btn-modal addon-btn"
                                                        data-addon="<?php echo $addOn['add_name']; ?>"
                                                        data-price="<?php echo $addOn['price']; ?>">
                                                        <?php echo $addOn['add_name']; ?>
                                                        (₱<?php echo $addOn['price']; ?>)
                                                    </button>
                                                    <?php }
                                                            } ?>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="button" class="btn btn-primary add-order-btn"
                                                    data-pid="<?php echo $drink['PID']; ?>"
                                                    data-product="<?php echo $drink['product_name']; ?>">
                                                    Add Order
                                                </button>


                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php }
                                } ?>
                            </div>
                        </div>
                    </div>


                    <!-- Pastries Section -->
                    <div class="tab-pane fade" id="pastries">
                        <div class="row">
                            <div class="product-container">
                                <?php
                                $pastries = $conn->query("SELECT * FROM product_view WHERE category_name = 'Pastry' AND product_qty != 0");
                                if ($pastries->num_rows > 0) {
                                    while ($pastry = $pastries->fetch_assoc()) {
                                        $pastryId = $pastry['PID']; // Unique ID for each pastry
                                        ?>
                                <div class="col mb-3">
                                    <!-- ✅ Unique Modal ID -->
                                    <div class="food-card" data-bs-toggle="modal"
                                        data-bs-target="#PastryfoodModal<?php echo $pastryId; ?>"
                                        style="cursor: pointer;">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($pastry['image']); ?>"
                                            alt="Pastry Image">
                                        <div class="food-title"><?php echo $pastry['product_name']; ?></div>
                                        <div class="food-price">₱<?php echo number_format($pastry['mprice'], 2); ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- ✅ Modal for each Pastry (No Size & Add-ons) -->
                                <div class="modal fade" id="PastryfoodModal<?php echo $pastryId; ?>" tabindex="-1"
                                    aria-labelledby="PastryModalLabel<?php echo $pastryId; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="PastryModalLabel<?php echo $pastryId; ?>">
                                                    <?php echo $pastry['product_name']; ?>
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($pastry['image']); ?>"
                                                    class="img-fluid mb-3" alt="Pastry Image">
                                                <p><strong>Price:</strong>
                                                    ₱<?php echo number_format($pastry['mprice'], 2); ?></p>

                                                <!-- ✅ Add to Order Button (Only Product Name & Price) -->
                                                <button class="btn btn-primary add-order-btn"
                                                    data-product="<?php echo $pastry['product_name']; ?>"
                                                    data-price="<?php echo $pastry['mprice']; ?>" data-pid="<?php echo $pastryId;
                                                               ['PID']; ?>">
                                                    Add to Order
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php }
                                } ?>
                            </div>
                        </div>
                    </div>


                    <!-- Meals Section -->
                    <div class="tab-pane fade" id="meals">
                        <div class="row">
                            <div class="product-container">
                                <?php
                                $meals = $conn->query("SELECT * FROM product_view WHERE category_name = 'Meal' AND product_qty != 0");
                                if ($meals->num_rows > 0) {
                                    while ($meal = $meals->fetch_assoc()) {
                                        ?>
                                <div class="col mb-3">
                                    <div class="food-card" data-bs-toggle="modal"
                                        data-bs-target="#RiceMealfoodModal<?php echo $meal['PID']; ?>"
                                        style="cursor: pointer;">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($meal['image']); ?>"
                                            alt="Meal Image">
                                        <div class="food-title"><?php echo $meal['product_name']; ?></div>
                                        <div class="food-price"><?php echo $meal['mprice']; ?></div>
                                    </div>
                                </div>


                                <!-- MEALS MODAL -->
                                <div class="modal fade" id="RiceMealfoodModal<?php echo $meal['PID']; ?>" tabindex="-1"
                                    aria-labelledby="RiceMealfoodModalLabel<?php echo $meal['PID']; ?>"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="RiceMealfoodModalLabel<?php echo $meal['PID']; ?>">
                                                    <?php echo $meal['product_name']; ?> - Options
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body align-text-center">
                                                <h6>Add-Ons</h6>
                                                <div class="btn-container">
                                                    <?php
                                                            $addOns = $conn->query("SELECT * FROM adds_on WHERE add_name IN ('Plain Rice', 'Gravy Sauc')");

                                                            if ($addOns->num_rows > 0) {
                                                                while ($addOn = $addOns->fetch_assoc()) {
                                                                    ?>
                                                    <button class="btn-modal addon-btn"
                                                        data-addon="<?php echo $addOn['add_name']; ?>"
                                                        data-price="<?php echo $addOn['price']; ?>">
                                                        <?php echo $addOn['add_name']; ?>
                                                        (₱<?php echo $addOn['price']; ?>)
                                                    </button>
                                                    <?php }
                                                            } ?>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="button" class="btn btn-primary add-order-btn"
                                                    data-pid="<?php echo $meal['PID']; ?>"
                                                    data-product="<?php echo $meal['product_name']; ?>"
                                                    data-price="<?php echo $meal['mprice']; ?>">
                                                    Add Order
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php }
                                } ?>
                            </div>
                        </div>
                    </div>

                </div>



                <!-- Order Summary -->
                <div class="col-md-3 order-panel p-3">
                    <div class="container p-0 gap-2 mt-3 m-0">
                        <p class="cashierID">Cashier ID: CAS-0001-0225</p>
                        <!--ILISAN NI SA DATABASE-->
                    </div>
                    <div class="container p-0 gap-2">
                        <p class="cashierName">Cashier Name: Disney Princess</p>
                        <!--ILISAN NI SA DATABASE-->
                    </div>
                    <div class="">
                        <table class="table text-center ">

                            <thead>
                                <tr>
                                    <th>Items</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    let orderItems = []; // Stores order items
                                    let orderAddOns = []; // Stores add-ons separately

                                    document.querySelectorAll('.modal').forEach(modal => {
                                        let selectedSizePrice = 0;
                                        let selectedSize = "";
                                        let selectedAddOns = [];

                                        modal.addEventListener('click', function(event) {
                                            let target = event.target;

                                            // ✅ Size selection
                                            if (target.classList.contains('size-btn')) {
                                                let parent = target.closest('.modal-body');
                                                parent.querySelectorAll('.size-btn').forEach(
                                                    btn => btn.classList.remove('active'));
                                                target.classList.add('active');

                                                selectedSize = target.dataset.size;
                                                selectedSizePrice = parseFloat(target.dataset
                                                    .price);
                                            }

                                            // ✅ Add-ons selection
                                            if (target.classList.contains('addon-btn')) {
                                                target.classList.toggle('active');
                                                let addonName = target.dataset.addon;
                                                let addonPrice = parseFloat(target.dataset
                                                    .price);

                                                if (target.classList.contains('active')) {
                                                    if (!selectedAddOns.some(addon => addon
                                                            .name === addonName)) {
                                                        selectedAddOns.push({
                                                            name: addonName,
                                                            price: addonPrice
                                                        });
                                                    }
                                                } else {
                                                    selectedAddOns = selectedAddOns.filter(
                                                        addon => addon.name !== addonName);
                                                }
                                            }

                                            // ✅ Add Order button click
                                            if (target.classList.contains('add-order-btn')) {
                                                let productName = target.dataset.product;
                                                let productId = target.dataset.pid;
                                                let isPastry = target.closest('.modal').id
                                                    .includes('PastryfoodModal');
                                                let isMeal = target.closest('.modal').id
                                                    .includes('RiceMealfoodModal');
                                                let finalPrice = selectedSizePrice;

                                                if (isPastry || isMeal) {
                                                    finalPrice = parseFloat(target.dataset
                                                        .price) || 0;
                                                } else {
                                                    if (!selectedSize) {
                                                        alert("Please select a size!");
                                                        return;
                                                    }
                                                    finalPrice = selectedSizePrice;
                                                }

                                                console.log(
                                                    `Adding ${productName} - Price: ₱${finalPrice}`
                                                );

                                                addToOrder(productName, finalPrice,
                                                    selectedSize, selectedAddOns);

                                                // ✅ Close modal safely
                                                let modalElement = document.getElementById(
                                                        `DrinksfoodModal${productId}`) ||
                                                    document.getElementById(
                                                        `PastryfoodModal${productId}`) ||
                                                    document.getElementById(
                                                        `RiceMealfoodModal${productId}`);

                                                if (modalElement) {
                                                    let modalInstance = bootstrap.Modal
                                                        .getInstance(modalElement) ||
                                                        new bootstrap.Modal(modalElement);
                                                    modalInstance.hide();
                                                }
                                            }
                                        });
                                    });

                                    // ✅ Function to add item to order
                                    function addToOrder(itemName, itemPrice, size = '', addOns = []) {
                                        let itemKey = `${itemName}-${size}`;
                                        let existingItem = orderItems.find(item => item.key === itemKey);

                                        if (existingItem) {
                                            existingItem.qty += 1;
                                        } else {
                                            orderItems.push({
                                                key: itemKey,
                                                name: itemName,
                                                price: itemPrice,
                                                qty: 1,
                                                size
                                            });
                                        }

                                        // ✅ Add add-ons correctly
                                        addOns.forEach(addon => {
                                            let addonKey = `${itemKey}-addon-${addon.name}`;
                                            let existingAddon = orderAddOns.find(a => a.key ===
                                                addonKey);

                                            if (existingAddon) {
                                                existingAddon.qty += 1;
                                            } else {
                                                orderAddOns.push({
                                                    key: addonKey,
                                                    name: addon.name,
                                                    price: addon.price,
                                                    qty: 1,
                                                    parentKey: itemKey
                                                });
                                            }
                                        });

                                        updateOrderSummary();
                                    }

                                    // ✅ Function to update order summary table
                                    function updateOrderSummary() {
                                        let orderTableBody = document.querySelector('.table tbody');
                                        orderTableBody.innerHTML = "";

                                        let subtotal = 0;

                                        orderItems.forEach(item => {
                                            let totalPrice = item.qty * item.price;
                                            subtotal += totalPrice;

                                            let newRow = document.createElement('tr');
                                            newRow.setAttribute('id', `row-${item.key}`);
                                            newRow.innerHTML = `
                <td><div class="food-order-title">${item.name} ${item.size ? '(' + item.size + ')' : ''}</div></td>
                <td><input type="number" value="${item.qty}" min="1" class="quantity-field" data-key="${item.key}"></td>
                <td><div class="food-total-price" data-key="${item.key}">₱${totalPrice.toFixed(2)}</div></td>
                <td><a href="#" class="delete-item" data-key="${item.key}"><i class="bi bi-trash"></i></a></td>

                
            
    <input type="hidden" value="${item.name}" name="item_name${item.name}" id="item_name${item.name}">
    
    <input type="hidden" value="${item.size}" name="size${item.size}" id="size${item.size}">
    
    <input type="hidden" value="${item.qty}" min="1" name="qty${item.qty}" id="qty${item.qty}">

   

     
            `;
                                            let productNames = [];
                                            let productSizes = [];
                                            let productQty = [];
                                            let addonNames = [];
                                            let addonQty = [];

                                            // Iterate through orderItems to collect product data
                                            orderItems.forEach(item => {
                                                productNames.push(item
                                                    .name); // Collect product name
                                                productSizes.push(item
                                                    .size); // Collect product size
                                                productQty.push(item
                                                    .qty); // Add quantity to the total
                                            });

                                            // Iterate through orderAddOns to collect add-on data
                                            orderAddOns.forEach(addon => {
                                                addonNames.push(addon
                                                    .name); // Collect add-on name
                                                addonQty.push(addon
                                                    .qty); // Add add-on quantity to the total
                                            });

                                            // Now, populate the hidden fields with the collected data
                                            document.getElementById('hiddenProducts').value =
                                                productNames.join(
                                                    ', '); // Join product names with commas
                                            document.getElementById('hiddenQty').value = productQty
                                                .join(', '); // Total quantity of products
                                            document.getElementById('size').value = productSizes.join(
                                                ', '); // Join product sizes with commas

                                            document.getElementById('hiddenaddons').value = addonNames
                                                .join(', '); // Join add-on names with commas
                                            document.getElementById('hiddenQtyaddons').value = addonQty
                                                .join(', '); // Total quantity of add-ons


                                            orderTableBody.appendChild(newRow);
                                        });

                                        // ✅ Add-ons as separate items
                                        orderAddOns.forEach(addon => {
                                            let totalPrice = addon.qty * addon.price;
                                            subtotal += totalPrice;

                                            let newRow = document.createElement('tr');
                                            newRow.setAttribute('id', `row-${addon.key}`);
                                            newRow.innerHTML = `
                <td><div class="food-add-title">+ ${addon.name}</div></td>
                <td><input type="number" value="${addon.qty}" min="1" class="quantity-field" data-key="${addon.key}"></td>
                <td><div class="food-total-price" data-key="${addon.key}">₱${totalPrice.toFixed(2)}</div></td>
                <td><a href="#" class="delete-item" data-key="${addon.key}"><i class="bi bi-trash"></i></a></td>

                 <input type="hidden" value="${addon.name}" name="item_name${addon.name}" id="item_name${addon.name}">
    
    <input type="hidden" value="${addon.qty}" name="size${addon.qty}" id="size${addon.qty}">
    
            `;
                                            orderTableBody.appendChild(newRow);
                                        });

                                        document.querySelector('.subtotalAmount').innerText =
                                            `₱${subtotal.toFixed(2)}`;

                                        attachEventListeners();
                                    }

                                    // ✅ Attach event listeners to new elements
                                    function attachEventListeners() {
                                        document.querySelectorAll('.quantity-field').forEach(input => {
                                            input.addEventListener('change', function() {
                                                updateQty(this.dataset.key, this.value);
                                            });
                                        });

                                        document.querySelectorAll('.delete-item').forEach(button => {
                                            button.addEventListener('click', function(event) {
                                                event.preventDefault();
                                                removeItem(this.dataset.key);
                                            });
                                        });
                                    }

                                    // ✅ Function to update quantity and price dynamically
                                    function updateQty(itemKey, newQty) {
                                        let item = orderItems.find(i => i.key === itemKey);
                                        let addon = orderAddOns.find(a => a.key === itemKey);

                                        if (item) {
                                            item.qty = parseInt(newQty);
                                        } else if (addon) {
                                            addon.qty = parseInt(newQty);
                                        }

                                        updateOrderSummary();
                                    }

                                    // ✅ Function to remove an item or add-on
                                    function removeItem(itemKey) {
                                        orderItems = orderItems.filter(item => item.key !== itemKey);
                                        orderAddOns = orderAddOns.filter(addon => addon.key !== itemKey);
                                        updateOrderSummary();
                                    }
                                });
                                </script>
                            </tbody>

                        </table>
                    </div>


                    <!-- DATE AND TIME -->
                    <div class="container-date-time-name d-flex gap-2 mb-3">
                        <p class="store-name m-0">Blacksnow Cafe</p>
                        <p class="m-0">|</p>
                        <p class="order-date m-0"></p>
                        <p class="m-0">|</p>
                        <p class="order-time m-0"></p>
                    </div>

                    <div class="cooking-instructions mt-4">
                        <input type="text" id="cooking-note" class="input-box"
                            placeholder="Add preparation instructions (Note)">
                    </div>

                    <div class="container-total p-0">
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <p class="Subtotal mb-0">Subtotal:</p>
                            <p class="subtotalAmount mb-0"></p>
                            <!--ILISAN NI SA DATABASE-->
                        </div>



                        <div class="mt-4">
                            <p class="fw-bold mb-4">Payment Method:</p>


                            <!-- Cash Payment -->
                            <div class="form-check d-flex align-items-center">
                                <div class="gap-2">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="cash"
                                        value="cash" checked>
                                    <label class="form-check-label" for="cash">Cash</label>
                                </div>
                                <div class="inputPaymentCash gap-2 d-flex align-items-center">
                                    <input class="cashpayment form-control" type="number" placeholder="Cash Payment">
                                    <input class="cashchange form-control ms-2" type="text" placeholder="Change"
                                        readonly>
                                </div>
                            </div>

                            <!-- Error Message -->
                            <div id="error-message" class="text-danger mt-2" style="display: none;"></div>
                        </div>

                        <div class="d-flex justify-content-center mt-4 gap-3 mb-4">
                            <button type="button" class="btn btn-danger">Cancel Order</button>

                            <button type="button" class="btn btn-success" onclick="payNowClicked()"
                                data-bs-toggle="modal">
                                Pay Order Now
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>



    <!-- Receipt Modal -->
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="receiptModalLabel">Order Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body-reciept">
                    <p class="headermodal">Blacksnow Cafe</p>
                    <p class="addressmodal">Emillio Jacinto St. Davao City</p>
                    <p class="divider">---------------------------------------</p>
                    <p class="textreciept">Order ID: <span id="receiptOrderID"></span></p>
                    <p class="textreciept"><strong>Order Time:</strong> <span id="orderTime"></span></p>
                    <p class="textreciept"><strong>Order Date:</strong> <span id="orderDate"></span></p>
                    <p class="divider">---------------------------------------</p>
                    <p class="textreciept">Payment Method: <span id="receiptPaymentMethod"></span></p>
                    <p class="textreciept">Items Ordered:</p>
                    <ul class="itemsorderlist" id="receiptItems"></ul>
                    <p class="pricereciept">Subtotal: <span id="receiptSubtotal"></span></p>
                    <p class="textreciept">Cash Given: <span id="receiptCashGiven"></span></p>
                    <p class="pricereciept">Change: <span id="receiptChange"></span></p>
                    <p class="textreciept">Cashier Name: <?php echo $_SESSION['username']; ?></span></p>


                    <form action="../   /cashier_handler.php" method="POST">
                        <!-- ✅ Hidden Inputs for Backend Processing -->
                        <input type="hidden" name="" id="hiddenOrderID">
                        <input type="hidden" name="paymentMethod" id="hiddenPaymentMethod">
                        <input type="hidden" name="subtotal" id="hiddenSubtotal">
                        <input type="hidden" name="cashGiven" id="hiddenCashGiven">
                        <input type="hidden" name="changeAmount" id="hiddenChange">
                        <input type="hidden" name="cashier" value="<?php echo $_SESSION['username']; ?>">


                        <!-- ✅ Visible Inputs for Reference -->
                        <input type="hidden" name="gcashReference" id="hiddenGCashRef">

                        <!-- ✅ Product Information -->
                        <input type="hidden" name="products" id="hiddenProducts">
                        <input type="hidden" name="totalQty" id="hiddenQty">
                        <input type="hidden" name="size" id="size">

                        <!-- ✅ Add-ons Information -->
                        <input type="hidden" name="addons" id="hiddenaddons">
                        <input type="hidden" name="totalQtyaddons" id="hiddenQtyaddons">

                        <!-- ✅ Hidden Inputs for Each Ordered Item -->
                        <div id="hiddenOrderItems"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="print" type="submit" class="btn btn-primary">Save Order</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
    function payNowClicked() {
        console.log("✅ Pay Order Now button clicked!");

        let subtotalElem = document.querySelector(".subtotalAmount");
        let cashGivenElem = document.querySelector(".cashpayment");
        let changeElem = document.querySelector(".cashchange");

        let subtotal = subtotalElem ? parseFloat(subtotalElem.innerText.replace(/[^\d.]/g, "")) || 0 : 0;
        let cashGiven = cashGivenElem ? parseFloat(cashGivenElem.value) || 0 : 0;
        let changeAmount = cashGiven - subtotal;

        if (changeElem) {
            changeElem.value = changeAmount >= 0 ? changeAmount.toFixed(2) : "Insufficient";
        }

        if (changeAmount < 0) {
            alert("❌ Insufficient balance. Please enter enough cash.");
            return; // Stop execution if balance is insufficient
        }

        let orderTableBody = document.querySelector(".table tbody");
        let orderItems = [];
        let totalQty = 0;

        orderTableBody?.querySelectorAll("tr").forEach(row => {
            let itemNameElem = row.querySelector(".food-add-title, .food-order-title");
            let itemQtyElem = row.querySelector(".quantity-field");
            let itemSizeElem = row.querySelector(".size-field");
            let itemPriceElem = row.querySelector(".food-total-price");

            let isAddon = row.classList.contains("addon-row");

            if (itemNameElem && itemQtyElem && itemPriceElem) {
                let itemName = itemNameElem.innerText.replace("+ ", "").trim();
                let itemQty = itemQtyElem.tagName === "INPUT" ? parseInt(itemQtyElem.value) || 1 : parseInt(
                    itemQtyElem.innerText) || 1;
                let itemSize = itemSizeElem && itemSizeElem.innerText.trim() ? itemSizeElem.innerText.trim() :
                    "";
                let itemPrice = parseFloat(itemPriceElem.innerText.replace(/[^\d.]/g, "")) || 0;

                if (!isAddon) {
                    totalQty += itemQty;
                } else {
                    itemQty = 0;
                }

                orderItems.push({
                    name: itemName,
                    size: itemSize,
                    qty: itemQty,
                    price: itemPrice,
                    isAddon
                });
            }
        });

        let orderData = {
            orderID: "BS-" + Math.floor(Math.random() * 100000),
            items: orderItems,
            subtotal: subtotal,
            paymentMethod: "cash",
            cashGiven: cashGiven,
            changeAmount: changeAmount,
            totalQty: totalQty
        };

        console.log("📋 Final Order Data:", orderData);

        document.getElementById("receiptOrderID").innerText = orderData.orderID;
        document.getElementById("receiptPaymentMethod").innerText = orderData.paymentMethod;
        document.getElementById("receiptSubtotal").innerText = `₱${orderData.subtotal.toFixed(2)}`;
        document.getElementById("receiptCashGiven").innerText = `₱${orderData.cashGiven.toFixed(2)}`;
        document.getElementById("receiptChange").innerText = `₱${orderData.changeAmount.toFixed(2)}`;

        document.getElementById("hiddenOrderID").value = orderData.orderID;
        document.getElementById("hiddenPaymentMethod").value = orderData.paymentMethod;
        document.getElementById("hiddenSubtotal").value = orderData.subtotal.toFixed(2);
        document.getElementById("hiddenCashGiven").value = orderData.cashGiven.toFixed(2);
        document.getElementById("hiddenChange").value = orderData.changeAmount.toFixed(2);

        let itemsList = document.getElementById("receiptItems");
        itemsList.innerHTML = "";
        orderData.items.forEach(item => {
            let li = document.createElement("li");
            li.innerText = item.isAddon ?
                `${item.name} - ₱${item.price.toFixed(2)}` :
                `${item.qty}x ${item.size} ${item.name} - ₱${item.price.toFixed(2)}`;
            itemsList.appendChild(li);
        });

        // ✅ Show receipt modal only if balance is sufficient
        let receiptModal = new bootstrap.Modal(document.getElementById("receiptModal"));
        receiptModal.show();
    }

    // Function to format the time as HH:MM AM/PM
    function getFormattedTime() {
        const now = new Date();
        let hours = now.getHours();
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12; // Convert to 12-hour format
        return `${hours}:${minutes} ${ampm}`;
    }

    // Function to format the date as MM/DD/YYYY
    function getFormattedDate() {
        const now = new Date();
        const month = (now.getMonth() + 1).toString().padStart(2, '0'); // Months are zero-based
        const day = now.getDate().toString().padStart(2, '0');
        const year = now.getFullYear();
        return `${month}/${day}/${year}`;
    }

    // Insert the formatted time and date into their respective spans
    document.getElementById('orderTime').textContent = getFormattedTime();
    document.getElementById('orderDate').textContent = getFormattedDate();


    // PRINT
    // Function to print the receipt
    function printReceipt() {
        // Clone the modal body content
        const modalBody = document.querySelector('.modal-body-reciept').cloneNode(true);

        // Create a new window for printing
        const printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Print Receipt</title>');
        printWindow.document.write('<style>');
        printWindow.document.write(`
    body {
      width: 58mm;
      margin: 0;
      padding: 0;
      font-family: monospace; /* Monospace font for thermal printers */
    }
    .modal-body-reciept {
      width: 100%;
      padding: 0;
      box-sizing: border-box;
      font-size: 10px;
      line-height: 1.2;
    }
    .headermodal {
      font-size: 12px;
      font-weight: bold;
      text-align: center;
      margin: 5px 0;
    }
    .addressmodal {
      font-size: 10px;
      text-align: center;
      margin: 5px 0;
    }
    .divider {
      text-align: center;
      margin: 5px 0;
      font-size: 10px;
    }
    .pricereciept {
      display: flex;
      justify-content: space-between;
      font-weight: bold;
      margin-bottom: 0;
    }
    .textreciept {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 3px 0;
      font-size: 10px;
    }
    .itemsorderlist {
      margin: 2px 0;
      font-size: 10px;
      list-style-type: none;
      padding: 0;
    }
  `);
        printWindow.document.write('</style></head><body>');
        printWindow.document.write(modalBody.outerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();

        // Print the new window
        printWindow.print();
    }

    // Add event listener to the print button
    const printBtn = document.getElementById('print');
    if (printBtn) {
        printBtn.addEventListener('click', printReceipt);
    }

    function updateDateTime() {
        const now = new Date();

        // Format the date as "Month Day, Year" (e.g., "October 5, 2023")
        const dateOptions = {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        const formattedDate = now.toLocaleDateString(undefined, dateOptions);

        // Format the time as "HH:MM:SS" (e.g., "14:35:07")
        const timeOptions = {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };
        const formattedTime = now.toLocaleTimeString(undefined, timeOptions);

        // Update the HTML elements
        document.querySelector('.order-date').textContent = formattedDate;
        document.querySelector('.order-time').textContent = formattedTime;
    }

    // Update the date and time every second
    setInterval(updateDateTime, 1000);

    // Initialize the date and time immediately
    updateDateTime();
    </script>

    <!-- Custom JS -->





</body>

</html>