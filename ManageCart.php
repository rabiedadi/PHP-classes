<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/web projet/scripts/init.php';

class ManageCart{
    private static $_instance = null ;
    public $link ;

    private function __construct(){
        $this->link = DB::getInstance();
        if (!isset($_SESSION['cart'])){
            ManageCart::initialise();
        }
    }

    public static function initialise(){
        $_SESSION['cart']=array();
        $_SESSION['cart']['id_produit'] = array();
        $_SESSION['cart']['nom_produit'] = array();
        $_SESSION['cart']['quantite'] = array();
        $_SESSION['cart']['prix'] = array();
        $_SESSION['cart']['prixtotal'] = array();
    }

    public static function getInstance(){
        if (!isset(self::$_instance)){
            self::$_instance = new ManageCart();
        }
        return self::$_instance;
    }

    public function addToCart($id_produit, $qte){
        $produit = DB::getInstance()->get('Produit', array(array('IdProduit', '=', $id_produit)))->first();
        $position = array_search($id_produit, $_SESSION['cart']['id_produit']);
        if ($position !== false){
            $_SESSION['cart']['quantite'][$position] += $qte;
        } else {
            array_push($_SESSION['cart']['id_produit'], $id_produit);
            array_push($_SESSION['cart']['nom_produit'], $produit->Designation);
            array_push($_SESSION['cart']['quantite'], $qte);
            array_push($_SESSION['cart']['prix'], $produit->Prix);
            array_push($_SESSION['cart']['prixtotal'], $produit->Prix * $qte);
        }

    }

    public function updateCart($id_produit, $qte){
        $position = array_search($id_produit, $_SESSION['cart']['id_produit']);
        $_SESSION['cart']['quantie'][$position] = $qte;
    }

    public function deleteFromCart($id_produit){
        $position = array_search($id_produit, $_SESSION['cart']['id_produit']);
        foreach ($_SESSION['cart'] as $item){
            $item[$position] = null;
        }
    }

    public function emptyCart(){
        foreach ($_SESSION['cart'] as $item) {
            $item = array();
        }
    }

    public function confirmCart(){
        for($i=0; $i<$_SESSION['cart']['id_produit']; $i++){
            $produit = DB::getInstance()->get('Produit', array('IdProduit', '=', $_SESSION['cart']['id_produit'][i]))->first();
            $qte = min($produit->Quantite,$_SESSION['cart']['quantite'][$i]);
            ManageProduct::getInstance()->updateProduct('IdProduit', $_SESSION['cart']['id_produit'][$i], array(
                'Quantite' => $produit->Quantite - $qte
            ));
        }
    }
}