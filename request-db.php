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

function checkLogin($email, $password) 
{
    global $db;

    $query = "SELECT user_ID, email, password FROM _User WHERE email = :email";

    $statement = $db->prepare($query);

    $statement->bindValue(':email', $email);

    $statement->execute();
    $result = $statement->fetch();
    $statement->closeCursor();

    if($result && verifyPassword($password, $result['password'])) {
        return true;
    } else {
        return false;
    }
}

function searchReqeust($searchType, $searchInput)
{
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

function addRequests($reqDate, $roomNumber, $reqBy, $repairDesc, $reqPriority)
{
    global $db;
    $reqDate = date('Y-m-d');   // ensure proper data type
    $query = "INSERT INTO requests (reqDate, roomNumber, reqBy, repairDesc, reqPriority) 
            VALUES (:reqDate, :roomNumber, :reqBy, :repairDesc, :reqPriority);";

    try {
        // $statement = $db->query($query);
        
        $statement = $db->prepare($query);

        $statement->bindValue(':reqDate', $reqDate);
        $statement->bindValue(':roomNumber', $roomNumber);
        $statement->bindValue(':reqBy', $reqBy);
        $statement->bindValue(':repairDesc', $repairDesc);
        $statement->bindValue(':reqPriority', $reqPriority);

        $statement->execute();
        $statement->closeCursor();

    } catch (PDOException $e) {
        $e->getMessage();
    } catch (Exception $e) {
        $e->getMessage();
    }
}

function getAllItems()
{
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

function getRequestById($id)  
{
    

}

function updateRequest($reqId, $reqDate, $roomNumber, $reqBy, $repairDesc, $reqPriority)
{


}

function deleteRequest($reqId)
{

    
}

?>