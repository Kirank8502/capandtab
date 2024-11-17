<?php
namespace Opencart\Admin\Model\Catalog;

class Product extends \Opencart\System\Engine\Model {
	public function addProduct($data) {
		$fittings_id = !empty($data['fittings_ids']) && is_array($data['fittings_ids']) ? implode(',', $data['fittings_ids']) : 0;
      	$this->db->query("INSERT INTO " . DB_PREFIX . "product SET `name` = '" . $this->db->escape($data['product_name']) . "', `fittings_id` = '".$fittings_id."', `image` = '" . $data['image'] . "', `category_id` = '". (int)$data['category_id'] ."', status = '" . (int)$data['product_status']."'");
		
		$product_id= $this->db->getLastId();
		
		$this->cache->delete('product');
	}
	
	public function editProduct($product_id, $data) {
		$fittings_id = !empty($data['fittings_ids']) && is_array($data['fittings_ids']) ? implode(',', $data['fittings_ids']) : 0;
      	$this->db->query("UPDATE " . DB_PREFIX . "product SET `name` = '" . $this->db->escape($data['product_name']) . "', `fittings_id` = '".$fittings_id."', `image` = '" . $data['image'] . "', `category_id` = '". (int)$data['category_id'] ."', status = '" . (int)$data['product_status'] ."' WHERE product_id= '" . (int)$product_id. "'");
		
		$this->cache->delete('product');
	}
	
	public function deleteProduct($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id= '" . (int)$product_id. "'");			
		$this->cache->delete('product');
	}	
	
	public function getProduct($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id= '" . (int)$product_id. "'");
		
		return $query->row;
	}
	
	public function getProducts($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "product WHERE 1 ";

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
			$sql .= " ORDER BY `product_id`";	
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
	
	public function getTotalProducts() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product");
		
		return $query->row['total'];
	}
	
	public function importSupplier($import_file){

		$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "product");
		
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

				$insert_sql = "INSERT INTO " . DB_PREFIX . "product (type,purity,weight,price) VALUES ('{$values}');";
				$this->db->query($insert_sql);
				
			}
			fclose($handle);
		}



		//$this->db->query("load DATA LOCAL infile \"".str_replace("\\", "/", $import_file['import_file']['tmp_name'])."\" INTO TABLE " . DB_PREFIX . "product FIELDS TERMINATED BY '".','."' OPTIONALLY ENCLOSED BY '\"' IGNORE 1 LINES");
	}

	public function getSupplierDetails($data=array()) {

		$sql = "SELECT SUM(o.sale_price) as sale_price, s.name as supplier_name FROM " . DB_PREFIX . "product s LEFT JOIN " . DB_PREFIX . "orders o ON s.supplier_id = o.supplier_id WHERE 1 ";

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

	public function getTotalOrders($data=array()) {

		$sql = "SELECT COUNT(orders_id) as total FROM ".DB_PREFIX."orders WHERE 1 ";

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

	public function getCategories(){
		$sql = "SELECT c.category_id,cd.name FROM " . DB_PREFIX . "category c LEFT JOIN ". DB_PREFIX ."category_description cd ON c.category_id = cd.category_id";

		$query = $this->db->query($sql);

		return $query->rows;		
	}

	public function getFittings(){
		$sql = "SELECT fittings_id,name FROM " . DB_PREFIX . "fittings";

		$query = $this->db->query($sql);

		return $query->rows;		
	}
}
?>