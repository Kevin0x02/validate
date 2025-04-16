<?php
//Send requests here:
//http://localhost:8000/request.php

//http://localhost:8000/request.php?street=Pokey Oaks Street&city=Townsville&state=CN&zip=99999


define($STREET, 3);
define($CITY, 4);
define($ZIP, 5);

$username = "Guybrush";
$password = "MightyPirate";

if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) 
{
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo "Could not authenticate: No credentials given";
    exit;
}

if ($_SERVER['PHP_AUTH_USER'] !== $username || $_SERVER['PHP_AUTH_PW'] !== $password)
{
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 403 Forbidden');
    echo "Could not authenticate: Invalid credentials";
    exit;
}

$file = fopen("source.csv", "r");
$csv = array();
while(($data = fgetcsv($file, 9999, ",")) !== FALSE)
{
    $csv[] = $data;
}
fclose($file);


$street = $_GET['street'] ?? "";
$city = $_GET['city'] ?? "";
$state = $_GET['state'] ?? "";
$zip = $_GET['zip'] ?? "";

$street_parts = explode(" ", $street)[0] ?? "";
$street_nmb = $street_parts[0];

$csv_len = sizeof($csv);

for ($i = 0; $i < $csv_len; $i++)
{
    $row = $csv[i];
    if ($street_nmb == explode(" ", $row[$STREET])[0])
    {
        return [true, json_encode($row)];
    }
}



$shortest = "";
$shortest_amt = 99999;
for ($i = 0; $i < $csv_len; $i++)
{
    $dist = levenshtein($street_nmb, 
}




//$respone = [];

header('Content-Type: application/json');
echo json_encode($response);


?>