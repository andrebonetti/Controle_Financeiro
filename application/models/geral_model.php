<?php
	class Geral_model extends CI_Model {
			
        // -- SELECT -- 
		function Listar($pData,$pOrderBy = null){
 			
            if(isset($pData["id"])){$this->db->where("id",$pData["id"]);}  
            if(isset($pData["receita"])){$this->db->where("receita",$pData["receita"]);}
            if(isset($pData["despesas"])){$this->db->where("despesas",$pData["despesas"]);}
            if(isset($pData["salario"])){$this->db->where("salario",$pData["salario"]);}
            if(isset($pData["cartao"])){$this->db->where("cartao",$pData["cartao"]);}
            if(isset($pData["poupanca"])){$this->db->where("poupanca",$pData["poupanca"]);}
            if(isset($pData["poupanca_final"])){$this->db->where("poupanca_final",$pData["poupanca_final"]);}
            if(isset($pData["dizimo"])){$this->db->where("dizimo",$pData["dizimo"]);}
            if(isset($pData["saldo_mes"])){$this->db->where("saldo_mes",$pData["saldo_mes"]);}
            if(isset($pData["saldo_final"])){$this->db->where("saldo_final",$pData["saldo_final"]);}
            
            if(isset($pData["periodo_de"])){
                if($pData["periodo_de"] == true){
                    if(isset($pData["mes"])){$this->db->where("mes >=",$pData["mes"]);}
                    if(isset($pData["ano"])){$this->db->where("ano >=",$pData["ano"]);}
                }
            }
            if(isset($pData["periodo_ate"])){
                if($pData["periodo_ate"] == true){
                    if(isset($pData["mes"])){$this->db->where("mes <=",$pData["mes"]);}
                    if(isset($pData["ano"])){$this->db->where("ano <=",$pData["ano"]);}
                }
            }
            if((!isset($pData["periodo_de"]))&&(!isset($pData["periodo_ate"]))){
                if(isset($pData["ano"])){$this->db->where("ano",$pData["ano"]);}
                if(isset($pData["mes"])){$this->db->where("mes",$pData["mes"]);}
            }
            
            if($pOrderBy != null){
                $this->db->order_by($pOrderBy);
            }
            
			return $this->db->get("geral")->result_array();		       
        }
        			
		function Buscar($pData){
            
			if(isset($pData["id"])){$this->db->where("id",$pData["id"]);}  
            if(isset($pData["receita"])){$this->db->where("receita",$pData["receita"]);}
            if(isset($pData["despesas"])){$this->db->where("despesas",$pData["despesas"]);}
            if(isset($pData["salario"])){$this->db->where("salario",$pData["salario"]);}
            if(isset($pData["cartao"])){$this->db->where("cartao",$pData["cartao"]);}
            if(isset($pData["poupanca"])){$this->db->where("poupanca",$pData["poupanca"]);}
            if(isset($pData["poupanca_final"])){$this->db->where("poupanca_final",$pData["poupanca_final"]);}
            if(isset($pData["dizimo"])){$this->db->where("dizimo",$pData["dizimo"]);}
            if(isset($pData["saldo_mes"])){$this->db->where("saldo_mes",$pData["saldo_mes"]);}
            if(isset($pData["saldo_final"])){$this->db->where("saldo_final",$pData["saldo_final"]);}
            if(isset($pData["ano"])){$this->db->where("ano",$pData["ano"]);}
            if(isset($pData["mes"])){$this->db->where("mes",$pData["mes"]);}
            
			return $this->db->get("geral")->row_array();		       
        }
						
		// -- UPDATE --	
		function Atualizar($pData){
            $this->db->where 	('id', $pData["id"]);
			$this->db->update	("geral", $pData);
		}
    }