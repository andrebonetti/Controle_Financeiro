<?php
	class Contas_model extends CI_Model {

        function Buscar($pData){ 
            
            if(isset($pData["Id"])){$this->db->where("contas.Id",$pData["Id"]);} 
            if(isset($pData["Usuario"]["Id"])){$this->db->where("contas.IdUsuario",$pData["Usuario"]["Id"]);}  
            
            $this->db->from("contas");   
            return $this->db->get()->row_array();    
        }

        function Listar($pParam = array()){ 
            
            if(isset($pParam["Id"])){$this->db->where("contas.Id",$pParam["Id"]);} 
            if(isset($pParam["Usuario"]["Id"])){$this->db->where("contas.IdUsuario",$pParam["Usuario"]["Id"]);} 
            if( (isset($pParam["Ano"])) && isset($pParam["Mes"]) ) {
                // $this->db->where("contas.MesInicio <=",$pParam["Mes"]);
                // $this->db->where("contas.AnoInicio >=",$pParam["Ano"]);

                $WhereData = 
                "(
                    (
                        `AnoInicio` =  ".$pParam["Ano"]."
                        AND `MesInicio` <= ".$pParam["Mes"]."

                    )
                    OR 
                    (`AnoInicio` < ".$pParam["Ano"].")
                )";
                
                $this->db->where($WhereData); 
                
            } 

            $this->db->order_by("Ordem");

            if( (!isset($pParam["HasInnerJoin"])) || ($pParam["HasInnerJoin"] == false) ){
                return $this->db->get("contas")->result_array();    
            }else{

                $ci         = get_instance();
                $contas     = $this->db->get("contas")->result_array();

                $data["Contas_Banco"] = util_gerarIndiceArray($contas,"Id");
                $data["Contas_Banco"] = $ci->bancos_model->ListarBancosContas($data["Contas_Banco"]);
                $data["Contas_Banco"] = $ci->contas_saldo_model->ListarSaldosContas($pParam,$data["Contas_Banco"]);

                return $data;
            }
        }
           
        // -- INSERT --
        function Incluir($pData){
			$this->db->insert("contas", $pData);
		}
       
        // -- UPDATE --
        function Atualizar($pData){
			$this->db->where 	('Id', $pData["Id"]);
			$this->db->update	("contas", $pData);
		}
    }