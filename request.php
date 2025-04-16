<?php
//Send requests here:
//http://localhost:8000/request.php

//Test request: fail
//http://localhost:8000/request.php?street=10102 Pokey Oaks St.&city=Townsville&state=CN&zip=99999

//Test request: perfect match
//http://localhost:8000/request.php?street=12696 HORSESHOE LN&city=NORTH PLATTE&state=NE&zip=69101


define("STREET", 3);
define("CITY", 4);
define("STATE", 5);
define("ZIP", 6);
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
    $adr = explode(" ", $row[STREET]);
    if (($street_nmb == $adr[ADR_NMB]) and ($zip == $row[ZIP]))
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
    return [true, json_encode($match)];
}


$match_most = -1;
$match_most_amt = 0;

for ($i = 0; $i < $csv_len; $i++)
{
    $row = $csv[$i];
    $adr = explode(" ", $row[STREET]);
    if (($street_nmb == $adr[ADR_NMB]) and (($city == $row[CITY]) and ($state == $row[STATE])))
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
    return [true, json_encode($match)];
}


echo "No match found.\n";
echo "Street: " . $street . "\n";
echo "City: " . $city . "\n";
echo "State: " . $state . "\n";
echo "Zip: " . $zip . "\n";
return [false, json_encode([])];


?>