<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login | PharmCodeX Pharmacy System</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body.bg-cover {
            background: url('includes/Bg.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        /* ================= SPLASH SCREEN ================= */
        #splash {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #212529;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeOut 1s ease forwards;
            animation-delay: 2.5s;
        }

        .splash-content {
            text-align: center;
            color: white;
            animation: scaleUp 1.2s ease-in-out infinite alternate;
        }

        .splash-content img {
            width: 150px;
            margin-bottom: 15px;
        }

        @keyframes scaleUp {
            from {
                transform: scale(1);
            }
            to {
                transform: scale(1.08);
            }
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                visibility: hidden;
            }
        }

        /* Hide main content initially */
        #main-content {
            display: none;
        }
    </style>
</head>

<body class="bg-cover">

<!-- ================= SPLASH SCREEN ================= -->
<div id="splash">
    <div class="splash-content">
        <img src="includes/PharmCodeX-Logo.PNG" alt="PharmCodeX Logo">
        <h2>PharmCodeX</h2>
        <p>Pharmacy Management System</p>
         <p>Developed by: Ali Raza</p>
    </div>
</div>

<!-- ================= MAIN CONTENT ================= -->
<div id="main-content">
    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-md-4">
                <div class="card shadow login-card">
                    <div class="card-header text-center bg-primary text-white">
                        <h1>PharmCodeX</h1>
                        <h5>LOGIN</h5>
                    </div>

                    <div class="card-body p-4">
                        <form method="POST" action="auth/login.php">

                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" placeholder="Enter username" name="username" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" placeholder="Enter password" name="password" required>
                            </div>

                            <button class="btn btn-primary w-100" type="submit" name="login">
                                Login
                            </button>
                        </form>

                        <p class="text-center text-primary mt-3">
                            PharmCodeX: Pharmacy Management System
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= SPLASH JS ================= -->
<script>
    setTimeout(() => {
        document.getElementById("splash").style.display = "none";
        document.getElementById("main-content").style.display = "block";
    }, 3000);
</script>

</body>
</html>
