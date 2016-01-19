<?php
	class Usuarios_model extends CI_Model {
		
		function valida_usuario($usuario,$senha){
			$this->db->where("usuario",$usuario);
			$this->db->where("senha",$senha);
			return $this->db->get("usuarios")->row_array();
		}
        
        function lista_usuarios(){
            $this->db->where("id !=","1");
			return $this->db->get("usuarios")->result_array();
		}
		
		function busca_usuario($usuario){
            $this->db->where("usuario",$usuario);
			return $this->db->get("usuarios")->row_array();
		}
}