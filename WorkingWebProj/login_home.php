<?php

session_start();

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? '' ];

$currentactiveform = $_SESSION['currentactiveform'] ?? 'login';

session_unset();

function showError($error): string {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '' ;
}

function setFormActive($formName, $currentactiveform): string{
    return $formName === $currentactiveform ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset = "UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login & Register page</title>
        <link rel="stylesheet" href="css/theme.css">
        <link rel="stylesheet" href="css/style_login.css">
        <!-- FontAwesome (if needed later) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>

    <body id="login">
        <div class="container">
            <div class="form-box <?= setFormActive('login', $currentactiveform); ?>" id="login-form">
                <?php
                    if (isset($_GET["newpswrd"])){
                        if ($_GET["newpswrd"] == "passwordupdated"){
                            echo '<p id="success-alert" class="alert-success"> Password has been successfully resetted</p>';
                        }
                    }
                ?>
            <form action="login_with_registration.php" method="post">
                <h2>Login</h2>
                <?= showError($errors['login']); ?>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
                <p>Don't have an account? <a href="#" onclick="showForm('register-form')">Register</a></p>
                <div style="margin-top 10px;">
                    <a href="forgot_password.php">Forgot password</a>
                </div>
                <div style="margin-top 10px;">
                    <a href="index.php">Back to Home</a>
                </div>

            </form>

            </div>

            <div class="form-box <?= setFormActive('register', $currentactiveform); ?>" id="register-form">
                <form action="login_with_registration.php" method="post">
                    <h2>Register</h2>
                    <?= showError($errors['register']); ?>
                    <input type="text" name="name" placeholder="Name" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="register">Register</button>
                    <p>Already have an account? <a href="#" onclick ="showForm('login-form')">Login</a></p>

                </form>

            </div>


        

        <script src="javascript/myscript.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const alert=document.getElementById("success-alert");

                if(alert) {
                    setTimeout(function() {
                        alert.style.transition = "opacity 0.5s ease";
                        alert.style.opacity = "0";

                    setTimeout(function(){
                        alert.remove();

                    }, 500);
                    }, 5000);
                }
            });
        </script>



    </body>




















</html>