<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil-Etude statistiques</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Chart.js pour les diagrammes -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- notre css -->
    <link rel="stylesheet" href="{{ asset('assets/css/css_accueil.css') }}">>
 
</head>
<body>

    <!-- Barre de défilement en haut -->
    <div class="ticker-container">
        <div class="ticker-text">Bienvenue sur la plateforme de la DGESS, dédiée à la gestion des études statistiques du MID. Conçue pour répondre à vos besoins, elle garantit accessibilité, fiabilité, sécurité et performance.</div>
    </div>

    <!-- Navbar horizontale dynamique (responsive avec Bootstrap) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid armoiries">
         <img src="{{ asset('assets/images/armoiries.JPEG') }}" class="d-block w-" alt="Image 1">
            <a class="navbar-brand" href="#">DGESS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Liens de navigation à gauche -->
                <ul class="navbar-nav navbar-nav-left">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Accueil</a>
                    </li>
                    <li class="nav-item">
                         <a class="nav-link" href="#stats-section">Statistiques</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">À Propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
                <!-- Bouton de connexion à droite -->
                <ul class="navbar-nav ms-auto">
    <li class="nav-item">
        <a class="btn connexion_btn" href="{{ route('login') }}">Connexion</a>
    </li>
</ul>

            </div>
        </div>
    </nav>

    <!-- Section Slider (Carousel Bootstrap pour images dynamiques) -->
    <div id="carouselExampleIndicators" class="carousel slide mt-2" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
            <img src="{{ asset('assets/images/im4.jpg') }}" class="d-block w-100" alt="Image 1">
                <div class="carousel-caption">
                    <h1>Suivre et évaluer vos projets d'investissement.</h1>
                    <p>vous accompagner au quotidien et vous offrir une expérience utilisateur optimale.</p>
                </div>
            </div>
            <div class="carousel-item">
            <img src="{{ asset('assets/images/im2.jpg') }}" class="d-block w-100" alt="Image 2">
                <div class="carousel-caption">
                    <h1>Élaborer vos activités</h1>
                    <p>vous accompagner au quotidien et vous offrir une expérience utilisateur optimale.</p>
                </div>
            </div>
            <div class="carousel-item">
            <img src="{{ asset('assets/images/im5.jpg') }}" class="d-block w-100" alt="Image 3">
                <div class="carousel-caption">
                    <h1>Élaborer vos annuaires statistiques.</h1>
                    <p>vous accompagner au quotidien et vous offrir une expérience utilisateur optimale.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Précédent</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Suivant</span>
        </button>
    </div>

    <!-- Section Statistiques avec Diagramme et Tableau -->
    <section class="stats-section" id="stats-section" >
        <div class="container">
            <h2 class="text-center mb-4">Quelques statistiques</h2>
            <!-- Diagramme (Chart.js - Exemple de Bar Chart) -->
            <div id="chartContainer" class="col-6">
               <h3 class="text-center">Évolution de l’état du réseau routier (en %)</h3>
                <canvas id="myChart"></canvas>
            </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <!-- Informations de l'entreprise -->
                <div class="col-md-4 text-center text-md-start">
                    <h5>DGESS</h5>
                    <p>vous accompagner au quotidien et vous offrir une expérience utilisateur optimale.</p>
                </div>
                <!-- Liens rapides -->
                <div class="col-md-4 text-center">
                    <h5>Liens Rapides</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Accueil</a></li>
                        <li><a href="#">Statistiques</a></li>
                        <li><a href="#">À Propos</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <!-- Réseaux sociaux -->
                <div class="col-md-4 text-center text-md-end">
                    <h5>Suivez-nous</h5>
                    <div class="social-icons">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <hr class="bg-light">
            <div class="text-center">
                <p>&copy; 2025 DSI/MID. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- JS personnalisé pour le chart -->
    <script src="{{ asset('assets/js/accueil.js') }}"></script>
    <!-- Inclure les icônes Bootstrap pour les réseaux sociaux -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>
</html>