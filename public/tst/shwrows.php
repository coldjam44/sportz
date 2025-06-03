<?php
include 'routes.php'; // فيه الاتصال $conn والتصميم الأساسي

// جلب أسماء الجداول
$tables = [];
$res = $conn->query("SHOW TABLES");
if ($res) {
    while ($row = $res->fetch_array()) {
        $tables[] = $row[0];
    }
}

// التحقق من وجود جدول محدد
$selectedTable = $_GET['table'] ?? null;
$columns = [];
$rows = [];

if ($selectedTable && in_array($selectedTable, $tables)) {
    $descRes = $conn->query("DESCRIBE `$selectedTable`");
    if ($descRes) {
        while ($row = $descRes->fetch_assoc()) {
            $columns[] = $row;
        }
    }

    // جلب بيانات الجدول (أول 10 صفوف)
    $dataRes = $conn->query("SELECT * FROM `$selectedTable` LIMIT 10");
    if ($dataRes) {
        while ($row = $dataRes->fetch_assoc()) {
            $rows[] = $row;
        }
    }
}
?>

<!-- Scrollable Navbar -->
<div class="bg-dark overflow-auto">
    <div class="nav nav-pills flex-nowrap px-3 py-2" style="white-space: nowrap; overflow-x: auto;">
        <?php foreach ($tables as $tbl): ?>
            <a class="nav-link <?= ($tbl === $selectedTable) ? 'active' : 'text-white' ?>"
               href="?table=<?= urlencode($tbl) ?>">
                <?= htmlspecialchars($tbl) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<div class="container mt-4">
    <?php if ($selectedTable): ?>
        <h2 class="text-center mb-3">📊 أعمدة الجدول: <code><?= htmlspecialchars($selectedTable) ?></code></h2>

        <?php if (!empty($columns)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>العمود</th>
                            <th>النوع</th>
                            <th>Null</th>
                            <th>Key</th>
                            <th>Default</th>
                            <th>Extra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($columns as $col): ?>
                            <tr>
                                <td><?= htmlspecialchars($col['Field']) ?></td>
                                <td><?= htmlspecialchars($col['Type']) ?></td>
                                <td><?= htmlspecialchars($col['Null']) ?></td>
                                <td><?= htmlspecialchars($col['Key']) ?></td>
                                <td><?= htmlspecialchars($col['Default']) ?></td>
                                <td><?= htmlspecialchars($col['Extra']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <h3 class="mt-5 mb-3">📋 بيانات الجدول (أول 10 صفوف)</h3>

            <?php if (!empty($rows)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-secondary">
                            <tr>
                                <?php foreach (array_keys($rows[0]) as $colName): ?>
                                    <th><?= htmlspecialchars($colName) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <?php foreach ($row as $value): ?>
                                        <td><?= htmlspecialchars($value) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-warning">لا توجد بيانات لعرضها.</p>
            <?php endif; ?>

        <?php else: ?>
            <p class="text-center text-warning">لا توجد أعمدة.</p>
        <?php endif; ?>
    <?php else: ?>
        <p class="text-center text-muted">يرجى اختيار جدول من الشريط العلوي.</p>
    <?php endif; ?>
</div>
