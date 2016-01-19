<?php
	class crud_model extends CI_Model {
		
		/* INSERT */
		function insert($table,$data){
			$this->db->insert($table, $data);
		}
		
		/* UPDATE */
		function update($table,$id,$data){
			$this->db->where 	('id', $id);
			$this->db->update	($table, $data);
		}
		
		/* UPDATE GERAL */
		function update_mes($ano,$mes,$data){
			$this->db->where 	('ano', $ano);
			$this->db->where 	('mes', $mes);
			$this->db->update	("geral", $data);
		}

		/* DELETE */
		function delete($table,$id){
        	$this->db->delete($table, array('id' => $id));
		}
		
	}	