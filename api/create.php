<?php

require 'api_includes/db_connection.php';


if (isset($_GET['content']))
{
    try {

        $query = "insert into messages (content, username) values(:content, :username)";
        $query = $conn->prepare($query);

        // TODO
        $content = $_GET['content'];
        $username = filter_var($_GET['username'], FILTER_SANITIZE_STRING);

        $query->bindValue(':content', $content, PDO::PARAM_STR);
        $query->bindValue(':username', $username, PDO::PARAM_STR);
        $query->execute();

    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
        return json_encode($error);
    }
    echo "success";
}



