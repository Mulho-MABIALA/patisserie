<?php
// Configuration de la pâtisserie
$patisserie = [
    'nom' => 'Pâtisserie Délice',
    'slogan' => 'L\'art de la pâtisserie française depuis 1985',
    'fondateurs' => ['Marie Dubois', 'Pierre Dubois'],
    'annee_creation' => 1985,
    'ville' => 'Paris'
];

// Informations sur le chef
$chef = [
    'nom' => 'Chef Marcel Rousseau',
    'experience' => 25,
    'specialites' => ['Macarons', 'Éclairs', 'Tartes aux fruits', 'Viennoiseries'],
    'formation' => 'École de Pâtisserie de Paris'
];

// Valeurs de l'entreprise
$valeurs = [
    [
        'icone' => '🌟',
        'titre' => 'Excellence',
        'description' => 'Nous visons l\'excellence dans chaque création, de la sélection des ingrédients à la présentation finale.'
    ],
    [
        'icone' => '🤝',
        'titre' => 'Tradition',
        'description' => 'Nous perpétuons les techniques ancestrales de la pâtisserie française tout en y apportant notre touche moderne.'
    ],
    [
        'icone' => '❤️',
        'titre' => 'Passion',
        'description' => 'Chaque gâteau, chaque viennoiserie est créé avec amour et dédication pour vous offrir le meilleur.'
    ],
    [
        'icone' => '🌱',
        'titre' => 'Qualité',
        'description' => 'Nous privilégions les circuits courts et les producteurs locaux pour garantir la fraîcheur et la qualité.'
    ]
];

// Statistiques
$stats = [
    ['nombre' => 40, 'label' => 'Années d\'expérience'],
    ['nombre' => 500, 'label' => 'Clients satisfaits par jour'],
    ['nombre' => 50, 'label' => 'Créations différentes'],
    ['nombre' => 3, 'label' => 'Générations de savoir-faire']
];

