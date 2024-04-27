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

    $query = "SELECT user_ID, email, password, [admin] FROM _User WHERE email = :email";

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

function checkAdmin($user_ID) {
    global $db;

    $query = "SELECT admin FROM _User WHERE user_ID = :user_ID";

    $statement = $db->prepare($query);

    $statement->bindValue(':user_ID', $user_ID, PDO::PARAM_INT);

    $statement->execute();
    $result = $statement->fetch();
    $statement->closeCursor();

    return $result["admin"];
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

function getItems() {
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

function requestChangeAddStore($user_ID, $store_name, $street_number, $street_name,
                                $city, $state, $zip_code, $notes)
{
    global $db;

    $change_details = "(add store) Name: \"$store_name\", \"$street_number $street_name, $city $state $zip_code\" (notes) \"$notes\"";
    $dateTime = new DateTime();
    $dateTime = $dateTime->format('Y-m-d_H:i:s');

    $query = "INSERT INTO ChangeRequest (user, request_time, change_details, accepted) 
                VALUES (:user_ID, :time, :change_details, false)";
    $statement = $db->prepare($query);

    $statement->bindValue(':user_ID', $user_ID, PDO::PARAM_INT);
    $statement->bindValue(':time', $dateTime, PDO::PARAM_STR);
    $statement->bindValue(':change_details', $change_details, PDO::PARAM_STR);

    $statement->execute();
    $statement->closeCursor();
    
    echo "Added \"add store\" change request.";
}

function requestChangeRemoveStore($user_ID, $store, $reason, $notes)
{
    global $db;

    list($store_ID, $store_name) = explode(':', $store, 2);
    $store_ID = intval(trim($store_ID));
    $store_name = trim($store_name);
    $dateTime = new DateTime();
    $dateTime = $dateTime->format('Y-m-d_H:i:s');

    $change_details = "(remove store) Name: \"$store_name\", reason: \"$reason\" (notes) \"$notes\"";

    $query = "INSERT INTO ChangeRequest (user, store, request_time, change_details, accepted) 
                VALUES (:user_ID, :store_ID, :time, :change_details, false)";
    $statement = $db->prepare($query);

    $statement->bindValue(':user_ID', $user_ID, PDO::PARAM_INT);
    $statement->bindValue(':store_ID', $store_ID, PDO::PARAM_INT);
    $statement->bindValue(':time', $dateTime, PDO::PARAM_STR);
    $statement->bindValue(':change_details', $change_details, PDO::PARAM_STR);

    $statement->execute();
    $statement->closeCursor();
    
    echo "Added \"remove store\" change request.";
}

function requestChangeAddStoreItem($user_ID, $store, $item_name,
                                    $item_brand, $price, $weight, $unit, $notes)
{
    global $db;

    list($store_ID, $store_name) = explode(':', $store, 2);
    $store_ID = intval(trim($store_ID));
    $store_name = trim($store_name);
    $dateTime = new DateTime();
    $dateTime = $dateTime->format('Y-m-d_H:i:s');

    $change_details = "(add store item) Store: \"$store_name\", Item info: name-\"$item_name\" brand-\"$item_brand\" price-\"$price\" weight-\"$weight\" unit-\"$unit\" (notes) \"$notes\"";

    $query = "INSERT INTO ChangeRequest (user, store, request_time, change_details, accepted) 
                VALUES (:user_ID, :store_ID, :time, :change_details, false)";
    $statement = $db->prepare($query);

    $statement->bindValue(':user_ID', $user_ID, PDO::PARAM_INT);
    $statement->bindValue(':store_ID', $store_ID, PDO::PARAM_INT);
    $statement->bindValue(':time', $dateTime, PDO::PARAM_STR);
    $statement->bindValue(':change_details', $change_details, PDO::PARAM_STR);

    $statement->execute();
    $statement->closeCursor();
    
    echo "Added \"add store item\" change request.";
}     

function requestChangeChangePrice($user_ID, $store, $item_name,
                                    $item_brand, $price, $weight, $unit, $notes)
{
    global $db;

    list($store_ID, $store_name) = explode(':', $store, 2);
    $store_ID = intval(trim($store_ID));
    $store_name = trim($store_name);
    $dateTime = new DateTime();
    $dateTime = $dateTime->format('Y-m-d_H:i:s');

    $change_details = "(change price) Store: \"$store_name\", Item info: name-\"$item_name\" brand-\"$item_brand\" new price-\"$price\" new weight-\"$weight\" new unit-\"$unit\" (notes) \"$notes\"";

    $query = "INSERT INTO ChangeRequest (user, store, request_time, change_details, accepted) 
                VALUES (:user_ID, :store_ID, :time, :change_details, false)";
    $statement = $db->prepare($query);

    $statement->bindValue(':user_ID', $user_ID, PDO::PARAM_INT);
    $statement->bindValue(':store_ID', $store_ID, PDO::PARAM_INT);
    $statement->bindValue(':time', $dateTime, PDO::PARAM_STR);
    $statement->bindValue(':change_details', $change_details, PDO::PARAM_STR);

    $statement->execute();
    $statement->closeCursor();
    
    echo "Added \"change price\" change request.";
}                                    

function getChangeRequests()
{
    global $db;

    $query = "SELECT * FROM ChangeRequest WHERE accepted=false ORDER BY request_time DESC";
    $statement = $db->prepare($query);       // compile
    $statement->execute();
    $result1 = $statement->fetchAll();      // fetch all results
    $statement->closeCursor();

    $query = "SELECT * FROM ChangeRequest WHERE accepted=true ORDER BY request_time DESC LIMIT 10";
    $statement = $db->prepare($query);       // compile
    $statement->execute();
    $result2 = $statement->fetchAll();      // fetch all results
    $statement->closeCursor();

    $result = array("unprocessed"=>$result1, "processed"=>$result2);

    return $result;
}

function adminAddStore($store_name, $street_num, $street_name,
                        $city, $state, $zip_code)
{
    global $db;

    $query = "CALL addStore(:store_name, :street_num, :street_name, :city, :state, :zipcode);";
    $statement = $db->prepare($query);

    $statement->bindValue(':store_name', $store_name, PDO::PARAM_STR);
    $statement->bindValue(':street_num', $street_num, PDO::PARAM_STR);
    $statement->bindValue(':street_name', $street_name, PDO::PARAM_STR);
    $statement->bindValue(':city', $city, PDO::PARAM_STR);
    $statement->bindValue(':state', $state, PDO::PARAM_STR);
    $statement->bindValue(':zipcode', $zip_code, PDO::PARAM_STR);

    $statement->execute();
    $statement->closeCursor();
    
    echo "Added store \"$store_name\".";
}

function adminRemoveStore($store)
{
    global $db;

    list($store_ID, $store_name) = explode(':', $store, 2);
    $store_ID = intval(trim($store_ID));
    $store_name = trim($store_name);

    $query = "DELETE FROM store WHERE store_ID = :store_id";
    $statement = $db->prepare($query);

    $statement->bindValue(':store_id', $store_ID, PDO::PARAM_INT);

    $statement->execute();
    $statement->closeCursor();
    
    echo "Removed store \"$store_name\".";
}

function adminAddStoreItem($store, $item, $price, $weight, $unit)
{
    // does not check if item already exists in the store

    global $db;

    $price = intval($price);
    $weight = intval($weight);
    $price_per_unit = round($price/$weight, 2);

    list($store_ID, $store_name) = explode(':', $store, 2);
    $store_ID = intval(trim($store_ID));

    list($item_ID, $item_name) = explode(':', $item, 2);
    $item_ID = intval(trim($item_ID));

    $query = "INSERT INTO storeItems (store, item, price, weight, unit, price_per_unit)
                values (:store, :item, :price, :weight, :unit, :price_per_unit)";
    $statement = $db->prepare($query);

    $statement->bindValue(':store', $store_ID, PDO::PARAM_INT);
    $statement->bindValue(':item', $item_ID, PDO::PARAM_INT);
    $statement->bindValue(':price', $price, PDO::PARAM_INT);
    $statement->bindValue(':weight', $weight, PDO::PARAM_INT);
    $statement->bindValue(':unit', $unit, PDO::PARAM_STR);
    $statement->bindValue(':price_per_unit', $price_per_unit, PDO::PARAM_INT);

    $statement->execute();
    $statement->closeCursor();
    
    echo "Added item \"$item_name\" to store \"$store_name\".";
}

function adminChangePrice($store, $item, $price, $weight, $unit)
{
    global $db;

    $price = intval($price);
    $weight = intval($weight);
    $price_per_unit = round($price/$weight, 2);

    list($store_ID, $store_name) = explode(':', $store, 2);
    $store_ID = intval(trim($store_ID));

    list($item_ID, $item_name) = explode(':', $item, 2);
    $item_ID = intval(trim($item_ID));

    $query = "UPDATE storeItems
                SET price = :price, weight = :weight, unit = :unit, price_per_unit = :price_per_unit
                WHERE store = :store AND item = :item";
    $statement = $db->prepare($query);

    $statement->bindValue(':store', $store_ID, PDO::PARAM_INT);
    $statement->bindValue(':item', $item_ID, PDO::PARAM_INT);
    $statement->bindValue(':price', $price, PDO::PARAM_INT);
    $statement->bindValue(':weight', $weight, PDO::PARAM_INT);
    $statement->bindValue(':unit', $unit, PDO::PARAM_STR);
    $statement->bindValue(':price_per_unit', $price_per_unit, PDO::PARAM_INT);

    $statement->execute();
    $statement->closeCursor();
    
    echo "Change price of item \"$item_name\" from store \"$store_name\".";
}

function acceptChangeRequest($request_ID) {
    global $db;
    
    $query = "UPDATE changeRequest
                SET accepted = true
                WHERE request_ID = :request_ID";
    $statement = $db->prepare($query);

    $statement->bindValue(':request_ID', $request_ID, PDO::PARAM_INT);

    $statement->execute();
    $statement->closeCursor();
}

function rejectChangeRequest($request_ID) {
    global $db;

    $query = "UPDATE changeRequest
                SET accepted = false
                WHERE request_ID = :request_ID";
    $statement = $db->prepare($query);

    $statement->bindValue(':request_ID', $request_ID, PDO::PARAM_INT);

    $statement->execute();
    $statement->closeCursor();
}

?>