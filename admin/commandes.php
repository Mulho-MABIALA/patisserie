<?php
require_once '../config.php';

if(!isAdmin()) {
    redirect('login.php');
}

// Mettre à jour le statut d'une commande
if(isset($_POST['update_status']) && isset($_POST['commande_id']) && isset($_POST['statut'])) {
    $commande_id = (int)$_POST['commande_id'];
    $nouveau_statut = $_POST['statut'];
    
    // Vérifier que le statut est valide
    $statuts_valides = ['en_attente', 'confirmee', 'expediee', 'livree', 'annulee'];
    if(in_array($nouveau_statut, $statuts_valides)) {
        $stmt = $pdo->prepare("UPDATE commandes SET statut = ? WHERE id = ?");
        $stmt->execute([$nouveau_statut, $commande_id]);
    }
    
    // Rediriger pour éviter la resoumission du formulaire
    redirect('commandes.php');
}

// Récupérer les statistiques
$stats = [
    'total' => 0,
    'en_attente' => 0,
    'confirmee' => 0,
    'expediee' => 0,
    'livree' => 0,
    'annulee' => 0,
    'chiffre_affaires' => 0
];

// Total des commandes
$stmt = $pdo->query("SELECT COUNT(*) as total FROM commandes");
$stats['total'] = $stmt->fetch()['total'];

// Commandes par statut
$statuts = ['en_attente', 'confirmee', 'expediee', 'livree', 'annulee'];
foreach($statuts as $statut) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM commandes WHERE statut = ?");
    $stmt->execute([$statut]);
    $stats[$statut] = $stmt->fetch()['total'];
}

// Chiffre d'affaires (toutes les commandes sauf annulées)
$stmt = $pdo->query("SELECT SUM(total) as total FROM commandes WHERE statut != 'annulee'");
$stats['chiffre_affaires'] = $stmt->fetch()['total'] ?: 0;

