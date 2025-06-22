-- Implement the database schema as discussed in the design.

-- Database Schema for Robert CAT Tool
-- This schema supports multilingual content management with version control

-- Create database (uncomment if needed)
-- CREATE DATABASE IF NOT EXISTS robert_cat_tool CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE robert_cat_tool;

-- Users table for tracking who makes changes
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('translator', 'reviewer', 'admin') DEFAULT 'translator',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Languages table for supported languages
CREATE TABLE IF NOT EXISTS languages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    language_code VARCHAR(5) NOT NULL UNIQUE,
    language_name VARCHAR(50) NOT NULL,
    native_name VARCHAR(50) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_language_code (language_code)
);

-- Translation units table (source content)
CREATE TABLE IF NOT EXISTS translation_units (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source_text TEXT NOT NULL,
    source_language_code VARCHAR(5) NOT NULL,
    context VARCHAR(255) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('pending', 'in_progress', 'completed', 'reviewed') DEFAULT 'pending',
    created_by_user_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (source_language_code) REFERENCES languages(language_code),
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_source_language (source_language_code),
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_created_at (created_at),
    FULLTEXT idx_source_text (source_text)
);

-- Translations table (target content)
CREATE TABLE IF NOT EXISTS translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unit_id INT NOT NULL,
    language_code VARCHAR(5) NOT NULL,
    target_text TEXT NOT NULL,
    status ENUM('draft', 'pending_review', 'approved', 'rejected') DEFAULT 'draft',
    quality_score DECIMAL(3,2) DEFAULT NULL,
    reviewed_by_user_id INT DEFAULT NULL,
    reviewed_at TIMESTAMP NULL,
    created_by_user_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (unit_id) REFERENCES translation_units(id) ON DELETE CASCADE,
    FOREIGN KEY (language_code) REFERENCES languages(language_code),
    FOREIGN KEY (reviewed_by_user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_unit_language (unit_id, language_code),
    INDEX idx_language_code (language_code),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_quality_score (quality_score)
);

-- Translation history table for version control
CREATE TABLE IF NOT EXISTS translation_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    translation_id INT NOT NULL,
    old_target_text TEXT DEFAULT NULL,
    new_target_text TEXT NOT NULL,
    change_type ENUM('created', 'updated', 'deleted') DEFAULT 'updated',
    changed_by_user_id INT DEFAULT NULL,
    change_reason VARCHAR(255) DEFAULT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (translation_id) REFERENCES translations(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by_user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_translation_id (translation_id),
    INDEX idx_changed_at (changed_at),
    INDEX idx_changed_by (changed_by_user_id)
);

-- Projects table for organizing translation work
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    source_language_code VARCHAR(5) NOT NULL,
    target_language_codes JSON NOT NULL,
    status ENUM('draft', 'active', 'completed', 'archived') DEFAULT 'draft',
    deadline DATE DEFAULT NULL,
    created_by_user_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (source_language_code) REFERENCES languages(language_code),
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_deadline (deadline),
    INDEX idx_created_at (created_at)
);

-- Project units table for linking units to projects
CREATE TABLE IF NOT EXISTS project_units (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    unit_id INT NOT NULL,
    assigned_to_user_id INT DEFAULT NULL,
    due_date DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES translation_units(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to_user_id) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_project_unit (project_id, unit_id),
    INDEX idx_assigned_to (assigned_to_user_id),
    INDEX idx_due_date (due_date)
);

-- Comments table for collaboration
CREATE TABLE IF NOT EXISTS translation_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    translation_id INT NOT NULL,
    user_id INT NOT NULL,
    comment_text TEXT NOT NULL,
    is_resolved BOOLEAN DEFAULT FALSE,
    resolved_by_user_id INT DEFAULT NULL,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (translation_id) REFERENCES translations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (resolved_by_user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_translation_id (translation_id),
    INDEX idx_user_id (user_id),
    INDEX idx_is_resolved (is_resolved)
);

-- Insert default languages
INSERT IGNORE INTO languages (language_code, language_name, native_name) VALUES
('en', 'English', 'English'),
('fr', 'French', 'Français'),
('es', 'Spanish', 'Español'),
('de', 'German', 'Deutsch'),
('it', 'Italian', 'Italiano'),
('pt', 'Portuguese', 'Português'),
('ru', 'Russian', 'Русский'),
('zh', 'Chinese', '中文'),
('ja', 'Japanese', '日本語'),
('ko', 'Korean', '한국어');

-- Insert default admin user
INSERT IGNORE INTO users (username, email, full_name, role) VALUES
('admin', 'admin@robert-cat-tool.com', 'System Administrator', 'admin');

-- Create views for easier querying
CREATE OR REPLACE VIEW translation_units_with_stats AS
SELECT 
    tu.id,
    tu.source_text,
    tu.source_language_code,
    tu.context,
    tu.notes,
    tu.priority,
    tu.status,
    tu.created_at,
    tu.updated_at,
    COUNT(t.id) as translation_count,
    COUNT(CASE WHEN t.status = 'approved' THEN 1 END) as approved_count,
    COUNT(CASE WHEN t.status = 'pending_review' THEN 1 END) as pending_review_count
FROM translation_units tu
LEFT JOIN translations t ON tu.id = t.unit_id
GROUP BY tu.id;

-- Create view for translation progress
CREATE OR REPLACE VIEW translation_progress AS
SELECT 
    p.id as project_id,
    p.name as project_name,
    p.source_language_code,
    p.target_language_codes,
    COUNT(pu.unit_id) as total_units,
    COUNT(CASE WHEN t.status = 'approved' THEN 1 END) as completed_translations,
    ROUND((COUNT(CASE WHEN t.status = 'approved' THEN 1 END) / COUNT(pu.unit_id)) * 100, 2) as completion_percentage
FROM projects p
LEFT JOIN project_units pu ON p.id = pu.project_id
LEFT JOIN translations t ON pu.unit_id = t.unit_id
GROUP BY p.id;

-- Create indexes for better performance
CREATE INDEX idx_translations_unit_language ON translations(unit_id, language_code);
CREATE INDEX idx_history_translation_date ON translation_history(translation_id, changed_at);
CREATE INDEX idx_comments_translation_resolved ON translation_comments(translation_id, is_resolved);
