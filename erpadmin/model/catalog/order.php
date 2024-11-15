<?php
namespace Opencart\Admin\Model\Catalog;
class Order extends \Opencart\System\Engine\Model {
	public function addOrder($data) {
		// if($data['order_type'] == 0){
			// $sql = $this->db->query("SELECT qty FROM " . DB_PREFIX . "accessories WHERE accessories_id LIKE '%".$data['accessories_id']."%'");
			// if($sql->row['qty'] >= $data['req_qty']){
				// $cal = $sql->row['qty'] + $data['req_qty'];
				// $sql_update = $this->db->query("UPDATE " . DB_PREFIX . "accessories SET `qty` = ".(int)$cal." WHERE accessories_id LIKE '%".$data['accessories_id']."%'");
			// }else{
				// return false;
			// }
		// }	

		if($data['order_type'] == 0 && $data['powder_id'] != 0){
			$sql = $this->db->query("SELECT qty FROM " . DB_PREFIX . "powder WHERE powder_id LIKE '%".$data['powder_id']."%'");
			if($sql->row['qty'] >= $data['bags']){
				$cal = $sql->row['qty'] - $data['bags'];
				$sql_update = $this->db->query("UPDATE " . DB_PREFIX . "powder SET `qty` = ".(int)$cal." WHERE powder_id LIKE '%".$data['powder_id']."%'");
			}else{
				return false;
			}
		}
		
		// if($data['order_type'] == 1){
			// $sql = $this->db->query("SELECT qty FROM " . DB_PREFIX . "fittings WHERE fittings_id LIKE '%".$data['fittings_id']."%'");
			// if($sql->row['qty'] >= $data['qty']){
				// $cal = $sql->row['qty'] - $data['qty'];
				// $sql_update = $this->db->query("UPDATE " . DB_PREFIX . "fittings SET `qty` = ".(int)$cal." ");
			// }else{
			// 	return false;
			// }
		// }

		$acc_fitts_id = isset($data['acc_fitts_id']) ? (is_array($data['acc_fitts_id']) ? (implode(',', (!empty($data['acc_fitts_id']) ? $data['acc_fitts_id'] : 0))) : 0):0;

		$fittings_id = isset($data['fittings_ids']) ? (is_array($data['fittings_ids']) ? (implode(',', (!empty($data['fittings_ids']) ? $data['fittings_ids'] : 0))) : 0):0;

		$this->db->query("INSERT INTO " . DB_PREFIX . "orders SET `po_no` = '" . (!empty($data['po_no']) ? $data['po_no'] : 0) . "', `product_id` = '" . (!empty($data['product_id']) ? (int)$data['product_id'] : 0) . "', order_type = '" . (int)$data['order_type'] . "', client_id = '" . (!empty($data['client_id']) ? (int)$data['client_id'] : 0) . "', powder_id = '" . (!empty($data['powder_id']) ? (int)$data['powder_id'] : 0) . "', colour_id = '" . (int)$data['colour_id'] . "', `acc_fitts_id` = '".$acc_fitts_id."', `no_qty` = ".$data['no_qty'].", master_batch_id = '" . (!empty($data['master_batch_id']) ? (int)$data['master_batch_id'] : 0) . "', pigment_id = '" . (!empty($data['pigment_id']) ? (int)$data['pigment_id'] : 0) . "', die_id = '" . (!empty($data['die_id']) ? (int)$data['die_id'] : 0) . "', moulder_id = '" . (!empty($data['moulder_id']) ? (int)$data['moulder_id'] : 0) . "', accessories_id = '" . (!empty($data['accessories_id']) ? (int)$data['accessories_id'] : 0) . "', `fittings_id` = '".$fittings_id."', address = '" . $this->db->escape($data['address']) . "', check_color = '".(!empty($data['check_color']) ? (int)$data['check_color'] : 0)."', bags = '".(!empty($data['bags']) ? (int)$data['bags'] : 0)."', total_weight = '".(!empty($data['weight']) ? (int)$data['weight'] : 0)."', qty = '" . (int)$data['qty'] . "', req_qty = '" . ((!empty($data['req_qty']) && $data['req_qty'] > 0) ? (int)$data['req_qty'] : 0) . "', targeted_date = date('" . $data['targeted_date'] . "'), date_added = NOW()");

		$order_id = $this->db->getLastId();

		return true;
		
		$this->cache->delete('order');
	}
	
