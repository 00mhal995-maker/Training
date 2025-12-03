<?php
// Start session to store student details
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['student_name'] ?? '');
    $section = trim($_POST['student_section'] ?? '');
    
    // Validate that both fields are provided
    if ($name !== '' && $section !== '') {
        // Store data in session
        $_SESSION['student_name'] = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $_SESSION['student_section'] = htmlspecialchars($section, ENT_QUOTES, 'UTF-8');
        
        // Redirect to test page
        header('Location: test.php');
        exit();
    }
}
// If validation fails redirect back to index
header('Location: index.php');
exit();