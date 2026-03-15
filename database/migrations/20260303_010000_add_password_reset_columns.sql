-- Add password reset columns to users table
ALTER TABLE users
    ADD COLUMN password_reset_token VARCHAR(255) NULL DEFAULT NULL AFTER is_active,
    ADD COLUMN password_reset_expires DATETIME NULL DEFAULT NULL AFTER password_reset_token;
