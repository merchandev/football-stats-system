<?php
/**
 * Coach Model
 */

class Coach {
    private $conn;
    private $table_name = "coaches";

    public $id;
    public $first_name;
    public $last_name;
    public $birth_date;
    public $nationality;
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
                  (first_name, last_name, birth_date, nationality, photo_url) 
                  VALUES (:first_name, :last_name, :birth_date, :nationality, :photo_url)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":birth_date", $this->birth_date);
        $stmt->bindParam(":nationality", $this->nationality);
        $stmt->bindParam(":photo_url", $this->photo_url);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET first_name = :first_name, last_name = :last_name,
                      birth_date = :birth_date, nationality = :nationality,
                      photo_url = :photo_url
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":birth_date", $this->birth_date);
        $stmt->bindParam(":nationality", $this->nationality);
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
     * Get coach statistics by team
     */
    public function getStatsByTeam($coach_id) {
        $query = "SELECT 
                    t.id as team_id, t.name as team_name,
                    COUNT(*) as matches_directed,
                    SUM(CASE 
                        WHEN (m.home_coach_id = ? AND m.home_score > m.away_score) OR
                             (m.away_coach_id = ? AND m.away_score > m.home_score)
                        THEN 1 ELSE 0 END) as wins,
                    SUM(CASE 
                        WHEN m.home_score = m.away_score 
                        THEN 1 ELSE 0 END) as draws,
                    SUM(CASE 
                        WHEN (m.home_coach_id = ? AND m.home_score < m.away_score) OR
                             (m.away_coach_id = ? AND m.away_score < m.home_score)
                        THEN 1 ELSE 0 END) as losses
                  FROM matches m
                  JOIN teams t ON (t.id = m.home_team_id OR t.id = m.away_team_id)
                  WHERE (m.home_coach_id = ? OR m.away_coach_id = ?)
                    AND ((m.home_coach_id = ? AND t.id = m.home_team_id) OR
                         (m.away_coach_id = ? AND t.id = m.away_team_id))
                    AND m.match_status = 'completed'
                  GROUP BY t.id, t.name";
        
        $stmt = $this->conn->prepare($query);
        for($i = 1; $i <= 8; $i++) {
            $stmt->bindParam($i, $coach_id);
        }
        $stmt->execute();
        return $stmt;
    }
}
