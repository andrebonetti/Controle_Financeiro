<!-- MODAL - ADICIONAR TRANSAÇÃO -->
<div class="modal fade modal-adiciona" id="add-transacao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 999999;">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Adicionar Transação</h4>
        </div>
        <div class="modal-body">

            <?= form_open("adm_crud/transacao_insert")?>

                <input type="hidden" name="usuario" value="<?=$usuario['Id']?>">
                <input type="hidden" name="ano" value="<?=$dataAtual["Ano"]?>">
                <input type="hidden" name="mes" value="<?=$dataAtual["Mes"]?>">
                <input type="hidden" class="idCartao" name="idCartao" value="">

                <div class="new-line">

                    <!-- DIA -->
                    <div class="dia">
                        <label>Dia</label>
                        <input type="number" name="dia" class="dia-add form-control" placeholder="Dia">
                        <span class="mes">/ <?=$dataAtual["Mes"]?></span>
                        <span class="ano">/ <?=$dataAtual["Ano"]?></span>
                    </div>  

                    <!-- DATA COMPRA -->
                    <div class="dataCompra">
                        <label>Data Compra</label>
                        <input type="text" name="dataCompra" class="dataCompra-add form-control" placeholder="DD/MM/AAAA">
                    </div>      

                    <!-- CATEGORIA -->
                    <div class="categoria">
                        <label>Categoria</label>
                        <select name="categoria" class="categoria_modal form-control">       
                            <option value="0">Escolha a Categoria</option>
                            <?php foreach($categorias as $categoria){?>
                                <option value="<?=$categoria["IdCategoria"]?>"><?=$categoria["DescricaoCategoria"]?></option>
                            <?php } ?>
                            <option value="transferencia_conta">Transferência entre Contas</option>
                            <option value="nova-categoria">Adicionar Categoria</option>
                        </select>
                    </div>    

                    <!-- SUB_CATEGORIA -->
                    <div class="sub-categoria">
                        <label>Sub-Categoria</label>
                        <select name="sub_categoria" class="subcategoria_modal form-control">
                            <option value="0">Escolha a Sub Categoria</option>
                            <?php foreach($all_sub_categorias as $sub_categoria){?>
                                <option value="<?=$sub_categoria["IdSubCategoria"]?>" class="option_SubCategoria" name="<?=$sub_categoria["IdCategoria"]?>"><?=$sub_categoria["DescricaoSubCategoria"]?></option>
                            <?php } ?>
                            <option value="nova-sub_categoria">Adicionar Sub_categoria</option>
                        </select>
                    </div>  

                </div> 

                <!-- TRANSFERENCIA CONTAS -->
                <div class="transferencia-contas new-line">

                    <div class="transf-origem">
                        <label>Conta Origem</label>
                        <select class="conta-origem form-control" name="origem">
                            <option value="">Escolha a Conta</option>
                            <?php foreach($lcontaUsuario["Contas_Banco"] as $key => $itemConta){ ?>
                                <option value="<?=$key?>"><?=$itemConta["Descricao"]?></option>
                            <?php }  ?>
                        </select>
                    </div>

                    <div class="transf-destino">
                        <label>Conta Origem</label>
                        <select class="conta-destino form-control" name="destino" disabled="disabled">
                            <option value="">Escolha a Conta</option>
                            <?php foreach($lcontaUsuario["Contas_Banco"] as $key => $itemConta){ ?>
                                <option value="<?=$key?>"><?=$itemConta["Descricao"]?></option>
                            <?php }  ?>
                        </select>
                    </div> 

                </div>

                <!-- ADD CATEGORIA -->
                <div class="adiciona-categoria new-line">
                    <label>Adiconar Categoria</label>
                    <input type="text" name="adiciona-categoria" class="input_adiciona-categoria form-control" Placeholder="Nome da Nova Categoria:">
                    <input type="text" name="adiciona-subcategoria" class="input_adiciona-subcategoria form-control" Placeholder="Nome da Nova Sub Categoria:">
                    <span class="cancelar_nova_categoria btn btn-danger">Cancelar</span>
                </div>
                
                <!-- ADD SUB_CATEGORIA -->
                <div class="adiciona-sub_categoria new-line">
                    <label>Adiconar Sub Categoria</label>
                    <input type="text" name="adiciona-sub_categoria" class="form-control input_adiciona-subcategoria">
                    <span class="cancelar_nova_subcategoria btn btn-danger">Cancelar</span>
                </div>    

                <div class="new-line">
                
                    <!-- DESCRIÇÃO -->
                    <div class="descricao">
                        <label>Descrição</label>
                        <input type="text"   name="descricao" class="form-control">
                    </div>    
                
                    <!-- VALOR -->
                    <div class="valor"> 
                        <label>Valor</label>
                        <input type="text" name="valor" class="form-control" placeholder="Valor">
                    </div>
                
                    <!-- PARCELAS -->
                    <div class="transacao-parcelada">
                        <label>Numero de Parcelas</label>
                        <input type="number" class="p-total form-control" name="totalParcelas" value="0" placeholder="Número de Parcelas"/>
                    </div>

                </div>

                <div class="new-line">

                    <!-- CONTA -->
                    <div class="conta">
                        <label>Conta</label>
                        <select class="conta-origem form-control" name="conta">
                             <?php foreach($lcontaUsuario["Contas_Banco"] as $key => $itemConta){ ?>
                                <option value="<?=$key?>"><?=$itemConta["Descricao"]?></option>
                            <?php }  ?>
                        </select>
                    </div>
                
                    <!-- TRANSAÇÃO RECORRENTE -->
                    <div class="transacao-recorrente">
                        <label>Transação Recorrente ?</label>
                        <input type="radio" name="isRecorrente" class="RecorrenteSim" value="1"><span class="RecorrenteSim">Sim</span>
                        <input type="radio" name="isRecorrente" class="RecorrenteNao" value="0" checked><span class="RecorrenteNao">Não</span>
                    </div>

                </div>

                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <input type="submit" class="btn btn-primary" value="Adicionar"> 
            
            <?= form_close()?>
        </div>
    </div>
    </div>
