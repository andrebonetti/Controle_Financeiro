<?php

    class Subcategoria_model extends CI_Model {
     
        // -- SELECT --
        function Listar($pData = null,$pOrderBy = null){

            if(isset($pData["id_sub_categoria"])){$this->db->where("id_sub_categoria",$pData["id_sub_categoria"]);}  
            if(isset($pData["categoria"])){$this->db->where("categoria",$pData["categoria"]);}
            if(isset($pData["nome_sub_categoria"])){$this->db->where("nome_sub_categoria",$pData["nome_sub_categoria"]);}
            
            $this->db->from("sub_categoria");
            return $this->db->get()->result_array();

        }
        
        function Buscar($pData){

            if(isset($pData["id_sub_categoria"])){$this->db->where("id_sub_categoria",$pData["id_sub_categoria"]);}  
            if(isset($pData["categoria"])){$this->db->where("categoria",$pData["categoria"]);}
            if(isset($pData["nome_sub_categoria"])){$this->db->where("nome_sub_categoria",$pData["nome_sub_categoria"]);}
            
            $this->db->from("sub_categoria");
            return $this->db->get()->row_array();

        }
        
    }