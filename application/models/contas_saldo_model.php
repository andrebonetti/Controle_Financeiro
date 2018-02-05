<?php
	class Contas_saldo_model extends CI_Model {

        function Buscar($pData){ 
            
            $this->db->where("contas_saldo.Id",$pData["Id"]);

            $this->db->from("contas_saldo");   
            return $this->db->get()->row_array();    
        }

        function Listar($pData){ 
            
            $this->db->where("contas_saldo.Id",$pData["Id"]);

            $this->db->from("contas_saldo");   
            return $this->db->get()->result_array();    
        }
           
        function ListarSaldosContas($pData,$pContas){

            $cont = 0;
            foreach($pContas as $itemConta){
                
                $this->db->where("contas_saldo.IdConta",$itemConta["Id"]);
                $this->db->where("contas_saldo.Ano",$pData["Ano"]);
                $this->db->where("contas_saldo.Mes",$pData["Mes"]);

                $pContas[$cont]["Saldo"] = $this->db->get("contas_saldo")->row_array();  

                $cont ++;
            }

            return $pContas;
        }

        // -- INSERT --
        function Incluir($pData){
			$this->db->insert("contas_saldo", $pData);
		}
       
        // -- UPDATE --
        function Atualizar($pData){
			$this->db->where 	('Id', $pData["Id"]);
			$this->db->update	("contas_saldo", $pData);
		}
    }