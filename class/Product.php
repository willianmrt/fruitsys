<?php

require_once 'Generate.php';
require_once 'Database.php';

class Product {

    function __construct() {
        $this->PDO = new \PDOTester();
        $this->Obj = new Generate();
    }

    public function lista($dados){
        extract($dados, EXTR_PREFIX_SAME, "wddx"); //cria variavel pela posicao do array       
        $select = $this->PDO->prepare("SELECT id, title, DATE_FORMAT(use_by,'%d/%m/%Y') as use_by, price FROM product");
        $select->execute();
        $dado = $select->fetchAll(\PDO::FETCH_ASSOC);
        $result["data"] = [];
        foreach ($dado as $values) {
            $result["data"][] = [
                $values["id"],
                $values["title"],
                $values["use_by"],
                $values["price"],
                "<td class='align_center'><button id='editar'  type='buttom' id_produto='" . $values['id'] . "'  class='btn btn-white btn-sm' data-toggle='modal' href='#modal-dialog'><i class='fas fa-pencil-alt'></i></button>&nbsp;&nbsp;                     <button id='deletar' type='buttom' id_produto='" . $values['id'] . "'  class='btn btn-white btn-sm'><i class='fas fa-trash-alt'></i></button></td>"
            ];
        }
        echo json_encode($result);
    }

    public function recupera($dados) {
        extract($dados, EXTR_PREFIX_SAME, "wddx"); //cria variavel pela posicao do array       
        $select = $this->PDO->prepare("SELECT id, title, DATE_FORMAT(use_by,'%d/%m/%Y') as use_by, price FROM product WHERE id = :id ");
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
        $select = $this->PDO->prepare("INSERT INTO product (" . implode(",", $keys) . ") VALUES (:" . implode(",:", $keys) . ")");
        foreach ($dados as $key => $value) {
            $select->bindValue(":" . $key, $value);
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

        $update = $this->PDO->prepare("UPDATE product SET " . implode(",", $sets) . " WHERE id = :id ;");
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
                "mensagem" => "FALHOU!",
                "causa" => "DESCULPE! TIVEMOS UM PROBLEMA! (" . $ex->getCode() . ")",
            ];
        }
        echo json_encode($result);
    }

    public function deleta($dados) {              
        $pks = $this->Obj->buscaPK("product");

        extract($dados, EXTR_PREFIX_SAME, "wddx"); //cria variavel pela posicao do array  

        $delete = $this->PDO->prepare("DELETE FROM product WHERE id = :id ;");
        foreach ($pks as $value) {
            foreach ($dados as $key => $values) {
                if ($key === $value["column_name"]) {
                    $delete->bindValue(":" . $value["column_name"], $values);
                }
            }
        }
        try {
            if ($delete->execute()){
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
                "mensagem" => "FALHOU!",
                "causa" => "DESCULPE! TIVEMOS UM PROBLEMA! (" . $ex->getCode() . ")",
            ];
        }
        echo json_encode($result);
    }
    
    public function busca($dados){
        extract($dados, EXTR_PREFIX_SAME, "wddx"); //cria variavel pela posicao do array       
        $select = $this->PDO->prepare("SELECT id, title, DATE_FORMAT(use_by,'%d/%m/%Y') as use_by, price FROM product WHERE use_by >= now() AND (id like :id_produto OR title like :descricao)");
        $select->bindValue(":id_produto", '%'.$id_produto.'%');
        $select->bindValue(":descricao", '%'.$descricao.'%');
        
        $select->execute();
        $count = $select->rowCount();
        if($count>0){
            $dado = $select->fetchAll(\PDO::FETCH_ASSOC);
            $result = [];
            foreach ($dado as $values){
                $result[] = [  
                    'error' => '',
                    //'debug' => $select->getSQL(),
                    'id' => $values['id'],
                    'title' => $values['title'],
                    'price' => $values['price'],
                    'use_by' => $values['use_by'],
                ];
            }        
        }else{
            $result[] = [  
                'error' => '*',
                'message' => 'Nenhum produto encontrado!',                
            ];
            
        }
        echo json_encode($result);
    }

}
