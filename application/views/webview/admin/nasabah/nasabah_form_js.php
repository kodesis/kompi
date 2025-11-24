<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        // Function to get current date in YYYY-MM-DD format
        function getCurrentDateFormatted() {
            const today = new Date();
            const year = today.getFullYear();
            // Add 1 to month (since it's 0-indexed) and pad with '0'
            const month = String(today.getMonth() + 1).padStart(2, '0');
            // Pad day with '0'
            const day = String(today.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }

        // Get the input element by its ID
        const dateInput = document.getElementById('tgl_pendaftaran_add');

        // Check if the element exists and set its value
        if (dateInput) {
            dateInput.value = getCurrentDateFormatted();
        }
    });
</script>
<script>
    // Get the elements
    const checkbox = document.getElementById('change_password_check');
    const passwordInput = document.getElementById('password_edit');

    // Add an event listener to the checkbox
    checkbox.addEventListener('change', function() {
        // Toggle the 'disabled' attribute based on the checkbox's checked state
        if (this.checked) {
            passwordInput.removeAttribute('disabled');
        } else {
            passwordInput.setAttribute('disabled', 'disabled');
        }
    });
</script>