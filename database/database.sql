-- Create database
CREATE DATABASE IF NOT EXISTS department_portal;
USE department_portal;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    department VARCHAR(50) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    INDEX idx_email (email),
    INDEX idx_department (department)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Login history table
CREATE TABLE IF NOT EXISTS login_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    logout_time TIMESTAMP NULL,
    ip_address VARCHAR(45),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_login_time (login_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Department table
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    manager_id INT,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample departments
INSERT INTO departments (name, description) VALUES
('Information Technology', 'Handles all IT related tasks and infrastructure'),
('Human Resources', 'Manages employee relations and recruitment'),
('Finance', 'Handles financial operations and budgeting'),
('Sales', 'Manages sales operations and client relations'),
('Marketing', 'Handles marketing campaigns and brand management');

-- Activity log table
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    description TEXT,
    activity_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_activity_date (activity_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data for users table (password: password123)
INSERT INTO users (fullname, email, department, phone, password) VALUES
('John Doe', 'john.doe@example.com', 'IT', '1234567890', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36DRcT36'),
('Jane Smith', 'jane.smith@example.com', 'HR', '0987654321', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36DRcT36');

-- Create views for frequently used queries
CREATE OR REPLACE VIEW user_department_view AS
SELECT u.id, u.fullname, u.email, u.department, u.phone, u.registration_date, d.description as department_description
FROM users u
LEFT JOIN departments d ON u.department = d.name
WHERE u.is_active = 1;

-- Create stored procedure for user login
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS LogUserLogin(IN p_user_id INT, IN p_ip_address VARCHAR(45))
BEGIN
    INSERT INTO login_history (user_id, ip_address) VALUES (p_user_id, p_ip_address);
END //
DELIMITER ;

-- Create stored procedure for user registration activity
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS LogUserActivity(IN p_user_id INT, IN p_action VARCHAR(255), IN p_description TEXT)
BEGIN
    INSERT INTO activity_log (user_id, action, description) VALUES (p_user_id, p_action, p_description);
END //
DELIMITER ;

-- Create triggers for audit trail

-- Trigger for user updates
DELIMITER //
CREATE TRIGGER IF NOT EXISTS user_update_trigger
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
    IF NEW.fullname != OLD.fullname OR NEW.phone != OLD.phone OR NEW.department != OLD.department THEN
        INSERT INTO activity_log (user_id, action, description) 
        VALUES (NEW.id, 'UPDATE_PROFILE', CONCAT('Updated profile. Old email: ', OLD.email, ', New email: ', NEW.email));
    END IF;
END //
DELIMITER ;

-- Verify database structure
SHOW TABLES;
