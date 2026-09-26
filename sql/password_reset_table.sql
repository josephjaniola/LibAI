-- Add this table to support password reset tokens
CREATE TABLE IF NOT EXISTS password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_type ENUM('admin','librarian','student','faculty') NOT NULL,
  user_ref_id INT NOT NULL,
  token VARCHAR(255) NOT NULL,
  code_hash VARCHAR(255) DEFAULT NULL,
  expires_at DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (token),
  INDEX (expires_at)
);
