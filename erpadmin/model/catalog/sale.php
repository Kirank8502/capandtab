<?php
namespace Opencart\Admin\Model\Catalog;
class Sale extends \Opencart\System\Engine\Model {
	public function addSale($data) {

		$product_ids = isset($data['product_id']) ? (is_array($data['product_id']) ? (implode(',', (!empty($data['product_id']) ? $data['product_id'] : 0))) : 0):0;

		foreach($data['product_id'] as $key => $value){
			if(str_starts_with($value,'acc_')){
				$sql = $this->db->query("SELECT qty FROM " . DB_PREFIX . "accessories WHERE accessories_id = ".str_replace("acc_","",$value)."");
				$qty = $sql->row['qty']+$data['qty'];
				$this->db->query("UPDATE " . DB_PREFIX . "accessories SET qty = ".$qty." WHERE accessories_id = ".str_replace("acc_","",$value)."");
			}elseif(str_starts_with($value,'fitts_')){
				$sql = $this->db->query("SELECT qty FROM " . DB_PREFIX . "fittings WHERE fittings_id = ".str_replace("fitts_","",$value)."");
				$qty = $sql->row['qty']+$data['qty'];
				$this->db->query("UPDATE " . DB_PREFIX . "fittings SET qty = ".$qty." WHERE fittings_id = ".str_replace("fitts_","",$value)."");
			}elseif(str_starts_with($value,'prod_')){
				$sql = $this->db->query("SELECT qty FROM " . DB_PREFIX . "product WHERE product_id = ".str_replace("prod_","",$value)."");
				$qty = $sql->row['qty']+$data['qty'];
				$this->db->query("UPDATE " . DB_PREFIX . "product SET qty = ".$qty." WHERE product_id = ".str_replace("prod_","",$value)."");
			}elseif(str_starts_with($value,'pow_')){
				$sql = $this->db->query("SELECT qty FROM " . DB_PREFIX . "powder WHERE powder_id = ".str_replace("pow_","",$value)."");
				$qty = $sql->row['qty']+$data['qty'];
				$this->db->query("UPDATE " . DB_PREFIX . "powder SET qty = ".$qty." WHERE powder_id = ".str_replace("pow_","",$value)."");
			}elseif(str_starts_with($value,'mb_')){
				$sql = $this->db->query("SELECT qty FROM " . DB_PREFIX . "master_batch WHERE master_batch_id = ".str_replace("mb_","",$value)."");
				$qty = $sql->row['qty']+$data['qty'];
				$this->db->query("UPDATE " . DB_PREFIX . "master_batch SET qty = ".$qty." WHERE master_batch_id = ".str_replace("mb_","",$value)."");
			}elseif(str_starts_with($value,'pig_')){
				$sql = $this->db->query("SELECT qty FROM " . DB_PREFIX . "pigment WHERE pigment_id = ".str_replace("pig_","",$value)."");
				$qty = $sql->row['qty']+$data['qty'];
				$this->db->query("UPDATE " . DB_PREFIX . "pigment SET qty = ".$qty." WHERE pigment_id = ".str_replace("pig_","",$value)."");
			}elseif(str_starts_with($value,'dies_')){
				
			}
		}

		$this->db->query("INSERT INTO " . DB_PREFIX . "sale SET `product_id` = '" . $this->db->escape($product_ids) . "', file = '".(!empty($data['filename']) ? $this->db->escape($data['filename']) : '')."', product_name = '".(!empty($data['product_name']) ? $this->db->escape($data['product_name']) : '')."', vendor_id = '" . (!empty($data['vendor_id']) ? $this->db->escape($data['vendor_id']) : '') . "', orders_id = '" . (!empty($data['orders_id']) ? $this->db->escape($data['orders_id']) : 0) . "', `rate` = '" . (!empty($data['rate']) ? $data['rate'] : 0.00) . "', `amount` = '" . (!empty($data['amount']) ? $data['amount'] : 0.00) . "', `labor_cost` = '" . (!empty($data['labor_cost']) ? $data['labor_cost'] : 0.00) . "', `packaging_cost` = '" . (!empty($data['packaging_cost']) ? $data['packaging_cost'] : 0.00) . "', `transportation_cost` = '" . (!empty($data['transportation_cost']) ? $data['transportation_cost'] : 0.00) . "', qty = '" . (!empty($data['qty']) ? (int)$data['qty'] : 0) . "', gst = '" . (!empty($data['gst']) ? (int)$data['gst'] : 0) . "', gst_status = '" . (!empty($data['gst_status']) ? (int)$data['gst_status'] : 0) . "', sale_date = date('" . $data['sale_date'] . "'), payment_date = '" . (!empty($data['payment_date']) ? $this->db->escape($data['payment_date']) : 0) . "', date_added = NOW()");

		$sale_id = $this->db->getLastId();

		return true;
		
		$this->cache->delete('sale');
	}
	