</div>

<!-- MODAL - EDITAR TRANSAÇÃO  -->
<div class="modal fade modal-edita" id="edit-transacao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 999999;">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Editar Transação : <span class="tipo-transacao"></span></h4>
        </div>
        <div class="modal-body">
            <?= form_open("adm_crud/transacao_update",array("class"=>"link-update"))?>

                <input type="hidden" name="usuario" value="<?=$usuario['Id']?>">
                <input type="hidden" class="id-edit" name="id" value="">
                <input type="hidden" class="codigo-transacao" name="codigoTransacao" value="">
                <input type="hidden" class="p-atual" name="numero-parcela" value="">
                <input type="hidden" class="p-total" name="parcela-total" value="">
                <input type="hidden" class="id-tipo-transacao" name="id-tipo-transacao" value="">
                <input type="hidden" class="idCartao" name="idCartao" value="">
                <input type="hidden" class="isTransferencia" name="is-transferencia" value="">

                <!-- DIA -->
                <div class="dia dia-edit">
                    <label>Dia</label>
                    <input type="number" name="dia" class="dia-edit form-control" placeholder="Dia">/
                </div>    
                <div class="mes">
                    <label>Mês</label>
                    <input type="number" class="mes-edit form-control" name="mes" value="<?=$dataAtual["Mes"]?>">/
                </div>    
                <div class="ano">
                    <label>Ano</label>
                    <input type="number" class="ano-edit form-control" name="ano" value="<?=$dataAtual["Ano"]?>">
                </div>   

                <!-- DATA COMPRA -->
                <div class="dataCompra">
                    <label>Data Compra</label>
                    <input type="text" name="dataCompra" class="data-compra form-control" placeholder="DD/MM/AAAA">
                </div>       
            
                <!-- CATEGORIA -->
                <div class="categoria">
                    <label>Categoria</label>
                    <select name="categoria" class="categoria-atual form-control">       
                        <option>Escolha a Categoria</option>
                        <?php foreach($categorias as $categoria){?>
                            <option value="<?=$categoria["IdCategoria"]?>"><?=$categoria["DescricaoCategoria"]?></option>
                        <?php } ?>
                        <option value="nova-categoria">Adicionar Categoria</option>
                    </select>
                </div>    

                <!-- SUB_CATEGORIA -->
                <div class="sub-categoria">
                    <label>Sub-Categoria</label>
                    <select name="sub_categoria" class="sub_categoria-atual form-control">
                        <option>Escolha a Sub Categoria</option>
                        <?php foreach($all_sub_categorias as $sub_categoria){?>
                            <option value="<?=$sub_categoria["IdSubCategoria"]?>" class="option_SubCategoria" name="<?=$sub_categoria["IdCategoria"]?>"><?=$sub_categoria["DescricaoSubCategoria"]?></option>
                        <?php } ?>
                        <option value="nova-sub_categoria">Adicionar Sub_categoria</option>
                    </select>
                </div>    

                <!-- ADD CATEGORIA -->
                <div class="adiciona-categoria">
                    <label>Adiconar Categoria</label>
                    <input type="text" name="adiciona-categoria" class="form-control" Placeholder="Nome da Nova Categoria:">
                </div>
                
                <!-- ADD SUB_CATEGORIA -->
                <div class="adiciona-sub_categoria">
                    <label>Adiconar Sub Categoria</label>
                    <select name="categoria-sub" class="categoria-sub form-control">
                        <option>Categoria Relacionada</option>
                        <?php foreach($categorias as $categoria){?>
                            <option value="<?=$categoria["id_categoria"]?>"><?=$categoria["nome_categoria"]?></option>
                        <?php } ?>
                        <option id="adiciona-categoria">Adicionar Categoria</option>
                    </select>
                    <input type="text" name="adiciona-sub_categoria" class="form-control">
                </div>    

                <!-- TRANSFERENCIA CONTAS -->
                <div class="transferencia-contas new-line">

                    <div class="transf-origem">
                        <label>Conta Origem</label>
                        <select class="conta-origem form-control" name="origem">
                            <option value="">Escolha a Conta</option>
                            <?php foreach($lcontaUsuario["Contas_Banco"] as $key => $itemConta){ ?>
                                <option value="<?=$key?>"><?=$itemConta["Descricao"]?></option>
                            <?php }  ?>
                        </select>
                    </div>

                    <div class="transf-destino">
                        <label>Conta Origem</label>
                        <select class="conta-destino form-control" name="destino" disabled="disabled">
                            <option value="">Escolha a Conta</option>
                            <?php foreach($lcontaUsuario["Contas_Banco"] as $key => $itemConta){ ?>
                                <option value="<?=$key?>"><?=$itemConta["Descricao"]?></option>
                            <?php }  ?>
                        </select>
                    </div> 

                </div>
                
                <!-- DESCRIÇÃO -->
                <div class="descricao">
                    <label>Descrição</label>
                    <input type="text"   name="descricao" class="descricao-atual form-control">
                </div>    
            
                <!-- VALOR -->
                <div class="valor"> 
                    <label>Valor</label>
                    <input type="text" name="valor" class="valor-atual form-control" placeholder="Valor">
                </div>
            
                <!-- PARCELAS -->
                <div class="transacao-parcelada">
                    <label>Parcela / Total Parcelas</label>
                    <input type="number" class="p-total-atual form-control" name="total" value="0" placeholder="Número de Parcelas"/>
                </div>

                <!-- CONTA -->
                <div class="conta">
                    <label>Conta</label>
                    <select class="conta-origem form-control" name="conta">
                            <?php foreach($lcontaUsuario["Contas_Banco"] as $key => $itemConta){ ?>
                            <option value="<?=$key?>"><?=$itemConta["Descricao"]?></option>
                        <?php }  ?>
                    </select>
                </div>

                <div class="campoCheckboxModalTransacao opcoes-transacao-isContabilizado">
                    <p class="alert"><input type="checkbox" class="chkCheckboxModalTransacao isContabilizado" name="isContabilizado"/> Contabilizar no Saldo <p>
                </div>

                <div class="campoCheckboxModalTransacao opcoes-transacao-repeticao no-view">
                    <p class="alert alert-warning"><input type="checkbox" class="chkCheckboxModalTransacao EspelharProximasFaturas" name="espelhar-proximas"/> Espelhar Próximas Transações Parceladas<p>
                </div>

                <div class="buttons">
                    <button class="btn btn-danger link-delete">Excluir</button>
                    <input type="submit" class="btn btn-atualizar btn-primary" value="Atualizar"> 
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>

            <?= form_close()?>
        </div>
    </div>
    </div>
