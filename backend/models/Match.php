<?php
/**
 * Match Model
 * Handles CRUD operations for matches
 */

class Match {
    private $conn;
    private $table_name = "matches";

    public $id;
    public $championship_id;
    public $home_team_id;
    public $away_team_id;
    public $match_date;
    public $match_time;
    public $stadium_id;
    public $referee_id;
    public $home_coach_id;
    public $away_coach_id;
    public $home_score;
    public $away_score;
    public $match_status;
    public $round;
    public $notes;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($championship_id = null) {
        $query = "SELECT m.*, 
                    ht.name as home_team_name, at.name as away_team_name,
                    c.name as championship_name,
                    s.name as stadium_name,
                    CONCAT(r.first_name, ' ', r.last_name) as referee_name
                  FROM " . $this->table_name . " m
                  JOIN teams ht ON m.home_team_id = ht.id
                  JOIN teams at ON m.away_team_id = at.id
                  JOIN championships c ON m.championship_id = c.id
                  LEFT JOIN stadiums s ON m.stadium_id = s.id
                  LEFT JOIN referees r ON m.referee_id = r.id";
        
        if($championship_id) {
            $query .= " WHERE m.championship_id = ?";
        }
        
        $query .= " ORDER BY m.match_date DESC, m.match_time DESC";
        
        $stmt = $this->conn->prepare($query);
        if($championship_id) {
            $stmt->bindParam(1, $championship_id);
        }
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT m.*, 
                    ht.name as home_team_name, ht.short_name as home_team_short,
                    at.name as away_team_name, at.short_name as away_team_short,
                    c.name as championship_name, c.year as championship_year,
                    s.name as stadium_name, s.city as stadium_city,
                    CONCAT(r.first_name, ' ', r.last_name) as referee_name,
                    CONCAT(hc.first_name, ' ', hc.last_name) as home_coach_name,
                    CONCAT(ac.first_name, ' ', ac.last_name) as away_coach_name
                  FROM " . $this->table_name . " m
                  JOIN teams ht ON m.home_team_id = ht.id
                  JOIN teams at ON m.away_team_id = at.id
                  JOIN championships c ON m.championship_id = c.id
                  LEFT JOIN stadiums s ON m.stadium_id = s.id
                  LEFT JOIN referees r ON m.referee_id = r.id
                  LEFT JOIN coaches hc ON m.home_coach_id = hc.id
                  LEFT JOIN coaches ac ON m.away_coach_id = ac.id
                  WHERE m.id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (championship_id, home_team_id, away_team_id, match_date, match_time,
                   stadium_id, referee_id, home_coach_id, away_coach_id, home_score, away_score,
                   match_status, round, notes) 
                  VALUES (:championship_id, :home_team_id, :away_team_id, :match_date, :match_time,
                          :stadium_id, :referee_id, :home_coach_id, :away_coach_id, :home_score, :away_score,
                          :match_status, :round, :notes)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":championship_id", $this->championship_id);
        $stmt->bindParam(":home_team_id", $this->home_team_id);
        $stmt->bindParam(":away_team_id", $this->away_team_id);
        $stmt->bindParam(":match_date", $this->match_date);
        $stmt->bindParam(":match_time", $this->match_time);
        $stmt->bindParam(":stadium_id", $this->stadium_id);
        $stmt->bindParam(":referee_id", $this->referee_id);
        $stmt->bindParam(":home_coach_id", $this->home_coach_id);
        $stmt->bindParam(":away_coach_id", $this->away_coach_id);
        $stmt->bindParam(":home_score", $this->home_score);
        $stmt->bindParam(":away_score", $this->away_score);
        $stmt->bindParam(":match_status", $this->match_status);
        $stmt->bindParam(":round", $this->round);
        $stmt->bindParam(":notes", $this->notes);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET championship_id = :championship_id, home_team_id = :home_team_id,
                      away_team_id = :away_team_id, match_date = :match_date, match_time = :match_time,
                      stadium_id = :stadium_id, referee_id = :referee_id,
                      home_coach_id = :home_coach_id, away_coach_id = :away_coach_id,
                      home_score = :home_score, away_score = :away_score,
                      match_status = :match_status, round = :round, notes = :notes
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":championship_id", $this->championship_id);
        $stmt->bindParam(":home_team_id", $this->home_team_id);
        $stmt->bindParam(":away_team_id", $this->away_team_id);
        $stmt->bindParam(":match_date", $this->match_date);
        $stmt->bindParam(":match_time", $this->match_time);
        $stmt->bindParam(":stadium_id", $this->stadium_id);
        $stmt->bindParam(":referee_id", $this->referee_id);
        $stmt->bindParam(":home_coach_id", $this->home_coach_id);
        $stmt->bindParam(":away_coach_id", $this->away_coach_id);
        $stmt->bindParam(":home_score", $this->home_score);
        $stmt->bindParam(":away_score", $this->away_score);
        $stmt->bindParam(":match_status", $this->match_status);
        $stmt->bindParam(":round", $this->round);
        $stmt->bindParam(":notes", $this->notes);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }

    /**
     * Add goal to match
     */
    public function addGoal($player_id, $team_id, $minute, $goal_type = 'regular', $assist_player_id = null, $description = null) {
        $query = "INSERT INTO match_goals (match_id, player_id, team_id, minute, goal_type, assist_player_id, description)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $player_id);
        $stmt->bindParam(3, $team_id);
        $stmt->bindParam(4, $minute);
        $stmt->bindParam(5, $goal_type);
        $stmt->bindParam(6, $assist_player_id);
        $stmt->bindParam(7, $description);
        
        return $stmt->execute();
    }

    /**
     * Add card to match
     */
    public function addCard($player_id, $team_id, $card_type, $minute, $reason = null) {
        $query = "INSERT INTO match_cards (match_id, player_id, team_id, card_type, minute, reason)
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $player_id);
        $stmt->bindParam(3, $team_id);
        $stmt->bindParam(4, $card_type);
        $stmt->bindParam(5, $minute);
        $stmt->bindParam(6, $reason);
        
        return $stmt->execute();
    }

    /**
     * Add player to lineup
     */
    public function addLineup($player_id, $team_id, $position, $jersey_number, $is_starter = true) {
        $query = "INSERT INTO match_lineups (match_id, player_id, team_id, position, jersey_number, is_starter)
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $player_id);
        $stmt->bindParam(3, $team_id);
        $stmt->bindParam(4, $position);
        $stmt->bindParam(5, $jersey_number);
        $stmt->bindParam(6, $is_starter);
        
        return $stmt->execute();
    }

    /**
     * Get match lineup
     */
    public function getLineup($match_id) {
        $query = "SELECT ml.*, 
                    CONCAT(p.first_name, ' ', p.last_name) as player_name,
                    p.position as player_position,
                    t.name as team_name
                  FROM match_lineups ml
                  JOIN players p ON ml.player_id = p.id
                  JOIN teams t ON ml.team_id = t.id
                  WHERE ml.match_id = ?
                  ORDER BY t.id, ml.is_starter DESC, ml.position";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $match_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get match goals
     */
    public function getGoals($match_id) {
        $query = "SELECT mg.*,
                    CONCAT(p.first_name, ' ', p.last_name) as scorer_name,
                    CONCAT(a.first_name, ' ', a.last_name) as assist_name,
                    t.name as team_name
                  FROM match_goals mg
                  JOIN players p ON mg.player_id = p.id
                  LEFT JOIN players a ON mg.assist_player_id = a.id
                  JOIN teams t ON mg.team_id = t.id
                  WHERE mg.match_id = ?
                  ORDER BY mg.minute ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $match_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get match cards
     */
    public function getCards($match_id) {
        $query = "SELECT mc.*,
                    CONCAT(p.first_name, ' ', p.last_name) as player_name,
                    t.name as team_name
                  FROM match_cards mc
                  JOIN players p ON mc.player_id = p.id
                  JOIN teams t ON mc.team_id = t.id
                  WHERE mc.match_id = ?
                  ORDER BY mc.minute ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $match_id);
        $stmt->execute();
        return $stmt;
    }
}
