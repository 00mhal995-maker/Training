<?php
session_start();
// Ensure student details exist
if (!isset($_SESSION['student_name']) || !isset($_SESSION['student_section'])) {
    header('Location: index.php');
    exit();
}

// Only handle POST submissions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: test.php');
    exit();
}

// Collect posted data
$answers = $_POST['answers'] ?? [];
$startTime = isset($_POST['start_time']) ? (float)$_POST['start_time'] : 0;
$endTime = isset($_POST['end_time']) ? (float)$_POST['end_time'] : 0;

// Calculate time taken in seconds
$timeTaken = $endTime > $startTime ? ($endTime - $startTime) / 1000 : 0;

// Load questions to calculate score
$questionsFile = __DIR__ . '/questions.json';
$questions = [];
if (file_exists($questionsFile)) {
    $contents = file_get_contents($questionsFile);
    $questions = json_decode($contents, true) ?? [];
}

// Compute score
$correctCount = 0;
$totalQuestions = count($questions);

// We'll also store the student's answers for record keeping
$recordedAnswers = [];

foreach ($questions as $q) {
    $qid = $q['id'];
    $correctIndex = $q['correct'];
    if (isset($answers[$qid])) {
        $selected = (int)$answers[$qid];
        $recordedAnswers[$qid] = $selected;
        if ($selected === (int)$correctIndex) {
            $correctCount++;
        }
    } else {
        $recordedAnswers[$qid] = null;
    }
}

// Prepare result entry
$resultEntry = [
    'name' => $_SESSION['student_name'],
    'section' => $_SESSION['student_section'],
    'answers' => $recordedAnswers,
    'correct' => $correctCount,
    'total' => $totalQuestions,
    'time_taken_sec' => $timeTaken,
    'submitted_at' => date('Y-m-d H:i:s'),
];

// Append to results.json
$resultsFile = __DIR__ . '/results.json';
$resultsData = [];
if (file_exists($resultsFile)) {
    $resultsContent = file_get_contents($resultsFile);
    $resultsData = json_decode($resultsContent, true) ?? [];
}

$resultsData[] = $resultEntry;

// Write back with file locking to avoid race conditions
$fp = fopen($resultsFile, 'w');
if ($fp) {
    // Acquire exclusive lock
    if (flock($fp, LOCK_EX)) {
        fwrite($fp, json_encode($resultsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    fclose($fp);
}

// Destroy session to prevent resubmission
session_destroy();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم تسليم الاختبار</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>شكرًا على إتمام الاختبار</h1>
        <p>عدد الأسئلة الصحيحة: <?php echo $correctCount; ?> من <?php echo $totalQuestions; ?></p>
        <p>الوقت المستغرق: <?php echo round($timeTaken, 2); ?> ثانية</p>
        <p>تم تسجيل نتيجتك بنجاح.</p>
        <a href="index.php" class="submit-btn">العودة إلى الصفحة الرئيسية</a>
    </div>
</body>
</html>