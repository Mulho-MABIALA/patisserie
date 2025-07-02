<?php
require_once '../config.php';

if(!isAdmin()) {
    redirect('login.php');
}

// Vérifier que l'ID est fourni
if(!isset($_GET['id'])) {
    redirect('commandes.php');
}

$commande_id = (int)$_GET['id'];

// Récupérer les informations de la commande
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ?");
$stmt->execute([$commande_id]);
$commande = $stmt->fetch();

if(!$commande) {
    redirect('commandes.php');
}

// Récupérer les détails des produits commandés
$stmt = $pdo->prepare("
    SELECT dc.*, p.nom, p.description, p.image 
    FROM details_commande dc
    JOIN produits p ON dc.produit_id = p.id
    WHERE dc.commande_id = ?
");
$stmt->execute([$commande_id]);
$details = $stmt->fetchAll();

// Calculer les statistiques
$nb_articles = array_sum(array_column($details, 'quantite'));
$nb_types_produits = count($details);
$prix_moyen = $nb_articles > 0 ? $commande['total'] / $nb_articles : 0;

// Configuration des statuts
$statut_icons = [
    'en_attente' => 'fa-hourglass-half',
    'confirmee' => 'fa-check',
    'expediee' => 'fa-truck',
    'livree' => 'fa-check-double',
    'annulee' => 'fa-times'
];

$statut_colors = [
    'en_attente' => 'yellow',
    'confirmee' => 'blue',
    'expediee' => 'purple',
    'livree' => 'green',
    'annulee' => 'red'
];

$statut_labels = [
    'en_attente' => 'En attente',
    'confirmee' => 'Confirmée',
    'expediee' => 'Expédiée',
    'livree' => 'Livrée',
    'annulee' => 'Annulée'
];

$current_statut = $commande['statut'];
$statut_icon = $statut_icons[$current_statut] ?? 'fa-question';
$statut_color = $statut_colors[$current_statut] ?? 'gray';
$statut_label = $statut_labels[$current_statut] ?? 'Inconnu';

// Ordre de progression des statuts
$statut_order = ['en_attente', 'confirmee', 'expediee', 'livree'];
$current_statut_index = array_search($current_statut, $statut_order);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la commande #<?php echo str_pad($commande_id, 6, '0', STR_PAD_LEFT); ?> - Administration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#55D5E0',
                        'secondary': '#335F8A', 
                        'dark': '#2F4558',
                        'accent': '#F6B12D',
                        'warning': '#F26619',
                        'danger': '#EF4444'
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .shadow-lg { box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Mobile menu overlay -->
        <div id="mobile-menu-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden no-print"></div>
        
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-dark shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 no-print">
            <div class="flex items-center justify-between p-6 lg:justify-center">
                <div class="flex items-center">
                    <i class="fas fa-gem text-accent text-2xl mr-3"></i>
                    <h1 class="text-xl font-bold text-white">Admin Panel</h1>
                </div>
                <!-- Close button for mobile -->
                <button id="close-sidebar" class="lg:hidden text-white hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <nav class="px-6 pb-6 space-y-2">
                <a href="index.php" class="flex items-center px-4 py-3 text-gray-300 hover:bg-secondary hover:text-white rounded-lg transition duration-200">
                    <i class="fas fa-dashboard mr-3"></i>
                    <span class="font-medium">Tableau de bord</span>
                </a>
                <a href="gestion_clients.php" class="flex items-center px-4 py-3 text-gray-300 hover:bg-secondary hover:text-white rounded-lg transition duration-200">
                    <i class="fas fa-users mr-3"></i>
                    <span class="font-medium">Clients</span>
                </a>
                <a href="produits.php" class="flex items-center px-4 py-3 text-gray-300 hover:bg-secondary hover:text-white rounded-lg transition duration-200">
                    <i class="fas fa-gem mr-3"></i>
                    <span class="font-medium">Produits</span>
                </a>
                <a href="commandes.php" class="flex items-center px-4 py-3 bg-secondary text-white rounded-lg">
                    <i class="fas fa-shopping-cart mr-3"></i>
                    <span class="font-medium">Commandes</span>
                </a>
                <hr class="my-4 border-gray-700">
                <a href="../index.php" target="_blank" class="flex items-center px-4 py-3 text-gray-300 hover:bg-secondary hover:text-white rounded-lg transition duration-200">
                    <i class="fas fa-external-link-alt mr-3"></i>
                    <span class="font-medium">Voir le site</span>
                </a>
                <a href="logout.php" class="flex items-center px-4 py-3 text-gray-300 hover:bg-danger hover:text-white rounded-lg transition duration-200">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    <span class="font-medium">Déconnexion</span>
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 no-print">
                <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button id="mobile-menu-button" class="lg:hidden mr-4 text-gray-600 hover:text-gray-900">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-receipt text-primary text-xl sm:text-2xl"></i>
                            <h2 class="text-lg sm:text-2xl font-bold text-dark">Commande #<?php echo str_pad($commande_id, 6, '0', STR_PAD_LEFT); ?></h2>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="hidden sm:block text-gray-600 mr-4 text-sm sm:text-base">Admin</span>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-primary rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-sm sm:text-base"></i>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Breadcrumb -->
            <div class="px-4 sm:px-6 lg:px-8 py-4 bg-gray-50 border-b border-gray-200 no-print">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm text-gray-500">
                        <li>
                            <a href="index.php" class="hover:text-primary transition-colors duration-200">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>
                        <li><i class="fas fa-chevron-right text-gray-300"></i></li>
                        <li>
                            <a href="commandes.php" class="hover:text-primary transition-colors duration-200">Commandes</a>
                        </li>
                        <li><i class="fas fa-chevron-right text-gray-300"></i></li>
                        <li class="text-dark font-medium">Commande #<?php echo str_pad($commande_id, 6, '0', STR_PAD_LEFT); ?></li>
                    </ol>
                </nav>
            </div>
            
            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    <!-- Status Banner -->
                    <div class="mb-6 sm:mb-8 bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-<?php echo $statut_color; ?>-400">
                        <div class="p-4 sm:p-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-<?php echo $statut_color; ?>-100 rounded-full flex items-center justify-center">
                                            <i class="fas <?php echo $statut_icon; ?> text-<?php echo $statut_color; ?>-600 text-xl sm:text-2xl"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-xl sm:text-2xl font-bold text-dark">Commande #<?php echo str_pad($commande_id, 6, '0', STR_PAD_LEFT); ?></h3>
                                        <p class="text-<?php echo $statut_color; ?>-600 font-semibold text-base sm:text-lg"><?php echo $statut_label; ?></p>
                                        <p class="text-gray-500 text-sm">Passée le <?php echo date('d/m/Y à H:i', strtotime($commande['created_at'])); ?></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl sm:text-3xl font-bold text-accent"><?php echo number_format($commande['total'], 0, ',', ' '); ?> CFA</p>
                                    <p class="text-gray-500 text-sm"><?php echo $nb_articles; ?> article<?php echo $nb_articles > 1 ? 's' : ''; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                        <!-- Main Content -->
                        <div class="lg:col-span-2 space-y-6 lg:space-y-8">
                            <!-- Customer Information -->
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                                <div class="bg-gradient-to-r from-secondary to-dark px-4 sm:px-6 py-4">
                                    <h4 class="text-lg sm:text-xl font-bold text-white flex items-center space-x-2">
                                        <i class="fas fa-user text-primary"></i>
                                        <span>Informations client</span>
                                    </h4>
                                </div>
                                
                                <div class="p-4 sm:p-6">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                                        <div class="space-y-4">
                                            <div class="flex items-start space-x-3">
                                                <i class="fas fa-user text-secondary text-lg mt-1"></i>
                                                <div>
                                                    <p class="text-sm text-gray-600">Nom complet</p>
                                                    <p class="font-semibold text-dark"><?php echo htmlspecialchars($commande['nom_client']); ?></p>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-start space-x-3">
                                                <i class="fas fa-envelope text-secondary text-lg mt-1"></i>
                                                <div>
                                                    <p class="text-sm text-gray-600">Email</p>
                                                    <a href="mailto:<?php echo htmlspecialchars($commande['email']); ?>" 
                                                       class="font-semibold text-primary hover:text-cyan-600 transition-colors duration-200">
                                                        <?php echo htmlspecialchars($commande['email']); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="space-y-4">
                                            <?php if($commande['telephone']): ?>
                                            <div class="flex items-start space-x-3">
                                                <i class="fas fa-phone text-secondary text-lg mt-1"></i>
                                                <div>
                                                    <p class="text-sm text-gray-600">Téléphone</p>
                                                    <a href="tel:<?php echo htmlspecialchars($commande['telephone']); ?>" 
                                                       class="font-semibold text-dark hover:text-primary transition-colors duration-200">
                                                        <?php echo htmlspecialchars($commande['telephone']); ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <div class="flex items-start space-x-3">
                                                <i class="fas fa-map-marker-alt text-secondary text-lg mt-1"></i>
                                                <div>
                                                    <p class="text-sm text-gray-600">Adresse de livraison</p>
                                                    <div class="font-semibold text-dark whitespace-pre-line"><?php echo htmlspecialchars($commande['adresse']); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Products Ordered -->
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                                <div class="bg-gradient-to-r from-primary to-cyan-400 px-4 sm:px-6 py-4">
                                    <h4 class="text-lg sm:text-xl font-bold text-white flex items-center space-x-2">
                                        <i class="fas fa-shopping-bag text-white"></i>
                                        <span>Produits commandés</span>
                                    </h4>
                                </div>
                                
                                <!-- Desktop Table -->
                                <div class="hidden md:block overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 xl:px-6 py-4 text-left text-sm font-semibold text-gray-600">Produit</th>
                                                <th class="px-4 xl:px-6 py-4 text-left text-sm font-semibold text-gray-600">Prix unitaire</th>
                                                <th class="px-4 xl:px-6 py-4 text-left text-sm font-semibold text-gray-600">Quantité</th>
                                                <th class="px-4 xl:px-6 py-4 text-left text-sm font-semibold text-gray-600">Sous-total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <?php foreach($details as $detail): ?>
                                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                                <td class="px-4 xl:px-6 py-4">
                                                    <div class="flex items-center space-x-3">
                                                        <?php if($detail['image'] && file_exists('../uploads/' . $detail['image'])): ?>
                                                        <img src="../uploads/<?php echo htmlspecialchars($detail['image']); ?>" 
                                                             alt="<?php echo htmlspecialchars($detail['nom']); ?>"
                                                             class="w-12 h-12 object-cover rounded-lg">
                                                        <?php else: ?>
                                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                            <i class="fas fa-image text-gray-400"></i>
                                                        </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <div class="font-semibold text-dark"><?php echo htmlspecialchars($detail['nom']); ?></div>
                                                            <?php if($detail['description']): ?>
                                                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars(substr($detail['description'], 0, 50)); ?>...</div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 xl:px-6 py-4">
                                                    <span class="text-accent font-semibold"><?php echo number_format($detail['prix_unitaire'], 0, ',', ' '); ?> CFA</span>
                                                </td>
                                                <td class="px-4 xl:px-6 py-4">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-semibold bg-secondary text-white">
                                                        <?php echo $detail['quantite']; ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 xl:px-6 py-4">
                                                    <span class="font-bold text-dark"><?php echo number_format($detail['prix_unitaire'] * $detail['quantite'], 0, ',', ' '); ?> CFA</span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="bg-gradient-to-r from-accent to-orange-400">
                                            <tr>
                                                <th colspan="3" class="px-4 xl:px-6 py-4 text-left text-white font-bold text-lg">Total de la commande</th>
                                                <th class="px-4 xl:px-6 py-4 text-left text-white font-bold text-xl"><?php echo number_format($commande['total'], 0, ',', ' '); ?> CFA</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                                <!-- Mobile Cards -->
                                <div class="md:hidden divide-y divide-gray-200">
                                    <?php foreach($details as $detail): ?>
                                    <div class="p-4 space-y-3">
                                        <div class="font-semibold text-dark text-lg"><?php echo htmlspecialchars($detail['nom']); ?></div>
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-600">Prix unitaire:</span>
                                                <div class="font-semibold text-accent"><?php echo number_format($detail['prix_unitaire'], 0, ',', ' '); ?> CFA</div>
                                            </div>
                                            <div>
                                                <span class="text-gray-600">Quantité:</span>
                                                <div class="font-semibold text-secondary"><?php echo $detail['quantite']; ?></div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-gray-600 text-sm">Sous-total:</span>
                                            <div class="font-bold text-dark text-lg"><?php echo number_format($detail['prix_unitaire'] * $detail['quantite'], 0, ',', ' '); ?> CFA</div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    
                                    <div class="p-4 bg-gradient-to-r from-accent to-orange-400">
                                        <div class="flex justify-between items-center">
                                            <span class="text-white font-bold text-lg">Total de la commande</span>
                                            <span class="text-white font-bold text-xl"><?php echo number_format($commande['total'], 0, ',', ' '); ?> CFA</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sidebar -->
                        <div class="space-y-6">
                            <!-- Order Stats -->
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                                <div class="bg-gradient-to-r from-accent to-orange-400 px-4 py-3">
                                    <h4 class="text-lg font-bold text-white flex items-center space-x-2">
                                        <i class="fas fa-chart-pie text-white"></i>
                                        <span>Statistiques</span>
                                    </h4>
                                </div>
                                
                                <div class="p-4 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Articles</span>
                                        <span class="font-bold text-dark"><?php echo $nb_articles; ?></span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Types produits</span>
                                        <span class="font-bold text-dark"><?php echo $nb_types_produits; ?></span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Prix moyen</span>
                                        <span class="font-bold text-accent"><?php echo number_format($prix_moyen, 0, ',', ' '); ?> CFA</span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Total</span>
                                        <span class="font-bold text-secondary"><?php echo number_format($commande['total'], 0, ',', ' '); ?> CFA</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Order Timeline -->
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                                <div class="bg-gradient-to-r from-secondary to-dark px-4 py-3">
                                    <h4 class="text-lg font-bold text-white flex items-center space-x-2">
                                        <i class="fas fa-clock text-primary"></i>
                                        <span>Chronologie</span>
                                    </h4>
                                </div>
                                
                                <div class="p-4">
                                    <div class="space-y-4">
                                        <?php 
                                        $timeline_items = [
                                            ['status' => 'en_attente', 'label' => 'Commande passée', 'icon' => 'fa-shopping-cart'],
                                            ['status' => 'confirmee', 'label' => 'Commande confirmée', 'icon' => 'fa-check'],
                                            ['status' => 'expediee', 'label' => 'Expédition', 'icon' => 'fa-truck'],
                                            ['status' => 'livree', 'label' => 'Livraison', 'icon' => 'fa-check-double']
                                        ];
                                        
                                        foreach($timeline_items as $index => $item):
                                            $is_completed = $current_statut_index !== false && $index <= $current_statut_index;
                                            $is_current = $item['status'] == $current_statut;
                                        ?>
                                        <div class="flex items-center space-x-3">
                                            <div class="w-3 h-3 <?php echo $is_completed ? 'bg-green-500' : 'bg-gray-300'; ?> rounded-full <?php echo $is_current ? 'ring-4 ring-green-200' : ''; ?>"></div>
                                            <div class="flex-1">
                                                <p class="text-sm <?php echo $is_completed ? 'font-semibold text-dark' : 'text-gray-500'; ?>">
                                                    <?php echo $item['label']; ?>
                                                </p>
                                                <?php if($is_completed && $item['status'] == 'en_attente'): ?>
                                                <p class="text-xs text-gray-500"><?php echo date('d/m/Y H:i', strtotime($commande['created_at'])); ?></p>
                                                <?php elseif($is_current && $current_statut != 'en_attente'): ?>
                                                <p class="text-xs text-green-600">Statut actuel</p>
                                                <?php else: ?>
                                                <p class="text-xs text-gray-400">En attente</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                        
                                        <?php if($current_statut == 'annulee'): ?>
                                        <div class="flex items-center space-x-3">
                                            <div class="w-3 h-3 bg-red-500 rounded-full ring-4 ring-red-200"></div>
                                            <div>
                                                <p class="text-sm font-semibold text-red-600">Commande annulée</p>
                                                <p class="text-xs text-red-500">Statut final</p>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden no-print">
                                <div class="bg-gradient-to-r from-warning to-red-500 px-4 py-3">
                                    <h4 class="text-lg font-bold text-white flex items-center space-x-2">
                                        <i class="fas fa-bolt text-white"></i>
                                        <span>Actions</span>
                                    </h4>
                                </div>
                                
                                <div class="p-4 space-y-3">
                                    <a href="commandes.php" 
                                       class="w-full flex items-center space-x-2 bg-gray-100 hover:bg-gray-200 text-dark font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-arrow-left text-secondary"></i>
                                        <span>Retour aux commandes</span>
                                    </a>
                                    
                                    <?php if($commande['email']): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($commande['email']); ?>" 
                                       class="w-full flex items-center space-x-2 bg-primary hover:bg-cyan-400 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-envelope"></i>
                                        <span>Contacter le client</span>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <button onclick="window.print()" 
                                            class="w-full flex items-center space-x-2 bg-accent hover:bg-yellow-500 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-print"></i>
                                        <span>Imprimer</span>
                                    </button>
                                    
                                    <?php if($current_statut != 'livree' && $current_statut != 'annulee'): ?>
                                    <a href="commandes.php" 
                                       class="w-full flex items-center space-x-2 bg-secondary hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-edit"></i>
                                        <span>Modifier statut</span>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Contact Info -->
                            <?php if($commande['telephone'] || $commande['email']): ?>
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden no-print">
                                <div class="bg-gradient-to-r from-green-400 to-green-600 px-4 py-3">
                                    <h4 class="text-lg font-bold text-white flex items-center space-x-2">
                                        <i class="fas fa-phone text-white"></i>
                                        <span>Contact rapide</span>
                                    </h4>
                                </div>
                                
                                <div class="p-4 space-y-3">
                                    <?php if($commande['telephone']): ?>
                                    <a href="tel:<?php echo htmlspecialchars($commande['telephone']); ?>" 
                                       class="w-full flex items-center space-x-2 bg-green-100 hover:bg-green-200 text-green-800 font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-phone"></i>
                                        <span><?php echo htmlspecialchars($commande['telephone']); ?></span>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if($commande['email']): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($commande['email']); ?>" 
                                       class="w-full flex items-center space-x-2 bg-blue-100 hover:bg-blue-200 text-blue-800 font-medium py-2 px-4 rounded-lg transition-colors duration-200 overflow-hidden">
                                        <i class="fas fa-envelope flex-shrink-0"></i>
                                        <span class="truncate"><?php echo htmlspecialchars($commande['email']); ?></span>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeSidebarButton = document.getElementById('close-sidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-menu-overlay');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        mobileMenuButton.addEventListener('click', openSidebar);
        closeSidebarButton.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);

        // Close sidebar on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                closeSidebar();
            }
        });
    </script>
</body>
</html>