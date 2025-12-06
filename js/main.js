// Main JavaScript file
document.addEventListener('DOMContentLoaded', function() {
    // Initialize floating cards animation
    initFloatingCards();
    
    // Initialize service cards animation
    initServiceCards();
    
    // Handle contact form submission
    initContactForm();
    
    // Add hover effects to service cards
    addServiceCardHoverEffects();
});

// Login button in hero section
document.addEventListener('DOMContentLoaded', function() {
    
    // Update login button in hero section
    const showLoginBtn = document.querySelector('.btn-primary'); // atau gunakan ID yang spesifik
    if (showLoginBtn && showLoginBtn.textContent.includes('Login')) {
        showLoginBtn.addEventListener('click', function() {
            window.location.href = 'auth.html';
        });
    }
});

// Initialize floating cards animation
function initFloatingCards() {
    const floatingCards = document.querySelectorAll('.floating-card');
    
    floatingCards.forEach((card, index) => {
        // Add animation delay for each card
        card.style.animationDelay = `${index * 0.5}s`;
        
        // Add hover effect
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.05)';
            this.style.boxShadow = '0 15px 35px rgba(31, 38, 135, 0.2)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
        });
    });
}

// Initialize service cards animation on scroll
function initServiceCards() {
    const serviceCards = document.querySelectorAll('.service-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });
    
    serviceCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
}

// Add hover effects to service cards
function addServiceCardHoverEffects() {
    const serviceCards = document.querySelectorAll('.service-card');
    
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.service-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(5deg)';
                icon.style.transition = 'transform 0.3s ease';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.service-icon');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });
}

// Initialize contact form
function initContactForm() {
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form values
            const name = this.querySelector('input[type="text"]').value;
            const email = this.querySelector('input[type="email"]').value;
            const message = this.querySelector('textarea').value;
            
            // Simple validation
            if (!name || !email || !message) {
                showNotification('Harap isi semua field yang wajib diisi.', 'error');
                return;
            }
            
            // Simulate form submission
            showNotification('Pesan Anda telah terkirim! Kami akan menghubungi Anda dalam 1x24 jam.', 'success');
            
            // Reset form
            this.reset();
            
            // In actual implementation, send data to server
            // Example using fetch:
            /*
            fetch('send-message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ name, email, message })
            })
            .then(response => response.json())
            .then(data => {
                showNotification('Pesan berhasil dikirim!', 'success');
                this.reset();
            })
            .catch(error => {
                showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
            });
            */
        });
    }
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        
        if (href !== '#') {
            e.preventDefault();
            const targetElement = document.querySelector(href);
            
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        }
    });
});