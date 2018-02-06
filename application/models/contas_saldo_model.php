<?php
	class Contas_saldo_model extends CI_Model {

        function Buscar($pData){ 
            
            if(isset($pData["Id"])){$this->db->where("contas_saldo.Id",$pData["Id"]);}
            if(isset($pData["IdConta"])){$this->db->where("contas_saldo.IdConta",$pData["IdConta"]);}
            if(isset($pData["Ano"])){$this->db->where("contas_saldo.Ano",$pData["Ano"]);}
            if(isset($pData["Mes"])){$this->db->where("contas_saldo.Mes",$pData["Mes"]);}

            return $this->db->get("contas_saldo")->row_array();    
        }

        function Listar($pData){ 
            
            if(isset($pData["Id"])){$this->db->where("contas_saldo.Id",$pData["Id"]);}
            if(isset($pData["IdConta"])){$this->db->where("contas_saldo.IdConta",$pData["IdConta"]);}

            if(isset($pData["PeriodoDe"])){
                if($pData["PeriodoDe"] == true){
                    if(isset($pData["Mes"])){$this->db->where("Mes >=",$pData["Mes"]);}
                    if(isset($pData["Ano"])){$this->db->where("Ano >=",$pData["Ano"]);}
                }
            }
            if(isset($pData["PeriodoAte"])){
                if($pData["PeriodoAte"] == true){
                    if(isset($pData["Mes"])){$this->db->where("Mes <=",$pData["Mes"]);}
                    if(isset($pData["Ano"])){$this->db->where("Ano <=",$pData["Ano"]);}
                }
            }
            if((!isset($pData["PeriodoDe"]))&&(!isset($pData["PeriodoAte"]))){
                if(isset($pData["Ano"])){$this->db->where("Ano",$pData["Ano"]);}
                if(isset($pData["Mes"])){$this->db->where("Mes",$pData["Mes"]);}
            }
            
            return $this->db->get("contas_saldo")->result_array();    
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