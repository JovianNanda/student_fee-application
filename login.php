<?php
include_once __DIR__ . "/bootstrap.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=homeUrl()?>/assets/css/page/login.css">
    <title>Login</title>
</head>

<?php

if (isset($_POST['login'])) {
    login($_POST);
}

if (isLogin()) {
    redirect("dashboard/");
}
?>

<body>
    <noscript>
        <div class="modal show">
            <div class="modal-content">
                <div class="modal-header">
                    <li class="ico ico-exclamation-triangle c-danger"></li>
                    <h1 class="c-danger">Javascript!</h1>
                    <span class="c-black">Tolong Aktifkan Javascript di browser anda!</span>
                </div>
            </div>
        </div>
    </noscript>
    <div class="login-container">
        <div class="login-left-container">
            <div class="form-container">
                <div class="login-title">
                    <li class="ico ico-logo c-green"></li>
                    <h1 class="c-green">Login</h1>
                </div>
                <form action="" method="post">
                    <p>Sign in menggunakan data akun anda.</p>
                    <?php alert();?>
                    <div class="input-group">
                        <div class="input-layout">
                            <label for="username">Username Atau NIS</label>
                            <input type="text" id="username" name="username" class="input c-success"
                                placeholder="Username Atau NIS">
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-layout">
                            <label for="password">Password</label>
                            <div class="input-icon">
                                <input type="password" id="password" name="password" class="input c-success"
                                    placeholder="Password">
                                <button type="button" class="button button-transparent icon no-border"
                                    data-toggle="password">
                                    <li class="ico ico-eye c-black"></li>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button class="button bg-green" name="login" id="loginBtn" style="width:100%;">Login <li
                            class="ico ico-login c-white"></li> </button>
                    <!-- <p class="text-footer">Tidak memiliki akun? <a href="register.php"><b>Register</b></a></p> -->
                </form>
            </div>
        </div>
        <div class="login-right-container" style="position: relative;">
            <img src="assets/img/login-img.png" alt="">
        </div>
    </div>
    <script src="assets/js/functions.js"></script>
    <?php
printValidation();
?>
</body>

</html>