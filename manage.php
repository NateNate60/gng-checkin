<?php
    session_start();
    include_once "config.php";    

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (array_key_exists("addcategory", $_POST)) {
            $sql = $connection->prepare("INSERT INTO Events (event_name) VALUES (:ename)");
            $sql->bindParam(":ename", $_POST["addcategory"]);
            $sql->execute();
        } else if (array_key_exists("deleteevent", $_POST)) {
            $sql = $connection->prepare("DELETE FROM EventAttendance WHERE event_type = :etype");
            $sql->bindParam(":etype", $_POST["deleteevent"]);
            $sql->execute();
            $sql = $connection->prepare("DELETE FROM Events WHERE event_type = :etype");
            $sql->bindParam(":etype", $_POST["deleteevent"]);
            $sql->execute();
        } else if (array_key_exists("setevent", $_POST)) {
            $settings = fopen("settings.json", "w");
            fwrite($settings, '{"current_event": ' . $_POST["setevent"] . '}');
            fclose($settings);
            $event_type = $_POST["setevent"];
        }
        
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css">
        <script type="text/javascript" src="manage.js"></script>
    </head>
    <body>
        <h1 class="page-title">Management console</h1>
        <div>
            <div class="left-align">
                <a class="white-button" href="index.html">Back</a>
                <button class="blue-button" onclick="return download_csv()" target="_blank">Export to CSV</button>
                <form>
                    <p>View...</p>
                    <table onchange="checkfields()">
                        <tr>
                            <td><input type="radio" name="view" value="all_players" id="all_players" checked></td>
                            <td style="text-align: left"><label>all players in the database</label></td>
                        </tr>
                        <tr>
                            <td><input type="radio" name="view" value="by_date" id="by_date"></td>
                            <td style="text-align: left"><label>players who participated in one or more events on </label></td>
                            <td><input type="date" name="date" id="date_input" disabled required value=<?php echo array_key_exists("date", $_GET) ? $_GET["date"] : "" ?> ></td>
                        </tr>
                            <td><input type="radio" name="view" value="by_name" id="by_name"></td>
                            <td style="text-align: left"><label>players whose name contains </label></td>
                            <td><input type="text" name="name" disabled required id="name" value=<?php echo array_key_exists("name", $_GET) ? $_GET["name"] : ""?> ></td>
                        </tr>
                        </tr>
                            <td><input type="radio" name="view" value="events_participated" id="events_participated"></td>
                            <td style="text-align: left"><label>events participated in by the player with pid </label></td>
                            <td><input type="number" name="player" disabled required id="events_participated_player" value=<?php echo array_key_exists("player", $_GET) ? $_GET["player"] : ""?> ></td>
                        </tr>
                        </tr>
                            <td><input type="radio" name="view" value="attendance" id="attendance"></td>
                            <td style="text-align: left"><label>players who participated in an event of type </label></td>
                            <td>
                                <select name="event_type" id="event_type_selection" disabled required>
                                    <?php 
                                        $sql = $connection->prepare("SELECT * FROM Events");
                                        $sql->execute();
                                        $preresult = $sql->setFetchMode(PDO::FETCH_ASSOC);
                                        $categories = $sql->fetchall();
                                        foreach (new RecursiveArrayIterator($categories) as $k=>$v) {  
                                            echo '<option value="' . $v["event_type"] . '">(' . $v["event_type"] . ') ' . $v["event_name"] . '</option>';
                                        }
                                    ?>
                                </select>
                            </td>
                            <td> on the date </td>
                            <td><input type="date" name="date" id="event_date_input" disabled required value=<?php echo array_key_exists("date", $_GET) ? $_GET["date"] : "" ?> ></td>

                        </tr>
                        <tr>
                            <td></td>   
                            <td>
                                <input type="submit" value="Search database" class="white-button">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="right-align" style="margin-right: 10%; width: 30%">
                <table>
                    <form method="POST">
                        <tr>
                            <th colspan="3">Create a new event category</th>
                        </tr>
                        <tr>
                            <td style="width: 20%"><label>Name </label></td>
                            <td style="width: 40%"><input type="text" name="addcategory" required></td>
                            <td style="width: 40%"><input type="submit" class="green-button" value="Add"></td>
                        </tr>
                    </form>
                    <form method="POST">
                        <tr>
                            <th colspan="3">Delete an event category</th>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align: center">Warning: Deleting an event category will also delete all attendance records for that event!</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <select name="deleteevent">
                                    <?php 
                                        foreach (new RecursiveArrayIterator($categories) as $k=>$v) {  
                                            echo '<option value="' . $v["event_type"] . '">(' . $v["event_type"] . ') ' . $v["event_name"] . '</option>';
                                        }
                                    ?>
                                </select>
                            </td>
                            <td><input type="submit" value="Delete" class="red-button"></td>
                        </tr>
                    </form>
                    <form method="POST">
                        <tr>
                            <th colspan="3">Set the current event</th>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align: center">All players who check in will be checked into the current active event.</td>
                        </tr>
                        <tr>
                            <td>Current event </td>
                            <td>
                                <select name="setevent" value=<?php echo $event_type?>>
                                    <?php 
                                        foreach (new RecursiveArrayIterator($categories) as $k=>$v) {  
                                            echo '<option value="' . $v["event_type"] . '"' . ($v['event_type'] == $event_type ? "selected" : "") .'>(' . $v["event_type"] . ') ' . $v["event_name"] . '</option>';
                                        }
                                    ?>
                                </select>
                            </td>
                            <td><input type="submit" value="Update" class="white-button"></td>
                        </tr>
                    </form>
                </table>
            </div>
            <?php 
                if (array_key_exists("delete", $_GET)) {
                    $sql = $connection->prepare("DELETE FROM Players WHERE pid = :id");
                    $sql->bindParam(":id", $_GET["delete"]);
                    try {
                        $sql->execute();
                    } catch (PDOException $exception) {
                        echo '<div style="text-align: center">';
                        echo '<p class="error-text">That player cannot be deleted because they participated in at least one event.</p>';
                        echo '<a class="red-button" href="manage.php?deleteall=' . $_GET["delete"] . '">Delete them and all records of them</a>';
                        echo "</div>";
                    }
                    
                } else if (array_key_exists("deleteall", $_GET)) {
                    $sql = $connection->prepare("DELETE FROM EventAttendance WHERE pid = :id");
                    $sql->bindParam(":id", $_GET["deleteall"]);
                    $sql2 = $connection->prepare("DELETE FROM Players WHERE pid = :id");
                    $sql2->bindParam(":id", $_GET["deleteall"]);
                    
                    try {
                        $sql->execute();
                        $sql2->execute();
                    } catch (PDOException $exception) {
                        echo '<p class="error-text">An error occurred while attempted to delete the player.</p>';
                    }
                    
                }
            ?>
        </div>
        <table class="results-list" style="width: 95%">
            <thead style="width: 100%">
            <tr>
                <?php 
                if (array_key_exists('view', $_GET)) {
                    $mode = $_GET['view'];
                } else {
                    $mode = 'all_players';
                }

                $results_type = in_array($mode, array( 1 => "all_players", 2 => "by_name", 3 => "by_date")) ? "players" : "events";

                if ($results_type == 'players') {
                    //display a table of players
                    echo '<th style="width: 2%">pid</th>';
                    echo '<th style="width: 10%">First name</th>';
                    echo '<th style="width: 10%">Last name</th>';
                    echo '<th style="width: 8%">Telephone</th>';
                    echo '<th style="width: 10%">Birthday</th>';
                    echo '<th style="width: 15%">Parent name</th>';
                    echo '<th style="width: 8%">Pokemon ID</th>';
                    echo '<th style="width: 12%">MHA Email</th>';
                    echo '<th style="width: 12%">MTGA Email</th>';
                    echo '<th style="width: 13%">Lorcana Email</th>';
                } else if ($results_type == "events") {
                    //display a table of events or player attendance records
                    echo '<th style="width: 5%">pid</th>';
                    echo '<th style="width: 15%">First name</th>';
                    echo '<th style="width: 15%">Last name</th>';
                    echo '<th style="width: 10%">Pokemon ID</th>';
                    echo '<th style="width: 10%">MTGA Email</th>';
                    echo '<th style="width: 10%">MHA Email</th>';
                    echo '<th style="width: 10%">Lorcana Email</th>';
                    echo '<th style="width: 5%">Event date</th>';
                    echo '<th style="width: 20%">Event name</th>';
                    
                }
                ?>
            </tr>
            </thead>
            <?php 
                if ($mode == "all_players") {
                    $sql = $connection->prepare("SELECT * FROM Players");
                } else if ($mode == "by_date") {
                    $sql = $connection->prepare("SELECT * FROM Players WHERE pid IN (SELECT pid FROM EventAttendance WHERE event_date = :eventdate)");
                    $sql->bindParam(":eventdate", $_GET["date"]);
                } else if ($mode == "by_name") {
                    $sql = $connection->prepare("SELECT * FROM Players WHERE CONCAT(fname, ' ', lname) LIKE :query");
                    $query = '%' . $_GET['name'] . '%';
                    $sql->bindParam(":query", $query);
                } else if ($mode == "events_participated") {
                    $sql = $connection->prepare("SELECT EventAttendance.pid, Events.event_name, EventAttendance.event_date, EventAttendance.event_type, Players.fname, Players.lname, Players.pokemon_id, Players.mtg_id, Players.mha_id FROM EventAttendance INNER JOIN Players ON EventAttendance.pid = Players.pid INNER JOIN Events ON Events.event_type = EventAttendance.event_type WHERE EventAttendance.pid = :p_id");
                    $sql->bindParam(":p_id", $_GET["player"]);
                } else if ($mode == "attendance") {
                    $sql = $connection->prepare("SELECT EventAttendance.pid, Events.event_name, EventAttendance.event_date, Players.fname, Players.lname, Players.pokemon_id, Players.mtg_id, Players.mha_id, Players.email FROM EventAttendance INNER JOIN Players ON EventAttendance.pid = Players.pid INNER JOIN Events ON Events.event_type = EventAttendance.event_type WHERE EventAttendance.event_type = :etype AND EventAttendance.event_date = :edate");
                    $sql->bindParam(":etype", $_GET["event_type"]);
                    $sql->bindParam(":edate", $_GET["date"]);
                }

                if ($sql->execute()) {
                    $result = $sql->setFetchMode(PDO::FETCH_ASSOC);
                    foreach (new RecursiveArrayIterator($sql->fetchall()) as $k=>$v) {
                        if ($results_type == "players") {
                            echo '<tr>';
                            echo '<td class="results-list">' . $v['pid'] . '</td>';
                            echo '<td class="results-list">' . ($v['fname'] == "" ? '<span style="color: grey">(empty)</span>' : $v['fname']) . '</td>';
                            echo '<td class="results-list">' . ($v['lname'] == "" ? '<span style="color: grey">(empty)</span>' : $v['lname']) . '</td>';
                            echo '<td class="results-list">' . ($v['phone'] == "" ? '<span style="color: grey">(empty)</span>' : $v['phone']) . '</td>';
                            echo '<td class="results-list">' . ($v['bday'] == "" ? '<span style="color: grey">(empty)</span>' : $v['bday']) . '</td>';
                            echo '<td class="results-list">' . ($v['parent_name'] == "" ? '<span style="color: grey">(empty)</span>' : $v['parent_name']) . '</td>';
                            echo '<td class="results-list">' . ($v['pokemon_id'] == "" ? '<span style="color: grey">(empty)</span>' : $v['pokemon_id']) . '</td>';
                            echo '<td class="results-list">' . ($v['mha_id'] == "" ? '<span style="color: grey">(empty)</span>' : $v['mha_id']) . '</td>';
                            echo '<td class="results-list">' . ($v['mtg_id'] == "" ? '<span style="color: grey">(empty)</span>' : $v['mtg_id']) . '</td>';
                            echo '<td class="results-list">' . ($v['email'] == "" ? '<span style="color: grey">(empty)</span>' : $v['email']) . '</td>';
                            echo '<td><a class="white-button" href="editplayer.php?player=' . $v['pid'] . '">Edit</a></td>';
                            echo '<td><a class="red-button" href="manage.php?delete=' . $v['pid'] . '">Delete</a></td>';
                            echo '</tr>';
                        } else if ($results_type == "events") {
                            echo '<tr>';
                            echo '<td class="results-list">' . $v['pid'] . '</td>';
                            echo '<td class="results-list">' . ($v['fname'] == "" ? '<span style="color: grey">(empty)</span>' : $v['fname']) . '</td>';
                            echo '<td class="results-list">' . ($v['lname'] == "" ? '<span style="color: grey">(empty)</span>' : $v['lname']) . '</td>';
                            echo '<td class="results-list">' . ($v['pokemon_id'] == "" ? '<span style="color: grey">(empty)</span>' : $v['pokemon_id']) . '</td>';
                            echo '<td class="results-list">' . ($v['mtg_id'] == "" ? '<span style="color: grey">(empty)</span>' : $v['mtg_id']) . '</td>';
                            echo '<td class="results-list">' . ($v['mha_id'] == "" ? '<span style="color: grey">(empty)</span>' : $v['mha_id']) . '</td>';
                            echo '<td class="results-list">' . ($v['email'] == "" ? '<span style="color: grey">(empty)</span>' : $v['email']) . '</td>';
                            echo '<td class="results-list">' . ($v['event_date'] == "" ? '<span style="color: grey">(empty)</span>' : $v['event_date']) . '</td>';
                            echo '<td class="results-list">' . ($v['event_name'] == "" ? '<span style="color: grey">(empty)</span>' : $v['event_name']) . '</td>';
                            
                            echo '<td></td>';
                            echo '</tr>';
                        }
                        
                    }
                } else {
                    echo "Database connection failed!";
                }

                
                
            ?>
        </table>
        <script type="text/javascript">
            defaultchecked()
        </script>
    </body>
</html>
