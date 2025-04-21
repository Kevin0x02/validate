<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'address';

$conn = new mysqli($host, $user, $pass, $dbName);

$source = fopen("source.csv", "r");

$headers = fgetcsv($source);

$table_name = 'adr';

$columns = [];
foreach ($headers as $header) 
{
    $columns[] = "`" . $conn->real_escape_string($header) . "` TEXT";
}
$create_q = "CREATE TABLE IF NOT EXISTS `$table_name` (" . implode(", ", $columns) . ")";
$conn->query($create_q);

while (($data = fgetcsv($source)) !== FALSE) 
{
    $escaped = array_map([$conn, 'real_escape_string'], $data);
    $insert = "INSERT INTO `$table_name` VALUES ('" . implode("','", $escaped) . "')";
    $conn->query($insert);
}

fclose($source);
echo "Successfully imported CSV as new table.";
$conn->close();
?>
