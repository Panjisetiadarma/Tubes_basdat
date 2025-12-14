USE project_notaris;

-- Insert Admin Notaris (password: admin123)
-- Insert user Notaris (password: user123)

INSERT INTO User (username, password, nama_lengkap, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator Notaris', 'AdminNotaris');

-- Insert User biasa (password: user123)
INSERT INTO User (username, password, nama_lengkap, role) VALUES
('user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'User Test', 'user');
