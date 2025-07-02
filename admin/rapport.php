<?php
// fichier: rapport.php
require_once("../config.php");

// Récupération des données globales
$produits = $conn->query("SELECT COUNT(*) AS total FROM produits")->fetch_assoc()['total'];
$commandes = $conn->query("SELECT COUNT(*) AS total FROM commandes")->fetch_assoc()['total'];
$revenu = $conn->query("SELECT SUM(total) AS total_revenu FROM commandes")->fetch_assoc()['total_revenu'];
$clients = $conn->query("SELECT COUNT(DISTINCT email) AS total_clients FROM commandes")->fetch_assoc()['total_clients'];

// Commandes récentes
$liste_commandes = $conn->query("SELECT * FROM commandes ORDER BY date_commande DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport - Pâtisserie</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-pink-600 mb-6">Rapport Complet de la Pâtisserie</h1>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h2 class="text-xl font-semibold text-gray-700">Produits</h2>
                <p class="text-3xl font-bold text-pink-500 mt-2"><?= $produits ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h2 class="text-xl font-semibold text-gray-700">Commandes</h2>
                <p class="text-3xl font-bold text-pink-500 mt-2"><?= $commandes ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h2 class="text-xl font-semibold text-gray-700">Revenus (€)</h2>
                <p class="text-3xl font-bold text-pink-500 mt-2"><?= number_format($revenu, 2, ',', ' ') ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h2 class="text-xl font-semibold text-gray-700">Clients</h2>
                <p class="text-3xl font-bold text-pink-500 mt-2"><?= $clients ?></p>
            </div>
        </div>

        <!-- Commandes récentes -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Commandes récentes</h2>
            <table class="min-w-full table-auto">
                <thead class="bg-pink-200 text-pink-800">
                    <tr>
                        <th class="px-4 py-2">#ID</th>
                        <th class="px-4 py-2">Client</th>
                        <th class="px-4 py-2">Total (€)</th>
                        <th class="px-4 py-2">Statut</th>
                        <th class="px-4 py-2">Date</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php while ($row = $liste_commandes->fetch_assoc()): ?>
                        <tr class="border-t">
                            <td class="px-4 py-2"><?= $row['id'] ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['nom_client']) ?></td>
                            <td class="px-4 py-2"><?= number_format($row['total'], 2, ',', ' ') ?></td>
                            <td class="px-4 py-2"><?= ucfirst($row['statut']) ?></td>
                            <td class="px-4 py-2"><?= date("d/m/Y", strtotime($row['date_commande'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
