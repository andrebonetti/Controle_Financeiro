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
                
                if($data["Valor"] > 0){$tipo = 1;}
                else{$tipo = 2;}
                
                // -- TYPE 1 = Transação Recorrente -- 
                if($data["IdTipoTransacao"] == 1){

                    $data["AnoFim"] = 2050;
                    $data["MesFim"] = 12;
                    
                    // -- BD INSERT -- 
                    $this->transacoes_model->Incluir($data);

                    // -- SALDO GERAL --
                    geral_UpdateSaldo($data,$tipo);

                }
                // -- TYPE 2 = Transação Parcelada -- 
                if($data["IdTipoTransacao"] == 2){	

                    $anoParcela = $data["Ano"];
                    $mesParcela = $data["Mes"];

                    for($n = 1;$n <= $data["TotalParcelas"] ; $n++){

                        $dataParcela = $data;

                        $dataParcela["Ano"]		= $anoParcela;	
                        $dataParcela["Mes"]		= $mesParcela;										
                        $dataParcela["NumeroParcela"] = $n;

                        // -- BD INSERT -- 
                        $this->transacoes_model->Incluir($dataParcela);

                        // -- SALDO GERAL --
                        geral_UpdateSaldo($dataParcela,$tipo);

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
                    geral_UpdateSaldo($data,$tipo);

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
		public function transacao_update($ano,$mes,$id,$pIsExclusao = null){
				
			$this->output->enable_profiler(TRUE);
			
            /*VALIDACAO*/valida_usuario();
            
            $data                    = transacao_getPosts();
            if($pIsExclusao == 1){$data["Valor"] = 0;}
            echo "Id: ".$data["Id"]."<br>";

            $dataBusca["Id"]         = $data["Id"];
			$transacaoAtual          = $this->transacoes_model->Buscar($dataBusca);
            
            $data["IdTipoTransacao"] = $transacaoAtual["IdTipoTransacao"];
            $isTransacaoValidada     = ValidaEntidadeTransacao($data);
            
            if($isTransacaoValidada = true)
            {
                // DATA ALTERACAO
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
                else{
                    echo "Sem Alteração Valor <br>";
                }

                // -- ALTERACAO MÊS --
                if(((int)$data["Mes"] != (int)$transacaoAtual["Mes"])&&($data["IdTipoTransacao"] != 1)){
                    
                    echo "Alteração Mês: de ".$transacaoAtual["Mes"]." para ".$data["Mes"]."<br>";
                    
                    $this->transacoes_model->Atualizar($data);

                    $dataParcela["Valor"] = $transacaoAtual["Valor"]*(-1);
                    $dataParcela["Ano"] = $transacaoAtual["Ano"];
                    $dataParcela["Mes"] = $transacaoAtual["Mes"];
                    $dataParcela["IdTipoTransacao"] = 3;

                    geral_UpdateSaldo($dataParcela);
                }
                // -- SEM ALTERAÇÃO MÊS
                else{

                    echo "Sem Alteração Mês <br>";
                    
                    //Transacao Recorrente
                    if($transacaoAtual["IdTipoTransacao"] == 1)
                    {
                        echo "Transação Tipo: 1 <br>";
                        
                        if($mes == 1)
                        {
                            $transacaoAtual["AnoFim"] = $ano - 1;
                            $transacaoAtual["MesFim"] = 12;                   
                        }
                        else{
                            $transacaoAtual["AnoFim"] = $ano;
                            $transacaoAtual["MesFim"] = $mes-1;
                        }

                        var_dump($transacaoAtual);

                        $this->transacoes_model->Atualizar($transacaoAtual);

                        if($pIsExclusao != 1)
                        {
                            $transacaoAtual["Dia"] = $data["Dia"];
                            $transacaoAtual["IdCategoria"] = $data["IdCategoria"];
                            $transacaoAtual["IdSubCategoria"] = $data["IdSubCategoria"];
                            $transacaoAtual["Descricao"] = $data["Descricao"];
                            $transacaoAtual["Valor"] = $data["Valor"];
                            $transacaoAtual["Ano"] = $ano;
                            $transacaoAtual["Mes"] = $mes;  
                            $transacaoAtual["AnoFim"] = 2050;
                            $transacaoAtual["MesFim"] = 12;  

                            $this->transacoes_model->Incluir($transacaoAtual);
                        }
                    }
                    
                    //Transacao Parcelada / Transacao Simples
                    else{
                        
                        echo "Transação Tipo: 2 ou 3 <br>";
                        
                        $transacaoAtual["Dia"] = $data["Dia"];
                        $transacaoAtual["IdCategoria"] = $data["IdCategoria"];
                        $transacaoAtual["IdSubCategoria"] = $data["IdSubCategoria"];
                        $transacaoAtual["Descricao"] = $data["Descricao"];
                        $transacaoAtual["Valor"] = $data["Valor"];
                        
                        var_dump($data);
                        var_dump($transacaoAtual);
                        
                        $this->transacoes_model->Atualizar($transacaoAtual);
                    }
                    
                    if($hasAlteracaoValor == true)
                    {
                        $transacaoAtual["Valor"] = $valorDiferenca; 
                        
                        if($transacaoAtual["Valor"] > 0){$tipo = 1;}
                        else{$tipo = 2;}
                        
                        $transacaoAtual["Ano"] = $ano;
                        $transacaoAtual["Mes"] = $mes;
                        
                        // -- SALDO GERAL --
                        geral_UpdateSaldo($transacaoAtual,$tipo);
                    }

                }
            }
            else{
                $ci->session->set_flashdata('msg-error',"Existem campos obrigatórios não preenchidos");
            }
            
            echo "ano: ".$ano." mes: ".$mes;
  
            // -- MSG SUCESSO - REDIRECT
            $this->session->set_flashdata('msg-success',"Transação alterada com sucesso!");
            redirect("content/month_content/".$ano."/".$mes);

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
            
            if($transacaoAtual["Valor"] > 0){$tipo = 1;}
            else{$tipo = 2;}
	
			// -- SALDO GERAL --
            geral_UpdateSaldo($data,$tipo);
            
            // -- MSG SUCESSO - REDIRECT
            $this->session->set_flashdata('msg-success',"Transacao deletada com sucesso!");
            redirect("content/month_content/".$ano."/".$mes);
			
		}
        
        // ----- INSERT CARTAO -----
        public function cartao_insert(){
        	
			$this->output->enable_profiler(TRUE);
			
            // -- VALIDACAO USUARIO --
            valida_usuario();
			  
            /* -- DATA -- */
            $data   = transacao_getPosts();	
            $isTransacaoValidada    = ValidaEntidadeTransacao($data);
            
            if($data["Valor"] > 0){$tipo = 1;}
            else{$tipo = 2;}
            
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
                    $this->cartao_model->Incluir($data);

                    // -- SALDO GERAL --
                    geral_UpdateSaldo($data);
                    
                    // -- SALDO GERAL CARTAO
                    geral_UpdateSaldoMesCartao($data,$tipo);

                }
                // -- TYPE 2 = Transação Parcelada --
                if($data["IdTipoTransacao"] == 2){

                    $anoParcela = $data["Ano"];
                    $mesParcela = $data["Mes"];

                    for($n = 1;$n <= $data["TotalParcelas"] ; $n++){

                        $dataParcela = $data;

                        $dataParcela["Ano"]		= $anoParcela;	
                        $dataParcela["Mes"]		= $mesParcela;										
                        $dataParcela["NumeroParcela"] = $n;

                        // -- BD INSERT -- 
                        $this->cartao_model->Incluir($dataParcela);

                        // -- SALDO GERAL --
                        geral_UpdateSaldo($dataParcela,$tipo);
                        // -- SALDO GERAL CARTAO
                        geral_UpdateSaldoMesCartao($dataParcela,$tipo);

                        $mesParcela++;
                        if($mesParcela > 12){
                            $anoParcela++;
                            $mesParcela = 1;
                        }						
                    }	

                }
                
                // -- TYPE 3 = Transação Simples -- 
			    if($data["IdTipoTransacao"] == 3){
                
                    // -- BD - INSERIR --
                    $this->cartao_model->Incluir($data);

                    // -- SALDO Cartao --
                    geral_UpdateCartaoMes($data);      

                    // -- SALDO GERAL --
                    geral_UpdateSaldo($data);
                    // -- SALDO GERAL CARTAO
                    geral_UpdateSaldoMesCartao($data,$tipo);
			    }
                             
            }
            else{
                $ci->session->set_flashdata('msg-error',"Existem campos obrigatórios não preenchidos");
            }
            
            // -- MSG SUCESSO - REDIRECT
            $this->session->set_flashdata('msg-success',"Transação adicionada com sucesso!");
            redirect("content/month_content/".$data["Ano"]."/".$data["Mes"]."");
		} 
        
		// ----- UPDATE CARTAO -----
        public function cartao_update($ano,$mes,$id){
        	
            $this->output->enable_profiler(TRUE);			
            /*VALIDACAO*/valida_usuario();
            
            $data = transacao_getPosts();
            $isTransacaoValidada     = ValidaEntidadeTransacao($data);
            
            $data_update["Id"] = $data["Id"];
            $data_update       = $this->cartao_model->Buscar($data_update);
            
            echo "Data \/"; var_dump($data);
            echo "Data Update \/"; var_dump($data_update);
            
            if($isTransacaoValidada = true)
            {
                date_default_timezone_set('America/Sao_Paulo');           
                $data["DataAlteracao"] = date('Y-m-d H:i:s');
                $hasAlteracaoValor = false;
                
                // -- ALTERACAO VALOR --
                if(($data["Valor"] != $data_update["Valor"])){

                    $hasAlteracaoValor = true;
                    
                    echo "valor atual: ".$data_update["Valor"]."<br>";
                    echo "valor atualizado: ".$data["Valor"]."<br>";

                    $valorDiferenca = $data["Valor"] - $data_update["Valor"];
                    $valorDiferenca = round($valorDiferenca, 2);
                        
                    echo "- Alteração Valor: ".$valorDiferenca."<br>";
                }
                else{
                    echo "Sem Alteração Valor <br>";
                }
                
                if($data["IdTipoTransacao"] == 1){

                    echo "IdTipoTransacao: 1 <br><br>";

                    if($mes == 1)
                    {
                        $data_update["AnoFim"] = $ano - 1;
                        $data_update["MesFim"] = 12;                   
                    }
                    else{
                        $data_update["AnoFim"] = $ano;
                        $data_update["MesFim"] = $mes-1;
                    }

                    echo "Data Update \/"; var_dump($data_update);
                    $this->cartao_model->Atualizar($data_update);

                    $data_insert = $data;

                    $data_insert["Id"]     = null;
                    $data_insert["AnoFim"] = 2050;
                    $data_insert["MesFim"] = 12;   

                    echo "Data Insert \/"; var_dump($data_insert);
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
                        geral_UpdateSaldo($dataGeral,2);
                        geral_UpdateCartaoMes($dataGeral);
                    }

                }
                else{
                    
                    if($data["IdTipoTransacao"] == 2){

                        $dataBusca["PeriodoDe"]  = true;
                        $dataBusca["Descricao"]  = $data["Descricao"];
                        $dataBusca["Ano"]        = $data["Ano"];
                        $dataBusca["Mes"]        = $data["Mes"];
                        $cartaoAtual             = $this->cartao_model->Listar($dataBusca);

                        foreach($cartaoAtual as $itemContent){

                            // DATA INCLUSAO
                            $hasAlteracaoValor = false;

                            $dataUpdate                  = array();
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

                    if($data["IdTipoTransacao"] == 3){

                        echo "Transação Tipo: 2 ou 3 <br>";

                        $data_update["IdCategoria"] = $data["IdCategoria"];
                        $data_update["IdSubCategoria"] = $data["IdSubCategoria"];
                        $data_update["Descricao"] = $data["Descricao"];
                        $data_update["Valor"] = $data["Valor"];

                        var_dump($data_update);

                        $this->cartao_model->Atualizar($data_update);        
                        
                    }

                    if($hasAlteracaoValor == true)
                    {
                        $data_update["Valor"] = $valorDiferenca; 
                        
                        if($data_update["Valor"] > 0){$tipo = 1;}
                        else{$tipo = 2;}

                        // -- SALDO GERAL --
                        geral_UpdateSaldo($data_update,$tipo);
                        
                        // -- SALDO GERAL CARTAO
                        geral_UpdateSaldoMesCartao($data_update,$tipo);
                    }
                    
                }
                
            }
			else{
                $ci->session->set_flashdata('msg-error',"Existem campos obrigatórios não preenchidos");
            }
            
			// -- MSG SUCESSO - REDIRECT
            $this->session->set_flashdata('msg-success',"Transação alterada com sucesso!");
            redirect("content/month_content/".$ano."/".$mes);
		} 
				            
        // ---- ALTERACAO MANUAL ----- //
        public function alteracao_manual(){
            
            $this->output->enable_profiler(TRUE);
            
            $data["Ano"]            = $this->input->post("ano");
            $data["Mes"]            = $this->input->post("mes");
            $valor                  = valor_decimal($this->input->post("valor"));	
            
            echo $valor;
            
            $tipoAlteracao	        = $this->input->post("tipo_alteracao");
            
            echo " - ".$tipoAlteracao;

            $data[$tipoAlteracao]   = valor_decimal($valor);
            
            if(($tipoAlteracao == "SaldoMes")||($tipoAlteracao == "SaldoFinal")){
                
                $saldoAnterior          = $this->input->post("SaldoAnterior");
                
                if($tipoAlteracao == "SaldoMes"){
                    $data["SaldoFinal"] = $saldoAnterior + $valor;
                }
                
                if($tipoAlteracao == "SaldoFinal"){
                    $data["SaldoFinal"] = $valor;
                    $data["SaldoMes"] = $valor - $saldoAnterior;
                }
                
                $this->geral_model->Atualizar_Manual($data);   
                
                $dataGeral["Mes"] = $data["Mes"];
                $dataGeral["Ano"] = $data["Ano"];
                geral_UpdateSaldoManual($dataGeral);
            }
            else{
                
                if($tipoAlteracao == "Cartao"){
                    
                    $ValorAnterior  = $this->input->post("ValorAnterior");
                    $SaldoMes  = $this->input->post("SaldoMes");
                    $SaldoFinal  = $this->input->post("SaldoFinal");

                    $valorDiferenca = $valor - $ValorAnterior;
                    
                    $data["Cartao"]     = $valor;
                    $data["SaldoMes"]   = $SaldoMes + $valorDiferenca;
                    $data["SaldoFinal"]   = $SaldoFinal + $valorDiferenca;
                    
                    $this->geral_model->Atualizar_Manual($data);

                    $dataGeral["Mes"] = $data["Mes"];
                    $dataGeral["Ano"] = $data["Ano"];

                    geral_UpdateSaldoManual($dataGeral);
                    
                }
                else{
                    
                    $this->geral_model->Atualizar_Manual($data);   
                    
                }
            }
            
            redirect("content/month_content/".$data["Ano"]."/".$data["Mes"]);
        }
    }