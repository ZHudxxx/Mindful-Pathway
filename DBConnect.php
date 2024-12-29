<?php
$dbc = mysqli_connect("localhost", "root", "", "mindfulpathway");

if (mysqli_connect_errno()) {
    echo "Failed to connect to the database: " . mysqli_connect_error();
}


if (!function_exists('query')) {
    function query($query)
    {
        global $dbc;

        $result  = mysqli_query($dbc, $query);

        $rows = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        return $rows;
    }
}
?>