<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Content extends CI_Controller{
		    
       public function month_content($pAno,$pMes,$pConta = null){

        $this->output->enable_profiler(FALSE);

        // -- CONFIG    
		$config = config_base(array("showTemplate" => true,"rollback" => false));//array("rollback" => true,"retorno" => false)); 

        // -------------------------- USUARIO LOGADO ----------------------------------------
        $paramBusca["Usuario"]                      = valida_acessoUsuario();

        // -- SALDO --
        $paramBusca["Mes"]                          = $pMes;
        $paramBusca["Ano"]                          = $pAno;

        $this->db->trans_begin();

        #COMPETENCIAS --
        $paramListaU["Usuario"]["Id"]               = $paramBusca["Usuario"]["Id"];
        $lCompetencias                              = $this->geral_model->Listar($paramListaU); 
        
        //util_print($lCompetencias[count($lCompetencias)-1],"lCompetencias Ultima");
        
        #BUSCA/CRIA      
        $competenciaAtual   	                    = geral_competenciaAtualTemplate($paramBusca);//TEMP

        //util_print($competenciaAtual,"competencia_Atual");

        #CARTOES - FATURA
        $paramCartoesFatura["Cartoes"]["IdUsuario"] = $paramBusca["Usuario"]["Id"];
        $paramCartoesFatura["Ano"]                  = $competenciaAtual["Ano"];
        $paramCartoesFatura["Mes"]                  = $competenciaAtual["Mes"];

        //util_print($paramCartoesFatura,"paramCartoesFatura");

        $lCartoes_Fatura                            = cartao_buscarFaturasMes($paramCartoesFatura);   

        //util_print($lCartoes_Fatura,"lCartoes_Fatura");

        $lcontaUsuario   	                        = contas_BuscarContasCompleto($paramBusca,$competenciaAtual);

        //util_print($lcontaUsuario,"lcontaUsuario");

		// -- DATA --
		$qtdeDiasMes 	                            =  days_in_month($pMes);
		$primeiroDiaMes                             =  date("w", mktime(0,0,0,$pMes,1,$pAno)); 
        $DiaAtual                                   =  mdate("%d"); 
        $paramBusca["isListaPorTipo"]               =  true; 
                
        $modeloCalendario = 1;
      
        if($modeloCalendario == 1){
            if($primeiroDiaMes > 0){
                $primeiroDiaMes -= 1;
            }else{
                $primeiroDiaMes = 6;
            }
        }

		// ---------- CONTEUDO ---------

        /* -- CALCULO SALDO INICIAL-- */    
        foreach($lcontaUsuario["Contas_Banco"] as $keySaldo => $itemSaldo){
            $lcontaUsuario["Contas_Banco"][$keySaldo]["SaldoTela"]["SaldoFinal"]     =  $itemSaldo["Saldo"]["SaldoAnterior"];
            $lcontaUsuario["Contas_Banco"][$keySaldo]["SaldoTela"]["Despesas"]       =  0;
            $lcontaUsuario["Contas_Banco"][$keySaldo]["SaldoTela"]["Receita"]        =  0;
        }
     
        if(count($lcontaUsuario["Contas_Banco"]) == 0){
            $lcontaUsuario["Contas_Banco"][1]["Id"] = 1;
            $lcontaUsuario["Contas_Banco"][1]["IdUsuario"] = 1;
            $lcontaUsuario["Contas_Banco"][1]["IdBanco"] = 1;
            $lcontaUsuario["Contas_Banco"][1]["Agencia"]= 6404;
            $lcontaUsuario["Contas_Banco"][1]["Conta"] = 25462-2;
            $lcontaUsuario["Contas_Banco"][1]["IdTipo"] = 1;
            $lcontaUsuario["Contas_Banco"][1]["Descricao"] = "Itau C/C";
            $lcontaUsuario["Contas_Banco"][1]["Ordem"] = 1;
            $lcontaUsuario["Contas_Banco"][1]["AnoInicio"] = 2018;
            $lcontaUsuario["Contas_Banco"][1]["MesInicio"] = 2;
            $lcontaUsuario["Contas_Banco"][1]["ValorInicio"] = -123;
            $lcontaUsuario["Contas_Banco"][1]["CSS"] = "background-color: orange";
            $lcontaUsuario["Contas_Banco"][1]["Saldo"]["SaldoAnterior"] = 0;
            $lcontaUsuario["Contas_Banco"][1]["Saldo"]["SaldoMes"] = 0;
            $lcontaUsuario["Contas_Banco"][1]["Saldo"]["SaldoFinal"] = 0;
            $lcontaUsuario["Contas_Banco"][1]["SaldoTela"]["SaldoFinal"] = 0;
            $lcontaUsuario["Contas_Banco"][1]["SaldoTela"]["Despesas"] = 0;
            $lcontaUsuario["Contas_Banco"][1]["SaldoTela"]["Receita"] = 0;
        }

        //util_print($lcontaUsuario,"lcontaUsuario => SALDO INICIAL");

        // -- CONSISTENCIA VALOR FATURA --
        $lCartoes           = $this->cartoes_model->ListarCartoesAtivos(array("Ano"=>$pAno,"Mes"=>$pMes));
        $lCartoes           = util_transforamaIdEmChave($lCartoes,"Id");

        //util_print($lCartoes,"lCartoes");
        $paramBuscaCartao = $paramBusca;
        unset($paramBuscaCartao["Dia"]);
        foreach($lCartoes as $itemCartao){
            $paramBuscaCartao["IdCartao"] = $itemCartao["Id"];
            $lTransacoesCartao          = $this->transacoes_model->ListarPorRegraTipo($paramBuscaCartao);
            $valorFaturaConsistencia    = 0;
            foreach($lTransacoesCartao as $transacao){
                $valorFaturaConsistencia += $transacao["Valor"];
            }

            if(
                ($paramBusca["Mes"] >= 9 && $paramBusca["Ano"] == 2019)
                ||
                ($paramBusca["Ano"] > 2019)
            ){
                foreach($lCartoes_Fatura as $keyFatura => $itemFatura){
                    if($itemCartao["Id"] == $itemFatura["Id"]){
                        
                        if(util_diferenca($itemFatura["Valor"],$valorFaturaConsistencia,true)){
                            cartao_UpdateValorMes($itemFatura,util_diferenca($valorFaturaConsistencia,$itemFatura["Valor"]));
                            $lCartoes_Fatura[$keyFatura]["Valor"] = $valorFaturaConsistencia;
                        }
                    }
                    
                }
            }
            
        }

        // -- DIAS --    
        for ($dia = 1; $dia <= 31 ;$dia++){
            
            if($dia <= $qtdeDiasMes){

                $diaT = $dia;
                $dataMes[$diaT]["lTransacoes"]  = array();
                $dataMes[$diaT]["lFaturas"]      = array();

                //Zera Saldo Inicial Dia          
                foreach($lcontaUsuario["Contas_Banco"] as $keySaldo => $itemSaldo){
                    $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$keySaldo]["SaldoDia"]           = 0;
                    $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$keySaldo]["Despesas"]           = $lcontaUsuario["Contas_Banco"][$keySaldo]["SaldoTela"]["Despesas"];
                    $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$keySaldo]["Receita"]            = $lcontaUsuario["Contas_Banco"][$keySaldo]["SaldoTela"]["Receita"];
                    $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$keySaldo]["SaldoFinal"]         = $lcontaUsuario["Contas_Banco"][$keySaldo]["SaldoTela"]["SaldoFinal"];
                    $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$keySaldo]["CSS"]                = $lcontaUsuario["Contas_Banco"][$itemSaldo["Id"]]["CSS"];                              
                }

            }
            else{
                $diaT = $qtdeDiasMes;
            }
            
            $paramBusca["Dia"]                      = $dia;
            $paramBusca["Mes"]                      = $pMes;
            $paramBusca["Ano"]                      = $pAno;
            $paramBusca["PreencherEntidadesFilhas"] = true;

            $diaSemana                              = date("w", mktime(0,0,0,$paramBusca["Mes"],$paramBusca["Dia"],$paramBusca["Ano"])); 
        
            $lTransacoes                            = $this->transacoes_model->ListarPorRegraTipo($paramBusca);

            //util_print($lTransacoes,"lTransacoes => ".$dia);

            foreach($lTransacoes as $KeyTransacao => $itemTransacao){

                if($itemTransacao["IsContabilizado"] == 1){

                    $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdConta"]]["SaldoDia"]              += $itemTransacao["Valor"]; 
                    $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdConta"]]["SaldoFinal"]            += $itemTransacao["Valor"];

                    if($itemTransacao["IsTransferencia"] == 1){ 
                        $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdContaOrigem"]]["SaldoDia"]    -= $itemTransacao["Valor"]; 
                        $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdContaOrigem"]]["SaldoFinal"]  -= $itemTransacao["Valor"]; 
                    } 
                     
                    if($itemTransacao["IsTransferencia"] == false){// || ($itemTransacao["IsTransferencia"] == true && $itemTransacao["IdContaOrigem"] != $itemTransacao["IdConta"] )){ 
                        if($itemTransacao["Valor"] >= 0){
                            $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdConta"]]["Receita"]      += $itemTransacao["Valor"];
                            $lcontaUsuario["Contas_Banco"][$itemTransacao["IdConta"]]["SaldoTela"]["Receita"]       += $itemTransacao["Valor"];
                        }
                        else{
                            $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdConta"]]["Despesas"]     += $itemTransacao["Valor"]*(-1);
                            $lcontaUsuario["Contas_Banco"][$itemTransacao["IdConta"]]["SaldoTela"]["Despesas"]      += $itemTransacao["Valor"]*(-1);
                        }
                    }

                    if($itemTransacao["IsTransferencia"] == true){

                        $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdContaOrigem"]]["Despesas"]     += $itemTransacao["Valor"];
                        $lcontaUsuario["Contas_Banco"][$itemTransacao["IdContaOrigem"]]["SaldoTela"]["Despesas"]      += $itemTransacao["Valor"];

                        $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdConta"]]["Receita"]            += $itemTransacao["Valor"];
                        $lcontaUsuario["Contas_Banco"][$itemTransacao["IdConta"]]["SaldoTela"]["Receita"]             += $itemTransacao["Valor"];

                    }
                  
                }

                array_push($dataMes[$diaT]["lTransacoes"],$itemTransacao);

            }

            //CARTOES
            if(count($lCartoes_Fatura) > 0){
               
                //util_print($lCartoes_Fatura,"lCartoes_Fatura");
                foreach($lCartoes_Fatura as $keyFatura => $itemFatura){
                    
                    //echo $diaT." - ".$itemFatura["DiaVencimento"]."<br>";
                    if(!util_diferenca($diaT,$itemFatura["DiaVencimento"],true)){
                        $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$itemFatura["IdConta"]]["SaldoDia"]           += $itemFatura["Valor"];
                        $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$itemFatura["IdConta"]]["SaldoFinal"]         += $itemFatura["Valor"];

                        if($itemFatura["Valor"] >= 0){
                            $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$itemFatura["IdConta"]]["Receita"]        += $itemFatura["Valor"];
                            $lcontaUsuario["Contas_Banco"][$itemFatura["IdConta"]]["SaldoTela"]["Receita"]         += $itemFatura["Valor"]; 
                        }
                        else{
                            $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$itemFatura["IdConta"]]["Despesas"]        += $itemFatura["Valor"]*(-1);
                            $lcontaUsuario["Contas_Banco"][$itemFatura["IdConta"]]["SaldoTela"]["Despesas"]         += $itemFatura["Valor"]*(-1);   
                        }
                        
                        $itemFaturaCartao["Descricao"]  = $itemFatura["Descricao"]; 
                        $itemFaturaCartao["Valor"]      = $itemFatura["Valor"];
                        $itemFaturaCartao["IdConta"]    = $itemFatura["IdConta"];

                        array_push($dataMes[$diaT]["lFaturas"],$itemFaturaCartao);

                    }
                                     
                }
             
            }else{
                //LEGADO / SEM CARTAO
                if( (($diaT == 9)&&( ($diaSemana != 6)&&($diaSemana != 0) )) || ($diaT == 10 && $diaSemana == 1) || ($diaT == 11 && $diaSemana == 1) ){
                    
                    if(isset($dataMes[$diaT]["ResumoDia"]["Contas_Banco"][1])){
                        $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][1]["SaldoDia"]           += $competenciaAtual["Cartao"]*-1;
                        $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][1]["SaldoFinal"]         += $competenciaAtual["Cartao"]*-1;
                        $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][1]["Despesas"]           += $competenciaAtual["Cartao"];

                        $itemFaturaCartao["Descricao"]  = "Fatura Cartao"; 
                        $itemFaturaCartao["Valor"]      = $competenciaAtual["Cartao"]*-1;
                        $itemFaturaCartao["IdConta"]    = 1;

                        array_push($dataMes[$diaT]["lFaturas"],$itemFaturaCartao);   
                    }           
                }
            }

            $saldoGeralDia      = 0;
            $saldoGeralFinal    = 0;
            $despesasGeralDia   = 0;
            $receitaGeralDia    = 0;

            foreach($dataMes[$diaT]["ResumoDia"]["Contas_Banco"] as $keyConta => $itemConta){
                $lcontaUsuario["Contas_Banco"][$keyConta]["SaldoTela"]["SaldoFinal"] = $itemConta["SaldoFinal"];
                $lcontaUsuario["Contas_Banco"][$keyConta]["SaldoTela"]["Despesas"]   = $itemConta["Despesas"];
                $lcontaUsuario["Contas_Banco"][$keyConta]["SaldoTela"]["Receita"]    = $itemConta["Receita"];
                
                $saldoGeralDia      += $itemConta["SaldoDia"];
                $saldoGeralFinal    += $itemConta["SaldoFinal"];
                $despesasGeralDia   += $itemConta["Despesas"];
                $receitaGeralDia    += $itemConta["Receita"];

                //$receitaGeralDia    -= $receitaTransferênciaGeral;
            }

            $dataMes[$diaT]["ResumoDia"]["Geral"]["SaldoDia"]   = $saldoGeralDia;
            $dataMes[$diaT]["ResumoDia"]["Geral"]["SaldoFinal"] = $saldoGeralFinal;
            $dataMes[$diaT]["ResumoDia"]["Geral"]["Receita"]    = $receitaGeralDia;
            $dataMes[$diaT]["ResumoDia"]["Geral"]["Despesas"]   = $despesasGeralDia;

            //util_print($dataMes[$diaT]["ResumoDia"]["Geral"],"Saldo => ".$diaT);
        }

        //util_print($paramBusca,"paramBusca");
        if(
            ($paramBusca["Mes"] >= 2 && $paramBusca["Ano"] == 2018)
            ||
            ($paramBusca["Ano"] > 2018)
        ){
            //util_print($lcontaUsuario,"lcontaUsuario => ANTES CONSISTENCIA");
            $lcontaUsuario  = contas_saldo_validarConsistencia($lcontaUsuario,$dataMes[$qtdeDiasMes]["ResumoDia"]);
            //util_print($lcontaUsuario,"lcontaUsuario => DEPOIS CONSISTENCIA");
        }

        // -- CARTOES --
        //util_print($lCartoes,"lCartoes");
        $paramBuscaCartao = $paramBusca;
        unset($paramBuscaCartao["Dia"]);
        foreach($lCartoes as $itemCartao){

            $lCartoes[$itemCartao["Id"]]["lTransacao"] = array();
            $lCartoes[$itemCartao["Id"]]["Saldo"]     = 0;

            $paramBuscaCartao["IdCartao"] = $itemCartao["Id"];

            $lTransacoesCartao          = $this->transacoes_model->ListarPorRegraTipo($paramBuscaCartao);
            $valorFaturaConsistencia    = 0;
            foreach($lTransacoesCartao as $transacao){
                $lCartoes[$itemCartao["Id"]]["Saldo"] += $transacao["Valor"];
                array_push($lCartoes[$itemCartao["Id"]]["lTransacao"],$transacao);
            }
            
        }

        // -- CARTAO --
        $data_cartao = array();
        
        foreach($this->cartao_de_credito_model->ListarFaturaRecorrente($paramBusca) as $itemContent){
            array_push($data_cartao,$itemContent);
        }  
        
        foreach($this->cartao_de_credito_model->ListarFaturaSimplesParcelada($paramBusca) as $itemContent){
            array_push($data_cartao,$itemContent);
        }
            
        // -- CATEGORIAS --
        $paramCat["Usuario"]["Id"]  = $paramBusca["Usuario"]["Id"];
        $lCategorias                = $this->categoria_model->Listar($paramCat);	
        $lSubCategoriasTotal        = $this->subCategoria_model->Listar($paramCat);
        $lCategoriasFinal           = array();	
        foreach($lCategorias as $itemCategoria)
        {
            $pDataSubCategoria["IdCategoria"] = $itemCategoria["IdCategoria"];
            $lSubCategorias = $this->subCategoria_model->Listar($pDataSubCategoria);
            
            $lCategoriasFinal[$itemCategoria["DescricaoCategoria"]] = $lSubCategorias;
        }    
  
        config_finalTransaction($config);

        //util_print($dataMes,"DataMes");
        //util_print($lCartoes,"CARTOES");
        //util_print($lcontaUsuario,"lcontaUsuario");

        #ALTERA ORDEM CONTAS
        if(!empty($pConta)){
            $tempLContas = array();

            foreach($lcontaUsuario["Contas_Banco"] as $keyConta => $itemConta){
                if($keyConta == $pConta){
                    $tempLContas[$keyConta] = $itemConta;
                    unset($lcontaUsuario["Contas_Banco"][$keyConta]);
                }
            }
            foreach($lcontaUsuario["Contas_Banco"] as $keyConta => $itemConta){
                $tempLContas[$keyConta] = $itemConta;
            }

            $lcontaUsuario["Contas_Banco"] = $tempLContas;
        }


        $dataAtual["Ano"] = $pAno;
        $dataAtual["Mes"] = $pMes;
        $dataAtual["Dia"] = $DiaAtual;
        $dataAtual["PrimeiroDiaMes"] = $primeiroDiaMes;
        
        $principal = 1;

        foreach($lcontaUsuario["Contas_Banco"] as $keyConta => $itemConta){
            if($itemConta["Ordem"] == 1){
                $principal = $keyConta;
                continue;
            }
        }

		// --------------------------CONTENT----------------------------------
		$content = array(     
		"usuario"		  		=> $paramBusca["Usuario"],
        "dataAtual"             => $dataAtual,
        "lCartoes"              => $lCartoes,
		"categorias"	  		=> $lCategorias,
        "fatura_cartao"   		=> $data_cartao,
        "all_sub_categorias"  	=> $lSubCategoriasTotal,
        "sub_categorias"  		=> $lCategoriasFinal,      
        "lCompetencias"         => $lCompetencias,
		"dataMes"      		    => $dataMes,
        "lcontaUsuario"      	=> $lcontaUsuario,
        "contaPrincipal"        => $principal);

        //util_print($content,"Conteudo Controller");
		
        if($config["showTemplate"]){
            // -- VIEW --
            $this->load->template("template_content.php",$content);
        }
           
	   }

	   public function geral(){

           $this->output->enable_profiler(TRUE);
	   		
            $data["OrderBy"] = "Ano,Mes";
            $lGeral = $this->geral_model->Listar($data);

			$content = array( 
				"lGeral"		=> $lGeral,
			);
		
			//VIEW
            $this->load->template("template_geral.php",$content);
            	
		
	   }	
    }