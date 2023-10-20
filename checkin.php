<?php
    session_start();
    require_once "config.php";

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css">
        <meta http-equiv="Refresh" content="5; url=index.html">
    </head>
    <body>
    <?php 
        if (array_key_exists("player", $_GET)) {
            if ($_GET["player"] == "newest") {
                $sql = $connection->prepare("INSERT INTO EventAttendance VALUES ((SELECT MAX(pid) FROM Players), CURDATE(), :eventtype)");
            } else {
                $sql = $connection->prepare("INSERT INTO EventAttendance VALUES (:p_id, CURDATE(), :eventtype)");
                $sql->bindParam(':p_id', $_GET["player"]);
            }
            
            $sql->bindParam(':eventtype', $event_type);
            try {
                if ($sql->execute()) {
                    echo '<h1 class="page-title">You\'re all checked in!</h1>';
                    echo '<p style="text-align: center">Find yourself a seat and get comfortable!</p>';
                } else {
                    echo '<h1 class="page-title">Error: Invalid player ID!</h1>';
                }
            } catch (PDOException $exception) {
                echo '<h1 class="page-title">You are already checked in</h1>';
                echo '<p style="text-align: center">No need to do so again! Find a seat and get comfortable!</p>';
            }
        } else {
            echo '<h1 class="page-title">Error: no player ID provided!</h1>';
        }
        echo '<p style="text-align: center">This screen will advance automatically after five seconds.</p>';
    ?>
    </body>
</html>