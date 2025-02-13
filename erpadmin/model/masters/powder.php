<?php
namespace Opencart\Admin\Model\Masters;

class Powder extends \Opencart\System\Engine\Model {
	public function addPowder($data) {
      	$this->db->query("INSERT INTO " . DB_PREFIX . "powder SET `name` = '" . $this->db->escape($data['powder_name']) . "', `qty` = '" . (int)$data['powder_qty'] . "', `weight` = '" . (int)$data['powder_weight'] . "', status = '" . (int)$data['powder_status']."'");
		
		$powder_id= $this->db->getLastId();
		
		$this->cache->delete('powder');
	}
	
	public function editPowder($powder_id, $data) {
      	$this->db->query("UPDATE " . DB_PREFIX . "powder SET `name` = '" . $this->db->escape($data['powder_name']) . "', `qty` = '" . (int)$data['powder_qty'] . "', `weight` = '" . (int)$data['powder_weight'] . "', status = '" . (int)$data['powder_status'] ."' WHERE powder_id= '" . (int)$powder_id. "'");
		
		$this->cache->delete('powder');
	}
	
	public function deletePowder($powder_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "powder WHERE powder_id= '" . (int)$powder_id. "'");			
		$this->cache->delete('powder');
	}	
	
	public function getPowder($powder_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "powder WHERE powder_id= '" . (int)$powder_id. "'");
		
		return $query->row;
	}
	
	public function getPowders($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "powder";

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
			$sql .= " ORDER BY `powder_id`";	
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
	
	public function getTotalPowders() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "powder");
		
		return $query->row['total'];
	}
	
	public function importClient($import_file){

		$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "client");
		
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

				$insert_sql = "INSERT INTO " . DB_PREFIX . "client (type,purity,weight,price) VALUES ('{$values}');";
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

	public function getMoulder($moulder_id) {
		$sql = "SELECT name,address,email FROM " . DB_PREFIX . "moulder WHERE moulder_id = '".$moulder_id."' ";

		$query = $this->db->query($sql);
	
		return $query->row;
	}

	public function getClient($client_id) {
		$sql = "SELECT name,address FROM " . DB_PREFIX . "client WHERE client_id = '".$client_id."' ";

		$query = $this->db->query($sql);
	
		return $query->row;
	}

	public function exportPowder($powder_id){
		$sql = "SELECT powder_id,bags,po_no,client_id,moulder_id,targeted_date,order_type FROM ".DB_PREFIX."orders WHERE powder_id=".$powder_id;
		$query = $this->db->query($sql);
		return $query->rows;
	}
}
?>