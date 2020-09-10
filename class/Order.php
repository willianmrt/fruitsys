<?php

require_once 'Generate.php';
require_once 'Database.php';

class Order {

    function __construct() {
        $this->PDO = new \PDOTester();
        $this->Obj = new Generate();
    }

    public function numeroOrder() {
        $select = $this->PDO->prepare("SELECT max(id) as ultimo FROM orders");
        $select->execute();
        $dado = $select->fetchAll(\PDO::FETCH_ASSOC);
        $result = [];
        foreach ($dado as $values) {  
            $ultimo = $values['ultimo'] + 1;            
            $result[] = [
                'ultimo' => $ultimo,
            ];
        }
        echo json_encode($result);
    }
    
    public function lista($dados) {        
        extract($dados, EXTR_PREFIX_SAME, "wddx"); //cria variavel pela posicao do array       
        $select = $this->PDO->prepare("SELECT id, DATE_FORMAT(registration_date,'%d/%m/%Y') as registration_date, payment, zip_code, address, district, number, state, city, total FROM orders");
        $select->execute();
        $dado = $select->fetchAll(\PDO::FETCH_ASSOC);
        $result["data"] = [];
        foreach ($dado as $values) {
            $payment = $this->Obj->pegaDescricaoPayment($values["payment"]);
            $result["data"][] = [
                $values["id"],
                $values["registration_date"],
                $payment,
                $values["zip_code"],
                $values["address"],
                $values["district"],
                $values["number"],
                $values["total"],
                "<td class='align_center'><button id='editar'  type='buttom' id_orders='" . $values['id'] . "'  class='btn btn-white btn-sm' data-toggle='modal' href='#modal-dialog'><i class='fas fa-pencil-alt'></i></button>&nbsp;&nbsp;                     <button id='deletar' type='buttom' id_orders='" . $values['id'] . "'  class='btn btn-white btn-sm'><i class='fas fa-trash-alt'></i></button></td>"
            ];
        }
        echo json_encode($result);
    }

    public function atualizaTotal($id_order, $total) {         
        $update = $this->PDO->prepare("UPDATE orders SET total= :total WHERE id = :id ;");
        $update->bindValue(":total", $total);
        $update->bindValue(":id", $id_order);
        
        try {
            if ($update->execute()) {
                $result = [
                    "error" => "",
                    //"debug" => $update->getSQL(),
                    "mensagem" => "SUCESSO!",
                    "causa" => "Parabéns! Dados atualizados com sucesso!",
                ];
            }
        } catch (PDOException $ex) {
            $result = [
                "error" => "*",                
                "mensagem" => "FALHOU!",
                "causa" => "DESCULPE! TIVEMOS UM PROBLEMA! (" . $ex->getCode() . ")",
            ];
        }
        //echo json_encode($result);
    }
    
    public function recupera($dados) {
        extract($dados, EXTR_PREFIX_SAME, "wddx"); //cria variavel pela posicao do array       
        $select = $this->PDO->prepare("SELECT id, DATE_FORMAT(registration_date,'%d/%m/%Y') as registration_date, payment, zip_code, address, district, number, state, city, total FROM orders WHERE id = :id ");
        foreach ($dados as $key => $values) {
            $select->bindValue(":" . $key, $values);
        }
        
        $select->execute();
        $result = $select->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode($result);
    }

    public function novo($dados) {
        $now = date('Y-m-d');
        $json_dados = (sizeof($dados) == 0) ? $_REQUEST : $dados;
        $keys = array_keys($json_dados); //Pega as chaves do array   
        $select = $this->PDO->prepare("INSERT INTO orders (" . implode(",", $keys) . ") VALUES (:" . implode(",:", $keys) . ")");
        foreach ($dados as $key => $value) {
            if($key=="registration_date"){
                $select->bindValue(":" . $key, $now);                
            }else{
                $select->bindValue(":" . $key, $value);
            }
        }
        try {
            if ($select->execute()) {
                $result = [
                    "error" => "",
                    //"debug" => $select->getSQL(),
                    "mensagem" => "SUCESSO!",
                    "causa" => "Parabéns! Dados inseridos com sucesso!",
                ];
            }
        } catch (PDOException $ex) {
            $result = [
                "error" => "*",
                "debug" => $ex->getMessage(),
                "mensagem" => "FALHOU!",
                "causa" => "DESCULPE! TIVEMOS UM PROBLEMA! (" . $ex->getCode() . ")",
            ];
        }
        echo json_encode($result);
    }

    public function edita($dados) {
        $now = date('Y-m-d');
        extract($dados, EXTR_PREFIX_SAME, "wddx"); //cria variavel pela posicao do array 
        $json_dados = (sizeof($dados) == 0) ? $_REQUEST : $dados;
        $keys = array_keys($json_dados); //Pega as chaves do array                

        $sets = array();
        foreach ($dados as $key => $VALUES) {            
            $sets[] = $key . " = :" . $key;
        }

        $update = $this->PDO->prepare("UPDATE orders SET " . implode(",", $sets) . " WHERE id = :id ;");
        foreach ($dados as $key => $values) {
            if($key=="registration_date"){
                $update->bindValue(":" . $key, $now);                
            }else{
                $update->bindValue(":" . $key, $values);
            }
            
        }
        try {
            if ($update->execute()) {
                $result = [
                    "error" => "",
                    //"debug" => $update->getSQL(),
                    "mensagem" => "SUCESSO!",
                    "causa" => "Parabéns! Dados atualizados com sucesso!",
                ];
            }
        } catch (PDOException $ex) {
            $result = [
                "error" => "*",
                //"debug" => $update->getSQL(),
                "mensagem" => "FALHOU!",
                "causa" => "DESCULPE! TIVEMOS UM PROBLEMA! (" . $ex->getCode() . ")",
            ];
        }
        echo json_encode($result);
    }

    public function deleta($dados) {        
        $pks = $this->Obj->buscaPK("orders");

        extract($dados, EXTR_PREFIX_SAME, "wddx"); //cria variavel pela posicao do array  

        $delete = $this->PDO->prepare("DELETE FROM orders WHERE id = :id ;");
        foreach ($pks as $value) {
            foreach ($dados as $key => $values) {
                if ($key === $value["column_name"]) {
                    $delete->bindValue(":" . $value["column_name"], $values);
                }
            }
        }
        try {
            if ($delete->execute()) {
                $result = [
                    "error" => "",
                    "debug" => $delete->getSQL(),
                    "mensagem" => "SUCESSO!",
                    "causa" => "Parabéns! Dados deletados com sucesso!",
                ];
            }
        } catch (PDOException $ex) {
            $result = [
                "error" => "*",
                "debug" => $delete->getSQL(),
                "mensagem" => "FALHOU!",
                "causa" => "DESCULPE! TIVEMOS UM PROBLEMA! (" . $ex->getCode() . ")",
            ];
        }
        echo json_encode($result);
    }

}
