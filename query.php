<?php
function get_rows_by_zip($street_nmb, $zip)
{
    $host = 'localhost';
    $user = 'root';
    $pass = ''; 
    $db = 'address';
    $table_name = 'adr';
    $conn = new mysqli($host, $user, $pass, $db);

    $query = $conn->prepare("
    SELECT *
    FROM $table_name
    WHERE zip = ?
    AND CAST(SUBSTRING_INDEX(address_primary, ' ', 1) AS UNSIGNED) = ?
    ");

    $query->bind_param("si", $zip, $street_nmb);
    $query->execute();

    $result = $query->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) 
    {
        $rows[] = $row;
    }
    return $rows;
}

function get_rows_by_citystate($city, $state)
{
    $host = 'localhost';
    $user = 'root';
    $pass = ''; 
    $db = 'address';
    $table_name = 'adr';
    $conn = new mysqli($host, $user, $pass, $db);

    $query = $conn->prepare("
    SELECT *
    FROM $table_name
    WHERE city = ?
    AND state = ?
    ");

    $query->bind_param("ss", $city, $state);
    $query->execute();

    $result = $query->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) 
    {
        $rows[] = $row;
    }
    return $rows;
}
?>