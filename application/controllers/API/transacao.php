<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Transacao extends API_Controller{
             
        public function incluir(){	
            
            
            // /* -- DATA -- */
            // $data                       = transacao_getPosts();	
            // util_printR($data,"data POST");

            // $data["IsContabilizado"]    = true;
            // $idContaRetorno             = 1;

            // if(ValidaEntidadeTransacao($data))
            // {
            //     echo "<br>transacao Validada";
            //     $this->db->trans_begin();

            //     $idContaRetorno = $data["IdConta"];

            //     if($data["IsTransferencia"] == true){
                    
            //         echo "<br>É Transferência";

            //         contas_saldo_transferirValores($data);

            //         $origem         = $data["origem"];
            //         $destino        = $data["destino"];
            //         $idContaRetorno = $data["destino"];

            //         unset($data["origem"]);
            //         unset($data["destino"]);

            //         $data["IdCategoria"]    = 27;
            //         $data["IdSubCategoria"] = 134;
            //         $data["IdConta"]        = $destino;
            //         $data["Valor"]          = $data["Valor"];
            //         $data["IdContaOrigem"]  = $origem;

            //         if($data["IdTipoTransacao"] == 1){
            //             $data["AnoFim"] = 2050;
            //             $data["MesFim"] = 12;
            //         }

            //         $this->transacoes_model->Incluir($data);

            //     }
            //     else{
            //         if($data["Valor"] > 0){$tipo = 1;}
            //         else{$tipo = 2;}
            //         echo "<br> Tipo: ". $tipo;    

            //         echo "<br> IdTipoTransacao: ". $data["IdTipoTransacao"];    

            //         // -- TYPE 1 = Transação Recorrente -- 
            //         if($data["IdTipoTransacao"] == 1){

            //             $data["AnoFim"] = 2050;
            //             $data["MesFim"] = 12;
                        
            //             // -- BD INSERT -- 
            //             $this->transacoes_model->Incluir($data);

            //             // -- SALDO GERAL --
            //             geral_UpdateSaldo($data,$tipo);
            //             contas_saldo_UpdateSaldo($data);

            //             if(isset($data["IdCartao"]) &&  $data["IdCartao"] > 0){
            //                 echo "Cartao<br>";
            //                 // -- SALDO GERAL CARTAO
            //                 $dataCartao["IdCartao"] = $data["IdCartao"];
            //                 $dataCartao["Ano"]      = $data["Ano"];
            //                 $dataCartao["Mes"]      = $data["Mes"];

            //                 // -- SALDO GERAL CARTAO
            //                 cartao_UpdateValorMes($dataCartao,$data["Valor"]);
            //             }
            //         }

            //         // -- TYPE 2 = Transação Parcelada -- 
            //         if($data["IdTipoTransacao"] == 2){	

            //             $anoParcela = $data["Ano"];
            //             $mesParcela = $data["Mes"];

            //             $ultimoCodigoTransacao  = $this->transacoes_model->bucarUltimoCodigoTransacao();
            //             $proximo                = $ultimoCodigoTransacao["CodigoTransacao"] + 1;
            //             echo "Proximo Codigo Transacao: ".$proximo ."<br>";

            //             for($n = 1;$n <= $data["TotalParcelas"] ; $n++){

            //                 $dataParcela = $data;

            //                 $dataParcela["Ano"]		        = $anoParcela;	
            //                 $dataParcela["Mes"]		        = $mesParcela;										
            //                 $dataParcela["NumeroParcela"]   = $n;
            //                 $dataParcela["CodigoTransacao"] = $proximo;

            //                 // -- BD INSERT -- 
            //                 $this->transacoes_model->Incluir($dataParcela);

            //                 // -- SALDO GERAL --
            //                 geral_UpdateSaldo($dataParcela,$tipo);
            //                 contas_saldo_UpdateSaldo($data);

            //                 if(isset($data["IdCartao"]) &&  $data["IdCartao"] > 0){

            //                     // -- SALDO GERAL CARTAO
            //                     $dataCartao["IdCartao"] = $data["IdCartao"];
            //                     $dataCartao["Ano"]      = $anoParcela;
            //                     $dataCartao["Mes"]      = $mesParcela;

            //                     // -- SALDO GERAL CARTAO
            //                     cartao_UpdateValorMes($dataCartao,$data["Valor"]);

            //                 }

            //                 $mesParcela++;
            //                 if($mesParcela > 12){
            //                     $anoParcela++;
            //                     $mesParcela = 1;
            //                 }				
            //             }

            //         }
            //         // -- TYPE 3 = Transação Simples -- 
            //         if($data["IdTipoTransacao"] == 3){	

            //             // -- BD INSERT -- 
            //             $this->transacoes_model->Incluir($data);
                        
            //             // -- SALDO GERAL --
            //             geral_UpdateSaldo($data,$tipo);
            //             contas_saldo_UpdateSaldo($data);

            //             if(isset($data["IdCartao"]) &&  $data["IdCartao"] > 0){
            //                 echo "Cartao<br>";
            //                 // -- SALDO GERAL CARTAO
            //                 $dataCartao["IdCartao"] = $data["IdCartao"];
            //                 $dataCartao["Ano"]      = $data["Ano"];
            //                 $dataCartao["Mes"]      = $data["Mes"];

            //                 // -- SALDO GERAL CARTAO
            //                 cartao_UpdateValorMes($dataCartao,$data["Valor"]);
            //             }

            //         }

            //     }

            //     config_finalTransaction($config);
            //     $this->session->set_flashdata('msg-success',"Transação adicionada com sucesso!");
            // }

            // if($config["retorno"] == true){
            //     redirect("content/month_content/".$data["Ano"]."/".$data["Mes"]."/".$idContaRetorno);
            // }
		}
    
    }