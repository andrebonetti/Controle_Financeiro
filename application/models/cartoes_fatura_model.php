<?php

    class Cartoes_Fatura_model extends CI_Model {

        // -- SELECT --
        function Listar($pData = null,$pOrderBy = null){

            if(isset($pData["IdGeral"])){$this->db->where("cartoes_fatura.IdGeral",$pData["IdGeral"]);}  
            if(isset($pData["Cartoes"])){
            
                $this->db->join("cartoes", "cartoes.Id = cartoes_fatura.IdCartao");
                
                if(isset($pData["Cartoes"]["IdUsuario"])){$this->db->where("cartoes.IdUsuario",$pData["Cartoes"]["IdUsuario"]);}
            }

            $this->db->from("cartoes_fatura");
            return $this->db->get()->result_array();

        }
        
        function Buscar($pData){

            $this->db->from("cartoes_fatura");
            return $this->db->get()->row_array();

        }
        
        // -- INSERT --
        function Incluir($pData){
			$this->db->insert("cartoes_fatura", $pData);
		}
        
    }