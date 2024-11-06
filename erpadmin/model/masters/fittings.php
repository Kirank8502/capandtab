<?php
namespace Opencart\Admin\Model\Masters;

class Fittings extends \Opencart\System\Engine\Model {
	public function addFitting($data) {
      	$this->db->query("INSERT INTO " . DB_PREFIX . "fittings SET `name` = '" . $this->db->escape($data['fittings_name']) . "', `image` = '" . $data['fittings_image'] . "', `height` = '" . (float)$data['fittings_height'] . "', `weight` = '" . (float)$data['fittings_weight'] . "', `width` = '" . (float)$data['fittings_width'] . "', `inner_circle` = '" . (float)$data['fittings_inner_circle'] . "', `outer_circle` = '" . (float)$data['fittings_outer_circle'] . "', `thickness` = '" . (float)$data['fittings_thickness'] . "', qty = '" . (int)$data['fittings_qty']."', price = '" . $data['fittings_price']."', status = '" . (int)$data['fittings_status']."'");
		
		$fittings_id= $this->db->getLastId();
		
		$this->cache->delete('fittings');
	}
	
	public function editFitting($fittings_id, $data) {
      	$this->db->query("UPDATE " . DB_PREFIX . "fittings SET `name` = '" . $this->db->escape($data['fittings_name']) . "', `image` = '" . $data['fittings_image'] . "', `height` = '" . (float)$data['fittings_height'] . "', `weight` = '" . (float)$data['fittings_weight'] . "', `width` = '" . (float)$data['fittings_width'] . "', `inner_circle` = '" . (float)$data['fittings_inner_circle'] . "', `outer_circle` = '" . (float)$data['fittings_outer_circle'] . "', `thickness` = '" . (float)$data['fittings_thickness'] . "', qty = '" . (int)$data['fittings_qty']."', price = '" . $data['fittings_price']."', status = '" . (int)$data['fittings_status'] ."' WHERE fittings_id= '" . (int)$fittings_id. "'");
		
		$this->cache->delete('fittings');
	}
	
	public function deleteFitting($fittings_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "fittings WHERE fittings_id= '" . (int)$fittings_id. "'");			
		$this->cache->delete('fittings');
	}	
	
	public function getFitting($fittings_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "fittings WHERE fittings_id= '" . (int)$fittings_id. "'");
		
		return $query->row;
	}
	
	public function getFittings($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "fittings";

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
			$sql .= " ORDER BY `fittings_id`";	
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
	
	public function getTotalFittings() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "fittings");
		
		return $query->row['total'];
	}
	
	public function importFitting($import_file){

		$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "fittings");
		
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

				$insert_sql = "INSERT INTO " . DB_PREFIX . "fittings (type,purity,weight,price) VALUES ('{$values}');";
				$this->db->query($insert_sql);
				
			}
			fclose($handle);
		}



		//$this->db->query("load DATA LOCAL infile \"".str_replace("\\", "/", $import_file['import_file']['tmp_name'])."\" INTO TABLE " . DB_PREFIX . "assembler FIELDS TERMINATED BY '".','."' OPTIONALLY ENCLOSED BY '\"' IGNORE 1 LINES");
	}

	public function getFittingDetails($data=array()) {

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