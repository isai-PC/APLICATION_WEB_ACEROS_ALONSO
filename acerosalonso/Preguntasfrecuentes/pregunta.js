const cards = document.querySelectorAll('.faq-card');

cards.forEach(card => {
    card.addEventListener('click', () => {
        card.classList.toggle('active');
    });
});
