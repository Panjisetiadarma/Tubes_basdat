// Load navbar dynamically
document.addEventListener('DOMContentLoaded', function() {
    const navbarContainer = document.getElementById('navbar-container');
    
    // Navbar HTML
    const navbarHTML = `
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="#home">
                    Notaris<span style="color: #6A85FF;">Pro</span>
                </a>

                <!-- Mobile Toggle Button -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" 
                        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation Menu -->
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="#home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#services">Layanan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Kontak</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="consultationBtnNav">Konsultasi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link glass-btn" href="auth.html" id="loginBtnNav">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    `;
    
    // Insert navbar HTML
    navbarContainer.innerHTML = navbarHTML;
    
    // Initialize navbar functionality
    initNavbar();
});

// Initialize navbar functionality
function initNavbar() {
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Active link navigation
    const navLinks = document.querySelectorAll('.nav-link');
    
    // Function to update active link
    function updateActiveLink() {
        const scrollPos = window.scrollY + 100;
        
        // Get all sections
        const sections = document.querySelectorAll('section[id]');
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');
            
            if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                // Remove active class from all links
                navLinks.forEach(link => {
                    link.classList.remove('active');
                });
                
                // Add active class to corresponding link
                const activeLink = document.querySelector(`.nav-link[href="#${sectionId}"]`);
                if (activeLink) {
                    activeLink.classList.add('active');
                }
            }
        });
    }
    
    // Update active link on scroll
    window.addEventListener('scroll', updateActiveLink);
    
    // Smooth scroll for nav links - PERBAIKAN DI SINI
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            
            // Perbaikan: Periksa apakah targetId valid dan bukan hanya '#'
            if (targetId && targetId.startsWith('#') && targetId.length > 1) {
                e.preventDefault();
                
                // Remove active class from all links
                navLinks.forEach(item => {
                    item.classList.remove('active');
                });
                
                // Add active class to clicked link
                this.classList.add('active');
                
                // Smooth scroll to target
                const targetSection = document.querySelector(targetId);
                if (targetSection) {
                    window.scrollTo({
                        top: targetSection.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
                
                // Close mobile menu if open
                if (window.innerWidth < 992) {
                    const navbarCollapse = document.getElementById('mainNavbar');
                    if (navbarCollapse.classList.contains('show')) {
                        const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                            toggle: false
                        });
                        bsCollapse.hide();
                    }
                }
            }
            // Jika href adalah '#', jangan lakukan apa-apa atau biarkan default behavior
        });
    });

    // Login button functionality
    const loginBtn = document.getElementById('loginBtnNav');
    if (loginBtn) {
        // Biarkan link bekerja secara normal (ke auth.html)
        // Hapus event listener yang menimpa href
        loginBtn.addEventListener('click', function(e) {
            // Tidak perlu preventDefault karena kita ingin link bekerja
            // Cukup pastikan href sudah di-set ke "auth.html"
        });
    }

    // Consultation button functionality
    const consultationBtn = document.getElementById('consultationBtnNav');
    if (consultationBtn) {
        consultationBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (window.location.pathname.includes('auth.html')) {
                // Jika di halaman auth, arahkan ke login tab
                window.location.href = 'auth.html';
            } else {
                // Jika di halaman lain, show notification
                showNotification('Silakan login terlebih dahulu untuk konsultasi.', 'info');
            }
        });
    }
}

// Notification function
function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Style the notification
    Object.assign(notification.style, {
        position: 'fixed',
        top: '90px',
        right: '20px',
        padding: '15px 20px',
        borderRadius: '8px',
        zIndex: '9999',
        fontWeight: '500',
        boxShadow: '0 5px 15px rgba(0, 0, 0, 0.1)',
        transform: 'translateX(100%)',
        transition: 'transform 0.3s ease',
        maxWidth: '400px',
        backgroundColor: type === 'success' ? '#4CAF50' : type === 'error' ? '#FF5252' : 'rgba(106, 133, 255, 0.9)',
        color: 'white',
        backdropFilter: 'blur(10px)',
        border: '1px solid rgba(255, 255, 255, 0.2)'
    });
    
    // Add to DOM
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 10);
    
    // Remove after 5 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 5000);
}