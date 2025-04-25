<?php
function get_rows_by_zip($street_nmb, $zip)
{
    $host = 'localhost';
    $user = 'root';
    $pass = ''; 
    $db = 'address';
    $table_name = 'adr';
    $conn = new mysqli($host, $user, $pass, $db);

    $street_nmb = $street_nmb . '%';

    $query = $conn->prepare("
    SELECT *
    FROM $table_name
    WHERE zip = ?
    AND address_primary LIKE ?
    ");

    $query->bind_param("ss", $zip, $street_nmb);
    $query->execute();

    $result = $query->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) 
    {
        $rows[] = $row;
    }
    return $rows;
}

function get_rows_by_citystate($street_nmb, $city, $state)
{
    $host = 'localhost';
    $user = 'root';
    $pass = ''; 
    $db = 'address';
    $table_name = 'adr';
    $conn = new mysqli($host, $user, $pass, $db);

    $street_nmb = $street_nmb . '%';

    $query = $conn->prepare("
    SELECT *
    FROM $table_name
    WHERE city = ?
    AND state = ?
    AND address_primary LIKE ?
    ");

    $query->bind_param("sss", $city, $state, $street_nmb);
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