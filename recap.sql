-- =============================================
-- 1. ROLES TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    daily_limit INT DEFAULT 0,
    max_video_seconds INT DEFAULT 0,
    can_watermark TINYINT(1) DEFAULT 0,
    can_subtitle TINYINT(1) DEFAULT 0,
    can_voiceover TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO roles
    (name, daily_limit, max_video_seconds, can_watermark, can_subtitle, can_voiceover)
VALUES
    ('tester', 1,   40,  0, 0, 1),
    ('normal', 3,   120, 1, 1, 1),
    ('pro',    3,   180, 1, 1, 1),
    ('vip',    5,   300, 1, 1, 1),
    ('admin',  999, 0,   1, 1, 1);

-- =============================================
-- 2. USERS TABLE ALTER
-- =============================================
ALTER TABLE users
ADD COLUMN IF NOT EXISTS username VARCHAR(100) UNIQUE NULL,
ADD COLUMN IF NOT EXISTS role_name VARCHAR(50) DEFAULT 'tester',
ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1,
ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS api_key VARCHAR(100) UNIQUE DEFAULT NULL,
ADD COLUMN IF NOT EXISTS google_id VARCHAR(100) UNIQUE DEFAULT NULL,
ADD COLUMN IF NOT EXISTS plan_expires_at DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS recap_limit INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS total_recap_used INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS session_expires_at DATETIME DEFAULT NULL;

-- =============================================
-- 3. USAGE LOG TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS usage_log (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    used_date DATE NOT NULL,
    run_count INT DEFAULT 0,
    UNIQUE KEY uniq_user_date (user_id, used_date),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- 4. PLAN HISTORY TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS plan_history (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    role_name VARCHAR(50) NOT NULL,
    recap_limit INT DEFAULT 0,
    plan_starts_at DATETIME NOT NULL,
    plan_expires_at DATETIME NOT NULL,
    renewed_by VARCHAR(100) DEFAULT 'admin',
    note TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS recap_jobs (
    id VARCHAR(36) PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    step INT DEFAULT 0,
    progress LONGTEXT DEFAULT NULL,
    error TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);