// Récupérer toutes les commandes
$stmt = $pdo->query("SELECT * FROM commandes ORDER BY created_at DESC");
$commandes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des commandes - Administration</title>
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
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Mobile menu overlay -->
        <div id="mobile-menu-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>
        
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-dark shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
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
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button id="mobile-menu-button" class="lg:hidden mr-4 text-gray-600 hover:text-gray-900">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-shopping-cart text-primary text-xl sm:text-2xl"></i>
                            <h2 class="text-lg sm:text-2xl font-bold text-dark">Gestion des commandes</h2>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="hidden sm:flex items-center space-x-2 bg-gray-100 px-3 py-2 rounded-lg">
                            <i class="fas fa-clock text-primary text-sm"></i>
                            <span class="text-xs text-dark font-medium">Mise à jour: <?php echo date('H:i'); ?></span>
                        </div>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-primary rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-sm sm:text-base"></i>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Breadcrumb -->
            <div class="px-4 sm:px-6 lg:px-8 py-4 bg-gray-50 border-b border-gray-200">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm text-gray-500">
                        <li>
                            <a href="index.php" class="hover:text-primary transition-colors duration-200">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>
                        <li><i class="fas fa-chevron-right text-gray-300"></i></li>
                        <li class="text-dark font-medium">Commandes</li>
                    </ol>
                </nav>
            </div>
            
            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    <!-- Page Description -->
                    <div class="mb-6 sm:mb-8">
                        <p class="text-gray-600 text-sm sm:text-base">Suivez et gérez toutes vos commandes en temps réel</p>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="mb-6 sm:mb-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3 sm:gap-4">
                        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 border-l-4 border-primary">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-gray-600 truncate">Total</p>
                                    <p class="text-lg font-bold text-dark"><?php echo $stats['total']; ?></p>
                                </div>
                                <i class="fas fa-list text-primary text-sm sm:text-lg"></i>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 border-l-4 border-yellow-400">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-gray-600 truncate">En attente</p>
                                    <p class="text-lg font-bold text-yellow-600"><?php echo $stats['en_attente']; ?></p>
                                </div>
                                <i class="fas fa-hourglass-half text-yellow-500 text-sm sm:text-lg"></i>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 border-l-4 border-blue-400">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-gray-600 truncate">Confirmées</p>
                                    <p class="text-lg font-bold text-blue-600"><?php echo $stats['confirmee']; ?></p>
                                </div>
                                <i class="fas fa-check text-blue-500 text-sm sm:text-lg"></i>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 border-l-4 border-purple-400">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-gray-600 truncate">Expédiées</p>
                                    <p class="text-lg font-bold text-purple-600"><?php echo $stats['expediee']; ?></p>
                                </div>
                                <i class="fas fa-truck text-purple-500 text-sm sm:text-lg"></i>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 border-l-4 border-green-400">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-gray-600 truncate">Livrées</p>
                                    <p class="text-lg font-bold text-green-600"><?php echo $stats['livree']; ?></p>
                                </div>
                                <i class="fas fa-check-double text-green-500 text-sm sm:text-lg"></i>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 border-l-4 border-red-400">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-gray-600 truncate">Annulées</p>
                                    <p class="text-lg font-bold text-red-600"><?php echo $stats['annulee']; ?></p>
                                </div>
                                <i class="fas fa-times text-red-500 text-sm sm:text-lg"></i>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 border-l-4 border-accent col-span-2 sm:col-span-1">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-gray-600 truncate">CA Total</p>
                                    <p class="text-sm sm:text-lg font-bold text-accent"><?php echo number_format($stats['chiffre_affaires'], 0, ',', ' '); ?> €</p>
                                </div>
                                <i class="fas fa-coins text-accent text-sm sm:text-lg"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Orders Table -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <!-- Table Header for mobile -->
                        <div class="bg-gradient-to-r from-secondary to-dark p-4 sm:hidden">
                            <h3 class="text-white font-semibold text-lg">Liste des commandes</h3>
                        </div>
                        
                        <!-- Desktop Table -->
                        <div class="hidden lg:block overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gradient-to-r from-secondary to-dark">
                                    <tr>
                                        <th class="px-4 xl:px-6 py-4 text-left text-white font-semibold">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-hashtag text-primary"></i>
                                                <span>N° Commande</span>
                                            </div>
                                        </th>
                                        <th class="px-4 xl:px-6 py-4 text-left text-white font-semibold">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-calendar text-primary"></i>
                                                <span>Date</span>
                                            </div>
                                        </th>
                                        <th class="px-4 xl:px-6 py-4 text-left text-white font-semibold">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-user text-primary"></i>
                                                <span>Client</span>
                                            </div>
                                        </th>
                                        <th class="px-4 xl:px-6 py-4 text-left text-white font-semibold">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-envelope text-primary"></i>
                                                <span>Email</span>
                                            </div>
                                        </th>
                                        <th class="px-4 xl:px-6 py-4 text-left text-white font-semibold">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-coins text-primary"></i>
                                                <span>Total</span>
                                            </div>
                                        </th>
                                        <th class="px-4 xl:px-6 py-4 text-left text-white font-semibold">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-flag text-primary"></i>
                                                <span>Statut</span>
                                            </div>
                                        </th>
                                        <th class="px-4 xl:px-6 py-4 text-left text-white font-semibold">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-cog text-primary"></i>
                                                <span>Actions</span>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach($commandes as $index => $commande): ?>
                                    <tr class="hover:bg-gray-50 transition-colors duration-150 <?php echo $index % 2 == 0 ? 'bg-gray-25' : 'bg-white'; ?>">
                                        <td class="px-4 xl:px-6 py-4">
                                            <span class="text-sm font-bold text-dark">#<?php echo str_pad($commande['id'], 6, '0', STR_PAD_LEFT); ?></span>
                                        </td>
                                        <td class="px-4 xl:px-6 py-4">
                                            <div class="text-sm text-dark">
                                                <div class="font-medium"><?php echo date('d/m/Y', strtotime($commande['created_at'])); ?></div>
                                                <div class="text-gray-500"><?php echo date('H:i', strtotime($commande['created_at'])); ?></div>
                                            </div>
                                        </td>
                                        <td class="px-4 xl:px-6 py-4">
                                            <div class="text-sm font-semibold text-dark"><?php echo htmlspecialchars($commande['nom_client']); ?></div>
                                            <?php if(isset($commande['telephone']) && $commande['telephone']): ?>
                                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($commande['telephone']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 xl:px-6 py-4">
                                            <a href="mailto:<?php echo htmlspecialchars($commande['email']); ?>" class="text-sm text-secondary hover:text-primary transition-colors duration-200">
                                                <?php echo htmlspecialchars($commande['email']); ?>
                                            </a>
                                        </td>
                                        <td class="px-9 xl:px-6 py-4">
                                            <span class="inline-flex items-center px-5 py-2 text-sm font-medium text-white bg-accent hover:bg-blue-600 rounded-lg transition-colors duration-200">
                                                <?php echo number_format($commande['total'], 2, ',', ' '); ?> €
                                            </span>
                                        </td>
                                        <td class="px-4 xl:px-6 py-4">
                                            <form method="POST" class="inline-block">
                                                <input type="hidden" name="commande_id" value="<?php echo $commande['id']; ?>">
                                                <input type="hidden" name="update_status" value="1">
                                                <div class="flex items-center space-x-2">
                                                    <select name="statut" onchange="this.form.submit()" 
                                                            class="text-sm rounded-lg border-2 focus:ring-2 focus:border-transparent transition-colors duration-200 px-3 py-1
                                                            <?php 
                                                            switch($commande['statut']) {
                                                                case 'en_attente': echo 'border-yellow-300 text-yellow-700 bg-yellow-50 focus:ring-yellow-500'; break;
                                                                case 'confirmee': echo 'border-blue-300 text-blue-700 bg-blue-50 focus:ring-blue-500'; break;
                                                                case 'expediee': echo 'border-purple-300 text-purple-700 bg-purple-50 focus:ring-purple-500'; break;
                                                                case 'livree': echo 'border-green-300 text-green-700 bg-green-50 focus:ring-green-500'; break;
                                                                case 'annulee': echo 'border-red-300 text-red-700 bg-red-50 focus:ring-red-500'; break;
                                                            }
                                                            ?>">
                                                        <option value="en_attente" <?php echo $commande['statut'] == 'en_attente' ? 'selected' : ''; ?>>⏳ En attente</option>
                                                        <option value="confirmee" <?php echo $commande['statut'] == 'confirmee' ? 'selected' : ''; ?>>✅ Confirmée</option>
                                                        <option value="expediee" <?php echo $commande['statut'] == 'expediee' ? 'selected' : ''; ?>>🚚 Expédiée</option>
                                                        <option value="livree" <?php echo $commande['statut'] == 'livree' ? 'selected' : ''; ?>>📦 Livrée</option>
                                                        <option value="annulee" <?php echo $commande['statut'] == 'annulee' ? 'selected' : ''; ?>>❌ Annulée</option>
                                                    </select>
                                                </div>
                                            </form>
                                        </td>
                                        <td class="px-4 xl:px-6 py-4">
                                            <a href="details_commande.php?id=<?php echo $commande['id']; ?>" 
                                               class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-secondary hover:bg-blue-600 rounded-lg transition-colors duration-200">
                                                <i class="fas fa-eye mr-1"></i>
                                                Détails
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Mobile & Tablet Cards -->
                        <div class="lg:hidden divide-y divide-gray-200">
                            <?php foreach($commandes as $commande): ?>
                            <div class="p-4 space-y-4">
                                <!-- Header -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs font-medium text-gray-500">Commande:</span>
                                        <span class="text-sm font-bold text-dark">#<?php echo str_pad($commande['id'], 6, '0', STR_PAD_LEFT); ?></span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs text-gray-500"><?php echo date('d/m/Y', strtotime($commande['created_at'])); ?></div>
                                        <div class="text-xs text-gray-400"><?php echo date('H:i', strtotime($commande['created_at'])); ?></div>
                                    </div>
                                </div>
                                
                                <!-- Client Info -->
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-user text-secondary"></i>
                                        <span class="font-semibold text-dark"><?php echo htmlspecialchars($commande['nom_client']); ?></span>
                                    </div>
                                    <?php if(isset($commande['telephone']) && $commande['telephone']): ?>
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-phone text-secondary"></i>
                                        <span class="text-sm text-gray-600"><?php echo htmlspecialchars($commande['telephone']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-envelope text-secondary"></i>
                                        <a href="mailto:<?php echo htmlspecialchars($commande['email']); ?>" 
                                           class="text-sm text-secondary hover:text-primary transition-colors duration-200">
                                            <?php echo htmlspecialchars($commande['email']); ?>
                                        </a>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-coins text-accent"></i>
                                        <span class="font-bold text-accent text-lg"><?php echo number_format($commande['total'], 2, ',', ' '); ?> €</span>
                                    </div>
                                </div>
                                
                                <!-- Status & Actions -->
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-3 border-t border-gray-200">
                                    <form method="POST" class="flex-1">
                                        <input type="hidden" name="commande_id" value="<?php echo $commande['id']; ?>">
                                        <input type="hidden" name="update_status" value="1">
                                        <select name="statut" onchange="this.form.submit()" 
                                                class="w-full text-sm rounded-lg border-2 focus:ring-2 focus:border-transparent transition-colors duration-200 px-3 py-2
                                                <?php 
                                                switch($commande['statut']) {
                                                    case 'en_attente': echo 'border-yellow-300 text-yellow-700 bg-yellow-50 focus:ring-yellow-500'; break;
                                                    case 'confirmee': echo 'border-blue-300 text-blue-700 bg-blue-50 focus:ring-blue-500'; break;
                                                    case 'expediee': echo 'border-purple-300 text-purple-700 bg-purple-50 focus:ring-purple-500'; break;
                                                    case 'livree': echo 'border-green-300 text-green-700 bg-green-50 focus:ring-green-500'; break;
                                                    case 'annulee': echo 'border-red-300 text-red-700 bg-red-50 focus:ring-red-500'; break;
                                                }
                                                ?>">
                                            <option value="en_attente" <?php echo $commande['statut'] == 'en_attente' ? 'selected' : ''; ?>>⏳ En attente</option>
                                            <option value="confirmee" <?php echo $commande['statut'] == 'confirmee' ? 'selected' : ''; ?>>✅ Confirmée</option>
                                            <option value="expediee" <?php echo $commande['statut'] == 'expediee' ? 'selected' : ''; ?>>🚚 Expédiée</option>
                                            <option value="livree" <?php echo $commande['statut'] == 'livree' ? 'selected' : ''; ?>>📦 Livrée</option>
                                            <option value="annulee" <?php echo $commande['statut'] == 'annulee' ? 'selected' : ''; ?>>❌ Annulée</option>
                                        </select>
                                    </form>
                                    
                                    <a href="details_commande.php?id=<?php echo $commande['id']; ?>" 
                                       class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-secondary hover:bg-blue-600 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-eye mr-2"></i>
                                        Voir détails
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if(empty($commandes)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-shopping-cart text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune commande trouvée</h3>
                            <p class="text-gray-500 mb-6">Les commandes apparaîtront ici une fois passées</p>
                        </div>
                        <?php endif; ?>
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