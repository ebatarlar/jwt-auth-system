$(document).ready(function() {
    // Check if user is already logged in (token exists)
    const token = localStorage.getItem('jwt_token');
    if (token) {
        // Redirect to dashboard
        window.location.href = 'dashboard.php';
        return;
    }
    
    // Form submission
    $('#login-form').on('submit', function(e) {
        e.preventDefault();
        
        // Reset alerts
        $('#error-alert').addClass('hidden').text('');
        
        // Get form data
        const email = $('#email').val().trim();
        const password = $('#password').val();
        
        // Basic validation
        if (!email || !validateEmail(email)) {
            showError('Geçerli bir e-posta adresi giriniz.');
            return;
        }
        
        if (!password) {
            showError('Şifre alanı gereklidir.');
            return;
        }
        
        // Prepare data for API
        const loginData = {
            email: email,
            password: password
        };
        
        // Show loading state
        $('#login-button').prop('disabled', true).html('<span class="inline-block animate-spin mr-2">&#8635;</span> Giriş yapılıyor...');
        
        // Send login request
        $.ajax({
            url: 'api/login.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(loginData),
            success: function(response) {
                // Handle successful login
                if (response.success && response.data && response.data.token) {
                    // Store token in localStorage
                    localStorage.setItem('jwt_token', response.data.token);
                    
                    // Store user data if available
                    if (response.data.user) {
                        localStorage.setItem('user_data', JSON.stringify(response.data.user));
                    }
                    
                    // Redirect to dashboard
                    window.location.href = 'dashboard.php';
                } else {
                    // Success but no token
                    showError('Beklenmeyen yanıt: Token alınamadı.');
                    $('#login-button').prop('disabled', false).text('Giriş Yap');
                }
            },
            error: function(xhr) {
                // Handle error
                let errorMessage = 'Giriş başarısız.';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                
                showError(errorMessage);
                $('#login-button').prop('disabled', false).text('Giriş Yap');
            }
        });
    });
    
    // Helper functions
    function showError(message) {
        $('#error-alert').removeClass('hidden').text(message);
    }
    
    function validateEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }
});
