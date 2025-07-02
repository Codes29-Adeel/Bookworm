// Custom JavaScript for the slider
document.addEventListener('DOMContentLoaded', function() {
    const myCarousel = document.getElementById('imageSlider');
    const carousel = new bootstrap.Carousel(myCarousel);
    
    // Optional: Auto-rotate the slider
    let carouselInterval;
    
    function startCarousel() {
        carouselInterval = setInterval(function() {
            carousel.next();
        }, 3000); // Change slide every 3 seconds
    }
    
    function stopCarousel() {
        clearInterval(carouselInterval);
    }
    
    // Start auto-rotation
    startCarousel();
    
    // Pause on hover
    myCarousel.addEventListener('mouseenter', stopCarousel);
    myCarousel.addEventListener('mouseleave', startCarousel);
    
    // Optional: Add touch events for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    myCarousel.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, {passive: true});
    
    myCarousel.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, {passive: true});
    
    function handleSwipe() {
        if (touchEndX < touchStartX) {
            carousel.next();
        }
        if (touchEndX > touchStartX) {
            carousel.prev();
        }
    }
    
    // Optional: Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            carousel.prev();
        } else if (e.key === 'ArrowRight') {
            carousel.next();
        }
    });
});

// In your script.js
document.addEventListener('DOMContentLoaded', function() {
    const sliderData = [
        {
            imageUrl: 'https://via.placeholder.com/800x400?text=Slide+1',
            title: 'First Slide',
            description: 'This is the first slide description'
        },
        {
            imageUrl: 'https://via.placeholder.com/800x400?text=Slide+2',
            title: 'Second Slide',
            description: 'This is the second slide description'
        },
        {
            imageUrl: 'https://via.placeholder.com/800x400?text=Slide+3',
            title: 'Third Slide',
            description: 'This is the third slide description'
        },
        // Add more slides as needed
    ];
    
    const carouselInner = document.querySelector('.carousel-inner');
    const carouselIndicators = document.querySelector('.carousel-indicators');
    
    // Clear any existing items
    carouselInner.innerHTML = '';
    carouselIndicators.innerHTML = '';
    
    // Build the carousel items
    sliderData.forEach((slide, index) => {
        // Create indicator
        const indicator = document.createElement('button');
        indicator.type = 'button';
        indicator.setAttribute('data-bs-target', '#imageSlider');
        indicator.setAttribute('data-bs-slide-to', index);
        indicator.setAttribute('aria-label', `Slide ${index + 1}`);
        if (index === 0) {
            indicator.classList.add('active');
            indicator.setAttribute('aria-current', 'true');
        }
        carouselIndicators.appendChild(indicator);
        
        // Create slide
        const slideItem = document.createElement('div');
        slideItem.classList.add('carousel-item');
        if (index === 0) slideItem.classList.add('active');
        
        const img = document.createElement('img');
        img.src = slide.imageUrl;
        img.classList.add('d-block', 'w-100');
        img.alt = slide.title;
        
        const caption = document.createElement('div');
        caption.classList.add('carousel-caption', 'd-none', 'd-md-block');
        
        const title = document.createElement('h5');
        title.textContent = slide.title;
        
        const desc = document.createElement('p');
        desc.textContent = slide.description;
        
        caption.appendChild(title);
        caption.appendChild(desc);
        slideItem.appendChild(img);
        slideItem.appendChild(caption);
        carouselInner.appendChild(slideItem);
    });
    
    // Initialize the carousel
    const carousel = new bootstrap.Carousel(document.getElementById('imageSlider'));
});