var base_url = $(".base_url").attr("href");

// --------------------- VIEW ------------------- 
var ultimaSemana = $(".dia-calendario").last().data("semana");
var semanaAltura=1;

// -- SALDOS --
while(semanaAltura<=ultimaSemana){

    var altura = 0;
    var altura_nova = 0;

    $(".semana-mes-"+semanaAltura).each(function() {

        altura_nova = $(this).height();

        if(altura_nova > altura){
            altura = altura_nova;
        }      

    });

    $(".semana-mes-"+semanaAltura).css("height",(altura + 60)+"px");  

    semanaAltura++;  
}

setTimeout(function(){

    // $(".data-total_saldoDia").css("position","absolute");
    // $(".data-total_saldoDia").css("bottom","18px");
    // $(".data-total_saldoDia").show();
    // $(".data-total_saldoFinal").css("position","absolute");
    // $(".data-total_saldoFinal").css("bottom","0");
    // $(".data-total_saldoFinal").show();

},300);

// --------------------- PREENCHE E CALCULA CATEGORIAS E SUB-CATEGORIAS --------------------- 
$(".categoria-resumo").each(function() {

    var dom_categoria = $(this);
    var dom_BoxCategoria = dom_categoria.closest(".box");
    var count_categoria = 0;
    var total_categoria = 0;

    $(this).find(".sub_categoria-resumo").each(function() {
        
        var dom_subcategoria = $(this);
        var id_subcategoria  = $(this).data("id-subcategoria");
        var count_subcategoria = 0;
        var total_sub_categoria = 0;
        var conteudo = "";

        $(".IdSubCategoria-"+id_subcategoria).each(function(){ 

            var info_content = $(this).find(".info-content");

            var dia          = info_content.data("dia");
            var valor        = info_content.data("valor");
            var descricao    = info_content.data("descricao");
            var dataCompra   = info_content.data("datacompra");
            var classe       = "";

            if(dataCompra){
                dia = "Cartao";
                classe = "cartao";
            }
            else{
                dia = ("00" + dia).slice(-2);
                classe = "dia";
            }
        
            conteudo         = conteudo +    
                "<tr>"
                +   "<td class='dia " + classe + "'>" + dia + "</td>"
                +   "<td class='descricao'>" + descricao + "</td>"
                +   "<td class='valor' value="+valor+" >" + formatarReal(valor) + "</td>"
                +"</tr>";

            count_subcategoria++;
            count_categoria++;
            total_sub_categoria += valor;
        });

        if(count_subcategoria > 0){

            conteudo = conteudo +
            "<tr><td class='valor-total-sub_categoria' colspan='3'>"+formatarReal(total_sub_categoria)+"</td></tr>";

            $(conteudo).insertAfter(dom_subcategoria);

            total_categoria += total_sub_categoria;
        }
        else{
            dom_subcategoria.remove();
        }
        
    });

    if(count_categoria > 0){
        dom_categoria.find(".valor-total-categoria").text(formatarReal(total_categoria))
    }
    else{
        dom_BoxCategoria.remove();
    }
    
});

// ------------------------- MODAL TRANSACAO -------------------
$("tr.data-day").click(function(){
    var diaMes = $(this).closest(".dia").data("dia-mes");

    $(".modal-adiciona").find(".dia").show();
    $(".modal-adiciona").find(".dataCompra").hide();
    $(".modal-adiciona").find(".idCartao").attr("value","");
    $(".modal-adiciona").find(".dia-add").attr("value",diaMes);
});

$(".insert-cartao").click(function(){
    $(".modal-adiciona").find(".dia").hide();
    $(".modal-adiciona").find(".dataCompra").show();

    var Id = $(this).data("id");
    $(".modal-adiciona").find(".idCartao").attr("value",Id);
});

