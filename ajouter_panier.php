<?php
require_once 'config.php';

if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Vérifier si le produit existe et est en stock
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ? AND stock > 0");
    $stmt->execute([$id]);
    $produit = $stmt->fetch();
    
    if($produit) {
        // Initialiser le panier si nécessaire
        if(!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }
        
        // Ajouter au panier
        if(isset($_SESSION['panier'][$id])) {
            $_SESSION['panier'][$id]++;
        } else {
            $_SESSION['panier'][$id] = 1;
        }
    }
}

redirect('panier.php');
?>