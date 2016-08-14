<?php
	class Cartao_model extends CI_Model {
				
		function fatura_infinit($usuario,$year,$month){
            
            /* FROM */ $this->db->from("cartao_de_credito");
            
            /* JOIN */
            $this->db->join("categoria", "categoria.id_categoria = cartao_de_credito.categoria");
            $this->db->join("sub_categoria", "sub_categoria.id_sub_categoria = cartao_de_credito.sub_categoria");           
			
            /* WHERE */
			$this->db->where("type","1");
            $this->db->where("ano <=",$year);
            $this->db->where("mes <=",$month);
			//$this->db->where("ano_fim >=",$year);
            //$this->db->where("mes_fim >=",$month);
			$this->db->where("valor !=",0);
			$this->db->where("usuario",$usuario);
			
			return $this->db->get()->result_array();		
			  
        }
		
		function fatura_parcela($year,$month,$usuario){
            
            /* FROM */ $this->db->from("cartao_de_credito");
            
            /* JOIN */
            $this->db->join("categoria", "categoria.id_categoria = cartao_de_credito.categoria");
            $this->db->join("sub_categoria", "sub_categoria.id_sub_categoria = cartao_de_credito.sub_categoria");           
			
            /* WHERE */
			$this->db->where("type","2");
			$this->db->where("ano",$year);
            $this->db->where("mes",$month);
            $this->db->where("valor !=",0);
			$this->db->where("usuario",$usuario);
			
			return $this->db->get()->result_array();		
			  
        }
        
        function fatura_unique($year,$month,$usuario){
            
            /* FROM */ $this->db->from("cartao_de_credito");
            
            /* JOIN */
            $this->db->join("categoria", "categoria.id_categoria = cartao_de_credito.categoria");
            $this->db->join("sub_categoria", "sub_categoria.id_sub_categoria = cartao_de_credito.sub_categoria");   
			
            /* WHERE */
			$this->db->where("type","3");
            $this->db->where("ano",$year);
            $this->db->where("mes",$month);
            $this->db->where("valor !=",0);
			$this->db->where("usuario",$usuario);
			
			return $this->db->get()->result_array();
        
        }
        
        // -- INSERT --
        function Incluir($pData){
			$this->db->insert("cartao_de_credito", $pData);
		}
       
    }