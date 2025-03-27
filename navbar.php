<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche</title>
    <!-- Inclure Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <style>
        /* Votre CSS ici */
        .bg-purple {
            background-color: #6f42c1; /* Couleur violet */
        }
        .navbar-brand {
            color: white !important; /* Couleur du texte de la marque */
            font-weight: bold; /* Mettre en gras */
        }
        .nav-link {
            color: white !important; /* Couleur du texte des liens */
            margin-right: 15px; /* Espacement entre les liens */
            transition: color 0.3s; /* Transition pour l'effet de survol */
        }
        .nav-link:hover {
            color: #f8f9fa !important; /* Couleur au survol */
        }
        .btn-outline-light {
            border-color: white; /* Couleur de la bordure */
            color: white; /* Couleur du texte */
        }
        .btn-outline-light:hover {
            background-color: white; /* Couleur de fond au survol */
            color: #6f42c1; /* Couleur du texte au survol */
        }
        .btn-danger {
            background-color: #dc3545; /* Couleur rouge pour le bouton */
            border: none; /* Pas de bordure */
        }
        .btn-danger:hover {
            background-color: #c82333; /* Couleur au survol */
            color: white; /* Couleur du texte au survol */
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-purple fixed-top">
    <div class="container">
        <a class="navbar-brand" href="/">ChristianUnion</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="recherche.php">
                        <i class="bi bi-search"></i> Rechercher
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="messages.php">
                        <i class="bi bi-envelope"></i> Messages <span class="badge bg-danger" id="msgCount">0</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="profil.php" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person"></i> Mon Profil
                    </a>
                    <ul class="dropdown-menu" aria-labelledby=navbarDropdown">
                        <li><a class="dropdown-item" href="/parametres.php">Paramètres</a></li>
                        <li><a class="dropdown-item" href="deconnexion.php">Déconnexion</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-light" href="profil.php"><i class="bi bi-person"></i> Mon Profil</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-danger" href="parametre.php">
                    <i class="fa fa-name-cog">parametre</i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Inclure Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>