$(".categoria_modal").change(function(){

    var dom_categoria_selecionada = $(this);
    var categoria_selecionada = dom_categoria_selecionada.val();
    $(".transferencia-contas").hide();
    
    if(categoria_selecionada == "nova-categoria"){

        var dom_adicionaCategoria = dom_categoria_selecionada.closest(".modal-body").find(".adiciona-categoria");
        
        dom_adicionaCategoria.css("display","table");
        dom_adicionaCategoria.slideDown();
        
        var nova_categoria = dom_adicionaCategoria.find("input").val();
    
        if(nova_categoria != ""){
            $("select.categoria-sub").append("<option value='categoria-nova'>"+nova_categoria+"</option>");
        }

    }
    else if(categoria_selecionada == "transferencia_conta"){
        $(".sub-categoria").hide();
        $(".transferencia-contas").show();
    }
    else
    {
        $(".sub-categoria").show();
        $(".transferencia-contas").hide();
        $(".option_SubCategoria").each(function() {
    
            var id_categoria = $(this).attr("name");

            if(id_categoria != categoria_selecionada){
                $(this).hide();
            }
            else{
                $(this).show();
            }
        })
    }
    
});

$(".subcategoria_modal").change(function(){
    
    var dom_subCategoria_selecionada = $(this);
    var subCategoria_selecionada = dom_subCategoria_selecionada.val();

    if(subCategoria_selecionada == "nova-sub_categoria"){

        var dom_adicionaSubCategoria = dom_subCategoria_selecionada.closest(".modal-body").find(".adiciona-sub_categoria");
        
        dom_adicionaSubCategoria.css("display","table");
        dom_adicionaSubCategoria.slideDown();
        
        var nova_sub_categoria = $(".adiciona-categoria").find("input").val(); 
    }
});

$(".cancelar_nova_categoria").click(function(){

    $(".adiciona-categoria").slideUp();
    $("input_adiciona-categoria").val("");
    
});

$(".cancelar_nova_subcategoria").click(function(){
    
        $(".adiciona-sub_categoria").slideUp();
        $("input_adiciona-subcategoria").val("");
        
});

$(document).on("change",".conta-origem",function(){

    var var_DomContaOrigem = $(this);
    var var_DomContaDestino = $(".conta-destino");

    var id_conta = var_DomContaOrigem.val();

    if(id_conta != ""){

        var_DomContaDestino.prop("disabled",false);

        var_DomContaDestino.find("option").show();

        var_DomContaDestino.find("option").each(function() {
        
            var_DomOption = $(this);

            if( (var_DomOption.val() == id_conta) && (var_DomOption.val() != "") ){
                var_DomOption.hide();
            }

        });

    }
    else{
        var_DomContaDestino.prop("disabled",true);
        var_DomContaDestino.find("option").show();
    }

});

