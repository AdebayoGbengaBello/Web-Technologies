document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('attendanceForm');
    const feedback = document.getElementById('feedback');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);

            
            fetch('faculty_manage_attendance.php?session_id=' + formData.get('session_id'), {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                feedback.style.display = 'block';
                feedback.textContent = data.message;
                
                if (data.success) {
                    feedback.style.color = 'green';
                } else {
                    feedback.style.color = 'red';
                }
                
                setTimeout(() => {
                    feedback.style.display = 'none';
                }, 3000);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
});