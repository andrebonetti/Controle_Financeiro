<?php

    class Cartoes_Fatura_model extends CI_Model {

        // -- SELECT --
        function Listar($pData = null,$pOrderBy = null){

            if(isset($pData["IdGeral"])){$this->db->where("cartoes_fatura.IdGeral",$pData["IdGeral"]);}  
            if(isset($pData["Cartoes"])){
            
                $this->db->join("cartoes", "cartoes.Id = cartoes_fatura.IdCartao");
                
                if(isset($pData["Cartoes"]["IdUsuario"])){$this->db->where("cartoes.IdUsuario",$pData["Cartoes"]["IdUsuario"]);}
            }
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

            $this->db->from("cartoes_fatura");
            return $this->db->get()->result_array();

        }
        
        function Buscar($pData){

            //if(isset($pData["IdCartao"])){$this->db->where("cartoes_fatura.IdCartao",$pData["IdCartao"]);}  
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

            if(isset($pData["Cartoes"])){
            
                $this->db->join("cartoes", "cartoes.Id = cartoes_fatura.IdCartao");
                
                if(isset($pData["Cartoes"]["IdUsuario"])){$this->db->where("cartoes.IdUsuario",$pData["Cartoes"]["IdUsuario"]);}
            }
            
            $this->db->from("cartoes_fatura");
            return $this->db->get()->row_array();

        }

        // -- UPDATE --
        function Atualizar($pData){
			$this->db->where 	('Id', $pData["Id"]);
			$this->db->update	("cartoes_fatura", $pData);
		}
        
        // -- INSERT --
        function Incluir($pData){
			$this->db->insert("cartoes_fatura", $pData);
		}
        
    }