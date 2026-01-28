<?php
/**
 * Team Model
 * Handles CRUD operations for teams
 */

class Team {
    private $conn;
    private $table_name = "teams";

    public $id;
    public $name;
    public $short_name;
    public $founded_year;
    public $city;
    public $country;
    public $stadium_home;
    public $logo_url;
    public $description;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (name, short_name, founded_year, city, country, stadium_home, logo_url, description) 
                  VALUES (:name, :short_name, :founded_year, :city, :country, :stadium_home, :logo_url, :description)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":short_name", $this->short_name);
        $stmt->bindParam(":founded_year", $this->founded_year);
        $stmt->bindParam(":city", $this->city);
        $stmt->bindParam(":country", $this->country);
        $stmt->bindParam(":stadium_home", $this->stadium_home);
        $stmt->bindParam(":logo_url", $this->logo_url);
        $stmt->bindParam(":description", $this->description);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, short_name = :short_name, founded_year = :founded_year,
                      city = :city, country = :country, stadium_home = :stadium_home,
                      logo_url = :logo_url, description = :description
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":short_name", $this->short_name);
        $stmt->bindParam(":founded_year", $this->founded_year);
        $stmt->bindParam(":city", $this->city);
        $stmt->bindParam(":country", $this->country);
        $stmt->bindParam(":stadium_home", $this->stadium_home);
        $stmt->bindParam(":logo_url", $this->logo_url);
        $stmt->bindParam(":description", $this->description);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }

    /**
     * Get team statistics for a specific championship
     */
    public function getChampionshipStats($team_id, $championship_id) {
        $query = "SELECT 
                    c.name as championship_name, c.year,
                    tc.finish_position, tc.points, tc.wins, tc.draws, tc.losses,
                    tc.goals_for, tc.goals_against,
                    (tc.goals_for - tc.goals_against) as goal_difference
                  FROM team_championships tc
                  JOIN championships c ON tc.championship_id = c.id
                  WHERE tc.team_id = ? AND tc.championship_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $team_id);
        $stmt->bindParam(2, $championship_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get all championships a team has participated in
     */
    public function getChampionships($team_id) {
        $query = "SELECT DISTINCT
                    c.id, c.name, c.year, c.format,
                    tc.finish_position, tc.points
                  FROM championships c
                  JOIN team_championships tc ON c.id = tc.championship_id
                  WHERE tc.team_id = ?
                  ORDER BY c.year DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $team_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get team titles (championships won)
     */
    public function getTitles($team_id) {
        $query = "SELECT 
                    c.id, c.name, c.year, c.format
                  FROM championships c
                  JOIN team_championships tc ON c.id = tc.championship_id
                  WHERE tc.team_id = ? AND tc.finish_position = 1
                  ORDER BY c.year DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $team_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get head-to-head statistics against another team
     */
    public function getHeadToHead($team1_id, $team2_id) {
        $query = "SELECT 
                    COUNT(*) as total_matches,
                    SUM(CASE 
                        WHEN (m.home_team_id = ? AND m.home_score > m.away_score) OR 
                             (m.away_team_id = ? AND m.away_score > m.home_score) 
                        THEN 1 ELSE 0 END) as team1_wins,
                    SUM(CASE 
                        WHEN m.home_score = m.away_score 
                        THEN 1 ELSE 0 END) as draws,
                    SUM(CASE 
                        WHEN (m.home_team_id = ? AND m.home_score > m.away_score) OR 
                             (m.away_team_id = ? AND m.away_score > m.home_score) 
                        THEN 1 ELSE 0 END) as team2_wins,
                    SUM(CASE 
                        WHEN m.home_team_id = ? THEN m.home_score 
                        ELSE m.away_score END) as team1_goals,
                    SUM(CASE 
                        WHEN m.home_team_id = ? THEN m.home_score 
                        ELSE m.away_score END) as team2_goals
                  FROM matches m
                  WHERE (m.home_team_id = ? OR m.away_team_id = ?)
                    AND (m.home_team_id = ? OR m.away_team_id = ?)
                    AND m.match_status = 'completed'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $team1_id);
        $stmt->bindParam(2, $team1_id);
        $stmt->bindParam(3, $team2_id);
        $stmt->bindParam(4, $team2_id);
        $stmt->bindParam(5, $team1_id);
        $stmt->bindParam(6, $team2_id);
        $stmt->bindParam(7, $team1_id);
        $stmt->bindParam(8, $team1_id);
        $stmt->bindParam(9, $team2_id);
        $stmt->bindParam(10, $team2_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get top scorers for the team
     */
    public function getTopScorers($team_id, $limit = 10) {
        $query = "SELECT 
                    p.id, p.first_name, p.last_name,
                    SUM(ms.goals) as total_goals,
                    COUNT(DISTINCT ms.match_id) as matches_played
                  FROM players p
                  JOIN match_stats ms ON p.id = ms.player_id
                  WHERE ms.team_id = ?
                  GROUP BY p.id, p.first_name, p.last_name
                  HAVING total_goals > 0
                  ORDER BY total_goals DESC
                  LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $team_id);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get players with most matches for the team
     */
    public function getPlayersWithMostMatches($team_id, $limit = 10) {
        $query = "SELECT 
                    p.id, p.first_name, p.last_name,
                    COUNT(DISTINCT ms.match_id) as matches_played,
                    SUM(ms.minutes_played) as total_minutes,
                    SUM(ms.goals) as total_goals
                  FROM players p
                  JOIN match_stats ms ON p.id = ms.player_id
                  WHERE ms.team_id = ?
                  GROUP BY p.id, p.first_name, p.last_name
                  ORDER BY matches_played DESC, total_minutes DESC
                  LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $team_id);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}
