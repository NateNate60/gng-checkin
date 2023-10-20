<?php 
    session_start();
    include_once "config.php";

    $fname = $lname = $bday = $pokemon_id = $pid = $mtg_id = $mha_id = $phone = $email = $parent_name = "";
    $success = null;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $sql = $connection->prepare("UPDATE Players SET fname = :firstname, lname = :lastname, bday = :birthday, phone = :tel, email = :e_mail, parent_name = :pname, pokemon_id = :pkmnid, mtg_id = :mtgid, mha_id = :mhaid WHERE pid = :p_id");
        $sql->bindParam(":firstname", $_POST["fname"]);
        $sql->bindParam(":lastname", $_POST["lname"]);
        $sql->bindParam(":birthday", $_POST["bday"]);
        $sql->bindParam(":tel", $_POST["phone"]);
        $sql->bindParam(":e_mail", $_POST["email"]);
        $sql->bindParam(":pname", $_POST["parent_name"]);
        $sql->bindParam(":pkmnid", $_POST["pokemon_id"]);
        $sql->bindParam(":mtgid", $_POST["mtg_id"]);
        $sql->bindParam(":mhaid", $_POST["mha_id"]);
        $sql->bindParam(":p_id", $_POST["pid"]);
        if ($sql->execute()) {
            $success = true;
        } else {
            $success = false;
        }
    }

    $pid = $_GET["player"];
    $sql = $connection->prepare("SELECT * FROM Players WHERE pid = :p_id");
    $sql->bindParam(":p_id", $pid);
    if ($sql->execute()) {
        $result = $sql->setFetchMode(PDO::FETCH_ASSOC);
        foreach (new RecursiveArrayIterator($sql->fetchall()) as $k=>$v) {
            $fname = $v["fname"];
            $lname = $v["lname"];
            $bday = $v["bday"];
            $phone = $v["phone"];
            $email = $v["email"];
            $parent_name = $v["parent_name"];
            $pokemon_id = $v["pokemon_id"];
            $mha_id = $v["mha_id"];
            $mtg_id = $v["mtg_id"];
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <h1 class="page-title">Edit player</h1>
        <div style="text-align: center">
            <?php 
                if ($success) {
                    echo '<p>Successfully updated record.</p>';
                } else if ($success === false) {
                    echo '<p class="error-text">Failed to update record.</p>';
                }
            ?>
        </div>
        <div style="margin-left: 20%; margin-right: 20%">
            <a class="white-button" href="manage.php">Back</a>
            <table class="centre-align">
                <form method="POST">
                    <input type="hidden" name="pid" value=<?php echo $pid ?> >
                    <tr>
                        <td>First name</td>
                        <td><input type="text" name="fname" value="<?php echo $fname ?>" required></td>
                    </tr>
                    <tr>
                        <td>Last name</td>
                        <td><input type="text" name="lname" value="<?php echo $lname ?>" required></td>
                    </tr>
                    <tr>
                        <td>Birthday</td>
                        <td><input type="date" name="bday" value="<?php echo $bday ?>" required></td>
                    </tr>
                    <tr>
                        <td>Phone number</td>
                        <td><input type="tel" name="phone" value="<?php echo $phone ?>" required></td>
                    </tr>
                    <tr>
                        <td>Lorcana Email</td>
                        <td><input type="email" name="email" value="<?php echo $email ?>"></td>
                    </tr>
                    <tr>
                        <td>Parent name</td>
                        <td><input type="text" name="parent_name" value="<?php echo $parent_name ?>"></td>
                    </tr>
                    <tr>
                        <td>Pokemon ID</td>
                        <td><input type="number" name="pokemon_id" value="<?php echo $pokemon_id ?>"></td>
                    </tr>
                    <tr>
                        <td>MHA Email</td>
                        <td><input type="text" name="mha_id" value="<?php echo $mha_id ?>"></td>
                    </tr>
                    <tr>
                        <td>MTGA Email</td>
                        <td><input type="text" name="mtg_id" value="<?php echo $mtg_id ?>"></td>
                    </tr>
                    <tr>
                        <td><input type="submit" value="Save" class="green-button"></td>
                        <td><a href="manage.php?delete=<?php echo $pid?>" class="red-button">Delete player</a></td>
                    </tr>
                </form>
            </table>
        </div>
    </body>
</html>
