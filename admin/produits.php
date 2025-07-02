<?php
require_once '../config.php';

if(!isAdmin()) {
    redirect('login.php');
}

// Supprimer un produit
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Récupérer le nom de l'image avant suppression
    $stmt = $pdo->prepare("SELECT image FROM produits WHERE id = ?");
    $stmt->execute([$id]);
    $produit = $stmt->fetch();
    
    // Supprimer le produit
    $stmt = $pdo->prepare("DELETE FROM produits WHERE id = ?");
    if($stmt->execute([$id])) {
        // Supprimer l'image si elle existe
        if($produit && $produit['image'] && file_exists('../uploads/' . $produit['image'])) {
            unlink('../uploads/' . $produit['image']);
        }
    }
    
    redirect('produits.php');
}

// Récupérer tous les produits
$stmt = $pdo->query("SELECT * FROM produits ORDER BY created_at DESC");
$produits = $stmt->fetchAll();

// Calculer les statistiques
$total_produits = count($produits);
$stock_total = array_sum(array_column($produits, 'stock'));
$valeur_stock = array_sum(array_map(function($p) { return $p['prix'] * $p['stock']; }, $produits));
$stock_faible = count(array_filter($produits, function($p) { return $p['stock'] <= 5; }));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des produits - Administration</title>
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
                <a href="gestion_clients.php" class="flex items
                    px-4 py-3 text-gray-300 hover:bg-secondary hover:text-white rounded-lg transition duration-200">
                    <i class="fas fa-users mr-3"></i>
                    <span class="font-medium">Clients</span>
                </a>
                <a href="produits.php" class="flex items-center px-4 py-3 bg-secondary text-white rounded-lg">
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
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-gem text-primary text-xl sm:text-2xl"></i>
                            <h2 class="text-lg sm:text-2xl font-bold text-dark">Gestion des produits</h2>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="ajouter_produit.php" class="hidden sm:inline-flex items-center space-x-2 bg-primary hover:bg-cyan-400 text-white font-semibold px-4 py-2 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                            <i class="fas fa-plus"></i>
                            <span>Ajouter</span>
                        </a>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-primary rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-sm sm:text-base"></i>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Breadcrumb -->
            <div class="px-4 sm:px-6 lg:px-8 py-4 bg-gray-50 border-b border-gray-200">
                <nav class="flex justify-between items-center" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm text-gray-500">
                        <li>
                            <a href="index.php" class="hover:text-primary transition-colors duration-200">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>
                        <li><i class="fas fa-chevron-right text-gray-300"></i></li>
                        <li class="text-dark font-medium">Produits</li>
                    </ol>
                    <a href="ajouter_produit.php" class="sm:hidden inline-flex items-center space-x-2 bg-primary hover:bg-cyan-400 text-white font-semibold px-3 py-2 rounded-lg text-sm">
                        <i class="fas fa-plus"></i>
                        <span>Ajouter</span>
                    </a>
                </nav>
            </div>
            
            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    <!-- Page Description -->
                    <div class="mb-6 sm:mb-8">
                        <p class="text-gray-600 text-sm sm:text-base">Gérez votre catalogue de produits et surveillez vos stocks</p>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="mb-6 sm:mb-8 grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
                        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-6 border-l-4 border-primary">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Total Produits</p>
                                    <p class="text-lg sm:text-2xl font-bold text-dark"><?php echo $total_produits; ?></p>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-2 sm:p-3 rounded-full ml-2">
                                    <i class="fas fa-gem text-primary text-sm sm:text-xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-6 border-l-4 border-accent">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Stock Total</p>
                                    <p class="text-lg sm:text-2xl font-bold text-dark"><?php echo $stock_total; ?></p>
                                </div>
                                <div class="bg-accent bg-opacity-10 p-2 sm:p-3 rounded-full ml-2">
                                    <i class="fas fa-cubes text-accent text-sm sm:text-xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-6 border-l-4 border-secondary col-span-2 sm:col-span-1">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Valeur Stock</p>
                                    <p class="text-base sm:text-2xl font-bold text-dark"><?php echo number_format($valeur_stock, 0, ',', ' '); ?> €</p>
                                </div>
                                <div class="bg-secondary bg-opacity-10 p-2 sm:p-3 rounded-full ml-2">
                                    <i class="fas fa-coins text-secondary text-sm sm:text-xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-6 border-l-4 border-warning col-span-2 sm:col-span-1">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Stock Faible</p>
                                    <p class="text-lg sm:text-2xl font-bold text-dark"><?php echo $stock_faible; ?></p>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-2 sm:p-3 rounded-full ml-2">
                                    <i class="fas fa-exclamation-triangle text-warning text-sm sm:text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Products Table -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <!-- Table Header for mobile -->
                        <div class="bg-gradient-to-r from-secondary to-dark p-4 sm:hidden">
                            <h3 class="text-white font-semibold text-lg">Liste des produits</h3>
                        </div>
                        
                        <!-- Desktop Table -->
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gradient-to-r from-secondary to-dark">
                                    <tr>
                                        <th class="px-4 lg:px-6 py-4 text-left text-white font-semibold">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-hashtag text-primary"></i>
                                                <span>ID</span>
                                            </div>
                                        </th>
                                        <th class="px-4 lg:px-6 py-4 text-left text-white font-semibold">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-image text-primary"></i>
                                                <span>Image</span>
                                            </div>
                                        </th>
                                        <th class="px-4 lg:px-6 py-4 text-left text-white font-semibold">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-tag text-primary"></i>
                                                <span>Nom</span>
                                            </div>
                                        </th>
                                        <th class="px-4 lg:px-6 py-4 text-left text-white font-semibold">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-coins text-primary"></i>
                                                <span>Prix</span>
                                            </div>
                                        </th>
                                        <th class="px-4 lg:px-6 py-4 text-left text-white font-semibold">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-cubes text-primary"></i>
                                                <span>Stock</span>
                                            </div>
                                        </th>
                                        <th class="px-4 lg:px-6 py-4 text-left text-white font-semibold">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-calendar text-primary"></i>
                                                <span>Créé le</span>
                                            </div>
                                        </th>
                                        <th class="px-4 lg:px-6 py-4 text-left text-white font-semibold">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-cog text-primary"></i>
                                                <span>Actions</span>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach($produits as $index => $produit): ?>
                                    <tr class="hover:bg-gray-50 transition-colors duration-150 <?php echo $index % 2 == 0 ? 'bg-gray-25' : 'bg-white'; ?>">
                                        <td class="px-4 lg:px-6 py-4 text-sm font-medium text-dark">
                                            #<?php echo str_pad($produit['id'], 3, '0', STR_PAD_LEFT); ?>
                                        </td>
                                        <td class="px-4 lg:px-6 py-4">
                                            <?php if($produit['image'] && file_exists('../uploads/' . $produit['image'])): ?>
                                                <img src="../uploads/<?php echo htmlspecialchars($produit['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($produit['nom']); ?>" 
                                                     class="w-12 h-12 object-cover rounded-lg">
                                            <?php else: ?>
                                                <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 lg:px-6 py-4">
                                            <div class="text-sm font-semibold text-dark"><?php echo htmlspecialchars($produit['nom']); ?></div>
                                            <?php if(isset($produit['description']) && $produit['description']): ?>
                                            <div class="text-xs text-gray-500 truncate max-w-xs"><?php echo htmlspecialchars(substr($produit['description'], 0, 50)) . (strlen($produit['description']) > 50 ? '...' : ''); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 lg:px-6 py-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-accent text-white">
                                                <?php echo number_format($produit['prix'], 2, ',', ' '); ?> €
                                            </span>
                                        </td>
                                        <td class="px-4 lg:px-6 py-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold 
                                                <?php 
                                                if($produit['stock'] == 0) {
                                                    echo 'bg-red-100 text-red-800';
                                                } elseif($produit['stock'] <= 5) {
                                                    echo 'bg-yellow-100 text-yellow-800';
                                                } else {
                                                    echo 'bg-green-100 text-green-800';
                                                }
                                                ?>">
                                                <?php echo $produit['stock']; ?>
                                            </span>
                                        </td>
                                        <td class="px-4 lg:px-6 py-4">
                                            <?php if(isset($produit['created_at'])): ?>
                                            <div class="text-sm text-dark">
                                                <div class="font-medium"><?php echo date('d/m/Y', strtotime($produit['created_at'])); ?></div>
                                                <div class="text-gray-500"><?php echo date('H:i', strtotime($produit['created_at'])); ?></div>
                                            </div>
                                            <?php else: ?>
                                            <span class="text-xs text-gray-400">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 lg:px-6 py-4">
                                            <div class="flex items-center space-x-2">
                                                <a href="modifier_produit.php?id=<?php echo $produit['id']; ?>" 
                                                   class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-secondary hover:bg-blue-600 rounded-lg transition-colors duration-200">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    Modifier
                                                </a>
                                                <a href="?delete=<?php echo $produit['id']; ?>" 
                                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')"
                                                   class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-danger hover:bg-red-600 rounded-lg transition-colors duration-200">
                                                    <i class="fas fa-trash mr-1"></i>
                                                    Supprimer
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Mobile Cards -->
                        <div class="sm:hidden divide-y divide-gray-200">
                            <?php foreach($produits as $produit): ?>
                            <div class="p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <?php if($produit['image'] && file_exists('../uploads/' . $produit['image'])): ?>
                                            <img src="../uploads/<?php echo htmlspecialchars($produit['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($produit['nom']); ?>" 
                                                 class="w-12 h-12 object-cover rounded-lg">
                                        <?php else: ?>
                                            <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <span class="text-xs font-medium text-gray-500">ID:</span>
                                            <span class="text-sm font-bold text-dark">#<?php echo str_pad($produit['id'], 3, '0', STR_PAD_LEFT); ?></span>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold 
                                        <?php 
                                        if($produit['stock'] == 0) {
                                            echo 'bg-red-100 text-red-800';
                                        } elseif($produit['stock'] <= 5) {
                                            echo 'bg-yellow-100 text-yellow-800';
                                        } else {
                                            echo 'bg-green-100 text-green-800';
                                        }
                                        ?>">
                                        Stock: <?php echo $produit['stock']; ?>
                                    </span>
                                </div>
                                
                                <div>
                                    <h3 class="font-semibold text-dark text-lg"><?php echo htmlspecialchars($produit['nom']); ?></h3>
                                    <?php if(isset($produit['description']) && $produit['description']): ?>
                                    <p class="text-xs text-gray-500 mb-2"><?php echo htmlspecialchars(substr($produit['description'], 0, 80)) . (strlen($produit['description']) > 80 ? '...' : ''); ?></p>
                                    <?php endif; ?>
                                    <p class="text-accent font-bold text-xl"><?php echo number_format($produit['prix'], 2, ',', ' '); ?> €</p>
                                    <?php if(isset($produit['created_at'])): ?>
                                    <p class="text-xs text-gray-500 mt-1">Créé le: <?php echo date('d/m/Y', strtotime($produit['created_at'])); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="flex space-x-2 pt-2">
                                    <a href="modifier_produit.php?id=<?php echo $produit['id']; ?>" 
                                       class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-white bg-secondary hover:bg-blue-600 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-edit mr-2"></i>
                                        Modifier
                                    </a>
                                    <a href="?delete=<?php echo $produit['id']; ?>" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')"
                                       class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-white bg-danger hover:bg-red-600 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-trash mr-2"></i>
                                        Supprimer
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if(empty($produits)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-box-open text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun produit trouvé</h3>
                            <p class="text-gray-500 mb-6">Commencez par ajouter votre premier produit</p>
                            <a href="ajouter_produit.php" class="inline-flex items-center space-x-2 bg-primary hover:bg-cyan-400 text-white font-semibold px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                                <i class="fas fa-plus"></i>
                                <span>Ajouter un produit</span>
                            </a>
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