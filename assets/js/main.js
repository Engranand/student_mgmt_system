// Password Show/Hide Toggle
function togglePassword(inputId, iconId) {
    const pass = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (pass.type === 'password') {
        pass.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        pass.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Auto dismiss alerts after 3 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 3000);
});

// Confirm delete
function confirmDelete(msg) {
    return confirm(msg || 'Are you sure you want to delete?');
}