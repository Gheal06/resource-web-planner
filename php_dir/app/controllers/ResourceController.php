<?php
    require_once __DIR__ . "/../services/ResourceService.php";
    class ResourceController{
        private $resourceService;
        private $connection;
        public function __construct($connection) {
            $this -> connection = $connection;
            $this -> resourceService = new ResourceService($connection);
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
        public function addCurrency(){
            if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitNewCurrency']) && isset($_GET['inventory_id'])){
                try{
                    $msg = new FonduriModel($this->connection) -> create($_GET['inventory_id'], $_POST['currencyCode'], $_POST['currencyName'], $_POST['description']);
                    header('Location: inventory.php?inventory_id='.$_GET['inventory_id']);
                }catch(Exception $e){
                    return $e->getMessage();
                }
                return $msg;
            }
        }
        public function removeCurrency(){
            if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitRemoveCurrency']) && isset($_GET['inventory_id']) && isset($_GET['currencyId'])){
                try{
                    $msg = new FonduriModel($this->connection) -> deleteFonduri($_GET['inventory_id'], $_GET['currencyId']);
                }catch(Exception $e){
                }
                finally{
                    header('Location: inventory.php?inventory_id='.$_GET['inventory_id']);
                }
                return $msg;
            }
            header('Location: error.php');
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