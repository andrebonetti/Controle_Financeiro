<?php
	class Cartao_model extends CI_Model {
				
		// -- SELECT --		
        function ListarFaturaSimplesParcelada($pData = null,$pOrderBy = null){
                      
            $this->db->where("valor !=","0");
            
            if(isset($pData["Ano"])){$this->db->where("Ano",$pData["Ano"]);}
            if(isset($pData["Mes"])){$this->db->where("Mes",$pData["Mes"]);}

            if(isset($pData["PreencherEntidadesFilhas"])){
                if($pData["PreencherEntidadesFilhas"] == true){
                    $this->db->join("Categoria", "Categoria.IdCategoria = cartao_de_credito.IdCategoria");
                    $this->db->join("Sub_Categoria", "Sub_Categoria.IdSubCategoria = cartao_de_credito.IdSubCategoria");
                }
            }
            
            $this->db->from("cartao_de_credito");
            return $this->db->get()->result_array();
        }
        
        function ListarFaturaRecorrente($pData = null,$pOrderBy = null){
                      
            $this->db->where("valor !=","0");
            $this->db->where("IdTipoTransacao",1);

            if(isset($pData["PreencherEntidadesFilhas"])){
                if($pData["PreencherEntidadesFilhas"] == true){
                    $this->db->join("Categoria", "Categoria.IdCategoria = cartao_de_credito.IdCategoria");
                    $this->db->join("Sub_Categoria", "Sub_Categoria.IdSubCategoria = cartao_de_credito.IdSubCategoria");
                }
            }
            
            $this->db->where("Ano <=", $pData["Ano"]);
            $this->db->where("Mes <=", $pData["Mes"]);
            $this->db->where("AnoFim >=", $pData["Ano"]);
            $this->db->where("MesFim >=", $pData["Mes"]);
            
            $this->db->from("cartao_de_credito");
            return $this->db->get()->result_array();
        }
        
        function Listar($pData){ 
            
            if(isset($pData["Id"])){$this->db->where("cartao_de_credito.Id",$pData["Id"]);}  
            if(isset($pData["IdUsuario"])){$this->db->where("cartao_de_credito.IdUsuario",$pData["IdUsuario"]);}
            if(isset($pData["Dia"])){$this->db->where("cartao_de_credito.Dia",$pData["dia"]);}
            if(isset($pData["IdCategoria"])){$this->db->where("cartao_de_credito.IdCategoria",$pData["IdCategoria"]);}
            if(isset($pData["IdSubCategoria"])){$this->db->where("cartao_de_credito.IdSubCategoria",$pData["IdSubCategoria"]);}
            if(isset($pData["Descricao"])){$this->db->where("cartao_de_credito.Descricao",$pData["Descricao"]);}
            if(isset($pData["NumeroParcela"])){$this->db->where("cartao_de_credito.NumeroParcela",$pData["NumeroParcela"]);}
            if(isset($pData["TotalParcelas"])){$this->db->where("cartao_de_credito.TotalParcelas",$pData["TotalParcelas"]);}
            if(isset($pData["Valor"])){$this->db->where("cartao_de_credito.Valor",$pData["Valor"]);}
            
            if(isset($pData["PeriodoDe"])){
                $this->db->where("cartao_de_credito.Ano >=",$pData["Ano"]);
                $this->db->where("cartao_de_credito.Mes >=",$pData["Mes"]);               
            }
            else
            {
                if(isset($pData["Ano"])){$this->db->where("cartao_de_credito.Ano",$pData["Ano"]);}
                if(isset($pData["Mes"])){$this->db->where("cartao_de_credito.Mes",$pData["Mes"]);}
            }
            
            if(isset($pData["PreencherEntidadesFilhas"])){
                if($pData["PreencherEntidadesFilhas"] == true){
                    $this->db->join("Categoria", "Categoria.IdCategoria = cartao_de_credito.IdCategoria");
                    $this->db->join("Sub_Categoria", "Sub_Categoria.IdSubCategoria = cartao_de_credito.IdSubCategoria");
                }
            }
            
            $this->db->from("cartao_de_credito");   
            return $this->db->get()->result_array();    
        }
        
        function Buscar($pData){ 
            
            if(isset($pData["Id"])){$this->db->where("cartao_de_credito.Id",$pData["Id"]);}  
            if(isset($pData["IdUsuario"])){$this->db->where("cartao_de_credito.IdUsuario",$pData["IdUsuario"]);}
            if(isset($pData["Ano"])){$this->db->where("cartao_de_credito.Ano",$pData["Ano"]);}
            if(isset($pData["Mes"])){$this->db->where("cartao_de_credito.Mes",$pData["mes"]);}
            if(isset($pData["Dia"])){$this->db->where("cartao_de_credito.Dia",$pData["dia"]);}
            if(isset($pData["IdCategoria"])){$this->db->where("cartao_de_credito.IdCategoria",$pData["IdCategoria"]);}
            if(isset($pData["IdSubCategoria"])){$this->db->where("cartao_de_credito.IdSubCategoria",$pData["IdSubCategoria"]);}
            if(isset($pData["Descricao"])){$this->db->where("cartao_de_credito.Descricao",$pData["Descricao"]);}
            if(isset($pData["NumeroParcela"])){$this->db->where("cartao_de_credito.NumeroParcela",$pData["NumeroParcela"]);}
            if(isset($pData["TotalParcelas"])){$this->db->where("cartao_de_credito.TotalParcelas",$pData["TotalParcelas"]);}
            if(isset($pData["Valor"])){$this->db->where("cartao_de_credito.Valor",$pData["Valor"]);}

            if(isset($pData["PreencherEntidadesFilhas"])){
                if($pData["PreencherEntidadesFilhas"] == true){
                    $this->db->join("Categoria", "Categoria.IdCategoria = cartao_de_credito.IdCategoria");
                    $this->db->join("Sub_Categoria", "Sub_Categoria.IdSubCategoria = cartao_de_credito.IdSubCategoria");
                }
            }
            
            $this->db->from("cartao_de_credito");   
            return $this->db->get()->row_array();    
        }
        
        // -- INSERT --
        function Incluir($pData){
			$this->db->insert("cartao_de_credito", $pData);
		}
       
        // -- UPDATE --
        function Atualizar($pData){
			$this->db->where 	('Id', $pData["Id"]);
			$this->db->update	("cartao_de_credito", $pData);
		}
    }