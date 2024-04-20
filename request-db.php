<?php

function hashPassword($password) {
    // password_hash() is function in PHP
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    return $hashedPassword;
}

function verifyPassword($password, $hashedPassword) {
    // password_verity() is function in PHP
    if (password_verify($password, $hashedPassword)) {
        return true;
    } else {
        return false;
    }
}

function changePassword($user_ID, $newPassword, $conf) {
    $equal = $newPassword == $conf;    
    if (!$equal) {
        return array("success"=>false, "cause"=>"different");
    }

    $password = hashPassword($newPassword);

    global $db;

    $query = "UPDATE _User SET password = :password WHERE user_ID = :user_ID";

    $statement = $db->prepare($query);

    $statement->bindValue(':password', $password, PDO::PARAM_STR);
    $statement->bindValue(':user_ID', $user_ID, PDO::PARAM_INT);

    $statement->execute();
    $statement->closeCursor();

    return array("success"=>true, "cause"=>"none");
}

function checkLogin($email, $password) {
    global $db;

    $query = "SELECT user_ID, email, password FROM _User WHERE email = :email";

    $statement = $db->prepare($query);

    $statement->bindValue(':email', $email);

    $statement->execute();
    $result = $statement->fetch();
    $statement->closeCursor();

    // echo "got here too";

    if (!$result) {
        return array("success"=>false, "cause"=>"exist");
    } else if (!verifyPassword($password, $result['password'])){
        return array("success"=>false, "cause"=>"password");
    } else {
        return array("success"=>true, "cause"=>"none", "user"=>$result);
    }
}

function signUp($first_name, $last_name, $email, $password, $password_conf) {
    global $db;

    $query = "SELECT * FROM _User WHERE email = :email";
    $statement = $db->prepare($query);

    $emailString = "$email";
    $statement->bindValue(':email', $emailString, PDO::PARAM_STR);

    $statement->execute();
    $result = $statement->fetchAll();
    $statement->closeCursor();

    if (count($result) > 0) {
        return array("success"=>false, "cause"=>"exist");
    }

    $password_match = $password == $password_conf;
    
    if (!$password_match) {
        return array("success"=>false, "cause"=>"password");
    }

    $passwordHashed = hashPassword($password);

    $query = "INSERT INTO _user (first_name, last_name, email, password) 
                VALUES (:first_name, :last_name, :email, :password)";
    $statement = $db->prepare($query);

    $statement->bindValue(':first_name', "$first_name");
    $statement->bindValue(':last_name', $last_name);
    $statement->bindValue(':email', $email);
    $statement->bindValue(':password', $passwordHashed);

    $statement->execute();
    $statement->closeCursor();

    return array("success"=>true, "cause"=>"none");
}

function searchReqeust($searchType, $searchInput) {
    global $db;

    if ($searchType == "item") {
        $query = "SELECT * FROM item WHERE name LIKE :searchInput";
    } elseif ($searchType == "store") {
        $query = "SELECT * 
                  FROM store JOIN address ON store.address = address.address_ID 
                  WHERE name LIKE :searchInput";
    } elseif ($searchType == "storeItemsID") {
        $query = 
               "SELECT
                    s.store_ID AS store_ID,
                    s.name AS store_name,
                    a.zipcode,
                    i.item_ID AS item_ID,
                    i.name AS item_name,
                    i.brand AS item_brand,
                    si.price,
                    si.weight,
                    si.unit,
                    si.price_per_unit
                FROM
                    Store s
                    JOIN Address a ON s.address = a.address_ID
                    JOIN StoreItems si ON s.store_ID = si.store
                    JOIN Item i ON si.item = i.item_ID
                WHERE
                    s.store_ID = :searchInput;";  
    } elseif ($searchType == "itemInStores") {
        $query = 
               "SELECT
                    i.item_ID AS item_ID,
                    i.name AS item_name,
                    i.brand AS item_brand,
                    s.store_ID AS store_ID,
                    s.name AS store_name,
                    a.zipcode,
                    si.price,
                    si.weight,
                    si.unit,
                    si.price_per_unit
                FROM
                    Store s
                    JOIN Address a ON s.address = a.address_ID
                    JOIN StoreItems si ON s.store_ID = si.store
                    JOIN Item i ON si.item = i.item_ID
                WHERE
                    i.item_ID = :searchInput";
    } else {
        // handle invalid $searchType value
        echo "Invalid search type...";
        return [];
    }

    $statement = $db->prepare($query);

    $searchPattern = "%$searchInput%";
    if ($searchType == "storeItemsID" || $searchType == "itemInStores") {
        $statement->bindValue(':searchInput', $searchInput, PDO::PARAM_INT); // Bind $searchInput as an integer
    } else {
        $statement->bindValue(':searchInput', $searchPattern, PDO::PARAM_STR);
    }

    $statement->execute();
    $result = $statement->fetchAll();
    $statement->closeCursor();

    $itemCount = count($result);

    return $result;
}

function getAllItems() {
    global $db;

    $query = "SELECT * FROM Item";
    $statement = $db->prepare($query);       // compile
    $statement->execute();
    $result = $statement->fetchAll();      // fetch all results
    $statement->closeCursor();

    $itemCount = count($result);

    echo "Number of total items: " . $itemCount;

    return $result;
}

function getUserInformation($user_id) {
    global $db;

    $query = "SELECT * FROM _User WHERE user_ID = :user_ID";

    $statement = $db->prepare($query);

    $statement->bindValue(':user_ID', $user_id, PDO::PARAM_INT);

    $statement->execute();
    $result = $statement->fetch();
    $statement->closeCursor();

    return $result;
}

function updateUserInformation($user_ID, $first_name, $last_name, $age) {
    global $db;

    $query = "  UPDATE _User
                SET first_name = :first_name,
                    last_name = :last_name,
                    age = :age
                WHERE user_ID = :user_ID";

    $statement = $db->prepare($query);

    $statement->bindValue(':first_name', $first_name, PDO::PARAM_STR);
    $statement->bindValue(':last_name', $last_name, PDO::PARAM_STR);
    $statement->bindValue(':age', $age, PDO::PARAM_INT);
    $statement->bindValue(':user_ID', $user_ID, PDO::PARAM_INT);

    $statement->execute();
    // $result = $statement->fetch();
    $statement->closeCursor();

    // return;
}

function getStores() {
    global $db;

    $query = "SELECT * FROM Store";
    $statement = $db->prepare($query);       // compile
    $statement->execute();
    $result = $statement->fetchAll();      // fetch all results
    $statement->closeCursor();

    $itemCount = count($result);

    echo "Number of total stores: " . $itemCount;

    return $result;
}

?>