<?php
require_once '../config.php';

if(!isAdmin()) {
    redirect('login.php');
}

// Récupérer les statistiques
// Total des produits
$stmt = $pdo->query("SELECT COUNT(*) as total FROM produits");
$total_produits = $stmt->fetch()['total'];

// Total des commandes
$stmt = $pdo->query("SELECT COUNT(*) as total FROM commandes");
$total_commandes = $stmt->fetch()['total'];

// Commandes en attente
$stmt = $pdo->query("SELECT COUNT(*) as total FROM commandes WHERE statut = 'en_attente'");
$commandes_attente = $stmt->fetch()['total'];

// Chiffre d'affaires (toutes les commandes sauf annulées)
$stmt = $pdo->query("SELECT SUM(total) as total FROM commandes WHERE statut != 'annulee'");
$chiffre_affaires = $stmt->fetch()['total'] ?: 0;

// Chiffre d'affaires du mois en cours
$stmt = $pdo->query("SELECT SUM(total) as total FROM commandes WHERE statut != 'annulee' AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
$ca_mois = $stmt->fetch()['total'] ?: 0;

// Chiffre d'affaires du mois précédent
$stmt = $pdo->query("SELECT SUM(total) as total FROM commandes WHERE statut != 'annulee' AND MONTH(created_at) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))");
$ca_mois_precedent = $stmt->fetch()['total'] ?: 1; // Éviter division par zéro

// Calculer le pourcentage de croissance
$croissance = $ca_mois_precedent > 0 ? round((($ca_mois - $ca_mois_precedent) / $ca_mois_precedent) * 100, 1) : 0;

// Récupérer les 5 dernières commandes
$stmt = $pdo->query("SELECT * FROM commandes ORDER BY created_at DESC LIMIT 5");
$dernieres_commandes = $stmt->fetchAll();

// Statistiques supplémentaires
$stmt = $pdo->query("SELECT COUNT(*) as total FROM produits WHERE stock <= 5");
$produits_stock_faible = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM produits WHERE stock = 0");
$produits_rupture = $stmt->fetch()['total'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Administration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#55D5E0',
                        'secondary': '#335F8A',
                        'dark': '#2F4558',
                        'accent': '#F6B12D',
                        'danger': '#F26619'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Mobile menu overlay -->
        <div id="mobile-menu-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>
        
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-dark shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
            <div class="flex items-center justify-between p-6 lg:justify-center">
                <div class="flex items-center">
                    <i class="fas fa-gem text-accent text-2xl mr-3"></i>
                    <h1 class="text-xl font-bold text-white">FM-Cakes</h1>
                </div>
                <!-- Close button for mobile -->
                <button id="close-sidebar" class="lg:hidden text-white hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <nav class="px-6 pb-6 space-y-2">
                <a href="index.php" class="flex items-center px-4 py-3 bg-secondary text-white rounded-lg">
                    <i class="fas fa-dashboard mr-3"></i>
                    <span class="font-medium">Tableau de bord</span>
                </a>
                <a href="gestion_clients.php" class="flex items-center px-4 py-3 text-gray-300 hover:bg-secondary hover:text-white rounded-lg transition duration-200">
                    <i class="fas fa-users mr-3"></i>
                    <span class="font-medium">Clients</span>
                <a href="produits.php" class="flex items-center px-4 py-3 text-gray-300 hover:bg-secondary hover:text-white rounded-lg transition duration-200">
                    <i class="fas fa-gem mr-3"></i>
                    <span class="font-medium">Produits</span>
                </a>
                <a href="commandes.php" class="flex items-center px-4 py-3 text-gray-300 hover:bg-secondary hover:text-white rounded-lg transition duration-200">
                    <i class="fas fa-shopping-cart mr-3"></i>
                    <span class="font-medium">Commandes</span>
                </a>
                <a href="rapport.php" class="flex items-center px-4 py-3 text-gray-300 hover:bg-secondary hover:text-white rounded-lg transition duration-200">
                    <i class="fas fa-chart-bar mr-3"></i>
                    <span class="font-medium">Rapports</span>   
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
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button id="mobile-menu-button" class="lg:hidden mr-4 text-gray-600 hover:text-gray-900">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h2 class="text-xl sm:text-2xl font-bold text-dark">Tableau de bord</h2>
                    </div>
                    <div class="flex items-center">
                        <span class="hidden sm:block text-gray-600 mr-4 text-sm sm:text-base">
                            <i class="fas fa-calendar mr-2 text-primary"></i>
                            <?php echo date('d F Y'); ?>
                        </span>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-primary rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-sm sm:text-base"></i>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <!-- Welcome Message -->
                <div class="mb-6 bg-gradient-to-r from-primary to-secondary rounded-xl p-6 text-white">
                    <h3 class="text-2xl font-bold mb-2">Bienvenue dans votre espace admin !</h3>
                    <p class="opacity-90">Gérez vos produits, suivez vos commandes et analysez vos performances.</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
                    <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border-l-4 border-primary hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-gray-600 text-xs sm:text-sm truncate">Total Produits</p>
                                <h3 class="text-2xl sm:text-3xl font-bold text-dark mt-1"><?php echo $total_produits; ?></h3>
                                <?php if($produits_stock_faible > 0): ?>
                                <p class="text-orange-600 text-xs mt-1">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <?php echo $produits_stock_faible; ?> en stock faible
                                </p>
                                <?php endif; ?>
                            </div>
                            <div class="bg-primary/10 p-2 sm:p-3 rounded-lg ml-3">
                                <i class="fas fa-gem text-primary text-lg sm:text-2xl"></i>
                            </div>
                        </div>
                        <a href="produits.php" class="text-primary text-xs sm:text-sm mt-3 inline-block hover:underline">
                            Voir tous →
                        </a>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border-l-4 border-secondary hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-gray-600 text-xs sm:text-sm truncate">Total Commandes</p>
                                <h3 class="text-2xl sm:text-3xl font-bold text-dark mt-1"><?php echo $total_commandes; ?></h3>
                            </div>
                            <div class="bg-secondary/10 p-2 sm:p-3 rounded-lg ml-3">
                                <i class="fas fa-shopping-cart text-secondary text-lg sm:text-2xl"></i>
                            </div>
                        </div>
                        <a href="commandes.php" class="text-secondary text-xs sm:text-sm mt-3 inline-block hover:underline">
                            Voir toutes →
                        </a>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border-l-4 border-accent hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-gray-600 text-xs sm:text-sm truncate">En attente</p>
                                <h3 class="text-2xl sm:text-3xl font-bold text-dark mt-1"><?php echo $commandes_attente; ?></h3>
                            </div>
                            <div class="bg-accent/10 p-2 sm:p-3 rounded-lg ml-3">
                                <i class="fas fa-clock text-accent text-lg sm:text-2xl"></i>
                            </div>
                        </div>
                        <?php if($commandes_attente > 0): ?>
                        <p class="text-accent text-xs sm:text-sm mt-3">À traiter rapidement</p>
                        <?php else: ?>
                        <p class="text-green-600 text-xs sm:text-sm mt-3">Tout est à jour !</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border-l-4 border-danger hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-gray-600 text-xs sm:text-sm truncate">Chiffre d'affaires</p>
                                <h3 class="text-lg sm:text-2xl font-bold text-dark mt-1">
                                    <?php echo number_format($chiffre_affaires, 0, ',', ' '); ?> CFA
                                </h3>
                            </div>
                            <div class="bg-danger/10 p-2 sm:p-3 rounded-lg ml-3">
                                <i class="fas fa-coins text-danger text-lg sm:text-2xl"></i>
                            </div>
                        </div>
                        <p class="<?php echo $croissance >= 0 ? 'text-green-600' : 'text-red-600'; ?> text-xs sm:text-sm mt-3">
                            <i class="fas fa-arrow-<?php echo $croissance >= 0 ? 'up' : 'down'; ?> mr-1"></i>
                            <?php echo abs($croissance); ?>% ce mois
                        </p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <a href="ajouter_produit.php" class="bg-primary text-white rounded-lg p-4 text-center hover:bg-cyan-500 transition-colors">
                        <i class="fas fa-plus-circle text-2xl mb-2"></i>
                        <p class="font-semibold">Ajouter un produit</p>
                    </a>
                    <a href="commandes.php?statut=en_attente" class="bg-accent text-white rounded-lg p-4 text-center hover:bg-yellow-500 transition-colors">
                        <i class="fas fa-hourglass-half text-2xl mb-2"></i>
                        <p class="font-semibold">Commandes en attente</p>
                    </a>
                    <a href="produits.php?stock=faible" class="bg-danger text-white rounded-lg p-4 text-center hover:bg-orange-600 transition-colors">
                        <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                        <p class="font-semibold">Stock faible</p>
                    </a>
                    <a href="../index.php" target="_blank" class="bg-secondary text-white rounded-lg p-4 text-center hover:bg-blue-800 transition-colors">
                        <i class="fas fa-external-link-alt text-2xl mb-2"></i>
                        <p class="font-semibold">Voir le site</p>
                    </a>
                </div>
                
                <!-- Recent Orders -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="p-4 sm:p-6 border-b border-gray-200 bg-gradient-to-r from-secondary to-dark">
                        <h3 class="text-lg sm:text-xl font-bold text-white">
                            <i class="fas fa-clock mr-2"></i>Dernières commandes
                        </h3>
                    </div>
                    
                    <?php if(empty($dernieres_commandes)): ?>
                    <div class="p-8 text-center">
                        <i class="fas fa-inbox text-gray-300 text-5xl mb-3"></i>
                        <p class="text-gray-500">Aucune commande pour le moment</p>
                    </div>
                    <?php else: ?>
                    <!-- Desktop Table -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Commande</th>
                                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach($dernieres_commandes as $commande): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium text-primary">
                                        #<?php echo str_pad($commande['id'], 6, '0', STR_PAD_LEFT); ?>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($commande['nom_client']); ?>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('d/m/Y', strtotime($commande['created_at'])); ?>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        <?php echo number_format($commande['total'], 0, ',', ' '); ?> CFA
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $statut_colors = [
                                            'en_attente' => 'bg-yellow-100 text-yellow-800',
                                            'confirmee' => 'bg-blue-100 text-blue-800',
                                            'expediee' => 'bg-purple-100 text-purple-800',
                                            'livree' => 'bg-green-100 text-green-800',
                                            'annulee' => 'bg-red-100 text-red-800'
                                        ];
                                        $statut_labels = [
                                            'en_attente' => 'En attente',
                                            'confirmee' => 'Confirmée',
                                            'expediee' => 'Expédiée',
                                            'livree' => 'Livrée',
                                            'annulee' => 'Annulée'
                                        ];
                                        $color = $statut_colors[$commande['statut']] ?? 'bg-gray-100 text-gray-800';
                                        $label = $statut_labels[$commande['statut']] ?? $commande['statut'];
                                        ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                                            <?php echo $label; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="details_commande.php?id=<?php echo $commande['id']; ?>" 
                                           class="text-primary hover:text-secondary transition-colors">
                                            <i class="fas fa-eye mr-1"></i>Voir
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Mobile Cards -->
                    <div class="sm:hidden">
                        <div class="divide-y divide-gray-200">
                            <?php foreach($dernieres_commandes as $commande): ?>
                            <div class="p-4 hover:bg-gray-50">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-primary">
                                        #<?php echo str_pad($commande['id'], 6, '0', STR_PAD_LEFT); ?>
                                    </span>
                                    <?php
                                    $statut_colors = [
                                        'en_attente' => 'bg-yellow-100 text-yellow-800',
                                        'confirmee' => 'bg-blue-100 text-blue-800',
                                        'expediee' => 'bg-purple-100 text-purple-800',
                                        'livree' => 'bg-green-100 text-green-800',
                                        'annulee' => 'bg-red-100 text-red-800'
                                    ];
                                    $statut_labels = [
                                        'en_attente' => 'En attente',
                                        'confirmee' => 'Confirmée',
                                        'expediee' => 'Expédiée',
                                        'livree' => 'Livrée',
                                        'annulee' => 'Annulée'
                                    ];
                                    $color = $statut_colors[$commande['statut']] ?? 'bg-gray-100 text-gray-800';
                                    $label = $statut_labels[$commande['statut']] ?? $commande['statut'];
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                                        <?php echo $label; ?>
                                    </span>
                                </div>
                                <div class="text-sm text-gray-900 font-medium mb-1">
                                    <?php echo htmlspecialchars($commande['nom_client']); ?>
                                </div>
                                <div class="flex justify-between items-center text-sm text-gray-500">
                                    <span><?php echo date('d/m/Y', strtotime($commande['created_at'])); ?></span>
                                    <span class="font-semibold text-gray-900">
                                        <?php echo number_format($commande['total'], 0, ',', ' '); ?> CFA
                                    </span>
                                </div>
                                <a href="details_commande.php?id=<?php echo $commande['id']; ?>" 
                                   class="text-primary hover:text-secondary text-sm mt-2 inline-block">
                                    <i class="fas fa-eye mr-1"></i>Voir détails
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="p-4 sm:p-6 border-t border-gray-200 bg-gray-50">
                        <a href="commandes.php" class="text-primary hover:underline text-sm font-semibold">
                            <i class="fas fa-arrow-right mr-1"></i>Voir toutes les commandes
                        </a>
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