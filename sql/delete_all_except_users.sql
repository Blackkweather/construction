-- SQL script to delete all data from all tables except the users table (utilisateurs)

-- Disable foreign key checks to allow truncation
SET FOREIGN_KEY_CHECKS = 0;

-- Delete all reservations
DELETE FROM reservations;

-- Delete all vehicles
DELETE FROM vehicules;

-- Add other tables to delete data from as needed
-- DELETE FROM bookings;
-- DELETE FROM other_table;

-- Enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;
