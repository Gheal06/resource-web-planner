<?php
    $inventoryManagementService = new InventoryManagementService($connection);
    $inventoryId = $_GET['inventory_id'] ?? -1;
    $allResources = $inventoryManagementService -> getResourcesByInventoryId($inventoryId);
    $allFunds = $inventoryManagementService -> getFonduriByInventoryId($inventoryId);
?>
<div>
    <h2>Stats</h2>
    <div class="sep"></div>
    <h3>Generate Report</h3>
    <form action="report.php" method="post">
        <label for="asset-name"> Asset Name: </label>
        <select id="asset-id" name="asset_id" required>
            <option value="">-- Select Asset --</option>
            <?php 
                foreach($allFunds as $fund){
                    // echo '<option>'.$fund['name'].'</option>';
                    /*foreach($fund as $key => $val){
                        echo '<option>'.$key.': '.$val.'</option>';
                    }*/
                    $id = $fund['id'] ?? '';
                    $code = $fund['currency_code'] ?? '';
                    $name = $fund['name'];
                    echo '<option value="f'.htmlspecialchars($id).'"> Currency: '.htmlspecialchars($code).(isset($name) ? '('.$name.')' : '').'</option>';
                }
            ?>
            <?php 
                foreach($allResources as $resource){
                    // echo '<option>'.$fund['name'].'</option>';
                    /*foreach($fund as $key => $val){
                        echo '<option>'.$key.': '.$val.'</option>';
                    }*/
                    $id = $resource['id'] ?? '';
                    $name = $resource['name'] ?? 'N/A';
                    echo '<option value="r'.htmlspecialchars($id).'"> Resource: '.htmlspecialchars($name).'</option>';
                }
            ?>
        </select> <br>
        <input type="text" name="inventory_id" value="<?php echo $inventoryId?>" style="display:none">
        <label for="start-date">Start Date:</label>
        <input type="date" name="start_date" id="start-date"> <br>
        <label for="end-date">End Date:</label>
        <input type="date" name="end_date" id="end-date"> <br> <br>
        <input type="submit" name="submit_generate_html" value="Generate HTML" onclick="window.open(url, '_blank')">
        <input type="submit" name="submit_download_pdf" value="Download PDF" onclick="window.open(url, '_blank')" formaction = "pdf_report.php">
    </form>
</div>