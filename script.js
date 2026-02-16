// --- Mobile Menu Toggle ---
const navSlide = () => {
    const burger = document.querySelector('.burger');
    const nav = document.querySelector('.nav-links');
    const navLinks = document.querySelectorAll('.nav-links li');

    burger.addEventListener('click', () => {
        // Toggle Nav
        nav.classList.toggle('nav-active');

        // Animate Links
        navLinks.forEach((link, index) => {
            if (link.style.animation) {
                link.style.animation = '';
            } else {
                link.style.animation = `navLinkFade 0.5s ease forwards ${index / 7 + 0.3}s`;
            }
        });

        // Burger Animation
        burger.classList.toggle('toggle');
    });
}
navSlide();


// --- Simple Slideshow ---
const slides = document.querySelectorAll('.slide');
let currentSlide = 0;
const slideInterval = 5000; // 5 seconds

function nextSlide() {
    slides[currentSlide].classList.remove('active');
    currentSlide = (currentSlide + 1) % slides.length;
    slides[currentSlide].classList.add('active');
}

// Only run slideshow if slides exist
if (slides.length > 0) {
    setInterval(nextSlide, slideInterval);
}


// --- Add to Cart Simulation ---
// In a real PHP scenario, this could be an AJAX call
const addToCartButtons = document.querySelectorAll('.btn-add');

addToCartButtons.forEach(button => {
    button.addEventListener('click', (e) => {
        const productCard = e.target.closest('.product-card');
        const productName = productCard.querySelector('.product-title').innerText;
        alert(`${productName} telah ditambahkan ke keranjang!`);
    });
});
