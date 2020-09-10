<?php

require_once 'Generate.php';
require_once 'Database.php';
require_once 'Order.php';

class Order_detail {
    function __construct(){
        $this->PDO = new \PDOTester();
        $this->Obj = new Generate();
    }
        
    public function lista($dados){
        extract($dados, EXTR_PREFIX_SAME, "wddx"); //cria variavel pela posicao do array       
        $select = $this->PDO->prepare("SELECT o.id_order, o.id_product, o.product_price, o.amount, o.subtotal, p.title as produto FROM order_detail o INNER JOIN product p ON p.id = o.id_product WHERE id_order =:id_order");
        $select->bindValue(":id_order", $id_order, PDO::PARAM_INT);        
               
        $select->execute();
        $dado = $select->fetchAll(\PDO::FETCH_ASSOC);
        $result = [];
        foreach ($dado as $values){
            $total += $values["subtotal"];
            $result[] = [
                'id_order' => $values["id_order"],
                'id_product' => $values["id_product"],
                'produto' => $values["produto"],
                'amount' => $values["amount"],
                'product_price' => $values["product_price"],
                'subtotal' => $values["subtotal"],                
                'total' => $total,                
            ];
        }
        
        $Order = new Order();
        $Order->atualizaTotal($id_order, $total);
        echo json_encode($result);
    }

    public function recupera($dados) {
        extract($dados, EXTR_PREFIX_SAME, "wddx"); //cria variavel pela posicao do array       
        $select = $this->PDO->prepare("SELECT o.id_order, o.id_product, o.product_price, o.amount, o.subtotal, p.title as produto FROM order_detail o INNER JOIN product p ON p.id = o.id_product WHERE id_order = :id_order AND id_product = :id_product ");
        foreach ($dados as $key => $values) {
            $select->bindValue(":" . $key, $values);
        }
        $select->execute();
        $result = $select->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode($result);
    }

    public function novo($dados) {        
        $json_dados = (sizeof($dados) == 0) ? $_REQUEST : $dados;
        $keys = array_keys($json_dados); //Pega as chaves do array   
        $select = $this->PDO->prepare("INSERT INTO order_detail (" . implode(",", $keys) . ") VALUES (:" . implode(",:", $keys) . ")");
        foreach ($dados as $key => $value) {
            $select->bindValue(":" . $key, $value);
        }
        try {
            if ($select->execute()) {
                $result = [
                    "error" => "",
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
        extract($dados, EXTR_PREFIX_SAME, "wddx"); //cria variavel pela posicao do array 
        $json_dados = (sizeof($dados) == 0) ? $_REQUEST : $dados;
        $keys = array_keys($json_dados); //Pega as chaves do array                

        $sets = [];
        foreach ($dados as $key => $VALUES) {
            $sets[] = $key . " = :" . $key;
        }

        $update = $this->PDO->prepare("UPDATE order_detail SET " . implode(",", $sets) . " WHERE id_order = :id_order AND id_product = :id_product ;");
        foreach ($dados as $key => $values) {
            $update->bindValue(":" . $key, $values);
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
        $pks = $this->Obj->buscaPK("order_detail");
        extract($dados, EXTR_PREFIX_SAME, "wddx"); //cria variavel pela posicao do array  

        $delete = $this->PDO->prepare("DELETE FROM order_detail WHERE id_order = :id_order AND id_product = :id_product ;");
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
                    //"debug" => $delete->getSQL(),
                    "mensagem" => "SUCESSO!",
                    "causa" => "Parabéns! Dados deletados com sucesso!",
                ];
            }
        } catch (PDOException $ex) {
            $result = [
                "error" => "*",
                //"debug" => $delete->getSQL(),
                "mensagem" => "FALHOU!",
                "causa" => "DESCULPE! TIVEMOS UM PROBLEMA! (" . $ex->getCode() . ")",
            ];
        }
        echo json_encode($result);
    }

}
