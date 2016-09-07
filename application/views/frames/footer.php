    <?=anchor("/","",array("class"=>"no-view base_url"))?>

    <!-- MODAIS -->

    <!-- MODAL - ADICIONAR TRANSAÇÃO -->
    <div class="modal fade modal-adiciona" id="add-transacao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Adicionar Transação</h4>
          </div>
          <div class="modal-body">

              <?= form_open("Adm_crud/transacao_insert")?>

                    <input type="hidden" name="usuario" value="<?=$usuario['Id']?>">
                    <input type="hidden" name="ano" value="<?=$ano?>">
                    <input type="hidden" name="mes" value="<?=$mes?>">

                    <!-- DIA -->
                    <div class="dia">
                        <label>Dia</label>
                        <input type="number" name="dia" class="dia-add form-control" placeholder="Dia">
                        <span class="mes">/ <?=$mes?></span>
                        <span class="ano">/ <?=$ano?></span>
                    </div>    

                    <!-- CATEGORIA -->
                    <div class="categoria">
                        <label>Categoria</label>
                        <select name="categoria" class="form-control">       
                            <option value="0">Escolha a Categoria</option>
                            <?php foreach($categorias as $categoria){?>
                                <option value="<?=$categoria["IdCategoria"]?>"><?=$categoria["DescricaoCategoria"]?></option>
                            <?php } ?>
                            <option value="nova-categoria">Adicionar Categoria</option>
                        </select>
                    </div>    

                    <!-- SUB_CATEGORIA -->
                    <div class="sub-categoria">
                        <label>Sub-Categoria</label>
                        <select name="sub_categoria" class="form-control">
                            <option value="0">Escolha a Sub Categoria</option>
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
              
                    <!-- TRANSAÇÃO RECORRENTE -->
                    <div class="transacao-recorrente">
                        <label>Transação Recorrente ?</label>
                        <input type="radio" name="isRecorrente" class="RecorrenteSim" value="1"><span class="RecorrenteSim">Sim</span>
                        <input type="radio" name="isRecorrente" class="RecorrenteNao" value="0" checked><span class="RecorrenteNao">Não</span>
                    </div>

                    <input type="submit" class="btn btn-primary" value="Adicionar"> 
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                <?= form_close()?>
          </div>
        </div>
      </div>
    </div>

     <!-- MODAL - EDITAR TRANSAÇÃO  -->
    <div class="modal fade modal-edita" id="edit-transacao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Editar Transação</h4>
          </div>
          <div class="modal-body">
                <?= form_open("Adm_crud/transacao_update",array("class"=>"link-update"))?>

                    <input type="hidden" class="id-edit" name="id" value="">
              
                    <!-- DIA -->
                    <div class="dia dia-edit">
                        <label>Dia</label>
                        <input type="number" name="dia" class="dia-edit form-control" placeholder="Dia">/
                    </div>    
                    <div class="mes">
                        <label>Mês</label>
                        <input type="number" class="mes-edit form-control" name="mes" value="<?=$mes?>">/
                    </div>    
                    <div class="ano">
                        <label>Ano</label>
                        <input type="number" class="ano-edit form-control" name="ano" value="<?=$ano?>">
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
                        <label>Numero de Parcelas</label>
                        <input type="number" class="p-total-atual form-control" name="total" value="0" placeholder="Número de Parcelas"/>
                    </div>

                    <div class="buttons">
                        <?=anchor("","Excluir",array("class"=>"btn btn-danger link-delete"))?>
                        <input type="submit" class="btn btn-atualizar btn-primary" value="Atualizar"> 
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    </div>

                <?= form_close()?>
          </div>
        </div>
      </div>
    </div>

    <!-- ADD CARTAO-->
    <div class="modal fade modal-adiciona" id="add-cartao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Adicionar Transação</h4>
          </div>
          <div class="modal-body">

              <?= form_open("Adm_crud/cartao_insert")?>
                    <input type="hidden" name="usuario" value="<?=$usuario['id_usuario']?>">
                    <input type="hidden" name="ano" value="<?=$ano?>">
                    <input type="hidden" name="mes" value="<?=$mes?>">
                    <input type="text" name="data_compra" class="dia-add form-control" placeholder="Compra">
                    <select name="categoria" class="categoria form-control">       
                        <option>Escolha a Categoria</option>
                        <?php foreach($categorias as $categoria){?>
                            <option value="<?=$categoria["id_categoria"]?>"><?=$categoria["nome_categoria"]?></option>
                        <?php } ?>
                        <option value="nova-categoria">Adicionar Categoria</option>
                    </select>

                    <select name="sub_categoria" class="sub_categoria form-control">
                        <option>Escolha a Sub Categoira</option>
                        <?php foreach($all_sub_categorias as $sub_categoria){?>
                            <option value="<?=$sub_categoria["id_sub_categoria"]?>" name="<?=$sub_categoria["id_categoria"]?>"><?=$sub_categoria["nome_sub_categoria"]?></option>
                        <?php } ?>
                        <option value="nova-sub_categoria">Adicionar Sub_categoria</option>
                    </select>

                    <div class="adiciona-categoria">
                        <label>Adiconar Categoria</label>
                        <input type="text" name="adiciona-categoria" class="form-control" Placeholder="Nome da Nova Categoria:">
                    </div>

                    <div class="adiciona-sub_categoria">
                        <label>Adiconar Sub Categoria</label>
                        <select name="categoria-sub" class="categoria-sub form-control">
                            <option>Categoria Relacionada</option>
                            <?php foreach($categorias as $categoria){?>
                                <option value="<?=$categoria["id_categoria"]?>"><?=$categoria["nome_categoria"]?></option>
                            <?php } ?>
                            <option id="adiciona-categoria">Adicionar Categoria</option>
                        </select>
                        <input type="text" name="adiciona-sub_categoria" class="categoria-sub form-control">
                    </div>    

                    <input type="text"   name="descricao" class="descricao form-control" placeholder="Descrição">
                    <input type="text" name="valor" class="valor form-control" placeholder="Valor">

                    <input type="number" class="p-total form-control" name="total" placeholder="Número de Parcelas"/>

                    <input type="checkbox" name="type" value="1"> Transação Recorrente <br>
                    <input type="checkbox" name="type" value="2"> Transação Parcelada

                    <input type="submit" class="btn btn-primary" value="Adicionar"> 
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                <?= form_close()?>
          </div>
        </div>
      </div>
    </div>

    <!-- EDIT CARTAO-->
    <div class="modal fade modal-cartao-edit" id="edita-cartao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Editar Fatura</h4>
          </div>
          <div class="modal-body">

              <?= form_open("Adm_crud/cartao_update",array("class"=>"link-update"))?>

                    <input type="hidden" name="usuario" value="<?=$usuario['id_usuario']?>">
                    <input type="hidden" class="ano" name="ano" value="<?=$ano?>">
                    <input type="hidden" class="mes" name="mes" value="<?=$mes?>">

                    <select name="categoria" class="categoria form-control">
                        <option class="categoria-atual"></option>
                        <?php foreach($categorias as $categoria){?>
                            <option value="<?=$categoria["id_categoria"]?>"><?=$categoria["nome_categoria"]?></option>
                        <?php } ?>
                    </select>

                    <select name="sub_categoria" class="sub_categoria form-control">
                        <option class="sub_categoria-atual"></option>
                        <?php foreach($all_sub_categorias as $sub_categoria){?>
                            <option value="<?=$sub_categoria["id_sub_categoria"]?>"><?=$sub_categoria["nome_sub_categoria"]?></option>
                        <?php } ?>
                    </select>

                    <div class="adiciona-categoria">
                        <label>Adiconar Categoria</label>
                        <input type="text" name="adiciona-categoria" class="form-control" Placeholder="Nome da Nova Categoria:">
                    </div>

                    <div class="adiciona-sub_categoria">
                        <label>Adiconar Sub Categoria</label>
                        <select name="categoria-sub" class="categoria-sub form-control">
                            <option>Categoria Relacionada</option>
                            <?php foreach($categorias as $categoria){?>
                                <option value="<?=$categoria["id_categoria"]?>"><?=$categoria["nome_categoria"]?></option>
                            <?php } ?>
                            <option id="adiciona-categoria">Adicionar Categoria</option>
                        </select>
                        <input type="text" name="adiciona-sub_categoria" class="categoria-sub form-control">
                    </div>    

                    <input type="text"   name="descricao" class="descricao form-control" placeholder="Descrição">
                    <input type="text" name="valor" class="valor form-control" placeholder="Valor">

                    <div class="buttons">
                        <?=anchor("","Excluir",array("class"=>"btn btn-danger link-delete"))?>
                        <input type="submit" class="btn btn-primary" value="Atualizar"> 
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    </div>

                <?= form_close()?>

          </div>
        </div>
      </div>
    </div>

   
    <!-- EDIT -->
    <div class="modal fade modal-poupanca" id="edit-poupanca" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Adicionar Transação</h4>
          </div>
          <div class="modal-body">
                <?= form_open("Adm_crud/transacao_update",array("class"=>"link-update"))?>

                    <label>Poupança: </label>
                    <input type="text" name="valor" class="valor form-control" placeholder="Valor">

                    <div class="buttons">
                        <?=anchor("","Excluir",array("class"=>"btn btn-danger link-delete"))?>
                        <input type="submit" class="btn btn-primary" value="Atualizar"> 
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    </div>

                <?= form_close()?>
          </div>
        </div>
      </div>
    </div>

        <script src="<?=base_url("js/my_script-content.js")?>"></script>

        </body>
    </html>    
