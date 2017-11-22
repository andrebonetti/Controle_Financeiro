<?php
	class Geral_model extends CI_Model {
			
        // -- SELECT -- 
		function Listar($pData = null){
 			
            if(isset($pData["Id"])){$this->db->where("Id",$pData["Id"]);}  
            if(isset($pData["Receita"])){$this->db->where("Receita",$pData["Receita"]);}
            if(isset($pData["Despesas"])){$this->db->where("Despesas",$pData["Despesas"]);}
            if(isset($pData["Salario"])){$this->db->where("Salario",$pData["Salario"]);}
            if(isset($pData["Cartao"])){$this->db->where("Cartao",$pData["Cartao"]);}
            if(isset($pData["Poupanca"])){$this->db->where("Poupanca",$pData["Poupanca"]);}
            if(isset($pData["PoupancaFinal"])){$this->db->where("PoupancaFinal",$pData["PoupancaFinal"]);}
            if(isset($pData["SaldoMes"])){$this->db->where("SaldoMes",$pData["SaldoMes"]);}
            if(isset($pData["saldoFinal"])){$this->db->where("SaldoFinal",$pData["SaldoFinal"]);}
            
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
            
            if(isset($pData["OrderBy"]) != null){
                $this->db->order_by($pData["OrderBy"]);
            }
            
			return $this->db->get("geral")->result_array();		       
        }
        			
		function Buscar($pData){
            
			if(isset($pData["Id"])){$this->db->where("Id",$pData["Id"]);}  
            if(isset($pData["Receita"])){$this->db->where("Receita",$pData["Receita"]);}
            if(isset($pData["Despesas"])){$this->db->where("Despesas",$pData["Despesas"]);}
            if(isset($pData["Salario"])){$this->db->where("Salario",$pData["Salario"]);}
            if(isset($pData["Cartao"])){$this->db->where("Cartao",$pData["Cartao"]);}
            if(isset($pData["Poupanca"])){$this->db->where("Poupanca",$pData["Poupanca"]);}
            if(isset($pData["PoupancaFinal"])){$this->db->where("PoupancaFinal",$pData["PoupancaFinal"]);}
            if(isset($pData["SaldoMes"])){$this->db->where("SaldoMes",$pData["SaldoMes"]);}
            if(isset($pData["saldoFinal"])){$this->db->where("SaldoFinal",$pData["SaldoFinal"]);}
            if(isset($pData["Ano"])){$this->db->where("Ano",$pData["Ano"]);}
            if(isset($pData["Mes"])){$this->db->where("Mes",$pData["Mes"]);}
            
			return $this->db->get("geral")->row_array();		       
        }
        
        // -- INSERT --
        function Incluir($pData){
			$this->db->insert("geral", $pData);
		}
						
		// -- UPDATE --	
		function Atualizar($pData){
            $this->db->where 	('Id', $pData["Id"]);
			$this->db->update	("geral", $pData);
		}
        
        function Atualizar_Manual($pData){
            $this->db->where 	('Mes', $pData["Mes"]);
            $this->db->where 	('Ano', $pData["Ano"]);
			$this->db->update	("geral", $pData);
		}
    }