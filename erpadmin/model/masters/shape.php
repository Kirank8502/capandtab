<?php
namespace Opencart\Admin\Model\Masters;

class Shape extends \Opencart\System\Engine\Model {
	public function addShape($data) {
      	$this->db->query("INSERT INTO " . DB_PREFIX . "shape SET `name` = '" . $this->db->escape($data['shape_name']) . "',status = '" . (int)$data['shape_status']."'");
		
		$shape_id= $this->db->getLastId();
		
		$this->cache->delete('shape');
	}
	
	public function editShape($shape_id, $data) {
      	$this->db->query("UPDATE " . DB_PREFIX . "shape SET `name` = '" . $this->db->escape($data['shape_name']) . "', status = '" . (int)$data['shape_status'] ."' WHERE shape_id= '" . (int)$shape_id. "'");
		
		$this->cache->delete('shape');
	}
	
	public function deleteShape($shape_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "shape WHERE shape_id= '" . (int)$shape_id. "'");			
		$this->cache->delete('shape');
	}	
	
	public function getShape($shape_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "shape WHERE shape_id= '" . (int)$shape_id. "'");
		
		return $query->row;
	}
	
	public function getShapes($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "shape";

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
			$sql .= " ORDER BY `shape_id`";	
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
	
	public function getTotalShapes() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "shape");
		
		return $query->row['total'];
	}
	
	public function importShape($import_file){

		$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "shape");
		
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

				$insert_sql = "INSERT INTO " . DB_PREFIX . "shape (type,purity,weight,price) VALUES ('{$values}');";
				$this->db->query($insert_sql);
				
			}
			fclose($handle);
		}



		//$this->db->query("load DATA LOCAL infile \"".str_replace("\\", "/", $import_file['import_file']['tmp_name'])."\" INTO TABLE " . DB_PREFIX . "assembler FIELDS TERMINATED BY '".','."' OPTIONALLY ENCLOSED BY '\"' IGNORE 1 LINES");
	}

	public function getShapeDetails($data=array()) {

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