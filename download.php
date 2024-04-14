<?php
    include_once "config.php";

    if (array_key_exists('view', $_GET)) {
        $mode = $_GET['view'];
    } else {
        $mode = 'all_players';
    }


    if ($mode == "all_players") {
        $sql = $connection->prepare("SELECT * FROM Players INTO OUTFILE :exportfilename 
                                     FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\\n'");
    } else if ($mode == "by_date") {
        $sql = $connection->prepare("SELECT * FROM Players 
                                     WHERE pid IN (SELECT pid FROM EventAttendance WHERE event_date = :eventdate) INTO OUTFILE :exportfilename 
                                     FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\\n'");
        $sql->bindParam(":eventdate", $_GET["date"]);
    } else if ($mode == "by_name") {
        $sql = $connection->prepare("SELECT * FROM Players WHERE CONCAT(fname, ' ', lname) LIKE :query INTO OUTFILE :exportfilename 
                                     FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\\n'");
        $query = '%' . $_GET['name'] . '%';        
        $sql->bindParam(":query", $query);
    } else if ($mode == "events_participated") {
        $sql = $connection->prepare("SELECT EventAttendance.pid, Players.fname, Players.lname, Players.pokemon_id, Players.mtg_id, Players.mha_id, Players.email, EventAttendance.event_date, Events.event_name 
                                     FROM EventAttendance INNER JOIN Players ON EventAttendance.pid = Players.pid 
                                     INNER JOIN Events ON Events.event_type = EventAttendance.event_type WHERE EventAttendance.pid = :p_id 
                                     INTO OUTFILE :exportfilename FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\\n'");
        $sql->bindParam(":p_id", $_GET["player"]);
    } else if ($mode == "attendance") {
        $sql = $connection->prepare("SELECT EventAttendance.pid, Players.fname, Players.lname, Players.pokemon_id, Players.mtg_id, Players.mha_id, Players.email, EventAttendance.event_date, Events.event_name
                                     FROM EventAttendance INNER JOIN Players ON EventAttendance.pid = Players.pid 
                                     INNER JOIN Events ON Events.event_type = EventAttendance.event_type 
                                     WHERE EventAttendance.event_type = :etype AND EventAttendance.event_date LIKE :edate 
                                     INTO OUTFILE :exportfilename FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\\n'");
        $sql->bindParam(":etype", $_GET["event_type"]);
        $month = $_GET["date"] . "%";
        $sql->bindParam(":edate", $month);
    }

    $filename = "/var/www/html/gng/export/export";
    $filename = $filename . rand(1000000, 9999999);
    $filename = $filename . ".csv";
    $sql->bindParam(":exportfilename", $filename);

    if ($sql->execute()) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($filename));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();
        readfile($filename);
        exit;
    }
?>