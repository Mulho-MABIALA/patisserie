<?php
require_once 'config.php';

// Vérifier que le panier n'est pas vide
if(!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
    redirect('panier.php');
}

// Traiter le formulaire de commande
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse']);
    
    // Validation basique
    if(empty($nom) || empty($email) || empty($adresse)) {
        $erreur = "Veuillez remplir tous les champs obligatoires.";
    } else {
        // Calculer le total
        $total = 0;
        $ids = array_keys($_SESSION['panier']);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $stmt = $pdo->prepare("SELECT * FROM produits WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        
        $produits = [];
        while($produit = $stmt->fetch()) {
            $produit['quantite'] = $_SESSION['panier'][$produit['id']];
            $total += $produit['prix'] * $produit['quantite'];
            $produits[] = $produit;
        }
        
        // Créer la commande
        $pdo->beginTransaction();
        try {
            // Insérer la commande
            $stmt = $pdo->prepare("INSERT INTO commandes (nom_client, email, telephone, adresse, total) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $email, $telephone, $adresse, $total]);
            $commande_id = $pdo->lastInsertId();
            
            // Insérer les détails de la commande et mettre à jour le stock
            foreach($produits as $produit) {
                $stmt = $pdo->prepare("INSERT INTO details_commande (commande_id, produit_id, quantite, prix_unitaire) VALUES (?, ?, ?, ?)");
                $stmt->execute([$commande_id, $produit['id'], $produit['quantite'], $produit['prix']]);
                
                // Mettre à jour le stock
                $stmt = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE id = ?");
                $stmt->execute([$produit['quantite'], $produit['id']]);
            }
            
            $pdo->commit();
            
            // Vider le panier
            unset($_SESSION['panier']);
            
            // Rediriger vers la confirmation
            $_SESSION['commande_id'] = $commande_id;
            redirect('confirmation.php');
            
        } catch(Exception $e) {
            $pdo->rollBack();
            $erreur = "Une erreur est survenue lors de la commande.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passer commande</title>
    <style>
        /* Styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        header {
            background-color: #333;
            color: white;
            padding: 1rem;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn:hover {
            background-color: #229954;
        }
        .erreur {
            color: #e74c3c;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>Finaliser votre commande</h1>
    </header>
    
    <div class="container">
        <?php if(isset($erreur)): ?>
            <p class="erreur"><?php echo $erreur; ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="nom">Nom complet *</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone">
            </div>
            
            <div class="form-group">
                <label for="adresse">Adresse de livraison *</label>
                <textarea id="adresse" name="adresse" rows="4" required></textarea>
            </div>
            
            <button type="submit" class="btn">Confirmer la commande</button>
        </form>
    </div>
</body>
</html>