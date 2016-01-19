<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Adm_crud extends CI_Controller{
        
        /*INSERT*/
        public function transacao_insert(){
        	
			$this->output->enable_profiler(TRUE);
			
            /*VALIDACAO*/valida_usuario();
			  
            /*NOVA CATEGORIA*/
            	$nova_categoria = nova_categoria();     
            /*NOVA SUB CATEGORIA*/
            	$nova_sub_categoria = nova_sub_categoria();	
				$id_nova_sub_categoria  = $this->transacoes_model->get_id_sub_categoria($nova_sub_categoria); 
			           
			$type					= $this->input->post("type");
			
			/*DATA*/
			$data["p_total"]		= $this->input->post("total");
			
			if($type == 1){
				
				$data["ano"]  		= $this->input->post("ano");	
				$data["mes"]  		= $this->input->post("mes");
				
				$lista_geral  	= $this->geral_model->lista_geral($data["ano"] ,$data["mes"]);	
				$data 			= crud_transacao($type);	
								
				/*BD-CRUD*/$this->crud_model->insert("transacoes",$data);
				
				foreach($lista_geral as $lista){				
					/*SALDO GERAL*/crud_geral($lista["ano"],$lista["mes"],$data["categoria"],$data["valor"]);
				}
			}
			if($type == 2){							
				$ano  				= $this->input->post("ano");	
				$mes  				= $this->input->post("mes");
				$p_total			= $this->input->post("total");
				
				for($n = 1;$n <= $p_total ; $n++){
					
					$data = crud_transacao($type);
											
					$data["ano"]			= $ano;	
					$data["mes"]			= $mes;										
					$data["parcela"] 		= $n;
					$data["p_total"] 		= $p_total;
											
					/*BD-CRUD*/$this->crud_model->insert("transacoes",$data);
					/*SALDO GERAL*/	crud_geral($data["ano"],$data["mes"],$data["categoria"],$data["valor"]);			
						
					$mes++;
					if($mes > 12){
						$ano++;
						$mes = 1;
					}				
				}
			}		
			if(empty($type)){
				$data = crud_transacao($type);	
				
				$data["ano"]  		= $this->input->post("ano");	
				$data["mes"]  		= $this->input->post("mes");
				$data["type"]  		= "3";
				
				/*BD-CRUD*/$this->crud_model->insert("transacoes",$data);
				/*SALDO GERAL*/crud_geral($data["ano"],$data["mes"],$data["categoria"],$data["valor"]);
			}
				
			$ano  		= 	$this->input->post("ano");	
			$mes  		= 	$this->input->post("mes");
			
            /*MSG*/$this->session->set_flashdata('msg-success',"Transação adicionada com sucesso!");
            /*REDIRECT*/redirect("content/month_content/".$ano."/".$mes."");
		} 
 
		/*INSERT CARTAO*/
        public function cartao_insert(){
        	
			$this->output->enable_profiler(TRUE);
			
            /*VALIDACAO*/valida_usuario();
             
            /*NOVA CATEGORIA*/
            	$nova_categoria = nova_categoria();     
            /*NOVA SUB CATEGORIA*/
            	$nova_sub_categoria = nova_sub_categoria();	
				$id_nova_sub_categoria  = $this->transacoes_model->get_id_sub_categoria($nova_sub_categoria); 
					                   
			$type					= $this->input->post("type");
			
			if($type != 2){
				$data = crud_cartao($type);	
				
				$data["ano"]  		= $this->input->post("ano");	
				$data["mes"]  		= $this->input->post("mes");
				
				/*BD-CRUD*/$this->crud_model->insert("cartao_de_credito",$data);
				/*BD-CRUD*/crud_geral_cartao($data["ano"],$data["mes"],$data["valor"]);
				/*SALDO GERAL*/crud_geral($data["ano"],$data["mes"],$data["categoria"],$data["valor"]);	
			}
			
			if($type == 2){
				
				$ano  				= $this->input->post("ano");	
				$mes  				= $this->input->post("mes");
				$p_total			= $this->input->post("total");
				
				for($n = 1;$n <= $p_total ; $n++){
					
					$data = crud_cartao($type);
											
					$data["ano"]			= $ano;	
					$data["mes"]			= $mes;										
					$data["parcela"] 		= $n;
					$data["p_total"] 		= $p_total;
											
					/*BD-CRUD*/$this->crud_model->insert("cartao_de_credito",$data);
					/*BD-CRUD*/crud_geral_cartao($data["ano"],$data["mes"],$data["valor"]);
					/*SALDO GERAL*/crud_geral($data["ano"],$data["mes"],$data["categoria"],$data["valor"]);			
						
					$mes++;
					if($mes > 12){
						$ano++;
						$mes = 1;
					}				
				}	
		
			}
			
			$ano  		= 	$this->input->post("ano");	
			$mes  		= 	$this->input->post("mes");
			
            /*MSG*/$this->session->set_flashdata('msg-success',"Transação adicionada com sucesso!");
            /*REDIRECT*/redirect("content/month_content/".$ano."/".$mes."");
		} 
        
		/*UPDATE CARTAO*/
        public function cartao_update($ano,$mes,$id){
        	
            $this->output->enable_profiler(TRUE);
            
            /*VALIDACAO*/valida_usuario();
			
			
			$cartao_atual 			= $this->transacoes_model->get_where("cartao_de_credito","id",$id);
					
            $data["categoria"]  	= $this->input->post("categoria");
			$data["sub_categoria"]  = $this->input->post("sub_categoria");
            $data["descricao"]      = $this->input->post("descricao");
			$data["valor"]          = valor_decimal($this->input->post("valor"));	
			
			if(($cartao_atual["valor"]) != $data["valor"]){

				$diferenca 		= $data["valor"] - $cartao_atual["valor"]  ;
				$lista_geral 	= $this->geral_model->lista_geral($ano,$mes);
				
            	if($cartao_atual["type"] == "1"){
						
            		//FISH OLD
            		if(($mes - 1) ==  0){
            			$mes_anterior = 12;
						$ano_anterior = $ano - 1;
					}
					else{
						$mes_anterior = $mes - 1;
						$ano_anterior = $ano;
					}
            		$cartao_atual["mes_fim"] = $mes_anterior;
            		$cartao_atual["ano_fim"] = $ano_anterior;
					
            		/*BD-CRUD*/$this->crud_model->update("cartao_de_credito",$id,$cartao_atual);	
				
					//NEW
					$cartao_atual["valor"] = $data["valor"];
					$cartao_atual["ano"] = $ano;
					$cartao_atual["mes"] = $mes;
					$cartao_atual["id"] = '';
					$cartao_atual["mes_fim"] = "";
            		$cartao_atual["ano_fim"] = "";
					
					/*BD-CRUD*/$this->crud_model->insert("cartao_de_credito",$cartao_atual);
													
					foreach($lista_geral as $lista){
						/*SALDO GERAL*/crud_geral($lista["ano"],$lista["mes"],"cartao",$diferenca);
					}	
						
				}
				else{
					/*SALDO GERAL*/crud_geral($data["ano"],$data["mes"],"cartao",$diferenca);	
				}
              }
			
			/*MSG*/$this->session->set_flashdata('msg-success',"Transacao atualizada com sucesso!");
			/*REDIRECT*///redirect("content/month_content/".$ano."/".$mes);
		} 
				
        /*UPDATE*/
		public function transacao_update($ano,$mes,$id){
				
			$this->output->enable_profiler(TRUE);
			
            /*VALIDACAO*/valida_usuario();
            
			$transacao_atual 		= $this->transacoes_model->get_where("transacoes","id",$id);
			$salario  	  			= $this->input->post("total_salario"); 
						
            $data["ano"]  		    = $this->input->post("ano");	
            $data["mes"]  		    = $this->input->post("mes");
            $data["dia"]  		    = $this->input->post("dia");
			$data["categoria"]  	= $this->input->post("categoria");
            $data["sub_categoria"]  = $this->input->post("sub_categoria");
            $data["descricao"]      = $this->input->post("descricao");
			$data["valor"]          = valor_decimal($this->input->post("valor"));	
			
			if(($data["dia"] != 0)&&($transacao_atual["type"]) == "4"){
				$data["type"] = "3";
				/*SALDO GERAL*/crud_geral($data["ano"],$data["mes"],$data["categoria"],$data["valor"]);
			}
			
			if( (int)$data["mes"] != (int)$transacao_atual["mes"]){
				
				if($transacao_atual["type"] == 1){
					$transacao_atual["type"] = 3; 
					$transacao_atual["id"] = ""; 
					var_dump($transacao_atual);
					/*BD-CRUD*/$this->crud_model->insert("transacoes",$transacao_atual);			
				}
				else{		
					/*SALDO GERAL*/crud_geral($transacao_atual["ano"],$transacao_atual["mes"],$data["categoria"],-$transacao_atual["valor"]);
					/*SALDO GERAL*/crud_geral($data["ano"],$data["mes"],$data["categoria"],$data["valor"]);	
				}
			}
	
            if(($transacao_atual["valor"]) != $data["valor"]){

				$diferenca 		= $data["valor"] - $transacao_atual["valor"]  ;
				$lista_geral 	= $this->geral_model->lista_geral($data["ano"],$data["mes"]);
				
				echo "diferença : ".$diferenca."<br>";
				
            	if($transacao_atual["type"] == "1"){
            		
            		$data["mes"] = $data["mes"] + 1;
					$data["ano"] = $ano;
					
					if($data["mes"] > 12){
						$data["ano"] = $data["ano"] + 1;
						$data["mes"] = 1;
					}	
										
					$transacao_atual["id"] 		= "";	
					$transacao_atual["type"] 	= "3";	
					$transacao_atual["ano"] 	= $this->input->post("ano");
					$transacao_atual["mes"] 	= $this->input->post("mes");
					$transacao_atual["valor"] 	= $this->input->post("valor");	
						
					/*BD-CRUD*/$this->crud_model->insert("transacoes",$transacao_atual);

					foreach($lista_geral as $lista){
						/*SALDO GERAL*/crud_geral($lista["ano"],$lista["mes"],$data["categoria"],$diferenca);
					}	
				}
				else{
					/*SALDO GERAL*/crud_geral($data["ano"],$data["mes"],$data["categoria"],$diferenca);	
				}
            }
            
			/*BD-CRUD*/$this->crud_model->update("transacoes",$id,$data);
            /*MSG*/$this->session->set_flashdata('msg-success',"Transação alterada com sucesso!");
            /*REDIRECT*/redirect("content/month_content/".$ano."/".$mes);
		}

		/*UPDATE*/
		public function poupanca_update($ano,$mes){
				
			$this->output->enable_profiler(TRUE);
			
            /*VALIDACAO*/valida_usuario();
			
			$geral_atual 		= $this->geral_model->get_where2("ano",$ano,"mes",$mes);
            	
			$poupanca	=	valor_decimal($this->input->post("valor"));	
			
			$diferenca  = $poupanca - $geral_atual["poupanca"];
						
			/*BD-CRUD*/crud_poupanca($ano,$mes,$diferenca);
									          
            /*MSG*/$this->session->set_flashdata('msg-success',"Transação alterada com sucesso!");
            /*REDIRECT*/redirect("content/month_content/".$ano."/".$mes);
		}
        
        /*DELETE*/
		public function transacao_delete($ano,$mes,$id){
				
			$this->output->enable_profiler(TRUE);
            
            /*VALIDACAO*/valida_usuario();
			
			$transacao_atual = $this->transacoes_model->get_where("transacoes","id",$id);
			
			/*SALDO GERAL*/crud_geral($transacao_atual["ano"],$transacao_atual["mes"],$transacao_atual["categoria"],-$transacao_atual["valor"]);
			/*BD-CRUD*/$this->crud_model->delete("transacoes",$id);
            /*MSG*/$this->session->set_flashdata('msg-success',"Transacao deletada com sucesso!");
            
			/*REDIRECT*/redirect("content/month_content/".$ano."/".$mes);
			
		}
        
    }