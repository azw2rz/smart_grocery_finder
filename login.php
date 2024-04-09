<?php include 'header.php'; ?>

<?php

if ($_SESSION) {
    header("Location: grocery.php");
    exit;
}

// Login form submission handling
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['login'])) {
        $result = checkLogin($_POST['email'], $_POST['password']);

        if ($result["success"] == true) {
            $_SESSION['user_id'] = $result["user"]['user_ID'];
            header("Location: grocery.php");
            exit;
        } else if ($result["cause"] == "exist") {
            echo "Account doesn't exist.";
        } else if ($result["cause"] == "password") {
            echo "Password is incorrect";
        } else {
            echo "Unknown error...";
        }
    }
    else if (!empty($_POST['signup'])) {
        header("Location: signup.php");
        exit;
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
</head>

<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if(isset($error)) echo "<p>$error</p>"; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="email">Email:</label><br>
            <input type="text" id="email" name="email"><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password"><br>
            <input type="submit" name="login" value="Login">
            <input type="submit" name="signup" value="Signup">
        </form>
    </div>  
</body>
</html>
