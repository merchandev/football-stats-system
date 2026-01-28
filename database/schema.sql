-- Football Statistics Database Schema
-- Database for Women's Football Statistics Management System

CREATE DATABASE IF NOT EXISTS football_stats CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE football_stats;

-- Drop tables if exists (for clean reinstall)
DROP TABLE IF EXISTS match_stats;
DROP TABLE IF EXISTS match_cards;
DROP TABLE IF EXISTS match_goals;
DROP TABLE IF EXISTS match_substitutions;
DROP TABLE IF EXISTS match_lineups;
DROP TABLE IF EXISTS matches;
DROP TABLE IF EXISTS team_championships;
DROP TABLE IF EXISTS important_moments;
DROP TABLE IF EXISTS players;
DROP TABLE IF EXISTS coaches;
DROP TABLE IF EXISTS referees;
DROP TABLE IF EXISTS stadiums;
DROP TABLE IF EXISTS teams;
DROP TABLE IF EXISTS championships;

-- Championships table
CREATE TABLE championships (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    format VARCHAR(100) NOT NULL COMMENT 'League, Cup, Tournament, etc.',
    year INT NOT NULL,
    start_date DATE,
    end_date DATE,
    description TEXT,
    country VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_year (year),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Teams table
CREATE TABLE teams (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    short_name VARCHAR(50),
    founded_year INT,
    city VARCHAR(100),
    country VARCHAR(100),
    stadium_home VARCHAR(255),
    logo_url VARCHAR(500),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_team_name (name),
    INDEX idx_name (name),
    INDEX idx_country (country)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stadiums table
CREATE TABLE stadiums (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    city VARCHAR(100),
    country VARCHAR(100),
    capacity INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_stadium (name, city),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Players table
CREATE TABLE players (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    birth_date DATE,
    nationality VARCHAR(100),
    position VARCHAR(50) COMMENT 'GK, DF, MF, FW',
    jersey_number INT,
    height INT COMMENT 'in cm',
    weight INT COMMENT 'in kg',
    current_team_id INT,
    photo_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (current_team_id) REFERENCES teams(id) ON DELETE SET NULL,
    INDEX idx_name (last_name, first_name),
    INDEX idx_team (current_team_id),
    INDEX idx_position (position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Coaches table
CREATE TABLE coaches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    birth_date DATE,
    nationality VARCHAR(100),
    photo_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (last_name, first_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Referees table
CREATE TABLE referees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    nationality VARCHAR(100),
    birth_date DATE,
    photo_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (last_name, first_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Matches table
CREATE TABLE matches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    championship_id INT NOT NULL,
    home_team_id INT NOT NULL,
    away_team_id INT NOT NULL,
    match_date DATE NOT NULL,
    match_time TIME,
    stadium_id INT,
    referee_id INT,
    home_coach_id INT,
    away_coach_id INT,
    home_score INT DEFAULT 0,
    away_score INT DEFAULT 0,
    match_status VARCHAR(50) DEFAULT 'scheduled' COMMENT 'scheduled, completed, cancelled',
    round VARCHAR(50) COMMENT 'Round/Week number or playoff stage',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (championship_id) REFERENCES championships(id) ON DELETE CASCADE,
    FOREIGN KEY (home_team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (away_team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (stadium_id) REFERENCES stadiums(id) ON DELETE SET NULL,
    FOREIGN KEY (referee_id) REFERENCES referees(id) ON DELETE SET NULL,
    FOREIGN KEY (home_coach_id) REFERENCES coaches(id) ON DELETE SET NULL,
    FOREIGN KEY (away_coach_id) REFERENCES coaches(id) ON DELETE SET NULL,
    INDEX idx_championship (championship_id),
    INDEX idx_teams (home_team_id, away_team_id),
    INDEX idx_date (match_date),
    INDEX idx_referee (referee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Match lineups (starting players)
CREATE TABLE match_lineups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    match_id INT NOT NULL,
    player_id INT NOT NULL,
    team_id INT NOT NULL,
    position VARCHAR(50),
    jersey_number INT,
    is_starter BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    UNIQUE KEY unique_player_match (match_id, player_id),
    INDEX idx_match (match_id),
    INDEX idx_player (player_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Match substitutions
CREATE TABLE match_substitutions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    match_id INT NOT NULL,
    player_in_id INT NOT NULL,
    player_out_id INT NOT NULL,
    team_id INT NOT NULL,
    minute INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (player_in_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (player_out_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    INDEX idx_match (match_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Match goals
CREATE TABLE match_goals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    match_id INT NOT NULL,
    player_id INT NOT NULL,
    team_id INT NOT NULL,
    assist_player_id INT NULL COMMENT 'Player who assisted the goal',
    minute INT NOT NULL,
    goal_type VARCHAR(50) DEFAULT 'regular' COMMENT 'regular, penalty, free-kick, header, own-goal',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (assist_player_id) REFERENCES players(id) ON DELETE SET NULL,
    INDEX idx_match (match_id),
    INDEX idx_player (player_id),
    INDEX idx_assist (assist_player_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Match cards
CREATE TABLE match_cards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    match_id INT NOT NULL,
    player_id INT NOT NULL,
    team_id INT NOT NULL,
    card_type ENUM('yellow', 'red') NOT NULL,
    minute INT NOT NULL,
    reason VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    INDEX idx_match (match_id),
    INDEX idx_player (player_id),
    INDEX idx_card_type (card_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Match statistics per player
CREATE TABLE match_stats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    match_id INT NOT NULL,
    player_id INT NOT NULL,
    team_id INT NOT NULL,
    minutes_played INT DEFAULT 0,
    goals INT DEFAULT 0,
    assists INT DEFAULT 0,
    yellow_cards INT DEFAULT 0,
    red_cards INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    UNIQUE KEY unique_player_match_stats (match_id, player_id),
    INDEX idx_match (match_id),
    INDEX idx_player (player_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Team championships (titles won)
CREATE TABLE team_championships (
    id INT PRIMARY KEY AUTO_INCREMENT,
    team_id INT NOT NULL,
    championship_id INT NOT NULL,
    finish_position INT NOT NULL COMMENT '1 for winner, 2 for runner-up, etc.',
    points INT,
    wins INT,
    draws INT,
    losses INT,
    goals_for INT,
    goals_against INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (championship_id) REFERENCES championships(id) ON DELETE CASCADE,
    UNIQUE KEY unique_team_championship (team_id, championship_id),
    INDEX idx_team (team_id),
    INDEX idx_championship (championship_id),
    INDEX idx_position (finish_position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Important moments / reminders
CREATE TABLE important_moments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    event_type VARCHAR(50) COMMENT 'title, milestone, record, etc.',
    related_team_id INT,
    related_player_id INT,
    related_championship_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (related_team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (related_player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (related_championship_id) REFERENCES championships(id) ON DELETE CASCADE,
    INDEX idx_date (event_date),
    INDEX idx_type (event_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for testing

-- Sample Championships
INSERT INTO championships (name, format, year, start_date, end_date, country) VALUES
('Copa Mundial Femenina 2023', 'Tournament', 2023, '2023-07-20', '2023-08-20', 'Australia/New Zealand'),
('Liga Femenina Nacional 2024', 'League', 2024, '2024-03-01', '2024-11-30', 'Argentina'),
('Copa Libertadores Femenina 2024', 'Cup', 2024, '2024-10-01', '2024-10-20', 'South America');

-- Sample Teams
INSERT INTO teams (name, short_name, city, country) VALUES
('Boca Juniors Femenino', 'Boca', 'Buenos Aires', 'Argentina'),
('River Plate Femenino', 'River', 'Buenos Aires', 'Argentina'),
('San Lorenzo Femenino', 'San Lorenzo', 'Buenos Aires', 'Argentina'),
('Racing Club Femenino', 'Racing', 'Avellaneda', 'Argentina');

-- Sample Stadium
INSERT INTO stadiums (name, city, country, capacity) VALUES
('La Bombonera', 'Buenos Aires', 'Argentina', 49000),
('Monumental', 'Buenos Aires', 'Argentina', 70000),
('Nuevo Gasómetro', 'Buenos Aires', 'Argentina', 47000);

-- Sample Players
INSERT INTO players (first_name, last_name, birth_date, nationality, position, jersey_number, current_team_id) VALUES
('Yamila', 'Rodríguez', '1997-04-05', 'Argentina', 'FW', 9, 1),
('Estefanía', 'Banini', '1990-06-21', 'Argentina', 'MF', 10, 1),
('Vanina', 'Correa', '1991-03-30', 'Argentina', 'GK', 1, 1),
('María', 'Fernández', '1995-08-15', 'Argentina', 'DF', 4, 2),
('Lorena', 'Benítez', '1998-02-20', 'Argentina', 'FW', 11, 2);

-- Sample Coaches
INSERT INTO coaches (first_name, last_name, nationality) VALUES
('Carlos', 'Borrello', 'Argentina'),
('Pablo', 'Santella', 'Argentina');

-- Sample Referee
INSERT INTO referees (first_name, last_name, nationality) VALUES
('Laura', 'Fortunato', 'Argentina'),
('Mariana', 'De Almeida', 'Argentina');

-- Sample Match
INSERT INTO matches (championship_id, home_team_id, away_team_id, match_date, match_time, stadium_id, referee_id, home_coach_id, away_coach_id, home_score, away_score, match_status, round) VALUES
(2, 1, 2, '2024-04-15', '20:00:00', 1, 1, 1, 2, 2, 1, 'completed', 'Round 5');

-- Sample match lineup
INSERT INTO match_lineups (match_id, player_id, team_id, position, jersey_number, is_starter) VALUES
(1, 1, 1, 'FW', 9, TRUE),
(1, 2, 1, 'MF', 10, TRUE),
(1, 3, 1, 'GK', 1, TRUE),
(1, 4, 2, 'DF', 4, TRUE),
(1, 5, 2, 'FW', 11, TRUE);

-- Sample goals
INSERT INTO match_goals (match_id, player_id, team_id, assist_player_id, minute, goal_type) VALUES
(1, 1, 1, 2, 23, 'regular'),
(1, 2, 1, NULL, 67, 'free-kick'),
(1, 5, 2, 4, 45, 'regular');

-- Sample cards
INSERT INTO match_cards (match_id, player_id, team_id, card_type, minute) VALUES
(1, 4, 2, 'yellow', 34);

-- Sample match stats
INSERT INTO match_stats (match_id, player_id, team_id, minutes_played, goals, assists) VALUES
(1, 1, 1, 90, 1, 0),
(1, 2, 1, 90, 1, 1),
(1, 3, 1, 90, 0, 0),
(1, 4, 2, 90, 0, 1),
(1, 5, 2, 90, 1, 0);
