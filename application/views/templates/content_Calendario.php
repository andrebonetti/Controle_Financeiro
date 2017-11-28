<div class="calendario">
            
    <div class="header-calendario">
        <h1><?=nome_mes($mes)?> - <?=$ano?></h1>
        <div class="despesas">
            <p> 
                <span class="alteracao_manual" name="despesas" title="Alterar" value="0">Despesas:</span>
                <span class="valor_span"><?=numeroEmReais2($competenciaAtual["Despesas"])?></span>
            </p>

            
            <?= form_open("adm_crud/alteracao_manual",array("class"=>"alteracao_manual alteracao-despesas"))?>
            
                <input type="hidden" name="tipo_alteracao" value="Despesas">
                <input type="hidden" name="ano" value="<?=$ano?>">
                <input type="hidden" name="mes" value="<?=$mes?>">
            
                <input type="text" class="form-control input-despesas" name="valor" value="<?=numeroEmReais2($competenciaAtual["Despesas"])?>">
                
                    <input type="submit" class="btn btn-success" value="Ok"> 
            
            <?= form_close()?>
            
        </div> 
        <div class="receita">
            <p> 
                <span class="alteracao_manual" name="receita" value="0">Receita:</span>
                <span class="valor_span"><?=numeroEmReais2($competenciaAtual["Receita"])?></span>
            </p>
            <?= form_open("adm_crud/alteracao_manual",array("class"=>"alteracao_manual alteracao-receita"))?>
            
                <input type="hidden" name="tipo_alteracao" value="Receita">
                <input type="hidden" name="ano" value="<?=$ano?>">
                <input type="hidden" name="mes" value="<?=$mes?>">
            
                <input type="text" class="form-control input-receita" name="valor" value="<?=numeroEmReais2($competenciaAtual["Receita"])?>">
                
                    <input type="submit" class="btn btn-success" value="Ok"> 
            
            <?= form_close()?>
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
        foreach($dataMes as $dataDia) {?>

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
                <?php foreach($dataDia as $dataTransacao){
                
                    $sub_categoria = valida_sub($dataTransacao["DescricaoCategoria"],$dataTransacao["DescricaoSubCategoria"])
                
                ?> 
                
                    <tr class="content-day IdSubCategoria-<?=$dataTransacao["IdSubCategoria"]?>" data-toggle="modal" data-target="#edit-transacao">  
                        
                        <td class="info-content no-view"
                                data-id="<?=$dataTransacao["Id"]?>"
                                data-dia="<?=$dataTransacao["Dia"]?>"
                                data-valor="<?=$dataTransacao["Valor"]?>"
                                data-type="<?=$dataTransacao["IdTipoTransacao"]?>"
                                data-p-total-atual="<?=$dataTransacao["TotalParcelas"]?>"
                                data-categoria-id="<?=$dataTransacao["IdCategoria"]?>"
                                data-categoria-descricao="<?=trim($dataTransacao["DescricaoCategoria"])?>"
                                data-subcategoria-id="<?=trim($dataTransacao["IdSubCategoria"])?>"
                                data-subcategoria-descricao="<?=trim(valida_sub($dataTransacao["DescricaoCategoria"],$dataTransacao["DescricaoSubCategoria"]))?>"
                                data-descricao="<?=trim($dataTransacao["Descricao"])?>"
                                >
                        </td>
                        
                        <td class="descricao" data-toggle="tooltip" data-placement="top" title="<?=trim($dataTransacao["DescricaoCategoria"])." - ".trim(valida_sub($dataTransacao["DescricaoCategoria"],$dataTransacao["DescricaoSubCategoria"]))?>">

                            <?php if($dataTransacao["Descricao"] == NULL){?>
                                <?=trim($dataTransacao["DescricaoSubCategoria"])?>
                            <?php } else{ ?>
                                <?=trim($dataTransacao["Descricao"])?>
                                <?php if($dataTransacao["IdTipoTransacao"] == "2"){?>
                                    - <?=$dataTransacao["NumeroParcela"]?>/<?= $dataTransacao["TotalParcelas"] ?>	
                            <?php }} ?>

                        </td>
                        
                        <td class="valor <?=sinal_valor($dataTransacao["Valor"])?>"><?=numeroEmReais2($dataTransacao["Valor"])?></td>
                        
                    </tr>
                
                <?php } ?>
                
                <!-- RESUMO DIA -->
                
                <?php if(isset($DiaSaldo[$diaMes]["SaldoFinal"])){ ?>
                
                    <tr class="data-total_saldoDia">
                        <td class="titulo">Saldo Dia</td>
                        <td class="valorSaldo"><?=numeroEmReais2($DiaSaldo[$diaMes]["SaldoDia"])?></td>
                    </tr>
                        <tr class="data-total_saldoFinal">
                        <td class="titulo">Saldo Final</td>
                        <td class="valorSaldo"><?=numeroEmReais2($DiaSaldo[$diaMes]["SaldoFinal"])?></td>
                    </tr>

                <?php } ?>
                
                <?php if( (($diaMes == 9)&&($primeiroDiaMes < 6)) || ($diaMes == 10 && $primeiroDiaMes == 1) || ($diaMes == 11 && $primeiroDiaMes == 1) ) {?>
                    <tr class="cartao">
                        <td class='cartao'>Cartão</td>
                        <td class='valor-fatura valor' value="<?=-$competenciaAtual["Cartao"]?>"><?=numeroEmReais2(-$competenciaAtual["Cartao"])?></td>
                    </tr>
                <?php } ?>
                
            </table>
            
            <div class="valor-total"></div>

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