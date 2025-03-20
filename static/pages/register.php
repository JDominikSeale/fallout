<?php

include_once("../../server/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST["register_button"])){
        $db = new DB;
        $formUsername = $_POST["username"];
        $username = $formUsername;
        $password = $_POST["password"];
        $username = htmlspecialchars($formUsername);
        $password = htmlspecialchars($password);
        $password = password_hash($password, PASSWORD_DEFAULT);
        $userID = $db->registerUser($username, $password);
        if($userID){
            session_start();
            $_SESSION["userID"] = $userID;
            header("Location: home.php");
            exit();
        }
    }
}else{
    $formUsername = null;
}
?>




<html>
    <head>
        <title>Register</title>
        <link rel="stylesheet" href="../css/index.css">
    </head>
    <body>
        <div class="container">
            <div class="login-container">
                <h1>Register</h1>
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
                            <button type="submit" name="register_button">Register</button>
                        </div>
                        <div class="error-container">
                            <p><?php echo isset($error) ? $error : ''; ?></p>
                        </div>
                    </form>
                </div>
            </div>
    </body>
</html>
