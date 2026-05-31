<?php

class ResurseModel {
    private $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    public function getResurseById($resource_id) {
        $res = @pg_query_params($this->connection, "SELECT * FROM resources WHERE id = $1", array($resource_id));
        if (!$res) return null;
        return pg_fetch_assoc($res);
    }

    public function getResourcesByInventoryId($inventory_id) {
        $res = @pg_query_params($this->connection, "SELECT * FROM resources WHERE inventory_id = $1", array($inventory_id));
        if (!$res) return array();
        $rows = array();
        while ($row = pg_fetch_assoc($res)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function create($name, $description, $quantity, $unit, $inventory_id) {
        $res = @pg_query_params($this->connection,
            "INSERT INTO resources (name, description, quantity, unit, inventory_id) VALUES ($1, $2, $3, $4, $5) RETURNING id",
            array($name, $description, $quantity, $unit, $inventory_id)
        );
        if ($res === false) return false;
        $row = pg_fetch_assoc($res);
        return $row ? $row['id'] : false;
    }
    public function delete($inventory_id, $id) {
        return pg_query_params($this->connection,
            "DELETE FROM resources WHERE id=$1 AND inventory_id=$2",
            array($id, $inventory_id));
    }

    public function move($resource_id, $new_inventory_id) {
        $res = @pg_query_params($this->connection,
            "UPDATE resources SET inventory_id = $1 WHERE id = $2",
            array($new_inventory_id, $resource_id)
        );
        return $res === false ? false : $res;
    }

    public function add_ammount($resource_id, $amount) {
        $res = @pg_query_params($this->connection,
            "UPDATE resources SET quantity = quantity + $1 WHERE id = $2",
            array($amount, $resource_id)
        );
        if ($res === false) {
            $err = pg_last_error($this->connection);
            return array('success' => false, 'message' => $err, 'code' => 'db_error');
        }
        return array('success' => true, 'message' => 'Amount added.');
    }

    public function set_ammount($resource_id, $amount) {
        if ($amount < 0) {
            return array('success' => false, 'message' => 'Amount cannot be negative.', 'code' => 'invalid_amount');
        }
        $res = @pg_query_params($this->connection,
            "UPDATE resources SET quantity = $1 WHERE id = $2",
            array($amount, $resource_id)
        );
        if ($res === false) {
            $err = pg_last_error($this->connection);
            return array('success' => false, 'message' => $err, 'code' => 'db_error');
        }
        return array('success' => true, 'message' => 'Amount set.');
    }

    public function getTagsForResource($resource_id) {
        $sql = "SELECT t.* FROM tags t JOIN has_tag ht ON ht.tag_id = t.id WHERE ht.resource_id = $1";
        $res = pg_query_params($this->connection, $sql, array($resource_id));
        if (!$res){
            return array();
        }
        $rows = array();
        while ($row = pg_fetch_assoc($res)) {
            array_push($rows, $row);
        }
        return $rows;
    }

    public function getResourcesByTags($inventory_id, $tag_ids) {
        if (empty($tag_ids)) return array();
        
        // Build placeholders for tag_ids: $1, $2, ...
        $placeholders = array();
        for ($i = 0; $i < count($tag_ids); $i++) {
            $placeholders[] = '$' . ($i + 2);
        }
        $placeholder_str = implode(',', $placeholders);
        
        // COUNT ensures resource has ALL tags (intersection)
        $sql = "SELECT DISTINCT r.* FROM resources r 
                JOIN has_tag ht ON ht.resource_id = r.id 
                WHERE r.inventory_id = $1 AND ht.tag_id IN ($placeholder_str) 
                GROUP BY r.id 
                HAVING COUNT(DISTINCT ht.tag_id) = " . count($tag_ids);
        
        $params = array_merge(array($inventory_id), $tag_ids);
        $res = @pg_query_params($this->connection, $sql, $params);
        if (!$res) return array();
        $rows = array();
        while ($row = pg_fetch_assoc($res)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getAllTags($inventory_id) {
        $res = @pg_query_params($this->connection, "SELECT * FROM tags where inventory_id=".$inventory_id." ORDER BY name", array());

        if (!$res) return array();
        $rows = array();
        while ($row = pg_fetch_assoc($res)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getTagById($tag_id) {
        $res = @pg_query_params($this->connection, "SELECT * FROM tags WHERE id = $1", array($tag_id));
        if (!$res) return null;
        return pg_fetch_assoc($res);
    }

    public function createTag($inventory_id, $name, $foreground_color, $background_color) {
        if (!$name || !$foreground_color || !$background_color) {
            return false;
        }
        $res = @pg_query_params($this->connection,
            "INSERT INTO tags (name, inventory_id, fgcolor, bgcolor) VALUES ($1, $2, $3, $4) RETURNING id",
            array($name, $inventory_id, $foreground_color, $background_color)
        );
        if ($res === false) return false;
        $row = pg_fetch_assoc($res);
        return $row ? $row['id'] : false;
    }

    public function addTagToResource($resource_id, $tag_id) {
        return @pg_query_params(
            $this->connection,
            "INSERT INTO has_tag (resource_id, tag_id) VALUES ($1, $2)",
            array($resource_id, $tag_id)
        );
    }

    public function updateTag($tag_id, $name = null, $description = null, $foreground_color = null, $background_color = null) {
        $updates = array();
        $params = array();
        $param_count = 1;

        if ($name !== null) {
            $updates[] = "name = \$" . $param_count++;
            $params[] = $name;
        }
        if ($description !== null) {
            $updates[] = "description = \$" . $param_count++;
            $params[] = $description;
        }
        if ($foreground_color !== null) {
            $updates[] = "foreground_color = \$" . $param_count++;
            $params[] = $foreground_color;
        }
        if ($background_color !== null) {
            $updates[] = "background_color = \$" . $param_count++;
            $params[] = $background_color;
        }

        if (empty($updates)) return false;

        $updates[] = "id = \$" . $param_count;
        $params[] = $tag_id;

        $sql = "UPDATE tags SET " . implode(", ", array_slice($updates, 0, -1)) . " WHERE id = \$" . $param_count;
        return @pg_query_params($this->connection, $sql, $params);
    }
}

?>