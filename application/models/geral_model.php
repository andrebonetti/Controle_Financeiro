<?php
	class Geral_model extends CI_Model {
			
		function lista_geral($year,$month){
 			/* WHERE */
			$this->db->where("ano >=",$year);
			$this->db->where("mes >=",$month);	
				
			return $this->db->get("geral")->result_array();		       
        }
				
		function saldo($year,$month){
 			/* WHERE */
			$this->db->where("ano",$year);
			$this->db->where("mes",$month);	
				
			return $this->db->get("geral")->row_array();		       
        }
			
		function get_where($type,$value){
 			/* WHERE */
			$this->db->where($type,$value);
			$this->db->order_by("mes");
				
			return $this->db->get("geral")->result_array();		       
        }
		
		function get_where2($type,$value,$type2,$value2){
 			/* WHERE */
			$this->db->where($type,$value);
			$this->db->where($type2,$value2);
				
			return $this->db->get("geral")->row_array();		       
        }
		
		function lista_distinct($col){
 			/* WHERE */
			$this->db->select($col);
			$this->db->distinct();	
			$this->db->order_by($col);
				
			return $this->db->get("geral")->result_array();		       
        }
		
		
    }