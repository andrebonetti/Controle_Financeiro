<?php

    function login_result($resultado,$redirect){
       $ci = get_instance();    
        
	   if (!empty ($resultado)){
           $ci->session->set_userdata("usuario",$resultado['Id']);	
           redirect($redirect);
        }
        else{
            $ci->session->set_flashdata('msg-error',"Usuário e senha inválidos");
            redirect("login");
        }
	}
	
	function valida_usuario(){
		$ci = get_instance();
		$usuario = $ci->session->userdata('usuario');
		if(empty($usuario)){
			$ci->session->set_flashdata('msg-error','Efetue o login para ter acesso a essa página.');
			redirect("");
		}
	}