<?php
    require_once __DIR__ . "/../services/ResourceService.php";
    class ResourceController{
        private $resourceService;
        public function __construct() {
            $this -> resourceService = new ResourceService();
        }
        public function addResource(){
            if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitNewResource']) && isset($_GET['inventory_id'])){
                $msg = $this -> resourceService -> addResource($_GET['inventory_id'], $_POST['resource-name'], $_POST['unit'], $_POST['description']);
                if($msg == ""){
                    header('Location: inventory.php?inventory_id='.$_GET['inventory_id']);
                }
                return $msg;
            }
        }
        public function addTag(){
            if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitNewTag']) && isset($_GET['inventory_id'])){
                $msg = $this -> resourceService -> addTag($_GET['inventory_id'], $_POST['tag-name'], $_POST['bgcolor'], $_POST['fgcolor']);
                if($msg == ""){
                    header('Location: inventory.php?inventory_id='.$_GET['inventory_id']);
                }
                return $msg;
            }
        }
    }
?>