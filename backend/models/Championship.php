<?php
/**
 * Championship Model
 * Handles CRUD operations for championships
 */

class Championship {
    private $conn;
    private $table_name = "championships";

    public $id;
    public $name;
    public $format;
    public $year;
    public $start_date;
    public $end_date;
    public $description;
    public $country;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all championships
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY year DESC, start_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get single championship by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Create new championship
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (name, format, year, start_date, end_date, description, country) 
                  VALUES (:name, :format, :year, :start_date, :end_date, :description, :country)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":format", $this->format);
        $stmt->bindParam(":year", $this->year);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":country", $this->country);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Update championship
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, format = :format, year = :year, 
                      start_date = :start_date, end_date = :end_date, 
                      description = :description, country = :country
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":format", $this->format);
        $stmt->bindParam(":year", $this->year);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":country", $this->country);

        return $stmt->execute();
    }

    /**
     * Delete championship
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }

    /**
     * Get championship standings
     */
    public function getStandings($championship_id) {
        $query = "SELECT 
                    t.id, t.name, t.short_name,
                    tc.points, tc.wins, tc.draws, tc.losses,
                    tc.goals_for, tc.goals_against,
                    (tc.goals_for - tc.goals_against) as goal_difference,
                    tc.finish_position
                  FROM teams t
                  JOIN team_championships tc ON t.id = tc.team_id
                  WHERE tc.championship_id = ?
                  ORDER BY tc.finish_position ASC, tc.points DESC, goal_difference DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $championship_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get top scorers in a championship
     */
    public function getTopScorers($championship_id) {
        $query = "SELECT 
                    p.id, p.first_name, p.last_name, t.name as team_name,
                    SUM(ms.goals) as total_goals
                  FROM players p
                  JOIN match_stats ms ON p.id = ms.player_id
                  JOIN matches m ON ms.match_id = m.id
                  JOIN teams t ON ms.team_id = t.id
                  WHERE m.championship_id = ?
                  GROUP BY p.id, p.first_name, p.last_name, t.name
                  HAVING total_goals > 0
                  ORDER BY total_goals DESC
                  LIMIT 20";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $championship_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get top assisters in a championship
     */
    public function getTopAssisters($championship_id) {
        $query = "SELECT 
                    p.id, p.first_name, p.last_name, t.name as team_name,
                    SUM(ms.assists) as total_assists
                  FROM players p
                  JOIN match_stats ms ON p.id = ms.player_id
                  JOIN matches m ON ms.match_id = m.id
                  JOIN teams t ON ms.team_id = t.id
                  WHERE m.championship_id = ?
                  GROUP BY p.id, p.first_name, p.last_name, t.name
                  HAVING total_assists > 0
                  ORDER BY total_assists DESC
                  LIMIT 20";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $championship_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get cards statistics in a championship
     */
    public function getCardsStats($championship_id) {
        $query = "SELECT 
                    p.id, p.first_name, p.last_name, t.name as team_name,
                    SUM(CASE WHEN mc.card_type = 'yellow' THEN 1 ELSE 0 END) as yellow_cards,
                    SUM(CASE WHEN mc.card_type = 'red' THEN 1 ELSE 0 END) as red_cards
                  FROM players p
                  JOIN match_cards mc ON p.id = mc.player_id
                  JOIN matches m ON mc.match_id = m.id
                  JOIN teams t ON mc.team_id = t.id
                  WHERE m.championship_id = ?
                  GROUP BY p.id, p.first_name, p.last_name, t.name
                  ORDER BY red_cards DESC, yellow_cards DESC
                  LIMIT 20";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $championship_id);
        $stmt->execute();
        return $stmt;
    }
}
