<?php
require_once "menu.php";

?>

<script type="text/javascript">
    $(document).ready(function () {
        $(function () {
            $(".campo_data").datepicker({
                language: "pt_br",
                format: "dd/mm/yyyy",
            }).on("changeDate", function (e){
                $(this).datepicker("hide");
            });
        });

        lista();

        function lista() {
            var titulo = "Relatório de Produto";
            var tamanho_fonte = 9;
            var orientacao = "landscape";
            var coluna_visivel = [0, 1, 2, 3];
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
                            doc.pageMargins = [20, 10, 10, 25];//left,top,right,bottom 
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
                                        text: "Empresa:" + $("#ps_nome_empresa").val(),
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
                        "call": "Product.lista"},
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
                ],
            });          
        } 

        $("#novo").click(function () {
            $("#acao").val("N");
            limpar_cadastro();
        });

        $("#salvar").click(function () {
            var salva = true;
            if ($("#title").val() === "") {
                salva = false;
                $("#title").css("border", "1px solid red");
            } else {
                $("#title").css("border", "1px solid #303338");
            }
            if ($("#use_by").val() === "") {
                salva = false;
                $("#use_by").css("border", "1px solid red");
            } else {
                $("#use_by").css("border", "1px solid #303338");
            }
            if ($("#price").val() === "") {
                salva = false;
                $("#price").css("border", "1px solid red");
            } else {
                $("#price").css("border", "1px solid #303338");
            }

            if (salva) {
                if ($("#acao").val() === "N") {
                    novo();
                }
                if ($("#acao").val() === "E") {
                    edita();
                }
            } else {
                swal("Atenção", "Por favor, informe os campo destacados em VERMELHO!", "warning");
            }
        });

        $("table").on("click", "#editar", function (){
            limpar_cadastro();
            $("#acao").val("E");
            $("#id").val($(this).attr("id_produto"));
            recupera();
        });

        $("table").on("click", "#deletar", function (){
            $("#id").val($(this).attr("id_produto"));
            swal({
                title: "Deseja deletar o registro?",
                //text: "Once deleted, you will not be able to recover this imaginary file!",
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
            $("#title").val("");
            $("#title").css("border", "1px solid #303338");
            $("#use_by").val("");
            $("#use_by").css("border", "1px solid #303338");
            $("#price").val("");
            $("#price").css("border", "1px solid #303338");
        }

        function recupera() {
            $.ajax({
                url: "router",
                cache: false,
                dataType: "json",
                type: "POST",
                data: {
                    "call": "Product.recupera",
                    "id": $("#id").val(),
                },
                success: function (data) {
                    $("#id").val(data[0]["id"]);
                    $("#title").val(data[0]["title"]);
                    $("#use_by").val(data[0]["use_by"]);
                    $("#price").val(data[0]["price"]);
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
                    "call": "Product.novo",
                    "title": $("#title").val(),
                    "use_by": formataData($("#use_by").val()),
                    "price": $("#price").val(),
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
                    "call": "Product.edita",
                    "id": $("#id").val(),
                    "title": $("#title").val(),
                    "use_by": formataData($("#use_by").val()),
                    "price": $("#price").val(),
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
                    "call": "Product.deleta",
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
                    <tr><th class="align_center">Cód.</th>
                        <th class="align_center">Descrição</th>
                        <th class="align_center">Data de Validade</th>
                        <th class="align_center">Preço</th>
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
<div class="modal fade" id="modal-dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Product</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form action="post">
                    <input id="id" class="form-control" type="hidden">
                    <input id="acao" class="form-control" type="hidden">                    
                    <div class="form-row">
                        <div class="col-lg-12 col-xs-12">
                            <div class="form-group col-md-12"><label>Descrição</label>
                                <input id="title" class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6 col-xs-12">
                            <div class="form-group col-md-12"><label>Data de Validade</label>
                                <input id="use_by" class="form-control campo_data" readonly="true" type="text">
                            </div>
                        </div>
                        <div class="col-lg-6 col-xs-12">
                            <div class="form-group col-md-12"><label>Preço</label>
                                <input id="price" class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="salvar" class="btn btn-success"><i class="fas fa-save fa-2x">&nbsp;</i>Salvar</button>
                <button type="button" id="cancelar" class="btn btn-warning" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times fa-2x">&nbsp;</i>Cancelar</button>
            </div>
        </div>
    </div>
</div>
<!--FIM MODAL-->    
</body>   
</html>