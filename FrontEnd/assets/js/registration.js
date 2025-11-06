document.addEventListener('DOMContentLoaded', function() {
    const registrationForm = document.getElementById('registration-form');
    
    if (registrationForm) {
        registrationForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Show loader
            if (typeof Loader !== 'undefined') {
                Loader.show();
            }
            
            // Get form data
            const formData = new FormData(this);
            
            try {
                // Correct path from FrontEnd to BackEnd/api
                const response = await fetch('../BackEnd/api/postRegistrationForm.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                // Hide loader
                if (typeof Loader !== 'undefined') {
                    Loader.hide();
                }
                
                if (result.success) {
                    // Show success notification
                    if (typeof showNotification !== 'undefined') {
                        showNotification(result.message || 'Registration successful!', 'success');
                    } else {
                        alert(result.message || 'Registration successful!');
                    }
                    
                    // Redirect to login after 2 seconds
                    setTimeout(() => {
                        window.location.href = 'Login.php';
                    }, 2000);
                } else {
                    // Show error notification
                    if (typeof showNotification !== 'undefined') {
                        showNotification(result.message || 'Registration failed. Please try again.', 'error');
                    } else {
                        alert(result.message || 'Registration failed. Please try again.');
                    }
                }
            } catch (error) {
                // Hide loader
                if (typeof Loader !== 'undefined') {
                    Loader.hide();
                }
                
                console.error('Registration error:', error);
                
                if (typeof showNotification !== 'undefined') {
                    showNotification('An error occurred during registration. Please try again.', 'error');
                } else {
                    alert('An error occurred during registration. Please try again.');
                }
            }
        });
    }
});
