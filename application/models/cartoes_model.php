<?php
	class Cartoes_model extends CI_Model {

        function ListarCartoesAtivos($pData){ 

            if(isset($pData["Cartoes"]["IdUsuario"])){$this->db->where("IdUsuario",$pData["Cartoes"]["IdUsuario"]);}
            $WhereData = "(
                (
                    `AnoVencimento` =  ".$pData["Ano"]."
                    AND `MesVencimento` >= ".$pData["Mes"]."

                )
                OR 
                (`AnoVencimento` > ".$pData["Ano"].")
            )";
            
            $this->db->where($WhereData);

            $this->db->from("cartoes");   
            return $this->db->get()->result_array();    
        }
        
        function Buscar($pData){ 
            
            $this->db->where("cartoes.Id",$pData["Id"]);

            $this->db->from("cartoes");   
            return $this->db->get()->row_array();    
        }

        function ListarTransacoesFatura($pData){ 

            $WhereData = "(
                (
                    `AnoVencimento` =  ".$pData["Ano"]."
                    AND `MesVencimento` >= ".$pData["Mes"]."

                )
                OR 
                (`AnoVencimento` > ".$pData["Ano"].")
            )";
            
            $this->db->where($WhereData);

            $this->db->from("cartoes");   
            return $this->db->get()->result_array();    
        }        
        
        // -- INSERT --
        function Incluir($pData){
			$this->db->insert("cartoes", $pData);
		}
       
        // -- UPDATE --
        function Atualizar($pData){
			$this->db->where 	('Id', $pData["Id"]);
			$this->db->update	("cartoes", $pData);
		}
    }