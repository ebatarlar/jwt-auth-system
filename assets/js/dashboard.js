$(document).ready(function() {
    // Check if user is logged in (token exists)
    const token = localStorage.getItem('jwt_token');
    if (!token) {
        // Redirect to login if no token
        window.location.href = 'index.php';
        return;
    }

   

    // Fetch profile button click handler
    $('#fetch-profile').on('click', function() {
        // Show loading state
        $(this).prop('disabled', true);
        $(this).html('<span class="inline-block animate-spin mr-2">&#8635;</span> Yükleniyor...');
        $('#profile-status').removeClass('hidden');
        
        // Make API request to profile endpoint
        $.ajax({
            url: 'api/profile.php',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                // Update profile information
                if (response.success && response.data) {
                    const user = response.data.user;
                    
                    // Update displayed API user info
                    $('#api-user-name').text(user.name);
                    $('#api-user-email').text(user.email);
                    $('#api-user-id').text(user.id || 'N/A');
                    $('#api-last-updated').text(new Date().toLocaleTimeString());
                    $('#api-profile-data').removeClass('hidden');
                    
                    // Update status
                    $('#profile-status')
                        .removeClass('text-yellow-700 bg-yellow-100')
                        .addClass('text-green-700 bg-green-100')
                        .text('Profil bilgileri başarıyla güncellendi');
                    
                    // Store updated user data
                    localStorage.setItem('user_data', JSON.stringify(user));
                } else {
                    // Show error
                    $('#profile-status')
                        .removeClass('text-yellow-700 bg-yellow-100')
                        .addClass('text-red-700 bg-red-100')
                        .text('Profil bilgileri alınamadı: ' + (response.error || 'Bilinmeyen hata'));
                }
            },
            error: function(xhr) {
                // Handle error
                let errorMessage = 'API isteği başarısız';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                
                // Show error status
                $('#profile-status')
                    .removeClass('text-yellow-700 bg-yellow-100')
                    .addClass('text-red-700 bg-red-100')
                    .text('Hata: ' + errorMessage);
                
                // If unauthorized, redirect to login
                if (xhr.status === 401) {
                    localStorage.removeItem('jwt_token');
                    localStorage.removeItem('user_data');
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    }, 2000);
                }
            },
            complete: function() {
                // Reset button state
                $('#fetch-profile').prop('disabled', false).text('Profil Bilgilerini Yenile');
            }
        });
    });

    // Logout button handler
    $('#logout-button').on('click', function() {
        window.location.href = 'logout.php';
    });
});
