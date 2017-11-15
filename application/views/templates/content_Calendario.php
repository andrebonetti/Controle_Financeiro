<div class="calendario">
            
    <div class="header-calendario">
        <h1><?=nome_mes($mes)?> - <?=$ano?></h1>
        <div class="despesas">
            <p> 
                <span class="alteracao_manual" name="despesas" title="Alterar" value="0">Despesas:</span>
                <span class="valor_span"><?=numeroEmReais2($saldo_atual["Despesas"])?></span>
            </p>

            
            <?= form_open("adm_crud/alteracao_manual",array("class"=>"alteracao_manual alteracao-despesas"))?>
            
                <input type="hidden" name="tipo_alteracao" value="Despesas">
                <input type="hidden" name="ano" value="<?=$ano?>">
                <input type="hidden" name="mes" value="<?=$mes?>">
            
                <input type="text" class="form-control input-despesas" name="valor" value="<?=numeroEmReais2($saldo_atual["Despesas"])?>">
                
                    <input type="submit" class="btn btn-success" value="Ok"> 
            
            <?= form_close()?>
            
        </div> 
        <div class="receita">
            <p> 
                <span class="alteracao_manual" name="receita" value="0">Receita:</span>
                <span class="valor_span"><?=numeroEmReais2($saldo_atual["Receita"])?></span>
            </p>
            <?= form_open("adm_crud/alteracao_manual",array("class"=>"alteracao_manual alteracao-receita"))?>
            
                <input type="hidden" name="tipo_alteracao" value="Receita">
                <input type="hidden" name="ano" value="<?=$ano?>">
                <input type="hidden" name="mes" value="<?=$mes?>">
            
                <input type="text" class="form-control input-receita" name="valor" value="<?=numeroEmReais2($saldo_atual["Receita"])?>">
                
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
        foreach($data_month as $data_day) {?>
        
            <div 
                class="dia dia-calendario dia-<?=$diaMes?>  semana-mes-<?=$numSemana?>" id="dia-<?=$diaMes?> <?php if($primeiroDiaMes == 1){echo "new-line";}?>"
                data-dia-mes="<?=$diaMes?>"
                data-dia-semana="<?=$diaSemana?>"
                data-semana="<?=$numSemana?>"
            >
            
            <table>
                
                <!-- INFO DIA -->
                <tr class="data-day" data-toggle="modal" data-target="#add-transacao" >
                    <th colspan="2" class="dia_mes"><?=$diaMes?></th>
                </tr>

                <!-- CONTEUDO DIA -->
                <?php foreach($data_day as $content){?> 
                
                    <?php $sub_categoria = valida_sub($content["DescricaoCategoria"],$content["DescricaoSubCategoria"])?>
                
                    <tr class="content-day IdSubCategoria-<?=$content["IdSubCategoria"]?>" data-toggle="modal" data-target="#edit-transacao">  
                        
                        <td class="info-content no-view"
                                data-id="<?=$content["Id"]?>"
                                data-dia="<?=$content["Dia"]?>"
                                data-valor="<?=$content["Valor"]?>"
                                data-type="<?=$content["IdTipoTransacao"]?>"
                                data-p-total-atual="<?=$content["TotalParcelas"]?>"
                                data-categoria-id="<?=$content["IdCategoria"]?>"
                                data-categoria-descricao="<?=trim($content["DescricaoCategoria"])?>"
                                data-subcategoria-id="<?=trim($content["IdSubCategoria"])?>"
                                data-subcategoria-descricao="<?=trim(valida_sub($content["DescricaoCategoria"],$content["DescricaoSubCategoria"]))?>"
                                data-descricao="<?=trim($content["Descricao"])?>"
                                >
                        </td>
                        
                        <td class="descricao">
                            <?php if($content["Descricao"] == NULL){?>
                                <?=trim($content["DescricaoSubCategoria"])?>
                            <?php } else{ ?>
                                <?=trim($content["Descricao"])?>
                                <?php if($content["IdTipoTransacao"] == "2"){?>
                                    - <?=$content["NumeroParcela"]?>/<?= $content["TotalParcelas"] ?>	
                            <?php }} ?>
                        </td>
                        
                        <td class="valor <?=sinal_valor($content["Valor"])?>"><?=numeroEmReais2($content["Valor"])?></td>
                        
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
                        <td class='valor-fatura valor' value="<?=-$saldo_atual["Cartao"]?>"><?=numeroEmReais2(-$saldo_atual["Cartao"])?></td>
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