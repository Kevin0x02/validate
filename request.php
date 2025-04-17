<?php
//Send requests here:
//http://localhost:8000/request.php

/*
//Test request: fail
//http://localhost:8000/request.php?street=10102 Pokey Oaks St.&city=Townsville&state=CN&zip=99999

//Test request: perfect match
http://localhost:8000/request.php?street=12696 HORSESHOE LN&city=NORTH PLATTE&state=NE&zip=69101

//Test request: fuzzy match
http://localhost:8000/request.php?street=12696 HORSESHOE LANE&city=NORTH PLATTE&state=NE&zip=69101

//Test request: no-zip match
http://localhost:8000/request.php?street=12696 HORSESHOE LN&city=NORTH PLATTE&state=NE

//Test request: no-zip fuzzy match
http://localhost:8000/request.php?street=12696 HOUSESHOE LN&city=NORTH PLATTE&state=NE
*/

function get_return_data($row)
{
    $lst = [];
    array_push($lst, $row[COL_LOCATION_ID]);
    array_push($lst, $row[COL_BUILDING]);
    array_push($lst, $row[COL_SERVICE_AREA]);
    array_push($lst, $row[COL_CONSTRUCTED]);
    array_push($lst, $row[COL_NEEDS_CONTRUCTED]);
    return json_encode($lst);
}

//Columns
define("COL_X", 0);
define("COL_Y", 1);
define("COL_LOCATION_ID", 2);
define("COL_STREET", 3);
define("COL_CITY", 4);
define("COL_STATE", 5);
define("COL_ZIP", 6);
define("COL_BUILDING", 7);
define("COL_SERVICE_AREA", 8);
define("COL_CONSTRUCTED", 9);
define("COL_NEEDS_CONTRUCTED", 10);


define("ADR_NMB", 0);

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


$street = $_GET["street"] ?? "";
$city = $_GET["city"] ?? "";
$state = $_GET["state"] ?? "";
$zip = $_GET["zip"] ?? "";

$street_parts = explode(" ", $street);
$street_nmb = $street_parts[0];
$street_rest = implode(" ", array_slice($street_parts, 1));

$csv_len = sizeof($csv);

$match_most = -1;
$match_most_amt = 0;

for ($i = 0; $i < $csv_len; $i++)
{
    $row = $csv[$i];
    $adr = explode(" ", $row[COL_STREET]);
    if (($street_nmb == $adr[ADR_NMB]) and ($zip == $row[COL_ZIP]))
    {
        $rest = implode(" ", array_slice($adr, 1));
        $dist = similar_text($street_rest, $rest, $percent);
        if ($percent > $match_most_amt)
        {
            $match_most = $i;
            $match_most_amt = $percent;
            if ($percent >= 100) {break;}
        }
    }
}
if ($match_most > -1)
{
    $match = $csv[$match_most];
    echo "Found match on zip";
    return [true, get_return_data($match)];
}


$match_most = -1;
$match_most_amt = 0;

for ($i = 0; $i < $csv_len; $i++)
{
    $row = $csv[$i];
    $adr = explode(" ", $row[COL_STREET]);
    if (($street_nmb == $adr[ADR_NMB]) and (($city == $row[COL_CITY]) and ($state == $row[COL_STATE])))
    {
        $rest = implode(" ", array_slice($adr, 1));
        $dist = similar_text($street_rest, $rest, $percent);
        if ($percent > $match_most_amt)
        {
            $match_most = $i;
            $match_most_amt = $percent;
            if ($percent >= 100) {break;}
        }
    }
}
if ($match_most > -1)
{
    $match = $csv[$match_most];
    echo "Found match on city and state";
    return [true, get_return_data($match)];
}


echo "No match found.\n";
echo "Street: " . $street . "\n";
echo "City: " . $city . "\n";
echo "State: " . $state . "\n";
echo "Zip: " . $zip . "\n";
return [false, json_encode([])];


?>