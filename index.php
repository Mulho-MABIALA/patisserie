<?php
require_once 'config.php';

// Récupérer tous les produits disponibles
$stmt = $pdo->query("SELECT * FROM produits WHERE stock > 0 ORDER BY created_at DESC");
$produits_disponibles = $stmt->fetchAll();

// Récupérer tous les produits (y compris rupture de stock)
$stmt = $pdo->query("SELECT * FROM produits ORDER BY created_at DESC");
$tous_produits = $stmt->fetchAll();

// Utiliser tous les produits pour l'affichage
$produits = $tous_produits;

// Compter les articles dans le panier
$nb_articles_panier = 0;
if(isset($_SESSION['panier'])) {
    $nb_articles_panier = array_sum($_SESSION['panier']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les créations de Nahed - Bijouterie Artisanale</title>
    <?php echo getTailwindConfig(); ?>
    <style>
        /* Animation personnalisée pour le hero */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        .floating {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-dark shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <i class="fas fa-gem text-accent text-2xl mr-3 floating"></i>
                    <h1 class="text-xl sm:text-2xl font-bold text-white">Les créations de Nahed</h1>
                </div>
                
                <!-- Menu Mobile Toggle -->
                <div class="md:hidden">
                    <button id="mobile-menu-toggle" class="text-white hover:text-primary focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
                
                <!-- Menu Desktop -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="text-white hover:text-primary transition duration-300">
                        <i class="fas fa-home mr-2"></i>Accueil
                    </a>
                    <a href="panier.php" class="text-white hover:text-primary transition duration-300 relative">
                        <i class="fas fa-shopping-cart mr-2"></i>Panier
                        <?php if($nb_articles_panier > 0): ?>
                        <span class="absolute -top-2 -right-2 bg-danger text-white text-xs rounded-full h-6 w-6 flex items-center justify-center animate-pulse">
                            <?php echo $nb_articles_panier; ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    <a href="admin/login.php" class="bg-secondary hover:bg-primary text-white px-4 py-2 rounded-lg transition duration-300">
                        <i class="fas fa-user-shield mr-2"></i>Admin
                    </a>
                </div>
            </div>
            
            <!-- Menu Mobile -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <a href="index.php" class="block text-white hover:text-primary py-2">
                    <i class="fas fa-home mr-2"></i>Accueil
                </a>
                <a href="panier.php" class="block text-white hover:text-primary py-2">
                    <i class="fas fa-shopping-cart mr-2"></i>Panier (<?php echo $nb_articles_panier; ?>)
                </a>
                <a href="admin/login.php" class="block text-white hover:text-primary py-2">
                    <i class="fas fa-user-shield mr-2"></i>Admin
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-secondary to-primary py-20 relative overflow-hidden">
        <!-- Motif décoratif -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-72 h-72 bg-white rounded-full filter blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-accent rounded-full filter blur-3xl"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 text-center relative z-10">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-4 animate-fade-in">
                Découvrez nos Bijoux d'Exception
            </h2>
            <p class="text-lg sm:text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                Des créations uniques faites main avec amour et passion pour sublimer votre élégance
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#produits" class="bg-accent hover:bg-yellow-500 text-dark px-8 py-3 rounded-full font-semibold transition transform hover:scale-105 shadow-lg">
                    <i class="fas fa-shopping-bag mr-2"></i>Voir la collection
                </a>
                <a href="#contact" class="bg-white hover:bg-gray-100 text-dark px-8 py-3 rounded-full font-semibold transition transform hover:scale-105 shadow-lg">
                    <i class="fas fa-phone mr-2"></i>Nous contacter
                </a>
            </div>
        </div>
    </section>
    
    <!-- Stats Section -->
    <section class="py-8 bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div>
                    <h3 class="text-3xl font-bold text-primary"><?php echo count($produits); ?>+</h3>
                    <p class="text-gray-600">Créations uniques</p>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-secondary">100%</h3>
                    <p class="text-gray-600">Fait main</p>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-accent">48h</h3>
                    <p class="text-gray-600">Livraison</p>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-danger">5★</h3>
                    <p class="text-gray-600">Satisfaction</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Produits -->
    <section id="produits" class="py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-dark mb-4">
                    <i class="fas fa-star text-accent mr-2"></i>
                    Nos Créations
                    <i class="fas fa-star text-accent ml-2"></i>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Chaque bijou est une œuvre d'art unique, créée avec passion et savoir-faire
                </p>
            </div>
            
            <?php if(empty($produits)): ?>
            <div class="text-center py-12">
                <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-xl">Aucun produit disponible pour le moment</p>
                <p class="text-gray-400 mt-2">Revenez bientôt pour découvrir nos nouvelles créations !</p>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach($produits as $produit): ?>
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-2xl group">
                    <!-- Image -->
                    <div class="relative h-64 overflow-hidden bg-gray-100">
                        <?php if($produit['image'] && file_exists('uploads/' . $produit['image'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($produit['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($produit['nom']); ?>"
                                 class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                                <i class="fas fa-gem text-white text-6xl opacity-50"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Badge Stock -->
                        <?php if($produit['stock'] == 0): ?>
                        <span class="absolute top-4 right-4 bg-gray-600 text-white px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-times mr-1"></i>Rupture
                        </span>
                        <?php elseif($produit['stock'] <= 5): ?>
                        <span class="absolute top-4 right-4 bg-danger text-white px-3 py-1 rounded-full text-sm animate-pulse">
                            <i class="fas fa-exclamation mr-1"></i>Stock limité
                        </span>
                        <?php else: ?>
                        <span class="absolute top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-check mr-1"></i>Disponible
                        </span>
                        <?php endif; ?>
                        
                        <!-- Overlay au hover -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition duration-300"></div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-dark mb-2"><?php echo htmlspecialchars($produit['nom']); ?></h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            <?php echo htmlspecialchars($produit['description'] ?: 'Magnifique bijou artisanal créé avec amour et passion.'); ?>
                        </p>
                        
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-2xl font-bold text-danger">
                                <?php echo number_format($produit['prix'], 0, ',', ' '); ?> CFA
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-box mr-1"></i><?php echo $produit['stock']; ?> en stock
                            </span>
                        </div>
                        
                        <?php if($produit['stock'] > 0): ?>
                        <a href="ajouter_panier.php?id=<?php echo $produit['id']; ?>" 
                           class="block w-full bg-accent hover:bg-yellow-500 text-dark text-center py-3 rounded-lg font-semibold transition duration-300 transform hover:scale-105">
                            <i class="fas fa-cart-plus mr-2"></i>Ajouter au panier
                        </a>
                        <?php else: ?>
                        <button disabled 
                                class="block w-full bg-gray-300 text-gray-500 text-center py-3 rounded-lg font-semibold cursor-not-allowed">
                            <i class="fas fa-times mr-2"></i>Indisponible
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Features -->
    <section class="py-16 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-dark mb-12">Pourquoi nous choisir ?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center group">
                    <div class="bg-primary w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition duration-300">
                        <i class="fas fa-truck text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-2">Livraison Rapide</h3>
                    <p class="text-gray-600">Livraison gratuite dès 25.000 CFA d'achat partout à Dakar</p>
                </div>
                
                <div class="text-center group">
                    <div class="bg-secondary w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition duration-300">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-2">Qualité Garantie</h3>
                    <p class="text-gray-600">Tous nos bijoux sont faits main avec des matériaux de qualité</p>
                </div>
                
                <div class="text-center group">
                    <div class="bg-accent w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition duration-300">
                        <i class="fas fa-undo text-dark text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-2">Satisfaction Client</h3>
                    <p class="text-gray-600">Échange gratuit sous 7 jours si vous n'êtes pas satisfait</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer id="contact" class="bg-dark text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4 text-primary">Les créations de Nahed</h3>
                    <p class="text-gray-300">Votre partenaire pour des bijoux artisanaux d'exception.</p>
                    <p class="text-gray-400 text-sm mt-2">Créations uniques depuis 2020</p>
                </div>
                
                <div>
                    <h3 class="text-xl font-bold mb-4 text-primary">Contact</h3>
                    <p class="text-gray-300 mb-2">
                        <i class="fas fa-phone mr-2 text-accent"></i>+221 77 628 01 31
                    </p>
                    <p class="text-gray-300 mb-2">
                        <i class="fas fa-envelope mr-2 text-accent"></i>contact@creationsnahed.sn
                    </p>
                    <p class="text-gray-300">
                        <i class="fas fa-map-marker-alt mr-2 text-accent"></i>Dakar, Sénégal
                    </p>
                </div>
                
                <div>
                    <h3 class="text-xl font-bold mb-4 text-primary">Suivez-nous</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="bg-secondary hover:bg-primary w-10 h-10 rounded-full flex items-center justify-center transition duration-300 transform hover:scale-110">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="bg-secondary hover:bg-primary w-10 h-10 rounded-full flex items-center justify-center transition duration-300 transform hover:scale-110">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="bg-secondary hover:bg-primary w-10 h-10 rounded-full flex items-center justify-center transition duration-300 transform hover:scale-110">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="#" class="bg-secondary hover:bg-primary w-10 h-10 rounded-full flex items-center justify-center transition duration-300 transform hover:scale-110">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-400">&copy; 2024 Les créations de Nahed. Tous droits réservés.</p>
                <p class="text-gray-500 text-sm mt-2">Fait avec <i class="fas fa-heart text-danger"></i> à Dakar</p>
            </div>
        </div>
    </footer>
    
    <!-- Bouton retour en haut -->
    <button id="scrollToTop" class="fixed bottom-6 right-6 bg-primary text-white w-12 h-12 rounded-full shadow-lg hover:bg-secondary transition duration-300 transform hover:scale-110 hidden">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-toggle').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
        
        // Scroll to top button
        const scrollToTopButton = document.getElementById('scrollToTop');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollToTopButton.classList.remove('hidden');
            } else {
                scrollToTopButton.classList.add('hidden');
            }
        });
        
        scrollToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Smooth scroll pour les ancres
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>