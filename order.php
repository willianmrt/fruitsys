<?php
require_once "menu.php"; 
?>
<style>  
    /*FAZ MOSTRA A LISTA DO AUTOCOMPLETE NO MODAL*/ 
    .ui-autocomplete{
        z-index:9999 ;
    }

</style>  
<script type="text/javascript">
    $(document).ready(function (){
        $("#id_payment").val('');
       
        carregarPayment();
        lista();        
        limpar_produto();
    
        $("#payment").change(function () {
            $("#id_payment").val($(this).val());
        });
        
        $("#zip_code").blur(function () {
            var cep = $(this).val().replace(/\D/g, '');
            if (cep != "") {
                //Expressão regular para validar o CEP.
                var validacep = /^[0-9]{8}$/;
                if (validacep.test(cep)) {
                    $("#address").val("...");
                    $("#district").val("...");
                    $("#city").val("...");
                    $("#state").val("...");
                    //Consulta o webservice viacep.com.br/
                    $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {
                        if (!("erro" in dados)) {
                            $("#address").val(dados.logradouro);
                            $("#district").val(dados.bairro);
                            $("#city").val(dados.localidade);
                            $("#state").val(dados.uf);
                        } else {
                            swal("Atenção", "Cep não encontrado!", "warning");
                            limpa_formulário_cep();
                        }
                    });
                } else {
                    swal("Atenção", "Desculpe! Formato do CEP está incorreto!", "warning");
                    limpa_formulário_cep();
                }
            } else {
                limpa_formulário_cep();
            }
        });        
        
        function numeroPedido(){
            $.ajax({
                url: "router",
                cache: false,
                dataType: "json",
                type: "POST",
                data: {
                    "call": "Order.numeroOrder",
                },
                success: function (data){
                    $("#id").val(data[0]["ultimo"]);                    
                },
                error: function (data) {
                    swal(data["mensagem"], data["causa"], "error");
                }
            });
        }
        
        function limpa_formulário_cep(){
            $("#zip_code").val("");
            $("#address").val("");
            $("#district").val("");
            $("#city").val("");
            $("#state").val("");
            $("#zip_code").focus();
        }

        function lista() {
            var titulo = "Relatório de Order";
            var tamanho_fonte = 9;
            var orientacao = "landscape";
            var coluna_visivel = [0, 1, 2, 3, 4, 5, 6, 7];
            var imagem = "";
            var table_relacao = $("#table_relacao").DataTable({
                destroy: true,
                searching: true,
                paging: true,
                lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "Todos"]],
                dom: '<"row"<"col-sm-3"B><"col-sm-7"f><"col-sm-2"l>>t<"row"<"col-sm-5"i><"col-sm-7"p>>',
                buttons: [
                    "copy", "csv", "excel", "print",
                    {
                        extend: "pdfHtml5",
                        text: "PDF",
                        titleAttr: "Exportar para PDF",
                        orientation: orientacao,
                        pageSize: "LEGAL",
                        fontSize: tamanho_fonte,
                        title: titulo,
                        exportOptions: {
                            autoWidth: true,
                            columns: coluna_visivel, //column is visible    
                            orthogonal: "export",
                        },
                        customize: function (doc) {
                            doc.defaultStyle.fontSize = tamanho_fonte;
                            doc.pageMargins = [20, 10, 10, 25]; //left,top,right,bottom 
                            doc["styles"] = {
                                tableHeader: {
                                    alignment: "center"
                                },
                                athleteTable: {
                                    alignment: "center"
                                },
                                title: {
                                    fontSize: 18,
                                    bold: true,
                                    margin: [0, 0, 0, 0],
                                    alignment: "center"
                                },
                            };
                            var cols = [];
                            cols[0] = {
                                text: moment().format("DD/MM/YYYY, HH:mm:ss"),
                                alignment: "right", margin: [10, 10, 15, 15]
                            };
                            var objHeader = {};
                            objHeader["columns"] = cols;
                            doc["header"] = objHeader;
                            doc["content"]["1"].layout = "lightHorizontalLines";
                            var objFooter = {};
                            objFooter["alignment"] = "center";
                            doc["footer"] = function (currentPage, pageCount) {
                                var footer = [
                                    {
                                        text: "",
                                        alignment: "left",
                                        margin: [10, 10, 10, 0],
                                        color: "black",
                                    },
                                    {
                                        text: "Empresa-" + $("#ps_nome_empresa").val(),
                                        alignment: "center",
                                        margin: [10, 10, 10, 0],
                                        color: "black",
                                    },
                                    {
                                        text: "Página " + currentPage + " de " + pageCount,
                                        alignment: "right",
                                        margin: [10, 10, 10, 0],
                                        color: "black",
                                    },
                                ];
                                objFooter["columns"] = footer;
                                return objFooter;
                            };
                        }
                    }
                ],
                ajax: {
                    "url": "router",
                    "type": "POST",
                    "data": {
                        "call": "Order.lista"},
                },
                language: {
                    "decimal": "",
                    "emptyTable": "Nenhuma Informação Encontrada!",
                    "info": "Mostrando _START_ até _END_ de _TOTAL_ registro(s)",
                    "infoEmpty": "0 registro.",
                    "infoFiltered": "(Filtrados do total de _MAX_ registros)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "loadingRecords": "Carregando...",
                    "processing": "Processando...",
                    "search": "Filtrar",
                    "zeroRecords": "Nenhum Registro Encontrado!",
                    "paginate": {
                        "first": "Primeira",
                        "last": "Última",
                        "next": "Próxima",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": Ativado a Ordenação Ascendente da Coluna",
                        "sortDescending": ": Ativado a Ordenação Descendente da Coluna"
                    }
                },
                "columnDefs": [
                    {className: "align_center", "targets": [0]},
                    {className: "align_center", "targets": [1]},
                    {className: "align_center", "targets": [2]},
                    {className: "align_center", "targets": [3]},
                    {className: "align_center", "targets": [4]},
                    {className: "align_center", "targets": [5]},
                    {className: "align_center", "targets": [6]},
                    {className: "align_center", "targets": [7]},
                ],
            });
        }


        $("#novo").click(function () {            
            $("#acao").val("N");              
            if($("#acao").val()==='N'){
                $("#add_produto").prop('disabled',true);
            }
            numeroPedido();
            limpar_cadastro();
            limpar_produto();
            lista_item();
        });
        
        $("#cancelar").click(function () {
            lista();
        });
        $("#salvar").click(function () {
            var salva = true;
            if ($("#id").val() === "") {
                salva = false;
                $("#id").css("border", "1px solid red");
            } else {
                $("#id").css("border", "1px solid #303338");
            }
            if ($("#payment").val() === "") {
                salva = false;
                $("#payment").css("border", "1px solid red");
            } else {
                $("#payment").css("border", "1px solid #303338");
            }
            if ($("#zip_code").val() === "") {
                salva = false;
                $("#zip_code").css("border", "1px solid red");
            } else {
                $("#zip_code").css("border", "1px solid #303338");
            }
            if ($("#address").val() === "") {
                salva = false;
                $("#address").css("border", "1px solid red");
            } else {
                $("#address").css("border", "1px solid #303338");
            }
            if ($("#district").val() === "") {
                salva = false;
                $("#district").css("border", "1px solid red");
            } else {
                $("#district").css("border", "1px solid #303338");
            }
            if ($("#number").val() === "") {
                salva = false;
                $("#number").css("border", "1px solid red");
            } else {
                $("#number").css("border", "1px solid #303338");
            }
            if ($("#city").val() === "") {
                salva = false;
                $("#city").css("border", "1px solid red");
            } else {
                $("#city").css("border", "1px solid #303338");
            }
            if ($("#state").val() === "") {
                salva = false;
                $("#state").css("border", "1px solid red");
            } else {
                $("#state").css("border", "1px solid #303338");
            }

            if (salva) {
                $("#add_produto").prop('disabled',false);
                if ($("#acao").val() === "N") {
                    novo();
                }
                if ($("#acao").val() === "E") {
                    edita();
                } 
                lista();
            } else {
                swal("Atenção", "Por favor, informe os campo destacados em VERMELHO!", "warning");
            }
        });
        
        $("#table_relacao").on("click", "#editar", function () {
            limpar_cadastro();
            $("#acao").val("E");
            $("#id").val($(this).attr("id_orders"));
            recupera();
        });
        $("#table_relacao").on("click", "#deletar", function () {
            $("#id").val($(this).attr("id_orders"));
            swal({
                title: "Deseja deletar o registro?",
                icon: "warning",
                buttons: true,
                buttons: ["Cancelar", "Confirmar"],
            })
                    .then((willDelete) => {
                        if (willDelete) {
                            deleta();
                        }
                    });
        });
        
        function limpar_cadastro() {
            $("#id").val("");
            $("#id").css("border", "1px solid #303338");
            $("#registration_date").val("");
            $("#registration_date").css("border", "1px solid #303338");
            $("#payment").val("");
            $("#payment").css("border", "1px solid #303338");
            $("#zip_code").val("");
            $("#zip_code").css("border", "1px solid #303338");
            $("#address").val("");
            $("#address").css("border", "1px solid #303338");
            $("#district").val("");
            $("#district").css("border", "1px solid #303338");
            $("#number").val("");
            $("#number").css("border", "1px solid #303338");
            $("#city").val("");
            $("#city").css("border", "1px solid #303338");
            $("#state").val("");
            $("#state").css("border", "1px solid #303338");
            $("#total").val("");
            $("#total").css("border", "1px solid #303338");
        }

        function recupera(){
            lista_item();
            $.ajax({
                url: "router",
                cache: false,
                dataType: "json",
                type: "POST",
                data: {
                    "call": "Order.recupera",
                    "id": $("#id").val(),
                },
                success: function (data) {
                    $("#id").val(data[0]["id"]);
                    $("#registration_date").val(data[0]["registration_date"]);
                    $("#payment").val(data[0]["payment"]);
                    $("#zip_code").val(data[0]["zip_code"]);
                    $("#address").val(data[0]["address"]);
                    $("#district").val(data[0]["district"]);
                    $("#number").val(data[0]["number"]);
                    $("#city").val(data[0]["city"]);
                    $("#state").val(data[0]["state"]);
                    $("#total").val(data[0]["total"]);
                },
                error: function (data) {
                    swal(data["mensagem"], data["causa"], "error");
                }
            });
        }

        function novo() {
            $.ajax({
                url: "router",
                cache: false,
                dataType: "json",
                type: "POST",
                data: {
                    "call": "Order.novo",
                    "id": $("#id").val(),
                    "registration_date": formataData($("#registration_date").val()),
                    "payment": $("#payment").val(),
                    "zip_code": $("#zip_code").val(),
                    "address": $("#address").val(),
                    "district": $("#district").val(),
                    "number": $("#number").val(),
                    "city": $("#city").val(),
                    "state": $("#state").val(),
                    "total": $("#total").val(),
                },
                success: function (data) {
                    if (data["error"] === "*") {
                        swal(data["mensagem"], data["causa"], "error");
                    } else {                       
                        lista();                            
                        swal(data["mensagem"], data["causa"], "success");
                        $(".modal").modal("hide");                        
                    }
                },
                error: function (data) {
                    swal(data["mensagem"], data["causa"], "error");
                }
            });
        }

        function edita() {
            $.ajax({
                url: "router",
                cache: false,
                dataType: "json",
                type: "POST",
                data: {
                    "call": "Order.edita",
                    "id": $("#id").val(),
                    "registration_date": formataData($("#registration_date").val()),
                    "payment": $("#payment").val(),
                    "zip_code": $("#zip_code").val(),
                    "address": $("#address").val(),
                    "district": $("#district").val(),
                    "number": $("#number").val(),
                    "city": $("#city").val(),
                    "state": $("#state").val(),
                    "total": $("#total").val(),
                },
                success: function (data) {
                    if (data["error"] === "*") {
                        swal(data["mensagem"], data["causa"], "error");
                    } else {
                        lista();
                        swal(data["mensagem"], data["causa"], "success");
                        $(".modal").modal("hide");
                    }
                },
                error: function (data) {
                    swal(data["mensagem"], data["causa"], "error");
                }
            });
        }

        function deleta() {
            $.ajax({
                url: "router",
                data: {
                    "call": "Order.deleta",
                    "id": $("#id").val(),
                },
                cache: false,
                dataType: "json",
                type: "POST",
                success: function (data) {
                    if (data["error"] === "*") {
                        swal(data["mensagem"], data["causa"], "error");
                    } else {
                        lista();
                        swal(data["mensagem"], data["causa"], "success");
                    }
                },
                error: function (data) {
                    swal(data["mensagem"], data["causa"], "error");
                }
            });
        }        

        /*ITEM*/
        $("#produto").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: 'router',
                    dataType: 'json',
                    type: 'POST',
                    data: {call: "Product.busca", id_produto: $("#produto").val(), descricao: $("#produto").val()},
                    success: function (data) {
                        //console.log(data);
                        response($.map(data, function (item) {
                            if(item.error===''){
                                return{
                                    label: item.id + " - " + item.title,
                                    value: item.id + " - " + item.title,
                                    id: item.id,
                                    title: item.title,
                                    price: item.price,
                                    use_by: item.use_by
                                }                                
                            }else{
                                return{
                                    label: item.message,
                                    value: '',                                    
                                }
                            }
                        }))
                    },
                    error: function () {
                        swal("Erro", "Não foi possível retorna um result", "error");
                    }
                })
            },
            minLength: 1,
            type: "post",
            select: function (event, ui) {
                $('#id_product').val(ui.item.id);
                $('#produto').val(ui.item.title);
                $('#product_price').val(ui.item.price); 
                $("#acao_item").val('N');
                $("#acao").val("E");
            }
        });
        
        $("#amount").blur(function () {
            $("#subtotal").val($("#product_price").val() * $(this).val());
        });
        
        $("#cancel_produto").click(function () {
            limpar_produto();
        });
        
        $("#add_produto").click(function () {
            var salva = true;
            if ($("#id_product").val() === "") {
                salva = false;
                $("#produto").css("border", "1px solid red");
            } else {
                $("#produto").css("border", "1px solid #303338");
            }
            if ($("#amount").val() === "") {
                salva = false;
                $("#amount").css("border", "1px solid red");
            } else {
                $("#amount").css("border", "1px solid #303338");
            }
            if ($("#subtotal").val() === "") {
                salva = false;
                $("#subtotal").css("border", "1px solid red");
            } else {
                $("#subtotal").css("border", "1px solid #303338");
            }   

            if (salva) {
                //novo(false);
                if ($("#acao_item").val() === "N") {
                    novo_produto()();
                }
                if ($("#acao_item").val() === "E") {
                    edita_produto()();
                }
                lista();
            } else {
                swal("Atenção", "Por favor, informe os campo destacados em VERMELHO!", "warning");
            } 
        });
        
        function lista_item() {
            $.ajax({
                url: "router",
                cache: false,
                dataType: "json",
                type: "POST",
                data: {
                    "call": "Order_detail.lista",
                    "id_order": $("#id").val(),
                },
                success: function (data) {
                    var itens = "";
                    var total = 0;
                    var total_item = 0;
                    
                    if (data.length > 0) {
                        for (var i = 0; i < data.length; i++) {
                            var sub_total = data[i].amount * data[i].product_price;
                            total = Number(total) + Number(sub_total);
                            total_item = Number(total_item) + Number(data[i].amount);
                            itens += "<tr id='row'>";
                            itens += "<td class='align_left'>" + data[i].produto + "</td>";
                            itens += "<td class='align_center'>" + data[i].amount + "</td>";
                            itens += "<td class='align_right'>" + data[i].product_price + "</td>";
                            itens += "<td class='align_right'>" + sub_total + "</td>";
                            itens += "<td class='align_right'>\n\
                                              <button id='editar_produto'  id_product=" + data[i].id_product + " id_orders="+data[i].id_order+" type='button' class='btn btn-white btn-sm'><i class='fas fa-pencil-alt'></i></button> \n\
                                              <button id='deletar_produto' id_product=" + data[i].id_product + " id_orders="+data[i].id_order+" type='button' class='btn btn-white btn-sm' ><i class='fas fa-trash-alt' ></i></button> </td>";
                            itens += "</tr>";
                        }
                        $("#total").val(total);
                    } else {
                        itens += "<tr id='row'>";
                        itens += "<td class='align_center' colspan='5'>Nenhum produto encontrado!</td>";
                        itens += "<td></td>";
                        itens += "<td></td>";
                        itens += "<td></td>";
                        itens += "<td></td>";
                        itens += "</tr>";
                    }
                    $("#table_itens tbody").html(itens);
                },
                error: function (data){
                    swal(data["mensagem"], data["causa"], "error");
                }
            });
        }      
        
        function novo_produto(){
            $.ajax({
                url: "router",
                cache: false,
                dataType: "json",
                type: "POST",
                data: {
                    "call": "Order_detail.novo",
                    "id_order": $("#id").val(),
                    "id_product": $("#id_product").val(),
                    "product_price": $("#product_price").val(),
                    "amount": $("#amount").val(),
                    "subtotal": $("#amount").val(),
                    //"total": $("#total").val(),
                },
                success: function (data) {
                    if (data["error"] === "*") {
                        swal(data["mensagem"], data["causa"], "error");
                    } else {
                        swal(data["mensagem"], data["causa"], "success");                        
                        limpar_produto();
                        lista_item()();
                    }
                },
                error: function (data) {
                    swal(data["mensagem"], data["causa"], "error");
                }
            });
        }
        
        function limpar_produto(){           
            $("#id_product").val("");
            $("#produto").val("");
            $("#produto").css("border", "1px solid #303338");
            $("#product_price").css("border", "1px solid #303338");           
            $("#product_price").val("");
            $("#amount").val("");
            $("#amount").css("border", "1px solid #303338");           
            $("#subtotal").val("");
            $("#subtotal").css("border", "1px solid #303338");
        }

        function recupera_produto(){
           // lista_item();
            $.ajax({
                url: "router",
                cache: false,
                dataType: "json",
                type: "POST",
                data: {
                    "call": "Order_detail.recupera",
                    "id_order": $("#id").val(),
                    "id_product": $("#id_product").val(),
                },
                success: function (data){
                    $("#acao_item").val('E');
                    $("#id_product").val(data[0]["id_product"]);
                    $("#produto").val(data[0]["id_product"]+' - '+data[0]["produto"]);
                    $("#amount").val(data[0]["amount"]);
                    $("#product_price").val(data[0]["product_price"]);
                    $("#subtotal").val(data[0]["subtotal"]);                    
                },
                error: function (data) {
                    swal(data["mensagem"], data["causa"], "error");
                }
            });
        }      

        function edita_produto(){
            $.ajax({
                url: "router",
                cache: false,
                dataType: "json",
                type: "POST",
                data: {
                    "call": "Order_detail.edita",
                    "id_order": $("#id").val(),
                    "id_product": $("#id_product").val(),
                    "amount": $("#amount").val(),
                    "subtotal": $("#subtotal").val(),
                },
                success: function (data) {
                    if (data["error"] === "*") {
                        swal(data["mensagem"], data["causa"], "error");
                    } else {
                        limpar_produto();
                        lista_item()();
                        swal(data["mensagem"], data["causa"], "success");                        
                    }
                },
                error: function (data) {
                    swal(data["mensagem"], data["causa"], "error");
                }
            });
        }

        function deleta_produto(){
            $.ajax({
                url: "router",
                data: {
                    "call": "Order_detail.deleta",
                    "id_order": $("#id").val(),
                    "id_product": $("#id_product").val(),
                },
                cache: false,
                dataType: "json",
                type: "POST",
                success: function (data) {
                    if (data["error"] === "*") {
                        swal(data["mensagem"], data["causa"], "error");
                    } else {
                        lista();
                        swal(data["mensagem"], data["causa"], "success");
                        lista_item();
                    }
                },
                error: function (data) {
                    swal(data["mensagem"], data["causa"], "error");
                }
            });
        }
        
        $("#table_itens").on("click", "#editar_produto", function (){           
            limpar_produto();
            $("#acao_item").val("E");
            $("#id").val($(this).attr("id_orders"));
            $("#id_product").val($(this).attr("id_product"));
            recupera_produto();
        });
        
        $("#table_itens").on("click", "#deletar_produto", function (){
            $("#id").val($(this).attr("id_orders"));
            $("#id_product").val($(this).attr("id_product"));
            swal({
                title: "Deseja deletar o registro?",
                icon: "warning",
                buttons: true,
                buttons: ["Cancelar", "Confirmar"],
            })
                    .then((willDelete) => {
                        if (willDelete) {
                            deleta_produto();                            
                        }
                    });
        });
    });
