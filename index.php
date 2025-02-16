<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require "resources/config/database.php";
require "resources/helpers/utilities.php";
require "resources/helpers/main.php";
require "resources/html/header.php";
?>
<!DOCTYPE html>
<html>
<body>
    <h1 title="The C.W. Epic Story Submissions Tracker" style="font-family: 'Cinzel Decorative', cursive; color: #6b3b22;">
        <img src="resources/images/cwesst.webp" alt="Icon" style="vertical-align: middle;">
        CWESST Manager
    </h1>

    <!-- Menu Bar -->
    <div id="menu-bar">
        <?php
        // Dynamically generate menu buttons based on available popover files
        $popoverFiles = glob("resources/popovers/*.html");
        foreach ($popoverFiles as $file):

            $name = basename($file, ".html");
            // Replace underscores with spaces and capitalize the first letter of each word for display
            $displayName = ucwords(str_replace("_", " ", $name));
            ?>
            <button class="menu-button" data-popup="<?= htmlspecialchars(
                $name
            ) ?>">
                <img src="resources/images/<?= htmlspecialchars(
                    $name
                ) ?>.png" alt="<?= $displayName ?>">
                <?= $displayName ?>
            </button>
        <?php
        endforeach;
        ?>
    </div>

    <!-- Overlay and Popovers -->
    <div class="overlay"></div>

    <?php require "resources/html/popovers.php"; ?>

    <div id="tabs">
        <?php foreach ($tables as $tbl => $info): ?>
            <button class="tab-button <?= $tbl === "stories" ? "active" : "" ?>"
                    data-table="<?= htmlspecialchars($tbl) ?>">
                <?= ucfirst($tbl) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Tables -->
    <?php require "resources/html/tables.php"; ?>
    <audio id="myAudio" src="resources/audio/welcome.mp3"></audio>
    <script src="resources/helpers/clientSideFunctions.js"></script>

















    </body>
</html>
