<?php
require_once "Database.php";

class Generate{
    public $PDO;
    
    public function __construct(){        
        $this->PDO  = new \PDOTester();         
    }     
   
    //public function buscaPK($tabelaDB, $drive=null, $database=null){
    public function buscaPK($tabelaDB){
        if($this->PDO->drive==='MYSQL'){     
            $sqlPK = "SELECT sta.column_name,
                    (SELECT data_type FROM information_schema.columns WHERE column_name=sta.column_name 
                    AND table_schema=tab.table_schema AND table_name=tab.table_name) as data_type
                    FROM information_schema.tables as tab
                    INNER JOIN information_schema.statistics as sta ON sta.table_schema = tab.table_schema
                    AND sta.table_name = tab.table_name
                    AND sta.index_name = 'PRIMARY'
                    WHERE tab.table_schema = :database
                    AND tab.table_type = 'BASE TABLE'
                    AND tab.table_name = :tabelaDB;";
            $select = $this->PDO->prepare($sqlPK);                        
            $select->bindParam(':database', $this->PDO->database);        
        }else if($this->PDO->drive==='POSTGRESQL'){
            $sqlPK = "SELECT c.column_name, c.data_type
                      FROM information_schema.table_constraints tc 
                      JOIN information_schema.constraint_column_usage AS ccu USING (constraint_schema, constraint_name) 
                      JOIN information_schema.columns AS c ON c.table_schema = tc.constraint_schema
                      AND tc.table_name = c.table_name AND ccu.column_name = c.column_name
                      WHERE constraint_type = 'PRIMARY KEY' and tc.table_name = :tabelaDB ORDER BY ordinal_position;";
            $select = $this->PDO->prepare($sqlPK);            
        }
        
        $select->bindParam(':tabelaDB', $tabelaDB);          
        $select->execute();                         
        $pks = $select->fetchAll(\PDO::FETCH_ASSOC);        
                    
        return $pks;            
    }
    
    public function pegaDescricaoPayment($id){
        switch ($id) {
            case "1":
                $result = "DINHEIRO";
                break;
            case "2":
                $result = "CARTÃO";
                break;
            default:
                $result = "******";
                break;
        }        
        return $result;
    }
}
