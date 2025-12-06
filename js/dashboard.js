// Dashboard JavaScript
document.addEventListener('DOMContentLoaded', async function() {
    console.log('Dashboard loading...');
    
    // Check authentication using verifyToken
    try {
        const isAuthenticated = await authManager.verifyToken();
        console.log('Auth check result:', isAuthenticated);
        
        if (!isAuthenticated) {
            console.log('Not authenticated, redirecting to login...');
            showNotification('Sesi telah habis. Silakan login kembali.', 'error');
            setTimeout(() => {
                window.location.href = 'auth.html';
            }, 2000);
            return;
        }
    } catch (error) {
        console.error('Auth verification error:', error);
        showNotification('Terjadi kesalahan autentikasi.', 'error');
        setTimeout(() => {
            window.location.href = 'auth.html';
        }, 2000);
        return;
    }

    // Get current user
    const user = authManager.getCurrentUser();
    console.log('Current user:', user);
    
    if (!user) {
        console.log('No user found, redirecting...');
        showNotification('Sesi tidak valid. Silakan login kembali.', 'error');
        setTimeout(() => {
            window.location.href = 'auth.html';
        }, 2000);
        return;
    }

    // DOM Elements
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const logoutBtn = document.getElementById('logoutBtn');
    const todayBtn = document.getElementById('todayBtn');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    const currentMonthYear = document.getElementById('currentMonthYear');
    const calendarGrid = document.getElementById('calendarGrid');
    const userNameElements = document.querySelectorAll('#userName, #sidebarUserName, #welcomeUserName');
    const documentsTable = document.getElementById('documentsTable');
    const activityList = document.querySelector('.activity-list');

    // Initialize
    initDashboard();
    updateUserInfo(user);
    initCalendar();
    initCharts();
    loadRecentActivities();
    loadRecentDocuments();

    // Initialize dashboard
    function initDashboard() {
        // Set current date
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('currentDate').textContent = 
            now.toLocaleDateString('id-ID', options);
        
        // Update stats with random data
        updateStats();
    }

    // Update user information
    function updateUserInfo(user) {
        if (user) {
            const fullName = `${user.firstName} ${user.lastName}`;
            const firstName = user.firstName;
            
            userNameElements.forEach(element => {
                if (element.id === 'welcomeUserName') {
                    element.textContent = firstName;
                } else {
                    element.textContent = fullName;
                }
            });
            
            // Update email in sidebar
            const userEmail = document.querySelector('.user-profile p');
            if (userEmail && user.email) {
                userEmail.textContent = user.email;
            }
            
            console.log('User info updated:', fullName);
        }
    }

    // Update stats with random data
    function updateStats() {
        document.getElementById('docCount').textContent = Math.floor(Math.random() * 5) + 10;
        document.getElementById('appointmentCount').textContent = Math.floor(Math.random() * 3) + 1;
        document.getElementById('verifiedCount').textContent = Math.floor(Math.random() * 3) + 5;
        document.getElementById('consultationCount').textContent = Math.floor(Math.random() * 3) + 3;
    }

    // ... (sisanya sama seperti sebelumnya, pastikan logoutBtn event handler ada)

    // Logout handler
    logoutBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        
        if (confirm('Apakah Anda yakin ingin logout?')) {
            try {
                await authManager.logout();
                showNotification('Logout berhasil. Mengalihkan...', 'success');
                
                setTimeout(() => {
                    window.location.href = 'auth.html?logout=true';
                }, 1500);
            } catch (error) {
                showNotification('Logout gagal. Silakan coba lagi.', 'error');
            }
        }
    });
    
});

// Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Check authentication
    if (!authManager.checkAuth()) {
        window.location.href = 'auth.html';
        return;
    }

    // DOM Elements
    const user = authManager.getCurrentUser();
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const logoutBtn = document.getElementById('logoutBtn');
    const todayBtn = document.getElementById('todayBtn');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    const currentMonthYear = document.getElementById('currentMonthYear');
    const calendarGrid = document.getElementById('calendarGrid');
    const userNameElements = document.querySelectorAll('#userName, #sidebarUserName, #welcomeUserName');
    const documentsTable = document.getElementById('documentsTable');
    const activityList = document.querySelector('.activity-list');

    // Initialize
    initDashboard();
    updateUserInfo();
    initCalendar();
    initCharts();
    loadRecentActivities();
    loadRecentDocuments();

    // Initialize dashboard
    function initDashboard() {
        // Set current date
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('currentDate').textContent = 
            now.toLocaleDateString('id-ID', options);
        
        // Update stats with random data (in production, this would come from API)
        updateStats();
    }

    // Update user information
    function updateUserInfo() {
        if (user) {
            const fullName = `${user.firstName} ${user.lastName}`;
            const firstName = user.firstName;
            
            userNameElements.forEach(element => {
                if (element.id === 'welcomeUserName') {
                    element.textContent = firstName;
                } else {
                    element.textContent = fullName;
                }
            });
            
            // Update email in sidebar
            const userEmail = document.querySelector('.user-profile p');
            if (userEmail && user.email) {
                userEmail.textContent = user.email;
            }
        }
    }

    // Update stats with random data
    function updateStats() {
        document.getElementById('docCount').textContent = Math.floor(Math.random() * 5) + 10;
        document.getElementById('appointmentCount').textContent = Math.floor(Math.random() * 3) + 1;
        document.getElementById('verifiedCount').textContent = Math.floor(Math.random() * 3) + 5;
        document.getElementById('consultationCount').textContent = Math.floor(Math.random() * 3) + 3;
    }

    // Initialize calendar
    function initCalendar() {
        let currentDate = new Date();
        
        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            
            // Update month/year display
            const monthNames = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            currentMonthYear.textContent = `${monthNames[month]} ${year}`;
            
            // Get first day of month and total days
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const totalDays = lastDay.getDate();
            const firstDayIndex = firstDay.getDay();
            
            // Clear calendar grid
            calendarGrid.innerHTML = '';
            
            // Add day headers
            const dayNames = ['M', 'S', 'S', 'R', 'K', 'J', 'S'];
            dayNames.forEach(day => {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day fw-bold';
                dayElement.textContent = day;
                calendarGrid.appendChild(dayElement);
            });
            
            // Add empty cells for days before the first day of the month
            for (let i = 0; i < firstDayIndex; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'calendar-day other-month';
                calendarGrid.appendChild(emptyDay);
            }
            
            // Add days of the month
            const today = new Date();
            for (let day = 1; day <= totalDays; day++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';
                dayElement.textContent = day;
                
                // Highlight today
                if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                    dayElement.classList.add('active');
                }
                
                // Highlight weekends (Sunday = 0, Saturday = 6 in getDay())
                const dayOfWeek = new Date(year, month, day).getDay();
                if (dayOfWeek === 0 || dayOfWeek === 6) {
                    dayElement.classList.add('weekend');
                }
                
                // Add appointment indicators (simulated)
                if ([5, 12, 19, 26].includes(day)) {
                    dayElement.innerHTML = `${day} <span class="badge bg-primary badge-dot"></span>`;
                    dayElement.classList.add('has-appointment');
                }
                
                calendarGrid.appendChild(dayElement);
            }
        }
        
        // Navigation handlers
        prevMonthBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });
        
        nextMonthBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });
        
        todayBtn.addEventListener('click', () => {
            currentDate = new Date();
            renderCalendar();
        });
        
        // Initial render
        renderCalendar();
    }

    // Initialize charts
    function initCharts() {
        const ctx = document.getElementById('documentChart').getContext('2d');
        
        // Sample data
        const data = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            datasets: [
                {
                    label: 'Dokumen Selesai',
                    data: [12, 19, 15, 25, 22, 30],
                    borderColor: '#6A85FF',
                    backgroundColor: 'rgba(106, 133, 255, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Dokumen Diproses',
                    data: [8, 12, 10, 18, 15, 22],
                    borderColor: '#7DE2F2',
                    backgroundColor: 'rgba(125, 226, 242, 0.1)',
                    fill: true,
                    tension: 0.4
                }
            ]
        };
        
        // Chart configuration
        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        };
        
        // Create chart
        new Chart(ctx, config);
    }

    // Load recent activities
    function loadRecentActivities() {
        const activities = [
            {
                icon: 'fa-file-contract',
                title: 'Akta Jual Beli Selesai',
                time: '2 jam yang lalu',
                color: 'primary'
            },
            {
                icon: 'fa-calendar-check',
                title: 'Janji dengan Notaris Dijadwalkan',
                time: 'Kemarin, 14:30',
                color: 'success'
            },
            {
                icon: 'fa-upload',
                title: 'Dokumen KTP Diunggah',
                time: '2 hari yang lalu',
                color: 'info'
            },
            {
                icon: 'fa-comments',
                title: 'Konsultasi Hukum Warisan',
                time: '3 hari yang lalu',
                color: 'warning'
            },
            {
                icon: 'fa-check-circle',
                title: 'Legalisasi Sertifikat Selesai',
                time: '5 hari yang lalu',
                color: 'success'
            }
        ];
        
        activityList.innerHTML = '';
        
        activities.forEach(activity => {
            const activityItem = document.createElement('div');
            activityItem.className = 'activity-item';
            activityItem.innerHTML = `
                <div class="activity-icon">
                    <i class="fas ${activity.icon}"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">${activity.title}</div>
                    <div class="activity-time">${activity.time}</div>
                </div>
            `;
            activityList.appendChild(activityItem);
        });
    }

    // Load recent documents
    function loadRecentDocuments() {
        const documents = [
            {
                name: 'Akta Jual Beli Tanah',
                type: 'Akta Notaris',
                date: '15 Mei 2023',
                status: 'completed',
                statusText: 'Selesai'
            },
            {
                name: 'Surat Kuasa Khusus',
                type: 'Surat Kuasa',
                date: '14 Mei 2023',
                status: 'processing',
                statusText: 'Diproses'
            },
            {
                name: 'Legalisasi Ijazah',
                type: 'Legalisasi',
                date: '12 Mei 2023',
                status: 'completed',
                statusText: 'Selesai'
            },
            {
                name: 'Perjanjian Sewa Menyewa',
                type: 'Perjanjian',
                date: '10 Mei 2023',
                status: 'pending',
                statusText: 'Menunggu'
            },
            {
                name: 'Surat Wasiat',
                type: 'Testamen',
                date: '8 Mei 2023',
                status: 'processing',
                statusText: 'Diproses'
            }
        ];
        
        documentsTable.innerHTML = '';
        
        documents.forEach(doc => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <i class="fas fa-file-alt me-2"></i>
                    ${doc.name}
                </td>
                <td>${doc.type}</td>
                <td>${doc.date}</td>
                <td>
                    <span class="status-badge ${doc.status}">${doc.statusText}</span>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary ms-1">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            `;
            documentsTable.appendChild(row);
        });
    }

    // Sidebar toggle for mobile
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('show');
        mainContent.classList.toggle('blur');
    });

    // Logout handler
    logoutBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        
        if (confirm('Apakah Anda yakin ingin logout?')) {
            try {
                await authManager.logout();
                showNotification('Logout berhasil. Mengalihkan ke halaman login...', 'success');
                
                setTimeout(() => {
                    window.location.href = 'auth.html';
                }, 1500);
            } catch (error) {
                showNotification('Logout gagal. Silakan coba lagi.', 'error');
            }
        }
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth < 992) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('show');
                mainContent.classList.remove('blur');
            }
        }
    });

    // Notification function
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                <span>${message}</span>
            </div>
        `;
        
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
            backgroundColor: type === 'success' ? '#4CAF50' : type === 'error' ? '#FF5252' : '#2196F3',
            color: 'white'
        });
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }
});