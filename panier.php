<?php
require_once 'config.php';

// Traiter les actions du panier
if(isset($_POST['action'])) {
    if($_POST['action'] == 'update' && isset($_POST['quantities'])) {
        foreach($_POST['quantities'] as $id => $qty) {
            if($qty > 0) {
                $_SESSION['panier'][$id] = (int)$qty;
            } else {
                unset($_SESSION['panier'][$id]);
            }
        }
    } elseif($_POST['action'] == 'remove' && isset($_POST['id'])) {
        unset($_SESSION['panier'][$_POST['id']]);
    }
    redirect('panier.php');
}

// Récupérer les produits du panier
$produits_panier = [];
$total = 0;

if(isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
    $ids = array_keys($_SESSION['panier']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    
    while($produit = $stmt->fetch()) {
        $produit['quantite'] = $_SESSION['panier'][$produit['id']];
        $produit['sous_total'] = $produit['prix'] * $produit['quantite'];
        $total += $produit['sous_total'];
        $produits_panier[] = $produit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Panier - Bijouterie Élégance</title>
    <?php echo getTailwindConfig(); ?>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-dark shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <i class="fas fa-gem text-accent text-2xl mr-3"></i>
                    <h1 class="text-2xl font-bold text-white">Bijouterie Élégance</h1>
                </div>
                
                <div class="flex items-center space-x-6">
                    <a href="index.php" class="text-white hover:text-primary transition duration-300">
                        <i class="fas fa-arrow-left mr-2"></i>Continuer vos achats
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Contenu du panier -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <h2 class="text-3xl font-bold text-dark mb-8 flex items-center">
            <i class="fas fa-shopping-cart text-primary mr-3"></i>
            Votre Panier
        </h2>
        
        <?php if(empty($produits_panier)): ?>
        <!-- Panier vide -->
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <i class="fas fa-shopping-cart text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-2xl font-semibold text-gray-600 mb-4">Votre panier est vide</h3>
            <p class="text-gray-500 mb-8">Découvrez nos magnifiques bijoux et ajoutez vos préférés au panier.</p>
            <a href="index.php" class="inline-block bg-primary hover:bg-secondary text-white px-8 py-3 rounded-lg font-semibold transition duration-300">
                <i class="fas fa-shopping-bag mr-2"></i>Découvrir nos produits
            </a>
        </div>
        <?php else: ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Liste des produits -->
            <div class="lg:col-span-2">
                <form method="POST">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <?php foreach($produits_panier as $index => $produit): ?>
                        <div class="p-6 <?php echo $index > 0 ? 'border-t border-gray-100' : ''; ?>">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <!-- Image -->
                                <div class="w-full sm:w-24 h-24 flex-shrink-0">
                                    <?php if($produit['image']): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($produit['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($produit['nom']); ?>"
                                             class="w-full h-full object-cover rounded-lg">
                                    <?php else: ?>
                                        <div class="w-full h-full bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center">
                                            <i class="fas fa-gem text-white text-2xl"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Détails -->
                                <div class="flex-grow">
                                    <h3 class="text-lg font-semibold text-dark mb-2"><?php echo htmlspecialchars($produit['nom']); ?></h3>
                                    <p class="text-danger font-bold mb-2"><?php echo number_format($produit['prix'], 2); ?> €</p>
                                    
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center">
                                            <label class="text-sm text-gray-600 mr-2">Quantité:</label>
                                            <input type="number" 
                                                   name="quantities[<?php echo $produit['id']; ?>]" 
                                                   value="<?php echo $produit['quantite']; ?>" 
                                                   min="0" 
                                                   max="<?php echo $produit['stock']; ?>"
                                                   class="w-20 px-3 py-1 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                                        </div>
                                        
                                        <button type="submit" 
                                                name="action" 
                                                value="remove" 
                                                onclick="document.getElementById('remove_id').value=<?php echo $produit['id']; ?>"
                                                class="text-danger hover:text-red-700 transition duration-300">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Sous-total -->
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Sous-total</p>
                                    <p class="text-xl font-bold text-dark"><?php echo number_format($produit['sous_total'], 2); ?> €</p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <!-- Actions -->
                        <div class="bg-gray-50 p-6">
                            <input type="hidden" id="remove_id" name="id">
                            <button type="submit" 
                                    name="action" 
                                    value="update" 
                                    class="bg-secondary hover:bg-dark text-white px-6 py-2 rounded-lg font-semibold transition duration-300">
                                <i class="fas fa-sync-alt mr-2"></i>Mettre à jour le panier
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Résumé de la commande -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-4">
                    <h3 class="text-xl font-bold text-dark mb-6">Résumé de la commande</h3>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sous-total</span>
                            <span class="font-semibold"><?php echo number_format($total, 2); ?> €</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Livraison</span>
                            <span class="font-semibold text-green-600">Gratuite</span>
                        </div>
                        
                        <div class="border-t pt-4">
                            <div class="flex justify-between">
                                <span class="text-lg font-bold text-dark">Total</span>
                                <span class="text-2xl font-bold text-danger"><?php echo number_format($total, 2); ?> €</span>
                            </div>
                        </div>
                    </div>
                    
                    <a href="commander.php" 
                       class="block w-full bg-accent hover:bg-yellow-500 text-dark text-center py-3 rounded-lg font-semibold transition duration-300 transform hover:scale-105">
                        <i class="fas fa-credit-card mr-2"></i>Passer la commande
                    </a>
                    
                    <div class="mt-6 space-y-2 text-sm text-gray-600">
                        <p class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Paiement sécurisé
                        </p>
                        <p class="flex items-center">
                            <i class="fas fa-truck text-blue-500 mr-2"></i>
                            Livraison rapide
                        </p>
                        <p class="flex items-center">
                            <i class="fas fa-undo text-orange-500 mr-2"></i>
                            Retour facile
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>