	public function editOrder($order_id, $data) {

		if($data['order_type'] == 0 && $data['powder_id'] != 0){
			$sql = $this->db->query("SELECT qty FROM " . DB_PREFIX . "powder WHERE powder_id LIKE '%".$data['powder_id']."%'");
			if($sql->row['qty'] >= $data['bags']){
				$cal = $sql->row['qty'] - $data['bags'];
				$sql_update = $this->db->query("UPDATE " . DB_PREFIX . "powder SET `qty` = ".(int)$cal." WHERE powder_id LIKE '%".$data['powder_id']."%'");
			}else{
				return false;
			}
		}

		$acc_fitts_id = isset($data['acc_fitts_id']) ? (is_array($data['acc_fitts_id']) ? (implode(',', (!empty($data['acc_fitts_id']) ? $data['acc_fitts_id'] : 0))) : 0):0;

		$fittings_id = isset($data['fittings_ids']) ? (is_array($data['fittings_ids']) ? (implode(',', (!empty($data['fittings_ids']) ? $data['fittings_ids'] : 0))) : 0):0;

      	$this->db->query("UPDATE " . DB_PREFIX . "orders SET `po_no` = '" . (!empty($data['po_no']) ? $data['po_no'] : 0) . "', `product_id` = '" . (!empty($data['product_id']) ? (int)$data['product_id'] : 0) . "', order_type = '" . (int)$data['order_type'] . "', client_id = '" . (!empty($data['client_id']) ? (int)$data['client_id'] : 0) . "', powder_id = '" . (!empty($data['powder_id']) ? (int)$data['powder_id'] : 0) . "', colour_id = '" . (int)$data['colour_id'] . "', `acc_fitts_id` = '".$acc_fitts_id."', `no_qty` = ".$data['no_qty'].", master_batch_id = '" . (!empty($data['master_batch_id']) ? (int)$data['master_batch_id'] : 0) . "', pigment_id = '" . (!empty($data['pigment_id']) ? (int)$data['pigment_id'] : 0) . "', die_id = '" . (!empty($data['die_id']) ? (int)$data['die_id'] : 0) . "', moulder_id = '" . (!empty($data['moulder_id']) ? (int)$data['moulder_id'] : 0) . "', accessories_id = '" . (!empty($data['accessories_id']) ? (int)$data['accessories_id'] : 0) . "', `fittings_id` = '".$fittings_id."', address = '" . $this->db->escape($data['address']) . "', check_color = '".(!empty($data['check_color']) ? (int)$data['check_color'] : 0)."', bags = '".(!empty($data['bags']) ? (int)$data['bags'] : 0)."', total_weight = '".(!empty($data['weight']) ? (int)$data['weight'] : 0)."', qty = '" . (int)$data['qty'] . "', req_qty = '" . ((!empty($data['req_qty']) && $data['req_qty'] > 0) ? (int)$data['req_qty'] : 0) . "', targeted_date = date('" . $data['targeted_date'] . "') WHERE `orders_id` = ".$order_id);

		return true;
		
		$this->cache->delete('order');
	}
	
