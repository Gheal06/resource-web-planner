<?php
  class InventoryPermissionsModel {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function getUserInventoryIDsByMask($user_id, $required_permissions_mask) {
      // returneaza id-urile tabelelor pt care utilizatorul are MACAR drepturile din permission_mask
        $res = pg_query_params($this->conn, "SELECT inventory_id FROM user_inventory_permission WHERE user_inventory_permission.user_id = $1 AND (user_inventory_permission.permissions & $2) = $2", array($user_id, $required_permissions_mask));
      if (!$res) {
        return array();
      }
        $ids = array();
        while ($row = pg_fetch_assoc($res)) {
            $ids[] = $row['inventory_id'];
        }

        return $ids;
    }

    public function canUserAccessInventory($user_id, $inventory_id, $required_permissions_mask) {
      $res = pg_query_params($this->conn, "SELECT EXISTS(SELECT 1 FROM user_inventory_permission WHERE user_inventory_permission.user_id = $1 AND user_inventory_permission.inventory_id = $2 AND (user_inventory_permission.permissions & $3) = $3) AS has_access", array($user_id, $inventory_id, $required_permissions_mask));
      if (!$res) {
        return false;
      }

      $row = pg_fetch_assoc($res);
      return $row && $row['has_access'] == 't';
    }

    public function getUserInventoryPermissionMask($user_id, $inventory_id) {
      $res = pg_query_params($this->conn, "SELECT permissions FROM user_inventory_permission WHERE user_inventory_permission.user_id = $1 AND user_inventory_permission.inventory_id = $2", array($user_id, $inventory_id));
      if (!$res) {
        return 0;
      }

      $row = pg_fetch_assoc($res);
      return $row ? intval($row['permissions']) : 0;
    }

    public function getAllAssociatedUsers ($inventory_id) {
      // returneaza toti utilizatorii care au drepturi pe un tabel, impreuna cu drepturile lor
      $res = pg_query_params($this->conn, "SELECT users.id, users.username, users.email, user_inventory_permission.permissions FROM user_inventory_permission JOIN users ON user_inventory_permission.user_id = users.id WHERE user_inventory_permission.inventory_id = $1 ORDER BY users.username", array($inventory_id));
      if (!$res) {
        return array();
      }
      $users = array();
      while ($row = pg_fetch_assoc($res)) {
        $users[] = array('user_id' => $row['id'], 'username' => $row['username'], 'email' => $row['email'], 'permissions' => intval($row['permissions']));
      }
      return $users;
    }

    public function setUserInventoryPermissions($user_id, $inventory_id, $permissions_mask) {

      @pg_query_params($this->conn, "DELETE FROM user_inventory_permission WHERE user_id = $1 AND inventory_id = $2", array($user_id, $inventory_id));

      return @pg_query_params($this->conn, "INSERT INTO user_inventory_permission (user_id, inventory_id, permissions) VALUES ($1, $2, $3)", array($user_id, $inventory_id, $permissions_mask));
    }
    public function removeUserInventoryPermission($user_id, $inventory_id) {
      return pg_query_params($this->conn, "DELETE FROM user_inventory_permission WHERE user_id = $1 AND inventory_id = $2", array($user_id, $inventory_id));
    }  }

?>