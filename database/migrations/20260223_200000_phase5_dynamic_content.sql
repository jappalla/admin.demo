-- Phase 5 dynamic content extensions
-- Adds instant contact messages and default settings for profile/contact sections.

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    subject VARCHAR(190) NULL,
    message TEXT NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_contact_messages_status_created (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
    ('profile_text', 'Professionista full stack con esperienza nella realizzazione di applicazioni web affidabili, ottimizzate per SEO, performance e manutenzione nel tempo.'),
    ('contact_email', 'info@antonio-trapasso.it'),
    ('contact_linkedin_label', 'Profilo professionale'),
    ('contact_linkedin_url', 'https://www.linkedin.com/'),
    ('contact_phone', ''),
    ('contact_intro', 'Scrivimi direttamente dal form: riceverai una risposta nel minor tempo possibile.');
