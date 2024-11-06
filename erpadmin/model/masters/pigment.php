<?php
namespace Opencart\Admin\Model\Masters;

class Pigment extends \Opencart\System\Engine\Model {
	public function addPigment($data) {
      	$this->db->query("INSERT INTO " . DB_PREFIX . "pigment SET `qty` = '" . (int)$data['pigment_qty'] . "', `colour_id` = '" . (int)$data['colour_id'] . "', `image` = '" . $this->db->escape($data['image']) . "', status = '" . (int)$data['pigment_status']."'");
		
		$pigment_id= $this->db->getLastId();
		
		$this->cache->delete('pigment');
	}
	
	public function editPigment($pigment_id, $data) {
      	$this->db->query("UPDATE " . DB_PREFIX . "pigment SET `qty` = '" . (int)$data['pigment_qty'] . "', `colour_id` = '" . (int)$data['colour_id'] . "', `image` = '" . $this->db->escape($data['image']) . "', status = '" . (int)$data['pigment_status'] ."' WHERE pigment_id= '" . (int)$pigment_id. "'");
		
		$this->cache->delete('pigment');
	}
	
	public function deletePigment($pigment_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "pigment WHERE pigment_id= '" . (int)$pigment_id. "'");			
		$this->cache->delete('pigment');
	}	
	
	public function getPigment($pigment_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "pigment WHERE pigment_id= '" . (int)$pigment_id. "'");
		
		return $query->row;
	}

	public function getColors() {
		$query = $this->db->query("SELECT colour_id,name FROM " . DB_PREFIX . "colour");
		
		return $query->rows;
	}

	public function getColour($colour_id) {
		$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "colour WHERE colour_id = ".$colour_id."");
		
		return $query->row;
	}
	
	public function getPigments($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "pigment";

		if (!empty($data['filter_name'])) {
			$sql .= " WHERE `name` LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_code'])) {
			$sql .= " WHERE `code` LIKE '" . $this->db->escape($data['filter_code']) . "%'";
		}
		
		$sort_data = array(
			'`name`',
			'code',
			'status'
		);	
		
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY `pigment_id`";	
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
	
	public function getTotalPigments() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "pigment");
		
		return $query->row['total'];
	}
	
	public function importPigment($import_file){

		$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "pigment");
		
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

				$insert_sql = "INSERT INTO " . DB_PREFIX . "pigment (type,purity,weight,price) VALUES ('{$values}');";
				$this->db->query($insert_sql);
				
			}
			fclose($handle);
		}



		//$this->db->query("load DATA LOCAL infile \"".str_replace("\\", "/", $import_file['import_file']['tmp_name'])."\" INTO TABLE " . DB_PREFIX . "assembler FIELDS TERMINATED BY '".','."' OPTIONALLY ENCLOSED BY '\"' IGNORE 1 LINES");
	}

	public function getClientDetails($data=array()) {

		$sql = "SELECT SUM(o.sale_price) as sale_price, s.name as supplier_name FROM " . DB_PREFIX . "assembler s LEFT JOIN " . DB_PREFIX . "orders o ON s.supplier_id = o.supplier_id WHERE 1 ";

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
}
?>