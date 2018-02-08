<?php
	class Contas_model extends CI_Model {

        function Buscar($pData){ 
            
            if(isset($pData["Usuario"]["Id"])){$this->db->where("contas.IdUsuario",$pData["Usuario"]["Id"]);}  
            
            $this->db->from("contas");   
            return $this->db->get()->row_array();    
        }

        function Listar($pParam){ 
            
            if(isset($pParam["Usuario"]["Id"])){$this->db->where("contas.IdUsuario",$pParam["Usuario"]["Id"]);} 

            $this->db->order_by("Ordem");

            if( (!isset($pParam["HasInnerJoin"])) || ($pParam["HasInnerJoin"] == false) ){
                return $this->db->get("contas")->result_array();    
            }else{
                $data["Contas_Banco"] = $this->db->get("contas")->result_array();

                $ci = get_instance();
                $cont = 0;

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