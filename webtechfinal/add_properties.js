document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    
    const nameInput = document.getElementById('p_name');
    const rentInput = document.getElementById('p_rent');
    const imageInput = document.getElementById('p_image');

    function setError(input, message) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        
        const feedback = input.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.innerText = message;
        }
    }

    function setSuccess(input) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    }

    [nameInput, rentInput, imageInput].forEach(input => {
        if (input) {
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                }
            });
        }
    });

    if (form) {
        form.addEventListener('submit', function (event) {
            let isValid = true;

            if (nameInput.value.trim() === '') {
                setError(nameInput, "Property name is required.");
                isValid = false;
            } else {
                setSuccess(nameInput);
            }

            const rentValue = parseFloat(rentInput.value);
            if (rentInput.value === '' || isNaN(rentValue)) {
                setError(rentInput, "Please enter a rent amount.");
                isValid = false;
            } else if (rentValue < 0) {
                setError(rentInput, "Rent cannot be negative.");
                isValid = false;
            } else {
                setSuccess(rentInput);
            }

            if (imageInput.files.length > 0) {
                const file = imageInput.files[0];
                const fileSize = file.size / 1024 / 1024;
                const fileType = file.name.split('.').pop().toLowerCase();
                const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (fileSize > 5) {
                    setError(imageInput, "File is too large (Max 5MB).");
                    isValid = false;
                } else if (!allowedExtensions.includes(fileType)) {
                    setError(imageInput, "Invalid format. Only JPG, PNG, WEBP, or GIF.");
                    isValid = false;
                } else {
                    setSuccess(imageInput);
                }
            } else {
                imageInput.classList.remove('is-invalid');
            }

            if (!isValid) {
                event.preventDefault();
            }
        });
    }
});