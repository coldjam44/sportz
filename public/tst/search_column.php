<?php
include 'routes.php'; // فيه الاتصال $conn والتصميم الأساسي

$search = $_GET['column'] ?? '';
$results = [];

if ($search) {
    $res = $conn->query("SHOW TABLES");
    if ($res) {
        while ($row = $res->fetch_array()) {
            $table = $row[0];

            // وصف الجدول للحصول على الأعمدة
            $desc = $conn->query("DESCRIBE `$table`");
            if ($desc) {
                while ($col = $desc->fetch_assoc()) {
                    if (strtolower($col['Field']) === strtolower($search)) {
                        $results[] = [
                            'table' => $table,
                            'column' => $col['Field'],
                            'type' => $col['Type'],
                        ];
                    }
                }
            }
        }
    }
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">🔎 البحث عن عمود في قاعدة البيانات</h2>

    <form class="mb-4" method="get">
        <div class="input-group">
            <input type="text" name="column" class="form-control" placeholder="اكتب اسم العمود مثل product_id" value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary">بحث</button>
        </div>
    </form>

    <?php if ($search): ?>
        <h5 class="mb-3">نتائج البحث عن العمود: <code><?= htmlspecialchars($search) ?></code></h5>

        <?php if (count($results) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>الجدول</th>
                            <th>العمود</th>
                            <th>نوع البيانات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $result): ?>
                            <tr>
                                <td><?= htmlspecialchars($result['table']) ?></td>
                                <td><?= htmlspecialchars($result['column']) ?></td>
                                <td><?= htmlspecialchars($result['type']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-danger">لم يتم العثور على العمود المطلوب في أي جدول.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>
