<?php
	class Contas_model extends CI_Model {

        function Buscar($pData){ 
            
            if(isset($pData["Usuario"]["Id"])){$this->db->where("contas.IdUsuario",$pData["Usuario"]["Id"]);}  
            

            $this->db->from("contas");   
            return $this->db->get()->row_array();    
        }

        function Listar($pParam){ 
            
            if(isset($pParam["Usuario"]["Id"])){$this->db->where("contas.IdUsuario",$pParam["Usuario"]["Id"]);} 

            $this->db->from("contas");   

            if( (!isset($pParam["HasInnerJoin"])) || ($pParam["HasInnerJoin"] == false) ){
                return $this->db->get()->result_array();    
            }else{
                $data = $this->db->get()->result_array();

                $ci = get_instance();
                $cont = 0;

                // foreach($data as $item){

                //     $data[$cont]["Banco"] = $ci->bancos_model->Buscar(array("Id"=>$item["IdBanco"])); 
                //     $cont ++;
                // }

                $data = $ci->bancos_model->ListarBancosContas($data);
                $data = $ci->contas_saldo_model->ListarSaldosContas($pParam,$data);

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