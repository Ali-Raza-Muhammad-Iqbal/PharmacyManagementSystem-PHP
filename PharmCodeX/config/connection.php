<?php
$conn = mysqli_connect("localhost", "root", "", "pharmaco");

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
// else {
//     echo "Database Connected Successfully";
// }   
?>
