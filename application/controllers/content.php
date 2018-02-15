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
          
        $competenciaAtual   	         = geral_competenciaAtualTemplate($paramBusca);//TEMP
        $lcontaUsuario   	             = contas_BuscarContasCompleto($paramBusca,$competenciaAtual);

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
            
            // -- DIAS --    
            for ($diaN = 1; $diaN <= $qtdeDiasMes ;$diaN++){
                
                $dataMes[$diaN]                         = array();
                $dataMes[$diaN]["lTransacoes"]          = array();
                $paramBusca["Dia"]                      = $diaN;
                $paramBusca["Mes"]                      = $pMes;
                $paramBusca["Ano"]                      = $pAno;
                $paramBusca["PreencherEntidadesFilhas"] = true;
                
                foreach(buscarTransacoesPorTipo(1,$paramBusca) as $transacao){array_push($dataMes[$transacao["DiaCalendario"]]["lTransacoes"],$transacao);}
                foreach(buscarTransacoesPorTipo(2,$paramBusca) as $transacao){array_push($dataMes[$transacao["DiaCalendario"]]["lTransacoes"],$transacao);}
                foreach(buscarTransacoesPorTipo(3,$paramBusca) as $transacao){array_push($dataMes[$transacao["DiaCalendario"]]["lTransacoes"],$transacao);}
                
            }

            // -- COMPETENCIAS --
            $lCompetencias = $this->geral_model->Listar(); 
       
            /* -- CALCULO SALDO -- */
            $diaNDiaMes = 1;
            foreach($lcontaUsuario["Contas_Banco"] as $keySaldo => $itemSaldo){
                $dataMes[1]["ResumoDia"]["Contas_Banco"][$itemSaldo["Id"]]["SaldoDia"]     = 0;
                $dataMes[1]["ResumoDia"]["Contas_Banco"][$itemSaldo["Id"]]["SaldoFinal"]   = $itemSaldo["Saldo"]["SaldoAnterior"];
            }
            $dataMes[1]["ResumoDia"]["Geral"]["SaldoDia"]           = 0;
            $dataMes[1]["ResumoDia"]["Geral"]["SaldoFinal"]         = $lcontaUsuario["Geral"]["Saldo"]["SaldoAnterior"];

            $totalReceita = 0;
            $totalDespesas = 0;
            foreach($dataMes as $dataDia){
                
                $diaSemana            = date("w", mktime(0,0,0,$pMes,$diaNDiaMes,$pAno)); 

                foreach($lcontaUsuario["Contas_Banco"] as $keySaldo => $itemSaldo){
                    $dataMes[$diaNDiaMes]["ResumoDia"]["Contas_Banco"][$itemSaldo["Id"]]["SaldoDia"]    = 0;
                    $dataMes[$diaNDiaMes]["ResumoDia"]["Contas_Banco"][$itemSaldo["Id"]]["CSS"]         =  $lcontaUsuario["Contas_Banco"][$itemSaldo["Id"]]["CSS"]; 
                               
                    if($diaNDiaMes > 1){
                        $dataMes[$diaNDiaMes]["ResumoDia"]["Contas_Banco"][$itemSaldo["Id"]]["SaldoFinal"]  = $dataMes[$diaNDiaMes-1]["ResumoDia"]["Contas_Banco"][$itemSaldo["Id"]]["SaldoFinal"];                   
                    }
                }

                $dataMes[$diaNDiaMes]["ResumoDia"]["Geral"]["SaldoDia"]             = 0;    
                if($diaNDiaMes > 1){
                    $dataMes[$diaNDiaMes]["ResumoDia"]["Geral"]["SaldoFinal"]       = $dataMes[$diaNDiaMes-1]["ResumoDia"]["Geral"]["SaldoFinal"];
                } 

                if( (($diaNDiaMes == 9)&&( ($diaSemana != 6)&&($diaSemana != 0) )) || ($diaNDiaMes == 10 && $diaSemana == 1) || ($diaNDiaMes == 11 && $diaSemana == 1) ){
                    
                    if(isset($dataMes[$diaNDiaMes]["ResumoDia"]["Contas_Banco"][1])){
                        $dataMes[$diaNDiaMes]["ResumoDia"]["Contas_Banco"][1]["SaldoDia"]           += $competenciaAtual["Cartao"]*-1;
                        $dataMes[$diaNDiaMes]["ResumoDia"]["Contas_Banco"][1]["SaldoFinal"]         += $competenciaAtual["Cartao"]*-1;
                    }
                    $dataMes[$diaNDiaMes]["ResumoDia"]["Geral"]["SaldoDia"]     += $competenciaAtual["Cartao"]*-1;
                    $dataMes[$diaNDiaMes]["ResumoDia"]["Geral"]["SaldoFinal"]   += $competenciaAtual["Cartao"]*-1;

                    $totalDespesas += $competenciaAtual["Cartao"];
                                  
                }

                foreach($dataDia["lTransacoes"] as $KeyTransacao =>  $itemTransacao){

                    if($itemTransacao["IsContabilizado"] == 1){

                        $dataMes[$diaNDiaMes]["lTransacoes"][$KeyTransacao]["Conta"]                                            =  $lcontaUsuario["Contas_Banco"][$itemTransacao["IdConta"]];
                        
                        if($itemTransacao["IsTransferencia"] == 1){

                            $dataMes[$diaNDiaMes]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdConta"]]["SaldoDia"]           += $itemTransacao["Valor"]; 
                            $dataMes[$diaNDiaMes]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdConta"]]["SaldoFinal"]         += $itemTransacao["Valor"]; 

                            $dataMes[$diaNDiaMes]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdContaOrigem"]]["SaldoDia"]     -= $itemTransacao["Valor"]; 
                            $dataMes[$diaNDiaMes]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdContaOrigem"]]["SaldoFinal"]   -= $itemTransacao["Valor"]; 

                        }else{
                            if(isset($dataMes[$diaNDiaMes]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdConta"]])){
                                $dataMes[$diaNDiaMes]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdConta"]]["SaldoDia"]       += $itemTransacao["Valor"]; 
                                $dataMes[$diaNDiaMes]["ResumoDia"]["Contas_Banco"][$itemTransacao["IdConta"]]["SaldoFinal"]     += $itemTransacao["Valor"]; 
                            }

                            $dataMes[$diaNDiaMes]["ResumoDia"]["Geral"]["SaldoDia"]                                             += $itemTransacao["Valor"];  
                            $dataMes[$diaNDiaMes]["ResumoDia"]["Geral"]["SaldoFinal"]                                           += $itemTransacao["Valor"];

                            if($itemTransacao["Valor"] >= 0){
                                $totalReceita += $itemTransacao["Valor"];
                            }
                            else{
                                $totalDespesas += $itemTransacao["Valor"]*(-1);
                            }
                        }

                    }
                    
                }
                
                $diaNDiaMes++;
            }

            $lcontaUsuario  = contas_saldo_validarConsistencia($lcontaUsuario,$dataMes[$qtdeDiasMes]["ResumoDia"]);
            //$competenciaAtual = geral_verificarConsistencia($competenciaAnterior,$competenciaAtual,$totalReceita,$totalDespesas,$saldoFinalDia);

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

            foreach(buscarTransacoesPorTipo(1,$paramBusca) as $transacao){array_push($lCartoes[$contCartao]["lTransacao"],$transacao);}
            foreach(buscarTransacoesPorTipo(2,$paramBusca) as $transacao){array_push($lCartoes[$contCartao]["lTransacao"],$transacao);}
            foreach(buscarTransacoesPorTipo(3,$paramBusca) as $transacao){array_push($lCartoes[$contCartao]["lTransacao"],$transacao);}

            $contCartao++;
        }

        config_finalTransaction($config);

        //util_printArray($dataMes,"DataMes");

		// --------------------------CONTENT----------------------------------
		$content = array( 
		"competenciaAtual"		=> $competenciaAtual,       
		"usuario"		  		=> $paramBusca["Usuario"],
		"ano"			  		=> $pAno,
		"mes"			  		=> $pMes,
        "hoje"                  => $DiaAtual,
        "lCartao"               => $lCartoes,
		"categorias"	  		=> $lCategorias,
		"all_sub_categorias"  	=> $lSubCategoriasTotal,
        "fatura_cartao"   		=> $data_cartao,
        "sub_categorias"  		=> $lCategoriasFinal,      
        "primeiroDiaMes"       	=> $primeiroDiaMes,  
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