// -------------------------------- PREENCHE MODAL - EDICAO TRANSACAO -----------------------------
$(".content-day").click(function(){

    //Busca Dados
    var info_content = $(this).find(".info-content");

    var id                      = info_content.data("id");
    var type                    = info_content.data("type");
    var dia                     = info_content.data("dia");
    var categoria_descricao     = info_content.data("categoria-descricao");
    var categoria_id            = info_content.data("categoria-id");
    var sub_categoria_descricao = info_content.data("subcategoria-descricao");
    var sub_categoria_id        = info_content.data("subcategoria-id");
    var descricao               = info_content.data("descricao");
    var valor                   = info_content.data("valor");
    var parcelaAtual            = info_content.data("p-atual");
    var totalParcelas           = info_content.data("p-total-atual");
    var codigoTransacao         = info_content.data("codigo-transacao");
    var iscontabilizado         = info_content.data("iscontabilizado");
    var idconta                 = info_content.data("idconta");

    //Preenche Inputs
    var_DomModalEdita = $(".modal-edita");

    var Id = $(this).data("idcartao");
    if(Id){
        var_DomModalEdita.find(".idCartao").attr("value",Id);

        var_DomModalEdita.find(".dataCompra").show();
        var_DomModalEdita.find(".dia").hide();
        var_DomModalEdita.find(".mes").hide();
        var_DomModalEdita.find(".ano").hide();

        var datacompra = info_content.data("datacompra");
        var_DomModalEdita.find(".data-compra").attr("value",datacompra);
    }
    else{
        var_DomModalEdita.find(".dataCompra").hide();
        var_DomModalEdita.find(".dia").show();
        var_DomModalEdita.find(".mes").show();
        var_DomModalEdita.find(".ano").show();
    }

    var ano = var_DomModalEdita.find(".ano-edit").attr("value");
    var mes = var_DomModalEdita.find(".mes-edit").attr("value");
    
    var_DomModalEdita.find(".id-edit").attr("value",id);
    var_DomModalEdita.find(".codigo-transacao").attr("value",codigoTransacao);
    var_DomModalEdita.find(".dia-edit").attr("value",dia);
    var_DomModalEdita.find(".categoria-atual").val(categoria_id).change();
    var_DomModalEdita.find(".sub_categoria-atual").val(sub_categoria_id).change();
    var_DomModalEdita.find(".descricao-atual").attr("value",descricao);
    var_DomModalEdita.find(".valor-atual").attr("value",formatarReal(valor));
    var_DomModalEdita.find(".p-atual").attr("value",parcelaAtual);
    var_DomModalEdita.find(".p-total").attr("value",totalParcelas);
    var_DomModalEdita.find(".p-total-atual").attr("value",parcelaAtual +"/"+ totalParcelas);
    var_DomModalEdita.find(".link-update").attr("action",base_url+"adm_crud/transacao_update/"+ano+"/"+mes+"/"+id);
    var_DomModalEdita.find(".id-tipo-transacao").attr("value",type);
    var_DomModalEdita.find(".conta").val(idconta).change();
    
	$('.p-total-atual').attr("disabled", true);
    $('.p-total-atual').css("background-color", "#cccccc"); 

    if(iscontabilizado == 1){
        var_DomModalEdita.find(".isContabilizado").prop("checked",true);
        var_DomModalEdita.find(".isContabilizado").closest("p").addClass("alert-success");
        var_DomModalEdita.find(".isContabilizado").closest("p").removeClass("alert-danger");
    }
    else{
        var_DomModalEdita.find(".isContabilizado").prop("checked",false);
        var_DomModalEdita.find(".isContabilizado").closest("p").removeClass("alert-success");
        var_DomModalEdita.find(".isContabilizado").closest("p").addClass("alert-danger");    
    }
    
    if(type == "1")
    {
        var_DomModalEdita.find(".mes-edit").attr("disabled", true);
        var_DomModalEdita.find(".ano-edit").attr("disabled", true);
        $(".opcoes-transacao-repeticao").removeClass("no-view");

        $(".tipo-transacao").text("Transação Recorrente");
    }
    else{
        var_DomModalEdita.find(".mes-edit").attr("disabled", false);
        var_DomModalEdita.find(".ano-edit").attr("disabled", false);
    }

    if(type == "2"){
        $(".tipo-transacao").text("Transação Parcelada");
        $(".opcoes-transacao-repeticao").removeClass("no-view");
    }
    if(type == "3"){
        $(".tipo-transacao").text("Transação Comum");
        $(".opcoes-transacao-repeticao").addClass("no-view");
    }

});

$(".link-delete").on("click",function(){
    
    action = $(".modal-edita").find(".link-update").attr("action");
    
    $(".modal-edita").find(".link-update").attr("action",action +"/1");
});

// ---------------- ALTERACAO MANUAL ----------------------- //
var alteracao_manual = function(){
    
    status = $(this).attr("value");
    tipo = $(this).attr("name");
    
    if(status == 0){       
        $(this).attr("value",1);
        $(".alteracao-"+tipo).show();       
    }
    
    if(status == 1){       
        $(this).attr("value",0);
        $(".alteracao-"+tipo).hide();       
    }
     
}

// -------------- EVENTOS ----------------------
// -- ALTERACAO MANUAL -- 
$(".alteracao_manual").on("click",alteracao_manual);


$(".chkCheckboxModalTransacao").on("click",function(){

    if($(this).prop("checked")){
        $(this).closest("p").addClass("alert-success");
        $(this).closest("p").removeClass("alert-danger");
    }
    else{
        $(this).closest("p").removeClass("alert-success");
        $(this).closest("p").addClass("alert-danger"); 
    }

});

// -------------- FORMATACAO REAL ----------------------
function formatarReal(mixed) {

    var int = parseInt(parseFloat(mixed).toFixed(2).toString().replace(/[^\d]+/g, ''));
    var tmp = int + '';
    tmp = tmp.replace(/([0-9]{2})$/g, ",$1");
    if (tmp.length > 6)
        tmp = tmp.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
    
    if(parseFloat(mixed) < 0){
        tmp = "-"+tmp;    
    }
    
    return tmp;
}

