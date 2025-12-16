-- Migration: Add updated_at and avatar_url columns
-- Run this if you have existing data

-- Add avatar_url to users
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS avatar_url VARCHAR(255) DEFAULT NULL AFTER password_hash;

-- Add updated_at to users
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Add updated_at to topics
ALTER TABLE topics 
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Add updated_at to comments
ALTER TABLE comments 
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Add performance indexes
ALTER TABLE topics 
ADD INDEX IF NOT EXISTS idx_topics_created (created_at);

ALTER TABLE comments 
ADD INDEX IF NOT EXISTS idx_comments_created (topic_id, created_at);

-- Add fulltext search index for topics (requires MySQL 5.6+ with InnoDB)
ALTER TABLE topics 
ADD FULLTEXT INDEX IF NOT EXISTS idx_topics_search (title, body);
