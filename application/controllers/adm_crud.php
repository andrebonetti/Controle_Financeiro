<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Adm_crud extends CI_Controller{
        
        // ----- INSERT TRANSACAO -----
        public function transacao_insert(){
        	
			$this->output->enable_profiler(TRUE);
			
            // -- VALIDACAO USUARIO --
            valida_usuario();
			  
            // -- NOVA CATEGORIA --
            $novaCategoria = novaCategoria();     
            // -- NOVA SUB CATEGORIA --
            $novaSubCategoria = novaSubCategoria();	

            /* -- DATA -- */
            $data   = transacao_getPosts();	
				
            // -- TYPE 1 = Transação Recorrente -- 
			if($data["type"] == 1){
				
				// -- BD INSERT -- 
                $this->transacoes_model->Incluir($data);
                
                // -- SALDO GERAL --
                geral_UpdateSaldo($data);
                 
			}
			// -- TYPE 2 = Transação Parcelada -- 
			if($data["type"] == 2){				
                
                $anoParcela = $data["ano"];
                $mesParcela = $data["mes"];
						
				for($n = 1;$n <= $data["p_total"] ; $n++){
					
					$dataParcela = $data;
											
					$dataParcela["ano"]		= $anoParcela;	
					$dataParcela["mes"]		= $mesParcela;										
					$dataParcela["parcela"] = $n;
											
					// -- BD INSERT -- 
                    $this->transacoes_model->Incluir($dataParcela);
                    
                    // -- SALDO GERAL --
                    geral_UpdateSaldo($dataParcela);
                    
					$mesParcela++;
					if($mesParcela > 12){
						$anoParcela++;
						$mesParcela = 1;
					}				
				}
                
			}		
            // -- TYPE 3 = Transação Simples -- 
			if($data["type"] == 3){	
		
				// -- BD INSERT -- 
                $this->transacoes_model->Incluir($data);
                
				// -- SALDO GERAL --
                geral_UpdateSaldo($data);
                
			}
			
            // -- MSG SUCESSO - REDIRECT
            $this->session->set_flashdata('msg-success',"Transação adicionada com sucesso!");
            redirect("content/month_content/".$data["ano"]."/".$data["mes"]."");
		} 
 	
        // ----- UPDATE TRANSACAO -----
		public function transacao_update($ano,$mes,$id){
				
			$this->output->enable_profiler(TRUE);
			
            /*VALIDACAO*/valida_usuario();
            
            $data            = transacao_getPosts();
            $dataBusca["id"] = $data["id"];
			$transacaoAtual  = $this->transacoes_model->Buscar($dataBusca);
            $data["type"] = $transacaoAtual["type"];

            $hasAlteracaoValor = false;
            
            // -- ALTERACAO VALOR --
            if(($data["valor"] != $transacaoAtual["valor"])){
                
                $hasAlteracaoValor = true;
                
                $valorDiferenca = $data["valor"] - $transacaoAtual["valor"]  ;
            }
            
            // -- ALTERACAO MÊS --
			if( (int)$data["mes"] != (int)$transacaoAtual["mes"]){
				
                // -- TRANSACAO RECORRENTE -- 
				if($transacaoAtual["type"] == 1){
                    
					$data["type"] = 3; 

                    // -- BD : Atualizar --
					$this->transacoes_model->Atualizar($data);
                    
                    $data["type"] = 1; 
					$data["id"] = null;
                    
                    $data["mes"] += 1;
                    if($data["mes"] == 13)
                    {
                        $data["mes"] = 12;
                        $data["ano"] += 1;
                    }
                    
                    // -- BD : Atualizar --
					$this->transacoes_model->Incluir($data);
                }
                else{
                    $this->transacoes_model->Atualizar($data);
                }
                
                // -- SALDO GERAL --
                $data["periodo_de"] = true;
				$lGeral = $this->geral_model->Listar($data);
                
                $count = 0;
				foreach($lGeral as $itemGeral){		                    
                    
                    $dataGeral["mes"]   = $itemGeral["mes"]; 
                    $dataGeral["ano"]   = $itemGeral["ano"];
                    
                    if($count == 0)
                    {
                        $dataGeral["valor"] = -$transacaoAtual["valor"]; 
                    }
                    
                    if($count == 1)
                    {
                        $dataGeral["valor"] = $transacaoAtual["valor"]; 
                    }
                     
                    geral_UpdateSaldo($dataParcela);
                    
                }
			}
            else{
                
                $this->transacoes_model->Atualizar($data);

                if($hasAlteracaoValor == true)
                {
                    $data["valor"] = $valorDiferenca; 
                    echo $data["type"];
  
                    // -- SALDO GERAL --
                    geral_UpdateSaldo($data);
                }
                
            }
	       
            // -- MSG SUCESSO - REDIRECT
            $this->session->set_flashdata('msg-success',"Transação alterada com sucesso!");
            redirect("content/month_content/".$ano."/".$mes);
		}
        
        // ----- DELETE TRANSACAO -----
		public function transacao_delete($ano,$mes,$id){
				
			$this->output->enable_profiler(TRUE);
            
            /*VALIDACAO*/valida_usuario();
			
            $data["id"] = $id;
            
			$transacaoAtual = $this->transacoes_model->Buscar($data);
            
            // -- TRANSACAO SIMPLES / PARCELADA
            if(($transacaoAtual["type"] == 3)or($transacaoAtual["type"] == 2))
            {
                $data["valor"] = 0;
            }
            //TRANSCAO RECORRENTE
            if($data["type"]  = 1)
            {
                if(($transacaoAtual["ano"] == $ano)&&($transacaoAtual["mes"] == $mes))
                {
                    $data["valor"] = 0;
                }
                else{
                    $data["type"] = 3;
                }
            
            }
            
            $this->transacoes_model->Atualizar($data);
            
            $data["ano"]   = $ano;
            $data["mes"]   = $mes;
            $data["type"]  = $transacaoAtual["type"];
            $data["valor"] = -$transacaoAtual["valor"];
	
			// -- SALDO GERAL --
            geral_UpdateSaldo($data);
            
            // -- MSG SUCESSO - REDIRECT
            $this->session->set_flashdata('msg-success',"Transacao deletada com sucesso!");
            redirect("content/month_content/".$ano."/".$mes);
			
		}
        
        // ----- INSERT CARTAO -----
        public function cartao_insert(){
        	
			$this->output->enable_profiler(TRUE);
			
            /*VALIDACAO*/valida_usuario();
             
            // -- NOVA CATEGORIA --
            $nova_categoria = nova_categoria();     
            // -- NOVA SUB CATEGORIA --
            $nova_sub_categoria = nova_sub_categoria();
            
            /* -- DATA -- */
            $data   = cartao_getPosts();	
				
            // -- TYPE 1 = Transação Recorrente -- 
			if($data["type"] == 1){
				
				// -- BD INSERT -- 
                $this->cartao_model->Incluir($data);
                
                // -- SALDO GERAL --
                geral_UpdateSaldo($dataGeral);

			}
            // -- TYPE 2 = Transação Parcelada --
            if($data["type"] == 2){
				
				$anoParcela = $data["ano"];
                $mesParcela = $data["mes"];
						
				for($n = 1;$n <= $data["p_total"] ; $n++){
										
					$dataParcela = $data;
											
					$dataParcela["ano"]		= $anoParcela;	
					$dataParcela["mes"]		= $mesParcela;										
					$dataParcela["parcela"] = $n;
											
					// -- BD INSERT -- 
                    $this->cartao_model->Incluir($dataParcela);
                    
					// -- SALDO GERAL --
                    geral_Update($dataParcela);
                    
					$mesParcela++;
					if($mesParcela > 12){
						$anoParcela++;
						$mesParcela = 1;
					}						
				}	
		
			}
            // -- TYPE 3 = Transação Simples -- 
			if($data["type"] == 3){
                
				// -- BD - INSERIR --
                $this->cartao_model->Incluir($data);

				// -- SALDO GERAL --
                geral_UpdateSaldo($data);
                
			}
            
            // -- MSG SUCESSO - REDIRECT
            $this->session->set_flashdata('msg-success',"Transação adicionada com sucesso!");
            redirect("content/month_content/".$data["ano"]."/".$data["mes"]."");

		} 
        
		// ----- UPDATE CARTAO -----
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
				
		// ----- UPDATE POUPANCA -----
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
            
    }