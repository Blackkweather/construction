-- SQL script to create an admin user with known credentials
INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (
    'Admin User',
    'admin@local.test',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- bcrypt hash for 'password'
    'admin'
);
