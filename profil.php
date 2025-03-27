<?php
session_start();
require 'includes/config.php'; // Assurez-vous que ce fichier contient la connexion à la base de données

// Récupérer les informations de l'utilisateur connecté
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT * FROM users WHERE id = ?";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch();

// Récupérer les informations spirituelles de l'utilisateur
$sql_profil = "SELECT denomination, niveau_pratique  FROM profils WHERE utilisateur_id = ?";
$stmt_profil = $pdo->prepare($sql_profil);
$stmt_profil->execute([$user_id]);
$profil = $stmt_profil->fetch();



// Inclure le fichier de la navbar
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - ChristianUnion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Style pour arrondir la photo de profil */
        .img-profile {
            border-radius: 50%; /* Arrondir la photo */
            max-width: 150px; /* Largeur maximale */
            height: auto; /* Hauteur automatique pour garder le ratio */
        }
    </style>
</head>
<body>
   
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <!-- Navbar content -->
    </nav>

    <div class="container mt-5 pt-5">
        <div class="row">
            <!-- Colonne de gauche - Photo et infos principales -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <img src="<?php echo htmlspecialchars($user['photo_profil']); ?>" alt="Photo de profil" class="img-profile mb-3">
                        <h4><?php echo htmlspecialchars($user['prenom']) . ' ' . htmlspecialchars($user['nom']); ?></h4>
                        <div class="list-group">
                           
                            <div class="list-group-item d-flex align-items-center">
                                <i class="bi bi-gender-ambiguous me-2"></i>
                                <strong>Genre :  </strong> <?php echo htmlspecialchars($user['sexe']); ?>
                            </div>
                            <div class="list-group-item d-flex align-items-center">
                                <i class="bi bi-geo-alt-fill me-2"></i>
                                <strong>Ville :  </strong> <?php echo htmlspecialchars($user['ville']); ?>
                            </div>
                            <div class="list-group-item d-flex align-items-center">
                                <i class="bi bi-star-fill me-2"></i>
                                <strong>Dénomination :  </strong> <?php echo htmlspecialchars($profil['denomination']); ?>
                            </div>
                            <div class="list-group-item d-flex align-items-center">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <strong>Niveau de patique :  </strong> <?php echo htmlspecialchars($profil['niveau_pratique']); ?>
                            </div>
                            <div class="list-group-item d-flex align-items-center">
                               <p class="mt-3"><strong>Description :  </strong><br> <?php echo htmlspecialchars($user['descri']); ?></p>
                            </div>
                        </div><br>

                        <form action="update_profil.php" method="POST" enctype="multipart/form-data" class="d-inline">
                            <input type="file" name="photo_profil" id="photo_profil" accept="image/*" style="display: none;" onchange="this.form.submit();">
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('photo_profil').click();">Modifier la photo de profil</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Colonne de droite - Formulaire de mise à jour -->
            <div class="col-md-8">
                <h2>Modifier mon profil</h2>
                <form action="update_profil.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" name="prenom" id="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" name="nom" id="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <p class="form-control-plaintext">Genre : <?php echo htmlspecialchars($user['sexe']); ?></p>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description" rows="4" required> <?php echo htmlspecialchars($user['descri']); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Mettre à jour le profil</button>
                </form>
                <p class="mt-3 text-muted">Pour modifier votre prénom, nom ou genre, veuillez contacter le service support.</p>
            </div>
        </div>
    </div>

    <footer class="bg-light py-4 mt-5">
        <!-- Footer content -->
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 