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
            background-image: url('/PROJECT/images/getstarted.png');
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

        .hero-title {
            font-family: "Times New Roman", serif;
            font-size: 8vw;
            font-weight: normal;
            color: white;
            font-style: italic;
            text-align: center;
        }

        .description {
            font-size: 1.2rem;
            color: white; 
            text-align: center;
            max-width: 80%;
            margin: 0 auto;
            padding: 15px;
            border-radius: 10px;
            font-weight: normal;
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
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg px-3">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="#">
                <i class="bi bi-snow2"></i> Blacksnow Café
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Best Seller</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container d-flex flex-column justify-content-center align-items-center text-center vh-100">
        <h1 class="hero-title">Blacksnow Café</h1>
            <h4 class="description">
                "Blacksnow Café offers a symphony of flavors in every bite and sip. <br>
                Indulge in delicate pastries, exquisite meals, and perfectly crafted drinks. <br>
                Savor the moment, embrace the elegance."
            </h4>
        <button class="btn custom-btn btn-lg mt-4">Get Started <i class="bi bi-arrow-right"></i></button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
