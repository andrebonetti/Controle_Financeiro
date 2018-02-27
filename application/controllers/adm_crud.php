<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Adm_crud extends CI_Controller{
        
        // ----- INSERT TRANSACAO -----
        public function transacao_insert(){

            $config = config_base(array("rollback" => false,"retorno" => true));//array("rollback" => true,"retorno" => false));      
            valida_usuario();
			  
            /* -- DATA -- */
            $data = transacao_getPosts();	

            if(ValidaEntidadeTransacao($data) == true)
            {
                $this->db->trans_begin();

                if($data["IsTransferencia"] == true){
                    
                    echo "É Transferência <br>";

                    contas_saldo_transferirValores($data);

                    $origem  = $data["origem"];
                    $destino = $data["destino"];

                    unset($data["origem"]);
                    unset($data["destino"]);

                    $data["IdCategoria"]    = 27;
                    $data["IdSubCategoria"] = 134;
                    $data["IdConta"]        = $destino;
                    $data["Valor"]          = $data["Valor"];
                    $data["IdContaOrigem"]  = $origem;

                    if($data["IdTipoTransacao"] == 1){
                        $data["AnoFim"] = 2050;
                        $data["MesFim"] = 12;
                    }

                    $this->transacoes_model->Incluir($data);

                    // $data["IdCategoria"]    = 27;
                    // $data["IdSubCategoria"] = 134;
                    // $data["IdConta"]        = $destino;
                    // $data["Valor"]          = $data["Valor"]*(-1);

                    // if($data["IdTipoTransacao"] == 1){
                    //     $data["AnoFim"] = 2050;
                    //     $data["MesFim"] = 12;
                    // }

                    // $this->transacoes_model->Incluir($data);

                }
                else{
                    if($data["Valor"] > 0){$tipo = 1;}
                    else{$tipo = 2;}
                    
                    echo "IdTipoTransacao: ". $data["IdTipoTransacao"]."<br>";    

                    // -- TYPE 1 = Transação Recorrente -- 
                    if($data["IdTipoTransacao"] == 1){

                        $data["AnoFim"] = 2050;
                        $data["MesFim"] = 12;
                        
                        // -- BD INSERT -- 
                        $this->transacoes_model->Incluir($data);

                        // -- SALDO GERAL --
                        geral_UpdateSaldo($data,$tipo);
                        contas_saldo_UpdateSaldo($data);

                    }

                    // -- TYPE 2 = Transação Parcelada -- 
                    if($data["IdTipoTransacao"] == 2){	

                        $anoParcela = $data["Ano"];
                        $mesParcela = $data["Mes"];

                        $ultimoCodigoTransacao  = $this->transacoes_model->bucarUltimoCodigoTransacao();
                        $proximo                = $ultimoCodigoTransacao["CodigoTransacao"] + 1;
                        echo "Proximo Codigo Transacao: ".$proximo ;

                        for($n = 1;$n <= $data["TotalParcelas"] ; $n++){

                            $dataParcela = $data;

                            $dataParcela["Ano"]		        = $anoParcela;	
                            $dataParcela["Mes"]		        = $mesParcela;										
                            $dataParcela["NumeroParcela"]   = $n;
                            $dataParcela["CodigoTransacao"] = $proximo;

                            // -- BD INSERT -- 
                            $this->transacoes_model->Incluir($dataParcela);

                            // -- SALDO GERAL --
                            geral_UpdateSaldo($dataParcela,$tipo);
                            contas_saldo_UpdateSaldo($data);

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
                        contas_saldo_UpdateSaldo($data);

                    }

                }

                if(isset($data["IdCartao"]) &&  $data["IdCartao"] > 0){

                    // -- SALDO GERAL CARTAO
                    geral_UpdateSaldoMesCartao($data,$tipo);

                }
                config_finalTransaction($config);
                $this->session->set_flashdata('msg-success',"Transação adicionada com sucesso!");
            }

            if($config["retorno"] == true){
                redirect("content/month_content/".$data["Ano"]."/".$data["Mes"]."");
            }
		} 
 	
        // ----- UPDATE TRANSACAO -----
		public function transacao_update($ano,$mes,$id,$pIsExclusao = null){
				
			$config = config_base(array("rollback" => true,"retorno" => false));//array("rollback" => true,"retorno" => false));
            valida_usuario();

            $paramGet["Ano"]    = $ano;
            $paramGet["Mes"]    = $mes;
            $paramGet["Id"]     = $id;

            //ATUAL BD
            $data["Id"]         = $paramGet["Id"];
            $paramBusca["Id"]   = $data["Id"];
			$transacaoAtual     = $this->transacoes_model->Buscar($paramBusca);

            //ATUALIZACAO
            $data               = transacao_getPosts();

            util_print($data);

            if(ValidaEntidadeTransacao($data) == true)
            {

                $this->db->trans_begin();

                $hasAlteracaoConta              = false;
                $hasAlteracaoValor              = false;
                $hasAlteracaoValorTransferencia = false;

                //EXCLUIR (VALOR = 0)
                if($pIsExclusao == 1){$data["Valor"] = 0;}
                if($data["IsContabilizado"] == false){
                    $valorDiferenca = $transacaoAtual["Valor"]*(-1);

                    $hasAlteracaoValor = true;
                }

                //ALTERACAO CONTA
                if($transacaoAtual["IdConta"] != $data["IdConta"]){
                    $hasAlteracaoConta  = true;
                    $IdContaOriginal    = $transacaoAtual["IdConta"];

                    echo "Alteração Conta <br>";

                    if($transacaoAtual["IdTipoTransacao"] == 1){
                        $AnoOriginal = $paramGet["Ano"];
                        $MesOriginal = $paramGet["Mes"];
                    }
                    else{
                        $AnoOriginal = $transacaoAtual["Ano"];
                        $MesOriginal = $transacaoAtual["Mes"];
                    }
                }
                else{
                    echo "Sem Alteração Conta <br>";
                }
                
                //ALTERACAO VALOR
                if(((float)$data["Valor"] != (float)$transacaoAtual["Valor"])){

                    $hasAlteracaoValor = true;

                    echo "<b>Tem Diferença Valor</b> <br>";
                    
                    echo "valor atual: ".$transacaoAtual["Valor"]."<br>";
                    echo "valor atualizado: ".$data["Valor"]."<br>";

                    $valorDiferenca = $data["Valor"] - $transacaoAtual["Valor"];
                    $valorDiferenca = round($valorDiferenca, 2);
                        
                    echo "=> Diferença: ".$valorDiferenca."<br>";

                    if($transacaoAtual["IsTransferencia"]){
                        $hasAlteracaoValorTransferencia = true;
                    }
                }
                else{
                    echo "Sem Alteração Valor <br>";
                }

                //ALTERACAO MÊS
                if(((int)$data["Mes"] != (int)$transacaoAtual["Mes"])&&($data["IdTipoTransacao"] != 1)){
                    
                    echo "<b>Alteração Mês:</b> de ".$transacaoAtual["Mes"]." para ".$data["Mes"]."<br>";
                    unset($data["espelhar-proximas"]);
                    
                    $this->transacoes_model->Atualizar($data);

                    $dataParcela["Valor"] = $transacaoAtual["Valor"]*(-1);
                    $dataParcela["Ano"] = $transacaoAtual["Ano"];
                    $dataParcela["Mes"] = $transacaoAtual["Mes"];
                    $dataParcela["IdConta"] = $transacaoAtual["IdConta"];
                    $dataParcela["IdTipoTransacao"] = 3;

                    contas_saldo_UpdateSaldo($dataParcela);
                    geral_UpdateSaldo($dataParcela);

                }
                else{
                    //SEM ALTERAÇÃO MÊS
                    echo "<b>Sem Alteração Mês</b> <br>";
                    
                    //TANSACAO 1
                    if($transacaoAtual["IdTipoTransacao"] == 1){
                        echo "Transação Tipo: 1 <br>";
                        
                        $dataComptencia = calcularCompetencia($ano,$mes,-1);     
                        $transacaoAtual["AnoFim"]    = $dataComptencia["Ano"];        
                        $transacaoAtual["MesFim"]    = $dataComptencia["Mes"]; 
                        
                        $this->transacoes_model->Atualizar($transacaoAtual);

                        if($pIsExclusao != 1)
                        {
                            $transacaoAtual["AnoFim"] = 2050;
                            $transacaoAtual["MesFim"] = 12; 
                           
                            //ESPELHAR
                            if(isset($data["espelhar-proximas"]) && $data["espelhar-proximas"] == true ){

                                echo "espelhar-proximas <br>";
                                if(isset($data["Dia"])){$transacaoAtual["Dia"] = $data["Dia"];}
                                if(isset($data["IdCategoria"])){$transacaoAtual["IdCategoria"] = $data["IdCategoria"];}
                                if(isset($data["IdSubCategoria"])){$transacaoAtual["IdSubCategoria"] = $data["IdSubCategoria"];}
                                if(isset($data["Descricao"])){$transacaoAtual["Descricao"] = $data["Descricao"];}
                                if(isset($data["Valor"])){$transacaoAtual["Valor"] = $data["Valor"];}
                                if(isset($data["CodigoTransacao"])){$transacaoAtual["CodigoTransacao"] = $data["CodigoTransacao"];}
                                if(isset($data["IdConta"])){$transacaoAtual["IdConta"] = $data["IdConta"];}
                                if(isset($data["IsContabilizado"])){$transacaoAtual["IsContabilizado"] = $data["IsContabilizado"];}
                                $transacaoAtual["Ano"] = $paramGet["Ano"];
                                $transacaoAtual["Mes"] = $paramGet["Mes"];  
                                 
                                $this->transacoes_model->Incluir($transacaoAtual);

                            }
                            //EXCEÇÃO
                            else{

                                echo "sem espelhar <br>";
                                //Transacao Igual - Recorrente Proximo Mes
                                $dataComptencia = calcularCompetencia($ano,$mes,1);     
                                $transacaoAtual["Ano"]    = $dataComptencia["Ano"];        
                                $transacaoAtual["Mes"]    = $dataComptencia["Mes"];   

                                $this->transacoes_model->Incluir($transacaoAtual);

                                //Transacao Unica
                                $transacaoAtual["IdTipoTransacao"] = 3;
                                if(isset($data["Dia"])){$transacaoAtual["Dia"] = $data["Dia"];}
                                if(isset($data["IdCategoria"])){$transacaoAtual["IdCategoria"] = $data["IdCategoria"];}
                                if(isset($data["IdSubCategoria"])){$transacaoAtual["IdSubCategoria"] = $data["IdSubCategoria"];}
                                if(isset($data["Descricao"])){$transacaoAtual["Descricao"] = $data["Descricao"];}
                                if(isset($data["Valor"])){$transacaoAtual["Valor"] = $data["Valor"];}
                                if(isset($data["IdConta"])){$transacaoAtual["IdConta"] = $data["IdConta"];}

                                $transacaoAtual["Ano"] = $ano;
                                $transacaoAtual["Mes"] = $mes;

                                $this->transacoes_model->Incluir($transacaoAtual);
                            }

                        }
                    }
                    else{

                        //TRANSACAO 3
                        if((isset($data["IdCartao"])) && ($data["IdCartao"] > 0)){   
                            $transacaoAtual["DataCompra"] = $data["DataCompra"];
                        }else{
                            $transacaoAtual["Dia"] = $data["Dia"];
                        }

                        if(isset($data["IdCategoria"])){$transacaoAtual["IdCategoria"] = $data["IdCategoria"];}
                        if(isset($data["IdSubCategoria"])){$transacaoAtual["IdSubCategoria"] = $data["IdSubCategoria"];}
                        if(isset($data["Descricao"])){$transacaoAtual["Descricao"] = $data["Descricao"];}
                        if(isset($data["Valor"])){$transacaoAtual["Valor"] = $data["Valor"];}
                        if(isset($data["IdConta"])){$transacaoAtual["IdConta"] = $data["IdConta"];}
                        if(isset($data["IsContabilizado"])){$transacaoAtual["IsContabilizado"] = $data["IsContabilizado"];}
                        
                        //TRANSACAO 2
                        if($transacaoAtual["IdTipoTransacao"] == 2){
                        
                            echo "Transação Tipo: 2<br>";  
                            
                            if($data["espelhar-proximas"]){

                                echo "Espelhar Alteracao <br>";      

                                $paramBuscaParcela["CodigoTransacao"] = $data["CodigoTransacao"];
                                $paramBuscaParcela["NumeroParcela >="] = $data["NumeroParcela"];
                                
                                $dataParcelas = $this->transacoes_model->Listar($paramBuscaParcela);

                                foreach($dataParcelas as $parcela){

                                    $parcela["Dia"] = $data["Dia"];
                                    $parcela["IdCategoria"] = $data["IdCategoria"];
                                    $parcela["IdSubCategoria"] = $data["IdSubCategoria"];
                                    $parcela["Descricao"] = $data["Descricao"];
                                    $parcela["Valor"] = $data["Valor"];
                                    $parcela["IdConta"] = $data["IdConta"];
                                    $parcela["IsContabilizado"] = $data["IsContabilizado"];

                                    $this->transacoes_model->Atualizar($parcela); 

                                }
                            }
                            else{
                                $this->transacoes_model->Atualizar($transacaoAtual); 
                            }
                        }
                        else{
                            echo "Transação Tipo: 3<br>";  

                            $this->transacoes_model->Atualizar($transacaoAtual);
                        }
                  
                    }
                    
                    if($hasAlteracaoValor == true)
                    {
                        echo "hasAlteracaoValor <br>";

                        if( (!isset($data["espelhar-proximas"])||($data["espelhar-proximas"] == false)) ){
                            $data["IdTipoTransacao"] = 3;
                        }

                        if($hasAlteracaoValorTransferencia == true)
                        {
                            echo "Alteração Transferencia <br>";

                            $data["origem"]  = $transacaoAtual["IdContaOrigem"];
                            $data["destino"] = $transacaoAtual["IdConta"];

                            $data["Ano"]     = $transacaoAtual["Ano"];
                            $data["Mes"]     = $transacaoAtual["Mes"];

                            $data["Valor"]   = $valorDiferenca; 

                            util_print($data);

                            contas_saldo_transferirValores($data);
                        }
                        else{

                            echo "Alteração Transacao  <br>";

                            unset($transacaoAtual["Id"]);
                            $transacaoAtual["Valor"] = $valorDiferenca; 
                            
                            if($transacaoAtual["Valor"] > 0){$tipo = 1;}
                            else{$tipo = 2;}
                            
                            $transacaoAtual["Ano"] = $ano;
                            $transacaoAtual["Mes"] = $mes;
                            
                            // -- SALDO GERAL --
                            contas_saldo_UpdateSaldo($transacaoAtual);

                            if($hasAlteracaoConta != true){
                                geral_UpdateSaldo($transacaoAtual,$tipo);
                            }

                            
                            if(isset($data["IdCartao"]) && $data["IdCartao"] > 0){    
                                echo "IdCartao: ".$data["IdCartao"]."<br>";

                                // -- SALDO GERAL CARTAO
                                //contas_saldo_UpdateSaldo($data);
                                geral_UpdateSaldoMesCartao($transacaoAtual,$tipo);
                            }

                        }
                        
                    }

                }

                if($hasAlteracaoConta == true){

                    echo "Tem alteracao de Conta <br>";

                    $data["origem"] = $IdContaOriginal;
                    $data["destino"] = $data["IdConta"];

                    if($transacaoAtual["IdTipoTransacao"] == 3){

                        echo "Contas Saldo -> Transacao 3 <br>";

                        $data["Ano"] = $transacaoAtual["Ano"];
                        $data["Mes"] = $transacaoAtual["Mes"];

                        contas_saldo_transferirValores($data);
                    }

                    if($transacaoAtual["IdTipoTransacao"] == 2){

                        if($data["espelhar-proximas"]){

                            echo "Contas Saldo -> Transacao 2 -> Espelhar-Proximas <br>";

                            foreach($dataParcelas as $parcela){
                                $data["Ano"] = $parcela["Ano"];
                                $data["Mes"] = $parcela["Mes"];
                                contas_saldo_transferirValores($data);
                            }

                        }
                        else{

                            echo "Contas Saldo -> Transacao 1<br>";

                            $data["Ano"] = $transacaoAtual["Ano"];
                            $data["Mes"] = $transacaoAtual["Mes"];

                            contas_saldo_transferirValores($data);
                        }
                        
                    }
                    if($transacaoAtual["IdTipoTransacao"] == 1){

                        if($data["espelhar-proximas"]){

                            echo "Contas Saldo -> Transacao 1 -> Espelhar-Proximas <br>";

                            $data["Ano"]     = $AnoOriginal;
                            $data["Mes"]     = $MesOriginal;

                            contas_saldo_transferirValores($data);

                        }
                        else{

                            echo "Contas Saldo -> Transacao 1 <br>";

                            $data["Ano"] = $AnoOriginal;
                            $data["Mes"] = $MesOriginal;

                            contas_saldo_transferirValores($data);

                        }

                    }

                }

                config_finalTransaction($config);
                $this->session->set_flashdata('msg-success',"Transação alterada com sucesso!");
            }

            if($config["retorno"] == true){
                redirect("content/month_content/".$ano."/".$mes);
            }
            
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

        // // ----- DELETE TRANSACAO -----
		// public function transacao_delete($ano,$mes,$id){
				
		// 	$this->output->enable_profiler(TRUE);
            
        //     /*VALIDACAO*/valida_usuario();
			
        //     $data["Id"] = $id;
            
		// 	$transacaoAtual = $this->transacoes_model->Buscar($data);
            
        //     // -- TRANSACAO SIMPLES / PARCELADA
        //     if(($transacaoAtual["IdTipoTransacao"] == 3)or($transacaoAtual["IdTipoTransacao"] == 2))
        //     {
        //         $data["Valor"] = 0;
        //     }
        //     //TRANSCAO RECORRENTE
        //     if($data["IdTipoTransacao"]  = 1)
        //     {
        //         if(($transacaoAtual["Ano"] == $ano)&&($transacaoAtual["Mes"] == $mes))
        //         {
        //             $data["Valor"] = 0;
        //         }
        //         else{
        //             $data["IdTipoTransacao"] = 3;
        //         }
            
        //     }
            
        //     $this->transacoes_model->Atualizar($data);
            
        //     $data["Ano"]   = $ano;
        //     $data["Mes"]   = $mes;
        //     $data["IdTipoTransacao"]  = $transacaoAtual["IdTipoTransacao"];
        //     $data["Valor"] = -$transacaoAtual["valor"];
            
        //     if($transacaoAtual["Valor"] > 0){$tipo = 1;}
        //     else{$tipo = 2;}
	
		// 	// -- SALDO GERAL --
        //     geral_UpdateSaldo($data,$tipo);
            
        //     // -- MSG SUCESSO - REDIRECT
        //     $this->session->set_flashdata('msg-success',"Transacao deletada com sucesso!");
        //     redirect("content/month_content/".$ano."/".$mes);
			
		// }
        
        // // ----- INSERT CARTAO -----
        // public function cartao_insert(){
        	
		// 	$this->output->enable_profiler(TRUE);
			
        //     // -- VALIDACAO USUARIO --
        //     valida_usuario();
			  
        //     /* -- DATA -- */
        //     $data   = transacao_getPosts();	
        //     $isTransacaoValidada    = ValidaEntidadeTransacao($data);
            
        //     if($data["Valor"] > 0){$tipo = 1;}
        //     else{$tipo = 2;}
            
        //     if($isTransacaoValidada = true)
        //     {
        //         // DATA INCLUSAO
        //         date_default_timezone_set('America/Sao_Paulo');
        //         $data["DataInclusao"] = date('Y-m-d H:i:s');
                
        //         // -- TYPE 1 = Transação Recorrente -- 
        //         if($data["IdTipoTransacao"] == 1){

        //             $data["AnoFim"] = 2050;
        //             $data["MesFim"] = 12;

        //             // -- BD INSERT -- 
        //             $this->cartao_model->Incluir($data);

        //             // -- SALDO GERAL --
        //             geral_UpdateSaldo($data);
                    
        //             // -- SALDO GERAL CARTAO
        //             geral_UpdateSaldoMesCartao($data,$tipo);

        //         }
        //         // -- TYPE 2 = Transação Parcelada --
        //         if($data["IdTipoTransacao"] == 2){

        //             $anoParcela = $data["Ano"];
        //             $mesParcela = $data["Mes"];

        //             for($n = 1;$n <= $data["TotalParcelas"] ; $n++){

        //                 $dataParcela = $data;

        //                 $dataParcela["Ano"]		= $anoParcela;	
        //                 $dataParcela["Mes"]		= $mesParcela;										
        //                 $dataParcela["NumeroParcela"] = $n;

        //                 // -- BD INSERT -- 
        //                 $this->cartao_model->Incluir($dataParcela);

        //                 // -- SALDO GERAL --
        //                 geral_UpdateSaldo($dataParcela,$tipo);
        //                 // -- SALDO GERAL CARTAO
        //                 geral_UpdateSaldoMesCartao($dataParcela,$tipo);

        //                 $mesParcela++;
        //                 if($mesParcela > 12){
        //                     $anoParcela++;
        //                     $mesParcela = 1;
        //                 }						
        //             }	

        //         }
                
        //         // -- TYPE 3 = Transação Simples -- 
		// 	    if($data["IdTipoTransacao"] == 3){
                
        //             // -- BD - INSERIR --
        //             $this->cartao_model->Incluir($data);

        //             // -- SALDO Cartao --
        //             geral_UpdateCartaoMes($data);      

        //             // -- SALDO GERAL --
        //             geral_UpdateSaldo($data);
        //             // -- SALDO GERAL CARTAO
        //             geral_UpdateSaldoMesCartao($data,$tipo);
		// 	    }
                             
        //     }
        //     else{
        //         $ci->session->set_flashdata('msg-error',"Existem campos obrigatórios não preenchidos");
        //     }
            
        //     // -- MSG SUCESSO - REDIRECT
        //     $this->session->set_flashdata('msg-success',"Transação adicionada com sucesso!");
        //     redirect("content/month_content/".$data["Ano"]."/".$data["Mes"]."");
		// } 
        
		// // ----- UPDATE CARTAO -----
        // public function cartao_update($ano,$mes,$id){
        	
        //     $this->output->enable_profiler(TRUE);			
        //     /*VALIDACAO*/valida_usuario();
            
        //     $data = transacao_getPosts();
        //     $isTransacaoValidada     = ValidaEntidadeTransacao($data);
            
        //     $data_update["Id"] = $data["Id"];
        //     $data_update       = $this->cartao_model->Buscar($data_update);
            
        //     echo "Data \/"; var_dump($data);
        //     echo "Data Update \/"; var_dump($data_update);
            
        //     if($isTransacaoValidada = true)
        //     {
        //         date_default_timezone_set('America/Sao_Paulo');           
        //         $data["DataAlteracao"] = date('Y-m-d H:i:s');
        //         $hasAlteracaoValor = false;
                
        //         // -- ALTERACAO VALOR --
        //         if(($data["Valor"] != $data_update["Valor"])){

        //             $hasAlteracaoValor = true;
                    
        //             echo "valor atual: ".$data_update["Valor"]."<br>";
        //             echo "valor atualizado: ".$data["Valor"]."<br>";

        //             $valorDiferenca = $data["Valor"] - $data_update["Valor"];
        //             $valorDiferenca = round($valorDiferenca, 2);
                        
        //             echo "- Alteração Valor: ".$valorDiferenca."<br>";
        //         }
        //         else{
        //             echo "Sem Alteração Valor <br>";
        //         }
                
        //         if($data["IdTipoTransacao"] == 1){

        //             echo "IdTipoTransacao: 1 <br><br>";

        //             if($mes == 1)
        //             {
        //                 $data_update["AnoFim"] = $ano - 1;
        //                 $data_update["MesFim"] = 12;                   
        //             }
        //             else{
        //                 $data_update["AnoFim"] = $ano;
        //                 $data_update["MesFim"] = $mes-1;
        //             }

        //             echo "Data Update \/"; var_dump($data_update);
        //             $this->cartao_model->Atualizar($data_update);

        //             $data_insert = $data;

        //             $data_insert["Id"]     = null;
        //             $data_insert["AnoFim"] = 2050;
        //             $data_insert["MesFim"] = 12;   

        //             echo "Data Insert \/"; var_dump($data_insert);
        //             $this->cartao_model->Incluir($data_insert);

        //             if($data_update["Valor"] != $data_insert["Valor"])
        //             {
        //                 echo "valor atual: ".$data_update["Valor"]."<br>";
        //                 echo "valor atualizado: ".$data_insert["Valor"]."<br>";

        //                 $valorDiferenca = $data_insert["Valor"] - $data_update["Valor"];
        //                 $valorDiferenca = round($valorDiferenca, 2);

        //                 echo "- Alteração Valor: ".$valorDiferenca."<br>";

        //                 $dataGeral["Valor"]           = $valorDiferenca; 
        //                 $dataGeral["IdTipoTransacao"] = 1; 
        //                 $dataGeral["Ano"]             = $ano; 
        //                 $dataGeral["Mes"]             = $mes; 
        //                 $dataGeral["Ano"]             = $ano; 
        //                 $dataGeral["Mes"]             = $mes; 

        //                 // -- SALDO GERAL --
        //                 geral_UpdateSaldo($dataGeral,2);
        //                 geral_UpdateCartaoMes($dataGeral);
        //             }

        //         }
        //         else{
                    
        //             if($data["IdTipoTransacao"] == 2){

        //                 $paramBusca["PeriodoDe"]  = true;
        //                 $paramBusca["Descricao"]  = $data["Descricao"];
        //                 $paramBusca["Ano"]        = $data["Ano"];
        //                 $paramBusca["Mes"]        = $data["Mes"];
        //                 $cartaoAtual             = $this->cartao_model->Listar($paramBusca);

        //                 foreach($cartaoAtual as $itemContent){

        //                     // DATA INCLUSAO
        //                     $hasAlteracaoValor = false;

        //                     $dataUpdate                  = array();
        //                     $dataUpdate                  = $itemContent;
        //                     $dataUpdate["Descricao"]     = $data["Descricao"];
        //                     $dataUpdate["Valor"]         = $data["Valor"];
        //                     $dataUpdate["IdCategoria"]   = $data["IdCategoria"];
        //                     $dataUpdate["Descricao"]     = $data["IdSubCategoria"];
        //                     $dataUpdate["DataAlteracao"] = date('Y-m-d H:i:s');

        //                     $this->cartao_model->Atualizar($dataUpdate);

        //                     // -- ALTERACAO VALOR --
        //                     if(($dataUpdate["Valor"] != $itemContent["Valor"])){

        //                         $dataUpdate["Valor"] = $dataUpdate["Valor"] - $itemContent["Valor"];

        //                         // -- SALDO CARTAO --
        //                         geral_UpdateCartaoMes($dataUpdate); 

        //                         // -- SALDO GERAL
        //                         geral_UpdateSaldo($dataUpdate);

        //                     }
        //                 }

        //             }

        //             if($data["IdTipoTransacao"] == 3){

        //                 echo "Transação Tipo: 2 ou 3 <br>";

        //                 $data_update["IdCategoria"] = $data["IdCategoria"];
        //                 $data_update["IdSubCategoria"] = $data["IdSubCategoria"];
        //                 $data_update["Descricao"] = $data["Descricao"];
        //                 $data_update["Valor"] = $data["Valor"];

        //                 var_dump($data_update);

        //                 $this->cartao_model->Atualizar($data_update);        
                        
        //             }

        //             if($hasAlteracaoValor == true)
        //             {
        //                 $data_update["Valor"] = $valorDiferenca; 
                        
        //                 if($data_update["Valor"] > 0){$tipo = 1;}
        //                 else{$tipo = 2;}

        //                 // -- SALDO GERAL --
        //                 geral_UpdateSaldo($data_update,$tipo);
                        
        //                 // -- SALDO GERAL CARTAO
        //                 geral_UpdateSaldoMesCartao($data_update,$tipo);
        //             }
                    
        //         }
                
        //     }
		// 	else{
        //         $ci->session->set_flashdata('msg-error',"Existem campos obrigatórios não preenchidos");
        //     }
            
		// 	// -- MSG SUCESSO - REDIRECT
        //     $this->session->set_flashdata('msg-success',"Transação alterada com sucesso!");
        //     redirect("content/month_content/".$ano."/".$mes);
		// } 
    }