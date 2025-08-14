-- Create database and schema
CREATE DATABASE IF NOT EXISTS loan_app;
USE loan_app;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('client','admin') NOT NULL DEFAULT 'client'
);

CREATE TABLE loans (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  principal DECIMAL(10,2) NOT NULL,
  interest_rate DECIMAL(5,2) NOT NULL DEFAULT 20.00,
  borrowed_on DATE NOT NULL,
  due_date DATE NOT NULL,
  status ENUM('pending','approved','repaid') NOT NULL DEFAULT 'pending',
  balance DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Sample Users:
INSERT INTO users (username, password, role)
VALUES
('admin', '$2y$10$Uc7g6u5N.eZk7XoXDbNFvOMbY0Zf4k9LCQZ8xe/OBsXhquinuPaey', 'admin'),
('client', '$2y$10$kJ9TlPjw1sMtYp833F2GceQYvhj/eyrnW/cCq0qQjorUqwzkDXF2e', 'client');
-- 'adminpass' for admin, 'clientpass' for client

-- Sample Approved Loan:
INSERT INTO loans (user_id, principal, interest_rate, borrowed_on, due_date, status, balance)
VALUES (2, 100.00, 20.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'approved', 120.00);