	public function editSale($sale_id, $data) {

		$product_ids = isset($data['product_id']) ? (is_array($data['product_id']) ? (implode(',', (!empty($data['product_id']) ? $data['product_id'] : 0))) : 0):0;

      	$this->db->query("UPDATE " . DB_PREFIX . "sale SET `product_id` = '" . $this->db->escape($product_ids) . "', file = '".(!empty($data['filename']) ? $this->db->escape($data['filename']) : '')."', product_name = '".(!empty($data['product_name']) ? $this->db->escape($data['product_name']) : '')."', vendor_id = '" . (!empty($data['vendor_id']) ? $this->db->escape($data['vendor_id']) : '') . "', orders_id = '" . (!empty($data['orders_id']) ? $this->db->escape($data['orders_id']) : 0) . "', `rate` = '" . (!empty($data['rate']) ? $data['rate'] : 0.00) . "', `amount` = '" . (!empty($data['amount']) ? $data['amount'] : 0.00) . "', `labor_cost` = '" . (!empty($data['labor_cost']) ? $data['labor_cost'] : 0.00) . "', `packaging_cost` = '" . (!empty($data['packaging_cost']) ? $data['packaging_cost'] : 0.00) . "', `transportation_cost` = '" . (!empty($data['transportation_cost']) ? $data['transportation_cost'] : 0.00) . "', qty = '" . (!empty($data['qty']) ? (int)$data['qty'] : 0) . "', gst = '" . (!empty($data['gst']) ? (int)$data['gst'] : 0) . "', gst_status = '" . (!empty($data['gst_status']) ? (int)$data['gst_status'] : 0) . "', sale_date = date('" . $data['sale_date'] . "'), payment_date = '" . (!empty($data['payment_date']) ? $this->db->escape($data['payment_date']) : 0) . "' WHERE `sale_id` = ".$sale_id);

		return true;
		
		$this->cache->delete('sale');
	}
	
