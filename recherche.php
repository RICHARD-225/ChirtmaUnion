<?php
session_start();
require 'includes/config.php'; // Assurez-vous que ce fichier contient la connexion à la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

// Initialiser les variables de recherche
$ageMin = '';
$ageMax = '';
$lieu = '';
$denomination = '';
$pratique = '';
$genre = '';
$resultats = [];

// Initialiser une variable pour vérifier si des filtres sont appliqués
$filtersApplied = false;

// Traitement du formulaire de recherche
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des valeurs des filtres
    $ageMin = $_POST['ageMin'] ?? '';
    $ageMax = $_POST['ageMax'] ?? '';
    $lieu = $_POST['ville'] ?? '';
    $denomination = $_POST['denomination'] ?? '';
    $pratique = $_POST['pratique'] ?? '';
    $genre = $_POST['genre'] ?? '';

    // Requête pour rechercher des profils
    $sql = "SELECT p.*, u.prenom, u.ddn AS date_naissance, u.ville, u.descri AS description, u.photo_profil, p.niveau_pratique AS pratique 
            FROM profils p 
            JOIN users u ON p.utilisateur_id = u.id 
            WHERE 1=1"; // Condition de base pour la requête

    $params = []; // Tableau pour stocker les paramètres de la requête

    // Ajout des conditions en fonction des filtres
    if (!empty($ageMin)) {
        $sql .= " AND YEAR(CURDATE()) - YEAR(u.ddn) >= ?";
        $params[] = $ageMin;
        $filtersApplied = true; // Un filtre est appliqué
    }

    if (!empty($ageMax)) {
        $sql .= " AND YEAR(CURDATE()) - YEAR(u.ddn) <= ?";
        $params[] = $ageMax;
        $filtersApplied = true; // Un filtre est appliqué
    }

    if (!empty($lieu) && $lieu !== 'tous') {
        $sql .= " AND u.ville = ?";
        $params[] = $lieu;
        $filtersApplied = true; // Un filtre est appliqué
    }

    if (!empty($denomination) && $denomination !== 'tous') {
        $sql .= " AND u.eglise = ?";
        $params[] = $denomination; // Utilisation de 'eglise' pour la dénomination
        $filtersApplied = true; // Un filtre est appliqué
    }

    if (!empty($pratique) && $pratique !== 'tous') {
        $sql .= " AND p.niveau_pratique = ?";
        $params[] = $pratique;
        $filtersApplied = true; // Un filtre est appliqué
    }

    if (!empty($genre) && $genre !== 'tous') {
        $sql .= " AND u.sexe = ?";
        $params[] = $genre; // Utilisation de 'sexe' pour le genre
        $filtersApplied = true; // Un filtre est appliqué
    }

    // Si aucun filtre n'est appliqué, afficher un message
    if (!$filtersApplied) {
        echo "Veuillez sélectionner au moins un critère de recherche.<br>";
    } else {
        // Préparation et exécution de la requête
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Vérification des résultats
        if (empty($resultats)) {
            echo "Aucun résultat trouvé avec les critères spécifiés.<br>";
        } else {
            // Afficher les résultats
            // ... votre code pour afficher les résultats ...
        }
    }
}

// Inclure le fichier de la navbar
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche - ChristianUnion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!-- <link href="css/profile.css" rel="stylesheet"> -->
   
