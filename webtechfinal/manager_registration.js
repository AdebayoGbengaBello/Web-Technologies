document.getElementById('managerRegistration').addEventListener('submit', function(event) {
    let isValid = true;
    
    const nameInput = document.getElementById('managerName');
    const businessInput = document.getElementById('businessName');
    const emailInput = document.getElementById('managerEmail');
    const phoneInput= document.getElementById('managerPhone');
    const passwordInput = document.getElementById('managerPassword');
    const confirmInput = document.getElementById('confirmPassword');

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

    if (nameInput.value.trim() === '') {
        setError(nameInput);
    } else {
        setSuccess(nameInput);
    }

    if (businessInput.value.trim() === '') {
        setError(businessInput);
    } else {
        setSuccess(businessInput);
    }

    const phonePattern=/^[0-9]{10}$/;
    if (!phonePattern.test(phoneInput.value.trim())){
        setError(phoneInput, "Please enter 10 digits.");
    } else{
        setSuccess(phoneInput);
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

    if (confirmInput.value !== passwordInput.value) {
        setError(confirmInput, "Passwords do not match.");
    } else if (confirmInput.value === '') {
        setError(confirmInput, "Please confirm your password.");
    } else {
        setSuccess(confirmInput);
    }

    if (!isValid) {
        event.preventDefault();
        event.stopPropagation();
    }
});