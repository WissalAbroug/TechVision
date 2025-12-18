-- Add profile_photo column to compte table
ALTER TABLE compte ADD COLUMN profile_photo VARCHAR(255) DEFAULT NULL;

-- Optional: Add index for better performance if needed
-- CREATE INDEX idx_profile_photo ON compte(profile_photo);
