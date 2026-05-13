document.addEventListener('DOMContentLoaded', function() {
    const burger = document.getElementById('burger-menu');
    const mobileMenu = document.getElementById('mobile-menu');

    if (burger && mobileMenu) {
        burger.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
            
            if (mobileMenu.classList.contains('hidden')) {
                burger.textContent = '☰';
            } else {
                burger.textContent = '✕';
            }
        });
    }
});