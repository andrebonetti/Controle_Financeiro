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
        $paramBusca["isListaPorTipo"]    = true;
        $paramBusca["HasInnerJoin"]      = true;

        $competenciaAtual   	         = $this->geral_model       ->Buscar($paramBusca);
        $lcontaUsuario   	             = $this->contas_model      ->Listar($paramBusca);
        
        $lSaldoMes["Total"]["SaldoAnterior"] = 0;   
        $lSaldoMes["Total"]["SaldoMes"] = 0;  
        $lSaldoMes["Total"]["SaldoFinal"] = 0;                  
        foreach($lcontaUsuario as $itemConta){
            $lSaldoMes[$itemConta["Id"]]         =  $itemConta["Saldo"];
            $lSaldoMes["Total"]["SaldoAnterior"] += $itemConta["Saldo"]["SaldoAnterior"];
            $lSaldoMes["Total"]["SaldoMes"]      += $itemConta["Saldo"]["SaldoMes"];
            $lSaldoMes["Total"]["SaldoFinal"]    += $itemConta["Saldo"]["SaldoFinal"];
        }                  

        if(empty($competenciaAtual)){
            geral_CriarCompetencia($paramBusca);
            $competenciaAtual = $this->geral_model->Buscar($paramBusca);
        }
           
        $competenciaAnterior = geral_BuscarCompetenciaAnterior($pAno,$pMes);
        
		// -- DATA --
		$qtdeDiasMes 	=  days_in_month($pMes);
		$primeiroDiaMes =  date("w", mktime(0,0,0,$pMes,1,$pAno)); 
        $DiaAtual       =  mdate("%d");  
                
        $modeloCalendario = 1;

        if($modeloCalendario == 1){
            $primeiroDiaMes -= 1;
        }

		// ---------- CONTEUDO ---------
            
            // -- DIAS --    
            for ($diaN = 1; $diaN <= $qtdeDiasMes ;$diaN++){
                
                $dataMes[$diaN]                        = array();
                $paramBusca["Dia"]                      = $diaN;
                $paramBusca["Mes"]                      = $pMes;
                $paramBusca["Ano"]                      = $pAno;
                $paramBusca["PreencherEntidadesFilhas"] = true;
                
                foreach(buscarTransacoesPorTipo(1,$paramBusca) as $transacao){array_push($dataMes[$transacao["DiaCalendario"]],$transacao);}
                foreach(buscarTransacoesPorTipo(2,$paramBusca) as $transacao){array_push($dataMes[$transacao["DiaCalendario"]],$transacao);}
                foreach(buscarTransacoesPorTipo(3,$paramBusca) as $transacao){array_push($dataMes[$transacao["DiaCalendario"]],$transacao);}
                
            }
           
            /* -- CALCULO SALDO -- */
            $diaNDiaMes = 1;
            $saldoFinalDia = $competenciaAnterior["SaldoFinal"];
            $totalReceita = 0;
            $totalDespesas = 0;
            foreach($dataMes as $dataDia){
                
                $diaSemana            = date("w", mktime(0,0,0,$pMes,$diaNDiaMes,$pAno)); 
                $diaNDiaMesTransacoes = 0;
                $dataMes[$diaNDiaMes]["ResumoDia"]["IsResumo"] = true; 
                $dataMes[$diaNDiaMes]["ResumoDia"]["HasSaldo"] = false; 

                if( (($diaNDiaMes == 9)&&( ($diaSemana != 6)&&($diaSemana != 0) )) || ($diaNDiaMes == 10 && $diaSemana == 1) || ($diaNDiaMes == 11 && $diaSemana == 1) ){
                    
                    $saldoFinalDia += $competenciaAtual["Cartao"]*-1;
                    
                    $dataMes[$diaNDiaMes]["ResumoDia"]["SaldoDia"] = $competenciaAtual["Cartao"]*-1;
                    $dataMes[$diaNDiaMes]["ResumoDia"]["SaldoFinal"] = $saldoFinalDia;
                    $dataMes[$diaNDiaMes]["ResumoDia"]["HasSaldo"] = true; 
                    $totalDespesas += $competenciaAtual["Cartao"];
                        
                    $diaNDiaMesTransacoes ++;  
                    
                }
                foreach($dataDia as $dataTransacao){
                    
                    if($dataTransacao["IsContabilizado"] == 1){

                        if($diaNDiaMesTransacoes == 0){
                            $dataMes[$diaNDiaMes]["ResumoDia"]["SaldoDia"] = 0; 
                        }
                        
                        $dataMes[$diaNDiaMes]["ResumoDia"]["SaldoDia"]   += $dataTransacao["Valor"];
                        
                        $saldoFinalDia                       += $dataTransacao["Valor"];
                        $dataMes[$diaNDiaMes]["ResumoDia"]["SaldoFinal"] = $saldoFinalDia;

                        if($dataTransacao["Valor"] >= 0){
                            $totalReceita += $dataTransacao["Valor"];
                        }
                        else{
                            $totalDespesas += $dataTransacao["Valor"]*(-1);
                        }

                        $dataMes[$diaNDiaMes]["ResumoDia"]["HasSaldo"] = true; 

                    }
                    
                    $diaNDiaMesTransacoes++;
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

        // -- COMPETENCIAS --
        $lCompetencias = $this->geral_model->Listar(); 
           
		// --------------------------CONTENT----------------------------------
		$content = array( 
		"competenciaAnterior"	=> $competenciaAnterior,
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