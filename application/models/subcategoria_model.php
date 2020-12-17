<?php

    class Subcategoria_model extends CI_Model {
     
        // -- SELECT --
        function Listar($pData = null,$pOrderBy = null){

            if(isset($pData["Id"])){$this->db->where("Id",$pData["Id"]);}  
            if(isset($pData["Usuario"]["Id"])){$this->db->where("IdUsuario",$pData["Usuario"]["Id"]);}  
            if(isset($pData["IdCategoria"])){$this->db->where("IdCategoria",$pData["IdCategoria"]);}
            if(isset($pData["Descricao"])){$this->db->where("Descricao",$pData["Descricao"]);}
            
            $this->db->from("sub_categoria");
            return $this->db->get()->result_array();

        }
        
        function Buscar($pData){

            if(isset($pData["IdSubCategoria"])){$this->db->where("IdSubCategoria",$pData["IdSubCategoria"]);}  
            if(isset($pData["IdCategoria"])){$this->db->where("IdCategoria",$pData["IdCategoria"]);}
            if(isset($pData["DescricaoSubCategoria"])){$this->db->where("DescricaoSubCategoria",$pData["DescricaoSubCategoria"]);}
            
            $this->db->from("sub_categoria");
            return $this->db->get()->row_array();

        }
        
        // -- INSERT --
        function Incluir($pData){
			$this->db->insert("sub_categoria", $pData);
		}
        
    }