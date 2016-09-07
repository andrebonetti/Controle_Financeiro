<?php
	class Usuarios_model extends CI_Model {
		
		function valida_usuario($usuario){
			$this->db->where("Login",$usuario["Login"]);
			$this->db->where("Senha",$usuario["Senha"]);
			return $this->db->get("usuarios")->row_array();
		}
        
        function lista_usuarios(){
            $this->db->where("Id !=","1");
			return $this->db->get("usuarios")->result_array();
		}
		
		function Buscar($usuario){
            $this->db->where("Id",$usuario["Id"]);
			return $this->db->get("usuarios")->row_array();
		}
}