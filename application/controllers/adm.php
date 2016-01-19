<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Adm extends CI_Controller{
        
        public function signin(){	      
            /* ----- CONTENT ----- */
			$usuario  = $this->input->post("usuario");
			$senha	  = md5($this->input->post("senha"));	
            $result = $this->usuarios_model->valida_usuario($usuario,$senha); 	  
            /*REDIRECT*/login_result($result);	     
		}
        
        public function home(){   	
			/*VALIDACAO*/valida_usuario();	
			   	  
            /* ----- CONTENT ----- */
		    $content = array();
		      
            /*VIEW*/$this->load->template_adm("adm/adm_home.php",$content);
		}
		 
        public function signout(){ 
			/*UNSET*/$this->session->unset_userdata("usuario");
			/*REDIRECT*/redirect("home");   
		}	
        
    }