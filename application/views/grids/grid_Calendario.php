<div class="calendario">
            
    <div class="header-calendario">
        <h1><?=nome_mes($mes)?> - <?=$ano?></h1>
        <div class="despesas">
            <p> 
                <span class="alteracao_manual" name="despesas" title="Alterar" value="0">Despesas:</span>
                <span class="valor_span"><?=numeroEmReais2($competenciaAtual["Despesas"])?></span>
            </p>
        </div> 
        <div class="receita">
            <p> 
                <span class="alteracao_manual" name="receita" value="0">Receita:</span>
                <span class="valor_span"><?=numeroEmReais2($competenciaAtual["Receita"])?></span>
            </p>
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
         $diaMes = 1;

        /*-- INSERE DIAS MES ANTERIOR --*/
        for($pre=1;$pre <= $primeiroDiaMes;$pre++){ 

            echo "<div class='dia pre-nulo semana-mes-1'></div>";
            $diaSemana++; 

        } 

        /*-- FOREACH DIAS MES --*/
        foreach($dataMes as $keyDay => $dataDia) {?>

            <?php 
                $classeDia = "";
                if($diaSemana == 6 || $diaSemana ==7){
                    $classeDia = "final-de-semana";
                }
            ?>
        
            <div 
                class="dia dia-calendario dia-<?=$diaMes?> semana-mes-<?=$numSemana?> <?=$classeDia?>" id="dia-<?=$diaMes?>"
                data-dia-mes="<?=$diaMes?>"
                data-dia-semana="<?=$diaSemana?>"
                data-semana="<?=$numSemana?>"
            >
            
            <table>
                
                <!-- INFO DIA -->
                <tr class="data-day" data-toggle="modal" data-target="#add-transacao">
                    <th colspan="2" class="dia_mes"><?=$diaMes?></th>
                </tr>

                <!-- CONTEUDO DIA -->
                <?php foreach($dataDia["lTransacoes"] as $KeyTransacao =>  $itemTransacao){?> 

                    <tr class="content-day IdSubCategoria-<?=$itemTransacao["IdSubCategoria"]?> IsContabilizado-<?=$itemTransacao["IsContabilizado"]?>" data-toggle="modal" data-target="#edit-transacao">  
                        
                        <td class="info-content no-view "
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
                                data-idconta="<?=$itemTransacao["IdConta"]?>"
                                >
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

                <?php if( (($diaMes == 9)&&($primeiroDiaMes < 6)) || ($diaMes == 10 && $primeiroDiaMes == 1) || ($diaMes == 11 && $primeiroDiaMes == 1) ) {?>
                    <tr class="cartao" data-toggle="modal" data-target="#cartao_de_credito">
                        <td class='cartao'>Cartão</td>
                        <td class='valor-fatura valor' value="<?=-$competenciaAtual["Cartao"]?>"><?=numeroEmReais2(-$competenciaAtual["Cartao"])?></td>
                    </tr>
                <?php } ?>

            </table>  

            <table class="ResumoDia">

                <!-- RESUMO DIA -->
                <?php if(isset($dataDia["ResumoDia"]["Contas_Banco"])){ foreach($dataDia["ResumoDia"]["Contas_Banco"] as $KeyResumo =>  $itemResumo){?> 

                    <tr class="saldoDia_conta saldoDia_conta-<?=$KeyResumo?>">
                        <td class="conta"><?=$KeyResumo?></td>
                        <td class="titulo">Saldo Dia</td>
                        <td class="valorSaldo"><?=numeroEmReais2($itemResumo["SaldoDia"])?></td>
                    </tr>
                    <tr class="saldoFinal_conta saldoFinal_conta-<?=$KeyResumo?>">
                        <td class="conta"><?=$KeyResumo?></td>
                        <td class="titulo">Saldo Final</td>
                        <td class="valorSaldo"><?=numeroEmReais2($itemResumo["SaldoFinal"])?></td>
                    </tr>

                <?php }} ?>

                <tr class="saldoDia_geral">
                    <td class="conta">Total</td>
                    <td class="titulo">Saldo Dia</td>
                    <td class="valorSaldo"><?=numeroEmReais2($dataDia["ResumoDia"]["Geral"]["SaldoDia"])?></td>
                </tr>
                <tr class="saldoFinal_geral">
                    <td class="conta">Total</td>
                    <td class="titulo">Saldo Final</td>
                    <td class="valorSaldo"><?=numeroEmReais2($dataDia["ResumoDia"]["Geral"]["SaldoFinal"])?></td>
                </tr>
                
            </table>

        </div> 
        
    <?php
        $diaMes++;  
        $diaSemana ++;
        if($diaSemana > 7){
            $diaSemana = 1;
            $numSemana ++;
        }
    } ?>
    
</div>       