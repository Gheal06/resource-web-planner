<?php
    require_once __DIR__ . "/../services/ResourceService.php";
    class ResourceController{
        private $resourceService;
        public function __construct() {
            $this -> resourceService = new ResourceService();
        }
        public function addResource(){
            if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']) && isset($_GET['inventory_id'])){
                $msg = $this -> resourceService -> addResource($_GET['inventory_id'], $_POST['resource-name'], $_POST['unit'], $_POST['description']);
                if($msg == ""){
                    header('Location: inventory.php?inventory_id='.$_GET['inventory_id']);
                }
                return $msg;
            }
        }
    }
?>