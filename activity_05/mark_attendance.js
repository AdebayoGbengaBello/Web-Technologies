document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('markAttendanceForm');
    const feedback = document.getElementById('feedback');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch('student_mark_attendance.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                feedback.className = '';
                feedback.style.display = 'block';
                feedback.textContent = data.message;

                if (data.success) {
                    feedback.classList.add('success');
                    feedback.style.color = 'green';
                    form.reset();
                } else {
                    feedback.classList.add('error');
                    feedback.style.color = 'red';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                feedback.textContent = 'An unexpected error occurred.';
                feedback.style.color = 'red';
            });
        });
    }
});