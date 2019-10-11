<?php 
    require_once($_SERVER['DOCUMENT_ROOT'].'/web projet/scripts/init.php');

    class DB {
        private static $_instance = null ;
        protected $db_conn ;
        private $_query = '',
                $_error = false,
                $_result ,
                $_count = 0;

        private function __construct(){
            try{
                $this->db_conn = new PDO('mysql:host=' .Config::get('mysql/db_host'). '; dbname=' .Config::get('mysql/db_name'),
                                         Config::get('mysql/username'),
                                         Config::get('mysql/password'));
            }
            catch(PDOException $e){
                echo ($e->getMessage());
            }
        }
        
        public static function getInstance(){
            if (!isset(self::$_instance)){
                self::$_instance = new DB();
            }
            return self::$_instance;
        }
        public function query($sql, $params = array()){
            $this->_error = false;
            if ($this->_query = $this->db_conn->prepare($sql)){
                $x = 1;
                if (count($params)){
                    foreach($params as $param){
                        $this->_query->bindValue($x, $param);
                        $x++;
                    }
                }
                if ($this->_query->execute()) {
                    $this->_result = $this->_query->fetchAll(PDO::FETCH_OBJ);
                    $this->_count = $this->_query->rowCount();
                }else{
                    $this->_error = true ;
                }
            }
            return $this;
        }
        
        public function insert($table, $fields = array()){
            $keys = array_keys($fields);
            $values = '';
            $x = 1;

            foreach ($fields as $field){
                $values .= "?";
                if ($x < count($fields)){
                    $values .= ', ';
                }
                $x++;
            }

            $sql = "INSERT INTO {$table} (`" .implode('`, `', $keys). "`) VALUES ({$values})";
//            foreach ($fields as $key => $value){
//                echo $value;
//            }
//            die ($sql);
            if (!$this->query($sql, array_values($fields))->error()){
                return true;
            }

            return false;
        }
         
        public function update($table,$field_name, $field_value, $fields = array()){
            $set = '';
            $x = 1;
            foreach($fields as $name => $value){
                $set .= "{$name} = ?";
                if ($x < count($fields)){
                    $set .= ', ';
                }
                $x++;
            }
            
            $sql = "UPDATE {$table} SET {$set} WHERE {$field_name} = {$field_value}";
            if (!$this->query($sql, $fields)->error()){
                return true;
            }
            return false;
        }
        
        public function action($action, $table, $where){
            $operators = array ('=', '<','>', '<=', '>=');
            $values = array();
            $sql = "{$action} FROM {$table} ";
            $x = 0;
            if (isset($where)){
                foreach ($where as $param){
                    if (count($param == 3)){
                        $field    = $param[0];
                        $operator = $param[1];
                        $value    = $param[2];
                        if (in_array($operator, $operators)){
                            if ($x == 0 ) $sql .= "WHERE "; else  $sql .= "AND ";
                            $sql .= "{$field} {$operator} ? ";
                            array_push($values, $value);
                            $x++;
                        }
                    }
                }
            }
            $this->query($sql, $values);
            return $this;
        }
        
        public function get($table, $where){
            return $this->action('SELECT *', $table, $where);
        }
        
        public function delete($table, $where){
            return $this->action('DELETE', $table, $where);
        }
        
        public function first(){
            return $this->_result[0];
        }
        
        public function result(){
            return $this->_result;
        }
        
        public function error(){
            return $this->_error;
        }
        
        public function count(){
            return $this->_count;
        }
    }
