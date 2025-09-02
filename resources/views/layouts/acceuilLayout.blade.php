<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'Accueil Professionnelle</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Chart.js pour les diagrammes -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- notre css -->
    <link rel="stylesheet" href="{{ asset('css/accueil_css') }}">

    <link rel="stylesheet" href="{{ asset('css/accueil_js') }}">
 
</head>
<body>

    <!-- Barre de défilement en haut -->
    <div class="ticker-container">
        <div class="ticker-text">Bienvenue sur notre site professionnel - Découvrez nos services et solutions innovantes !</div>
    </div>

    <!-- Navbar horizontale dynamique (responsive avec Bootstrap) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Mon Site Pro</a>
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
                        <a class="nav-link" href="#">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">À Propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
                <!-- Bouton de connexion à droite -->
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item">
                        <a class="btn text-black" style="color: #EEEE14;" href="#">Connexion</a>
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
                <img src="im1.jpg" class="d-block w-100" alt="Image 1" >
                <div class="carousel-caption">
                    <h5>Titre de l'Image 1</h5>
                    <p>Description de l'image 1.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="im2.jpg" class="d-block w-100" alt="Image 2">
                <div class="carousel-caption">
                    <h5>Titre de l'Image 2</h5>
                    <p>Description de l'image 2.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="im3.jpg" class="d-block w-100" alt="Image 3">
                <div class="carousel-caption">
                    <h5>Titre de l'Image 3</h5>
                    <p>Description de l'image 3.</p>
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
    <section class="stats-section">
        <div class="container">
            <h2 class="text-center mb-4">Statistiques</h2>
            <!-- Diagramme (Chart.js - Exemple de Bar Chart) -->
            <div class="chart-container">
                <canvas id="myChart"></canvas>
            </div>
            <!-- Tableau Bootstrap -->
            <div class="table-container">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Catégorie</th>
                            <th scope="col">Valeur</th>
                            <th scope="col">Pourcentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">1</th>
                            <td>Ventes</td>
                            <td>1500</td>
                            <td>30%</td>
                        </tr>
                        <tr>
                            <th scope="row">2</th>
                            <td>Visiteurs</td>
                            <td>5000</td>
                            <td>50%</td>
                        </tr>
                        <tr>
                            <th scope="row">3</th>
                            <td>Produits</td>
                            <td>200</td>
                            <td>20%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <!-- Informations de l'entreprise -->
                <div class="col-md-4 text-center text-md-start">
                    <h5>Mon Site Pro</h5>
                    <p>Nous offrons des solutions innovantes pour votre entreprise.</p>
                </div>
                <!-- Liens rapides -->
                <div class="col-md-4 text-center">
                    <h5>Liens Rapides</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Accueil</a></li>
                        <li><a href="#">Services</a></li>
                        <li><a href="#">À Propos</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <!-- Réseaux sociaux -->
                <div class="col-md-4 text-center text-md-end">
                    <h5>Suivez-nous</h5>
                    <div class="social-icons">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
            </div>
            <hr class="bg-light">
            <div class="text-center">
                <p>&copy; 2025 Mon Site Pro. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- JS personnalisé pour le chart -->
    <!-- Inclure les icônes Bootstrap pour les réseaux sociaux -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>
</html>