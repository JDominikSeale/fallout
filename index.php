<?php

include_once("server/db.php");



if ($_SERVER["REQUEST_METHOD"] == "POST"){
    if (isset($_POST["login_button"])){
        $formUsername = $_POST["username"];
        $password = $_POST["password"];
        $username = htmlspecialchars($formUsername);
        $password = htmlspecialchars($password);
        $db = new DB;
        $userID = $db->checkUserLogin($username, $password);
        if($userID != false){
            session_start();
            $_SESSION["userID"] = $userID;
            header("Location: static/pages/home.php");
            exit();
        }
    }
    if (isset($_POST["register_button"])){
        header("Location: static/pages/register.php");
        exit();
    }
}else{
    $formUsername = null;
}

?>


<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" href="static/css/index.css">
    </head>
    <body>
        <div class="container">
            <div class="login-container">
                <h1>Login</h1>
                <div class="insert-container">
                    <form action="" method="post">
                        <div class="user-input-container">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" value="<?php echo isset($formUsername) ? $formUsername : ''; ?>">
                        </div>
                        <div class="password-input-container">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password">
                        </div>
                        <div class="button-container">
                            <button type="submit" name="login_button">Login</button>
                        </div>
                        <div>
                            <button type="submit" name="register_button">Register Page</button>
                        </div>
                        <div class="error-container">
                            <p><?php echo isset($error) ? $error : ''; ?></p>
                        </div>
                    </form>
                </div>
            </div>
    </body>
</html>
