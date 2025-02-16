<?php foreach ($tables as $tableName => $tableInfo):
        $data        = readCSV($tableInfo['file']);
        $isSubmits   = ($tableName === 'submissions');
    ?>
    <div class="table-container" id="container-<?= htmlspecialchars($tableName) ?>"
         data-table="<?= htmlspecialchars($tableName) ?>"
         style="<?= ($tableName === 'stories') ? 'display:block' : 'display:none' ?>">
        <table class="editable-table" data-table="<?= htmlspecialchars($tableName) ?>">
            <thead>
                <style>
                    #new-record-row-<?php echo htmlspecialchars($tableName); ?> th::after {
                        content: none !important; /* Remove arrows from the "New" row */
                    }
                </style>
                <tr id="new-record-row-<?= htmlspecialchars($tableName) ?>">
                    <th style="text-align:center">
                        New<br>
                        <button class="new-save-btn" data-table="<?= htmlspecialchars($tableName) ?>" title="Save">
                            <img src="resources/images/floppy.png" alt="Save" style="height:50%">
                        </button>
                    </th>
                    <?php if ($isSubmits): ?>
                        <!-- ... [submissions table handling remains unchanged] ... -->
                        <th>
                            <select name="StoryID">
                                <?php foreach ($storiesData as $story): ?>
                                    <option value="<?= htmlspecialchars($story[0]) ?>">
                                        <?= htmlspecialchars($story[1]) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </th>
                        <th>
                            <select name="PublisherID">
                                <?php foreach ($publishersData as $pub): ?>
                                    <option value="<?= htmlspecialchars($pub[0]) ?>">
                                        <?= htmlspecialchars($pub[1]) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </th>
                        <?php for ($i = 3; $i < count($data['header']); $i++): ?>
                            <th>
                                <?php
                                if (strcasecmp($data['header'][$i], 'Status') === 0): ?>
                                    <select name="Status">
                                        <option value="Submitted">Submitted</option>
                                        <option value="Rejected">Rejected</option>
                                        <option value="Accepted">Accepted</option>
                                        <option value="Bought">Bought</option>
                                        <option value="Published">Published</option>
                                    </select>
                                <?php elseif (strcasecmp($data['header'][$i], 'SubmissionDate') === 0): ?>
                                    <input type="date" name="<?= htmlspecialchars($data['header'][$i]) ?>" value="<?= date('Y-m-d') ?>">
                                <?php else: ?>
                                    <input type="text" name="<?= htmlspecialchars($data['header'][$i]) ?>"
                                        <?= strcasecmp($data['header'][$i], 'DateRecordModified') === 0 ?
                                            'value="'.date('Y-m-d').'" readonly style="background:#eee"' :
                                            'placeholder="'.$data['header'][$i].'"' ?>>
                                <?php endif; ?>
                            </th>
                        <?php endfor; ?>
                    <?php else: ?>
                        <?php for ($i = 1; $i < count($data['header']); $i++): ?>
                            <th>
                                <?php
                                if (($tableName === 'agents' || $tableName === 'editors') && strcasecmp($data['header'][$i], 'InterestedInStoryID') === 0): ?>
                                    <select name="InterestedInStoryID">
                                        <option value="">-- Select Story --</option>
                                        <?php foreach ($storiesData as $story): ?>
                                            <option value="<?= htmlspecialchars($story[0]) ?>">
                                                <?= htmlspecialchars($story[1]) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <input type="text" name="<?= htmlspecialchars($data['header'][$i]) ?>"
                                        <?= strcasecmp($data['header'][$i], 'DateRecordModified') === 0 ?
                                            'value="'.date('Y-m-d').'" readonly style="background:#eee"' :
                                            (($tableName === 'stories' && strcasecmp($data['header'][$i], 'Type') === 0) ?
                                                'value="Auto-Populated" readonly style="background:#eee"' :
                                                'placeholder="'.htmlspecialchars($data['header'][$i]).'"') ?>>
                                <?php endif; ?>
                            </th>
                        <?php endfor; ?>
                    <?php endif; ?>
                </tr>
                <tr>
                    <th>Actions</th>
                    <?php if ($isSubmits): ?>
                        <th>Story Title</th>
                        <th>Publisher Name</th>
                        <?php for ($i = 3; $i < count($data['header']); $i++): ?>
                            <th><?= htmlspecialchars($data['header'][$i]) ?></th>
                        <?php endfor; ?>
                    <?php else: ?>
                        <?php for ($i = 1; $i < count($data['header']); $i++): ?>
                            <th><?= htmlspecialchars($data['header'][$i]) ?></th>
                        <?php endfor; ?>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['rows'] as $row): ?>
                    <tr data-id="<?= htmlspecialchars($row[0]) ?>">
                        <td class="action-cell">
                            <button class="edit-btn" title="Edit">
                                <img src="resources/images/edit.png" alt="Edit">
                            </button>
                            <button class="save-btn" style="display:none" title="Save">
                                <img src="resources/images/floppy.png" alt="Save">
                            </button>
                            <button class="cancel-btn" style="display:none" title="Cancel">
                                <img src="resources/images/undo.png" alt="Cancel">
                            </button>
                            <button class="delete-btn" title="Delete">
                                <img src="resources/images/trash.png" alt="Delete">
                            </button>
                        </td>
                        <?php if ($isSubmits):
                            $statusIndex = array_search('Status', $data['header']);
                        ?>
                            <td class="data-cell" data-column="StoryID" title="StoryID: <?= htmlspecialchars($row[1]) ?>">
                                <span class="cell-content"><?= linkifyText(getLookupValue($row[1], $storiesData)) ?></span>
                                <select class="edit-input" name="StoryID">
                                    <?php foreach ($storiesData as $story): ?>
                                        <option value="<?= htmlspecialchars($story[0]) ?>"
                                            <?= ($story[0] == $row[1]) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($story[1]) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="data-cell" data-column="PublisherID" title="PublisherID: <?= htmlspecialchars($row[2]) ?>">
                                <span class="cell-content"><?= linkifyText(getLookupValue($row[2], $publishersData)) ?></span>
                                <select class="edit-input" name="PublisherID">
                                    <?php foreach ($publishersData as $pub): ?>
                                        <option value="<?= htmlspecialchars($pub[0]) ?>"
                                            <?= ($pub[0] == $row[2]) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($pub[1]) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <?php for ($i = 3; $i < count($data['header']); $i++): ?>
                                <td class="data-cell" data-column="<?= htmlspecialchars($data['header'][$i]) ?>">
                                    <span class="cell-content"><?= linkifyText($row[$i]) ?></span>
                                    <?php if (strcasecmp($data['header'][$i], 'Status') === 0): ?>
                                        <select class="edit-input" name="Status">
                                            <option value="Submitted" <?= ($row[$i] === 'Submitted') ? 'selected' : '' ?>>Submitted</option>
                                            <option value="Rejected" <?= ($row[$i] === 'Rejected') ? 'selected' : '' ?>>Rejected</option>
                                            <option value="Accepted" <?= ($row[$i] === 'Accepted') ? 'selected' : '' ?>>Accepted</option>
                                            <option value="Bought" <?= ($row[$i] === 'Bought') ? 'selected' : '' ?>>Bought</option>
                                            <option value="Published" <?= ($row[$i] === 'Published') ? 'selected' : '' ?>>Published</option>
                                        </select>
                                    <?php elseif (strcasecmp($data['header'][$i], 'SubmissionDate') === 0): ?>
                                        <input type="date" class="edit-input" name="<?= htmlspecialchars($data['header'][$i]) ?>" value="<?= htmlspecialchars($row[$i]) ?>">
                                    <?php else: ?>
                                        <input type="text" class="edit-input" name="<?= htmlspecialchars($data['header'][$i]) ?>" value="<?= htmlspecialchars($row[$i]) ?>"
                                            <?= strcasecmp($data['header'][$i], 'DateRecordModified') === 0 ? 'readonly style="background:#eee"' : '' ?>>
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                        <?php elseif ($tableName === 'agents' || $tableName === 'editors'):
                            $interestedIndex = array_search('InterestedInStoryID', $data['header']);
                            for ($i = 1; $i < count($data['header']); $i++): ?>
                                <td class="data-cell" data-column="<?= htmlspecialchars($data['header'][$i]) ?>"
                                    <?php if ($i === $interestedIndex): ?>
                                        title="StoryID: <?= htmlspecialchars($row[$i]) ?>"
                                    <?php endif; ?>>
                                    <span class="cell-content">
                                        <?php
                                        if ($i === $interestedIndex && $row[$i]) {
                                            echo linkifyText(getLookupValue($row[$i], $storiesData));
                                        } else {
                                            echo linkifyText($row[$i]);
                                        }
                                        ?>
                                    </span>
                                    <?php if ($i === $interestedIndex): ?>
                                        <select class="edit-input" name="<?= htmlspecialchars($data['header'][$i]) ?>">
                                            <option value="">-- Select Story --</option>
                                            <?php foreach ($storiesData as $story): ?>
                                                <option value="<?= htmlspecialchars($story[0]) ?>"
                                                    <?= ($story[0] == $row[$i]) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($story[1]) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <input type="text" class="edit-input" name="<?= htmlspecialchars($data['header'][$i]) ?>" value="<?= htmlspecialchars($row[$i]) ?>"
                                            <?= strcasecmp($data['header'][$i], 'DateRecordModified') === 0 ? 'readonly style="background:#eee"' : '' ?>>
                                    <?php endif; ?>
                                </td>
                        <?php endfor;
                        else: ?>
                            <?php for ($i = 1; $i < count($data['header']); $i++): ?>
                                <td class="data-cell" data-column="<?= htmlspecialchars($data['header'][$i]) ?>">
                                    <span class="cell-content"><?= linkifyText($row[$i]) ?></span>
                                    <input type="text" class="edit-input" name="<?= htmlspecialchars($data['header'][$i]) ?>" value="<?= htmlspecialchars($row[$i]) ?>"
                                        <?= strcasecmp($data['header'][$i], 'DateRecordModified') === 0 ? 'readonly style="background:#eee"' : '' ?>>
                                </td>
                            <?php endfor; ?>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endforeach; ?>

    <div id="calendar-popup"></div>
