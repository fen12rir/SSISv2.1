document.addEventListener('DOMContentLoaded', function() {
    // Get form elements
    const contactNumber = document.getElementById("contact-number");
    const guardianLname = document.getElementById("guardian-last-name");
    const guardianMname = document.getElementById("guardian-middle-name");
    const guardianFname = document.getElementById("guardian-first-name");

    // Array of name input fields
    const nameFields = [guardianLname, guardianMname, guardianFname];

    // ===== NAME FIELDS VALIDATION (Letters and spaces only) =====
    nameFields.forEach(field => {
        if (!field) return;

        // Prevent non-letter characters on keydown
        field.addEventListener('keydown', function(e) {
            const key = e.key;
            
            // Allow control keys
            const allowedKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Home', 'End', ' '];
            if (allowedKeys.includes(key)) {
                return;
            }
            
            // Only allow letters (a-z, A-Z)
            if (!/^[a-zA-Z]$/.test(key)) {
                e.preventDefault();
            }
        });

        // Clean input on input event (catches paste, etc.)
        field.addEventListener('input', function() {
            // Remove any characters that are not letters or spaces
            this.value = this.value.replace(/[^A-Za-z\s]/g, '');
            
            // Remove multiple consecutive spaces
            this.value = this.value.replace(/\s{2,}/g, ' ');
        });

        // Trim spaces on blur
        field.addEventListener('blur', function() {
            this.value = this.value.trim();
        });
    });

    // ===== CONTACT NUMBER VALIDATION (Numbers only, max 11 digits) =====
    if (contactNumber) {
        // Prevent non-number characters on keydown
        contactNumber.addEventListener('keydown', function(e) {
            const key = e.key;
            const currentLength = this.value.length;
            
            // Allow control keys
            const allowedKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Home', 'End'];
            if (allowedKeys.includes(key)) {
                return;
            }
            
            // Prevent non-number input
            if (isNaN(key) || key === ' ') {
                e.preventDefault();
                return;
            }
            
            // Prevent input if already 11 digits
            if (currentLength >= 11) {
                e.preventDefault();
                return;
            }
        });

        // Clean input on input event (catches paste, etc.)
        contactNumber.addEventListener('input', function() {
            // Remove any non-digit characters
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Limit to 11 digits
            if (this.value.length > 11) {
                this.value = this.value.slice(0, 11);
            }
        });

        // Prevent paste of non-numeric content
        contactNumber.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text');
            const numericOnly = pastedData.replace(/[^0-9]/g, '').slice(0, 11);
            this.value = numericOnly;
        });
    }
});