<?php
    // Loop through the popover files and generate each popover dynamically
    foreach ($popoverFiles as $file):
        $name = basename($file, '.html');
        // Replace underscores with spaces and capitalize the first letter of each word for display
        $displayName = ucwords(str_replace('_', ' ', $name));
    ?>
        <div class="popover" id="<?= htmlspecialchars($name) ?>-popup">
            <div class="popover-header">
                <div>
                    <h2>
                        <?php if (file_exists("resources/images/{$name}.png")): ?>
                            <img src="resources/images/<?= htmlspecialchars($name) ?>.png" alt="<?= $displayName ?>" style="vertical-align: middle;">
                        <?php endif; ?>
                        <?= $displayName ?>
                    </h2>
                </div>
                <span class="close-button">×</span>
            </div>
            <div class="popover-content">
                <?php include $file; // Dynamically include the content of the popover ?>
            </div>
        </div>
    <?php endforeach; ?>