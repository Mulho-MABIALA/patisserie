<?php
require_once 'config.php';

if(!isset($_SESSION['commande_id'])) {
    redirect('index.php');
}

$commande_id = $_SESSION['commande_id'];
unset($_SESSION['commande_id']);

// Récupérer les détails de la commande
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ?");
$stmt->execute([$commande_id]);
$commande = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande confirmée - Bijouterie Élégance</title>
    <?php echo getTailwindConfig(); ?>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
                <!-- Success Animation -->
                <div class="mb-6">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full">
                        <i class="fas fa-check text-green-500 text-4xl animate-pulse"></i>
                    </div>
                </div>
                
                <h1 class="text-3xl font-bold text-dark mb-4">Commande confirmée !</h1>
                
                <p class="text-gray-600 mb-6">
                    Merci pour votre confiance. Votre commande a été enregistrée avec succès.
                </p>
                
                <!-- Détails de la commande -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                    <h3 class="font-semibold text-dark mb-4">Détails de votre commande</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">N° de commande</span>
                            <span class="font-semibold text-primary">#<?php echo str_pad($commande_id, 6, '0', STR_PAD_LEFT); ?></span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date</span>
                            <span class="font-semibold"><?php echo date('d/m/Y'); ?></span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total</span>
                            <span class="font-bold text-danger text-lg"><?php echo number_format($commande['total'], 2); ?> €</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email</span>
                            <span class="font-semibold"><?php echo htmlspecialchars($commande['email']); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Prochaines étapes -->
                <div class="bg-blue-50 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-blue-800 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>
                        Prochaines étapes
                    </h4>
                    <ul class="text-sm text-blue-700 text-left space-y-1">
                        <li>• Vous recevrez un email de confirmation</li>
                        <li>• Notre équipe prépare votre commande</li>
                        <li>• Livraison sous 48-72h</li>
                        <li>• Paiement à la réception</li>
                    </ul>
                </div>
                
                <!-- Actions -->
                <div class="space-y-3">
                    <a href="index.php" class="block w-full bg-primary hover:bg-secondary text-white py-3 rounded-lg font-semibold transition duration-300">
                        <i class="fas fa-home mr-2"></i>
                        Retour à l'accueil
                    </a>
                    
                    <button onclick="window.print()" class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold transition duration-300">
                        <i class="fas fa-print mr-2"></i>
                        Imprimer
                    </button>
                </div>
                
                <!-- Contact -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-2">Des questions ?</p>
                    <p class="text-sm">
                        <i class="fas fa-phone text-primary mr-2"></i>
                        <a href="tel:+221771234567" class="text-primary hover:underline">+221 77 123 45 67</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>