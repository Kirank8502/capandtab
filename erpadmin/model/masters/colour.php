<?php
namespace Opencart\Admin\Model\Masters;

class Colour extends \Opencart\System\Engine\Model {
	public function addColour($data) {
      	$this->db->query("INSERT INTO " . DB_PREFIX . "colour SET `name` = '" . $this->db->escape($data['colour_name']) . "', `qty` = '0',status = '" . (int)$data['colour_status']."'");
		
		$colour_id= $this->db->getLastId();
		
		$this->cache->delete('colour');
	}
	
	public function editColour($colour_id, $data) {
      	$this->db->query("UPDATE " . DB_PREFIX . "colour SET `name` = '" . $this->db->escape($data['colour_name']) . "', `qty` = '0', status = '" . (int)$data['colour_status'] ."' WHERE colour_id= '" . (int)$colour_id. "'");
		
		$this->cache->delete('colour');
	}
	
	public function deleteColour($colour_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "colour WHERE colour_id= '" . (int)$colour_id. "'");			
		$this->cache->delete('colour');
	}	
	
	public function getColour($colour_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "colour WHERE colour_id= '" . (int)$colour_id. "'");
		
		return $query->row;
	}
	
	public function getColours($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "colour";

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
			$sql .= " ORDER BY `colour_id`";	
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
	
	public function getTotalColours() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "colour");
		
		return $query->row['total'];
	}
	
	public function importColour($import_file){

		$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "colour");
		
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

				$insert_sql = "INSERT INTO " . DB_PREFIX . "colour (type,purity,weight,price) VALUES ('{$values}');";
				$this->db->query($insert_sql);
				
			}
			fclose($handle);
		}



		//$this->db->query("load DATA LOCAL infile \"".str_replace("\\", "/", $import_file['import_file']['tmp_name'])."\" INTO TABLE " . DB_PREFIX . "assembler FIELDS TERMINATED BY '".','."' OPTIONALLY ENCLOSED BY '\"' IGNORE 1 LINES");
	}
}
?>