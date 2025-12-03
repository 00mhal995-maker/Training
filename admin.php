<?php
// Admin panel: view results and add questions
// Note: For simplicity, this page is not protected by authentication.

// Load existing questions
$questionsFile = __DIR__ . '/questions.json';
$questions = [];
if (file_exists($questionsFile)) {
    $questions = json_decode(file_get_contents($questionsFile), true) ?? [];
}

// Load existing results
$resultsFile = __DIR__ . '/results.json';
$results = [];
if (file_exists($resultsFile)) {
    $results = json_decode(file_get_contents($resultsFile), true) ?? [];
}

// Handle adding a new question
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_question'])) {
    $questionText = trim($_POST['question_text'] ?? '');
    $options = [
        trim($_POST['option1'] ?? ''),
        trim($_POST['option2'] ?? ''),
        trim($_POST['option3'] ?? ''),
        trim($_POST['option4'] ?? ''),
    ];
    $correctIndex = isset($_POST['correct_option']) ? (int)$_POST['correct_option'] : -1;
    
    // Validate
    if ($questionText === '' || in_array('', $options, true) || $correctIndex < 0 || $correctIndex > 3) {
        $message = 'يرجى ملء جميع الحقول بشكل صحيح.';
    } else {
        // Determine next ID
        $nextId = 1;
        if (!empty($questions)) {
            $ids = array_column($questions, 'id');
            $nextId = max($ids) + 1;
        }
        // Add new question
        $questions[] = [
            'id' => $nextId,
            'question' => $questionText,
            'options' => $options,
            'correct' => $correctIndex,
        ];
        
        // Save back to file
        file_put_contents($questionsFile, json_encode($questions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $message = 'تم إضافة السؤال بنجاح.';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - اختبار الاستماع</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>لوحة التحكم</h1>
        <?php if ($message) : ?>
            <p class="alert"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <div class="admin-section">
            <h2>إضافة سؤال جديد</h2>
            <form action="admin.php" method="post">
                <input type="hidden" name="new_question" value="1">
                <label for="question_text">نص السؤال:</label>
                <textarea id="question_text" name="question_text" rows="3" required></textarea>
                
                <label for="option1">الخيار الأول:</label>
                <input type="text" id="option1" name="option1" required>
                
                <label for="option2">الخيار الثاني:</label>
                <input type="text" id="option2" name="option2" required>
                
                <label for="option3">الخيار الثالث:</label>
                <input type="text" id="option3" name="option3" required>
                
                <label for="option4">الخيار الرابع:</label>
                <input type="text" id="option4" name="option4" required>
                
                <label for="correct_option">رقم الإجابة الصحيحة (1-4):</label>
                <select id="correct_option" name="correct_option" required>
                    <option value="0">1</option>
                    <option value="1">2</option>
                    <option value="2">3</option>
                    <option value="3">4</option>
                </select>
                
                <button type="submit">إضافة السؤال</button>
            </form>
        </div>
        
        <div class="admin-section">
            <h2>الأسئلة الحالية</h2>
            <?php if (!empty($questions)) : ?>
                <ol>
                    <?php foreach ($questions as $q) : ?>
                        <li><?php echo htmlspecialchars($q['question'], ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ol>
            <?php else : ?>
                <p>لا توجد أسئلة بعد.</p>
            <?php endif; ?>
        </div>
        
        <div class="admin-section">
            <h2>نتائج الطلاب</h2>
            <?php if (!empty($results)) : ?>
                <table class="results-table">
                    <thead>
                        <tr>
                            <th>اسم الطالب</th>
                            <th>الشعبة</th>
                            <th>عدد الإجابات الصحيحة</th>
                            <th>إجمالي الأسئلة</th>
                            <th>الوقت المستغرق (ث)</th>
                            <th>تاريخ الإرسال</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $res) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($res['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($res['section'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo (int)$res['correct']; ?></td>
                                <td><?php echo (int)$res['total']; ?></td>
                                <td><?php echo round($res['time_taken_sec'], 2); ?></td>
                                <td><?php echo htmlspecialchars($res['submitted_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>لا توجد نتائج بعد.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>