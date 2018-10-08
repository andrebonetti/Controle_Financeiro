<?php

    class Categoria_model extends CI_Model {

        // -- SELECT --
        function Listar($pData = null,$pOrderBy = null){

            if(isset($pData["Id"])){$this->db->where("Id",$pData["Id"]);}  
            if(isset($pData["Usuario"]["Id"])){$this->db->where("IdUsuario",$pData["Usuario"]["Id"]);}  
            if(isset($pData["Descricao"])){$this->db->where("Descricao",$pData["Descricao"]);}

            $this->db->from("categoria");
            return $this->db->get()->result_array();

        }
        
        function Buscar($pData){

            if(isset($pData["IdCategoria"])){$this->db->where("IdCategoria",$pData["IdCategoria"]);}  
            if(isset($pData["DescricaoCategoria"])){$this->db->where("DescricaoCategoria",$pData["DescricaoCategoria"]);}

            $this->db->from("categoria");
            return $this->db->get()->row_array();

        }
        
        // -- INSERT --
        function Incluir($pData){
			$this->db->insert("categoria", $pData);
		}
        
    }