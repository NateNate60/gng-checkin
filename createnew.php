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
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $fname = substr($_POST["first_name"], 0, 255);
                $lname = substr($_POST["last_name"], 0, 255);
                $phone = str_replace("-", "", $_POST["phone"]);
                $phone = substr($phone, 0, 10);
                $pid = substr($_POST["player_id"], 0, 16);
                $email = substr($_POST["email"], 0, 255);
                $pname = substr($_POST["parent_name"], 0, 255);
                $mha_id = substr($_POST["mha_id"], 0, 255);
                $mtg_id = substr($_POST["mtg_id"], 0, 255);
                $bday = new DateTime ($_POST["birthdate"]);
                $today = new DateTime("now");
                $under_18 = ($pname == "" && ($bday > $today->modify('-18 year')));
                $today2 = new DateTime("now");
                $under_13 = ($pname == "" && ($bday > $today2->modify('-13 year')));
                $ignore_errors = ($_POST["ignore_errors"] == "ignore");
                $no_parent_error = (!$ignore_errors && $under_18 && $pname == "");
                $no_parent_13_error = (!$ignore_errors && $under_13 && $pname == "");

                if (!$no_parent_error && !$no_parent_13_error) {
                    $dummy_mha = $dummy_mtg = "";

                    $insert_statement = $connection->prepare("INSERT INTO Players (fname, lname, phone, email, bday, parent_name, pokemon_id, mha_id, mtg_id) VALUES (:firstname, :lastname, :telephone, :email_add, :b_day, :parent, :pokemonid, :mhaid, :mtgid)");
                    $insert_statement->bindParam(':firstname', $fname);
                    $insert_statement->bindParam(':lastname', $lname);
                    $insert_statement->bindParam(':telephone', $phone);
                    $insert_statement->bindParam(':email_add', $email);
                    $insert_statement->bindParam(':b_day', $_POST["birthdate"]);
                    $insert_statement->bindParam(':parent', $pname);
                    $insert_statement->bindParam(':pokemonid', $pid);
                    $insert_statement->bindParam(':mhaid', $mha_id);
                    $insert_statement->bindParam(':mtgid', $mtg_id);

                    if ($insert_statement->execute()) {
                        header("Location: checkin.php?player=newest");
                    } else {
                        echo "<p class=\"error-text\">Error!</p>";
                    }
                    
                }
            } 
        ?>
        <div>
            <h1 class="page-title">Add new profile</h1>
            <a class="white-button" href="index.html" style="float: left; margin-left: 10%">Back</a>
            <a class="blue-button" href="existingplayer.php" style="float: right; margin-right: 10%">I already have a profile</a>
        </div>
        <div style="text-align: center">
            <?php 
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if ($no_parent_13_error) {
                        echo "<p class=\"error-text\">(Under 13) Please enter your parent or guardian's name.</p>";
                    } else if ($no_parent_error) {
                        echo "<p class=\"error-text\">(Under 18) Please check whether you want to enter you parent or guardian's name. If you're sure you don't want to enter it, press submit again.</p>";
                    } 
                } else {
                    echo "<p>Please fill out this form with your information.</p>";
                }
            ?>
        </div>
        <table style="margin: auto">
            <form method="POST" action="createnew.php" id="new_player_form">
                <tr>
                    <th style="width: 50%"></th>
                    <th style="width: 50%"></th>
                </tr>
                <tr>
                    <td style="width: 50%">
                        <label>★ First name </label>
                        <input type="text" name="first_name" required value=<?php echo $fname ?> >
                    </td>
                    <td style="width: 50%">
                        <label>★ Last name </label>
                        <input type="text" name="last_name" required value=<?php echo $lname ?> >
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>★ Phone number (or parent's phone number) </label>
                        <input type="tel" name="phone" required value=<?php echo $phone ?> >
                    </td>
                    <td>
                        <label>Pokemon player ID </label>
                        <input type="number" name="player_id" value=<?php echo $pid ?> >
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>Parent or guardian's name </label>
                        <input type="text" name="parent_name" value=<?php echo $pname ?> >
                    </td>
                    <td>
                        <label>MHA UGN Email </label>
                        <input type="email" name="mha_id" value=<?php echo $pid ?> >
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>★ Birthdate </label>
                        <?php 
                            if (is_null($bday)) {
                                echo '<input type="date" name="birthdate" required>';
                            } else {
                                echo '<input type="date" name="birthdate" required value=' . $bday->format("Y-m-d") . '>';
                            }
                        ?>
                    </td>
                    <td>
                        <label>Lorcana/Melee Email </label>
                        <input type="email" name="email" value=<?php echo $email ?> >
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <label>MTG Arena Email </label>
                        <input type="email" name="mtg_id" value=<?php echo $email ?> >
                    </td>
                    
                </tr>
                <tr>
                    <td><p>★ Required</p><button type="submit" form="new_player_form" class="green-button">Submit</button></td>
                    <td></td>
                </tr>
                <input type="text" name="ignore_errors" hidden value=<?php echo $under_18 && ! $under_13 ? "ignore" : "" ?> >
            </form>
        </table>
    </body>
</html>