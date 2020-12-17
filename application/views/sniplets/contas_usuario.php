<select class="menu-contas_usuario form-control" name="conta">
    <?php foreach($lcontaUsuario["Contas_Banco"] as $key => $itemConta){ ?>
        <option value="<?=$key?>"><?=$itemConta["Descricao"]?></option>
    <?php }  ?>
    <option value="Geral">Geral</option>
</select>