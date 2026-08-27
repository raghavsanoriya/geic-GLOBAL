CREATE TABLE IF NOT EXISTS profile_enquiries (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(120) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(190) NOT NULL,
    qualification VARCHAR(100) NOT NULL,
    passing_year SMALLINT UNSIGNED NOT NULL,
    academic_score VARCHAR(30) NOT NULL,
    preferred_country VARCHAR(100) NOT NULL,
    submitted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    source_ip VARCHAR(45) NOT NULL DEFAULT '',
    user_agent VARCHAR(500) NOT NULL DEFAULT '',
    PRIMARY KEY (id),
    KEY idx_profile_enquiries_submitted_at (submitted_at),
    KEY idx_profile_enquiries_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
