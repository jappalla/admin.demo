-- Phase 6+ enhancement: external link per competenza
-- Allows admin to manage a URL for each skill and expose it on home.

ALTER TABLE skills
    ADD COLUMN link_url VARCHAR(255) NULL AFTER level;
