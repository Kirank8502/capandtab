<?php
namespace Opencart\Admin\Model\Catalog;
class Order extends \Opencart\System\Engine\Model {
	public function addOrder($data) {
      	$this->db->query("INSERT INTO " . DB_PREFIX . "orders SET `product_id` = '" . (int)$data['product_id'] . "',image = '" . $data['image']."', client_id = '" . (int)$data['client_id'] . "', powder_id = '" . (int)$data['colour_id'] . "', address = '" . $this->db->escape($data['address']) . "', remark = '" . $this->db->escape($data['remark']) . "', status = '" . $data['status'] . "', date_added = 'NOW()'");
		
		$order_id= $this->db->getLastId();
		
		$this->cache->delete('order');
	}
	
	public function editOrder($order_id, $data) {
      	$this->db->query("UPDATE " . DB_PREFIX . "order SET `product_id` = '" . (int)$data['product_id'] . "',image = '" . $data['image']."', client_id = '" . (int)$data['client_id'] . "', powder_id = '" . (int)$data['colour_id'] . "', address = '" . $this->db->escape($data['address']) . "', remark = '" . $this->db->escape($data['remark']) . "', status = '" . $data['status'] . "', date_added = 'NOW()'");
		
		$this->cache->delete('order');
	}
	
	public function deleteOrder($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "order WHERE order_id= '" . (int)$order_id. "'");			
		$this->cache->delete('order');
	}	
	
	public function getOrder($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE order_id= '" . (int)$order_id. "'");
		
		return $query->row;
	}

	public function getProducts($data = array()) {
		$sql = "SELECT product_id,name FROM " . DB_PREFIX . "product WHERE status = '1'";

		$query = $this->db->query($sql);
	
		return $query->rows;
	}

	public function getClients($data = array()) {
		$sql = "SELECT client_id,name FROM " . DB_PREFIX . "client WHERE status = '1'";

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
	
	public function getOrders($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "order WHERE 1";

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
			$sql .= " ORDER BY `order_id`";	
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
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order");
		
		return $query->row['total'];
	}
	
	public function importSupplier($import_file){

		$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "order");
		
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
}
?>