<?php
require_once __DIR__ . "/../models/InventoryModel.php";
require_once __DIR__ . "/../models/InventoryPermissionsModel.php";
require_once __DIR__ . "/../models/UserModel.php";
require_once __DIR__ . "/../models/FonduriModel.php";
require_once __DIR__ . "/../models/ResurseModel.php";
require_once __DIR__ . "/../models/CurrencyModel.php";

  class InventoryManagementService {
    private $inventoryModel;
    private $inventoryPermissionsModel;
    private $userModel;
    private $fonduriModel;
    private $resurseModel;
    private $currencyModel;

    private $readPermissionMask = 1;
    private $insertPermissionMask = 2;
    private $updatePermissionMask = 4;
    private $deletePermissionMask = 8;

    public function __construct($connection) {
      $this->inventoryModel= new InventoryModel($connection);
      $this->inventoryPermissionsModel = new InventoryPermissionsModel($connection);
      $this->userModel = new UserModel($connection);
      $this->fonduriModel = new FonduriModel($connection);
      $this->resurseModel = new ResurseModel($connection);
      $this->currencyModel = new CurrencyModel($connection);
    }
    public function getUserInventoryIDsByMask($username, $permission_mask) {
        return $this->inventoryPermissionsModel->getUserInventoryIDsByMask($username, $permission_mask);
    }
    public function getUserInventoryById($id) {
        return $this->inventoryModel->getInventoryById($id);
    }
    public function getUserInventoriesByMask($username, $permission_mask) {
        $ids = $this->getUserInventoryIDsByMask($username, $permission_mask);
        $inventories = array();
        foreach ($ids as $id) {
            $inventories[] = $this->inventoryModel->getInventoryById($id);
        }
        return $inventories;
    }

    public function canUserAccessInventory($username, $inventory_id, $permission_mask) {
        return $this->inventoryPermissionsModel->canUserAccessInventory($username, $inventory_id, $permission_mask);
    }
    
    public function canRead($username, $inventory_id) {
      return $this->inventoryPermissionsModel->canUserAccessInventory($username, $inventory_id, $this->readPermissionMask);
    }

    public function canInsert($username, $inventory_id) {
      return $this->inventoryPermissionsModel->canUserAccessInventory($username, $inventory_id, $this->insertPermissionMask);
    }

    public function canEdit($username, $inventory_id) {
      return $this->inventoryPermissionsModel->canUserAccessInventory($username, $inventory_id, $this->editPermissionMask);
    }

    public function canDelete($username, $inventory_id) {
      return $this->inventoryPermissionsModel->canUserAccessInventory($username, $inventory_id, $this->deletePermissionMask);
    }

    private function accessDenied() {
      return array('success' => false, 'message' => 'Access denied.');
    }

    private function notFound($message) {
      return array('success' => false, 'message' => $message);
    }

    public function createInventory($name, $description, $username) {
      $this->inventoryModel->beginTransaction();
      try {
        $owner = $this->userModel->findByUsername($username);
        if (!$owner || !isset($owner['id'])) {
          throw new Exception('Owner user not found');
        }

        $owner_user_id = $owner['id'];
        $i_id = $this->inventoryModel->create($name, $description, $owner_user_id);
        if ($i_id === false) {
          throw new Exception('Failed to create inventory');
        }

        $permRes = $this->inventoryPermissionsModel->setUserInventoryPermissions($owner_user_id, $i_id, $this->readPermissionMask | $this->insertPermissionMask | $this->updatePermissionMask | $this->deletePermissionMask);
        if ($permRes === false) {
          throw new Exception('Failed to set inventory permissions');
        }

        $this->inventoryModel->commitTransaction();
        return $i_id;
      } catch (Exception $e) {
        $this->inventoryModel->rollbackTransaction();
        return false;
      }
    }
    public function updateInventory($username, $id, $name, $description) {
      if (!$this->canEdit($username, $id)) {
        return $this->accessDenied();
      }
      return $this->inventoryModel->update($id, $name, $description);
    }
    public function updateInventoryName($username, $id, $name) {
      if (!$this->canEdit($username, $id)) {
        return $this->accessDenied();
      }
      return $this->inventoryModel->update($id, $name, null);
    }

    public function getFonduriByInventoryId($inventory_id) {
      $res = $this->fonduriModel->getFonduriByInventoryId($inventory_id);
      return $res === null ? array() : $res;
    }

    public function getResourcesByInventoryId($inventory_id) {
      $res = $this->resurseModel->getResourcesByInventoryId($inventory_id);
      return $res === null ? array() : $res;
    }

    public function getFonduriByInventoryIdAndCurrency($inventory_id, $currency_code) {
      $res = $this->fonduriModel->getFonduriByInventoryIdAndCurrency($inventory_id, $currency_code);
      return $res === null ? null : $res;
    }

    public function addFonduri($username, $inventory_id, $amount, $currency_code, $name = null, $description = null) {
      $fonduri = $this->fonduriModel->getFonduriByInventoryIdAndCurrency($inventory_id, $currency_code);
      if ($fonduri) {
        if (!$this->canEdit($username, $inventory_id)) {
          return $this->accessDenied();
        }
      } else {
        if (!$this->canInsert($username, $inventory_id)) {
          return $this->accessDenied();
        }
      }
      $res = $this->fonduriModel->addFonduri($inventory_id, $amount, $currency_code, $name, $description);
      if ($res === false) {
        return array('success' => false, 'message' => 'Failed to add funds.');
      }
      return array('success' => true, 'message' => 'Funds added.');
    }

    public function setFonduri($username, $inventory_id, $amount, $currency_code, $name = null, $description = null) {
      $fonduri = $this->fonduriModel->getFonduriByInventoryIdAndCurrency($inventory_id, $currency_code);
      if ($fonduri) {
        if (!$this->canEdit($username, $inventory_id)) {
          return $this->accessDenied();
        }
      } else {
        if (!$this->canInsert($username, $inventory_id)) {
          return $this->accessDenied();
        }
      }
      $res = $this->fonduriModel->setFonduri($inventory_id, $amount, $currency_code, $name, $description);
      if ($res === false) {
        return array('success' => false, 'message' => 'Failed to set funds.');
      }
      return array('success' => true, 'message' => 'Funds set.');
    }

    public function createResurse($username, $name, $description, $quantity, $unit, $inventory_id) {
      if (!$this->canInsert($username, $inventory_id)) {
        return $this->accessDenied();
      }
      $res = $this->resurseModel->create($name, $description, $quantity, $unit, $inventory_id);
      if ($res === false) {
        return array('success' => false, 'message' => 'Failed to create resource.');
      }
      return array('success' => true, 'message' => 'Resource created.', 'id' => $res);
    }

    public function moveResurse($username, $resource_id, $new_inventory_id) {
      $resource = $this->resurseModel->getResurseById($resource_id);
      if (!$resource) {
        return $this->notFound('Resource not found.');
      }
      if (!$this->canDelete($username, $resource['inventory_id']) || !$this->canInsert($username, $new_inventory_id)) {
        return $this->accessDenied();
      }
      $res = $this->resurseModel->move($resource_id, $new_inventory_id);
      if ($res === false) {
        return array('success' => false, 'message' => 'Failed to move resource.');
      }
      return array('success' => true, 'message' => 'Resource moved.');
    }

    public function addResurseAmount($username, $resource_id, $amount) {
      $resource = $this->resurseModel->getResurseById($resource_id);
      if (!$resource) {
        return $this->notFound('Resource not found.');
      }
      if (!$this->canEdit($username, $resource['inventory_id'])) {
        return $this->accessDenied();
      }
      $res = $this->resurseModel->add_ammount($resource_id, $amount);
      if (is_array($res)) {
        return $res;
      }
      return array('success' => true, 'message' => 'Amount added.');
    }

    public function setResurseAmount($username, $resource_id, $amount) {
      $resource = $this->resurseModel->getResurseById($resource_id);
      if (!$resource) {
        return $this->notFound('Resource not found.');
      }
      if (!$this->canEdit($username, $resource['inventory_id'])) {
        return $this->accessDenied();
      }
      $res = $this->resurseModel->set_ammount($resource_id, $amount);
      if (is_array($res)) {
        return $res;
      }
      return array('success' => true, 'message' => 'Amount set.');
    }

    public function getTagsForResource($username, $resource_id) {
      $resource = $this->resurseModel->getResurseById($resource_id);
      if (!$resource) return $this->notFound('Resource not found.');
      if (!$this->canRead($username, $resource['inventory_id'])) return $this->accessDenied();
      return $this->resurseModel->getTagsForResource($resource_id);
    }

    public function getResourcesByTags($username, $inventory_id, $tag_ids) {
      if (!$this->canRead($username, $inventory_id)) return $this->accessDenied();
      return $this->resurseModel->getResourcesByTags($inventory_id, $tag_ids);
    }

    public function getAllTags() {
      return $this->resurseModel->getAllTags();
    }

    public function getAllCurrencies() {
      return $this->currencyModel->getAllCurrencies();
    }

    public function getTagById($tag_id) {
      return $this->resurseModel->getTagById($tag_id);
    }

    public function createTag($name, $foreground_color, $background_color, $description = null) {
      $tag_id = $this->resurseModel->createTag($name, $foreground_color, $background_color, $description);
      if ($tag_id === false) {
        return array('success' => false, 'message' => 'Failed to create tag.', 'code' => 'db_error');
      }
      return array('success' => true, 'message' => 'Tag created.', 'id' => $tag_id);
    }

    public function updateTag($tag_id, $name = null, $description = null, $foreground_color = null, $background_color = null) {
      $res = $this->resurseModel->updateTag($tag_id, $name, $description, $foreground_color, $background_color);
      if ($res === false) {
        return array('success' => false, 'message' => 'Failed to update tag.', 'code' => 'db_error');
      }
      return array('success' => true, 'message' => 'Tag updated.');
    }
  }
?>