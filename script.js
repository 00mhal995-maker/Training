document.addEventListener('DOMContentLoaded', () => {
    const setupForm = document.getElementById('setup-form');
    const studentInfoForm = document.getElementById('student-info-form');
    const testContainer = document.getElementById('test-container');
    const quizForm = document.getElementById('quiz-form');
    const displayName = document.getElementById('display-name');
    const displaySection = document.getElementById('display-section');
    const timerDisplay = document.querySelector('#timer span');

    let startTime;
    let timerInterval;
    // يمكنك تحديد وقت للاختبار بالدقائق
    const TEST_DURATION_MINUTES = 5; 
    let timeRemaining = TEST_DURATION_MINUTES * 60; // بالثواني

    // === الدالة التي تقوم بتحديث عرض الوقت ===
    function updateTimer() {
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            timerDisplay.textContent = 'انتهى الوقت!';
            submitQuizResults(true); // إرسال تلقائي لانتهاء الوقت
            return;
        }

        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        
        timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        timeRemaining--;
    }

    // === معالج بدء الاختبار ===
    studentInfoForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const name = document.getElementById('student-name').value.trim();
        const section = document.getElementById('student-section').value.trim();

        if (!name || !section) {
            alert('الرجاء إدخال الاسم والشعبة.');
            return;
        }

        // إخفاء نموذج الإعداد وعرض الاختبار
        setupForm.classList.add('hidden');
        testContainer.classList.remove('hidden');

        // عرض بيانات الطالب في رأس الاختبار
        displayName.textContent = `الاسم: ${name}`;
        displaySection.textContent = `الشعبة: ${section}`;
        
        // بدء المؤقت
        startTime = new Date();
        timerInterval = setInterval(updateTimer, 1000);

        // يمكنك تشغيل الصوت تلقائيًا هنا إذا كان المتصفح يسمح بذلك
    });

    // === دالة لجمع وإرسال النتائج ===
    function submitQuizResults(isTimeout = false) {
        if (quizForm.dataset.submitted) {
            alert('تم إرسال الاختبار مسبقاً.');
            return;
        }
        
        // إيقاف المؤقت
        clearInterval(timerInterval);

        const endTime = new Date();
        const studentName = document.getElementById('student-name').value.trim();
        const studentSection = document.getElementById('student-section').value.trim();
        
        // حساب الوقت المستغرق بالثواني
        const timeTakenSeconds = Math.round((endTime - startTime) / 1000);

        const answers = {};
        const questionBlocks = document.querySelectorAll('.question-block');
        
        questionBlocks.forEach(block => {
            const questionId = block.getAttribute('data-question-id');
            const selectedAnswer = document.querySelector(`input[name="q${questionId}"]:checked`);
            
            // تخزين الإجابة (Value) أو "لم يجب"
            answers[`q${questionId}`] = selectedAnswer ? selectedAnswer.value : 'unanswered';
        });

        const resultsData = {
            name: studentName,
            section: studentSection,
            time_taken: timeTakenSeconds,
            is_timeout: isTimeout,
            answers: answers
        };

        // تعيين علامة للإشارة إلى أن الاختبار قد تم إرساله
        quizForm.dataset.submitted = true;
        
        // رسالة تأكيد للطالب
        testContainer.innerHTML = `<h2>شكراً لك، تم إرسال إجاباتك بنجاح.</h2>
                                    <p>الوقت المستغرق: ${timeTakenSeconds} ثانية.</p>
                                    <p>سيتم إعلامك بالنتيجة لاحقاً.</p>`;
        
        // إرسال البيانات إلى ملف PHP
        sendDataToPHP(resultsData);
    }

    // === معالج إنهاء الاختبار ===
    quizForm.addEventListener('submit', (e) => {
        e.preventDefault();
        if (confirm('هل أنت متأكد من إنهاء الاختبار وإرسال النتائج؟')) {
            submitQuizResults(false);
        }
    });
    
    // === دالة الإرسال عبر Fetch/AJAX ===
    function sendDataToPHP(data) {
        fetch('submit_test.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        })
        .then(response => response.text())
        .then(result => {
            console.log('Success:', result);
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء إرسال النتائج. الرجاء التواصل مع المشرف.');
        });
    }
});
