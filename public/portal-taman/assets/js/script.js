const button = document.getElementById('menu');
const navLinks = document.getElementById('links');

button?.addEventListener('click', () => {
    navLinks.classList.toggle('show');
});


// =========================
// SLIDER FOTO TAMAN (detail.php)
// =========================

const slider = document.getElementById('parkSlider');

if (slider) {

    const track = slider.querySelector('.slider-track');
    const slides = slider.querySelectorAll('.slide');
    const dots = slider.querySelectorAll('.dot');
    const prevBtn = slider.querySelector('.prev');
    const nextBtn = slider.querySelector('.next');

    let current = 0;

    function goToSlide(index) {
        current = (index + slides.length) % slides.length;

        track.style.transform = `translateX(-${current * 100}%)`;

        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === current);
        });
    }

    prevBtn?.addEventListener('click', () => goToSlide(current - 1));
    nextBtn?.addEventListener('click', () => goToSlide(current + 1));

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            goToSlide(parseInt(dot.dataset.index, 10));
        });
    });

    // Geser pakai swipe di HP
    let startX = 0;

    track.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
    });

    track.addEventListener('touchend', (e) => {
        const diff = e.changedTouches[0].clientX - startX;

        if (diff > 50) goToSlide(current - 1);
        if (diff < -50) goToSlide(current + 1);
    });
}