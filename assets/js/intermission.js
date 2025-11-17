function updateCountdown() {
    var countdown = document.getElementById('countdown');
    if (!countdown) return;

    var targetDate = parseInt(countdown.dataset.target) * 1000;
    var autoDisable = countdown.dataset.autodisable === 'true';
    var homeUrl = countdown.dataset.homeurl;

    function update() {
        var now = new Date().getTime();
        var distance = targetDate - now;

        if (distance < 0) {
            if (autoDisable) {
                countdown.innerHTML = '<p class="intermission-message">Launching now! Redirecting...</p>';
                setTimeout(function() {
                    window.location.href = homeUrl;
                }, 2000);
            } else {
                countdown.innerHTML = '<p class="intermission-message">We are launching now!</p>';
            }
            return;
        }

        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById('days').textContent = String(days).padStart(2, '0');
        document.getElementById('hours').textContent = String(hours).padStart(2, '0');
        document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
        document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
    }

    update();
    setInterval(update, 1000);
}

updateCountdown();
