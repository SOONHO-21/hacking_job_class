<?php
include "../include/db_connect.php";
include "admin_check.php";

$result = mysqli_query($con, "SELECT id FROM _mem WHERE public_id = ''");

while ($row = mysqli_fetch_assoc($result)) {
    do {
        $public_id = substr(bin2hex(random_bytes(10)), 0, 10);
        $stmt = $con->prepare("SELECT id FROM _mem WHERE public_id = ?");
        $stmt->bind_param("s", $public_id);
        $stmt->execute();
        $stmt->store_result();
    } while ($stmt->num_rows > 0);

    $stmt = $con->prepare("UPDATE _mem SET public_id = ? WHERE id = ?");
    $stmt->bind_param("ss", $public_id, $row['id']);
    $stmt->execute();
}

echo "public_id migration complete";