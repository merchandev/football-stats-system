<?php
/**
 * Player Model
 * Handles CRUD operations for players
 */

class Player {
    private $conn;
    private $table_name = "players";

    public $id;
    public $first_name;
    public $last_name;
    public $birth_date;
    public $nationality;
    public $position;
    public $jersey_number;
    public $height;
    public $weight;
    public $current_team_id;
    public $photo_url;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT p.*, t.name as team_name 
                  FROM " . $this->table_name . " p
                  LEFT JOIN teams t ON p.current_team_id = t.id
                  ORDER BY p.last_name, p.first_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT p.*, t.name as team_name 
                  FROM " . $this->table_name . " p
                  LEFT JOIN teams t ON p.current_team_id = t.id
                  WHERE p.id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (first_name, last_name, birth_date, nationality, position, jersey_number, 
                   height, weight, current_team_id, photo_url) 
                  VALUES (:first_name, :last_name, :birth_date, :nationality, :position, 
                          :jersey_number, :height, :weight, :current_team_id, :photo_url)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":birth_date", $this->birth_date);
        $stmt->bindParam(":nationality", $this->nationality);
        $stmt->bindParam(":position", $this->position);
        $stmt->bindParam(":jersey_number", $this->jersey_number);
        $stmt->bindParam(":height", $this->height);
        $stmt->bindParam(":weight", $this->weight);
        $stmt->bindParam(":current_team_id", $this->current_team_id);
        $stmt->bindParam(":photo_url", $this->photo_url);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET first_name = :first_name, last_name = :last_name, birth_date = :birth_date,
                      nationality = :nationality, position = :position, jersey_number = :jersey_number,
                      height = :height, weight = :weight, current_team_id = :current_team_id,
                      photo_url = :photo_url
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":birth_date", $this->birth_date);
        $stmt->bindParam(":nationality", $this->nationality);
        $stmt->bindParam(":position", $this->position);
        $stmt->bindParam(":jersey_number", $this->jersey_number);
        $stmt->bindParam(":height", $this->height);
        $stmt->bindParam(":weight", $this->weight);
        $stmt->bindParam(":current_team_id", $this->current_team_id);
        $stmt->bindParam(":photo_url", $this->photo_url);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }

    /**
     * Get player career statistics
     */
    public function getCareerStats($player_id) {
        $query = "SELECT 
                    COUNT(DISTINCT ms.match_id) as total_matches,
                    SUM(ms.minutes_played) as total_minutes,
                    SUM(ms.goals) as total_goals,
                    SUM(ms.assists) as total_assists,
                    SUM(ms.yellow_cards) as total_yellow_cards,
                    SUM(ms.red_cards) as total_red_cards
                  FROM match_stats ms
                  WHERE ms.player_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $player_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get player's matches
     */
    public function getMatches($player_id, $limit = null) {
        $query = "SELECT 
                    m.id, m.match_date, m.match_time,
                    ht.name as home_team, at.name as away_team,
                    m.home_score, m.away_score,
                    c.name as championship_name,
                    ms.minutes_played, ms.goals, ms.assists, ms.yellow_cards, ms.red_cards
                  FROM match_stats ms
                  JOIN matches m ON ms.match_id = m.id
                  JOIN teams ht ON m.home_team_id = ht.id
                  JOIN teams at ON m.away_team_id = at.id
                  JOIN championships c ON m.championship_id = c.id
                  WHERE ms.player_id = ?
                  ORDER BY m.match_date DESC, m.match_time DESC";
        
        if($limit) {
            $query .= " LIMIT ?";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $player_id);
        if($limit) {
            $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get rivals (teams) the player has scored against
     */
    public function getGoalsByRival($player_id) {
        $query = "SELECT 
                    t.id, t.name as team_name,
                    COUNT(mg.id) as goals_scored
                  FROM match_goals mg
                  JOIN matches m ON mg.match_id = m.id
                  JOIN teams t ON (
                    CASE 
                        WHEN mg.team_id = m.home_team_id THEN m.away_team_id
                        ELSE m.home_team_id
                    END = t.id
                  )
                  WHERE mg.player_id = ?
                  GROUP BY t.id, t.name
                  ORDER BY goals_scored DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $player_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get player performance by championship
     */
    public function getStatsByChampionship($player_id) {
        $query = "SELECT 
                    c.id, c.name as championship_name, c.year,
                    COUNT(DISTINCT ms.match_id) as matches,
                    SUM(ms.minutes_played) as minutes,
                    SUM(ms.goals) as goals,
                    SUM(ms.assists) as assists,
                    SUM(ms.yellow_cards) as yellow_cards,
                    SUM(ms.red_cards) as red_cards
                  FROM match_stats ms
                  JOIN matches m ON ms.match_id = m.id
                  JOIN championships c ON m.championship_id = c.id
                  WHERE ms.player_id = ?
                  GROUP BY c.id, c.name, c.year
                  ORDER BY c.year DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $player_id);
        $stmt->execute();
        return $stmt;
    }
}
