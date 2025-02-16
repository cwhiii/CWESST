<div id="menu-bar">
        <?php
        // Dynamically generate menu buttons based on available popover files
        $popoverFiles = glob('resources/popovers/*.html');
        foreach ($popoverFiles as $file):
            $name = basename($file, '.html');
            // Replace underscores with spaces and capitalize the first letter of each word for display
            $displayName = ucwords(str_replace('_', ' ', $name));
        ?>
            <button class="menu-button" data-popup="<?= htmlspecialchars($name) ?>">
                <img src="resources/images/<?= htmlspecialchars($name) ?>.png" alt="<?= $displayName ?>">
                <?= $displayName ?>
            </button>
        <?php endforeach; ?>
    </div>
