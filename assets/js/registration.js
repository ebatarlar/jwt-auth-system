$(document).ready(function() {
    // Form submission
    $('#registration-form').on('submit', function(e) {
        e.preventDefault();
        
        // Reset alerts
        $('#error-alert, #success-alert').addClass('hidden').text('');
        
        // Client-side validation
        const name = $('#name').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val();
        const confirmPassword = $('#confirm-password').val();
        
        // Validation checks
        if (!name) {
            showError('İsim alanı gereklidir.');
            return;
        }
        
        if (!email) {
            showError('E-posta alanı gereklidir.');
            return;
        }
        
        if (!validateEmail(email)) {
            showError('Geçerli bir e-posta adresi giriniz.');
            return;
        }
        
        if (!password) {
            showError('Şifre alanı gereklidir.');
            return;
        }
        
        if (password.length < 6) {
            showError('Şifre en az 6 karakter olmalıdır.');
            return;
        }
        
        if (password !== confirmPassword) {
            showError('Şifreler eşleşmiyor.');
            return;
        }
        
        // Prepare data for API
        const userData = {
            name: name,
            email: email,
            password: password
        };
        
        // Send registration request
        $.ajax({
            url: 'api/register.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(userData),
            success: function(response) {
                // Handle successful registration
                showSuccess(response.message || 'Kayıt başarılı!');
                
                // Store token in localStorage (still needed for dashboard)
                if (response.data && response.data.token) {
                    localStorage.setItem('jwt_token', response.data.token);
                    
                    // Store user data in localStorage
                    if (response.data.user) {
                        localStorage.setItem('user_data', JSON.stringify(response.data.user));
                    }
                    
                    // Redirect to dashboard
                    setTimeout(function() {
                        window.location.href = 'dashboard.php';
                    }, 1500); // Short delay to show the success message
                }
            },
            error: function(xhr) {
                // Handle error
                let errorMessage = 'Kayıt işlemi başarısız.';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                
                showError(errorMessage);
            }
        });
    });
    
    // Helper functions
    function showError(message) {
        $('#error-alert').removeClass('hidden').text(message);
    }
    
    function showSuccess(message) {
        $('#success-alert').removeClass('hidden').text(message);
    }
    
    function validateEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }
});