// Fonction pour calculer l'âge de la pâtisserie
function calculerAge($annee_creation) {
    return date('Y') - $annee_creation;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À Propos - <?php echo $patisserie['nom']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'patisserie': {
                            'primary': '#8B4513',
                            'secondary': '#D2691E',
                            'accent': '#DAA520',
                            'cream': '#FFF8DC'
                        }
                    },
                    fontFamily: {
                        'serif': ['Georgia', 'serif']
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-fadeInUp { animation: fadeInUp 1s ease-out; }
        .animate-pulse-custom { animation: pulse 2s infinite; }
        .animate-bounce-custom { animation: bounce 3s ease-in-out infinite; }
        .animate-rotate { animation: rotate 20s linear infinite; }
    </style>
</head>
<body class="font-serif bg-gradient-to-br from-orange-100 via-amber-50 to-orange-200 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        
        <!-- Hero Section -->
        <div class="text-center py-16 bg-white/10 backdrop-blur-lg rounded-3xl mb-12 shadow-2xl border border-white/20 animate-fadeInUp">
            <h1 class="text-5xl md:text-6xl font-bold text-patisserie-primary mb-4 text-shadow">
                <?php echo $patisserie['nom']; ?>
            </h1>
            <p class="text-xl md:text-2xl text-patisserie-secondary italic">
                <?php echo $patisserie['slogan']; ?>
            </p>
            <div class="mt-6 flex justify-center items-center space-x-4">
                <span class="text-2xl">🥐</span>
                <span class="text-patisserie-primary font-semibold text-lg">
                    <?php echo calculerAge($patisserie['annee_creation']); ?> ans de tradition
                </span>
                <span class="text-2xl">🍰</span>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
            <?php foreach ($stats as $stat): ?>
            <div class="bg-white/90 p-6 rounded-2xl text-center shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border-l-4 border-patisserie-secondary">
                <div class="text-3xl font-bold text-patisserie-primary mb-2">
                    <?php echo $stat['nombre']; ?>+
                </div>
                <div class="text-sm text-gray-600 font-medium">
                    <?php echo $stat['label']; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Contenu principal -->
        <div class="grid lg:grid-cols-2 gap-8 mb-12">
            <!-- Notre Histoire -->
            <div class="bg-white/95 p-8 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-3 border-l-8 border-patisserie-secondary relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-patisserie-secondary via-patisserie-accent to-patisserie-primary transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                
                <div class="flex items-center mb-6">
                    <div class="w-8 h-8 bg-gradient-to-br from-patisserie-secondary to-patisserie-accent rounded-full mr-4 animate-pulse-custom"></div>
                    <h2 class="text-3xl font-bold text-patisserie-primary">Notre Histoire</h2>
                </div>
                
                <p class="text-gray-700 text-lg mb-4 leading-relaxed">
                    Fondée en <?php echo $patisserie['annee_creation']; ?> par 
                    <strong class="text-patisserie-primary">
                        <?php echo implode(' et ', $patisserie['fondateurs']); ?>
                    </strong>, 
                    la <?php echo $patisserie['nom']; ?> est née d'une passion commune pour l'art culinaire français.
                </p>
                
                <p class="text-gray-700 text-lg leading-relaxed">
                    Ce qui a commencé comme un petit atelier familial dans le cœur de 
                    <span class="font-semibold text-patisserie-secondary"><?php echo $patisserie['ville']; ?></span> 
                    s'est transformé en une véritable institution, perpétuant cette tradition d'excellence avec la même passion qui nous anime depuis le premier jour.
                </p>
            </div>

            <!-- Notre Philosophie -->
            <div class="bg-white/95 p-8 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-3 border-l-8 border-patisserie-accent relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-patisserie-accent via-patisserie-secondary to-patisserie-primary transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                
                <div class="flex items-center mb-6">
                    <div class="w-8 h-8 bg-gradient-to-br from-patisserie-accent to-patisserie-secondary rounded-full mr-4 animate-pulse-custom"></div>
                    <h2 class="text-3xl font-bold text-patisserie-primary">Notre Philosophie</h2>
                </div>
                
                <p class="text-gray-700 text-lg mb-4 leading-relaxed">
                    Chaque création qui sort de nos fours est le fruit d'un travail minutieux et passionné. Nous croyons que la pâtisserie est un art qui demande patience, précision et créativité.
                </p>
                
                <p class="text-gray-700 text-lg leading-relaxed">
                    Notre engagement ? Utiliser uniquement des ingrédients de première qualité, respecter les techniques traditionnelles tout en innovant, et offrir à nos clients des moments de pur bonheur gustatif.
                </p>
            </div>
        </div>

        <!-- Section Chef -->
        <div class="bg-white/98 p-8 md:p-12 rounded-3xl text-center shadow-2xl mb-12 relative overflow-hidden">
            <!-- Éléments décoratifs -->
            <div class="absolute top-4 right-4 text-6xl opacity-10 animate-rotate">🧁</div>
            <div class="absolute bottom-4 left-4 text-6xl opacity-10 animate-rotate" style="animation-direction: reverse;">🍰</div>
            
            <div class="w-32 h-32 md:w-40 md:h-40 bg-gradient-to-br from-patisserie-secondary to-patisserie-accent rounded-full mx-auto mb-6 flex items-center justify-center text-6xl md:text-7xl shadow-xl animate-bounce-custom">
                👨‍🍳
            </div>
            
            <h2 class="text-3xl md:text-4xl font-bold text-patisserie-primary mb-4">
                <?php echo $chef['nom']; ?>
            </h2>
            
            <div class="bg-patisserie-cream px-6 py-3 rounded-full inline-block mb-6">
                <span class="text-patisserie-primary font-semibold">
                    <?php echo $chef['experience']; ?> ans d'expérience • <?php echo $chef['formation']; ?>
                </span>
            </div>
            
            <p class="text-gray-700 text-lg max-w-3xl mx-auto mb-6 leading-relaxed">
                Formé dans les plus prestigieuses maisons parisiennes, notre chef pâtissier allie tradition et modernité pour créer des œuvres d'art comestibles. Chaque jour, il sélectionne personnellement les meilleurs ingrédients et supervise chaque étape de création.
            </p>
            
            <div class="flex flex-wrap justify-center gap-3">
                <span class="text-sm text-patisserie-secondary font-medium">Spécialités :</span>
                <?php foreach ($chef['specialites'] as $specialite): ?>
                <span class="bg-patisserie-secondary/10 text-patisserie-primary px-3 py-1 rounded-full text-sm font-medium border border-patisserie-secondary/20">
                    <?php echo $specialite; ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Nos Valeurs -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <?php foreach ($valeurs as $valeur): ?>
            <div class="bg-white/90 p-6 rounded-2xl text-center transition-all duration-300 hover:bg-white/95 hover:scale-105 hover:shadow-xl border-2 border-transparent hover:border-patisserie-secondary group">
                <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">
                    <?php echo $valeur['icone']; ?>
                </div>
                <h3 class="text-xl font-bold text-patisserie-primary mb-3">
                    <?php echo $valeur['titre']; ?>
                </h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    <?php echo $valeur['description']; ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Call to Action -->
        <div class="bg-gradient-to-r from-patisserie-primary to-patisserie-secondary text-white p-8 md:p-12 rounded-3xl text-center relative overflow-hidden shadow-2xl">
            <!-- Effet de motif animé -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-full h-full bg-repeat" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.1) 10px, rgba(255,255,255,0.1) 20px); animation: slide 10s linear infinite;"></div>
            </div>
            
            <div class="relative z-10">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">
                    Venez nous rencontrer
                </h2>
                <p class="text-xl mb-8 opacity-90">
                    Découvrez l'univers magique de notre pâtisserie et laissez-vous séduire par nos créations artisanales.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#contact" class="bg-white text-patisserie-primary px-8 py-4 rounded-full font-bold transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:bg-patisserie-cream inline-block">
                        📍 Nous contacter
                    </a>
                    <a href="#menu" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold transition-all duration-300 hover:bg-white hover:text-patisserie-primary inline-block">
                        🍰 Voir notre carte
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer avec informations dynamiques -->
        <div class="mt-12 text-center text-gray-600">
            <p class="text-sm">
                © <?php echo date('Y'); ?> <?php echo $patisserie['nom']; ?> - 
                Créée avec ❤️ à <?php echo $patisserie['ville']; ?> depuis <?php echo $patisserie['annee_creation']; ?>
            </p>
        </div>
    </div>

    <style>
        @keyframes slide {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>
</body>
</html>