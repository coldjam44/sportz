<?php
include 'routes.php'; // يستخدم الاتصال والتصميم من routes.php

// استعلام لجلب أسماء الجداول
$tables = [];
$result = $conn->query("SHOW TABLES");

if ($result) {
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">🗃️ قائمة الجداول في قاعدة البيانات</h2>

    <?php if (!empty($tables)): ?>
        <ul class="list-group">
            <?php foreach ($tables as $table): ?>
                <li class="list-group-item"><?= htmlspecialchars($table) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-danger text-center">لا توجد جداول.</p>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-outline-secondary">⬅️ عودة</a>
    </div>
</div>
