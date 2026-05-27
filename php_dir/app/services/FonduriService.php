<?php
require_once __DIR__ . "/../models/FonduriModel.php";

class FonduriService {
	private $model;

	public function __construct($connection) {
		$this->model = new FonduriModel($connection);
	}

	public function getFonduriByInventoryId($inventory_id) {
		$res = $this->model->getFonduriByInventoryId($inventory_id);
		return $res === null ? array() : $res;
	}

	public function getFonduriByInventoryIdAndCurrency($inventory_id, $currency_code) {
		$res = $this->model->getFonduriByInventoryIdAndCurrency($inventory_id, $currency_code);
		return $res === null ? null : $res;
	}

	public function addFonduri($inventory_id, $amount, $currency_code) {
		$res = $this->model->addFonduri($inventory_id, $amount, $currency_code);
		if ($res === false) {
			return array('success' => false, 'message' => 'Failed to add funds.');
		}
		return array('success' => true, 'message' => 'Funds added.');
	}

	public function setFonduri($inventory_id, $amount, $currency_code) {
		$res = $this->model->setFonduri($inventory_id, $amount, $currency_code);
		if ($res === false) {
			return array('success' => false, 'message' => 'Failed to set funds.');
		}
		return array('success' => true, 'message' => 'Funds set.');
	}
}

?>
