<?php

$con = mysqli_connect("localhost", "root", "",);

try {
    mysqli_select_db($con, "Kids_Korner");
} catch (Exception $e) {
    echo "Error in selecting database";
}
