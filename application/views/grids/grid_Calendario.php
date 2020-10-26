<div class="calendario">
            
    <div class="header-calendario">

        <h1><?=nome_mes($dataAtual["Mes"])?> - <?=$dataAtual["Ano"]?></h1>

    <?php 

        foreach ($lcontaUsuario["Contas_Banco"] as $keyContaUsuario => $valueContaUsuario) {?>

            <div class="info-movimentacao no-view movimentaca-<?=$keyContaUsuario?>">

                <div class="movimentacao">
                    <p>
                        <b>Movimentação</b><br>
                    </p>
                    <p>
                        <?=numeroEmReais2($valueContaUsuario["SaldoTela"]["Receita"] + ($valueContaUsuario["SaldoTela"]["Despesas"]))?>
                    </p>
                </div> 
                
                <div class="despesas">
                    <p>
                        <b>Despesas</b><br>
                    </p>
                    <p>
                        <?=numeroEmReais2($valueContaUsuario["SaldoTela"]["Despesas"])?>
                    </p>
                </div> 
                <div class="receita">
                    <p>
                        <b>Receita</b><br>
                    </p>
                    <p>
                        <?=numeroEmReais2($valueContaUsuario["SaldoTela"]["Receita"])?>
                    </p>
                </div> 


            </div>
            
    <?php } ?>

        <div class="info-movimentacao no-view movimentaca-Geral">
                    
            <div class="movimentacao">
                <p>
                    <b>Movimentação</b><br>
                </p>
                <p>
                    <?=numeroEmReais2($lcontaUsuario["Geral"]["Receita"] + ($lcontaUsuario["Geral"]["Despesas"]))?>
                </p>
            </div> 
            <div class="despesas">
                <p>
                    <b>Despesas</b><br>
                </p>
                <p>
                    <?=numeroEmReais2($lcontaUsuario["Geral"]["Despesas"])?>
                </p>
            </div> 
            <div class="receita">
                <p>
                    <b>Receita</b><br>
                </p>
                <p>
                    <?=numeroEmReais2($lcontaUsuario["Geral"]["Receita"])?>
                </p>
            </div> 


        </div>
    
    </div>
    
    
    <!-- SEMANAS -->
    <table class="semanas">           
        <tr>
            <th>Segunda-Feira</th>  
            <th>Terça-Feira</th> 
            <th>Quarta-Feira</th> 
            <th>Quinta-Feira</th> 
            <th>Sexta-Feira</th> 
            <th>Sabado</th>
            <th>Domingo</th>
        </tr>    
    </table>
            
    <!-- DIAS -->
    <div class="dias">
        
    <?php 

        // Inicial
         $numSemana = 1; 
         $diaSemana = 1;

        /*-- INSERE DIAS MES ANTERIOR --*/
        for($pre=1;$pre <= $dataAtual["PrimeiroDiaMes"];$pre++){ 

            echo "<div class='dia pre-nulo semana-mes-1'></div>";
            $diaSemana++; 

        } 

        /*-- FOREACH DIAS MES --*/
        foreach($dataMes as $keyDay => $dataDia) {?>

            <?php 
                $classeDia = "";
                if($diaSemana == 6 || $diaSemana ==7){
                    $classeDia = $classeDia." final-de-semana";
                }

                if($keyDay == $dataAtual["Dia"]){
                    $classeDia = $classeDia." diaAtual";
                }
            ?>
        
            <div 
                class="dia dia-calendario dia-<?=$keyDay?> semana-mes-<?=$numSemana?> <?=$classeDia?>" id="dia-<?=$keyDay?>"
                data-dia-mes="<?=$keyDay?>"
                data-dia-semana="<?=$diaSemana?>"
                data-semana="<?=$numSemana?>"
            >
            
            <table class="transacoes">
                
                <!-- INFO DIA -->
                <tr class="data-day" data-toggle="modal" data-target="#add-transacao">
                    <th colspan="2" class="dia_mes"><?=$keyDay?></th>
                </tr>

                <!-- CONTEUDO DIA -->
                <?php foreach($dataDia["lTransacoes"] as $KeyTransacao =>  $itemTransacao){?> 

                    <tr class="content-day  
                    IdSubCategoria-<?=$itemTransacao["IdSubCategoria"]?> 
                    IsContabilizado-<?=$itemTransacao["IsContabilizado"]?> 
                    IsTransferencia-<?=$itemTransacao["IsTransferencia"]?>
                    transacao_idconta-<?=$itemTransacao["IdConta"]?> transacao_idconta-<?=$itemTransacao["IdContaOrigem"]?>
                    transacao_conta-<?=$lcontaUsuario["Contas_Banco"][$itemTransacao["IdConta"]]["Ordem"]?>" 
                    data-toggle="modal" data-target="#edit-transacao">  
                        
                        <td class="info-content css_transacao" style="<?=$lcontaUsuario["Contas_Banco"][$itemTransacao["IdConta"]]["CSS"]?>"
                                data-id="<?=$itemTransacao["Id"]?>"
                                data-dia="<?=$itemTransacao["Dia"]?>"
                                data-valor="<?=$itemTransacao["Valor"]?>"
                                data-type="<?=$itemTransacao["IdTipoTransacao"]?>"
                                data-p-atual="<?=$itemTransacao["NumeroParcela"]?>"
                                data-p-total-atual="<?=$itemTransacao["TotalParcelas"]?>"
                                data-categoria-id="<?=$itemTransacao["IdCategoria"]?>"
                                data-categoria-descricao="<?=trim($itemTransacao["DescricaoCategoria"])?>"
                                data-subcategoria-id="<?=trim($itemTransacao["IdSubCategoria"])?>"
                                data-subcategoria-descricao="<?=trim(valida_sub($itemTransacao["DescricaoCategoria"],$itemTransacao["DescricaoSubCategoria"]))?>"
                                data-descricao="<?=trim($itemTransacao["Descricao"])?>"
                                data-codigo-transacao="<?=trim($itemTransacao["CodigoTransacao"])?>"
                                data-iscontabilizado="<?=$itemTransacao["IsContabilizado"]?>"
                                data-istransferencia="<?=$itemTransacao["IsTransferencia"]?>"
                                data-contaorigem="<?=$itemTransacao["IdContaOrigem"]?>"
                                data-idconta="<?=$itemTransacao["IdConta"]?>">        
                        </td>

                        <td class="descricao" data-toggle="tooltip" data-placement="top" title="<?=trim($itemTransacao["DescricaoCategoria"])." - ".trim(valida_sub($itemTransacao["DescricaoCategoria"],$itemTransacao["DescricaoSubCategoria"]))?>">

                            <?php if($itemTransacao["Descricao"] == NULL){?>
                                <?=trim($itemTransacao["DescricaoSubCategoria"])?>
                            <?php } else{ ?>
                                <?=trim($itemTransacao["Descricao"])?>
                                <?php if($itemTransacao["IdTipoTransacao"] == "2"){?>
                                    - <?=$itemTransacao["NumeroParcela"]?>/<?= $itemTransacao["TotalParcelas"] ?>	
                            <?php }} ?>

                        </td>
                        
                        <td class="valor <?=sinal_valor($itemTransacao["Valor"])?>"><?=numeroEmReais2($itemTransacao["Valor"])?></td>
                        
                    </tr>
                
                <?php } ?>

                <?php foreach($dataDia["lFaturas"] as $KeyTransacao =>  $itemTransacao){?> 

                    <tr class="
                    content-day 
                    transacao_conta-<?=$itemTransacao["IdConta"]?>
                    transacao_idconta-<?=$itemTransacao["IdConta"]?>" 
                    data-toggle="modal" data-target="#cartao_de_credito">
                    
                        <td class="info-content css_cartao" style="<?=$lcontaUsuario["Contas_Banco"][$itemTransacao["IdConta"]]["CSS"]?>"></td>

                        <td class="descricao" data-toggle="tooltip" data-placement="top">
                            <?=trim($itemTransacao["Descricao"])?>
                        </td>

                        <td class="valor <?=sinal_valor($itemTransacao["Valor"])?>"><?=numeroEmReais2($itemTransacao["Valor"])?></td>

                    </tr>

                <?php } ?>

            </table>  

            <table class="ResumoDia">

                <!-- RESUMO DIA -->
                <?php if( ( (count($dataDia["lTransacoes"]) > 0) || (count($dataDia["lFaturas"]) > 0) )&& (isset($dataDia["ResumoDia"]["Contas_Banco"])) ){ foreach($dataDia["ResumoDia"]["Contas_Banco"] as $KeyResumo =>  $itemResumo){?> 

                    <tr class="saldoDia_conta saldoDia_conta-<?=$KeyResumo?> $itemResumo["SaldoDia"]">
                        <td class="css_resumo" style="<?=$itemResumo["CSS"]?>"></td>
                        <td class="titulo">Saldo Dia</td>
                        <td class="valorSaldo"><?=numeroEmReais2($itemResumo["SaldoDia"])?></td>
                    </tr>
                    <tr class="saldoFinal_conta saldoFinal_conta-<?=$KeyResumo?>">
                        <td class="css_resumo" style="<?=$itemResumo["CSS"]?>"></td>
                        <td class="titulo">Saldo Final</td>
                        <td class="valorSaldo"><?=numeroEmReais2($itemResumo["SaldoFinal"])?></td>
                    </tr>

                <?php }} ?>

                <tr class="saldoDia_geral">
                    <td class="css_resumoGeral"></td>
                    <td class="titulo">Saldo Dia</td>
                    <td class="valorSaldo"><?=numeroEmReais2($dataDia["ResumoDia"]["Geral"]["SaldoDia"])?></td>
                </tr>
                <tr class="saldoFinal_geral">
                    <td class="css_resumoGeral"></td>
                    <td class="titulo">Saldo Final</td>
                    <td class="valorSaldo"><?=numeroEmReais2($dataDia["ResumoDia"]["Geral"]["SaldoFinal"])?></td>
                </tr>
                
            </table>

        </div> 
        
    <?php
 
        $diaSemana ++;
        if($diaSemana > 7){
            $diaSemana = 1;
            $numSemana ++;
        }
    } ?>
    
</div>       