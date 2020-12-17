<?=form_open("content/month_content/")?>

    <select class="menu-competencias form-control" name="competencia">
        <?php foreach($lCompetencias as $competencia){ ?>

            <option value="<?=$competencia["Ano"]?>/<?=$competencia["Mes"]?>"
            <?= ( ($competencia["Ano"] == $dataAtual["Ano"] && $competencia["Mes"] == $dataAtual["Mes"]) ? 'selected' : '' )?>>
                <?=nome_mes($competencia["Mes"])?> - <?=$competencia["Ano"]?>
            </option>

        <?php } ?>
    </select> 

<?=form_close()?>