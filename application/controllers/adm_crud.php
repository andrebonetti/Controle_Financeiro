<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Adm_crud extends CI_Controller{
        
        // ----- INSERT TRANSACAO -----
        public function transacao_insert(){
        	
			$this->output->enable_profiler(TRUE);
			
            // -- VALIDACAO USUARIO --
            valida_usuario();
			  
            /* -- DATA -- */
            $data                   = transacao_getPosts();	
            $isTransacaoValidada    = ValidaEntidadeTransacao($data);
            
            if($isTransacaoValidada = true)
            {
                // DATA INCLUSAO
                date_default_timezone_set('America/Sao_Paulo');
                $data["DataInclusao"] = date('Y-m-d H:i:s');
                
                // -- TYPE 1 = Transação Recorrente -- 
                if($data["IdTipoTransacao"] == 1){

                    $data["AnoFim"] = 2050;
                    $data["MesFim"] = 12;
                    
                    // -- BD INSERT -- 
                    $this->transacoes_model->Incluir($data);

                    // -- SALDO GERAL --
                    geral_UpdateSaldo($data);

                }
                // -- TYPE 2 = Transação Parcelada -- 
                if($data["type"] == 2){	

                    $anoParcela = $data["Ano"];
                    $mesParcela = $data["Mes"];

                    for($n = 1;$n <= $data["TotalParcelas"] ; $n++){

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
                if($data["IdTipoTransacao"] == 3){	

                    // -- BD INSERT -- 
                    $this->transacoes_model->Incluir($data);

                    // -- SALDO GERAL --
                    geral_UpdateSaldo($data);

                }
            }
            else{
                $ci->session->set_flashdata('msg-error',"Existem campos obrigatórios não preenchidos");
            }
			
            // -- MSG SUCESSO - REDIRECT
            $this->session->set_flashdata('msg-success',"Transação adicionada com sucesso!");
            redirect("content/month_content/".$data["Ano"]."/".$data["Mes"]."");
		} 
 	
        // ----- UPDATE TRANSACAO -----
		public function transacao_update($ano,$mes,$id){
				
			$this->output->enable_profiler(TRUE);
			
            /*VALIDACAO*/valida_usuario();
            
            $data                    = transacao_getPosts();
            $dataBusca["Id"]         = $data["Id"];
			$transacaoAtual          = $this->transacoes_model->Buscar($dataBusca);
            $data["IdTipoTransacao"] = $transacaoAtual["IdTipoTransacao"];
            
            $isTransacaoValidada     = ValidaEntidadeTransacao($data);
            
            if($isTransacaoValidada = true)
            {
                // DATA INCLUSAO
                date_default_timezone_set('America/Sao_Paulo');
                $data["DataAlteracao"] = date('Y-m-d H:i:s');
                
                $hasAlteracaoValor = false;

                // -- ALTERACAO VALOR --
                if(($data["Valor"] != $transacaoAtual["Valor"])){

                    $hasAlteracaoValor = true;
                    
                    echo "valor atual: ".$transacaoAtual["Valor"]."<br>";
                    echo "valor atualizado: ".$data["Valor"]."<br>";

                    $valorDiferenca = $data["Valor"] - $transacaoAtual["Valor"];
                    $valorDiferenca = round($valorDiferenca, 2);
                        
                    echo "- Alteração Valor: ".$valorDiferenca."<br>";
                }

                // -- ALTERACAO MÊS --
                if( (int)$data["Mes"] != (int)$transacaoAtual["Mes"]){
                    
                    echo "Alteração Mês"."<br>";
                    
                    $this->transacoes_model->Atualizar($data);

                    $dataParcela["Valor"] = $transacaoAtual["Valor"]*(-1);
                    $dataParcela["Ano"] = $transacaoAtual["Ano"];
                    $dataParcela["Mes"] = $transacaoAtual["Mes"];
                    $dataParcela["IdTipoTransacao"] = 3;

                    geral_UpdateSaldo($dataParcela);

                    $dataParcela["Valor"] = $data["Valor"];
                    $dataParcela["Ano"] = $data["Ano"];
                    $dataParcela["Mes"] = $data["Mes"];

                    geral_UpdateSaldo($dataParcela);
                }
                // -- SEM ALTERAÇÃO MÊS
                else{

                    echo "- Sem Alteração Mês <br>";
                    
                    var_dump($data);
                    
                    $this->transacoes_model->Atualizar($data);

                    if($hasAlteracaoValor == true)
                    {
                        $data["Valor"] = $valorDiferenca; 
                        echo $data["Valor"];

                        // -- SALDO GERAL --
                        geral_UpdateSaldo($data);
                    }

                }
            }
            else{
                $ci->session->set_flashdata('msg-error',"Existem campos obrigatórios não preenchidos");
            }
	       
            // -- MSG SUCESSO - REDIRECT
            /*$this->session->set_flashdata('msg-success',"Transação alterada com sucesso!");
            redirect("content/month_content/".$ano."/".$mes);*/
		}
        
        // ----- DELETE TRANSACAO -----
		public function transacao_delete($ano,$mes,$id){
				
			$this->output->enable_profiler(TRUE);
            
            /*VALIDACAO*/valida_usuario();
			
            $data["Id"] = $id;
            
			$transacaoAtual = $this->transacoes_model->Buscar($data);
            
            // -- TRANSACAO SIMPLES / PARCELADA
            if(($transacaoAtual["IdTipoTransacao"] == 3)or($transacaoAtual["IdTipoTransacao"] == 2))
            {
                $data["Valor"] = 0;
            }
            //TRANSCAO RECORRENTE
            if($data["IdTipoTransacao"]  = 1)
            {
                if(($transacaoAtual["Ano"] == $ano)&&($transacaoAtual["Mes"] == $mes))
                {
                    $data["Valor"] = 0;
                }
                else{
                    $data["IdTipoTransacao"] = 3;
                }
            
            }
            
            $this->transacoes_model->Atualizar($data);
            
            $data["Ano"]   = $ano;
            $data["Mes"]   = $mes;
            $data["IdTipoTransacao"]  = $transacaoAtual["IdTipoTransacao"];
            $data["Valor"] = -$transacaoAtual["valor"];
	
			// -- SALDO GERAL --
            geral_UpdateSaldo($data);
            
            // -- MSG SUCESSO - REDIRECT
            $this->session->set_flashdata('msg-success',"Transacao deletada com sucesso!");
            redirect("content/month_content/".$ano."/".$mes);
			
		}
        
        // ----- INSERT CARTAO -----
        public function cartao_insert(){
        	
			$this->output->enable_profiler(TRUE);
			
            // -- VALIDACAO USUARIO --
            valida_usuario();
			  
            // -- NOVA CATEGORIA --
            $novaCategoria = novaCategoria();     
            // -- NOVA SUB CATEGORIA --
            $novaSubCategoria = novaSubCategoria();	
            
            /* -- DATA -- */
            $data   = transacao_getPosts();	

            $isTransacaoValidada = false;
            if(
                ($data["DataCompra"] != "")
                &&
                ($data["Descricao"] != "")
                &&
                ($data["Valor"] > 0)	
				&&	
                ($data["Ano"] > 1900)
                &&
                ($data["Ano"] > 1900)
                &&
                (($data["Mes"] >= 1)&&($data["Mes"] <= 12))
                &&
                ($data["IdCategoria"] > 0)
                &&
                ($data["IdSubCategoria"] > 0)
                &&
                ($data["IdUsuario"] > 0)
                &&
                ($data["IdTipoTransacao"] > 0)
            )
            {
                $isTransacaoValidada = true;
            }
				
            if($isTransacaoValidada = true)
            {
                // DATA INCLUSAO
                date_default_timezone_set('America/Sao_Paulo');
                $data["DataInclusao"] = date('Y-m-d H:i:s');
                
                // -- TYPE 3 = Transação Simples -- 
			    if($data["IdTipoTransacao"] == 3){
                
                    // -- BD - INSERIR --
                    $this->cartao_model->Incluir($data);

                    // -- SALDO Cartao --
                    geral_UpdateCartaoMes($data);      

                    // -- SALDO GERAL --
                    geral_UpdateSaldo($data);
                    
			    }
                             
            }
            else{
                $ci->session->set_flashdata('msg-error',"Existem campos obrigatórios não preenchidos");
            }
            
            // -- TYPE 1 = Transação Recorrente -- 
			if($data["type"] == 1){
                
                $data["AnoFim"] = 2050;
                $data["MesFim"] = 12;
				
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
            
            
            
            // -- MSG SUCESSO - REDIRECT
            $this->session->set_flashdata('msg-success',"Transação adicionada com sucesso!");
            redirect("content/month_content/".$data["ano"]."/".$data["mes"]."");

		} 
        
		// ----- UPDATE CARTAO -----
        public function cartao_update($ano,$mes,$id){
        	
            $this->output->enable_profiler(TRUE);
			
            /*VALIDACAO*/valida_usuario();
            
            $data = transacao_getPosts();
            
            date_default_timezone_set('America/Sao_Paulo');
            
            if($data["IdTipoTransacao"] == 1){
                
                $data_update["Id"] = $data["Id"];
                $data_update       = $this->cartao_model->Buscar($data_update);

                if($mes == 1)
                {
                    $data_update["AnoFim"] = $ano - 1;
                    $data_update["MesFim"] = 12;                   
                }
                else{
                    $data_update["AnoFim"] = $ano;
                    $data_update["MesFim"] = $mes-1;
                }
                
                var_dump($data_update);
                $this->cartao_model->Atualizar($data_update);
                
                $data_insert = $data;
                
                $data_insert["Id"]     = null;
                $data_insert["AnoFim"] = 2050;
                $data_insert["MesFim"] = 12;   
                
                var_dump($data_insert);
                $this->cartao_model->Incluir($data_insert);
                
                if($data_update["Valor"] != $data_insert["Valor"])
                {
                    echo "valor atual: ".$data_update["Valor"]."<br>";
                    echo "valor atualizado: ".$data_insert["Valor"]."<br>";
                    
                    $valorDiferenca = $data_insert["Valor"] - $data_update["Valor"];
                    $valorDiferenca = round($valorDiferenca, 2);
                    
                    echo "- Alteração Valor: ".$valorDiferenca."<br>";
                    
                    $dataGeral["Valor"]           = $valorDiferenca; 
                    $dataGeral["IdTipoTransacao"] = 1; 
                    $dataGeral["Ano"]             = $ano; 
                    $dataGeral["Mes"]             = $mes; 
                    $dataGeral["Ano"]             = $ano; 
                    $dataGeral["Mes"]             = $mes; 
                    
                    // -- SALDO GERAL --
                    geral_UpdateSaldo($dataGeral);
                    geral_UpdateCartaoMes($dataGeral);
                }
                
            }
            
            /*if($data["IdTipoTransacao"] == 2){
               
                $dataBusca["PeriodoDe"]  = true;
                $dataBusca["Descricao"]  = $data["Descricao"];
                $dataBusca["Ano"]        = $data["Ano"];
                $dataBusca["Mes"]        = $data["Mes"];
                $cartaoAtual             = $this->cartao_model->Listar($dataBusca);
                
                
                
                foreach($cartaoAtual as $itemContent){
                    
                    // DATA INCLUSAO
                    $hasAlteracaoValor = false;
                    
                    $dataUpdate                  = [];
                    $dataUpdate                  = $itemContent;
                    $dataUpdate["Descricao"]     = $data["Descricao"];
                    $dataUpdate["Valor"]         = $data["Valor"];
                    $dataUpdate["IdCategoria"]   = $data["IdCategoria"];
                    $dataUpdate["Descricao"]     = $data["IdSubCategoria"];
                    $dataUpdate["DataAlteracao"] = date('Y-m-d H:i:s');
                    
                    $this->cartao_model->Atualizar($dataUpdate);
                    
                    // -- ALTERACAO VALOR --
                    if(($dataUpdate["Valor"] != $itemContent["Valor"])){
                        
                        $dataUpdate["Valor"] = $dataUpdate["Valor"] - $itemContent["Valor"];
                        
                        // -- SALDO CARTAO --
                        geral_UpdateCartaoMes($dataUpdate); 
                        
                        // -- SALDO GERAL
                        geral_UpdateSaldo($dataUpdate);
                        
                    }
                }

            }
                
            $data["Id"]              = $cartaoAtual["Id"];
            $data["IdTipoTransacao"] = $cartaoAtual["IdTipoTransacao"];
            
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
					
            		// --- BD-CRUD
                    $this->crud_model->update("cartao_de_credito",$id,$cartao_atual);	
				
					//NEW
					$cartao_atual["valor"] = $data["valor"];
					$cartao_atual["ano"] = $ano;
					$cartao_atual["mes"] = $mes;
					$cartao_atual["id"] = '';
					$cartao_atual["mes_fim"] = "";
            		$cartao_atual["ano_fim"] = "";
					
					// --- BD-CRUD
                    $this->crud_model->insert("cartao_de_credito",$cartao_atual);
													
					foreach($lista_geral as $lista){
                        // --- SALDO GERAL
						crud_geral($lista["ano"],$lista["mes"],"cartao",$diferenca);
					}	
						
				}
				else{
					// --- SALDO GERAL
                    crud_geral($data["ano"],$data["mes"],"cartao",$diferenca);	
				}
              }
			
			// -- MSG SUCESSO - REDIRECT
            $this->session->set_flashdata('msg-success',"Transação alterada com sucesso!");
            redirect("content/month_content/".$ano."/".$mes);*/
		} 
				            
    }