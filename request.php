<?php
require_once("query.php");
//Send requests here:
//http://localhost:8000/request.php

/*
//Test request: fail
http://localhost/valid//request.php?street=10102 Pokey Oaks St.&city=Townsville&state=CN&zip=99999

//Test request: perfect match
http://localhost/valid//request.php?street=12696 HORSESHOE LN&city=NORTH PLATTE&state=NE&zip=69101

//Test request: fuzzy match
http://localhost/valid//request.php?street=12696 HORSESHOE LANE&city=NORTH PLATTE&state=NE&zip=69101

//Test request: no-zip match
http://localhost/valid//request.php?street=12696 HORSESHOE LN&city=NORTH PLATTE&state=NE

//Test request: no-zip fuzzy match
http://localhost/valid//request.php?street=12696 HOUSESHOE LN&city=NORTH PLATTE&state=NE
*/

function get_return_data($row)
{
    $lst = [];
    array_push($lst, $row["location_id"]);
    array_push($lst, $row["building_type_code"]);
    array_push($lst, $row["Service Area"]);
    array_push($lst, $row["Constructed"]);
    array_push($lst, $row["NeedsConstructed"]);
    return json_encode($lst);
}

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

function check_rows($rows, $street_rest, $match_req, $match_most_amt, $type)
{
    if (!empty($rows))
    {
        foreach ($rows as $row)
        {
            $adr = explode(" ", $row["address_primary"]);
            $rest = strtoupper(implode(" ", array_slice($adr, 1)));
            similar_text($street_rest, $rest, $percent);
            if (($percent > $match_req) and ($percent > $match_most_amt))
            {
                $match_most = $row;
                $match_most_amt = $percent;
                if ($percent >= 100) {break;}
            }
        }
        if (!empty($match_most))
        {
            echo "Found match on $type\n";
            echo "Match percent: $match_most_amt%";
            return [true, get_return_data($match_most)];
        }
    }
    return [false, false];
}

$street = $_GET["street"] ?? "";
$city = $_GET["city"] ?? "";
$state = $_GET["state"] ?? "";
$zip = $_GET["zip"] ?? "";

$street_parts = explode(" ", $street);
$street_nmb = $street_parts[0];
$street_rest = strtoupper(implode(" ", array_slice($street_parts, 1)));

$match_req = 59;

$match_most = null;
$match_most_amt = 0;

$rows = get_rows_by_zip($street_nmb, $zip);
$result = check_rows($rows, $street_rest, $match_req, $match_most_amt, "zip");
if ($result[0]) {return $result[1];}

$rows = get_rows_by_citystate($city, $state);
$result = check_rows($rows, $street_rest, $match_req, $match_most_amt, "city/state");
if ($result[0]) {return $result[1];}


echo "No match found.\n";
echo "Street: " . $street . "\n";
echo "City: " . $city . "\n";
echo "State: " . $state . "\n";
echo "Zip: " . $zip . "\n";
return false;


?>