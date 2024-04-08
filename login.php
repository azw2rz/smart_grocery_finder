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
    // $success = hashPassword($_POST['password']);

    if ($success) {
        $_SESSION['user_id'] = $user['user_ID'];
        header("Location: grocery.php");
        exit;
    } else {
        echo "Invalid username or password";
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
    <title>Login</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">  

    <!-- <link rel="stylesheet" href="grocery.css"> -->

</head>

<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if(isset($error)) echo "<p>$error</p>"; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username"><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password"><br>
            <input type="submit" name="login" value="Login">
        </form>
    </div>  
</body>
</html>
