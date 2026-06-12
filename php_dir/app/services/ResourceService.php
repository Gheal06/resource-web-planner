<?php
    require_once __DIR__ . "/../models/ResurseModel.php";
    class ResourceService{
        private $resurseModel;

        public function __construct($connection){
            $this->resurseModel = new ResurseModel($connection);
        }

        public function addResource($inventoryId, $resourceName, $unit, $threshold_quantity, $resourceDescription){
            return $this->resurseModel->create($resourceName, $resourceDescription, 0, $threshold_quantity, $unit, $inventoryId);
        }
        public function removeResource($inventoryId, $resourceId){
            try{
                return $this->resurseModel->delete($inventoryId, $resourceId);
            }catch(Exception $e){
                return false;
            }
        }
        public function addTag($inventoryId, $tagName, $bgColor, $fgColor){
            return $this->resurseModel->createTag($inventoryId, $tagName, $fgColor, $bgColor);
        }
    }
?>