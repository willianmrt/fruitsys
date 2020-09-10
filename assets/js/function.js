function formataData(date) {
    result = date.substr(6, 4) + '-' + date.substr(3, 2) + '-' + date.substr(0, 2);
    return result;
}

function pegarDataHoje() {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();

    if (dd < 10) {
        dd = '0' + dd;
    }

    if (mm < 10) {
        mm = '0' + mm;
    }

    today = dd + '/' + mm + '/' + yyyy;
    return today;
}

function carregarPayment(){
    var payment = '';
    payment += '<option value="">**Selecione**</option>';
    payment += '<option value="1">DINHEIRO</option>';
    payment += '<option value="2">CARTÃO</option>';
    $('#payment').html(payment);
    $('#payment').val($('#id_payment').val());
}

function formata_decimal(expr, decplaces){
    expr = expr.toString(); 
    expr = expr.replace(/\,/g,".");                   
    var str = "" + Math.round(eval(expr) * Math.pow(10, decplaces));

    while (str.length <= decplaces){
        str = "0" + str;
    }                    
    var decpoint = str.length - decplaces;
    if(decplaces == 0){
        var resultado = str.substring(0, decpoint) + "" + str.substring(decpoint, str.length);
    }else{
        var resultado = str.substring(0, decpoint) + "," + str.substring(decpoint, str.length);    
    }
    resultado = resultado.replace(/(\d)(\d{3}(\.|,|$))/, '$1.$2');

    return resultado;
}

