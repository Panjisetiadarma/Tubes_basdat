/**
 * Authentication Manager
 * Mengelola autentikasi user dengan backend PHP
 */

const authManager = {
    API_BASE_URL: 'api/',
    
    /**
     * Login user
     */
    async login(email, password) {
        try {
            const response = await fetch(this.API_BASE_URL + 'login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: email,
                    password: password
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Simpan user info ke localStorage untuk akses cepat
                localStorage.setItem('current_user', JSON.stringify(data.user));
                return {
                    success: true,
                    user: data.user,
                    message: data.message
                };
            } else {
                return {
                    success: false,
                    message: data.message
                };
            }
        } catch (error) {
            console.error('Login error:', error);
            return {
                success: false,
                message: 'Terjadi kesalahan. Silakan coba lagi.'
            };
        }
    },
    
    /**
     * Register user baru
     */
    async register(userData) {
        try {
            const response = await fetch(this.API_BASE_URL + 'register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    nama_lengkap: userData.nama_lengkap,
                    username: userData.username || userData.email.split('@')[0],
                    email: userData.email,
                    password: userData.password,
                    confirm_password: userData.confirm_password,
                    phone: userData.phone
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Simpan user info ke localStorage
                localStorage.setItem('current_user', JSON.stringify(data.user));
                return {
                    success: true,
                    user: data.user,
                    message: data.message
                };
            } else {
                return {
                    success: false,
                    message: data.message
                };
            }
        } catch (error) {
            console.error('Register error:', error);
            return {
                success: false,
                message: 'Terjadi kesalahan. Silakan coba lagi.'
            };
        }
    },
    
    /**
     * Logout user
     */
    async logout() {
        try {
            const response = await fetch(this.API_BASE_URL + 'logout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            // Hapus dari localStorage
            localStorage.removeItem('current_user');
            
            return {
                success: true,
                message: 'Logout berhasil'
            };
        } catch (error) {
            console.error('Logout error:', error);
            // Tetap hapus dari localStorage meskipun error
            localStorage.removeItem('current_user');
            return {
                success: true,
                message: 'Logout berhasil'
            };
        }
    },
    
    /**
     * Cek apakah user sudah login
     */
    async checkAuth() {
        try {
            const response = await fetch(this.API_BASE_URL + 'check_auth.php', {
                method: 'GET',
                credentials: 'include'
            });
            
            const data = await response.json();
            
            if (data.success && data.authenticated) {
                // Update localStorage dengan data terbaru
                if (data.user) {
                    localStorage.setItem('current_user', JSON.stringify(data.user));
                }
                return true;
            } else {
                localStorage.removeItem('current_user');
                return false;
            }
        } catch (error) {
            console.error('Check auth error:', error);
            // Fallback ke localStorage
            return this.getCurrentUser() !== null;
        }
    },
    
    /**
     * Verify token (alias untuk checkAuth)
     */
    async verifyToken() {
        return await this.checkAuth();
    },
    
    /**
     * Get current user dari localStorage
     */
    getCurrentUser() {
        const userStr = localStorage.getItem('current_user');
        if (userStr) {
            try {
                return JSON.parse(userStr);
            } catch (e) {
                return null;
            }
        }
        return null;
    },
    
    /**
     * Check if user is authenticated (synchronous, dari localStorage)
     */
    isAuthenticated() {
        return this.getCurrentUser() !== null;
    }
};

// Export untuk digunakan di file lain
if (typeof module !== 'undefined' && module.exports) {
    module.exports = authManager;
}

