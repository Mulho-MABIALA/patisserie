<?php
require_once '../config.php';

if(isAdmin()) {
    redirect('index.php');
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Version sans hashage (temporaire)
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $admin = $stmt->fetch();
    
    if($admin) {
        $_SESSION['admin_id'] = $admin['id'];
        redirect('index.php');
    } else {
        $erreur = "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Connexion</title>
    <?php echo getTailwindConfig(); ?>
</head>
<body class="bg-gradient-to-br from-secondary to-dark min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full shadow-lg mb-4">
                <i class="fas fa-gem text-accent text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-white">Administration</h1>
            <p class="text-primary mt-2">Bijouterie Élégance</p>
        </div>
        
        <!-- Login Form -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <?php if(isset($erreur)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo $erreur; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-secondary"></i>
                        Nom d'utilisateur
                    </label>
                    <input type="text" 
                           id="username"
                           name="username" 
                           required
                           placeholder="admin"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition duration-200">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-secondary"></i>
                        Mot de passe
                    </label>
                    <input type="password" 
                           id="password"
                           name="password" 
                           required
                           placeholder="••••••••"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition duration-200">
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
                    </label>
                    <a href="#" class="text-sm text-primary hover:underline">Mot de passe oublié?</a>
                </div>
                
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-secondary to-primary text-white py-3 rounded-lg font-semibold hover:shadow-lg transform hover:scale-105 transition duration-300">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Se connecter
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <a href="../index.php" class="text-sm text-gray-600 hover:text-primary transition duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour au site
                </a>
            </div>
        </div>
        
        <!-- Footer -->
        <p class="text-center text-gray-300 text-sm mt-8">
            © 2024 Bijouterie Élégance. Tous droits réservés.
        </p>
    </div>
</body>
</html>