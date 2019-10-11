<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/web projet/scripts/init.php';

    class Session{

        public static function flash($name, $value =''){
            if (isset($_SESSION[$name])){
                $session = $_SESSION[$name];
                unset($_SESSION[$name]);
                return $session;
            } else {
                $_SESSION[$name] = $value;
            }
        }

        public static function userMatch($user_id){
            return $_SESSION['connected'] ?
                (ManageUser::getInstance()->getUserInfo('username', $_SESSION['username'])->first()->IdClient == $user_id)?
                    true :
                    false :
                false;
        }
    }