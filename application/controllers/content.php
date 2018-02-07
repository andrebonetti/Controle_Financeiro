<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Content extends CI_Controller{
		    
       public function month_content($pAno,$pMes){
			
        // -- CONFIG    
		$config = config_base(array("showTemplate" => true));//array("rollback" => true,"retorno" => false)); 

        // -- USUARIO --
        $paramBusca["Usuario"]          = valida_acessoUsuario();

        // -- SALDO --
        $paramBusca["Mes"]               = $pMes;
        $paramBusca["Ano"]               = $pAno;
        
        
        $competenciaAtual   	         = geral_competenciaAtualTemplate($paramBusca);//TEMP
        $lcontaUsuario   	             = contas_BuscarContasCompleto($paramBusca);
        $lSaldoMes                       = contas_saldo_GerarSaldoMes($paramBusca,$lcontaUsuario,$competenciaAtual);
        
        
                 
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

            foreach($lSaldoMes as $keySaldo => $itemSaldo){
                $dataMes[1]["ResumoDia"][$keySaldo]["SaldoFinal"] = $itemSaldo["SaldoAnterior"];
            }

            $totalReceita = 0;
            $totalDespesas = 0;
            foreach($dataMes as $dataDia){
                
                $diaSemana            = date("w", mktime(0,0,0,$pMes,$diaNDiaMes,$pAno)); 

                foreach($lSaldoMes as $keySaldo => $itemSaldo){

                    $dataMes[$diaNDiaMes]["ResumoDia"][$keySaldo]["SaldoDia"]    = 0;
                    $dataMes[$diaNDiaMes]["ResumoDia"]["Total"]["SaldoDia"]     = 0;                   

                    if($diaNDiaMes > 1){
                        $dataMes[$diaNDiaMes]["ResumoDia"][$keySaldo]["SaldoFinal"]  = $dataMes[$diaNDiaMes-1]["ResumoDia"][$keySaldo]["SaldoFinal"];                   
                        $dataMes[$diaNDiaMes]["ResumoDia"]["Total"]["SaldoFinal"]   = $dataMes[$diaNDiaMes-1]["ResumoDia"]["Total"]["SaldoFinal"]; 
                    }

                }

                if( (($diaNDiaMes == 9)&&( ($diaSemana != 6)&&($diaSemana != 0) )) || ($diaNDiaMes == 10 && $diaSemana == 1) || ($diaNDiaMes == 11 && $diaSemana == 1) ){
                    
                    $dataMes[$diaNDiaMes]["ResumoDia"][1]["SaldoDia"]           += $competenciaAtual["Cartao"]*-1;
                    $dataMes[$diaNDiaMes]["ResumoDia"][1]["SaldoFinal"]         += $dataMes[$diaNDiaMes]["ResumoDia"][1]["SaldoFinal"];
                    $dataMes[$diaNDiaMes]["ResumoDia"]["Total"]["SaldoDia"]     += $competenciaAtual["Cartao"]*-1;
                    $dataMes[$diaNDiaMes]["ResumoDia"]["Total"]["SaldoFinal"]   += $competenciaAtual["Cartao"]*-1;

                    $totalDespesas += $competenciaAtual["Cartao"];
                                  
                }

                foreach($dataDia["lTransacoes"] as $KeyTransacao =>  $itemTransacao){

                    if($itemTransacao["IsContabilizado"] == 1){

                        $idConta = $itemTransacao["IdConta"];

                        $dataMes[$diaNDiaMes]["ResumoDia"][$idConta]["SaldoDia"]        += $itemTransacao["Valor"]; 
                        $dataMes[$diaNDiaMes]["ResumoDia"][$idConta]["SaldoFinal"]      += $itemTransacao["Valor"]; 

                        $dataMes[$diaNDiaMes]["ResumoDia"]["Total"]["SaldoDia"]         += $itemTransacao["Valor"];  
                        $dataMes[$diaNDiaMes]["ResumoDia"]["Total"]["SaldoFinal"]       += $itemTransacao["Valor"];

                        if($itemTransacao["Valor"] >= 0){
                            $totalReceita += $itemTransacao["Valor"];
                        }
                        else{
                            $totalDespesas += $itemTransacao["Valor"]*(-1);
                        }

                    }
                    
                }
                
                $diaNDiaMes++;
            }

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

        // foreach($dataMes as $Keyday => $dataDia) {

        //         echo"<br>-----------------------------------------------------<br>";
        //         echo "<b>Dia:</b>".$Keyday."<br>";

        //         foreach($dataDia["lTransacoes"] as $KeyTransacao =>  $itemTransacao){
        //             echo $KeyTransacao." - ". var_dump($itemTransacao)."<br>";
        //         }

        //         foreach($dataDia["ResumoDia"] as $KeyResumo =>  $itemResumo){
        //             echo $KeyResumo." - ". var_dump($itemResumo)."<br>";
        //         }

            
        //     }

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
        "lcontaUsuario"      	=> $lcontaUsuario,
        "lSaldoMes"      		=> $lSaldoMes);
		
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