<?php

    class Categoria_model extends CI_Model {

        // -- SELECT --
        function Listar($pData = null,$pOrderBy = null){

            if(isset($pData["Id"])){$this->db->where("Id",$pData["Id"]);}  
            if(isset($pData["Descricao"])){$this->db->where("Descricao",$pData["Descricao"]);}

            $this->db->from("categoria");
            return $this->db->get()->result_array();

        }
        
        function Buscar($pData){

            if(isset($pData["Id"])){$this->db->where("Id",$pData["Id"]);}  
            if(isset($pData["Descricao"])){$this->db->where("Descricao",$pData["Descricao"]);}

            $this->db->from("categoria");
            return $this->db->get()->row_array();

        }
        
        // -- INSERT --
        function Incluir($pData){
			$this->db->insert("categoria", $pData);
		}
        
    }