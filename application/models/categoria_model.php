<?php

    class Categoria_model extends CI_Model {

        // -- SELECT --
        function Listar($pData = null,$pOrderBy = null){

            if(isset($pData["id_categoria"])){$this->db->where("id_categoria",$pData["id_categoria"]);}  
            if(isset($pData["nome_categoria"])){$this->db->where("nome_categoria",$pData["nome_categoria"]);}

            $this->db->from("categoria");
            return $this->db->get()->result_array();

        }
        
        function Buscar($pData){

            if(isset($pData["id_cetegoria"])){$this->db->where("id_cetegoria",$pData["id_cetegoria"]);}  
            if(isset($pData["nome_categoria"])){$this->db->where("nome_categoria",$pData["nome_categoria"]);}

            $this->db->from("categoria");
            return $this->db->get()->row_array();

        }
        
        // -- INSERT --
        function Incluir($pData){
			$this->db->insert("categoria", $pData);
		}
        
    }