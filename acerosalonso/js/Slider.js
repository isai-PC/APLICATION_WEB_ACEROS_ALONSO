let currentIndex = 0;

  function moveSlide(direction) {
    const slider = document.getElementById('slider');
    const items = document.querySelectorAll('.slider-item');
    const itemsVisible = 3; // Número de items visibles
    const maxIndex = items.length - itemsVisible;

    currentIndex += direction;
/* Limitar los índices */
    if (currentIndex < 0) currentIndex = 0;
    if (currentIndex > maxIndex) currentIndex = maxIndex;

    slider.style.transform = `translateX(-${currentIndex * (100 / itemsVisible)}%)`;
  }