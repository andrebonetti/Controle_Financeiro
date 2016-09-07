<?php
	class Cartao_model extends CI_Model {
				
		function fatura_infinit($usuario,$year,$month){
            
            /* FROM */ $this->db->from("cartao_de_credito");
            
            /* JOIN */
            $this->db->join("Categoria", "Categoria.IdCategoria = cartao_de_credito.IdCategoria");
            $this->db->join("Sub_Categoria", "sub_categoria.IdSubCategoria = cartao_de_credito.IdSubCategoria");             
			
            /* WHERE */
			$this->db->where("cartao_de_credito.IdTipoTransacao","1");
            $this->db->where("cartao_de_credito.Ano <=",$year);
            $this->db->where("cartao_de_credito.Mes <=",$month);
			//$this->db->where("ano_fim >=",$year);
            //$this->db->where("mes_fim >=",$month);
			$this->db->where("cartao_de_credito.Valor !=",0);
			$this->db->where("cartao_de_credito.IdUsuario",$usuario);
			
			return $this->db->get()->result_array();		
			  
        }
		
		function fatura_parcela($year,$month,$usuario){
            
            /* FROM */ $this->db->from("cartao_de_credito");
            
            /* JOIN */
            $this->db->join("Categoria", "Categoria.IdCategoria = cartao_de_credito.IdCategoria");
            $this->db->join("Sub_Categoria", "sub_categoria.IdSubCategoria = cartao_de_credito.IdSubCategoria");            
			
            /* WHERE */
			$this->db->where("cartao_de_credito.IdTipoTransacao","2");
			$this->db->where("cartao_de_credito.Ano",$year);
            $this->db->where("cartao_de_credito.Mes",$month);
            $this->db->where("cartao_de_credito.Valor !=",0);
			$this->db->where("cartao_de_credito.IdUsuario",$usuario);
			
			return $this->db->get()->result_array();		
			  
        }
        
        function fatura_unique($year,$month,$usuario){
            
            /* FROM */ $this->db->from("cartao_de_credito");
            
            /* JOIN */
            $this->db->join("Categoria", "Categoria.IdCategoria = cartao_de_credito.IdCategoria");
            $this->db->join("Sub_Categoria", "sub_categoria.IdSubCategoria = cartao_de_credito.IdSubCategoria");           
			
            /* WHERE */
			$this->db->where("cartao_de_credito.IdTipoTransacao","3");
            $this->db->where("cartao_de_credito.Ano",$year);
            $this->db->where("cartao_de_credito.Mes",$month);
            $this->db->where("cartao_de_credito.Valor !=",0);
			$this->db->where("cartao_de_credito.IdUsuario",$usuario);
			
			return $this->db->get()->result_array();
        
        }
        
        // -- INSERT --
        function Incluir($pData){
			$this->db->insert("cartao_de_credito", $pData);
		}
       
    }