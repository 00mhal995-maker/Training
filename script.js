// This script records the start and end time of the test
document.addEventListener('DOMContentLoaded', function () {
    const startInput = document.getElementById('start_time');
    const endInput = document.getElementById('end_time');
    const form = document.getElementById('testForm');

    // Set start time when the page loads
    const startTime = Date.now();
    if (startInput) {
        startInput.value = startTime;
    }

    // Add submit event listener to record end time
    if (form) {
        form.addEventListener('submit', function () {
            if (endInput) {
                endInput.value = Date.now();
            }
        });
    }
});