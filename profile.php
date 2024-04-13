<?php include 'header.php'; ?>

<?php

if (!$_SESSION["user_id"]) {
    header("Location: login.php");
    exit;
}

$user_information = getUserInformation($_SESSION["user_id"]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    if (!empty($_POST['editProfileBtn'])) 
    {
        $success = updateUserInformation($_SESSION["user_id"], $_POST['first_name'], $_POST['last_name'], $_POST['age']);
        $user_information = getUserInformation($_SESSION["user_id"]);

    } 
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Information</title>
    <style> 
        label {
            width: 100px;
        }
        .info-box {
            margin-bottom: 10px;
        }
        h2 {
            margin-bottom: 20px;
        }
        body {
            padding-left: 
        }
        #editProfileBtn {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>User Information</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class=info-box>
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name"name="first_name" value="<?php echo $user_information["first_name"]; ?>"><br>
            </div>
            <div class=info-box>
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo $user_information["last_name"]; ?>"><br>
            </div>
            <div class=info-box>
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" value="<?php echo $user_information["age"]; ?>"><br>
            </div>
            <div class=info-box>
                <label for="email">Email:</label>
                <label type="email" id="email" name="email"><?php echo $user_information["email"]; ?></label><br>
            </div>
            <!-- <div class=info-box>
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo $user_information["address"]; ?>"><br>
            </div> -->
            <input type="submit" value="Update" id="editProfileBtn" name="editProfileBtn" class="btn btn-primary"/>
        </form>
        <a href="changePassword.php">Change my password</a>
    </div>
</body>
</html>