</div>

<!-- CARTAO -->
<div class="modal fade modal-cartao" id="cartao_de_credito" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Fatura Cartão</h4>
            </div>
            <div class="modal-body">

                <!-- CATEGORIAS -->
                <div class="cartoes">

                    <?php foreach($lCartoes as $itemCartao){?>

                        <div class="box">

                            <div class="fatura-cartao">
                            
                                <div class="categoria-content">

                                    <h2><?=$itemCartao["Descricao"]?> ...<?=substr($itemCartao["Numero"],(strlen($itemCartao["Numero"])-4),strlen($itemCartao["Numero"]))?></h2>

                                    <button class="btn btn-primary insert-cartao" data-id="<?=$itemCartao["Id"]?>" data-toggle="modal" data-target="#add-transacao">Adicionar Cartão</button>

                                    <table>

                                        <tr>
                                            <th>Data da Compra</th>
                                            <th>Descrição</th>
                                            <th>Valor</th>
                                        </tr>

                                        <?php foreach($itemCartao["lTransacao"] as $content){?>

                                            <tr class="content-day transacao_idconta-1 IdSubCategoria-<?=$content["IdSubCategoria"]?>" data-toggle="modal" data-target="#edit-transacao" data-idcartao="<?=$itemCartao["Id"]?>"> 

                                            <td class="info-content no-view"
                                                    data-id="<?=$content["Id"]?>"
                                                    data-dia=""
                                                    data-datacompra="<?=dataMysqlParaPtBr($content["DataCompra"])?>"
                                                    data-valor="<?=$content["Valor"]?>"
                                                    data-type="<?=$content["IdTipoTransacao"]?>"
                                                    data-p-total-atual="<?=$content["TotalParcelas"]?>"
                                                    data-categoria-id="<?=$content["IdCategoria"]?>"
                                                    data-categoria-descricao="<?=trim($content["DescricaoCategoria"])?>"
                                                    data-subcategoria-id="<?=trim($content["IdSubCategoria"])?>"
                                                    data-subcategoria-descricao="<?=trim(valida_sub($content["DescricaoCategoria"],$content["DescricaoSubCategoria"]))?>"
                                                    data-descricao="<?=trim($content["Descricao"])?>"
                                                    data-iscontabilizado="<?=$content["IsContabilizado"]?>"
                                                    data-idconta="<?=$content["IdConta"]?>"
                                                    >
                                            </td>

                                            <td class="compra dataCompra-atual"><?=dataMysqlParaPtBr($content["DataCompra"])?></td>
                                            <td class="descricao" value="<?=$content["Descricao"]?>" data-toggle="tooltip" data-placement="top" title="<?=trim($content["DescricaoCategoria"])." - ".trim(valida_sub($content["DescricaoCategoria"],$content["DescricaoSubCategoria"]))?>">
                                                
                                                <?php if($content["Descricao"] == NULL){
                                                    echo $content["DescricaoSubCategoria"];
                                                } else{ 
                                                    echo $content["Descricao"];
                                                    if($content["IdTipoTransacao"] == "2"){
                                                        echo   "-".$content["NumeroParcela"]."/".$content["TotalParcelas"]; 	
                                                    }
                                                } 
                                                ?>

                                            </td>
                                            <td class="valor" value="<?=numeroEmReais2($content["Valor"])?>"><?=numeroEmReais2($content["Valor"])?></td>

                                            </tr>

                                        <?php } ?>

                                    </table> 

                                    <div class="total_cartao">

                                        <span class="valor_span"><?=numeroEmReais2($itemCartao["Saldo"])?></span> 
                                
                                    </div>

                                </div>
                        
                            </div>   

                        </div>

                    <?php } ?>

                </div>
            </div>           
        </div>
    </div>
</div>