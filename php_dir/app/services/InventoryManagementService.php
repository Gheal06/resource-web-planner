<?php
require_once __DIR__ . "/../models/InventoryModel.php";
require_once __DIR__ . "/../models/InventoryPermissionsModel.php";
require_once __DIR__ . "/../models/UserModel.php";
require_once __DIR__ . "/../models/FonduriModel.php";
require_once __DIR__ . "/../models/ResurseModel.php";
require_once __DIR__ . "/../models/CurrencyModel.php";
require_once __DIR__ . "/ResourceService.php";
require_once __DIR__ . "/MailingService.php";

  class InventoryManagementService {
    private $inventoryModel;
    private $inventoryPermissionsModel;
    private $userModel;
    private $fonduriModel;
    private $resurseModel;
    private $currencyModel;
    private $resourceService;

    private $mailingService;

    private $readPermissionMask = 1;
    private $editPermissionMask = 2;
    private $updatePermissionMask = 4;
    private $deletePermissionMask = 8;

    public function __construct($connection) {
      $this->inventoryModel= new InventoryModel($connection);
      $this->inventoryPermissionsModel = new InventoryPermissionsModel($connection);
      $this->userModel = new UserModel($connection);
      $this->fonduriModel = new FonduriModel($connection);
      $this->resurseModel = new ResurseModel($connection);
      $this->currencyModel = new CurrencyModel($connection);
      $this->resourceService = new ResourceService($connection);
      $this->mailingService = new MailingService();
    }

    public function getUserByUsername($username) {
      return $this->userModel->findByUsername($username);
    }
    public function getUserById($id) {
      return $this->userModel->findById($id);
    }

    public function getUserInventoryIDsByMask($username, $permission_mask) {
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return array();
      }
        return $this->inventoryPermissionsModel->getUserInventoryIDsByMask($user['id'], $permission_mask);
    }
    public function getUserInventoryById($id) {
        return $this->inventoryModel->getInventoryById($id);
    }
    public function getUserInventoriesByMask($username, $permission_mask) {
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return array();
      }
      $ids = $this->getUserInventoryIDsByMask($username, $permission_mask);
      $inventories = array();
      foreach ($ids as $id) {
        $inventories[] = $this->inventoryModel->getInventoryById($id);
      }
        return $inventories;
    }

    
    public function sendEmailToOwner($inventory_id, $subject, $body) {
      $inventory = $this->inventoryModel->getInventoryById($inventory_id);
      if (!$inventory) {
        return false;
      }
      $owner = $this->userModel->findById($inventory['owner_id']);
      if (!$owner || !isset($owner['email'])) {
        return false;
      }
      $to = $owner['email'];
      return $this->mailingService->send_email($to, $subject, $body);
    }

    public function sendEmailToAllAssoc($inventory_id, $subject, $body) {
      $inventory = $this->inventoryModel->getInventoryById($inventory_id);
      if (!$inventory) {
        return false;
      }
      $associates = $this->inventoryPermissionsModel->getAllAssociatedUsers($inventory_id);
      foreach ($associates as $associate) {
        $user = $this->userModel->findById($associate['user_id']);
        if (isset($user['email'])) {
          $to = $user['email'];
          if ($this->mailingService->send_email($to, $subject, $body)) {
            // echo $user['email'] . " - email sent\n";
          } else {
            return false;
          }
          // echo $user['email'] . "\n";
        }
        // else{
        //   echo "troll\n";
        // }
      }
      return true;
    }

    public function canUserAccessInventory($user_id, $inventory_id, $permission_mask) {
        return $this->inventoryPermissionsModel->canUserAccessInventory($user_id, $inventory_id, $permission_mask);
    }
    
    public function canRead($user_id, $inventory_id) {
      return $this->inventoryPermissionsModel->canUserAccessInventory($user_id, $inventory_id, $this->readPermissionMask);
    }

    public function canUpdate($user_id, $inventory_id) {
      return $this->inventoryPermissionsModel->canUserAccessInventory($user_id, $inventory_id, $this->updatePermissionMask);
    }

    public function canEdit($user_id, $inventory_id) {
      return $this->inventoryPermissionsModel->canUserAccessInventory($user_id, $inventory_id, $this->editPermissionMask);
    }

    public function canDelete($user_id, $inventory_id) {
      return $this->inventoryPermissionsModel->canUserAccessInventory($user_id, $inventory_id, $this->deletePermissionMask);
    }

    private function accessDenied() {
      return array('success' => false, 'message' => 'Access denied.');
    }

    private function notFound($message) {
      return array('success' => false, 'message' => $message);
    }

    private function getInventoryExportData($user_id, $inventory_id) {
      if (!$this->canRead($user_id, $inventory_id)) {
        return $this->accessDenied();
      }

      $inventory = $this->inventoryModel->getInventoryById($inventory_id);
      if (!$inventory) {
        return $this->notFound('Inventory not found.');
      }

      $exportInventory = array(
        'id' => isset($inventory['id']) ? $inventory['id'] : null,
        'name' => isset($inventory['name']) ? $inventory['name'] : null,
        'description' => isset($inventory['description']) ? $inventory['description'] : null,
      );

      $funds = $this->getFonduriByInventoryId($inventory_id);
      usort($funds, function ($left, $right) {
        return strcmp((string)($left['currency_code'] ?? ''), (string)($right['currency_code'] ?? ''));
      });

      $exportFunds = array();
      foreach ($funds as $fund) {
        $exportFunds[] = array(
          'id' => isset($fund['id']) ? $fund['id'] : null,
          'inventory_id' => isset($fund['inventory_id']) ? $fund['inventory_id'] : null,
          'amount' => isset($fund['amount']) ? $fund['amount'] : null,
          'currency_code' => isset($fund['currency_code']) ? $fund['currency_code'] : null,
          'name' => isset($fund['name']) ? $fund['name'] : null,
          'description' => isset($fund['description']) ? $fund['description'] : null,
        );
      }

      $resources = $this->getResourcesByInventoryId($inventory_id);
      usort($resources, function ($left, $right) {
        return (int)($left['id'] ?? 0) <=> (int)($right['id'] ?? 0);
      });

      $exportResources = array();
      foreach ($resources as $resource) {
        $tags = $this->resurseModel->getTagsForResource($resource['id']);
        usort($tags, function ($left, $right) {
          return strcmp((string)($left['name'] ?? ''), (string)($right['name'] ?? ''));
        });

        $exportTags = array();
        foreach ($tags as $tag) {
          $exportTags[] = array(
            'id' => isset($tag['id']) ? $tag['id'] : null,
            'name' => isset($tag['name']) ? $tag['name'] : null,
            'description' => isset($tag['description']) ? $tag['description'] : null,
            'foreground_color' => isset($tag['foreground_color']) ? $tag['foreground_color'] : null,
            'background_color' => isset($tag['background_color']) ? $tag['background_color'] : null,
          );
        }

        $exportResources[] = array(
          'id' => isset($resource['id']) ? $resource['id'] : null,
          'inventory_id' => isset($resource['inventory_id']) ? $resource['inventory_id'] : null,
          'name' => isset($resource['name']) ? $resource['name'] : null,
          'description' => isset($resource['description']) ? $resource['description'] : null,
          'quantity' => isset($resource['quantity']) ? $resource['quantity'] : null,
          'unit' => isset($resource['unit']) ? $resource['unit'] : null,
          'tags' => $exportTags,
        );
      }

      return array(
        'success' => true,
        'inventory' => $exportInventory,
        'funds' => $exportFunds,
        'resources' => $exportResources,
      );
    }

    private function getExportFilename($inventory_id, $extension) {
      return 'inventory-' . $inventory_id . '.' . $extension;
    }

    private function jsonEncodeExport($value) {
      return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    private function jsonEncodeInline($value) {
      return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    private function buildInventoryCsvContent($exportData) {
      $handle = fopen('php://temp', 'r+');
      if ($handle === false) {
        return false;
      }

      fputcsv($handle, array('record_type', 'inventory_id', 'inventory_name', 'inventory_description', 'record_id', 'name', 'description', 'quantity', 'unit', 'currency_code', 'amount', 'tags_json'));

      $inventory = $exportData['inventory'];
      fputcsv($handle, array(
        'inventory',
        $inventory['id'],
        $inventory['name'],
        $inventory['description'],
        $inventory['id'],
        $inventory['name'],
        $inventory['description'],
        '',
        '',
        '',
        '',
        $this->jsonEncodeInline(array()),
      ));

      foreach ($exportData['funds'] as $fund) {
        fputcsv($handle, array(
          'fund',
          $fund['inventory_id'],
          $inventory['name'],
          $inventory['description'],
          $fund['id'],
          $fund['name'],
          $fund['description'],
          '',
          '',
          $fund['currency_code'],
          $fund['amount'],
          $this->jsonEncodeInline(array()),
        ));
      }

      foreach ($exportData['resources'] as $resource) {
        fputcsv($handle, array(
          'resource',
          $resource['inventory_id'],
          $inventory['name'],
          $inventory['description'],
          $resource['id'],
          $resource['name'],
          $resource['description'],
          $resource['quantity'],
          $resource['unit'],
          '',
          '',
          $this->jsonEncodeInline($resource['tags']),
        ));
      }

      rewind($handle);
      $content = stream_get_contents($handle);
      fclose($handle);
      return $content;
    }

    private function buildInventorySummaryLines($exportData) {
      $lines = array();
      $inventory = $exportData['inventory'];

      $lines[] = array('type' => 'title', 'text' => 'Inventory export: ' . $inventory['name']);
      $lines[] = array('type' => 'body', 'text' => 'Inventory ID: ' . $inventory['id']);
      if (!empty($inventory['description'])) {
        $lines[] = array('type' => 'body', 'text' => 'Description: ' . $inventory['description']);
      }

      $lines[] = array('type' => 'section', 'text' => 'Funds');
      if (empty($exportData['funds'])) {
        $lines[] = array('type' => 'body', 'text' => 'No funds recorded.');
      } else {
        foreach ($exportData['funds'] as $fund) {
          $fundLabel = trim(($fund['currency_code'] ?? '') . ' ' . ($fund['amount'] ?? ''));
          $fundName = !empty($fund['name']) ? ' - ' . $fund['name'] : '';
          $lines[] = array('type' => 'body', 'text' => '- ' . $fundLabel . $fundName);
          if (!empty($fund['description'])) {
            $lines[] = array('type' => 'body', 'text' => '  Description: ' . $fund['description']);
          }
        }
      }

      $lines[] = array('type' => 'section', 'text' => 'Resources');
      if (empty($exportData['resources'])) {
        $lines[] = array('type' => 'body', 'text' => 'No resources recorded.');
      } else {
        foreach ($exportData['resources'] as $resource) {
          $lines[] = array('type' => 'body', 'text' => '- ' . $resource['name'] . ' (' . $resource['quantity'] . ' ' . $resource['unit'] . ')');
          if (!empty($resource['description'])) {
            $lines[] = array('type' => 'body', 'text' => '  Description: ' . $resource['description']);
          }

          $tagNames = array();
          foreach ($resource['tags'] as $tag) {
            $tagNames[] = $tag['name'];
          }

          $lines[] = array('type' => 'body', 'text' => '  Tags: ' . (empty($tagNames) ? 'none' : implode(', ', $tagNames)));
        }
      }

      return $lines;
    }

    private function renderInventoryImage($exportData, $format) {
      if (!function_exists('imagecreatetruecolor')) {
        return false;
      }

      $lines = $this->buildInventorySummaryLines($exportData);
      $bodyFont = 3;
      $titleFont = 5;
      $sectionFont = 4;
      $width = 1400;
      $leftMargin = 40;
      $rightMargin = 40;
      $topMargin = 30;
      $bottomMargin = 30;
      $lineGap = 6;
      $bodyLineHeight = imagefontheight($bodyFont) + $lineGap;
      $titleLineHeight = imagefontheight($titleFont) + 14;
      $sectionLineHeight = imagefontheight($sectionFont) + 12;

      $wrappedLines = array();
      foreach ($lines as $line) {
        $text = isset($line['text']) ? (string)$line['text'] : '';
        $chunks = explode("\n", wordwrap($text, 120, "\n", true));
        foreach ($chunks as $chunk) {
          $wrappedLines[] = array(
            'type' => $line['type'],
            'text' => $chunk,
          );
        }
      }

      $height = $topMargin + $bottomMargin + 90;
      foreach ($wrappedLines as $line) {
        if ($line['type'] === 'title') {
          $height += $titleLineHeight + 6;
        } elseif ($line['type'] === 'section') {
          $height += $sectionLineHeight + 8;
        } else {
          $height += $bodyLineHeight;
        }
      }

      $image = imagecreatetruecolor($width, $height);
      if ($image === false) {
        return false;
      }

      $background = imagecolorallocate($image, 246, 248, 252);
      $titleBackground = imagecolorallocate($image, 15, 23, 42);
      $sectionBackground = imagecolorallocate($image, 31, 111, 139);
      $bodyText = imagecolorallocate($image, 17, 24, 39);
      $mutedText = imagecolorallocate($image, 75, 85, 99);
      $white = imagecolorallocate($image, 255, 255, 255);
      $border = imagecolorallocate($image, 209, 213, 219);

      imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $background);
      imagerectangle($image, 0, 0, $width - 1, $height - 1, $border);

      imagefilledrectangle($image, 0, 0, $width - 1, 72, $titleBackground);
      imagestring($image, $titleFont, $leftMargin, 20, 'Inventory export: ' . $exportData['inventory']['name'], $white);
      imagestring($image, $bodyFont, $leftMargin, 46, 'Inventory ID: ' . $exportData['inventory']['id'], $white);

      $currentY = 96;
      foreach ($wrappedLines as $line) {
        if ($line['type'] === 'title') {
          continue;
        }

        if ($line['type'] === 'section') {
          imagefilledrectangle($image, $leftMargin - 12, $currentY - 4, $width - $rightMargin + 12, $currentY + $sectionLineHeight + 2, $sectionBackground);
          imagestring($image, $sectionFont, $leftMargin, $currentY + 2, $line['text'], $white);
          $currentY += $sectionLineHeight + 10;
          continue;
        }

        imagestring($image, $bodyFont, $leftMargin, $currentY, $line['text'], $bodyText);
        $currentY += $bodyLineHeight;
      }

      imagestring($image, $bodyFont, $leftMargin, $height - 20, 'Generated from inventory export', $mutedText);

      ob_start();
      if ($format === 'png') {
        imagepng($image);
      } elseif ($format === 'webp' && function_exists('imagewebp')) {
        imagewebp($image, null, 85);
      } else {
        imagedestroy($image);
        ob_end_clean();
        return false;
      }
      $binary = ob_get_clean();
      imagedestroy($image);
      return $binary;
    }

    public function exportInventoryAsCsv($user_id, $inventory_id) {
      $exportData = $this->getInventoryExportData($user_id, $inventory_id);
      if (!empty($exportData['success']) && $exportData['success'] === false) {
        return $exportData;
      }

      $content = $this->buildInventoryCsvContent($exportData);
      if ($content === false) {
        return array('success' => false, 'message' => 'Failed to build CSV export.', 'code' => 'export_error');
      }

      return array(
        'success' => true,
        'filename' => $this->getExportFilename($inventory_id, 'csv'),
        'mime' => 'text/csv',
        'content' => $content,
      );
    }

    public function exportInventoryAsJson($user_id, $inventory_id) {
      $exportData = $this->getInventoryExportData($user_id, $inventory_id);
      if (!empty($exportData['success']) && $exportData['success'] === false) {
        return $exportData;
      }

      $content = $this->jsonEncodeExport($exportData);
      if ($content === false) {
        return array('success' => false, 'message' => 'Failed to build JSON export.', 'code' => 'export_error');
      }

      return array(
        'success' => true,
        'filename' => $this->getExportFilename($inventory_id, 'json'),
        'mime' => 'application/json',
        'content' => $content,
      );
    }

    public function exportInventory($username, $inventory_id, $type) {
      $normalizedType = strtolower(trim((string)$type));

      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return array('success' => false, 'message' => 'User not found.');
      }
      if ($normalizedType === 'csv') {
        return $this->exportInventoryAsCsv($user['id'], $inventory_id);
      }

      if ($normalizedType === 'json') {
        return $this->exportInventoryAsJson($user['id'], $inventory_id);
      }


      return array('success' => false, 'message' => 'Unsupported export type.');
    }

    public function createInventory($name, $description, $username) {
      $this->inventoryModel->beginTransaction();
      $owner = $this->userModel->findByUsername($username);
      if (!$owner || !isset($owner['id'])) {
        throw new Exception('Owner user not found');
      }

      $owner_user_id = $owner['id'];
      $i_id = $this->inventoryModel->create($name, $description, $owner_user_id);
      if ($i_id === false) {
        throw new Exception('Failed to create inventory');
      }

      $permRes = $this->inventoryPermissionsModel->setUserInventoryPermissions($owner_user_id, $i_id, $this->readPermissionMask | $this->editPermissionMask | $this->updatePermissionMask | $this->deletePermissionMask);
      if ($permRes === false) {
        throw new Exception('Failed to set inventory permissions');
      }
      $this->inventoryModel->commitTransaction();
      return $i_id;
    }

    private function normalizeImportedTag($tag) {
      return array(
        'id' => isset($tag['id']) ? $tag['id'] : null,
        'name' => isset($tag['name']) ? $tag['name'] : null,
        'foreground_color' => isset($tag['foreground_color']) ? $tag['foreground_color'] : (isset($tag['fgcolor']) ? $tag['fgcolor'] : null),
        'background_color' => isset($tag['background_color']) ? $tag['background_color'] : (isset($tag['bgcolor']) ? $tag['bgcolor'] : null),
      );
    }

    private function getImportedTagSignature($tag) {
      $normalizedTag = $this->normalizeImportedTag($tag);
      return strtolower(trim((string)$normalizedTag['name'])) . '|' . strtolower(trim((string)$normalizedTag['foreground_color'])) . '|' . strtolower(trim((string)$normalizedTag['background_color']));
    }

    private function getUniqueImportedInventoryName($owner_user_id, $base_name) {
      $cleanBaseName = trim((string)$base_name);
      if ($cleanBaseName === '') {
        $cleanBaseName = 'Imported inventory';
      }

      $candidate = $cleanBaseName;
      $suffixIndex = 1;
      while ($this->inventoryModel->getInventoryByOwnerAndName($owner_user_id, $candidate)) {
        $suffixIndex += 1;
        $candidate = $cleanBaseName . ' (imported ' . $suffixIndex . ')';
      }

      return $candidate;
    }

    private function parseInventoryImportCsv($file_path) {
      $handle = fopen($file_path, 'r');
      if ($handle === false) {
        return array('success' => false, 'message' => 'Failed to open import file.');
      }

      $headers = fgetcsv($handle);
      if ($headers === false || empty($headers)) {
        fclose($handle);
        return array('success' => false, 'message' => 'Import file is empty.');
      }

      $inventory = null;
      $funds = array();
      $resources = array();

      while (($row = fgetcsv($handle)) !== false) {
        if ($row === array(null) || count(array_filter($row, function ($value) { return $value !== null && $value !== ''; })) === 0) {
          continue;
        }

        $record = array();
        $columnCount = min(count($headers), count($row));
        for ($index = 0; $index < $columnCount; $index++) {
          $record[$headers[$index]] = $row[$index];
        }

        $recordType = isset($record['record_type']) ? strtolower(trim((string)$record['record_type'])) : '';
        if ($recordType === 'inventory' && $inventory === null) {
          $inventory = array(
            'name' => isset($record['inventory_name']) ? $record['inventory_name'] : null,
            'description' => isset($record['inventory_description']) ? $record['inventory_description'] : null,
          );
          continue;
        }

        if ($recordType === 'fund') {
          $funds[] = array(
            'currency_code' => isset($record['currency_code']) ? $record['currency_code'] : null,
            'amount' => isset($record['amount']) ? $record['amount'] : 0,
            'name' => isset($record['name']) ? $record['name'] : null,
            'description' => isset($record['description']) ? $record['description'] : null,
          );
          continue;
        }

        if ($recordType === 'resource') {
          $tags = array();
          if (isset($record['tags_json']) && trim((string)$record['tags_json']) !== '') {
            $decodedTags = json_decode($record['tags_json'], true);
            if (is_array($decodedTags)) {
              $tags = $decodedTags;
            }
          }

          $resources[] = array(
            'name' => isset($record['name']) ? $record['name'] : null,
            'description' => isset($record['description']) ? $record['description'] : null,
            'quantity' => isset($record['quantity']) ? $record['quantity'] : 0,
            'unit' => isset($record['unit']) ? $record['unit'] : null,
            'tags' => $tags,
          );
        }
      }

      fclose($handle);

      if ($inventory === null) {
        return array('success' => false, 'message' => 'Could not find inventory data in CSV file.');
      }

      return array(
        'success' => true,
        'inventory' => $inventory,
        'funds' => $funds,
        'resources' => $resources,
      );
    }

    private function parseInventoryImportJson($file_path) {
      $rawContent = file_get_contents($file_path);
      if ($rawContent === false || trim($rawContent) === '') {
        return array('success' => false, 'message' => 'Import file is empty.');
      }

      $decoded = json_decode($rawContent, true);
      if (!is_array($decoded) || !isset($decoded['inventory'])) {
        return array('success' => false, 'message' => 'Invalid JSON export format.');
      }

      $funds = isset($decoded['funds']) && is_array($decoded['funds']) ? $decoded['funds'] : array();
      $resources = isset($decoded['resources']) && is_array($decoded['resources']) ? $decoded['resources'] : array();

      return array(
        'success' => true,
        'inventory' => $decoded['inventory'],
        'funds' => $funds,
        'resources' => $resources,
      );
    }

    private function parseInventoryImportFile($uploaded_file) {
      if (!isset($uploaded_file['tmp_name']) || !is_string($uploaded_file['tmp_name']) || $uploaded_file['tmp_name'] === '') {
        return array('success' => false, 'message' => 'No import file was uploaded.');
      }

      $filePath = $uploaded_file['tmp_name'];
      $fileName = isset($uploaded_file['name']) ? strtolower((string)$uploaded_file['name']) : '';
      $contentPrefix = '';
      $fileHandle = fopen($filePath, 'r');
      if ($fileHandle !== false) {
        $contentPrefix = trim((string)fread($fileHandle, 32));
        fclose($fileHandle);
      }

      if (preg_match('/\.json$/', $fileName) || (strlen($contentPrefix) > 0 && ($contentPrefix[0] === '{' || $contentPrefix[0] === '['))) {
        return $this->parseInventoryImportJson($filePath);
      }

      return $this->parseInventoryImportCsv($filePath);
    }

    public function importInventory($username, $uploaded_file) {
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return array('success' => false, 'message' => 'User not found.');
      }

      $parsedImport = $this->parseInventoryImportFile($uploaded_file);
      if (!empty($parsedImport['success']) && $parsedImport['success'] === false) {
        return $parsedImport;
      }

      $inventoryData = $parsedImport['inventory'];
      $fundsData = isset($parsedImport['funds']) && is_array($parsedImport['funds']) ? $parsedImport['funds'] : array();
      $resourcesData = isset($parsedImport['resources']) && is_array($parsedImport['resources']) ? $parsedImport['resources'] : array();

      $inventoryName = $this->getUniqueImportedInventoryName($user['id'], isset($inventoryData['name']) ? $inventoryData['name'] : 'Imported inventory');
      $inventoryDescription = isset($inventoryData['description']) ? $inventoryData['description'] : null;

      $this->inventoryModel->beginTransaction();
      try {
        $newInventoryId = $this->inventoryModel->create($inventoryName, $inventoryDescription, $user['id']);
        if ($newInventoryId === false) {
          throw new Exception('Failed to create inventory.');
        }

        $fullPermissionMask = $this->readPermissionMask | $this->insertPermissionMask | $this->updatePermissionMask | $this->deletePermissionMask;
        if ($this->inventoryPermissionsModel->setUserInventoryPermissions($user['id'], $newInventoryId, $fullPermissionMask) === false) {
          throw new Exception('Failed to set inventory permissions.');
        }

        $tagBySignature = array();
        $tagById = array();

        foreach ($resourcesData as $resourceData) {
          $resourceTags = isset($resourceData['tags']) && is_array($resourceData['tags']) ? $resourceData['tags'] : array();
          foreach ($resourceTags as $tagData) {
            $normalizedTag = $this->normalizeImportedTag($tagData);
            $tagSignature = $this->getImportedTagSignature($normalizedTag);
            if (isset($tagBySignature[$tagSignature])) {
              continue;
            }

            if (empty($normalizedTag['name']) || empty($normalizedTag['foreground_color']) || empty($normalizedTag['background_color'])) {
              throw new Exception('Invalid tag data in import file.');
            }

            $newTagId = $this->resurseModel->createTag($newInventoryId, $normalizedTag['name'], $normalizedTag['foreground_color'], $normalizedTag['background_color']);
            if ($newTagId === false) {
              throw new Exception('Failed to create tag.');
            }

            $tagBySignature[$tagSignature] = $newTagId;
            if (!empty($normalizedTag['id'])) {
              $tagById[(string)$normalizedTag['id']] = $newTagId;
            }
          }
        }

        foreach ($fundsData as $fundData) {
          if (!isset($fundData['currency_code']) || trim((string)$fundData['currency_code']) === '') {
            throw new Exception('Invalid fund data in import file.');
          }

          $amount = isset($fundData['amount']) ? $fundData['amount'] : 0;
          $fundResult = $this->fonduriModel->setFonduri(
            $newInventoryId,
            $amount,
            $fundData['currency_code'],
            isset($fundData['name']) ? $fundData['name'] : null,
            isset($fundData['description']) ? $fundData['description'] : null
          );
          if ($fundResult === false) {
            throw new Exception('Failed to create fund entry.');
          }
        }

        foreach ($resourcesData as $resourceData) {
          if (!isset($resourceData['name']) || trim((string)$resourceData['name']) === '') {
            throw new Exception('Invalid resource data in import file.');
          }
          if (!isset($resourceData['unit']) || trim((string)$resourceData['unit']) === '') {
            throw new Exception('Invalid resource unit in import file.');
          }

          $resourceId = $this->resurseModel->create(
            $resourceData['name'],
            isset($resourceData['description']) ? $resourceData['description'] : null,
            isset($resourceData['quantity']) ? $resourceData['quantity'] : 0,
            $resourceData['unit'],
            $newInventoryId
          );
          if ($resourceId === false) {
            throw new Exception('Failed to create resource.');
          }

          $seenTags = array();
          $resourceTags = isset($resourceData['tags']) && is_array($resourceData['tags']) ? $resourceData['tags'] : array();
          foreach ($resourceTags as $tagData) {
            $normalizedTag = $this->normalizeImportedTag($tagData);
            $tagSignature = $this->getImportedTagSignature($normalizedTag);

            if (isset($seenTags[$tagSignature])) {
              continue;
            }
            $seenTags[$tagSignature] = true;

            $tagId = null;
            if (!empty($normalizedTag['id']) && isset($tagById[(string)$normalizedTag['id']])) {
              $tagId = $tagById[(string)$normalizedTag['id']];
            } elseif (isset($tagBySignature[$tagSignature])) {
              $tagId = $tagBySignature[$tagSignature];
            }

            if ($tagId === null) {
              throw new Exception('Tag mapping failed during import.');
            }

            if ($this->resurseModel->addTagToResource($resourceId, $tagId) === false) {
              throw new Exception('Failed to attach tag to resource.');
            }
          }
        }

        $this->inventoryModel->commitTransaction();
        return array('success' => true, 'message' => 'Inventory imported.', 'inventory_id' => $newInventoryId);
      } catch (Exception $e) {
        $this->inventoryModel->rollbackTransaction();
        return array('success' => false, 'message' => $e->getMessage(), 'code' => 'import_error');
      }
    }

    public function updateInventory($username, $id, $name, $description) {
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }
      if (!$this->canEdit($user['id'], $id)) {
        return $this->accessDenied();
      }
      return $this->inventoryModel->update($id, $name, $description);
    }
    public function updateInventoryName($username, $id, $name) {
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }
      if (!$this->canEdit($user['id'], $id)) {
        return $this->accessDenied();
      }
      return $this->inventoryModel->update($id, $name, null);
    }
    public function deleteInventory($username, $inventory_id){
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }
      if(!$this->canDelete($user['id'], $inventory_id)){
          throw new Exception("Permission Denied");
      }

      $inventory = $this->inventoryModel->getInventoryById($inventory_id);
      if (!$inventory) {
        return $this->notFound('Inventory not found.');
      }

      if ($this->sendEmailToAllAssoc($inventory_id, "Inventory Deleted: " . $inventory['name'], "The inventory with ID " . $inventory_id . " has been deleted by user " . $user['username'] . ".")) {
        // merge bine
      } else {
        return array('success' => false, 'message' => 'Failed to send deletion notice emails.');
      }
      if ($this->inventoryModel->delete($inventory_id)) {
        return array('success' => true, 'message' => 'Inventory deleted.');
      }
      return array('success' => false, 'message' => 'Failed to delete inventory.');
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
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }
      $fonduri = $this->fonduriModel->getFonduriByInventoryIdAndCurrency($inventory_id, $currency_code);
      if (!$this->canEdit($user['id'], $inventory_id)) {
        return $this->accessDenied();
      }
      $res = $this->fonduriModel->addFonduri($inventory_id, $amount, $currency_code, $name, $description);
      if ($res === false) {
        return array('success' => false, 'message' => 'Failed to add funds.');
      }
      return array('success' => true, 'message' => 'Funds added.');
    }

    public function setFonduri($username, $inventory_id, $amount, $currency_code, $name = null, $description = null) {
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }
      $fonduri = $this->fonduriModel->getFonduriByInventoryIdAndCurrency($inventory_id, $currency_code);
      if ($fonduri) {
        if (!$this->canUpdate($user['id'], $inventory_id)) {
          return $this->accessDenied();
        }
      } else {
        if (!$this->canEdit($user['id'], $inventory_id)) {
          return $this->accessDenied();
        }
      }
      $res = $this->fonduriModel->setFonduri($inventory_id, $amount, $currency_code, $name, $description);
      if ($res === false) {
        return array('success' => false, 'message' => 'Failed to set funds.');
      }
      return array('success' => true, 'message' => 'Funds set.');
    }

    public function createFonduri($username, $inventory_id, $currency_code, $name = null, $description = null) {
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }
      if (!$this->canEdit($user['id'], $inventory_id)) {
        return $this->accessDenied();
      }
      $res = $this->fonduriModel->create($inventory_id, $currency_code, $name, $description);
      if ($res === false) {
        return array('success' => false, 'message' => 'Failed to create fund.');
      }
      return array('success' => true, 'message' => 'Fund created.');
    }

    public function deleteFonduri($username, $inventory_id, $currency_id) {
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }
      if (!$this->canDelete($user['id'], $inventory_id)) {
        return $this->accessDenied();
      }

      $fonduri = $this->getFonduriByInventoryId($inventory_id);
      $target = null;
      foreach ($fonduri as $f) {
        if (isset($f['id']) && (string)$f['id'] === (string)$currency_id) {
          $target = $f;
          break;
        }
      }
      if (!$target) {
        return $this->notFound('Fund not found.');
      }

      $res = $this->fonduriModel->deleteFonduri($inventory_id, $currency_id);
      if ($res === false) {
        return array('success' => false, 'message' => 'Failed to delete fund.');
      }

      $this->sendEmailToOwner($inventory_id, "Fund Deleted: " . ($target['name'] ?? $target['currency_code'] ?? ''), "The fund with ID " . $currency_id . " (" . ($target['name'] ?? $target['currency_code'] ?? '') . ") has been deleted from inventory " . ($this->inventoryModel->getInventoryById($inventory_id)['name'] ?? $inventory_id) . " by user " . $user['username'] . ".");

      return array('success' => true, 'message' => 'Fund deleted.');
    }

    public function createResurse($username, $name, $description, $quantity, $unit, $inventory_id) {
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }
      if (!$this->canEdit($user['id'], $inventory_id)) {
        return $this->accessDenied();
      }
      $res = $this->resourceService->addResource($inventory_id, $name, $unit, $description);
      if ($res === false) {
        return array('success' => false, 'message' => 'Failed to create resource.');
      }
      return array('success' => true, 'message' => 'Resource created.', 'id' => $res);
    }

    public function addResource($username, $inventory_id, $name, $unit, $description) {
      return $this->createResurse($username, $name, $description, 0, $unit, $inventory_id);
    }

    public function removeResource($username, $inventory_id, $resource_id) {
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }
      if (!$this->canDelete($user['id'], $inventory_id)) {
        return $this->accessDenied();
      }
      $resource = $this->resurseModel->getResurseById($resource_id);
      $res = $this->resourceService->removeResource($inventory_id, $resource_id);
      if ($res === false) {
        return array('success' => false, 'message' => 'Failed to delete resource.');
      }
      $inventory = $this->inventoryModel->getInventoryById($inventory_id);

      $this->sendEmailToOwner($inventory_id, "Resource Deleted: " . $resource['name'], "The resource with ID " . $resource_id . " has been deleted from inventory " . $inventory['name'] . " by user " . $user['username'] . ".");
      return array('success' => true, 'message' => 'Resource deleted.');
    }

    public function addTag($username, $inventory_id, $name, $bgColor, $fgColor) {
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }
      if (!$this->canEdit($user['id'], $inventory_id)) {
        return $this->accessDenied();
      }
      $res = $this->resourceService->addTag($inventory_id, $name, $bgColor, $fgColor);
      if ($res === false) {
        return array('success' => false, 'message' => 'Failed to create tag.');
      }
      return array('success' => true, 'message' => 'Tag created.', 'id' => $res);
    }

    public function moveResurse($username, $resource_id, $new_inventory_id) {
      $resource = $this->resurseModel->getResurseById($resource_id);
      if (!$resource) {
        return $this->notFound('Resource not found.');
      }
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }
      if (!$this->canDelete($user['id'], $resource['inventory_id']) || !$this->canEdit($user['id'], $new_inventory_id)) {
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
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }
      if (!$this->canEdit($user['id'], $resource['inventory_id'])) {
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
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }
      if (!$this->canEdit($user['id'], $resource['inventory_id'])) {
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
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }
      if (!$this->canRead($user['id'], $resource['inventory_id'])) return $this->accessDenied();
      try{
          return $this->resurseModel->getTagsForResource($resource_id);
      }catch(Exception $e){
          return array();
      }
      return array();
    }

    public function removeResourceById($username, $resource_id) {
      $resource = $this->resurseModel->getResurseById($resource_id);
      if (!$resource) {
        return $this->notFound('Resource not found.');
      }
      return $this->removeResource($username, $resource['inventory_id'], $resource_id);
    }

    public function getResourcesByTags($username, $inventory_id, $tag_ids) {
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }
      if (!$this->canRead($user['id'], $inventory_id)) return $this->accessDenied();
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

    public function getAccessManagementData($username, $inventory_id) {
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }

      $inventory = $this->inventoryModel->getInventoryById($inventory_id);
      if (!$inventory) {
        return $this->notFound('Inventory not found.');
      }

      // Only owner can manage access
      if ($inventory['owner_id'] != $user['id']) {
        return $this->accessDenied();
      }

      $associates = $this->inventoryPermissionsModel->getAllAssociatedUsers($inventory_id);
      return array(
        'success' => true,
        'inventory' => $inventory,
        'associates' => $associates,
        'permission_masks' => array(
          'read' => $this->readPermissionMask,
          'edit' => $this->editPermissionMask,
          'update' => $this->updatePermissionMask,
          'delete' => $this->deletePermissionMask,
        ),
      );
    }

    public function updateUserInventoryAccess($username, $inventory_id, $target_user_id, $new_permissions) {
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }

      $inventory = $this->inventoryModel->getInventoryById($inventory_id);
      if (!$inventory) {
        return $this->notFound('Inventory not found.');
      }

      // Only owner can manage access
      if ($inventory['owner_id'] != $user['id']) {
        return $this->accessDenied();
      }

      $target_user = $this->userModel->findById($target_user_id);
      if (!$target_user) {
        return $this->notFound('Target user not found.');
      }

      $new_permissions = intval($new_permissions);
      
      // Begin transaction
      $this->inventoryModel->beginTransaction();
      
      $res = $this->inventoryPermissionsModel->setUserInventoryPermissions($target_user_id, $inventory_id, $new_permissions);
      if ($res === false) {
        $this->inventoryModel->rollbackTransaction();
        return array('success' => false, 'message' => 'Failed to update permissions.');
      }

      // Send email to target user
      $permissionText = $this->getPermissionText($new_permissions);
      $emailBody = "Your access permissions for inventory \"" . $inventory['name'] . "\" have been updated by " . $user['username'] . ".\n\n";
      $emailBody .= "New permissions: " . $permissionText . "\n\n";
      $emailBody .= "Inventory ID: " . $inventory_id;

      $emailSent = true;
      if (isset($target_user['email'])) {
        $emailSent = $this->mailingService->send_email($target_user['email'], "Access Updated: " . $inventory['name'], $emailBody);
      }

      // Commit transaction
      $this->inventoryModel->commitTransaction();

      return array('success' => true, 'message' => 'Permissions updated and email sent.');
    }

    public function removeUserInventoryAccess($username, $inventory_id, $target_user_id) {
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }

      $inventory = $this->inventoryModel->getInventoryById($inventory_id);
      if (!$inventory) {
        return $this->notFound('Inventory not found.');
      }

      // Only owner can manage access
      if ($inventory['owner_id'] != $user['id']) {
        return $this->accessDenied();
      }

      $target_user = $this->userModel->findById($target_user_id);
      if (!$target_user) {
        return $this->notFound('Target user not found.');
      }

      // Begin transaction
      $this->inventoryModel->beginTransaction();
      
      $res = $this->inventoryPermissionsModel->removeUserInventoryPermission($target_user_id, $inventory_id);
      if (!$res) {
        $this->inventoryModel->rollbackTransaction();
        return array('success' => false, 'message' => 'Failed to remove user access.');
      }

      // Send email to target user
      $emailBody = "Your access to inventory \"" . $inventory['name'] . "\" has been revoked by " . $user['username'] . ".\n\n";
      $emailBody .= "Inventory ID: " . $inventory_id;

      if (isset($target_user['email'])) {
        $this->mailingService->send_email($target_user['email'], "Access Revoked: " . $inventory['name'], $emailBody);
      }

      // Commit transaction
      $this->inventoryModel->commitTransaction();

      return array('success' => true, 'message' => 'User access removed.');
    }

    public function addUserInventoryAccess($username, $inventory_id, $target_username, $initial_permissions) {
      $user = $this->userModel->findByUsername($username);
      if (!$user || !isset($user['id'])) {
        return $this->notFound('User not found.');
      }

      $inventory = $this->inventoryModel->getInventoryById($inventory_id);
      if (!$inventory) {
        return $this->notFound('Inventory not found.');
      }

      // Only owner can manage access
      if ($inventory['owner_id'] != $user['id']) {
        return $this->accessDenied();
      }

      $target_user = $this->userModel->findByUsername($target_username);
      if (!$target_user) {
        return $this->notFound('Target user not found.');
      }

      $initial_permissions = intval($initial_permissions);

      // Begin transaction
      $this->inventoryModel->beginTransaction();
      
      $res = $this->inventoryPermissionsModel->setUserInventoryPermissions($target_user['id'], $inventory_id, $initial_permissions);
      if ($res === false) {
        $this->inventoryModel->rollbackTransaction();
        return array('success' => false, 'message' => 'Failed to add user access.');
      }

      // Send email to target user
      $permissionText = $this->getPermissionText($initial_permissions);
      $emailBody = "You have been granted access to inventory \"" . $inventory['name'] . "\" by " . $user['username'] . ".\n\n";
      $emailBody .= "Your permissions: " . $permissionText . "\n\n";
      $emailBody .= "Inventory ID: " . $inventory_id;

      if (isset($target_user['email'])) {
        $this->mailingService->send_email($target_user['email'], "Access Granted: " . $inventory['name'], $emailBody);
      }

      // Commit transaction
      $this->inventoryModel->commitTransaction();

      return array('success' => true, 'message' => 'User access added.');
    }

    private function getPermissionText($permissions_mask) {
      $perms = array();
      if (($permissions_mask & $this->readPermissionMask) === $this->readPermissionMask) {
        $perms[] = 'Read';
      }
      if (($permissions_mask & $this->editPermissionMask) === $this->editPermissionMask) {
        $perms[] = 'Edit';
      }
      if (($permissions_mask & $this->updatePermissionMask) === $this->updatePermissionMask) {
        $perms[] = 'Update';
      }
      if (($permissions_mask & $this->deletePermissionMask) === $this->deletePermissionMask) {
        $perms[] = 'Delete';
      }
      return empty($perms) ? 'None' : implode(', ', $perms);
    }



  }
?>