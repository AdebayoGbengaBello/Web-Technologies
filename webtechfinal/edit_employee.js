document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editEmployeeForm');
    const nameInput = document.getElementById('empName');
    const emailInput = document.getElementById('empEmail');

    [nameInput, emailInput].forEach(input => {
        input.addEventListener('input', () => {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        });
    });

    form.addEventListener('submit', function(e) {
        let valid = true;
        
        if(nameInput.value.trim() === '') {
            nameInput.classList.add('is-invalid');
            valid = false;
        }
        
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if(!emailPattern.test(emailInput.value.trim())) {
            emailInput.classList.add('is-invalid');
            valid = false;
        }

        if(!valid) e.preventDefault();
    });
});