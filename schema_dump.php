<?php
include_once('database/db_connection.php');

$tables = [];
$result = mysqli_query($con, "SHOW TABLES");
while ($row = mysqli_fetch_row($result)) {
    $tables[] = $row[0];
}

foreach ($tables as $table) {
    echo "TABLE: $table\n";
    $schema = mysqli_query($con, "DESCRIBE $table");
    while ($row = mysqli_fetch_assoc($schema)) {
        echo "  " . $row['Field'] . " (" . $row['Type'] . ")";
        if ($row['Key'] == 'PRI') echo " PK";
        if ($row['Key'] == 'MUL') echo " FK/Index";
        echo "\n";
    }
    echo "\n";
}
?>
