<?php
/**
 * Referee Model
 */

class Referee {
    private $conn;
    private $table_name = "referees";

    public $id;
    public $first_name;
    public $last_name;
    public $nationality;
    public $birth_date;
    public $photo_url;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY last_name, first_name";
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
                  (first_name, last_name, nationality, birth_date, photo_url) 
                  VALUES (:first_name, :last_name, :nationality, :birth_date, :photo_url)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":nationality", $this->nationality);
        $stmt->bindParam(":birth_date", $this->birth_date);
        $stmt->bindParam(":photo_url", $this->photo_url);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET first_name = :first_name, last_name = :last_name,
                      nationality = :nationality, birth_date = :birth_date,
                      photo_url = :photo_url
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":nationality", $this->nationality);
        $stmt->bindParam(":birth_date", $this->birth_date);
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
     * Get referee statistics
     */
    public function getStats($referee_id) {
        $query = "SELECT 
                    COUNT(DISTINCT m.id) as matches_refereed,
                    COUNT(DISTINCT CASE WHEN mc.card_type = 'yellow' THEN mc.id END) as yellow_cards_issued,
                    COUNT(DISTINCT CASE WHEN mc.card_type = 'red' THEN mc.id END) as red_cards_issued,
                    COUNT(DISTINCT CASE WHEN mg.goal_type = 'penalty' THEN mg.id END) as penalties_awarded
                  FROM matches m
                  LEFT JOIN match_cards mc ON m.id = mc.match_id
                  LEFT JOIN match_goals mg ON m.id = mg.match_id
                  WHERE m.referee_id = ?
                  AND m.match_status = 'completed'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $referee_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get matches refereed
     */
    public function getMatches($referee_id) {
        $query = "SELECT 
                    m.id, m.match_date, m.match_time,
                    ht.name as home_team, at.name as away_team,
                    m.home_score, m.away_score,
                    c.name as championship_name,
                    (SELECT COUNT(*) FROM match_cards WHERE match_id = m.id AND card_type = 'yellow') as yellow_cards,
                    (SELECT COUNT(*) FROM match_cards WHERE match_id = m.id AND card_type = 'red') as red_cards
                  FROM matches m
                  JOIN teams ht ON m.home_team_id = ht.id
                  JOIN teams at ON m.away_team_id = at.id
                  JOIN championships c ON m.championship_id = c.id
                  WHERE m.referee_id = ?
                  ORDER BY m.match_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $referee_id);
        $stmt->execute();
        return $stmt;
    }
}
