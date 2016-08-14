<?php
	class Transacoes_model extends CI_Model {
				
        // -- SELECT --		
        function Listar($pData = null,$pOrderBy = null){
            
            if(isset($pData["id"])){$this->db->where("id",$pData["id"]);} 
            if(isset($pData["type"])){$this->db->where("type",$pData["type"]);}
            if(isset($pData["usuario"])){$this->db->where("usuario",$pData["usuario"]);}           
            
            if(isset($pData["categoria"])){$this->db->where("categoria",$pData["categoria"]);}
            if(isset($pData["sub_categoria"])){$this->db->where("sub_categoria",$pData["sub_categoria"]);}
            if(isset($pData["descricao"])){$this->db->where("descricao",$pData["descricao"]);}
            if(isset($pData["status"])){$this->db->where("status",$pData["status"]);}
            if(isset($pData["parcela"])){$this->db->where("parcela",$pData["parcelas"]);}
            if(isset($pData["p_total"])){$this->db->where("p_total",$pData["p_total"]);}
            if(isset($pData["valor"])){$this->db->where("valor",$pData["valor"]);}
            
            if((isset($pData["isListaPorTipo"]))&&($pData["isListaPorTipo"] == true))
            {
                $this->db->where("valor !=","0");
                
                //Se for Transação Recorrente Só busca pelo Dia
                if($pData["type"] == 1){
                    if(isset($pData["dia"])){$this->db->where("dia",$pData["dia"]);}
                }
                else{
                    if(isset($pData["ano"])){$this->db->where("ano",$pData["ano"]);}
                    if(isset($pData["mes"])){$this->db->where("mes",$pData["mes"]);}
                    if(isset($pData["dia"])){$this->db->where("dia",$pData["dia"]);}
                }
            }
            else{
                if(isset($pData["ano"])){$this->db->where("ano",$pData["ano"]);}
                if(isset($pData["mes"])){$this->db->where("mes",$pData["mes"]);}
                if(isset($pData["dia"])){$this->db->where("dia",$pData["dia"]);}
            }
               
            if(isset($pData["PreencherEntidadesFilhas"])){
                if($pData["PreencherEntidadesFilhas"] == true){
                    $this->db->join("categoria", "categoria.id_categoria = transacoes.categoria");
                    $this->db->join("sub_categoria", "sub_categoria.id_sub_categoria = transacoes.sub_categoria");
                }
            }
            
            $this->db->from("transacoes");
            return $this->db->get()->result_array();
        }
        
        function Buscar($pData){ 
            
            if(isset($pData["id"])){$this->db->where("id",$pData["id"]);}  
            if(isset($pData["usuario"])){$this->db->where("usuario",$pData["usuario"]);}
            if(isset($pData["ano"])){$this->db->where("ano",$pData["ano"]);}
            if(isset($pData["mes"])){$this->db->where("mes",$pData["mes"]);}
            if(isset($pData["dia"])){$this->db->where("dia",$pData["dia"]);}
            if(isset($pData["categoria"])){$this->db->where("categoria",$pData["categoria"]);}
            if(isset($pData["sub_categoria"])){$this->db->where("sub_categoria",$pData["sub_categoria"]);}
            if(isset($pData["descricao"])){$this->db->where("descricao",$pData["descricao"]);}
            if(isset($pData["status"])){$this->db->where("status",$pData["status"]);}
            if(isset($pData["parcela"])){$this->db->where("parcela",$pData["parcelas"]);}
            if(isset($pData["p_total"])){$this->db->where("p_total",$pData["p_total"]);}
            if(isset($pData["valor"])){$this->db->where("valor",$pData["valor"]);}
            
            if(isset($pData["PreencherEntidadesFilhas"])){
                if($pData["PreencherEntidadesFilhas"] == true){
                    $this->db->join("categoria", "categoria.id_categoria = transacoes.categoria");
                    $this->db->join("sub_categoria", "sub_categoria.id_sub_categoria = transacoes.sub_categoria");
                }
            }
            
            $this->db->from("transacoes");   
            return $this->db->get()->row_array();    
        }
              
        // -- INSERT --
        function Incluir($pData){
			$this->db->insert("transacoes", $pData);
		}
        
        // -- UPDATE --
        function Atualizar($pData){
			$this->db->where 	('id', $pData["id"]);
			$this->db->update	("transacoes", $pData);
		}
        
	}	