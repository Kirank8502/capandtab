<?php
namespace Opencart\Admin\Model\Catalog;
class StoreOrder extends \Opencart\System\Engine\Model {
	public function addOrderDetails($data) {
		// if($data['order_type'] == 0){
		// 	$sql = $this->db->query("SELECT qty FROM " . DB_PREFIX . "accessories WHERE accessories_id LIKE '%".$data['accessories_id']."%'");
		// 	if($sql->row['qty'] >= $data['req_qty']){
		// 		$cal = $sql->row['qty'] - $data['req_qty'];
		// 		$sql_update = $this->db->query("UPDATE " . DB_PREFIX . "accessories SET `qty` = ".(int)$cal." WHERE accessories_id LIKE '%".$data['accessories_id']."%'");
		// 	}else{
		// 		return false;
		// 	}
		// }
		
		// if($data['order_type'] == 1){
		// 	$sql = $this->db->query("SELECT qty FROM " . DB_PREFIX . "fittings WHERE fittings_id LIKE '%".$data['fittings_id']."%'");
		// 	if($sql->row['qty'] >= $data['qty']){
		// 		$cal = $sql->row['qty'] - $data['qty'];
		// 		$sql_update = $this->db->query("UPDATE " . DB_PREFIX . "fittings SET `qty` = ".(int)$cal." ");
		// 	}else{
		// 		return false;
		// 	}
		// }

		$accessories = $this->db->query("SELECT accessories_id FROM " . DB_PREFIX . "orders WHERE po_no='" . $data['po_no']. "'");
		$sql_query = $this->db->query("SELECT qty FROM " . DB_PREFIX . "accessories WHERE accessories_id='" . $accessories->row['accessories_id']. "'");
		$main_val = $sql_query->row['qty'] + $data['qty'];
		$this->db->query("UPDATE " . DB_PREFIX . "accessories SET `qty` = '" . (!empty($main_val) ? (int)$main_val : 0) . "' WHERE accessories_id='" . $accessories->row['accessories_id']. "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "order_details SET `orders_id` = '" . (!empty($data['orders_id']) ? (int)$data['orders_id'] : 0) . "', po_no = '" . $data['po_no'] . "', qty_rev = '" . (!empty($data['qty']) ? (int)$data['qty'] : 0) . "', image = '" . (!empty($data['image']) ? (int)$data['image'] : '') . "'");

		$order_id = $this->db->getLastId();
		
		$this->cache->delete('order');
		return true;
	}
	
	public function editOrderDetails($store_order_id, $data) {
		$accessories = $this->db->query("SELECT accessories_id FROM " . DB_PREFIX . "orders WHERE po_no='" . $data['po_no']. "'");
		$sql_query = $this->db->query("SELECT qty FROM " . DB_PREFIX . "accessories WHERE accessories_id='" . $accessories->row['accessories_id']. "'");
		$order_query = $this->db->query("SELECT qty_rev FROM " . DB_PREFIX . "order_details WHERE store_order_id='" . $store_order_id. "'");
		$temp = abs($order_query->row['qty_rev'] - $data['qty']);
		$main_val = $sql_query->row['qty'] + $temp;
		$this->db->query("UPDATE " . DB_PREFIX . "accessories SET `qty` = '" . (!empty($main_val) ? (int)$main_val : 0) . "' WHERE accessories_id='" . $accessories->row['accessories_id']. "'");

      	$this->db->query("UPDATE " . DB_PREFIX . "order_details SET `orders_id` = '" . (!empty($data['orders_id']) ? (int)$data['orders_id'] : 0) . "', po_no = '" . $data['po_no'] . "', qty_rev = '" . (!empty($data['qty']) ? (int)$data['qty'] : 0) . "', image = '" . (!empty($data['image']) ? $data['image'] : '') . "' WHERE `store_order_id` = ".$store_order_id."");
		
		$this->cache->delete('order');

		return true;
	}
	
	public function deleteOrder($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "orders WHERE orders_id= '" . (int)$order_id. "'");			
		$this->cache->delete('order');
	}	
	
