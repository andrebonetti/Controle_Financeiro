<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Content extends CI_Controller{
		    
       public function month_content($pAno,$pMes){
			
        // -- CONFIG    
		$config = config_base(array("showTemplate" => true,"rollback" => true));//array("rollback" => true,"retorno" => false)); 

        // -- USUARIO --
        $paramBusca["Usuario"]          = valida_acessoUsuario();

        // -- SALDO --
        $paramBusca["Mes"]               = $pMes;
        $paramBusca["Ano"]               = $pAno;

        $this->db->trans_begin();

        // -- COMPETENCIAS --
        $lCompetencias                   = $this->geral_model->Listar();   
        $competenciaAtual   	         = geral_competenciaAtualTemplate($paramBusca);//TEMP
        $lcontaUsuario   	             = contas_BuscarContasCompleto($paramBusca,$competenciaAtual);

        util_print($lcontaUsuario,"lcontas");

		// -- DATA --
		$qtdeDiasMes 	                 =  days_in_month($pMes);
		$primeiroDiaMes                  =  date("w", mktime(0,0,0,$pMes,1,$pAno)); 
        $DiaAtual                        =  mdate("%d"); 
        $paramBusca["isListaPorTipo"]    =  true; 
                
        $modeloCalendario = 1;

        if($modeloCalendario == 1){
            $primeiroDiaMes -= 1;
        }

		// ---------- CONTEUDO ---------

        /* -- CALCULO SALDO INICIAL-- */    
        foreach($lcontaUsuario["Contas_Banco"] as $keySaldo => $itemSaldo){
            $lcontaUsuario["Contas_Banco"][$keySaldo]["SaldoTela"]["SaldoFinal"]        =  $itemSaldo["Saldo"]["SaldoAnterior"];
        }

        $totalReceita   = 0;
        $totalDespesas  = 0;
        
        // -- DIAS --    
        for ($dia = 1; $dia <= 31 ;$dia++){
            
            if($dia <= $qtdeDiasMes){
                $diaT = $dia;
                $dataMes[$diaT]["lTransacoes"]  = array();
                $dataMes[$diaT]["lFaturas"]      = array();

                //Zera Saldo Inicial Dia          
                foreach($lcontaUsuario["Contas_Banco"] as $keySaldo => $itemSaldo){
                    $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$keySaldo]["SaldoDia"]           = 0;
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

            foreach($lTransacoes as $KeyTransacao => $itemTransacao){

                if($itemTransacao["IsContabilizado"] == 1){

                    $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdConta"]]["SaldoDia"]              += $itemTransacao["Valor"]; 
                    $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdConta"]]["SaldoFinal"]            += $itemTransacao["Valor"];

                    if($itemTransacao["IsTransferencia"] == 1){ 
                        $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdContaOrigem"]]["SaldoDia"]    -= $itemTransacao["Valor"]; 
                        $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdContaOrigem"]]["SaldoFinal"]  -= $itemTransacao["Valor"]; 
                    } 
                     
                    if($itemTransacao["Valor"] >= 0){$totalReceita += $itemTransacao["Valor"];}
                    else{$totalDespesas += $itemTransacao["Valor"]*(-1);}
                    
                }

                array_push($dataMes[$diaT]["lTransacoes"],$itemTransacao);

            }

            if( (($diaT == 9)&&( ($diaSemana != 6)&&($diaSemana != 0) )) || ($diaT == 10 && $diaSemana == 1) || ($diaT == 11 && $diaSemana == 1) ){
                    
                if(isset($dataMes[$diaT]["ResumoDia"]["Contas_Banco"][1])){
                    $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][1]["SaldoDia"]           += $competenciaAtual["Cartao"]*-1;
                    $dataMes[$diaT]["ResumoDia"]["Contas_Banco"][1]["SaldoFinal"]         += $competenciaAtual["Cartao"]*-1;
                }

                $itemFaturaCartao["Descricao"]  = "Fatura Cartao"; 
                $itemFaturaCartao["Valor"]      = $competenciaAtual["Cartao"]*-1;
                $itemFaturaCartao["IdConta"]    = 1;

                array_push($dataMes[$diaT]["lFaturas"],$itemFaturaCartao);

                $totalDespesas += $competenciaAtual["Cartao"];
                                
            }

            $saldoGeralDia   = 0;
            $saldoGeralFinal = 0;

            foreach($dataMes[$diaT]["ResumoDia"]["Contas_Banco"] as $keyConta => $itemConta){
                $lcontaUsuario["Contas_Banco"][$keyConta]["SaldoTela"]["SaldoFinal"] = $itemConta["SaldoFinal"];
                
                $saldoGeralDia   += $itemConta["SaldoDia"];
                $saldoGeralFinal += $itemConta["SaldoFinal"];
            }

            $dataMes[$diaT]["ResumoDia"]["Geral"]["SaldoDia"]   = $saldoGeralDia;
            $dataMes[$diaT]["ResumoDia"]["Geral"]["SaldoFinal"] = $saldoGeralFinal;
        }

        //$lcontaUsuario  = contas_saldo_validarConsistencia($lcontaUsuario,$dataMes[$qtdeDiasMes]["ResumoDia"]);

        // -- CARTAO --
        $data_cartao = array();
        
        foreach($this->cartao_de_credito_model->ListarFaturaRecorrente($paramBusca) as $itemContent){
            array_push($data_cartao,$itemContent);
        }  
        
        foreach($this->cartao_de_credito_model->ListarFaturaSimplesParcelada($paramBusca) as $itemContent){
            array_push($data_cartao,$itemContent);
        }
            
        // -- CATEGORIAS --
        $lCategorias         = $this->categoria_model->Listar();	
        $lSubCategoriasTotal =  $this->subCategoria_model->Listar();	
        //$lCategoriasFinal    = [];  
        foreach($lCategorias as $itemCategoria)
        {
            $pDataSubCategoria["IdCategoria"] = $itemCategoria["IdCategoria"];
            $lSubCategorias = $this->subCategoria_model->Listar($pDataSubCategoria);
            
            $lCategoriasFinal[$itemCategoria["DescricaoCategoria"]] = $lSubCategorias;
        }    

        // -- CARTOES --
        $lCartoes         = $this->cartoes_model->ListarCartoesAtivos(array("Ano"=>$pAno,"Mes"=>$pMes));

        $paramBusca["IdCartao"] = 1;
        unset($paramBusca["Dia"]);

        $contCartao = 0;
        foreach($lCartoes as $itemCartao){

            $lCartoes[$contCartao]["lTransacao"] = array();

            if($itemCartao["Id"] == 1){

                foreach($this->cartao_de_credito_model->ListarFaturaRecorrente($paramBusca) as $itemContent){
                    array_push($lCartoes[$contCartao]["lTransacao"],$itemContent);
                }  
            
                foreach($this->cartao_de_credito_model->ListarFaturaSimplesParcelada($paramBusca) as $itemContent){
                    array_push($lCartoes[$contCartao]["lTransacao"],$itemContent);
                }

            }

            foreach(buscarTransacoes(1,$paramBusca) as $transacao){array_push($lCartoes[$contCartao]["lTransacao"],$transacao);}
            foreach(buscarTransacoes(2,$paramBusca) as $transacao){array_push($lCartoes[$contCartao]["lTransacao"],$transacao);}
            foreach(buscarTransacoes(3,$paramBusca) as $transacao){array_push($lCartoes[$contCartao]["lTransacao"],$transacao);}

            $contCartao++;
        }

        config_finalTransaction($config);

        //util_print($dataMes,"DataMes");
        //util_print($lcontaUsuario,"lcontaUsuario");

        $dataAtual["Ano"] = $pAno;
        $dataAtual["Mes"] = $pMes;
        $dataAtual["Dia"] = $DiaAtual;
        $dataAtual["PrimeiroDiaMes"] = $primeiroDiaMes;

		// --------------------------CONTENT----------------------------------
		$content = array(     
		"usuario"		  		=> $paramBusca["Usuario"],
        "dataAtual"             => $dataAtual,
        "lCartao"               => $lCartoes,
		"categorias"	  		=> $lCategorias,
        "fatura_cartao"   		=> $data_cartao,
        "all_sub_categorias"  	=> $lSubCategoriasTotal,
        "sub_categorias"  		=> $lCategoriasFinal,      
        "lCompetencias"         => $lCompetencias,
		"dataMes"      		    => $dataMes,
        "lcontaUsuario"      	=> $lcontaUsuario);
		
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