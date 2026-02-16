-- Reset Users Table Data
-- This script will remove all users and restore the default Admin and User accounts.
USE `toko kelontong`;

-- Delete all users
DELETE FROM users;

-- Reset Auto Increment (Optional)
ALTER TABLE users AUTO_INCREMENT = 1;

-- Insert Admin (Password: admin)
INSERT INTO users (username, password, full_name, role) VALUES
('admin', '$2y$10$y3YTV62aamY8McsUlUSgYO0C.f9V0hniwa3lqLxZIp0fhkeSFFLdu', 'Administrator', 'admin');


