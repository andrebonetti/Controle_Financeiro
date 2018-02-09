<?php
	class Bancos_model extends CI_Model {

        function Buscar($pData){ 

            if(isset($pData["Id"])){$this->db->where("Id",$pData["Id"]);}  
            
            $this->db->from("bancos");   
            return $this->db->get()->row_array();    
        }

        function Listar($pData){ 

            $this->db->from("bancos");   
            return $this->db->get()->row_array(); 
        }

        function ListarBancosContas($pContas){

            foreach($pContas as $itemConta){               
                $this->db->where("bancos.Id",$itemConta["IdBanco"]);
                $pContas[$itemConta["Id"]]["Banco"] = $this->db->get("bancos")->row_array();  
            }

            return $pContas;
        }
           
        // -- INSERT --
        function Incluir($pData){
			$this->db->insert("bancos", $pData);
		}
       
        // -- UPDATE --
        function Atualizar($pData){
			$this->db->where 	('Id', $pData["Id"]);
			$this->db->update	("bancos", $pData);
		}
    }