</head>
<body>
    <!-- Navbar incluse ici -->
    

    <div class="container mt-5 pt-5">
        <!-- Filtres de recherche -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="searchForm" method="POST" action="">
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Âge</label>
                            <div class="d-flex gap-2">
                                <input type="number" name="ageMin" class="form-control" placeholder="Min" min="20" max="99" value="<?php echo htmlspecialchars($ageMin); ?>">
                                <input type="number" name="ageMax" class="form-control" placeholder="Max" min="20" max="99" value="<?php echo htmlspecialchars($ageMax); ?>">
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Ville</label>
                            <select name="ville" class="form-select">
                                <option value="">choisir</option>
                                <option value="tous" <?php echo ($lieu === 'tous') ? 'selected' : ''; ?>>Tous</option>
                                <option value="abidjan" <?php echo ($lieu === 'abidjan') ? 'selected' : ''; ?>>Abidjan</option>
                                <option value="yamoussoukro" <?php echo ($lieu === 'yamoussoukro') ? 'selected' : ''; ?>>Yamoussoukro</option>
                                <option value="bouake" <?php echo ($lieu === 'bouake') ? 'selected' : ''; ?>>Bouaké</option>
                                <option value="daloa" <?php echo ($lieu === 'daloa') ? 'selected' : ''; ?>>Daloa</option>
                                <option value="san_pedro" <?php echo ($lieu === 'san_pedro') ? 'selected' : ''; ?>>San Pedro</option>
                                <option value="korhogo" <?php echo ($lieu === 'korhogo') ? 'selected' : ''; ?>>Korhogo</option>
                                <option value="man" <?php echo ($lieu === 'man') ? 'selected' : ''; ?>>Man</option>
                                <option value="abengourou" <?php echo ($lieu === 'abengourou') ? 'selected' : ''; ?>>Abengourou</option>
                                <option value="gagnoa" <?php echo ($lieu === 'gagnoa') ? 'selected' : ''; ?>>Gagnoa</option>
                                <option value="sikasso" <?php echo ($lieu === 'sikasso') ? 'selected' : ''; ?>>Sikasso</option>
                                <option value="seguela" <?php echo ($lieu === 'seguela') ? 'selected' : ''; ?>>Séguéla</option>
                                <option value="bongouanou" <?php echo ($lieu === 'bongouanou') ? 'selected' : ''; ?>>Bongouanou</option>
                                <option value="katiola" <?php echo ($lieu === 'katiola') ? 'selected' : ''; ?>>Katiola</option>
                                <option value="odienne" <?php echo ($lieu === 'odienne') ? 'selected' : ''; ?>>Odienné</option>
                                <option value="issia" <?php echo ($lieu === 'issia') ? 'selected' : ''; ?>>Issia</option>
                                <option value="treichville" <?php echo ($lieu === 'treichville') ? 'selected' : ''; ?>>Treichville</option>
                                <option value="port_bouet" <?php echo ($lieu === 'port_bouet') ? 'selected' : ''; ?>>Port-Bouët</option>
                                <option value="marcory" <?php echo ($lieu === 'marcory') ? 'selected' : ''; ?>>Marcory</option>
                                <option value="cocody" <?php echo ($lieu === 'cocody') ? 'selected' : ''; ?>>Cocody</option>
                                <option value="attecoubé" <?php echo ($lieu === 'attecoubé') ? 'selected' : ''; ?>>Attécoubé</option>
                                <option value="abobo" <?php echo ($lieu === 'abobo') ? 'selected' : ''; ?>>Abobo</option>
                                <option value="aboisso" <?php echo ($lieu === 'aboisso') ? 'selected' : ''; ?>>Aboisso</option>
                                <option value="ferkessedougou" <?php echo ($lieu === 'ferkessedougou') ? 'selected' : ''; ?>>Ferkessédougou</option>
                                <option value="toumodi" <?php echo ($lieu === 'toumodi') ? 'selected' : ''; ?>>Toumodi</option>
                                <option value="lakota" <?php echo ($lieu === 'lakota') ? 'selected' : ''; ?>>Lakota</option>
                                <option value="tanda" <?php echo ($lieu === 'tanda') ? 'selected' : ''; ?>>Tanda</option>
                                <option value="toulepleu" <?php echo ($lieu === 'toulepleu') ? 'selected' : ''; ?>>Toulepleu</option>
                                <option value="grand_bassam" <?php echo ($lieu === 'grand_bassam') ? 'selected' : ''; ?>>Grand-Bassam</option>
                                <option value="bingerville" <?php echo ($lieu === 'bingerville') ? 'selected' : ''; ?>>Bingerville</option>
                                <option value="yopougon" <?php echo ($lieu === 'yopougon') ? 'selected' : ''; ?>>Yopougon</option>
                                <option value="agnibilekrou" <?php echo ($lieu === 'agnibilekrou') ? 'selected' : ''; ?>>Agnibilékrou</option>
                                <option value="bondoukou" <?php echo ($lieu === 'bondoukou') ? 'selected' : ''; ?>>Bondoukou</option>
                                <option value="guiglo" <?php echo ($lieu === 'guiglo') ? 'selected' : ''; ?>>Guiglo</option>
                                <option value="zuenoula" <?php echo ($lieu === 'zuenoula') ? 'selected' : ''; ?>>Zuenoula</option>
                                <option value="sassandra" <?php echo ($lieu === 'sassandra') ? 'selected' : ''; ?>>Sassandra</option>
                                <option value="tiapoum" <?php echo ($lieu === 'tiapoum') ? 'selected' : ''; ?>>Tiapoum</option>
                                <option value="koun_fao" <?php echo ($lieu === 'koun_fao') ? 'selected' : ''; ?>>Koun-Fao</option>
                                <option value="oume" <?php echo ($lieu === 'oume') ? 'selected' : ''; ?>>Oumé</option>
                                <option value="agboville" <?php echo ($lieu === 'agboville') ? 'selected' : ''; ?>>Agboville</option>
                                <option value="fresco" <?php echo ($lieu === 'fresco') ? 'selected' : ''; ?>>Fresco</option>
                                <option value="prikro" <?php echo ($lieu === 'prikro') ? 'selected' : ''; ?>>Prikro</option>
                                <option value="tiebissou" <?php echo ($lieu === 'tiebissou') ? 'selected' : ''; ?>>Tiebissou</option>
                                <option value="grand_lahou" <?php echo ($lieu === 'grand_lahou') ? 'selected' : ''; ?>>Grand-Lahou</option>
                                <option value="divo" <?php echo ($lieu === 'divo') ? 'selected' : ''; ?>>Divo</option>
                                <option value="dimbokro" <?php echo ($lieu === 'dimbokro') ? 'selected' : ''; ?>>Dimbokro</option>
                                <option value="sanwi" <?php echo ($lieu === 'sanwi') ? 'selected' : ''; ?>>Sanwi</option>
                                <option value="bouna" <?php echo ($lieu === 'bouna') ? 'selected' : ''; ?>>Bouna</option>
                                <option value="kani" <?php echo ($lieu === 'kani') ? 'selected' : ''; ?>>Kani</option>
                                <option value="sinfra" <?php echo ($lieu === 'sinfra') ? 'selected' : ''; ?>>Sinfra</option>  
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="denomination" class="form-label">Eglise</label>
                            <select name="denomination" class="form-select">
                                <option value="">choisir</option>
                                <option value="tous" <?php echo ($denomination === 'tous') ? 'selected' : ''; ?>>Tous</option>
                                <option value="evangelique" <?php echo ($denomination === 'Evengelique') ? 'selected' : ''; ?>>Évangélique</option>
                                <option value="pentecote" <?php echo ($denomination === 'pentecote') ? 'selected' : ''; ?>>Pentecôte</option>
                                <option value="catholique" <?php echo ($denomination === 'catholique') ? 'selected' : ''; ?>>Catholique</option>
                                <option value="baptiste" <?php echo ($denomination === 'baptiste') ? 'selected' : ''; ?>>Baptiste</option>
                                <option value="methodiste" <?php echo ($denomination === 'methodiste') ? 'selected' : ''; ?>>Méthodiste</option>
                                <option value="protestant" <?php echo ($denomination === 'protestant') ? 'selected' : ''; ?>>Protestant</option>
                                <option value="celeste" <?php echo ($denomination === 'celeste') ? 'selected' : ''; ?>>Celeste</option>
                                <option value="bethesda" <?php echo ($denomination === 'bethesda') ? 'selected' : ''; ?>>Bethesda</option>
                                <option value="assemble_de_dieu" <?php echo ($denomination === 'assemble_de_dieu') ? 'selected' : ''; ?>>Assemblée de Dieu</option>
                                <option value="temoin_de_jehova" <?php echo ($denomination === 'temoin_de_jehova') ? 'selected' : ''; ?>>Temoins de Jehova</option>
                                <option value="CMA" <?php echo ($denomination === 'CMA') ? 'selected' : ''; ?>>CMA</option>
                                <option value=" Eglise missionnaire" <?php echo ($denomination === 'Eglise missionnaire') ? 'selected' : ''; ?>>Missionnaire</option>
                            
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="pratique" class="form-label">Pratique religieuse</label>
                            <select name="pratique" class="form-select">
                                <option value="">choisir</option>
                                <option value="tous" <?php echo ($pratique === 'tous') ? 'selected' : ''; ?>>Tous</option>
                                <option value="Plusieurs fois par semaine" <?php echo ($pratique === 'Plusieurs fois par semaine') ? 'selected' : ''; ?>>Plusieurs fois par semaine</option>
                                <option value="cahque dimanche" <?php echo ($pratique === 'Chaque dimanche') ? 'selected' : ''; ?>>Chaque dimanche</option>
                                <option value="Occasionnelle" <?php echo ($pratique === 'Occasionnelle') ? 'selected' : ''; ?>>Occasionnelle</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="genre" class="form-label">Genre</label>
                            <select name="genre" class="form-select">
                                <option value="">choisir</option>
                                <option value="tous" <?php echo ($genre === 'tous') ? 'selected' : ''; ?>>Tous</option>
                                <option value="Homme" <?php echo ($genre === 'Homme') ? 'selected' : ''; ?>>Homme</option>
                                <option value="Femme" <?php echo ($genre === 'Femme') ? 'selected' : ''; ?>>Femme</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Rechercher
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                                Réinitialiser les filtres
                            </button>  
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Résultats de recherche -->
        <div class="row" id="searchResults">
            <?php if (!empty($resultats)): ?>
                <?php foreach ($resultats as $profil): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 profile-card-search">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="<?php echo htmlspecialchars($profil['photo_profil'] ?: 'images/default-profile.jpg'); ?>" class="rounded-circle me-3" width="60" height="60">
                                    <div>
                                        <?php 
                                        // Calcul de l'âge
                                        $age = date('Y') - date('Y', strtotime($profil['date_naissance']));
                                        ?>
                                        <h5 class="card-title mb-0"><?php echo htmlspecialchars($profil['prenom']) . ', ' . $age . ' ans'; ?></h5>
                                        <p class="text-muted mb-0">
                                            <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($profil['ville']); ?>
                                        </p>
                                    </div>
                                </div>
                                <p class="card-text"><?php echo htmlspecialchars($profil['description']); ?></p>
                                <div class="mb-3">
                                    <span class="badge bg-light text-dark"><?php echo htmlspecialchars($profil['denomination']); ?></span>
                                    <span class="badge bg-light text-dark"><?php echo htmlspecialchars($profil['niveau_pratique']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                       <a class="btn btn-default" href="messages.php" role="button"> <i class="bi bi-chat"></i> Message</a>

                                    <button class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-heart"></i> Intéressé(e)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning" role="alert">
                        Aucun profil trouvé avec les critères de recherche spécifiés.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Navigation des pages" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Précédent</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Suivant</a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <!-- ... copier le footer de index.html ... -->
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/recherche.js"></script>
    <script>
    function resetFilters() {
        // Réinitialiser tous les champs du formulaire
        document.getElementById('searchForm').reset();

        // Optionnel : Réinitialiser les sélections personnalisées
        const selects = document.querySelectorAll('select');
        selects.forEach(select => {
            select.selectedIndex = 0; // Réinitialiser à la première option
        });
    }
    </script>
</body>
</html> 