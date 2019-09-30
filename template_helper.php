<?php

    #FORMULARIO
    function template_configurarForm($pArrayFiltros,$pTipoForm = "BUSCA",$pTabela = null){

        $rFiltros           = template_validarCamposForm($pArrayFiltros,$pTipoForm,$pTabela);
        $filtrosValidados   = array_keys($rFiltros);  

        #VERIFICA SE HA GRUPOS DE INPUT
        foreach($pArrayFiltros as $keyFiltro => $itemFiltro){
            if(is_array($itemFiltro)){

                $nomeGrupo = $itemFiltro["NOME_GRUPO"];

                $explodeNomeGrupo = explode("|",$nomeGrupo);
                if(count($explodeNomeGrupo) > 1){
                    $nomeAdd    = template_configuracaoCampoForm($explodeNomeGrupo[1]);
                    if(util_isNotNull($nomeAdd)){
                        $nomeGrupo  = $explodeNomeGrupo[0]." - ".$nomeAdd->label;
                    }else{
                        $nomeGrupo  = $explodeNomeGrupo[0];
                    }
                }

                $agrupamento                            = template_validarCamposForm($itemFiltro["FILTROS"],$pTipoForm,$pTabela); 
                $rFiltros["AGRUPAMENTOS"][$nomeGrupo]   = $agrupamento;

                $filtrosValidadosGrupo                  = array_keys($agrupamento);
                $filtrosValidados                       = util_arrayMerge($filtrosValidados, $filtrosValidadosGrupo,false);
            }            
        }

        $rFiltros = template_validarFiltrosSelecionados($rFiltros,$filtrosValidados,$pTipoForm);
        return $rFiltros;
    }

    function template_configuracaoCampoForm($pCampo){

        $configuracao    = template_validarCamposForm(array($pCampo));
        if(util_isNotNull($configuracao)){
            return $configuracao[key($configuracao)];
        }else{
            return null;
        }
    }

    function template_validarCamposForm($pArrayFiltros,$pTipoForm = "BUSCA",$pTabela = null){

        $ci         = get_instance();
        $rFiltros   = array();
        $pTabela    = db_buscarTabela($pTabela);
        $model      = db_definirModel($pTabela);

        $incremento_name = "";
        $tipo_form = $pTipoForm;
        if(!empty($tipo_form)){
            if($tipo_form == "BUSCA"){$incremento_name = "WHERE->";}
        }else{$tipo_form = "CRUD";}

        foreach($pArrayFiltros as $keyFiltro => $itemFiltro){
        
            if(!is_array($itemFiltro)){

                $explodeItem    = explode("|",$itemFiltro);
                $paramItem      = array();

                $configDb       = null;                
                if(util_isNotNull($ci->G_FormConfig,$explodeItem[0])){
                    $configDb       = $ci->G_FormConfig[$explodeItem[0]]; 
                }  

                $itemForm       = $explodeItem[0];

                #PARAMNS ITEM     
                if(count($explodeItem) > 1){
                    $paramItem  = explode(",",$explodeItem[1]);
                }
                
                $campoConfig    = new FormConfig($itemForm,$tipo_form,$pTabela,$configDb,$paramItem);

                if($campoConfig->validacao){
                    $rFiltros[$campoConfig->campo] = $campoConfig;
                }

            }
        }

        return $rFiltros;
    }

    function template_validarFiltrosSelecionados($pFiltros,$pFiltrosValidados,$pTipoForm){

        $ci = get_instance();

        if($pTipoForm == "BUSCA"){
            $session_where_campos = $ci->session->userdata("WHERE_CAMPO");

            if(util_isNotNull($session_where_campos)){

                if(util_isNotNull($session_where_campos,"TIPO")){
                    $session_where_campos["TIPO_COMPLETO"] = $session_where_campos["TIPO"];
                }

                foreach($session_where_campos as $campoSession => $valorSession){

                    if(in_array($campoSession,$pFiltrosValidados)){

                        #VALOR
                        $valorSelected = $valorSession;

                        #BUSCA FILTRO
                        if(util_isNotNull($pFiltros,$campoSession)){
                            #VALIDA AUTOCOMPLETE - SELECTED ARRAY
                            if( ((isset($pFiltros[$campoSession]->autocomplete)) && ($pFiltros[$campoSession]->autocomplete))
                                ||
                                ($pFiltros[$campoSession]->type == "SELECT")
                            ){      
                                $valorArray                 = array();   
                                if(is_array($valorSelected)){
                                    $valorArray             = $valorSelected;
                                }else{
                                    $selectedExplode        = explode(";",$valorSelected);
                                    foreach($selectedExplode as $itemExplode){
                                        if(util_isNotNull($itemExplode)){
                                            array_push($valorArray,$itemExplode);
                                        }
                                    }
                                }
                                $pFiltros[$campoSession]->selectedArray = $valorArray;  

                            #CHECKBOX
                            }else if($pFiltros[$campoSession]->type == "CHECKBOX"){
                            
                                $pFiltros[$campoSession]->selectedText = "";

                                if($pFiltros[$campoSession]->valueTrue == $valorSession){
                                    $pFiltros[$campoSession]->selectedText      = "checked";
                                }
                            }
                            #SELECTED TEXT
                            else{
                                $pFiltros[$campoSession]->selectedText  = $valorSelected;
                            }

                            if(isset($pFiltros[$campoSession]->inputClass)){
                                $pFiltros[$campoSession]->inputClass   .= " Filtrado";
                            }else{
                                $pFiltros[$campoSession]->inputClass    = " Filtrado";
                            }

                            $pFiltros[$campoSession]->selectedValue     = $valorSelected;
                        }
                        #BUSCA EM AGRUPAMENTO
                        else{
                            if(isset($pFiltros["AGRUPAMENTOS"])){
                                foreach($pFiltros["AGRUPAMENTOS"] as $keyAgrupamento => $itemAgrupamento){
                                    if(isset($itemAgrupamento[$campoSession])){

                                        if( ( (isset($itemAgrupamento[$campoSession]->autocomplete)) && ($itemAgrupamento[$campoSession]->autocomplete))
                                            ||
                                            ($itemAgrupamento[$campoSession]->type == "SELECT")
                                        ){
                                            if(is_array($valorSelected)){
                                                $valorArray     = $valorSelected;
                                            }else{
                                                $selectedExplode        = explode(";",$valorSelected);
                                                $valorArray             = array();
                                                foreach($selectedExplode as $itemExplode){
                                                    if(!empty($itemExplode)){
                                                        array_push($valorArray,$itemExplode);
                                                    }
                                                }
                                            }

                                            $pFiltros["AGRUPAMENTOS"][$keyAgrupamento][$campoSession]->selectedArray = $valorArray;
                                        }else{
                                            $pFiltros["AGRUPAMENTOS"][$keyAgrupamento][$campoSession]->selectedText  = $valorSelected;
                                        }

                                        if(isset($itemAgrupamento[$campoSession]->inputClass)){
                                            $pFiltros["AGRUPAMENTOS"][$keyAgrupamento][$campoSession]->inputClass   .= " Filtrado";
                                        }else{
                                            $pFiltros["AGRUPAMENTOS"][$keyAgrupamento][$campoSession]->inputClass    = " Filtrado";
                                        }
                                    }
                                }
                            }

                        }
                        

                    }
                }
            }
        }

        return $pFiltros;
    }

    function templete_resultadoAutoComplete($pRegistro,$pCampo,$pResult){

        $return = array();

        foreach($pRegistro as $keyRegistro => $itemRegistro){

            $text = $itemRegistro[$pCampo];

            #RESULT
            if(!empty($pResult)){
                $resultExplode = explode(",",$pResult);
                foreach($resultExplode as $keyResult => $itemResult){
                    $text .= " - ".$itemRegistro[$itemResult];
                }
            }

            if(strlen($text) > 65){
                $text = substr($text,0,65);
                $text .= "...";
            }

            $item           = array();
            $item["text"]   = $text;
            $item["id"]     = $itemRegistro["ID"];

            array_push($return,$item);
        }

        return $return;
    }
    

    #REGISTROS
    function template_setConfig($pItens,$pTabela = null,$pLocal = "TABLE"){
        
        $ci                             = get_instance();
        $configuration                  = array();
        if($pTabela == null){$pTabela =  $ci->G_Config["Tabela"];}

        $paramConfigTemplate["GROUP_WHERE"]["LOCAL LIKE_ARRAY_STRING"]    = $pLocal; 
        $paramConfigTemplate["GROUP_WHERE"]["LOCAL OR"]                   = "0";
        $configTemplateDb   = $ci->template_config_model->Listar($paramConfigTemplate);
        $configTemplateDb   = util_arrayValorToIndice($configTemplateDb,"CAMPO");

        foreach($pItens as $pKeyItem => $pItem){

            $configDb = array();
            if(util_hasKey($pItem,$configTemplateDb)){
                $configDb           = $configTemplateDb[$pItem];     
            }

            $conteudoConfig     = new TemplateConfig($pItem,$configDb,$pTabela,$pLocal);

            if($conteudoConfig->validacao){
                $configuration[$pItem] = $conteudoConfig;
            }

        }

        return $configuration;

    }

    function template_setConfigConteudo($pItens,$pRegistros,$pTabela = null,$pLocal = "TABLE"){
        
        $ci                             = get_instance();
        if($pTabela == null){$pTabela   =  $ci->G_Config["Tabela"];}
        $configuration                  = template_setConfig($pItens,$pTabela,$pLocal);

        $registrosReturn = array();
        $registrosReturn["CONFIG"]      = $configuration;

        $registrosReturn["REGISTROS"]   = array();
        foreach($pRegistros as $keyRegistro => $itemRegistro){

            $registroConfig = array();
            foreach($configuration as $keyTb => $item){  
                $item->setConteudo($itemRegistro);
                $registroConfig[$keyTb] = $item->result;
            }

            $registrosReturn["REGISTROS"][$keyRegistro] = $registroConfig;
        }

        return $registrosReturn;
    }

    function template_setConfigItem($pItem,$pRegistro,$pTabela = null,$pLocal = "TABLE"){
        
        $ci                             = get_instance();
        if($pTabela == null){$pTabela   =  $ci->G_Config["Tabela"];}
        $configuration                  = template_setConfig($pItem,$pTabela,$pLocal);

        $registrosReturn = array();
        foreach($configuration as $keyTb => $item){  
            $item->setConteudo($pRegistro);
            $registrosReturn[$keyTb] = $item->result;
        }

        return $registrosReturn;

    }


    # ------------------------------


    function template_formatarConteudo($pItens,$pRegistros = array(),$pTabela = null,$textOnly = false){
        
        $ci = get_instance();
        $lItens = array();
        if($pTabela == null){$pTabela = $ci->G_Config["Tabela"];}

        foreach($pItens as $pKeyItem => $pItem){

            $item = array();

            $item["CAMPO"]              = $pItem;
            $item["LABEL"]              = Ucfirst(strtolower($pItem));
            $item["VALUE"]              = strtoupper($pItem);
            $item["CLASS"]              = "";
            $item["MASCARA"]            = "";

            $item["ICON_SORT"]["ASC"]   = "glyphicon-sort-by-alphabet";

            switch($pItem){

                case "CHECKLIST":

                    unset($item["ICON_SORT"]);
                    $item["LABEL"]          = "<span class='glyphicon glyphicon-check'></span>";
                    $item["CHECK_ON"]       = "<span class='glyphicon glyphicon-ok'></span>";
                    $item["CHECK_FALSE"]    = "<span class='glyphicon glyphicon-unchecked'></span>";
                    $item["CONTEUDO"]       = "<div class=''>
                                                    <input type='checkbox'
                                                    class='chk_toggle chk_itemRegistro'
                                                    value='DOC_INFO'
                                                    data-on='{$item["CHECK_ON"]}'
                                                    data-off='{$item["CHECK_FALSE"]}'
                                                    data-onstyle='primary'
                                                    data-offstyle='info'
                                                    data-size='mini'>
                                            </div>";
                    $item["VALUE"]          = "HASH";

                    break;
                case "ID":

                    $item["LABEL"] = "ID";

                    if(util_notIn($ci->G_Config["Ambiente"],array("desenvolvimento","teste_producao"))){         
                        $item = array();
                    }
                    
                    break;
                case "NOME":

                    if($pTabela != "EMPRESA"){
                        $item["SUBSTRING"]          = 30;
                    }

                    break;

                case "CPF":

                    $item["LABEL"]              = "CPF";

                    break;

                case "NOME":
                case "EMPRESA":

                    $item["SUBSTRING"]          = 50;

                    break;

                case "DT_EXCLUIDO":

                    $item["LABEL"]               = "Data Exclusão";

                    break;

                case "DT_CADASTRO":

                    $item["LABEL"]               = "Data Cadastro";

                    break;

                case "DT_ULTIMA_ACAO":

                    $item["LABEL"]               = "Data Ultima Ação";

                    break;
                case "DT_REJEITADO":

                    $item["LABEL"]               = "Data Rejeição";

                    break;

                case "DT_DEVOLVIDO":

                    $item["LABEL"]               = "Data Devolução";

                    break;

                case "ID_USUARIO_REJEITADO":

                    $item["LABEL"]                     = "Usuário Rejeição ";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "USUARIO_REJEITADO";
                    $item["VALUE_ARRAY"]["VALUE"]      = "NOME";

                    break;

                case "ID_USUARIO_DEVOLVIDO":

                    $item["LABEL"]                     = "Usuário Devolucao ";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "USUARIO_DEVOLVIDO";
                    $item["VALUE_ARRAY"]["VALUE"]      = "NOME";

                    break;

                case "ID_USUARIO_EXCLUIU":

                    $item["LABEL"]                     = "Usuario Exclusão";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "USUARIO_EXCLUSAO";
                    $item["VALUE_ARRAY"]["VALUE"]      = "NOME";

                    break;

                case "ID_USUARIO_CADASTRO":

                    $item["LABEL"]                      = "Usuario Cadastro";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "USUARIO_CADASTRO";
                    $item["VALUE_ARRAY"]["VALUE"]      = "NOME";

                    break;
                case "TIPO":

                    $item["LABEL"]                     = "Tipo Negociação";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "TIPO";
                    $item["VALUE_ARRAY"]["VALUE"]      = "NOME";

                    break;
                case "TIPO_EMPRESA":

                    $item["LABEL"]                     = "Tipo Empresa";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "TIPO_EMPRESA";
                    $item["VALUE_ARRAY"]["VALUE"]      = "NOME";

                    break;

                case "EMPRESA":

                    if($pTabela == "DOCUMENTO"){
                        $item["VALUE_ARRAY"]["NOME_ARRAY"] = "EMPRESA";
                        $item["VALUE_ARRAY"]["VALUE"]      = "NOME";
                        $item["SUBSTRING"]                 = 30;
                    }else{
                        $item["CAMPO"]                     = "TIPO";
                        $item["VALUE_ARRAY"]["NOME_ARRAY"] = "TIPO_EMPRESA";
                        $item["VALUE_ARRAY"]["VALUE"]      = "NOME";
                    }
                    
                    break;

                case "COD_ESCRITORIO":

                    $item["LABEL"]  = "Codigo";

                    break;

                case "CNPJ":

                    $item["LABEL"]                      = "CNPJ";
                    $item["LABEL"]                      = "CNPJ";

                    break;

                case "ID_EMPRESA":

                    $item["LABEL"]                     = "Empresa";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "EMPRESA";
                    $item["VALUE_ARRAY"]["VALUE"]      = "NOME";
                    $item["SUBSTRING"]                 = 25;

                    break;

                case "ID_EMPRESA_PAI":

                    $item["LABEL"]                     = "Empresa Pai";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "EMPRESA_PAI";
                    $item["VALUE_ARRAY"]["VALUE"]      = "NOME";
                    $item["SUBSTRING"]                 = 25;

                    break;

                case "ID_TP_ACESSO":
                case "TIPO_ACESSO":

                    $item["LABEL"]                     = "Tipo Acesso";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "TIPO_ACESSO";
                    $item["VALUE_ARRAY"]["VALUE"]      = "DESCRICAO";

                    break;

                case "ID_LOCK":
                    
                    $item["LABEL"]                     = "Usuario Tratamento";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "LOCK";
                    $item["VALUE_ARRAY"]["VALUE"]      = "NOME";
                    
                    break;
                case "DT_LOCK":
                    
                    $item["LABEL"]                     = "Data - Inicio Tratamento";
                    
                    break;
                case "ID_LOCK_TB":

                    $item["VALUE"]                              = "ID_LOCK";
                    $item["LABEL"]                               = "";
                    $item["CLASS"]                              = "TB_ID_LOCK no-view";
                    $item["IF"]["REGRAS"][0]["CHAVE"]           = "ID_LOCK";
                    $item["IF"]["REGRAS"][0]["CONDICAO"]        = ">";
                    $item["IF"]["REGRAS"][0]["VALOR"]           = "0";
                    
                    $valueIfTrue = "<span class='locked glyphicon glyphicon-lock'></span>";
                    // if(permissoes_validarUsuarioAcao("DOCUMENTO","DESBLOQUEAR_DOCUMENTO")){
                        $valueIfTrue = "<a href='#' class='desbloquear_documento' data-toggle='tooltip' data-original-title='Desbloquear'>".$valueIfTrue."</a>";
                    //}
                    
                    $item["IF"]["TRUE"]["VALUE"]                = $valueIfTrue;
                    $item["IF"]["TRUE"]["CLASS"]                = "Em-Tratamento";
                    $item["IF"]["FALSE"]["VALUE"]               = "";
                    $item["IF"]["FALSE"]["CLASS"]               = "Em-Aberto";

                    break;

                case "IC_ATIVO":

                    $item["LABEL"]                               = "ATIVO";
                    $item["IF"]["REGRAS"][0]["CHAVE"]           = "IC_ATIVO";
                    $item["IF"]["REGRAS"][0]["CONDICAO"]        = "=";
                    $item["IF"]["REGRAS"][0]["VALOR"]           = "1";
                    
                    $item["IF"]["TRUE"]["VALUE"]                = "<span class='glyphicon glyphicon-ok'></span>Sim";
                    $item["IF"]["TRUE"]["CLASS"]                = "";
                    $item["IF"]["FALSE"]["VALUE"]               = "<span class='glyphicon glyphicon-remove'></span>Não";
                    $item["IF"]["FALSE"]["CLASS"]               = "";

                    if($textOnly){
                        $item["IF"]["TRUE"]["VALUE"]            = "Sim";
                        $item["IF"]["FALSE"]["VALUE"]           = "Não";
                    }

                    break;

                case "LOGADO":

                    $item["LABEL"]                               = "Logado";
                    $item["IF"]["REGRAS"][0]["CHAVE"]           = "LOGADO";
                    $item["IF"]["REGRAS"][0]["CONDICAO"]        = "=";
                    $item["IF"]["REGRAS"][0]["VALOR"]           = "1";
                    
                    $item["IF"]["TRUE"]["VALUE"]                = "<span class='glyphicon glyphicon-ok'></span>Sim";
                    $item["IF"]["TRUE"]["CLASS"]                = "";
                    $item["IF"]["FALSE"]["VALUE"]               = "<span class='glyphicon glyphicon-remove'></span>Não";
                    $item["IF"]["FALSE"]["CLASS"]               = "";

                    if($textOnly){
                        $item["IF"]["TRUE"]["VALUE"]            = "Sim";
                        $item["IF"]["FALSE"]["VALUE"]           = "Não";
                    }

                    break;

                case "DT_ACAO":

                    $item["LABEL"]  = "Data";

                    break;

                case "ID_ACAO":

                    $item["LABEL"]                      = "Ação";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "ACAO";
                    $item["VALUE_ARRAY"]["VALUE"]      = "DESCRICAO";

                    break;

                case "ID_USUARIO_ACAO":

                    $item["LABEL"]                      = "Usuário Responsavel";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "USUARIO_ACAO";
                    $item["VALUE_ARRAY"]["VALUE"]      = "NOME";

                    break;

                case "NOME_CLIENTE":

                    $item["LABEL"]                       = "Nome Cliente";
                    $item["VALUE"]                      = "NOME_CLIENTE";
                    $item["SUBSTRING"]                  = 25;

                    break;

                case "ID_ORIGEM":

                    $item["LABEL"]                      = "Origem";
                    $item["CLASS"]                      = "Origem Origem-DOC_INFO";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"]  = "ORIGEM";
                    $item["VALUE_ARRAY"]["VALUE"]       = "VALOR";
                    $item["STR_CONFIG"]                 = "UPPER";

                    break;

                case "NR_RENEGOCIACAO":

                    $item["LABEL"]                      = "Nº Renegociação";
                    $item["VALUE"]                      = "NR_RENEGOCIACAO";

                    break;

                case "CHECK_ALTERACAO_ENDERECO":
            
                    $item["LABEL"]                              = "Alteração End.";
                    $item["VALUE"]                              = "CHECK_ALTERACAO_ENDERECO";
                    $item["CLASS"]                              = "Alteracao_End";
                    $item["IF"]["REGRAS"][0]["CHAVE"]           = "CHECK_ALTERACAO_ENDERECO";
                    $item["IF"]["REGRAS"][0]["CONDICAO"]        = "=";
                    $item["IF"]["REGRAS"][0]["VALOR"]           = "1";
                    
                            
                    $item["IF"]["TRUE"]["VALUE"]                = "<span class='glyphicon glyphicon-home'></span>Sim";
                    $item["IF"]["TRUE"]["CLASS"]                = "Alteracao-Endereco-Sim";
                    $item["IF"]["FALSE"]["VALUE"]               = "<span class='glyphicon glyphicon-ok-circle'></span>Não";
                    $item["IF"]["FALSE"]["CLASS"]               = "Alteracao-Endereco-Nao";

                    if($textOnly){
                        $item["IF"]["TRUE"]["VALUE"]            = "Sim";
                        $item["IF"]["FALSE"]["VALUE"]           = "Não";
                    }

                    break;
                case "CPF_CNPJ":

                    $item["LABEL"]                              = "CPF / CNPJ";

                    break;
                case "STATUS_ATUAL":   

                        $item["LABEL"]                      = "Status Atual";
                        $item["CLASS"]                      = "Status Status-DOC_INFO";
                        $item["VALUE_ARRAY"]["NOME_ARRAY"]  = "STATUS_ATUAL";
                        $item["VALUE_ARRAY"]["VALUE"]       = "DESCRICAO";

                    break;     

                case "STATUS":

                    $item["CLASS"]                      = "Status Status-DOC_INFO";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"]  = "STATUS";
                    $item["VALUE_ARRAY"]["VALUE"]       = "DESCRICAO";

                    break;
                case "STATUS_LOG":

                    $item["LABEL"]                      = "Status - Log";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"]  = "STATUS_LOG";
                    $item["VALUE_ARRAY"]["VALUE"]       = "DESCRICAO";

                    break;
                case "STATUS_ANTERIOR":

                    $item["LABEL"]                      = "Status Anterior";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"]  = "STATUS_ANTERIOR";
                    $item["VALUE_ARRAY"]["VALUE"]       = "DESCRICAO";

                    break;
                case "DT_STATUS":

                    $item["LABEL"]                      = "Data Ultima Atualização";

                    break;

                case "DT_VECT_RENEGOCIACAO":

                    $item["LABEL"]                      = "Data Vencimento Renegociação";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"]  = "BASE_IMPORTACAO";
                    $item["VALUE_ARRAY"]["VALUE"]       = "DT_VECT_RENEGOCIACAO";

                    break;    
                case "DESCRICAO_REGISTRO":

                    $item["LABEL"]  = "Tela/Tipo";  

                    break;

                case "ID_EMPRESA":

                    $item["LABEL"]                     = "Empresa";        
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "EMPRESA";
                    $item["VALUE_ARRAY"]["VALUE"]      = "NOME"; 

                    break;

                case "ID_RELATORIO":

                    $item["LABEL"]                      = "Tipo";        
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "RELETORIO";
                    $item["VALUE_ARRAY"]["VALUE"]      = "DESCRICAO";  

                    break;

                case "CONTRATO":

                    $item["LABEL"]                      = "Contrato";        
                    $item["VALUE"]                     = "CONTRATO";

                    break;

                case "ID_USUARIO_RESPONSAVEL":

                    $item["LABEL"]                      = "Usuário";        
                    $item["VALUE_ARRAY"]["NOME_ARRAY"]  = "USUARIO_ACAO";
                    $item["VALUE_ARRAY"]["VALUE"]       = "NOME";  
                    $item["SUBSTRING"]                  = 30;

                    break;

                case "ID_SERVICO";

                    $item["LABEL"]                      = "Serviço Automático";        
                    $item["VALUE_ARRAY"]["NOME_ARRAY"]  = "SERVICO";
                    $item["VALUE_ARRAY"]["VALUE"]       = "DESCRICAO";
                    $item["SUBSTRING"]                  = 30;

                    break;


                case "PARAM_TIPO_IMPORTACAO":

                    $item["LABEL"]                      = "Tipo Importação";        
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "TIPO_IMPORTACAO";
                    $item["VALUE_ARRAY"]["VALUE"]      = "NOME"; 

                    break;

                case "COUNT_EXCEL":

                    $item["LABEL"]                      = "Total - Planilha";        
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "DADOS";
                    $item["VALUE_ARRAY"]["VALUE"]      = "COUNT_RegistrosExcel"; 

                    break;

                case "COUNT_IMPORTADOS":

                    $item["LABEL"]                      = "Importados";        
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "DADOS";
                    $item["VALUE_ARRAY"]["VALUE"]      = "COUNT_RegistrosImportados";

                    break;
                            case "":


                    break;

                case "COUNT_ERROS":

                    $item["LABEL"]                      = "Total - Erros";        
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "DADOS";
                    $item["VALUE_ARRAY"]["VALUE"]      = "COUNT_RegistrosNaoImportados"; 

                    break;

                case "COUNT_NOVOS":

                    $item["LABEL"]                      = "Novos";        
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "DADOS";
                    $item["VALUE_ARRAY"]["VALUE"]      = "COUNT_RegistrosInseridos";  

                    break;

                case "COUNT_SUBSTITUIDOS":

                    $item["LABEL"]                      = "Atualizados";        
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "DADOS";
                    $item["VALUE_ARRAY"]["VALUE"]      = "COUNT_RegistrosAtualizados";

                    break;

                case "COUNT_EXCLUIDOS":

                    $item["LABEL"]                      = "Excluidos";        
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "DADOS";
                    $item["VALUE_ARRAY"]["VALUE"]      = "COUNT_RegistrosExcluidos";

                    break;

                case "RESULTADO_SERVICO":

                    $item["LABEL"]                      = "Resultado";  

                    break;

                case "ID_ORIGEM_SERVICO":

                    $item["LABEL"]                     = "Requisição"; 
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "ORIGEM_SERVICO";
                    $item["VALUE_ARRAY"]["VALUE"]      = "VALOR";       

                    break;

                case "STATUS_VALIDADOS":

                    $item["LABEL"]                     = "Status Validados"; 
                    $item["VALUE"]                     = "STATUS_VALIDADOS_DESCRICAO"; 

                    break;

                case "CA_TIPO_OFERTA":

                    $item["LABEL"]                     = "Tipo Oferta"; 
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "CAMPO_ADICIONAL";
                    $item["VALUE_ARRAY"]["VALUE"]      = "TIPO_OFERTA";   
                    break;
                case "CA_OBSERVACAO":

                    $item["LABEL"]                     = "Observação"; 
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "CAMPO_ADICIONAL";
                    $item["VALUE_ARRAY"]["VALUE"]      = "OBSERVACOES";   
                    break;
                case "DT_RENEGOCIACAO":

                    $item["LABEL"]          = "Data Renegociação";

                    break;

                case "ID_MOTIVO_REJEITADO":

                    $item["LABEL"]                      = "Motivo Rejeição";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"]  = "MOTIVO_REJEICAO";
                    $item["VALUE_ARRAY"]["VALUE"]       = "VALOR";  

                    break;

                case "ID_MOTIVO_DEVOLUCAO":

                    $item["LABEL"]                      = "Motivo Devolução";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"]  = "MOTIVO_DEVOLUCAO";
                    $item["VALUE_ARRAY"]["VALUE"]       = "VALOR";  

                    break;
                case "DT_PAGTO_ENTRADA":

                    $item["LABEL"]                      = "Data do Pagamento";

                    if($pTabela == "DOCUMENTO"){
                        $item["VALUE_ARRAY"]["NOME_ARRAY"]  = "BASE_IMPORTACAO";
                        $item["VALUE_ARRAY"]["VALUE"]       = "DT_PAGTO_ENTRADA"; 
                    }

                    break;
                case "DESCRICAO_RESPONSAVEL":

                    $item["LABEL"]                      = "Responsável";

                    break;
                case "TXT_DEVOLUCAO":
                case "TXT_REJEICAO":

                    $item["LABEL"]                      = "Observação";

                    break;
                case "NATUREZA":

                    $item["LABEL"]                     = "Natureza";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "NATUREZA";
                    $item["VALUE_ARRAY"]["VALUE"]      = "NOME";  

                    break;
                case "NATUREZA_DESCRICAO":

                    $item["LABEL"]                     = "Natureza";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "NATUREZA";
                    $item["VALUE_ARRAY"]["VALUE"]      = "NOME";  

                    break;
                case "DT_EMISSAO":

                    if($pTabela == "DOCUMENTO"){
                        $item["LABEL"]                     = "Data Renegociação";
                        $item["VALUE_ARRAY"]["NOME_ARRAY"] = "BASE_IMPORTACAO";
                        $item["VALUE_ARRAY"]["VALUE"]      = "DT_EMISSAO";  
                    }

                    break;
                case "CHECK_ALTERACAO_ENDERECO_DATAHIST":

                    $item["LABEL"]        = "Alteração de Endereço";
                
                    break; 

                case "DIV_PARCELAS":

                    $item["LABEL"]        = "Parcelas Montreal";

                    break;
                case "CA_LOGRADOURO":

                    $item["LABEL"]                     = "Logradouro";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "CAMPO_ADICIONAL";
                    $item["VALUE_ARRAY"]["VALUE"]      = "LOGRADOURO"; 

                    break;
                case "CA_BAIRRO":

                    $item["LABEL"]                     = "Bairro";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "CAMPO_ADICIONAL";
                    $item["VALUE_ARRAY"]["VALUE"]      = "BAIRRO"; 

                    break;
                case "CA_CEP":

                    $item["LABEL"]                     = "CEP";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "CAMPO_ADICIONAL";
                    $item["VALUE_ARRAY"]["VALUE"]      = "CEP"; 

                    break;
                case "CA_CIDADE":

                    $item["LABEL"]                     = "Cidade";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "CAMPO_ADICIONAL";
                    $item["VALUE_ARRAY"]["VALUE"]      = "CIDADE"; 

                    break;
                case "CA_UF":

                    $item["LABEL"]                     = "UF";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "CAMPO_ADICIONAL";
                    $item["VALUE_ARRAY"]["VALUE"]      = "UF"; 


                    break;
                case "CA_CHAVE_OP":

                    $item["LABEL"]                     = "Chave OP";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "CAMPO_ADICIONAL";
                    $item["VALUE_ARRAY"]["VALUE"]      = "CHAVE_OP"; 


                    break;
                case "CA_DIV_PARCELAS":

                    $item["LABEL"]                     = "Parcelas";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "CAMPO_ADICIONAL";
                    $item["VALUE_ARRAY"]["VALUE"]      = "DIV_PARCELAS"; 


                    break;
                case "CA_CAPTURA_PIN":

                    $item["LABEL"]                     = "PIN";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "CAMPO_ADICIONAL";
                    $item["VALUE_ARRAY"]["VALUE"]      = "CAPTURA_PIN"; 


                    break;
                case "CA_CAPTURA_NUMEROID":

                    $item["LABEL"]                     = "Número Identificação";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "CAMPO_ADICIONAL";
                    $item["VALUE_ARRAY"]["VALUE"]      = "CAPTURA_NUMEROID"; 


                    break;
                case "CA_CAPTURA_TIPOS_SOLICITADOS":

                    $item["LABEL"]                     = "Arquivos Solicitados";
                    $item["VALUE_ARRAY"]["NOME_ARRAY"] = "CAMPO_ADICIONAL";
                    $item["VALUE_ARRAY"]["VALUE"]      = "CAPTURA_TIPOS_SOLICITADOS"; 


                    break;
                case "PROTOCOLO_DIGITALIZACAO":

                    $item["LABEL"]                     = "Protocolo Digitalizacao";

                    break;
                case "DIGITALIZACAO":

                    $item["LABEL"]                              = "Captura Digital.";
                    $item["VALUE"]                              = "DIGITALIZACAO";
                    $item["IF"]["REGRAS"][0]["CHAVE"]           = "DIGITALIZACAO";
                    $item["IF"]["REGRAS"][0]["CONDICAO"]        = "=";
                    $item["IF"]["REGRAS"][0]["VALOR"]           = "1";
                    
                            
                    $item["IF"]["TRUE"]["VALUE"]                = "<span class='glyphicon glyphicon-camera'></span>Sim";
                    $item["IF"]["TRUE"]["CLASS"]                = "Digitalizacao-Sim";
                    $item["IF"]["FALSE"]["VALUE"]               = "<span class='glyphicon glyphicon-ok-circle'></span>Não";
                    $item["IF"]["FALSE"]["CLASS"]               = "Digitalizacao-Nao";

                    if($textOnly){
                        $item["IF"]["TRUE"]["VALUE"]            = "Sim";
                        $item["IF"]["FALSE"]["VALUE"]           = "Não";
                    }

                    break;
                case "DATAACESSO":

                    $item["LABEL"]                     = "Data Acesso";

                    break;
                case "TIPOACESSO":

                    $item["LABEL"]                     = "Tipo Acesso";

                    break;
                case "QTDACESSOS":

                    $item["LABEL"]                     = "Qtde Acessos";

                    break;
                case "QTDNEGOCIACOES":

                    $item["LABEL"]                     = "Qtde Negociações";

                    break;
                case "QTDPAGTOS":

                    $item["LABEL"]                     = "Qtd Pagamento";

                    break;
                case "QTDEFETIVACOES":

                    $item["LABEL"]                     = "Qtde Efetivações";

                    break;
                case "VALOR":

                    $item["LABEL"]                     = "Valor";
                    $item["MASCARA"]                   = "MONEY";

                    break;

                case "SENHA_TEMPORARIA":

                    $item["LABEL"]                     = "Senha Temporária";
                    
                    break;

                case "ACOES":

                    $item["LABEL"]          = "Ações";
                    
                    if(!permissoes_validarExibicaoAcoes($pTabela)){
                        $item = array();
                    }

                    break;
            }

            if(util_isNotNull($pRegistros)){
                
                #ORDER DESC
                if( (isset($item["ICON_SORT"])) && ($item["ICON_SORT"]["ASC"] == "glyphicon-sort-by-alphabet")){
                $item["ICON_SORT"]["DESC"] = "glyphicon-sort-by-alphabet-alt";}
                if( (isset($item["ICON_SORT"])) && ($item["ICON_SORT"]["ASC"] == "glyphicon-sort-by-order")){
                $item["ICON_SORT"]["DESC"] = "glyphicon-sort-by-order-alt";}

                if(isset($pRegistros["PARAMS"]["ORDER_BY"]["COLUNA"])){
                    if($item["VALUE"] == $pRegistros["PARAMS"]["ORDER_BY"]["COLUNA"]){
                        if($pRegistros["PARAMS"]["ORDER_BY"]["REGRA"] == "ASC"){
                            $item["ORDER_BY"]["REGRA"] = "ASC";
                        }else{
                            $item["ORDER_BY"]["REGRA"] = "DESC";
                        }
                    }
                }
        

                if( 
                    (isset($pRegistros["PARAMS"]["ORDER_BY"]["COLUNA_JOIN"])) && (isset($pRegistros["PARAMS"]["ORDER_BY"]["TABELA_JOIN"])) 
                    &&
                    (isset($item["VALUE_ARRAY"]["NOME_ARRAY"])) && (isset($item["VALUE_ARRAY"]["VALUE"])) 
                ){
                    if(
                        ($item["VALUE_ARRAY"]["NOME_ARRAY"] == $pRegistros["PARAMS"]["ORDER_BY"]["TABELA_JOIN"])
                        &&
                        ($item["VALUE_ARRAY"]["VALUE"] == $pRegistros["PARAMS"]["ORDER_BY"]["COLUNA_JOIN"])
                    ){
                        if($pRegistros["PARAMS"]["ORDER_BY"]["REGRA"] == "ASC"){
                            $item["ORDER_BY"]["REGRA"] = "ASC";
                        }else{
                            $item["ORDER_BY"]["REGRA"] = "DESC";
                        }
                    }
                }

            }

            if(!empty($item)){
                array_push($lItens,$item);
            }

        }

        return $lItens;

    }

    function template_formatarHistoricoDetalhes($pHistoricoRegistro){

        $return = array();

        foreach ($pHistoricoRegistro as $keyHistorico => $valueHistorico) {
            
            $itemHistorico = array();

            $tableItens = template_formatarConteudo(array("DT_ACAO","ID_USUARIO_ACAO"));
            foreach($tableItens as $keyTb => $item){  
                $tdData = template_validaConteudoTd($item,$valueHistorico,false);  
                
                #DESCRICAO CAMPOS HISTORICO
                if(!empty($tdData["DESCRICAO"])){
                    $itemHistorico["INFO_HISTORICO"][$item["LABEL"]] =  $tdData["DESCRICAO"]; 
                }            
            }

            #ACOES     
            $itemHistorico["ACAO"]                  = array();
            if(isset($valueHistorico["ACAO"])){
                $itemHistorico["ACAO"]              = $valueHistorico["ACAO"];
                $itemHistorico["ACAO"]["CLASS"]     = $valueHistorico["ACAO"]["NOME"];
            }

            #DADOS
            if(!empty($valueHistorico["ID_REGISTRO"]) && isset($valueHistorico["DADOS"])){

                if(!is_array($valueHistorico["DADOS"])){
                    $dados      = util_ObjectToArray2(json_decode($valueHistorico["DADOS"]));
                }else{
                    $dados      = $valueHistorico["DADOS"];
                }

                $dadosKeys      = array_keys($dados);
                $dadosFormatar  = array();
                #INFORMACOES - NÃO EXIBIR
                foreach($dadosKeys as $itemDado){
                    $dadosFormatar[] = $itemDado;
                }
       
                $content        = array();
                $tableData      = template_formatarConteudo($dadosFormatar);
                foreach($tableData as $keyData => $itemData){  

                    $itemDados = template_validaConteudoTd($itemData,$dados,false);  

                    if(!empty($itemDados)){
                        if(!empty($itemDados["DESCRICAO"])){

                            $content[$itemData["LABEL"]] =  $itemDados["DESCRICAO"];
                        }
                    }
                }

                $itemHistorico["DADOS"] = $content;
            }

            #ORIGEM_INFO
            $itemHistorico["ORIGEM_INFO"] = $valueHistorico["HIST_INFO"];

            $return[] = $itemHistorico;

        }

        return $return;

    }

    function template_setConfigConteudoHistorico($pHistoricoRegistro,$pIsLog = false){

        $return = array();

        foreach ($pHistoricoRegistro as $keyHistorico => $valueHistorico) {
            
            $itemHistorico = array();

            $formatacaoConfig = template_setConfigConteudo(array("DT_ACAO","ID_USUARIO_ACAO"),"HISTORICO","DETALHES");

            foreach($formatacaoConfig as $keyTb => $item){  
                
                $item->setConteudo($valueHistorico);
                $formatacaoConfig[$keyTb] = $item;

                #DESCRICAO CAMPOS HISTORICO
                if(util_isNotNull($item->result->descricao)){
                    $itemHistorico["INFO_HISTORICO"][$item->label] =  $item->result->descricao; 
                }            
            }

            #ACOES     
            $itemHistorico["ACAO"]                  = array();
            if(util_isNotNull($valueHistorico,"ACAO")){
                $itemHistorico["ACAO"]                  =   $valueHistorico["ACAO"];
                $itemHistorico["ACAO"]["CLASS"]         =   $valueHistorico["ACAO"]["NOME"];

                if($pIsLog){
                    $tabela = template_setConfigItem("TABELA",$valueHistorico,"HISTORICO","DETALHES");
                    $itemHistorico["ACAO"]["DESCRICAO"] .=  " - ".$tabela->result->descricao;
                }
            }

            #DADOS
            if(util_isNotNull($valueHistorico,"ID_REGISTRO") && util_isNotNull($valueHistorico,"DADOS")){

                if(!is_array($valueHistorico["DADOS"])){
                    $dados      = util_ObjectToArray2(json_decode($valueHistorico["DADOS"]));
                }else{
                    $dados      = $valueHistorico["DADOS"];
                }

                $dadosKeys      = array_keys($dados);
                $dadosFormatar  = array();
                #INFORMACOES - NÃO EXIBIR
                foreach($dadosKeys as $itemDado){
                    $dadosFormatar[] = $itemDado;
                }
       
                $content            = array();
                $dadosFormat        = template_setConfigConteudo($dadosFormatar); 
                foreach($dadosFormat as $keyData => $itemData){  

                    $itemData->setConteudo($dados);
                    $dadosFormat[$keyData] = $itemData;

                    if(util_isNotNull($itemData->result->descricao)){
                        $content[$itemData->label] =  $itemData->result->descricao;
                    }
                }

                $itemHistorico["DADOS"] = $content;
            }

            #ORIGEM_INFO
            $return[] = $itemHistorico;

        }

        return $return;

    }

    function template_validaConteudoTd($pItem,$pItemRegistro,$returnNull = true,$textOnly = false){

        $ci = get_instance();
        $return = null;

        if($pItem["LABEL"] != "Ações"){
            
            $tdData["VALUE"]                = "";
            $tdData["DESCRICAO"]            = "";
            $tdData["COMPL"]                = "";
            $tdData["DESCRICAO_COMPLETA"]   = "";
            $tdData["CLASS"]                = "";
            
            if(util_isNotNull($pItem,"VALUE_ARRAY") || util_isNotNull($pItem,"VALUE")){

                #VALUE ARRAY
                if(util_isNotNull($pItem,"VALUE_ARRAY")){

                    $array_Nome             = $pItem["VALUE_ARRAY"]["NOME_ARRAY"];
                    $array_Valor            = $pItem["VALUE_ARRAY"]["VALUE"];
                    
                    #IF HISTORICO
                    if(
                        (!array_key_exists($array_Nome, $pItemRegistro) )
                        &&
                        (isset($pItemRegistro["REGISTRO"]))
                    ){
                        $pItemRegistro = $pItemRegistro["REGISTRO"];
                    
                        if(util_isNotNull($pItemRegistro,$array_Nome)){
                            if(is_array($pItemRegistro[$array_Nome])){
                                if(!array_key_exists($array_Valor, $pItemRegistro[$array_Nome])){
                                    if(isset($pItemRegistro["REGISTRO"])){
                                        $pItemRegistro = $pItemRegistro["REGISTRO"];
                                    }
                                }
                            }
                        }

                    }

                    #CASO NÃO EXISTA
                    if((!isset($pItemRegistro[$array_Nome][$array_Valor]))||(!is_array($pItemRegistro[$array_Nome]))){
                        #RETORNA VAZIO?
                        if($returnNull){
                            $return = $tdData;
                        }
                    }

                    #EXISTE
                    else{
                        $tdData["VALUE"]        = $pItemRegistro[$array_Nome][$array_Valor];
                        $tdData["DESCRICAO"]    = $tdData["VALUE"];
                    }

                    #COMPLEMENTA COM ICON CASO EXISTA
                    if(!$textOnly){
                        if( isset($pItemRegistro[$array_Nome]["ICON"]) && !empty($pItemRegistro[$array_Nome]["ICON"]) ){                              
                            $tdData["DESCRICAO"]= "<span class='glyphicon ".$pItemRegistro[$array_Nome]["ICON"]."'></span>".$tdData["DESCRICAO"];
                        }
                    }
                }

                #VALUE
                else if(util_isNotNull($pItem,"VALUE")){

                    #IF HISTORICO
                    if((!isset($pItemRegistro[$pItem["VALUE"]]))
                        &&
                        (isset($pItemRegistro["REGISTRO"]))
                    ){
                        $pItemRegistro = $pItemRegistro["REGISTRO"];
                    }

                    #CASO NÃO EXISTA
                    if((!isset($pItemRegistro[$pItem["VALUE"]]))||(is_array($pItemRegistro[$pItem["VALUE"]]))){

                        #RETORNA VAZIO?
                        if($returnNull){
                            $return = $tdData;
                        }

                    }else{
                        $tdData["VALUE"]        = $pItemRegistro[$pItem["VALUE"]];
                        $tdData["DESCRICAO"]    = $tdData["VALUE"];
                    }

                    #COMPLEMENTA COM ICON CASO EXISTA
                    if( isset($pItemRegistro["ICON"]) && !empty($pItemRegistro["ICON"]) ){                              
                        $tdData["DESCRICAO"]= "<span class='glyphicon ".$pItemRegistro["ICON"]."'></span>".$tdData["DESCRICAO"];
                    }
                }

                #REPLACE
                if(util_isNotNull($pItem,"STR_REPLACE")){             
                    foreach($pItem["STR_REPLACE"] as $itemReplace){
                        if( ($itemReplace["ORIGINAL"] == "") && ($tdData["VALUE"] == "") ){$tdData["VALUE"] = $itemReplace["REPLACE"];}

                        $tdData["VALUE"]        = str_replace($itemReplace["ORIGINAL"],$itemReplace["REPLACE"],$tdData["VALUE"]);
                        $tdData["DESCRICAO"]    = $tdData["VALUE"];

                    }
                }

                #IF
                if(util_isNotNull($pItem,"IF")){
                    
                    $resultIf = false;

                    foreach($pItem["IF"]["REGRAS"] as $keyRegra => $itemRegra){

                        if(isset($itemRegra["USER_VALUE"])){
                            $tdData["VALUE"] = $itemRegra["USER_VALUE"];
                        }
                        if(isset($itemRegra["VALOR"])){
                            $tdData["VALUE"] = $itemRegra["VALOR"];
                        }
                        if($itemRegra["CONDICAO"] == ">"){

                            if(isset($pItemRegistro[$itemRegra["CHAVE"]]) && $pItemRegistro[$itemRegra["CHAVE"]] > $tdData["VALUE"]){
                                $resultIf = true;
                            }else{
                                $resultIf = false;
                            }

                            continue;
                        }
                        else if($itemRegra["CONDICAO"] == "<"){                        
                            if(isset($pItemRegistro[$itemRegra["CHAVE"]]) && $pItemRegistro[$itemRegra["CHAVE"]] < $tdData["VALUE"]){
                                $resultIf = true;
                            }else{
                                $resultIf = false;
                            }

                            continue;
                        }
                        else{
                            if(isset($pItemRegistro[$itemRegra["CHAVE"]]) && $pItemRegistro[$itemRegra["CHAVE"]] == $tdData["VALUE"]){
                                $resultIf = true;
                            }else{
                                $resultIf = false;
                            }

                            continue;
                        }

                    }
                                            
                    if($resultIf){
                        $tdData["DESCRICAO"] = $pItem["IF"]["TRUE"]["VALUE"];

                        if(util_isNotNull($pItem,"CLASS")){
                            $tdData["CLASS"] = $pItem["CLASS"]." ".$pItem["IF"]["TRUE"]["CLASS"];
                        }else{
                            $tdData["CLASS"] = $pItem["IF"]["TRUE"]["CLASS"];
                        }
                    }else{
                        $tdData["DESCRICAO"] = $pItem["IF"]["FALSE"]["VALUE"];

                        if(util_isNotNull($pItem,"CLASS")){
                            $tdData["CLASS"] = $pItem["CLASS"]." ".$pItem["IF"]["FALSE"]["CLASS"];
                        }else{
                            $tdData["CLASS"] = $pItem["IF"]["FALSE"]["CLASS"];
                        }
                    }

                }

                #COMPLEMENTO
                $tdData["COMPL"] = "";

                if(util_isNotNull($tdData,"DESCRICAO")){
                    $tdData["DESCRICAO"] = trim($tdData["DESCRICAO"]);

                    #SUBSTRING
                    if(isset($pItem["SUBSTRING"])){
                        $tdData["DESCRICAO_COMPLETA"] = $tdData["DESCRICAO"];

                        if(strlen($tdData["DESCRICAO"]) > $pItem["SUBSTRING"]){
                            $tdData["COMPL"] = "...";
                        }

                        $tdData["DESCRICAO"] = substr($tdData["DESCRICAO"],0,$pItem["SUBSTRING"]);
                        $tdData["DESCRICAO"] .= $tdData["COMPL"];
                    }

                    #MASCARA
                    if(util_isNotNull($pItem,"MASCARA")){                      
                        $convert["TIPO"] = $pItem["MASCARA"];
                        $tdData["DESCRICAO"] = util_formatar($tdData["DESCRICAO"],$convert);
                    }
                }

                if(util_isNotNull($pItem,"STR_CONFIG")){
                    if($pItem["STR_CONFIG"] == "UPPER"){
                        $tdData["DESCRICAO"] = strtoupper(util_retirarAcentuacao($tdData["DESCRICAO"])); 
                    }
                    if($pItem["STR_CONFIG"] == "UC_FIRST"){
                        $tdData["DESCRICAO"] = strtoupper(substr($tdData["DESCRICAO"],0,1)).strtolower(substr($tdData["DESCRICAO"],1,strlen($tdData["DESCRICAO"])-1));
                    }
                }

                

                $return = $tdData;

            }
      
        }
        
        return $return;          
    }

    function template_listarDadosRegistro($pArray,$pRegistro = null,$pTabela = null,$pChaveOriginal = false){

        $ci = get_instance();
        $content    = array();

        if($pTabela == null){$pTabela = $ci->G_Config["Tabela"];}
        if($pRegistro == null){$pRegistro = $ci->C_registro;}

        $tableItens = template_formatarConteudo($pArray,$pTabela);

        foreach($tableItens as $keyTb => $item){  

            $tdData = template_validaConteudoTd($item,$pRegistro,false);  

            if(!empty($tdData["DESCRICAO"])){

                $chave = $item["LABEL"];
                $valor = $tdData["DESCRICAO"];

                if($pChaveOriginal){
                    $chave = $item["CAMPO"];
 
                    $tdData["CONFIG"] = $item;
                    $tdData["CLASS"]  .= " campo-".$item["CAMPO"];
                    $tdData["CLASS"]  .= " ".str_replace("DOC_INFO",util_convertToClassCSS($tdData["VALUE"]),$item["CLASS"]);
                    
                    $valor = $tdData;
                }

                $content[$chave] =  $valor;
            }
        }

        return $content;
    }