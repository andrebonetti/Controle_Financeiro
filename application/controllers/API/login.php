<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Login extends CI_Controller{
             
        public function validacao(){	
            
            /* ----- CONTENT ----- */
			$usuario["Login"] = $this->input->post("usuario");
			$usuario["Senha"] = md5($this->input->post("senha"));  
            $resultado        = $this->usuarios_model->valida_usuario($usuario);

			$atual_y = mdate("%Y");
			$atual_m = mdate("%m");

            if(util_isNotNull($resultado)){  
                $this->C_DataReturn["success"]      = true; 
                $this->C_DataReturn["Mensagem"]     = "";
            }else{
                $this->C_DataReturn["success"]      = false; 
                $this->C_DataReturn["Mensagem"]     = "Usuario e senha nao encontrado";
            }

            echo json_encode($this->C_DataReturn);
            //REDIRECT
			//login_result($resultado,"content/month_content/$atual_y/$atual_m");	     
		}
    
    }