<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/web projet/scripts/init.php';

    class ManageUser{
        private static $_instance = null ;
        public $link ;

        private function __construct(){
            $this->link = DB::getInstance();
        }

        public static function getInstance(){
            if (!isset(self::$_instance)){
                self::$_instance = new ManageUser();
            }
            return self::$_instance;
        }
        
        function registerUser($email, $uname, $pass){
            $status = DB::getInstance()->insert('client', array(
                'username' => $uname,
                'email' => $email,
                'pwd' => md5($pass)
            ));
            return $status;
        }
        
        function completeUserInfo($ID, $nom, $prenom, $date_naissance, $sexe){
            $status = DB::getInstance()->update('client', 'IdClient', $ID, array(
                'Nom' => $nom,
                'Prenom' => $prenom,
                'sexe' => $sexe,
                'Date_deN' => $date_naissance
            ));
            return $status;
        }
        
        function getUserInfo($field, $field_value){

            $user = $this->link->get('Client', array(array($field, '=', $field_value)));
            return $user;
        }
    }
?>