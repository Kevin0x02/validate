<?php
function get_rows_by_zip($street_nmb, $zip)
{
    $conn = get_conn();
    $street_nmb .= ' %';

    $query = $conn->prepare("
    SELECT *
    FROM adr
    WHERE zip = ?
    AND address_primary LIKE ?
    ");

    $query->bind_param("ss", $zip, $street_nmb);
    return get_query_result($query);
}

function get_rows_by_citystate($street_nmb, $city, $state)
{
    $conn = get_conn();
    $street_nmb .= ' %';

    $query = $conn->prepare("
    SELECT *
    FROM adr
    WHERE city = ?
    AND state = ?
    AND address_primary LIKE ?
    ");

    $query->bind_param("sss", $city, $state, $street_nmb);
    return get_query_result($query);
}

function get_conn()
{
    $host = 'localhost';
    $user = 'root';
    $pass = ''; 
    $db = 'address';
    return new mysqli($host, $user, $pass, $db);
}

function get_query_result($query)
{
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