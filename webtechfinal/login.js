document.getElementById('login').addEventListener('submit', function(event) {
    let isValid = true;
    
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    function setError(input, message) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        if(message) {
            input.nextElementSibling.innerText = message;
        }
        isValid = false;
    }

    function setSuccess(input) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(emailInput.value.trim())) {
        setError(emailInput);
    } else {
        setSuccess(emailInput);
    }

    const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
    if (passwordInput.value==''){
        setError(passwordInput);
    } else if (!passwordPattern.test(passwordInput.value.trim())) {
        setError(passwordInput, "Password must be at least 8 characters and include an uppercase letter, a lowercase letter, and a number.");
    } else {
        setSuccess(passwordInput);
    }

    if (!isValid) {
        event.preventDefault();
        event.stopPropagation();
    }
});