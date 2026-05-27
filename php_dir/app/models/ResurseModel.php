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

    /**
     * Get all tags for a resource
     */
    public function getTagsForResource($resource_id) {
        $sql = "SELECT t.* FROM tags t JOIN has_tag ht ON ht.tag_id = t.id WHERE ht.resource_id = $1";
        $res = @pg_query_params($this->connection, $sql, array($resource_id));
        if (!$res) return array();
        $rows = array();
        while ($row = pg_fetch_assoc($res)) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * Get resources by a list of tag IDs (intersection - resource must have ALL tags)
     * $tag_ids is an array of tag IDs
     */
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

    /**
     * Get all tags in the system
     */
    public function getAllTags() {
        $res = @pg_query_params($this->connection, "SELECT * FROM tags ORDER BY name", array());
        if (!$res) return array();
        $rows = array();
        while ($row = pg_fetch_assoc($res)) {
            $rows[] = $row;
        }
        return $rows;
    }
}

?>
