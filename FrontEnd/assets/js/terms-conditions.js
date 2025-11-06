document.addEventListener('DOMContentLoaded', function() {
    const openTermsBtn = document.getElementById('open-terms-btn');
    const closeTermsModal = document.getElementById('close-terms-modal');
    const closeTermsBtn = document.getElementById('close-terms-btn');
    const termsModal = document.getElementById('terms-modal');
    const termsModalBody = document.getElementById('terms-modal-body');
    const termsCheckbox = document.getElementById('terms-acceptance');
    const termsLabel = document.getElementById('terms-label');
    const scrollIndicator = document.querySelector('.scroll-indicator');

    let hasScrolledToBottom = false;

    // Open modal
    openTermsBtn.addEventListener('click', function() {
        termsModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    });

    // Close modal functions
    function closeModal() {
        termsModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    closeTermsModal.addEventListener('click', closeModal);
    closeTermsBtn.addEventListener('click', closeModal);

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === termsModal) {
            closeModal();
        }
    });

    // Detect scroll to bottom
    termsModalBody.addEventListener('scroll', function() {
        const scrollTop = termsModalBody.scrollTop;
        const scrollHeight = termsModalBody.scrollHeight;
        const clientHeight = termsModalBody.clientHeight;
        
        // Check if scrolled to bottom (with 10px threshold)
        if (scrollTop + clientHeight >= scrollHeight - 10) {
            if (!hasScrolledToBottom) {
                hasScrolledToBottom = true;
                enableCheckbox();
                hideScrollIndicator();
            }
        }
    });

    // Enable checkbox
    function enableCheckbox() {
        termsCheckbox.disabled = false;
        termsLabel.classList.add('enabled');
        termsCheckbox.classList.add('enabled');
    }

    // Hide scroll indicator
    function hideScrollIndicator() {
        if (scrollIndicator) {
            scrollIndicator.style.display = 'none';
        }
    }

    // Form validation for terms acceptance
    const registrationForm = document.getElementById('registration-form');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(event) {
            if (!termsCheckbox.checked) {
                event.preventDefault();
                alert('You must accept the Terms & Conditions to proceed.');
                termsCheckbox.focus();
                return false;
            }
        });
    }
});
