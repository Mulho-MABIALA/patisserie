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
    <title>FM-Cakes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <?php echo getTailwindConfig(); ?>
    <style>
        /* Animations personnalisées */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        @keyframes bounce-gentle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes wiggle {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(5deg); }
            75% { transform: rotate(-5deg); }
        }

        .floating {
            animation: float 4s ease-in-out infinite;
        }

        .bounce-gentle {
            animation: bounce-gentle 2s ease-in-out infinite;
        }

        .wiggle:hover {
            animation: wiggle 0.5s ease-in-out;
        }

        /* Dégradés personnalisés pour pâtisserie */
        .bg-pastry-gradient {
            background: linear-gradient(135deg, #FF6B6B 0%, #FF8E8E 25%, #FFB3BA 50%, #FFCCCB 75%, #FFF0F0 100%);
        }

        .bg-cream-gradient {
            background: linear-gradient(135deg, #FFF8DC 0%, #FFFACD 50%, #FFEFD5 100%);
        }

        .bg-chocolate-gradient {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 50%, #CD853F 100%);
        }

        /* Couleurs personnalisées */
        :root {
            --pastry-pink: #FF6B6B;
            --pastry-cream: #FFF8DC;
            --pastry-chocolate: #8B4513;
            --pastry-gold: #FFD700;
            --pastry-mint: #98FB98;
        }
    </style>
</head>
<body class="bg-gradient-to-b from-pink-50 to-orange-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-xl sticky top-0 z-50 border-b-4 border-pink-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <img src="images/FM_Cakes.png" alt="Logo" class="h-10 mr-3">
                    <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-pink-600 to-orange-500 bg-clip-text text-transparent">
                        FM_Cakes
                    </h1>
                </div>

                <!-- Menu Mobile Toggle -->
                <div class="md:hidden">
                    <button id="mobile-menu-toggle" class="text-pink-600 hover:text-pink-800 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>

                <!-- Menu Desktop -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="text-gray-700 hover:text-pink-600 transition duration-300 font-medium">
                        <i class="fas fa-home mr-2"></i>Accueil
                    </a>
                     <a href="Apropos.php" class="text-gray-700 hover:text-pink-600 transition duration-300 font-medium">
                        <i class=""></i>A propos
                    </a>
                    <a href="Catalogue.php" class="text-gray-700 hover:text-pink-600 transition duration-300 font-medium">
                        <i class=""></i>Catalogue
                    </a>
                    <a href="panier.php" class="text-gray-700 hover:text-pink-600 transition duration-300 font-medium relative">
                        <i class="fas fa-shopping-cart mr-2"></i>Panier
                        <?php if($nb_articles_panier > 0): ?>
                        <span class="absolute -top-2 -right-2 bg-pink-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center animate-pulse">
                            <?php echo $nb_articles_panier; ?>
                        </span>
                        <?php endif; ?>
                    </a>

                    <a href="connexion.php" class="bg-gradient-to-r from-pink-500 to-orange-400 hover:from-pink-600 hover:to-orange-500 text-white px-6 py-2 rounded-full transition duration-300 font-medium shadow-lg">
                        <i class="fas fa-user-shield mr-2"></i>Connexion 
                    </a>
                </div>
            </div>

            <!-- Menu Mobile -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <a href="index.php" class="block text-gray-700 hover:text-pink-600 py-2 font-medium">
                    <i class="fas fa-home mr-2"></i>Accueil
                </a>
                <a href="panier.php" class="block text-gray-700 hover:text-pink-600 py-2 font-medium">
                    <i class="fas fa-shopping-cart mr-2"></i>Panier (<?php echo $nb_articles_panier; ?>)
                </a>
                <a href="admin/login.php" class="block text-gray-700 hover:text-pink-600 py-2 font-medium">
                    <i class="fas fa-user-shield mr-2"></i>Admin
                </a>
            </div>
        </div>
    </nav>

    <!-- Carrousel -->
    <div class="relative w-full max-w-7xl mx-auto my-8">
        <div class="overflow-hidden rounded-lg">
            <div class="flex transition-transform duration-500 ease-in-out" id="carousel">
                <!-- Slide 1 -->
                <div class="w-full flex-shrink-0">
                    <img src="images/image 1.jpg" alt="Slide 1" class="w-full">
                </div>
                <!-- Slide 2 -->
                <div class="w-full flex-shrink-0">
                    <img src="images/image 1.jpg" alt="Slide 2" class="w-full">
                </div>
                <!-- Slide 3 -->
                <div class="w-full flex-shrink-0">
                    <img src="images/image 1.jpg" alt="Slide 3" class="w-full">
                </div>
            </div>
        </div>
        <!-- Boutons de navigation du carrousel -->
        <button class="absolute top-1/2 left-0 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-md" onclick="prevSlide()">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="absolute top-1/2 right-0 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-md" onclick="nextSlide()">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>

    <!-- Hero Section -->
    <section class="bg-pastry-gradient py-20 relative overflow-hidden">
        <!-- Éléments décoratifs -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-10 left-10 w-32 h-32 bg-white rounded-full bounce-gentle"></div>
            <div class="absolute top-20 right-20 w-24 h-24 bg-yellow-200 rounded-full floating"></div>
            <div class="absolute bottom-20 left-20 w-40 h-40 bg-pink-200 rounded-full bounce-gentle"></div>
            <div class="absolute bottom-10 right-10 w-28 h-28 bg-orange-200 rounded-full floating"></div>
        </div>

        <!-- Icônes de pâtisserie flottantes -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <i class="fas fa-cookie-bite absolute top-1/4 left-1/4 text-6xl text-white opacity-10 floating"></i>
            <i class="fas fa-ice-cream absolute top-1/3 right-1/3 text-5xl text-white opacity-10 bounce-gentle"></i>
            <i class="fas fa-candy-cane absolute bottom-1/4 left-1/3 text-4xl text-white opacity-10 floating"></i>
        </div>

        <div class="max-w-7xl mx-auto px-4 text-center relative z-10">
            <div class="mb-6">
                <i class="fas fa-birthday-cake text-8xl text-white mb-4 wiggle"></i>
            </div>
            <h2 class="text-4xl sm:text-5xl md:text-6xl font-bold text-white mb-6 drop-shadow-lg">
                Savourez la Douceur
            </h2>
            <p class="text-xl sm:text-2xl text-white/95 mb-8 max-w-3xl mx-auto drop-shadow-md">
                Des pâtisseries artisanales préparées avec amour pour éveiller vos sens
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#produits" class="bg-white hover:bg-gray-100 text-pink-600 px-8 py-4 rounded-full font-bold text-lg transition transform hover:scale-105 shadow-xl">
                    <i class="fas fa-cookie-bite mr-2"></i>Découvrir nos créations
                </a>
                <a href="#contact" class="bg-yellow-400 hover:bg-yellow-500 text-gray-800 px-8 py-4 rounded-full font-bold text-lg transition transform hover:scale-105 shadow-xl">
                    <i class="fas fa-phone mr-2"></i>Nous contacter
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-12 bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="group">
                    <div class="bg-pink-100 rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center group-hover:scale-110 transition duration-300">
                        <i class="fas fa-birthday-cake text-pink-600 text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-pink-600"><?php echo count($produits); ?>+</h3>
                    <p class="text-gray-600 font-medium">Délices sucrés</p>
                </div>
                <div class="group">
                    <div class="bg-orange-100 rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center group-hover:scale-110 transition duration-300">
                        <i class="fas fa-heart text-orange-600 text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-orange-600">100%</h3>
                    <p class="text-gray-600 font-medium">Fait maison</p>
                </div>
                <div class="group">
                    <div class="bg-yellow-100 rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center group-hover:scale-110 transition duration-300">
                        <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-yellow-600">2h</h3>
                    <p class="text-gray-600 font-medium">Préparation</p>
                </div>
                <div class="group">
                    <div class="bg-green-100 rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center group-hover:scale-110 transition duration-300">
                        <i class="fas fa-star text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-green-600">5★</h3>
                    <p class="text-gray-600 font-medium">Qualité</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Produits -->
    <section id="produits" class="py-16 bg-gradient-to-b from-pink-50 to-orange-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <div class="flex justify-center items-center mb-4">
                    <i class="fas fa-cookie-bite text-pink-500 text-3xl mr-3 wiggle"></i>
                    <h2 class="text-4xl font-bold bg-gradient-to-r from-pink-600 to-orange-500 bg-clip-text text-transparent">
                        Nos Délices
                    </h2>
                    <i class="fas fa-ice-cream text-pink-500 text-3xl ml-3 wiggle"></i>
                </div>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Chaque pâtisserie est préparée avec des ingrédients frais et beaucoup d'amour
                </p>
            </div>

            <?php if(empty($produits)): ?>
            <div class="text-center py-16 bg-white rounded-3xl shadow-lg">
                <i class="fas fa-cookie-bite text-8xl text-pink-300 mb-6"></i>
                <p class="text-gray-500 text-2xl font-medium mb-2">Aucune pâtisserie disponible</p>
                <p class="text-gray-400 text-lg">Nos chefs préparent de nouveaux délices pour vous !</p>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <?php foreach($produits as $produit): ?>
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-2xl group border-2 border-pink-100">
                    <!-- Image -->
                    <div class="relative h-64 overflow-hidden">
                        <?php if($produit['image'] && file_exists('uploads/' . $produit['image'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($produit['image']); ?>"
                                 alt="<?php echo htmlspecialchars($produit['nom']); ?>"
                                 class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <?php else: ?>
                            <div class="w-full h-full bg-pastry-gradient flex items-center justify-center">
                                <i class="fas fa-birthday-cake text-white text-6xl opacity-70"></i>
                            </div>
                        <?php endif; ?>

                        <!-- Badge Stock -->
                        <?php if($produit['stock'] == 0): ?>
                        <span class="absolute top-4 right-4 bg-gray-600 text-white px-3 py-2 rounded-full text-sm font-medium shadow-lg">
                            <i class="fas fa-times mr-1"></i>Épuisé
                        </span>
                        <?php elseif($produit['stock'] <= 5): ?>
                        <span class="absolute top-4 right-4 bg-red-500 text-white px-3 py-2 rounded-full text-sm font-medium animate-pulse shadow-lg">
                            <i class="fas fa-fire mr-1"></i>Dernières pièces
                        </span>
                        <?php else: ?>
                        <span class="absolute top-4 right-4 bg-green-500 text-white px-3 py-2 rounded-full text-sm font-medium shadow-lg">
                            <i class="fas fa-check mr-1"></i>Disponible
                        </span>
                        <?php endif; ?>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($produit['nom']); ?></h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            <?php echo htmlspecialchars($produit['description'] ?: 'Délicieuse pâtisserie artisanale préparée avec amour et des ingrédients frais.'); ?>
                        </p>

                        <div class="flex items-center justify-between mb-4">
                            <span class="text-2xl font-bold text-pink-600">
                                <?php echo number_format($produit['prix'], 0, ',', ' '); ?> CFA
                            </span>
                            <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                <i class="fas fa-layer-group mr-1"></i><?php echo $produit['stock']; ?> disponible(s)
                            </span>
                        </div>

                        <?php if($produit['stock'] > 0): ?>
                        <a href="ajouter_panier.php?id=<?php echo $produit['id']; ?>"
                           class="block w-full bg-gradient-to-r from-pink-500 to-orange-400 hover:from-pink-600 hover:to-orange-500 text-white text-center py-3 rounded-xl font-bold transition duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-cart-plus mr-2"></i>Ajouter au panier
                        </a>
                        <?php else: ?>
                        <button disabled
                                class="block w-full bg-gray-300 text-gray-500 text-center py-3 rounded-xl font-bold cursor-not-allowed">
                            <i class="fas fa-times mr-2"></i>Non disponible
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
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-4 bg-gradient-to-r from-pink-600 to-orange-500 bg-clip-text text-transparent">
                Pourquoi choisir nos pâtisseries ?
            </h2>
            <p class="text-center text-gray-600 mb-12 text-lg">Des délices qui font la différence</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center group">
                    <div class="bg-gradient-to-r from-pink-500 to-pink-600 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition duration-300 shadow-xl">
                        <i class="fas fa-truck text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Livraison Express</h3>
                    <p class="text-gray-600 text-lg">Livraison gratuite dès 15.000 CFA dans tout Dakar</p>
                </div>

                <div class="text-center group">
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition duration-300 shadow-xl">
                        <i class="fas fa-award text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Qualité Premium</h3>
                    <p class="text-gray-600 text-lg">Ingrédients frais et recettes traditionnelles</p>
                </div>

                <div class="text-center group">
                    <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition duration-300 shadow-xl">
                        <i class="fas fa-smile text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Satisfaction Garantie</h3>
                    <p class="text-gray-600 text-lg">Remboursement si vous n'êtes pas satisfait</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gradient-to-r from-pink-600 to-orange-500 text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-birthday-cake text-3xl mr-3"></i>
                        <h3 class="text-2xl font-bold">Délices Sucrés</h3>
                    </div>
                    <p class="text-white/90 text-lg">Votre pâtisserie artisanale de confiance depuis 2020</p>
                    <p class="text-white/70 text-sm mt-2">Des moments sucrés partagés en famille</p>
                </div>

                <div>
                    <h3 class="text-xl font-bold mb-4">Contact</h3>
                    <p class="text-white/90 mb-3 text-lg">
                        <i class="fas fa-phone mr-3 text-yellow-300"></i>+221 78 166 64 80
                    </p>
                    <p class="text-white/90 mb-3 text-lg">
                        <i class="fas fa-envelope mr-3 text-yellow-300"></i>fmcake@gmail.com
                    </p>
                    <p class="text-white/90 text-lg">
                        <i class="fas fa-map-marker-alt mr-3 text-yellow-300"></i>Dakar, Sénégal
                    </p>
                </div>

                <div>
                    <h3 class="text-xl font-bold mb-4">Suivez-nous</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="bg-white/20 hover:bg-white/30 w-12 h-12 rounded-full flex items-center justify-center transition duration-300 transform hover:scale-110">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </a>
                        <a href="#" class="bg-white/20 hover:bg-white/30 w-12 h-12 rounded-full flex items-center justify-center transition duration-300 transform hover:scale-110">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="bg-white/20 hover:bg-white/30 w-12 h-12 rounded-full flex items-center justify-center transition duration-300 transform hover:scale-110">
                            <i class="fab fa-whatsapp text-xl"></i>
                        </a>
                        <a href="#" class="bg-white/20 hover:bg-white/30 w-12 h-12 rounded-full flex items-center justify-center transition duration-300 transform hover:scale-110">
                            <i class="fab fa-tiktok text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>
            <a href="admin/login.php" class="bg-gradient-to-r from-pink-500 to-orange-400 hover:from-pink-600 hover:to-orange-500 text-white px-6 py-2 rounded-full transition duration-300 font-medium shadow-lg">
                        <i class="fas fa-user-shield mr-2"></i>Admin
                    </a>
                </div>
            </div>

            <div class="border-t border-white/20 mt-8 pt-8 text-center">
                <p class="text-white/80">&copy; 2024 Délices Sucrés. Tous droits réservés.</p>
                <p class="text-white/60 text-sm mt-2">Fait avec <i class="fas fa-heart text-yellow-300"></i> et beaucoup de sucre à Dakar</p>
            </div>
        </div>
    </footer>

    <!-- Bouton retour en haut -->
    <button id="scrollToTop" class="fixed bottom-6 right-6 bg-gradient-to-r from-pink-500 to-orange-400 text-white w-14 h-14 rounded-full shadow-xl hover:from-pink-600 hover:to-orange-500 transition duration-300 transform hover:scale-110 hidden">
        <i class="fas fa-arrow-up text-xl"></i>
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

        // Carrousel
        let currentSlide = 0;
        const carousel = document.getElementById('carousel');
        const slides = carousel.querySelectorAll('div');

        function updateCarousel() {
            carousel.style.transform = `translateX(-${currentSlide * 100}%)`;
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            updateCarousel();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            updateCarousel();
        }

        // Changer de slide toutes les 5 secondes
        setInterval(nextSlide, 5000);
    </script>
</body>
</html>
<?php