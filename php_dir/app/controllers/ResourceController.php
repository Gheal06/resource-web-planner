<?php
    require_once __DIR__ . "/../services/ResourceService.php";
    class ResourceController{
        private $resourceService;
        public function __construct() {
            $this->resourceService = new ResourceService();
        }

    }
?>