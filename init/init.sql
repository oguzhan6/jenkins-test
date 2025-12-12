-- Create a database if it doesn't exist
drop DATABASE mydatabase;
CREATE DATABASE IF NOT EXISTS mydatabase;
USE mydatabase;

-- Create a table named oguzhan_fidanci
CREATE TABLE fullname (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255)
);

-- Insert some sample data
INSERT INTO fullname (name) VALUES ('anja fidanci');
