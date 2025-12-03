<?php
session_start();
// Ensure the student has entered their name and section
if (!isset($_SESSION['student_name']) || !isset($_SESSION['student_section'])) {
    header('Location: index.php');
    exit();
}

// Load questions from the JSON file
$questionsFile = __DIR__ . '/questions.json';
$questionsData = [];
if (file_exists($questionsFile)) {
    $jsonContents = file_get_contents($questionsFile);
    $questionsData = json_decode($jsonContents, true) ?? [];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار الاستماع</title>
    <link rel="stylesheet" href="style.css">
    <script defer src="script.js"></script>
</head>
<body>
    <div class="container">
        <h1>اختبار الاستماع</h1>
        <p>الطالب: <strong><?php echo $_SESSION['student_name']; ?></strong> | الشعبة: <strong><?php echo $_SESSION['student_section']; ?></strong></p>
        <form action="submit.php" method="post" id="testForm">
            <div class="audio-container">
                <audio controls>
                    <!-- Using WAV format for compatibility without external converters -->
                    <source src="audio/audio.wav" type="audio/wav">
                    متصفحك لا يدعم تشغيل الصوت.
                </audio>
            </div>
            <?php if (!empty($questionsData)) : ?>
                <?php foreach ($questionsData as $index => $question) : ?>
                    <fieldset class="question">
                        <legend><?php echo ($index + 1) . '. ' . htmlspecialchars($question['question'], ENT_QUOTES, 'UTF-8'); ?></legend>
                        <?php foreach ($question['options'] as $optIndex => $optionText) : ?>
                            <label>
                                <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="<?php echo $optIndex; ?>" required>
                                <?php echo htmlspecialchars($optionText, ENT_QUOTES, 'UTF-8'); ?>
                            </label><br>
                        <?php endforeach; ?>
                    </fieldset>
                <?php endforeach; ?>
                <input type="hidden" name="start_time" id="start_time" value="">
                <input type="hidden" name="end_time" id="end_time" value="">
                <button type="submit" class="submit-btn">إرسال الإجابات</button>
            <?php else : ?>
                <p class="alert">لم يتم إعداد أسئلة حتى الآن. يرجى العودة لاحقًا.</p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>