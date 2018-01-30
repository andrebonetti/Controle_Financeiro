<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Content extends CI_Controller{
		    
       public function month_content($pAno,$pMes){
			
        // -- CONFIG    
		$config = config_base();

        // -- USUARIO --
        $usuarioLogado = valida_acessoUsuario();

		// ----- TABELAS -----
           
        // -- SALDO --
        $dataBusca["IdUsuario"]         = $usuarioLogado["Id"];    
        $dataBusca["Mes"]               = $pMes;
        $dataBusca["Ano"]               = $pAno;
        $dataBusca["isListaPorTipo"]    = true;

        $competenciaAtual   	= $this->geral_model->Buscar($dataBusca);
           
        if(empty($competenciaAtual)){
            geral_CriarCompetencia($dataBusca);
            $competenciaAtual = $this->geral_model->Buscar($dataBusca);
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
                $dataBusca["Dia"]                      = $diaN;
                $dataBusca["Mes"]                      = $pMes;
                $dataBusca["Ano"]                      = $pAno;
                $dataBusca["PreencherEntidadesFilhas"] = true;
                
                foreach(buscarTransacoesPorTipo(1,$dataBusca) as $transacao){array_push($dataMes[$transacao["DiaCalendario"]],$transacao);}
                foreach(buscarTransacoesPorTipo(2,$dataBusca) as $transacao){array_push($dataMes[$transacao["DiaCalendario"]],$transacao);}
                foreach(buscarTransacoesPorTipo(3,$dataBusca) as $transacao){array_push($dataMes[$transacao["DiaCalendario"]],$transacao);}
                
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

            $competenciaAtual = geral_verificarConsistencia($competenciaAnterior,$competenciaAtual,$totalReceita,$totalDespesas,$saldoFinalDia);

            // -- CARTAO --
            $data_cartao = array();
           
            foreach($this->cartao_model->ListarFaturaRecorrente($dataBusca) as $itemContent){
                array_push($data_cartao,$itemContent);
            }  
           
            foreach($this->cartao_model->ListarFaturaSimplesParcelada($dataBusca) as $itemContent){
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

        $dataBusca["IdCartao"] = 1;
        unset($dataBusca["Dia"]);

        $contCartao = 0;
        foreach($lCartoes as $itemCartao){

            $lCartoes[$contCartao]["lTransacao"] = array();

            if($itemCartao["Id"] == 1){

                foreach($this->cartao_model->ListarFaturaRecorrente($dataBusca) as $itemContent){
                    array_push($lCartoes[$contCartao]["lTransacao"],$itemContent);
                }  
            
                foreach($this->cartao_model->ListarFaturaSimplesParcelada($dataBusca) as $itemContent){
                    array_push($lCartoes[$contCartao]["lTransacao"],$itemContent);
                }

            }

            foreach(buscarTransacoesPorTipo(1,$dataBusca) as $transacao){array_push($lCartoes[$contCartao]["lTransacao"],$transacao);}
            foreach(buscarTransacoesPorTipo(2,$dataBusca) as $transacao){array_push($lCartoes[$contCartao]["lTransacao"],$transacao);}
            foreach(buscarTransacoesPorTipo(3,$dataBusca) as $transacao){array_push($lCartoes[$contCartao]["lTransacao"],$transacao);}

            $contCartao++;
        }

        // -- COMPETENCIAS --
        $lCompetencias = $this->geral_model->Listar(); 
           
		// --------------------------CONTENT----------------------------------
		$content = array( 
		"competenciaAnterior"	=> $competenciaAnterior,
		"competenciaAtual"		=> $competenciaAtual,       
		"usuario"		  		=> $usuarioLogado,
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
		"dataMes"      		    => $dataMes);
		
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