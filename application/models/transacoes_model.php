<?php
	class Transacoes_model extends CI_Model {
				
		function pay_type($type,$year,$month,$day,$usuario){
			
			/* FROM */ $this->db->from("transacoes");
			
			/* JOIN */
            $this->db->join("categoria", "categoria.id_categoria = transacoes.categoria");
            $this->db->join("sub_categoria", "sub_categoria.id_sub_categoria = transacoes.sub_categoria");
            
            /* WHERE */
            if($type == "1"){}
            if(($type != "1")&&($type != "4")){
				$this->db->where("ano",$year);
            	$this->db->where("mes",$month);
			}
            if($type != "4"){
				$this->db->where("dia",$day);
            }
			
			$this->db->where("type",$type);	
			$this->db->where("valor !=",0);				
			$this->db->where("usuario",$usuario);
			
			return $this->db->get()->result_array();		
			
		}
		
        function lista_table($table){
        		
        	if($table == "sub_categoria"){
				/*WHERE*/$this->db->where("id_sub_categoria !=","0");
			}	
        	     
            /*FROM*/ $this->db->from($table);
            return $this->db->get()->result_array();
            
        }
        
        function lista_sub_table($id,$table){
            
            /* FROM */ $this->db->from($table);
            
            /* WHERE */$this->db->where("categoria",$id);
            
            return $this->db->get()->result_array();
            
        }
        
        function get_id_categoria($nome){         
            /*FROM*/$this->db->from("categoria");     
            /*WHERE*/$this->db->where("nome_categoria",$nome);        
            return $this->db->get()->row_array();    
        }
        function get_id_sub_categoria($nome){         
            /*FROM*/$this->db->from("sub_categoria");     
            /*WHERE*/$this->db->where("nome_sub_categoria",$nome);        
            return $this->db->get()->row_array();    
        }
		
		function get_where($table,$atribute,$value){         
            /*FROM*/$this->db->from($table);     
            /*WHERE*/$this->db->where($atribute,$value);        
            return $this->db->get()->row_array();    
        }
        
	}	