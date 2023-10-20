<?php 
    session_start();
    require_once "config.php";

    $fname = $lname = $phone = $pid = $email = $pname = $bday = $today = $under_18 = $today2 = $under_13 = $ignore_errors = $no_parent_error = $no_parent_13_error = null;
?>
<!DOCTYPE HTML>
<html>
    <head>
        <link rel="stylesheet" href="style.css">
        <script type="text/javascript" src="form.js"></script>
    </head>
    <body>
        <?php 
            if (array_key_exists("query", $_GET)) {
                $query = $_GET["query"];
            } else {
                $query = "";
            }
        ?>
        <div>
            <h1 class="page-title">Search for an existing profile</h1>
            <a class="white-button" href="index.html" style="float: left; margin-left: 10%">Back</a>
            <a class="green-button" href="createnew.php" style="float: right; margin-right: 10%">My name wasn't found</a>
        </div>
        <div style="text-align: center">
            <p>Begin typing your name or phone number here, then click "search" to look up the records in our database.</p>
        </div>
        <div class="centre-align">
            <form>
                <input type="text" name="query" style="width: 50%; height: 30px; font-size: 25px" required value=<?php echo $query?> >
                <input type="submit" value="Search" style="font-size: 25px;">
            </form>
            <table style="width: 100%">
                <tr>
                    <th style="width: 30%">First name</th>
                    <th style="width: 30%">Last name</th>
                    <th style="width: 30%">Phone number</th>
                    <th></th>
                </tr>
                <?php 
                    if ($query != "" && (strpos($query, "_") === false) && (strpos($query, "%") === false) && $query != ' ') {
                        $query = '%' . $query . '%';
                        $sql = $connection->prepare("SELECT pid, fname, lname, phone FROM Players WHERE CONCAT(fname, ' ', lname) LIKE :query OR phone LIKE :query");
                        $sql->bindParam(":query", $query);
                        if ($sql->execute()) {
                            $result = $sql->setFetchMode(PDO::FETCH_ASSOC);
                            foreach (new RecursiveArrayIterator($sql->fetchall()) as $k=>$v) {
                                echo '<tr>';
                                echo '<td class="results-list">' . $v['fname'] . '</td>';
                                echo '<td class="results-list">' . $v['lname'] . '</td>';
                                echo '<td class="results-list">XXX-XXX-' . substr($v['phone'], -4) . '</td>';
                                echo '<td style="text-align: right"><a class="white-button" href="checkin.php?player=' . $v['pid'] . '">Select</a></td>';
                                echo '</tr>';
                            }
                        }
                    }
                ?>
            </table>
        </div>
    </body>
</html>