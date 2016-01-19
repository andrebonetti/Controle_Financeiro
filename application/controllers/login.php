<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Login extends CI_Controller{
        
        public function index(){	
		  	/*VIEW*/$this->load->template("signin.php",$content = array());           
	    }
        
        public function validacao(){	
            
            /* ----- CONTENT ----- */
			$usuario  = $this->input->post("usuario");
			$senha	  = md5($this->input->post("senha"));  
            $result = $this->usuarios_model->valida_usuario($usuario,$senha);
			
			$atual_y = mdate("%Y");
			$atual_m = mdate("%m");
						
            /*REDIRECT*/login_result($result,"content/month_content/$atual_y/$atual_m");	     
		}
    
    }