	public function getOrder($store_order_id,$order_id) {
		if(!empty($store_order_id)){
			$query = $this->db->query("SELECT o.qty,o.orders_id as o_orders_id,o.order_type,o.po_no as order_po_no,o.moulder_id,o.client_id,od.* FROM " . DB_PREFIX . "orders o LEFT JOIN " . DB_PREFIX . "order_details od ON (o.po_no = od.po_no) WHERE od.store_order_id='" . $store_order_id. "'");
		}elseif($order_id){
			$query = $this->db->query("SELECT o.qty,o.orders_id as o_orders_id,o.order_type,o.po_no as order_po_no,o.moulder_id,o.client_id,od.* FROM " . DB_PREFIX . "orders o LEFT JOIN " . DB_PREFIX . "order_details od ON (o.po_no = od.po_no) WHERE o.orders_id='" . $order_id. "'");
		}
		
		return $query->row;
	}

	public function getProducts() {
		$sql = "SELECT product_id,name FROM " . DB_PREFIX . "product WHERE status = '1'";

		$query = $this->db->query($sql);
	
		return $query->rows;
	}

	public function getClients($data = array()) {
		$sql = "SELECT client_id,name,address FROM " . DB_PREFIX . "client WHERE status = '1' ";

		if(!empty($data['client_id'])){
			$sql .= " AND client_id = ".$data['client_id']." ";
		}

		$query = $this->db->query($sql);
	
		return $query->rows;
	}

	public function getPowders($data = array()) {
		$sql = "SELECT powder_id,name FROM " . DB_PREFIX . "powder WHERE status = '1'";

		$query = $this->db->query($sql);
	
		return $query->rows;
	}

	public function getColours($data = array()) {
		$sql = "SELECT colour_id,name FROM " . DB_PREFIX . "colour WHERE status = '1'";

		$query = $this->db->query($sql);
	
		return $query->rows;
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
		$sql = "SELECT moulder_id,name,address FROM " . DB_PREFIX . "moulder WHERE status = '1' AND moulder_id = ".$moulder_id." ";

		$query = $this->db->query($sql);
	
		return $query->row;
	}

	public function getClient($client_id) {
		$sql = "SELECT client_id,name,address FROM " . DB_PREFIX . "client WHERE status = '1' AND client_id = ".$client_id." ";

		$query = $this->db->query($sql);
	
		return $query->row;
	}

	public function getAccessories($data = array()) {
		$sql = "SELECT accessories_id,name,qty FROM " . DB_PREFIX . "accessories WHERE status = '1'";

		$query = $this->db->query($sql);
	
		return $query->rows;
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

	public function getFittings($data = array()) {
		$sql = "SELECT fittings_id,name,qty FROM " . DB_PREFIX . "fittings WHERE 1 ";

		$query = $this->db->query($sql);
	
		return $query->rows;
	}
	
	public function getOrders($data = array()) {
		$sql = "SELECT o.orders_id,o.po_no,o.order_type,o.moulder_id,o.client_id,o.qty,o.req_qty,od.image,od.image,od.qty_rev,od.store_order_id FROM " . DB_PREFIX . "orders o LEFT JOIN ".DB_PREFIX."order_details od ON (o.po_no = od.po_no) WHERE 1";
		
		$sort_data = array(
			'`name`',
			'code',
			'status'
		);	
		
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY o.orders_id";
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
					continue;
				}
				
				foreach ($data as $values) { $values = $this->db->escape($values); }
				$values = implode("','", $data);

				$insert_sql = "INSERT INTO " . DB_PREFIX . "order (type,purity,weight,price) VALUES ('{$values}');";
				$this->db->query($insert_sql);
				
			}
			fclose($handle);
		}
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