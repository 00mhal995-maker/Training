<?php
// Start session for storing student info
session_start();
// If the student already started the test, redirect directly to the test page
if (isset($_SESSION['student_name']) && isset($_SESSION['student_section'])) {
    header('Location: test.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نموذج اختبار استماع</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>مرحبا بكم في اختبار الاستماع</h1>
        <form action="start_test.php" method="post" class="login-form">
            <label for="student_name">اسم الطالب:</label>
            <input type="text" id="student_name" name="student_name" required>

            <label for="student_section">الشعبة:</label>
            <input type="text" id="student_section" name="student_section" required>

            <button type="submit">ابدأ الاختبار</button>
        </form>
    </div>
</body>
</html>