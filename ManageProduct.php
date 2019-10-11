<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/web projet/scripts/init.php';

class ManageProduct{
    private static $_instance = null ;
    public $link ;

    private function __construct(){
        $this->link = DB::getInstance();
    }

    public static function getInstance(){
        if (!isset(self::$_instance)){
            self::$_instance = new ManageProduct();
        }
        return self::$_instance;
    }

    public function getCategories(){
        $result = $this->link->get('categorie', null);
        return $result;
    }

    public function getProductDetails($ID){
        $result = $this->link->get('produit', array(array('IdProduit', '=', $ID)));
        return $result;
    }

    public function getProducts($where){
        $result = $this->link->get('produit', $where);
        return $result;
    }

    public function updateProduct($field_name, $field_value, $fields){
        $this->link->update('produit', $field_name, $field_value, $fields);
    }

    public function addComment($fields = array()){
        return $this->link->insert('commentaire', $fields);
    }

    public function getComments($productID){
        return $this->link->get('commentaire', array(array('idProduit', '=', $productID)));
    }

    public function getRate($table, $id_name, $id_value){
        $positif_rate = $this->link->get($table, array(array($id_name, '=', $id_value), array('etat', '=', 'p')))->count();
        $negatif_rate = $this->link->get($table, array(array($id_name, '=', $id_value), array('etat', '=', 'm')))->count();
        return $positif_rate - $negatif_rate;
    }
}