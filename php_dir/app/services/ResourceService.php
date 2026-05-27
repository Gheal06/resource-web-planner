<?php
    require_once __DIR__ . "/../models/ResurseModel.php";
    require_once __DIR__."/../../conn.php";
    class ResourceService{
        public function __construct(){

        }
        public function addResource($inventoryId, $resourceName, $unit, $resourceDescription){
            global $connection;
            $model = new ResurseModel($connection);
            $retval = $model -> create($resourceName, $resourceDescription, 0, $unit, $inventoryId);
            if($retval == false){
                return "Failed to create resource";
            }
            return "";
        }
    }
?>