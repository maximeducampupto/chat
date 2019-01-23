<?php

require 'api_includes/db_connection.php';

$results = [];

$query = "select * 
              from messages
              limit :limit";
$query = $conn->prepare($query);
$query->bindValue('limit', 100, PDO::PARAM_INT);
$query->execute();


while ($row = $query->fetch(PDO::FETCH_OBJ))
{
    array_push($results, $row);
}
echo json_encode($results);

