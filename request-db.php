<?php

function searchItems($searchInput)
{
    global $db;
    $query = "SELECT * FROM Item WHERE name LIKE :searchInput";

    $statement = $db->prepare($query);

    $searchPattern = "%$searchInput%";
    $statement->bindValue(':searchInput', $searchPattern);

    // $statement->bindValue(':searchInput', $searchPattern, PDO::PARAM_STR);

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