<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un produit - Administration</title>
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
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
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
                <a href="produits.php" class="flex items-center px-4 py-3 bg-secondary text-white rounded-lg">
                    <i class="fas fa-gem mr-3"></i>
                    <span class="font-medium">Produits</span>
                </a>
                <a href="commandes.php" class="flex items-center px-4 py-3 text-gray-300 hover:bg-secondary hover:text-white rounded-lg transition duration-200">
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
                            <i class="fas fa-edit text-primary text-xl sm:text-2xl"></i>
                            <h2 class="text-lg sm:text-2xl font-bold text-dark">Modifier un produit</h2>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="hidden sm:block text-gray-600 mr-4 text-sm sm:text-base">Bienvenue, Admin</span>
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
                        <li>
                            <a href="produits.php" class="hover:text-primary transition-colors duration-200">Produits</a>
                        </li>
                        <li><i class="fas fa-chevron-right text-gray-300"></i></li>
                        <li class="text-dark font-medium">Modifier #42</li>
                    </ol>
                </nav>
            </div>
            
            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                        <!-- Form Section -->
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                                <div class="bg-gradient-to-r from-secondary to-dark px-4 sm:px-6 py-4">
                                    <h3 class="text-lg sm:text-xl font-bold text-white flex items-center space-x-2">
                                        <i class="fas fa-edit text-primary"></i>
                                        <span>Informations du produit</span>
                                    </h3>
                                </div>
                                
                                <form method="POST" enctype="multipart/form-data" class="p-4 sm:p-6 space-y-6" id="productForm">
                                    <!-- Nom du produit -->
                                    <div class="space-y-2">
                                        <label for="nom" class="flex items-center space-x-2 text-sm font-semibold text-dark">
                                            <i class="fas fa-tag text-primary"></i>
                                            <span>Nom du produit</span>
                                            <span class="text-warning">*</span>
                                        </label>
                                        <input type="text" 
                                               id="nom" 
                                               name="nom" 
                                               value="iPhone 14 Pro Max" 
                                               required
                                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 transition-colors duration-200 text-dark placeholder-gray-400"
                                               placeholder="Entrez le nom du produit">
                                    </div>
                                    
                                    <!-- Description -->
                                    <div class="space-y-2">
                                        <label for="description" class="flex items-center space-x-2 text-sm font-semibold text-dark">
                                            <i class="fas fa-align-left text-primary"></i>
                                            <span>Description</span>
                                        </label>
                                        <textarea id="description" 
                                                  name="description" 
                                                  rows="4"
                                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 transition-colors duration-200 text-dark placeholder-gray-400 resize-vertical"
                                                  placeholder="Décrivez votre produit...">Smartphone haut de gamme avec écran Super Retina XDR de 6,7 pouces, puce A16 Bionic et système photo pro avancé. Disponible en plusieurs coloris.</textarea>
                                    </div>
                                    
                                    <!-- Prix et Stock -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label for="prix" class="flex items-center space-x-2 text-sm font-semibold text-dark">
                                                <i class="fas fa-coins text-accent"></i>
                                                <span>Prix (CFA)</span>
                                                <span class="text-warning">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="number" 
                                                       id="prix" 
                                                       name="prix" 
                                                       step="0.01" 
                                                       value="750000" 
                                                       required
                                                       min="0"
                                                       class="w-full pl-4 pr-16 py-3 border-2 border-gray-200 rounded-lg focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50 transition-colors duration-200 text-dark"
                                                       placeholder="0.00">
                                                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-accent font-semibold">CFA</span>
                                            </div>
                                        </div>
                                        
                                        <div class="space-y-2">
                                            <label for="stock" class="flex items-center space-x-2 text-sm font-semibold text-dark">
                                                <i class="fas fa-cubes text-secondary"></i>
                                                <span>Stock</span>
                                                <span class="text-warning">*</span>
                                            </label>
                                            <input type="number" 
                                                   id="stock" 
                                                   name="stock" 
                                                   value="15" 
                                                   required
                                                   min="0"
                                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-secondary focus:ring focus:ring-secondary focus:ring-opacity-50 transition-colors duration-200 text-dark"
                                                   placeholder="Quantité disponible">
                                        </div>
                                    </div>
                                    
                                    <!-- Image Upload -->
                                    <div class="space-y-2">
                                        <label for="image" class="flex items-center space-x-2 text-sm font-semibold text-dark">
                                            <i class="fas fa-image text-primary"></i>
                                            <span>Nouvelle image</span>
                                        </label>
                                        <div class="relative">
                                            <input type="file" 
                                                   id="image" 
                                                   name="image" 
                                                   accept="image/*"
                                                   class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 transition-colors duration-200 text-dark file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-cyan-600">
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-gray-500">
                                            <div class="flex items-center space-x-1">
                                                <i class="fas fa-check text-green-500"></i>
                                                <span>Formats: JPG, JPEG, PNG, GIF</span>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <i class="fas fa-info-circle text-blue-500"></i>
                                                <span>Laisser vide pour garder l'actuelle</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200">
                                        <button type="submit" 
                                                class="flex-1 flex items-center justify-center space-x-2 bg-primary hover:bg-cyan-400 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                                            <i class="fas fa-save"></i>
                                            <span>Enregistrer les modifications</span>
                                        </button>
                                        
                                        <a href="produits.php" 
                                           class="flex-1 flex items-center justify-center space-x-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                                            <i class="fas fa-times"></i>
                                            <span>Annuler</span>
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Sidebar Section -->
                        <div class="space-y-6">
                            <!-- Current Image -->
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                                <div class="bg-gradient-to-r from-accent to-orange-400 px-4 py-3">
                                    <h4 class="text-lg font-bold text-white flex items-center space-x-2">
                                        <i class="fas fa-image text-white"></i>
                                        <span>Image actuelle</span>
                                    </h4>
                                </div>
                                
                                <div class="p-4">
                                    <div class="relative group">
                                        <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg shadow-md group-hover:shadow-lg transition-shadow duration-200 flex items-center justify-center">
                                            <div class="text-center text-gray-400">
                                                <i class="fas fa-mobile-alt text-4xl mb-2"></i>
                                                <p class="text-sm font-medium">iPhone 14 Pro Max</p>
                                                <p class="text-xs">Image de démo</p>
                                            </div>
                                        </div>
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-opacity duration-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-2xl"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Product Info -->
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                                <div class="bg-gradient-to-r from-secondary to-dark px-4 py-3">
                                    <h4 class="text-lg font-bold text-white flex items-center space-x-2">
                                        <i class="fas fa-info-circle text-primary"></i>
                                        <span>Informations</span>
                                    </h4>
                                </div>
                                
                                <div class="p-4 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">ID Produit</span>
                                        <span class="font-bold text-dark">#42</span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Prix actuel</span>
                                        <span class="font-bold text-accent text-lg">750,000 CFA</span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Stock actuel</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                            15
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Valeur stock</span>
                                        <span class="font-bold text-secondary">11,250,000 CFA</span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Créé le</span>
                                        <span class="text-sm text-gray-800">15/05/2025</span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Dernière MAJ</span>
                                        <span class="text-sm text-gray-800">30/05/2025</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                                <div class="bg-gradient-to-r from-warning to-red-500 px-4 py-3">
                                    <h4 class="text-lg font-bold text-white flex items-center space-x-2">
                                        <i class="fas fa-bolt text-white"></i>
                                        <span>Actions rapides</span>
                                    </h4>
                                </div>
                                
                                <div class="p-4 space-y-3">
                                    <a href="produits.php" 
                                       class="w-full flex items-center space-x-2 bg-gray-100 hover:bg-gray-200 text-dark font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-arrow-left text-secondary"></i>
                                        <span>Retour à la liste</span>
                                    </a>
                                    
                                    <a href="ajouter_produit.php" 
                                       class="w-full flex items-center space-x-2 bg-primary hover:bg-cyan-400 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-plus"></i>
                                        <span>Ajouter un produit</span>
                                    </a>
                                    
                                    <button onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) { alert('Produit supprimé (démo)'); }" 
                                            class="w-full flex items-center space-x-2 bg-danger hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-trash"></i>
                                        <span>Supprimer</span>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Sales Analytics -->
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                                <div class="bg-gradient-to-r from-green-400 to-green-600 px-4 py-3">
                                    <h4 class="text-lg font-bold text-white flex items-center space-x-2">
                                        <i class="fas fa-chart-line text-white"></i>
                                        <span>Statistiques</span>
                                    </h4>
                                </div>
                                
                                <div class="p-4 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Vues produit</span>
                                        <span class="font-bold text-green-600">1,234</span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Ventes totales</span>
                                        <span class="font-bold text-green-600">8</span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">CA généré</span>
                                        <span class="font-bold text-green-600">6,000,000 CFA</span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Taux conversion</span>
                                        <span class="font-bold text-green-600">0.65%</span>
                                    </div>
                                </div>
                            </div>
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

        // Form validation feedback
        document.getElementById('productForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Pour la démo
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enregistrement...';
            submitBtn.disabled = true;
            
            // Simulation de sauvegarde
            setTimeout(() => {
                submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Modifications enregistrées !';
                submitBtn.classList.remove('bg-primary', 'hover:bg-cyan-400');
                submitBtn.classList.add('bg-green-500', 'hover:bg-green-600');
                
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
                    submitBtn.classList.add('bg-primary', 'hover:bg-cyan-400');
                }, 2000);
            }, 1500);
        });

        // Image preview for new uploads
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // You could update the current image preview here
                    console.log('New image selected:', file.name);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>