	public function deleteOrder($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "orders WHERE orders_id= '" . (int)$order_id. "'");			
		$this->cache->delete('order');
	}	
	
	public function getOrder($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "orders WHERE orders_id= '" . (int)$order_id. "'");
		
		return $query->row;
	}

	public function getProducts() {
		$sql = "SELECT product_id,name FROM " . DB_PREFIX . "product WHERE status = '1'";

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
		$sql = "SELECT name,address FROM " . DB_PREFIX . "moulder WHERE moulder_id = '".$moulder_id."' ";

		$query = $this->db->query($sql);
	
		return $query->row;
	}

	public function getAccessories($data = array()) {
		$sql = "SELECT accessories_id,name,qty FROM " . DB_PREFIX . "accessories WHERE status = '1'";

		$query = $this->db->query($sql);
	
		return $query->rows;
	}

	public function getAccessory($data_id) {
		$sql = "SELECT accessories_id,name,qty,image,weight FROM " . DB_PREFIX . "accessories WHERE accessories_id = ".$data_id."";

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
		$sql = "SELECT fittings_id,name,qty FROM " . DB_PREFIX . "fittings WHERE 1 ";

		$query = $this->db->query($sql);
	
		return $query->rows;
	}

	public function getFittingss($data) {
		$sql = "SELECT fittings_id,name,qty,image,weight FROM " . DB_PREFIX . "fittings WHERE fittings_id in (".$data.")";

		$query = $this->db->query($sql);
	
		return $query->rows;
	}
	
	public function getOrders($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "orders WHERE 1";

		if (!empty($data['filter_name'])) {
			$sql .= " AND `name` LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_code'])) {
			$sql .= " AND `code` LIKE '" . $this->db->escape($data['filter_code']) . "%'";
		}
		
		$sort_data = array(
			'`name`',
			'code',
			'status'
		);	
		
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY `orders_id`";	
		}
		
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
	
	public function getTotalOrders() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "orders");
		
		return $query->row['total'];
	}
	
	public function importSupplier($import_file){

		$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "orders");
		
		$row = 0;
		if (($handle = fopen($import_file['import_file']['tmp_name'], "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 0, ',', '"')) !== FALSE) {
				$row++;
				
				$fields = array();
				if ($row == 1) {
//					$column_name = "`" . implode("`,`" $row) . "`";
					continue;
				}
				
				foreach ($data as $values) { $values = $this->db->escape($values); }
				$values = implode("','", $data);

				$insert_sql = "INSERT INTO " . DB_PREFIX . "order (type,purity,weight,price) VALUES ('{$values}');";
				$this->db->query($insert_sql);
				
			}
			fclose($handle);
		}



		//$this->db->query("load DATA LOCAL infile \"".str_replace("\\", "/", $import_file['import_file']['tmp_name'])."\" INTO TABLE " . DB_PREFIX . "order FIELDS TERMINATED BY '".','."' OPTIONALLY ENCLOSED BY '\"' IGNORE 1 LINES");
	}

	public function getSupplierDetails($data=array()) {

		$sql = "SELECT SUM(o.sale_price) as sale_price, s.name as supplier_name FROM " . DB_PREFIX . "order s LEFT JOIN " . DB_PREFIX . "orders o ON s.supplier_id = o.supplier_id WHERE 1 ";

		if (!empty($data['filter_date_from'])) {
			$sql .= " AND DATE_FORMAT(o.order_date,'%Y-%m-%d') >= '" . $this->db->escape($data['filter_date_from']) . "'";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE_FORMAT(o.order_date,'%Y-%m-%d') <= '" . $this->db->escape($data['filter_date_to']) . "'";
		}

		$sql .= " GROUP BY s.supplier_id ";

		$query = $this->db->query($sql);

		if($query->num_rows) {
			return $query->rows;
		} else {
			return array();
		}
	}

	// public function getTotalOrders($data=array()) {

	// 	$sql = "SELECT COUNT(orders_id) as total FROM ".DB_PREFIX."orders WHERE 1 ";

	// 	if (!empty($data['filter_date_from'])) {
	// 		$sql .= " AND DATE_FORMAT(order_date,'%Y-%m-%d') >= '" . $this->db->escape($data['filter_date_from']) . "'";
	// 	}

	// 	if (!empty($data['filter_date_to'])) {
	// 		$sql .= " AND DATE_FORMAT(order_date,'%Y-%m-%d') <= '" . $this->db->escape($data['filter_date_to']) . "'";
	// 	}

	// 	$query = $this->db->query($sql);
	// 	if($query->num_rows) {
	// 		return $query->row['total'];
	// 	} else {
	// 		return 0;
	// 	}
	// }

	public function getPendingOrders($data=array()) {

		$sql = "SELECT COUNT(orders_id) as total FROM ".DB_PREFIX."orders WHERE status = 'Pending' ";

		if (!empty($data['filter_date_from'])) {
			$sql .= " AND DATE_FORMAT(order_date,'%Y-%m-%d') >= '" . $this->db->escape($data['filter_date_from']) . "'";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE_FORMAT(order_date,'%Y-%m-%d') <= '" . $this->db->escape($data['filter_date_to']) . "'";
		}

		$query = $this->db->query($sql);
		if($query->num_rows) {
			return $query->row['total'];
		} else {
			return 0;
		}
	}

	public function getTotalDeliveredItems($data=array()) {

		$sql = "SELECT COUNT(s.shipment_id) as total FROM ".DB_PREFIX."shipment s LEFT JOIN ".DB_PREFIX."orders o ON s.order_id = o.orders_id  WHERE s.ship_status = 'Delivered' ";

		if (!empty($data['filter_date_from'])) {
			$sql .= " AND DATE_FORMAT(o.order_date,'%Y-%m-%d') >= '" . $this->db->escape($data['filter_date_from']) . "'";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE_FORMAT(o.order_date,'%Y-%m-%d') <= '" . $this->db->escape($data['filter_date_to']) . "'";
		}

		$query = $this->db->query($sql);
		if($query->num_rows) {
			return $query->row['total'];
		} else {
			return 0;
		}

	}

	public function getTotalStocks($data=array()) {

		$sql = "SELECT COUNT(orders_id) as total FROM ".DB_PREFIX."orders WHERE status = 'Stock' ";

		if (!empty($data['filter_date_from'])) {
			$sql .= " AND DATE_FORMAT(order_date,'%Y-%m-%d') >= '" . $this->db->escape($data['filter_date_from']) . "'";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE_FORMAT(order_date,'%Y-%m-%d') <= '" . $this->db->escape($data['filter_date_to']) . "'";
		}

		$query = $this->db->query($sql);
		if($query->num_rows) {
			return $query->row['total'];
		} else {
			return 0;
		}
	}

	public function getTotalReturns($data=array()) {

		$sql = "SELECT COUNT(orders_id) as total FROM ".DB_PREFIX."orders WHERE status = 'Replacement' ";

		if (!empty($data['filter_date_from'])) {
			$sql .= " AND DATE_FORMAT(order_date,'%Y-%m-%d') >= '" . $this->db->escape($data['filter_date_from']) . "'";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE_FORMAT(order_date,'%Y-%m-%d') <= '" . $this->db->escape($data['filter_date_to']) . "'";
		}

		$query = $this->db->query($sql);
		if($query->num_rows) {
			return $query->row['total'];
		} else {
			return 0;
		}
	}

	public function getTotalIntransit($data=array()) {

		$sql = "SELECT COUNT(s.shipment_id) as total FROM ".DB_PREFIX."shipment s LEFT JOIN ".DB_PREFIX."orders o ON s.order_id = o.orders_id  WHERE s.ship_status LIKE '%transit%' ";

		if (!empty($data['filter_date_from'])) {
			$sql .= " AND DATE_FORMAT(o.order_date,'%Y-%m-%d') >= '" . $this->db->escape($data['filter_date_from']) . "'";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE_FORMAT(o.order_date,'%Y-%m-%d') <= '" . $this->db->escape($data['filter_date_to']) . "'";
		}

		$query = $this->db->query($sql);
		if($query->num_rows) {
			return $query->row['total'];
		} else {
			return 0;
		}
	}

	public function getOrderDetails($data=array()) {

		$sql = "SELECT `status`, COUNT(orders_id) AS total FROM ".DB_PREFIX."orders WHERE 1 ";

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

		if(!empty($data['qty']) && !empty($data['accessories_id'])){
			$sql .= " AND accessories_id = " . $data['accessories_id'] . "";
		}

		$query = $this->db->query($sql);
	
		return $query->row;
	}

	public function getOrderId() {
		$query = $this->db->query("SELECT MAX(po_no) AS latest_order_id FROM " . DB_PREFIX . "orders");

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