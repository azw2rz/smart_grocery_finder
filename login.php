<?php 
require("connect-db.php");
require("request-db.php");
?>

<?php include 'header.php'; ?>

<?php
session_start();

// Check if the user is already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: grocery.php");
    exit;
}

// Login form submission handling
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $success = checkLogin($_POST['username'], $_POST['password']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if(isset($error)) echo "<p>$error</p>"; ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username"><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password"><br>
        <input type="submit" name="login" value="Login">
    </form>
</body>
</html>
