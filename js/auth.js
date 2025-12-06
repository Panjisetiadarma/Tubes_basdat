// js/auth.js
const authManager = {
    // Simulasi data pengguna untuk testing
    users: [
        { email: 'demo@notaris.com', password: 'demo123', name: 'Demo User' }
    ],
    
    // Fungsi login
    async login(email, password) {
        return new Promise((resolve, reject) => {
            console.log('Attempting login with:', email);
            
            // Simulasi delay server
            setTimeout(() => {
                // Cari user berdasarkan email
                const user = this.users.find(u => u.email === email);
                
                if (!user) {
                    reject('Email tidak ditemukan');
                    return;
                }
                
                if (user.password !== password) {
                    reject('Password salah');
                    return;
                }
                
                // Simpan session (simplified)
                localStorage.setItem('isLoggedIn', 'true');
                localStorage.setItem('userEmail', email);
                localStorage.setItem('userName', user.name);
                
                resolve({
                    success: true,
                    message: 'Login berhasil',
                    user: user
                });
            }, 1000);
        });
    },
    
    // Fungsi register
    async register(userData) {
        return new Promise((resolve, reject) => {
            console.log('Registering user:', userData);
            
            setTimeout(() => {
                // Cek apakah email sudah terdaftar
                const existingUser = this.users.find(u => u.email === userData.email);
                
                if (existingUser) {
                    reject('Email sudah terdaftar');
                    return;
                }
                
                // Tambahkan user baru
                const newUser = {
                    email: userData.email,
                    password: userData.password,
                    name: `${userData.firstName} ${userData.lastName}`,
                    phone: userData.phone
                };
                
                this.users.push(newUser);
                
                // Simpan session
                localStorage.setItem('isLoggedIn', 'true');
                localStorage.setItem('userEmail', userData.email);
                localStorage.setItem('userName', `${userData.firstName} ${userData.lastName}`);
                
                resolve({
                    success: true,
                    message: 'Pendaftaran berhasil',
                    user: newUser
                });
            }, 1500);
        });
    },
    
    // Fungsi reset password
    async requestPasswordReset(email) {
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                const user = this.users.find(u => u.email === email);
                
                if (!user) {
                    reject('Email tidak ditemukan');
                    return;
                }
                
                // Simulasi: link reset dikirim
                resolve(`Link reset password telah dikirim ke ${email}. Silakan cek email Anda.`);
            }, 1000);
        });
    },
    
    // Cek apakah user sudah login
    checkLoginStatus() {
        return localStorage.getItem('isLoggedIn') === 'true';
    },
    
    // Logout
    logout() {
        localStorage.removeItem('isLoggedIn');
        localStorage.removeItem('userEmail');
        localStorage.removeItem('userName');
        window.location.href = 'auth.html?logout=true';
    }
};