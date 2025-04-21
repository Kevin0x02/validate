<?php
function get_all_rows() 
{
    $host = 'localhost';
    $user = 'root';
    $pass = ''; 
    $db = 'address';
    $tableName = 'adr';

    $conn = new mysqli($host, $user, $pass, $db);

    $rows = [];
    $result = $conn->query("SELECT * FROM `$tableName`");

    if (($result) and ($result->num_rows > 0))
    {
        while ($row = $result->fetch_assoc()) 
        {
            $rows[] = $row;
        }
    }
    $conn->close();
    return $rows;
}
?>
