CREATE DATABASE IF NOT EXISTS mydatabase;
USE mydatabase;

-- Drop the old table if it exists
DROP TABLE IF EXISTS fullname;

-- Create a table for the guestbook comments
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert some sample data to start
INSERT INTO comments (username, message) VALUES ('Admin', 'Welcome to the new interactive guestbook!');
INSERT INTO comments (username, message) VALUES ('user', 'This looks like a great start!');