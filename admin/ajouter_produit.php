<?php
require_once '../config.php';

if(!isAdmin()) {
    redirect('login.php');
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);
    $prix = (float)$_POST['prix'];
    $stock = (int)$_POST['stock'];
    $image = '';
    
    // Gérer l'upload d'image
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            $image = uniqid() . '.' . $ext;
            $upload_dir = '../uploads/';
            
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
        }
    }
    
    // Insérer le produit
    $stmt = $pdo->prepare("INSERT INTO produits (nom, description, prix, stock, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $description, $prix, $stock, $image]);
    
    redirect('produits.php');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit</title>
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
                        'warning': '#F26619'
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <!-- Header -->
    <header class="bg-dark shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-plus-circle text-primary text-2xl"></i>
                    <h1 class="text-white text-xl sm:text-2xl font-bold">Ajouter un produit</h1>
                </div>
                <a href="logout.php" class="flex items-center space-x-2 bg-warning hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="hidden sm:inline">Déconnexion</span>
                </a>
            </div>
        </div>
    </header>
    
    <!-- Navigation -->
    <nav class="bg-secondary shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex space-x-0 overflow-x-auto">
                <a href="index.php" class="flex items-center space-x-2 text-white hover:bg-dark px-4 py-3 transition-colors duration-200 whitespace-nowrap">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Tableau de bord</span>
                </a>
                <a href="produits.php" class="flex items-center space-x-2 text-white bg-dark px-4 py-3 transition-colors duration-200 whitespace-nowrap">
                    <i class="fas fa-box"></i>
                    <span>Produits</span>
                </a>
                <a href="commandes.php" class="flex items-center space-x-2 text-white hover:bg-dark px-4 py-3 transition-colors duration-200 whitespace-nowrap">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Commandes</span>
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Breadcrumb -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
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
                <li class="text-dark font-medium">Nouveau produit</li>
            </ol>
        </nav>
    </div>
    
    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-primary to-cyan-400 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center space-x-2">
                            <i class="fas fa-plus text-white"></i>
                            <span>Créer un nouveau produit</span>
                        </h2>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data" class="p-6 space-y-6" id="productForm">
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
                                   required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 transition-colors duration-200 text-dark placeholder-gray-400"
                                   placeholder="Ex: iPhone 14 Pro Max">
                            <p class="text-xs text-gray-500 flex items-center space-x-1">
                                <i class="fas fa-lightbulb"></i>
                                <span>Choisissez un nom clair et descriptif</span>
                            </p>
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
                                      placeholder="Décrivez votre produit en détail..."></textarea>
                            <p class="text-xs text-gray-500 flex items-center space-x-1">
                                <i class="fas fa-info-circle"></i>
                                <span>Une bonne description améliore les ventes</span>
                            </p>
                        </div>
                        
                        <!-- Prix et Stock -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="prix" class="flex items-center space-x-2 text-sm font-semibold text-dark">
                                    <i class="fas fa-money-bill-wave text-accent"></i>
                                    <span>Prix (CFA)</span>
                                    <span class="text-warning">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" 
                                           id="prix" 
                                           name="prix" 
                                           step="0.01" 
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
                                    <span>Stock initial</span>
                                    <span class="text-warning">*</span>
                                </label>
                                <input type="number" 
                                       id="stock" 
                                       name="stock" 
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
                                <span>Image du produit</span>
                            </label>
                            <div class="relative">
                                <input type="file" 
                                       id="image" 
                                       name="image" 
                                       accept="image/*"
                                       class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 transition-colors duration-200 text-dark file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-cyan-600">
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs text-gray-500">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-check text-green-500"></i>
                                    <span>Formats: JPG, JPEG, PNG, GIF</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-info-circle text-blue-500"></i>
                                    <span>Taille recommandée: 800x800px</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200">
                            <button type="submit" 
                                    class="flex-1 flex items-center justify-center space-x-2 bg-primary hover:bg-cyan-400 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                                <i class="fas fa-plus"></i>
                                <span>Ajouter le produit</span>
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
                <!-- Preview -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-accent to-orange-400 px-4 py-3">
                        <h3 class="text-lg font-bold text-white flex items-center space-x-2">
                            <i class="fas fa-eye text-white"></i>
                            <span>Aperçu</span>
                        </h3>
                    </div>
                    
                    <div class="p-4">
                        <div class="border-2 border-dashed border-gray-200 rounded-lg p-4 text-center">
                            <div id="imagePreview" class="hidden">
                                <img id="previewImg" src="" alt="Aperçu" class="w-full h-32 object-cover rounded-lg mb-2">
                            </div>
                            <div id="imagePlaceholder" class="text-gray-400">
                                <i class="fas fa-image text-4xl mb-2"></i>
                                <p class="text-sm">Image du produit</p>
                            </div>
                        </div>
                        <div class="mt-4 space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Nom:</span>
                                <span id="previewNom" class="text-sm font-semibold text-dark">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Prix:</span>
                                <span id="previewPrix" class="text-sm font-bold text-accent">- CFA</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Stock:</span>
                                <span id="previewStock" class="text-sm font-semibold text-secondary">-</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tips -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-secondary to-dark px-4 py-3">
                        <h3 class="text-lg font-bold text-white flex items-center space-x-2">
                            <i class="fas fa-lightbulb text-primary"></i>
                            <span>Conseils</span>
                        </h3>
                    </div>
                    
                    <div class="p-4 space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-primary rounded-full flex items-center justify-center">
                                <i class="fas fa-camera text-white text-xs"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-dark text-sm">Photo de qualité</h4>
                                <p class="text-xs text-gray-600">Utilisez des images nettes et bien éclairées</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-accent rounded-full flex items-center justify-center">
                                <i class="fas fa-money-bill text-white text-xs"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-dark text-sm">Prix compétitif</h4>
                                <p class="text-xs text-gray-600">Recherchez les prix du marché</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-secondary rounded-full flex items-center justify-center">
                                <i class="fas fa-boxes text-white text-xs"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-dark text-sm">Stock précis</h4>
                                <p class="text-xs text-gray-600">Indiquez la quantité réellement disponible</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-warning rounded-full flex items-center justify-center">
                                <i class="fas fa-edit text-white text-xs"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-dark text-sm">Description détaillée</h4>
                                <p class="text-xs text-gray-600">Plus d'infos = plus de ventes</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-warning to-red-500 px-4 py-3">
                        <h3 class="text-lg font-bold text-white flex items-center space-x-2">
                            <i class="fas fa-chart-line text-white"></i>
                            <span>Actions rapides</span>
                        </h3>
                    </div>
                    
                    <div class="p-4 space-y-3">
                        <a href="produits.php" 
                           class="w-full flex items-center space-x-2 bg-gray-100 hover:bg-gray-200 text-dark font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                            <i class="fas fa-list text-secondary"></i>
                            <span>Voir tous les produits</span>
                        </a>
                        
                        <button type="button" onclick="document.getElementById('productForm').reset(); updatePreview();" 
                                class="w-full flex items-center space-x-2 bg-primary hover:bg-cyan-400 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                            <i class="fas fa-eraser"></i>
                            <span>Vider le formulaire</span>
                        </button>
                        
                        <a href="commandes.php" 
                           class="w-full flex items-center space-x-2 bg-accent hover:bg-yellow-500 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Voir les commandes</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Live preview functionality
        function updatePreview() {
            const nom = document.getElementById('nom').value || '-';
            const prix = document.getElementById('prix').value || '0';
            const stock = document.getElementById('stock').value || '0';
            
            document.getElementById('previewNom').textContent = nom;
            document.getElementById('previewPrix').textContent = prix ? `${prix} CFA` : '- CFA';
            document.getElementById('previewStock').textContent = stock;
        }
        
        // Image preview
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').classList.remove('hidden');
                    document.getElementById('imagePlaceholder').classList.add('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('imagePreview').classList.add('hidden');
                document.getElementById('imagePlaceholder').classList.remove('hidden');
            }
        });
        
        // Real-time preview updates
        document.getElementById('nom').addEventListener('input', updatePreview);
        document.getElementById('prix').addEventListener('input', updatePreview);
        document.getElementById('stock').addEventListener('input', updatePreview);
        
        // Form validation feedback
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Ajout en cours...';
            submitBtn.disabled = true;
            
            // If validation fails, restore button
            setTimeout(() => {
                if (!this.checkValidity()) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }, 100);
        });
        
        // Initialize preview
        updatePreview();
    </script>
</body>
</html>