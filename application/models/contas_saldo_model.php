<?php
	class Contas_saldo_model extends CI_Model {

        function Buscar($pData){ 
            
            if(isset($pData["Id"])){$this->db->where("contas_saldo.Id",$pData["Id"]);}
            if(isset($pData["IdConta"])){$this->db->where("contas_saldo.IdConta",$pData["IdConta"]);}

            if(isset($pData["PeriodoDe"])){
                if($pData["PeriodoDe"] == true){
                    if(isset($pData["Mes"])){$this->db->where("Mes >=",$pData["Mes"]);}
                    if(isset($pData["Ano"])){$this->db->where("Ano >=",$pData["Ano"]);}
                }
            }
            else{
                if(isset($pData["Ano"])){$this->db->where("contas_saldo.Ano",$pData["Ano"]);}
                if(isset($pData["Mes"])){$this->db->where("contas_saldo.Mes",$pData["Mes"]);}
            }
        
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

            foreach($pContas as $itemConta){
                
                $this->db->where("contas_saldo.IdConta",$itemConta["Id"]);
                $this->db->where("contas_saldo.Ano",$pData["Ano"]);
                $this->db->where("contas_saldo.Mes",$pData["Mes"]);

                $dSaldo = $this->db->get("contas_saldo")->row_array();
                if(count($dSaldo) < 1){
                    $dSaldo = contas_saldo_criarContaSaldoMes($pData,$itemConta);
                }

                $pContas[$itemConta["Id"]]["Saldo"] = $dSaldo;

            }

            return $pContas;
        }

        // -- INSERT --
        function Incluir($pData){
            unset($pData["Id"]);
			$this->db->insert("contas_saldo", $pData);

            return $this->db->insert_id();
		}
       
        // -- UPDATE --
        function Atualizar($pData){
			$this->db->where 	('Id', $pData["Id"]);
			$this->db->update	("contas_saldo", $pData);
		}
    }