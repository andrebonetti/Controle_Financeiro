<?php

    function contas_BuscarContasCompleto($pParamBusca){

        $ci = get_instance();
        $pParamBusca["HasInnerJoin"]      = true;

        return $ci->contas_model->Listar($pParamBusca);

    }