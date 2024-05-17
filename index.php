<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login with Google - Google Drive Service</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="57x57" href="/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="manifest" href="/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }

        .login-container {
            background: white;
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        .login-button {
            background-color: #4285f4;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-button:hover {
            background-color: #357ae8;
        }

        .login-button:focus {
            outline: none;
        }

        .google-logo {
            width: 120px;
            margin-right: 8px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
</head>

<body>
    <div class="login-container">
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2f/Google_2015_logo.svg/1200px-Google_2015_logo.svg.png"
            class="google-logo" alt="Google Logo">
        <hr />
        <h2 class="mb-4">Akses ke google drive</h2>
        <hr/>
        <?php if(!isset($_SESSION['access_token'])) { ?>
            <p>Silahkan login terlebih dahulu</p>
            <button class="login-button rounded" id="loginButton">
                <i class="fab fa-google"></i> Login dengan Google
            </button>
        <?php } else { ?>
            <p>Anda sudah login</p>
            <div class="row">
                <div class="col-md-12">
                    <a href="home.php" class="btn btn-primary btn-md rounded" id="enterButton">
                        Masuk ke halaman utama
                    </a>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <a href="logout.php" class="btn btn-danger btn-md rounded" id="logoutButton">
                        Keluar akun
                    </a>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php if(!isset($_SESSION['access_token'])) { ?>
    <script>
        document.getElementById('loginButton').onclick = function () {
            $.ajax({
                url: 'login.php',
                type: 'POST',
                data: {
                    username: 'exampleUser',
                    password: 'examplePass'
                },
                success: function (response) {
                    if(response.status) {   
                        location.href = response.data;
                    } else {
                        alert("Login gagal");
                    }
                },
                error: function () {
                    $('#message').html('An error occurred');
                }
            });
        };
    </script>
    <?php } ?>

<!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>    
</body>

</html>
