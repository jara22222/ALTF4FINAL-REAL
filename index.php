<?php
include ("authentication/authenticated.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Blacksnow Cafe | Welcome!</title>
    <style>
        body {
            background-image: url('designs/Images/getstarted.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100vh;
        }

        .navbar {
            background: rgba(0, 0, 0, 0.7) !important;
        }

        .navbar-nav .nav-link {
            color: white !important;
            transition: background-color 0.3s, color 0.3s;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 5px;
            padding: 5px 10px;
        }

        .custom-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            padding: 30px;
        }

        .form-label {
            font-weight: bold;
        }

        .custom-btn {
            background-color: black;
            color: white;
            border: none;
            transition: background-color 0.3s;
        }

        .custom-btn:hover {
            background-color: grey;
            color: black;
        }

        .modal-content {
            border-radius: 15px;
        }

        .glass{
            background: linear-gradient(135deg, rgb(255, 255, 255), rgb(75, 67, 67));
            background-filter:blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 15px;
            border: none;
        }
    </style>
</head>
<body class="overflow-hidden">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg px-3">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="#">
                <i class="bi bi-snow2"></i> Blacksnow Café
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="custom-card glass col-md-6 col-lg-5">
            <h1 class="text-center mb-3">Log In</h1>
            <p class="text-secondary text-center mb-4">Please enter your credentials.</p>
            <form method="POST" action="handlers/login_handler.php">
                <!-- Username Input -->
                <div class="mb-3">
                    <label for="username" class="form-label">Username/Email</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
                </div>

                <!-- Password Input -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                </div>

               
                <!-- Login Button -->
                <div class="d-grid mb-3">
                    <button type="submit" name="login" class="btn btn-primary">Sign In</button>
                </div>

                <!-- Sign Up Link -->
               
            </form>
        </div>
    </div>

    <!-- Registration Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">Register New Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="login_handler.php" method="POST">
                        <div class="mb-3">
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="register" class="btn btn-primary">Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