</script>
</head>   
<div id="content" class="content">
    <div class="row">        
        <div class="col-1">
            <div class="box-tools pull-right">
                <div class="btn-group">
                    <button type="buttom" id="novo" class="btn btn-primary btn-microsoft" data-toggle="modal" href="#modal-dialog"><i class="fas fa-plus">&nbsp;</i>Novo</button>                       
                </div>
            </div>
        </div>
    </div>        
    <!--GRID-->
    <div class="box box-primary" style="width: 100%;">                              
        <div class="box-body"><br>
            <table id="table_relacao" class="table table-bordered table-striped table-condensed">
                <thead>
                    <tr><th class="align_center">Nº Pedido</th>
                        <th class="align_center">Data</th>
                        <th class="align_center">Forma de Pagamento</th>
                        <th class="align_center">CEP</th>
                        <th class="align_center">Endereço</th>
                        <th class="align_center">Bairro</th>
                        <th class="align_center">Número</th>
                        <th class="align_center">Total do Pedido</th>
                        <th class='align_center'></th>
                    </tr>
                </thead>
                <tbody>                                          
                </tbody>
            </table>
        </div>                                             
    </div> 
</div>

<!--MODAL-->
<div class="modal fade" id="modal-dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><b>Pedido</b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">                
                <input id="acao" class="form-control" type="hidden">
                <input id="acao_item" class="form-control" type="hidden">                              
                <input id="id_product" class="form-control" type="hidden">
                <input id="id_payment" class="form-control" type="hidden">
                <input id="use_by" class="form-control" type="hidden">
                <input id="registration_date" class="form-control" type="hidden">
                <div class="form-row">  
                    <div class="col-lg-2 col-xs-12">
                        <div class="form-group col-md-12"><label>Nº Pedido</label>
                            <input id="id" class="form-control" type="text" readonly="true">
                        </div>
                    </div> 
                    <div class="col-lg-6 col-xs-12">
                        <div class="form-group col-md-12"><label>Forma de Pagamento</label>
                            <select id="payment" class="form-control"></select>  
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-lg-2 col-xs-12">
                        <div class="form-group col-md-12"><label>CEP</label>
                            <input id="zip_code" class="form-control" type="text" maxlength="8">
                        </div>
                    </div>                    
                    <div class="col-lg-8 col-xs-12">
                        <div class="form-group col-md-12"><label>Endereço</label>
                            <input id="address" class="form-control" type="text">
                        </div>
                    </div>
                    <div class="col-lg-2 col-xs-12">
                        <div class="form-group col-md-12"><label>Número</label>
                            <input id="number" class="form-control" type="text">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-lg-6 col-xs-12">
                        <div class="form-group col-md-12"><label>Bairro</label>
                            <input id="district" class="form-control" type="text">
                        </div>
                    </div>
                    <div class="col-lg-4 col-xs-12">
                        <div class="form-group col-md-12"><label>Cidade</label>
                            <input id="city" class="form-control" type="text">
                        </div>
                    </div> 
                    <div class="col-lg-2 col-xs-12">
                        <div class="form-group col-md-12"><label>UF</label>
                            <input id="state" class="form-control" type="text">
                        </div>
                    </div>
                </div>                    
                <div class="form-row">
                    <div class="col-lg-6 col-xs-12">
                        <div class="form-group col-md-12"><label>Total do Pedido</label>
                            <input id="total" class="form-control" type="text">
                        </div>
                    </div>
                </div>

                <!--PRODUTOS-->
                <hr>
                <h3 class="box-title"><b>Item</b></h3>  
                <div class="form-row">
                    <div class="col-lg-6 col-xs-12">
                        <div class="form-group col-md-12"><label>Produto</label>
                            <input id="produto" class="form-control" type="text" placeholder="Informe o código ou a descrição do produto">
                        </div>
                    </div>
                    <div class="col-lg-2 col-xs-12">
                        <div class="form-group col-md-12"><label>Preço</label>
                            <input id="product_price" class="form-control" type="text" readonly="true">
                        </div>
                    </div>                    
                    <div class="col-lg-2 col-xs-12">
                        <div class="form-group col-md-12"><label>Quantidade</label>
                            <input id="amount" class="form-control" type="text">
                        </div>
                    </div>
                    <div class="col-lg-2 col-xs-12">
                        <div class="form-group col-md-12"><label>Sub-Total</label>
                            <input id="subtotal" class="form-control" type="text" readonly="true">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-lg-10 col-xs-12">                    
                        <button type="buttom" id="add_produto" class="btn btn-primary btn-lg btn-block">Salvar Produto</button>
                    </div>
                    <div class="col-lg-2 col-xs-12">                    
                        <button type="buttom" id="cancel_produto" class="btn btn-warning btn-lg btn-block">Cancelar</button>
                    </div>
                </div>   
                <hr>

                <div class="col-sm-12 table-responsive">                    
                    <table id="table_itens" class="table table-condensed">
                        <thead>
                            <tr>                                            
                                <th style='text-align: left;'>Produto</th>
                                <th style='text-align: center;'>Qtde</th>
                                <th style='text-align: right;'>Valor Unit.</th>
                                <th style='text-align: right;'>Sub-Total</th>                                                
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="salvar" class="btn btn-success"><i class="fas fa-save fa-2x">&nbsp;</i>Salvar</button>
                <button type="button" id="cancelar" class="btn btn-warning" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times fa-2x">&nbsp;</i>Cancelar</button>
            </div>
        </div>
    </div>
</div>
</body>   
</html>