	public function deleteSale($sale_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "sale WHERE sale_id= '" . (int)$sale_id. "'");			
		$this->cache->delete('order');
	}	
	
	public function getSale($sale_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sale WHERE sale_id= '" . (int)$sale_id. "'");
		
		return $query->row;
	}

	public function getOrderDetail($purchase_id) {
		$query = $this->db->query("SELECT od.*,o.* FROM " . DB_PREFIX . "purchase o LEFT JOIN " . DB_PREFIX . "order_details od ON o.purchase_id = od.purchase_id  WHERE o.purchase_id= '" . (int)$purchase_id. "'");
		
		return $query->row;
	}

	public function getProducts() {
		$sql = "SELECT product_id,name,total_price,product_cost FROM " . DB_PREFIX . "product WHERE status = '1'";

		$query = $this->db->query($sql);
	
		return $query->rows;
	}

	public function getProduct($data_id) {
		$sql = "SELECT product_id,name,image FROM " . DB_PREFIX . "product WHERE product_id = ".$data_id."";

		$query = $this->db->query($sql);
	
		return $query->row;
	}

	public function getClients($data = array()) {
		$sql = "SELECT client_id,name,address FROM " . DB_PREFIX . "client WHERE status = '1' ";

		if(!empty($data['client_id'])){
			$sql .= " AND client_id = ".$data['client_id']." ";
		}

		$query = $this->db->query($sql);
	
		return $query->rows;
	}

	public function getClient($client_id) {
		$sql = "SELECT name,address FROM " . DB_PREFIX . "client WHERE client_id = '".$client_id."' ";

		$query = $this->db->query($sql);
	
		return $query->row;
	}

	public function getPowders($data = array()) {
		$sql = "SELECT powder_id,name FROM " . DB_PREFIX . "powder WHERE status = '1'";

		$query = $this->db->query($sql);
	
		return $query->rows;
	}

	public function getPowder($powder_id) {
		$sql = "SELECT name,weight FROM " . DB_PREFIX . "powder WHERE powder_id = '".$powder_id."' ";

		$query = $this->db->query($sql);
	
		return $query->row;
	}

	public function getColours($data = array()) {
		$sql = "SELECT colour_id,name FROM " . DB_PREFIX . "colour WHERE status = '1'";

		$query = $this->db->query($sql);
	
		return $query->rows;
	}

	public function getColour($data_id) {
		$sql = "SELECT colour_id,name FROM " . DB_PREFIX . "colour WHERE colour_id = ".$data_id."";

		$query = $this->db->query($sql);
	
		return $query->row;
	}

	public function getMoulders($data = array()) {
		$sql = "SELECT moulder_id,name,address FROM " . DB_PREFIX . "moulder WHERE status = '1' ";

		if(!empty($data['moulder_id'])){
			$sql .= " AND moulder_id = ".$data['moulder_id']." ";
		}

		$query = $this->db->query($sql);
	
		return $query->rows;
	}

	public function getMoulder($moulder_id) {
		$sql = "SELECT name,address,email FROM " . DB_PREFIX . "moulder WHERE moulder_id = '".$moulder_id."' ";

		$query = $this->db->query($sql);
	
		return $query->row;
	}

	public function getAccessories($data = array()) {
		$sql = "SELECT accessories_id,name,qty,price FROM " . DB_PREFIX . "accessories WHERE status = '1'";

		$query = $this->db->query($sql);
	
		return $query->rows;
	}

	public function getAccessoryFittings($data) {
		if (!empty($data) && $data > 0 && str_starts_with($data,'acc_')){
			$acc_id = str_replace("acc_","",$data);
			$sql = "SELECT accessories_id,name,qty,image,weight FROM " . DB_PREFIX . "accessories WHERE accessories_id = ".$acc_id."";
		} elseif (!empty($data) && $data > 0 && str_starts_with($data,'fitts_')) {
			$fitts = str_replace("fitts_","",$data);
			$sql = "SELECT fittings_id,name,qty,image,weight FROM " . DB_PREFIX . "fittings WHERE fittings_id = ".$fitts."";
		}

		$query = $this->db->query($sql);
	
		return $query->row;
	}


	public function getMasterBatchs($data = array()) {
		$sql = "SELECT master_batch_id,name FROM " . DB_PREFIX . "master_batch WHERE status = '1'";

		$query = $this->db->query($sql);
	
		return $query->rows;
	}

	public function getPigments($data = array()) {
		$sql = "SELECT pigment_id,name FROM " . DB_PREFIX . "pigment WHERE status = '1'";

		$query = $this->db->query($sql);
	
		return $query->rows;
	}

	public function getDies($data = array()) {
		$sql = "SELECT die_id,name FROM " . DB_PREFIX . "die";

		$query = $this->db->query($sql);
	
		return $query->rows;
	}

	public function getDietype($data_id) {
		$sql = "SELECT die_id,name,type FROM " . DB_PREFIX . "die";

		$query = $this->db->query($sql);
	
		return $query->row;
	}

	public function getFittings($data = array()) {
		$sql = "SELECT fittings_id,name,qty,price FROM " . DB_PREFIX . "fittings WHERE 1 ";
		
		if(!empty($data['fittings_id'])){
			$sql .= " AND fittings_id = ".$data['fittings_id']."";
		}

		$query = $this->db->query($sql);
	
		return $query->rows;
	}

	public function getFittingss($data) {
		$sql = "SELECT fittings_id,name,qty,image,weight FROM " . DB_PREFIX . "fittings WHERE fittings_id in (".$data.")";

		$query = $this->db->query($sql);
	
		return $query->rows;
	}
	
	public function getSales($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "sale WHERE 1";
		
		$sql .= " ORDER BY `sale_id`";	
		
		$sql .= " DESC";
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}					

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	
		
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}				

		$query = $this->db->query($sql);
	
		return $query->rows;
	}
	
	public function getTotalSales() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "sale");
		
		return $query->row['total'];
	}

	public function getOrders($order_id = 0) {
		$sql = "SELECT orders_id,po_no,client_id,moulder_id,acc_fitts_id,product_id,order_type FROM " . DB_PREFIX . "orders WHERE 1";
		if($order_id > 0){
			$sql .= " AND orders_id = ".$order_id." ";
		}
		$query = $this->db->query($sql);
	
		return $query->rows;
	}

	public function getOrder($orders_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "orders WHERE orders_id = ".$orders_id."";

		$query = $this->db->query($sql);
	
		return $query->row;
	}

	public function getOrderDetails($data=array()) {

		$sql = "SELECT `status`, COUNT(sale_id) AS total FROM ".DB_PREFIX."sale WHERE 1 ";

		if (!empty($data['filter_date_from'])) {
			$sql .= " AND DATE_FORMAT(order_date,'%Y-%m-%d') >= '" . $this->db->escape($data['filter_date_from']) . "'";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE_FORMAT(order_date,'%Y-%m-%d') <= '" . $this->db->escape($data['filter_date_to']) . "'";
		}

		$sql .= " GROUP BY `status` ";

		$query = $this->db->query($sql);

		if($query->num_rows) {
			return $query->rows;
		} else {
			return 0;
		}
	}

	public function getAccessoriesDetails($data = array()) {
		$sql = "SELECT name,weight FROM " . DB_PREFIX . "accessories WHERE status = '1' ";

		$acc_id = ((!empty($data['accessories_id']) && $data['accessories_id'] > 0 && str_starts_with($data['accessories_id'],'acc_')) ? str_replace("acc_","",$data['accessories_id']) : 0);

		$sql .= " AND accessories_id = ".$acc_id."";

		$query = $this->db->query($sql);
	
		return $query->row;
	}

	public function getOrderId() {
		$query = $this->db->query("SELECT MAX(po_no) AS latest_order_id FROM " . DB_PREFIX . "sale");

		return $query->row;
	}

	public function getFittingsDetails() {
		$query = $this->db->query("SELECT SUM(weight) AS total_weight FROM " . DB_PREFIX . "fittings");

		return $query->row;
	}

	public function checkDie($die_id=0){
		$sql = "SELECT location,moulder_id FROM " . DB_PREFIX . "die WHERE die_id LIKE '%".$die_id."%'";

		$query = $this->db->query($sql);
		return $query->row;
	}
}
?>