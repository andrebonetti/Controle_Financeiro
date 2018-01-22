<?php
	class Transacoes_model extends CI_Model {
				
        // -- SELECT --		
        function Listar($pData = null,$pOrderBy = null){
            
            if(isset($pData["Id"])){$this->db->where("transacoes.Id",$pData["id"]);}  
            if(isset($pData["IdUsuario"])){$this->db->where("transacoes.IdUsuario",$pData["IdUsuario"]);}
            if(isset($pData["IdCartao"])){$this->db->where("transacoes.IdCartao",$pData["IdCartao"]);}
            if(isset($pData["IdCategoria"])){$this->db->where("transacoes.IdCategoria",$pData["IdCategoria"]);}
            if(isset($pData["IdSubCategoria"])){$this->db->where("transacoes.IdSubCategoria",$pData["IdSubCategoria"]);}
            if(isset($pData["Descricao"])){$this->db->where("transacoes.Descricao",$pData["Descricao"]);}
            if(isset($pData["NumeroParcela"])){$this->db->where("transacoes.NumeroParcela",$pData["NumeroParcela"]);}
            if(isset($pData["TotalParcelas"])){$this->db->where("transacoes.TotalParcelas",$pData["TotalParcelas"]);}
            if(isset($pData["Valor"])){$this->db->where("transacoes.Valor",$pData["Valor"]);}
            if(isset($pData["CodigoTransacao"])){$this->db->where("transacoes.CodigoTransacao",$pData["CodigoTransacao"]);}
            if(isset($pData["NumeroParcela >"])){$this->db->where("transacoes.NumeroParcela >",$pData["NumeroParcela >"]);}
            if(isset($pData["NumeroParcela >="])){$this->db->where("transacoes.NumeroParcela >=",$pData["NumeroParcela >="]);}
            if(isset($pData["NumeroParcela <"])){$this->db->where("transacoes.NumeroParcela <",$pData["NumeroParcela <"]);}
            if(isset($pData["NumeroParcela <="])){$this->db->where("transacoes.NumeroParcela <=",$pData["NumeroParcela <="]);}

            if((isset($pData["isListaPorTipo"]))&&($pData["isListaPorTipo"] == true))
            {
                $this->db->where("valor !=","0");
                $this->db->where("IdTipoTransacao",$pData["IdTipoTransacao"]);
                
                //Se for Transação Recorrente Só busca pelo Dia
                if($pData["IdTipoTransacao"] == 1){
                    if(isset($pData["Dia"])){$this->db->where("Dia",$pData["Dia"]);}
                    
                    if(isset($pData["Ano"]) and isset($pData["Mes"])){
                        
                        $WhereData = 
                        "(
                            (
                                `Ano` =  ".$pData["Ano"]."
                                AND `Mes` <= ".$pData["Mes"]."

                            )
                            OR 
                            (`Ano` < ".$pData["Ano"].")
                        )";
                        
                        $this->db->where($WhereData);       
                    }
                    
                    date_default_timezone_set('America/Sao_Paulo');
                    
                    $this->db->where("AnoFim >=", $pData["Ano"]); 
                    $this->db->where("MesFim >=", $pData["Mes"]); 
                    
                }
                else{
                    if(isset($pData["Ano"])){$this->db->where("Ano",$pData["Ano"]);}
                    if(isset($pData["Mes"])){$this->db->where("Mes",$pData["Mes"]);}
                    if(isset($pData["Dia"])){$this->db->where("Dia",$pData["Dia"]);}
                }
            }
            else{
                if(isset($pData["Ano"])){$this->db->where("Ano",$pData["Ano"]);}
                if(isset($pData["Mes"])){$this->db->where("Mes",$pData["Mes"]);}
                if(isset($pData["Dia"])){$this->db->where("Dia",$pData["Dia"]);}
            }
               
            if(isset($pData["PreencherEntidadesFilhas"])){
                if($pData["PreencherEntidadesFilhas"] == true){
                    $this->db->join("categoria", "categoria.IdCategoria = transacoes.IdCategoria");
                    $this->db->join("sub_categoria", "sub_categoria.IdSubCategoria = transacoes.IdSubCategoria");
                }
            }

            $this->db->order_by("DataCompra");
            
            $this->db->from("transacoes");
            return $this->db->get()->result_array();
        }
        
        function Buscar($pData){ 
            
            if(isset($pData["Id"])){$this->db->where("transacoes.Id",$pData["Id"]);}  
            if(isset($pData["IdUsuario"])){$this->db->where("transacoes.IdUsuario",$pData["IdUsuario"]);}
            if(isset($pData["Ano"])){$this->db->where("transacoes.Ano",$pData["Ano"]);}
            if(isset($pData["Mes"])){$this->db->where("transacoes.Mes",$pData["mes"]);}
            if(isset($pData["Dia"])){$this->db->where("transacoes.Dia",$pData["dia"]);}
            if(isset($pData["IdCategoria"])){$this->db->where("transacoes.IdCategoria",$pData["IdCategoria"]);}
            if(isset($pData["IdSubCategoria"])){$this->db->where("transacoes.IdSubCategoria",$pData["IdSubCategoria"]);}
            if(isset($pData["Descricao"])){$this->db->where("transacoes.Descricao",$pData["Descricao"]);}
            if(isset($pData["NumeroParcela"])){$this->db->where("transacoes.NumeroParcela",$pData["NumeroParcela"]);}
            if(isset($pData["TotalParcelas"])){$this->db->where("transacoes.TotalParcelas",$pData["TotalParcelas"]);}
            if(isset($pData["Valor"])){$this->db->where("transacoes.Valor",$pData["Valor"]);}
            
            if(isset($pData["PreencherEntidadesFilhas"])){
                if($pData["PreencherEntidadesFilhas"] == true){
                    $this->db->join("Categoria", "Categoria.IdCategoria = transacoes.IdCategoria");
                    $this->db->join("Sub_Categoria", "Sub_Categoria.IdSubCategoria = transacoes.IdSubCategoria");
                }
            }
            
            $this->db->from("transacoes");   
            return $this->db->get()->row_array();    
        }

        function bucarUltimoCodigoTransacao(){
            $this->db->select_max('CodigoTransacao');
            $this->db->from("transacoes");
            return $this->db->get()->row_array();
        }
              
        // -- INSERT --
        function Incluir($pData){
            $pData["Id"] = null;
			$this->db->insert("transacoes", $pData);
		}
        
        // -- UPDATE --
        function Atualizar($pData){
			$this->db->where 	('Id', $pData["Id"]);
			$this->db->update	("transacoes", $pData);
		}
        
	}	