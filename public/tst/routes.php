<?php
session_start(); // <-- تم إضافته هنا

// إعدادات الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "sportz";
$password = "uO1IMUeV7K03MxKNurXW";
$dbname = "sportz";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// جلب جميع ملفات PHP في نفس المجلد (عدا هذا الملف)
$files = array_filter(glob("*.php"), function ($file) {
    return basename($file) !== basename(__FILE__);
});
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>لوحة بسيطة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">لوحة التحكم</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php foreach ($files as $file): 
                    $name = basename($file, '.php'); ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $file ?>"><?= ucfirst($name) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 