<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Login extends CI_Controller{
        
        public function index(){	
		  	/*VIEW*/$this->load->template("signin.php",$content = array());           
	    }
        
        public function validacao(){	
            
            /* ----- CONTENT ----- */
			$usuario["Login"] = $this->input->post("usuario");
			$usuario["Senha"] = md5($this->input->post("senha"));  
            $resultado        = $this->usuarios_model->valida_usuario($usuario);

			$atual_y = mdate("%Y");
			$atual_m = mdate("%m");
						
            /*REDIRECT*/login_result($resultado,"content/month_content/$atual_y/$atual_m");	     
		}
    
    }