<?php 
require("connect-db.php");
require("request-db.php");
?>

<?php include 'header.php'; ?>

<?php
// Check if the user is already logged in
// if(isset($_SESSION['user_id'])) {
//     header("Location: grocery.php");
//     exit;
// }

// Login form submission handling
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $success = signUp($_POST['email'], $_POST['password'], $_POST['password-conf']);

    if ($success) {
        $_SESSION['user_id'] = $user['user_ID'];
        header("Location: grocery.php");
        exit;
    } else {
        echo "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Wilson Zheng">
    <meta name="description" content="Smart Grocery Finder App (CS 4750)">
    <title>Signup</title>
</head>

<body>
    <div class="signup-container">
        <h2>Signup</h2>
        <?php if(isset($error)) echo "<p>$error</p>"; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username">Email:</label><br>
            <input type="text" id="email" name="email"><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password"><br>
            <label for="password">Confirm Password:</label><br>
            <input type="password" id="password-conf" name="password-conf"><br>
            <input type="submit" name="signup" value="Signup">
        </form>
    </div>  
</body>
</html>
