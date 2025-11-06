document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('enrollmentStatusModal');
    const closeBtn = document.querySelector('.close-modal');
    const modalBody = document.getElementById('modal-body-content');
    
    // Event delegation for check status buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('check-status-btn')) {
            const enrolleeId = e.target.getAttribute('data-enrollee-id');
            const userId = e.target.getAttribute('data-user-id');
            
            if (enrolleeId && userId) {
                openModal(enrolleeId, userId);
            }
        }
        
        // Handle edit enrollment form button in modal
        if (e.target.classList.contains('edit-enrollment-form')) {
            const enrolleeId = e.target.getAttribute('data-id');
            if (enrolleeId) {
                modal.style.display = 'none';
                window.location.href = `./user_enrollment_form.php?edit=1&id=${enrolleeId}`;
            }
        }
    });
    
    // Close modal events
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }
    
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    function openModal(enrolleeId, userId) {
        modal.style.display = 'block';
        modalBody.innerHTML = '<div class="loading">Loading...</div>';
        
        fetch(`../../../BackEnd/templates/user/fetchEnrollmentStatus.php?enrollee_id=${enrolleeId}&user_id=${userId}`)
            .then(response => response.text())
            .then(data => {
                modalBody.innerHTML = data;
            })
            .catch(error => {
                modalBody.innerHTML = '<div class="error-message">Failed to load enrollment status</div>';
                console.error('Error:', error);
            });
    }
});
