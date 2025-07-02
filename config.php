<?php
session_start();

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'bijoux');
define('DB_USER', 'root');
define('DB_PASS', '');

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES 'utf8'");
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonction pour vérifier si l'admin est connecté
function isAdmin() {
    return isset($_SESSION['admin_id']);
}

// Fonction pour rediriger
function redirect($url) {
    header("Location: $url");
    exit();
}
// Configuration Tailwind personnalisée
function getTailwindConfig() {
    return "
    <script src='https://cdn.tailwindcss.com'></script>
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
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
    ";
}
?>