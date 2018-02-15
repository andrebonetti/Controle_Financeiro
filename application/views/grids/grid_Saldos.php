<div class="saldos">

    <?php foreach($lcontaUsuario["Contas_Banco"] as $keyConta => $itemConta){ ?>

        <div class="SaldoConta SaldoOrdem-<?=$itemConta["Ordem"]?>" id="saldo-conta-<?=$keyConta?>" style="<?php /*if($key != "Total"){echo "display:none;";}  */?>">

            <div class="saldo-anterior" value="<?=$itemConta["Saldo"]["SaldoAnterior"]?>">               
                Saldo Anterior: <span><?=numeroEmReais2($itemConta["Saldo"]["SaldoAnterior"])?></span> 
            </div>
            
            <div class="saldo-mes <?=sinal_valor($itemConta["Saldo"]["SaldoMes"])?>" value="<?=$itemConta["Saldo"]["SaldoMes"]?>">
                
                <span class="alteracao_manual" name="SaldoMes" title="Alterar" value="0">Saldo do Mes:</span>
                <span class="valor_span"><?=numeroEmReais2($itemConta["Saldo"]["SaldoMes"])?></span> 
           
            </div>

            <div class="saldo-futuro" value="<?=$itemConta["Saldo"]["SaldoFinal"]?>">
                
                <span class="alteracao_manual" name="SaldoFinal" title="Alterar" value="0">Saldo Futuro:</span>
                <span class="valor_span"><?=numeroEmReais2($itemConta["Saldo"]["SaldoFinal"])?></span> 
     
            </div>

        </div>

    <?php } ?>

    <div class="SaldoConta" id="saldo-conta-Geral" style="<?php /*if($key != "Total"){echo "display:none;";}  */?>">

        <div class="saldo-anterior" value="<?=$lcontaUsuario["Geral"]["Saldo"]["SaldoAnterior"]?>">               
            Saldo Anterior: <span><?=numeroEmReais2($lcontaUsuario["Geral"]["Saldo"]["SaldoAnterior"])?></span> 
        </div>
        
        <div class="saldo-mes <?=sinal_valor($lcontaUsuario["Geral"]["Saldo"]["SaldoMes"])?>" value="<?=$lcontaUsuario["Geral"]["Saldo"]["SaldoMes"]?>">
            
            <span class="alteracao_manual" name="SaldoMes" title="Alterar" value="0">Saldo do Mes:</span>
            <span class="valor_span"><?=numeroEmReais2($lcontaUsuario["Geral"]["Saldo"]["SaldoMes"])?></span> 
        
        </div>

        <div class="saldo-futuro" value="<?=$lcontaUsuario["Geral"]["Saldo"]["SaldoFinal"]?>">
            
            <span class="alteracao_manual" name="SaldoFinal" title="Alterar" value="0">Saldo Futuro:</span>
            <span class="valor_span"><?=numeroEmReais2($lcontaUsuario["Geral"]["Saldo"]["SaldoFinal"])?></span> 
    
        </div>

    </div>

</div>