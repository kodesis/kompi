<script>
    // Function to handle the password validation logic
    function validatePasswords() {
        const password = document.getElementById('password_add').value;
        const confirmation = document.getElementById('password_confirmation_add').value;
        const messageElement = document.getElementById('password_confirmation_text');

        // 1. Reset class list to default structure
        messageElement.className = 'form-text font-weight-bold';

        // 2. Check if the main password is empty
        if (password === '') {
            // If password_add is empty, show no error/success message
            messageElement.innerHTML = 'Please confirm your password.';
            messageElement.classList.add('text-muted');
            return;
        }

        // 3. Compare the passwords
        if (password === confirmation) {
            // Match: Green text
            messageElement.innerHTML = 'Passwords match perfectly!';
            messageElement.classList.add('text-success');
        } else {
            // No Match: Red text
            messageElement.innerHTML = 'Passwords do not match. Please ensure both entries are identical.';
            messageElement.classList.add('text-danger');
        }
    }

    // Attach the validation function to the 'input' event for real-time checking
    window.onload = () => {
        const passwordInput = document.getElementById('password_add');
        const confirmationInput = document.getElementById('password_confirmation_add');

        passwordInput.addEventListener('input', validatePasswords);
        confirmationInput.addEventListener('input', validatePasswords);

        // Initial call to validate the default '12345' values
        validatePasswords();
    };
</script>