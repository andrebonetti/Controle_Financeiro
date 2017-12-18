<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Content extends CI_Controller{
		    
       public function month_content($pAno,$pMes){
			
		$this->output->enable_profiler(TRUE);

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
                
                $Dia_transacoesSimples                  = null;
                $Dia_transacoesParceladas               = null;
                $lDia_transacoesParceladasSabado        = null;
                $lDia_transacoesParceladasDomingo       = null;
                $lDia_transacoesRecorrentes             = null;
                $lDia_transacoesRecorrentesSabado       = null;
                $lDia_transacoesRecorrentesDomingo      = null;
                
                $dataMes[$diaN]                        = array();
                $dataBusca["Dia"]                      = $diaN;
                $dataBusca["Mes"]                      = $pMes;
                $dataBusca["Ano"]                      = $pAno;
                $diaSemana                             = date("w", mktime(0,0,0,$dataBusca["Mes"],$diaN,$dataBusca["Ano"]));  
                $dataDia                               = array();
                $DiaSaldo                              = array();
                $Saldo                                 = $competenciaAnterior["SaldoFinal"];
                $dataBusca["PreencherEntidadesFilhas"] = true;
                
                foreach(buscarTransacoesPorTipo(1,$dataBusca) as $transacao){array_push($dataMes[$diaN],$transacao);}
                foreach(buscarTransacoesPorTipo(2,$dataBusca) as $transacao){array_push($dataMes[$diaN],$transacao);}
                foreach(buscarTransacoesPorTipo(3,$dataBusca) as $transacao){array_push($dataMes[$diaN],$transacao);}
                
            }
           
            /* -- CALCULO SALDO -- */
            $diaNDiaMes = 1;
            $saldoFinalDia = $competenciaAnterior["SaldoFinal"];
            $totalReceita = 0;
            $totalDespesas = 0;
            foreach($dataMes as $dataDia){
                
                $diaSemana         = date("w", mktime(0,0,0,$pMes,$diaNDiaMes,$pAno)); 
                $diaNDiaMesTransacoes = 0;

                if( (($diaNDiaMes == 9)&&( ($diaSemana != 6)&&($diaSemana != 0) )) || ($diaNDiaMes == 10 && $diaSemana == 1) || ($diaNDiaMes == 11 && $diaSemana == 1) ){
                    
                    $saldoFinalDia += $competenciaAtual["Cartao"]*-1;
                    
                    $DiaSaldo[$diaNDiaMes]["SaldoDia"] = $competenciaAtual["Cartao"]*-1;
                    $DiaSaldo[$diaNDiaMes]["SaldoFinal"] = $saldoFinalDia;
                    $totalDespesas += $competenciaAtual["Cartao"];
                        
                    $diaNDiaMesTransacoes ++;  
                    
                }
                foreach($dataDia as $dataTransacao){
                    
                    if($diaNDiaMesTransacoes == 0){
                        $DiaSaldo[$diaNDiaMes]["SaldoDia"] = 0; 
                    }
                    
                    $DiaSaldo[$diaNDiaMes]["SaldoDia"]   += $dataTransacao["Valor"];
                    
                    $saldoFinalDia                       += $dataTransacao["Valor"];
                    $DiaSaldo[$diaNDiaMes]["SaldoFinal"] = $saldoFinalDia;

                    if($dataTransacao["Valor"] >= 0){
                        $totalReceita += $dataTransacao["Valor"];
                    }
                    else{
                        $totalDespesas += $dataTransacao["Valor"]*(-1);
                    }
                    
                    $diaNDiaMesTransacoes++;
                }
                
                $diaNDiaMes++;
            }

            $competenciaAtual = geral_verificarConsistencia($competenciaAnterior,$competenciaAtual,$totalReceita,$totalDespesas,$saldoFinalDia);

            // -- CARTAO --
            $data_cartao = array();
           
            $cartao_Recorrente       = $this->cartao_model->ListarFaturaRecorrente($dataBusca);
            foreach($cartao_Recorrente as $itemContent){
                array_push($data_cartao,$itemContent);
            }  
           
            $cartao_SimplesParcelado = $this->cartao_model->ListarFaturaSimplesParcelada($dataBusca);
            foreach($cartao_SimplesParcelado as $itemContent){
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
           
		// --------------------------CONTENT----------------------------------
		$content = array( 
		"competenciaAnterior"	=> $competenciaAnterior,
		"competenciaAtual"		=> $competenciaAtual,       
		"usuario"		  		=> $usuarioLogado,
		"ano"			  		=> $pAno,
		"mes"			  		=> $pMes,
        "hoje"                  => $DiaAtual,
		"categorias"	  		=> $lCategorias,
		"all_sub_categorias"  	=> $lSubCategoriasTotal,
        "fatura_cartao"   		=> $data_cartao,
        "sub_categorias"  		=> $lCategoriasFinal,      
        "primeiroDiaMes"       	=> $primeiroDiaMes, 
        "last_day"       		=> $diaN - 1, 
		"dataMes"      		    => $dataMes,
        "DiaSaldo"      		=> $DiaSaldo);
		
		// -- VIEW --
        $this->load->template("content.php",$content);
           
	   }

	   public function geral(){

           $this->output->enable_profiler(TRUE);
	   		
            $data["OrderBy"] = "Ano,Mes";
            $lGeral = $this->geral_model->Listar($data);

			$content = array( 
				"lGeral"		=> $lGeral,
			);
		
			//VIEW
            $this->load->template("geral.php",$content);
            	
		
	   }	
    }