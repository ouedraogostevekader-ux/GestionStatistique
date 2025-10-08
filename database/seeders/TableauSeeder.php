<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Cellule;
use App\Models\ColonneTableau;
use App\Models\LigneTableau;
use App\Models\Tableau;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TableauSeeder extends Seeder
{
    public function run(): void
    {
        $categorie1 = Categorie::create(
            ['libelle' => 'LES INFRASTRUCTURES ROUTIÈRES']
        );

        $regions = [
            'Régions',
            'Boucle du Mouhoun',
            'Cascades',
            'Centre',
            'Centre-Est',
            'Centre-Nord',
            'Centre-Ouest',
            'Centre-Sud',
            'Est',
            'Hauts-Bassins',
            'Nord',
            'Plateau-Central',
            'Sahel',
            'Sud-Ouest',
            'Ensemble'
        ];
        $provinces = [
            'Provinces',
            'Banwa',
            'Kossi',
            'Mouhoun',
            'Comoé',
            'Léraba',
            'Kadiogo',
            'Bazèga',
            'Ganzourgou',
            'Kourwéogo',
            'Boulgou',
            'Koulpélogo',
            'Kouritenga',
            'Bam',
            'Sanmatenga',
            'Namentenga',
            'Sissili',
            'Ziro',
            'Bazèga',
            'Nahouri',
            'Gnagna',
            'Gourma',
            'Komondjari',
            'Kompièga',
            'Tapoa',
            'Houet',
            'Kénédougou',
            'Tuy',
            'Loroum',
            'Passoré',
            'Yatenga',
            'Zondoma',
            'Kourwéogo',
            'Oubritenga',
            'Oubritenga',
            'Kouritenga',
            'Séno',
            'Soum',
            'Oudalan',
            'Yagha',
            'Bougouriba',
            'Ioba',
            'Noumbiel',
            'Poni',
            'Zoundwéogo',
            'Ensemble'
        ];
        $annees = [
            '2014',
            '2015',
            '2016',
            '2017',
            '2018',
            '2019',
            '2020',
            '2021',
            '2022',
            '2023',
            '2024',
            '2025',
            '2026',
        ];
        $tabRB = [
            'Tableau I-9: Linéaire des routes nationales selon la classification technique (en km)',
            'Tableau I-10: Linéaire des routes régionales selon la classification technique (en km)',
            'Tableau I-11: Linéaire des routes départementales selon la classification technique (en km)',
        ];

        $tabR = [
            'Tableau I-14: Linéaire de routes entretenues par l’entretien courant par région (en km)',
            'Tableau I-15: Linéaire de routes entretenues par l’entretien périodique par région (en km)',

        ];

        $tabE = [
            'Tableau I-17: Évolution de l’état du réseau routier (en %)',
            'Tableau I-18: Évolution de l’état du réseau routier dans la Boucle du Mouhoun (en km)',
            'Tableau I-19: Évolution de l’état du réseau routier dans les Cascades (en km)',
            'Tableau I-20: Évolution de l’état du réseau routier dans le Centre (en km)',
            'Tableau I-21: Évolution de l’état du réseau routier dans le Centre-Est (en km)',
            'Tableau I-22: Évolution de l’état du réseau routier dans le Centre-Nord (en km)',
            'Tableau I-23: Évolution de l’état du réseau routier dans le Centre-Ouest (en km)',
            'Tableau I-24: Évolution de l’état du réseau routier dans le Centre-Sud (en km)',
            'Tableau I-25: Évolution de l’état du réseau routier dans l’Est (en km)',
            'Tableau I-26: Évolution de l’état du réseau routier dans les Hauts-Bassins (en km)',
            'Tableau I-27: Évolution de l’état du réseau routier dans le Nord (en km)',
            'Tableau I-28: Évolution de l’état du réseau routier dans le Plateau Central (en km)',
            'Tableau I-29: Évolution de l’état du réseau routier dans le Sahel (en km)',
            'Tableau I-30: Évolution de l’état du réseau routier dans le Sud-Ouest (en km)',
        ];
        $RoutesN = [
            'Routes',
            'Routes Nationales',
            'Routes Régionales',
            'Routes Départementales',
            'Pistes Rurales',
            'Ensemble'
        ];

        $tabA = [
            'Tableau I-34: Nombre de communes reliées au chef-lieu de province par au moins une route ou piste en toute saison de l’année par région',
            'Tableau I-35: Évolution du linéaire de voiries bitumées par région (en km)',
            'Tableau I-36: Linéaire de voiries bitumées par année et par région (en km)',
            'Tableau I-44: Localités desservies par de nouvelles routes et pistes rurales par année et par région',
            'Tableau I-51: Évolution du linéaire de pistes rurales aménagées par région (en km)',
            'Tableau I-53: Linéaire de pistes rurales aménagées par région et par année (en km)',
            'Tableau I-55: Linéaire des pistes rurales entretenues par région et par année (en km)'
        ];
        $tabP = [
            'Tableau I-52:Evolution du linéaire de pistes rurales aménagées par province (en km)',
            'Tableau I-54: Linéaire de pistes rurales aménagées par province et par années (en km)',
        ];

        $RoutesB = [
            'Types Routes',
            'Routes bitumées',
            'Routes en terre moderne',
            'Routes en terre ordinaire',
            'Routes améliorées de type A',
            'Routes améliorées de type B',
            'Pistes ordinaires',
            'Ensemble'
        ];
        $butimes = [
            'Linéaire butimée',
            'Linéaire non butimée',
            'Linéaire totale'
        ];

        $etats = [
            'Etat',
            'Bon',
            'Moyen',
            'Mauvais',
            'En travaux',
            'Total'
        ];

        $typeO = [
            'Type d\'ouvrage',
            'Radiers',
            'Buses',
            'Dalots',
            'Ponts',
            'Echangeur',
        ];

        $routesC = [
            'Routes communautaire',
            'CU2A',
            'CU7B',
            'CU08',
            'CU09',
            'CU12',
            'CU13',
            'CU14',
            'CU15',
            'CU20',
            'CU22',
            'CU23',
            'CU25',
            'CU26',
            'CU27',
            'Ensemble'
        ];

        $rb = [
            'Classification nationale (RN, RR, RD)',
            'Linéaire bitumé',
            'Linéaire non bitumé',
            'Linéaire total (km'
        ];

        $tr = [
            'Type de routes',
            'Routes bitumées renforcées/réhabilitées en cours',
            'Routes bitumées renforcées/réhabilitées achevées'
        ];

        $entretiens = [
            'Type entretien',
            'Entretien périodique',
            'Construction et bitumage',
            'Réhabilitation/Renforcement',
            'Aménagement de pistes rurales',
            'Construction d’ouvrages',
            'Études géotechniques',
        ];

        $typesT = [
            'Type entretien',
            'Entretien périodique',
            'Construction et bitumage',
            'Réhabilitation/Renforcement',
            'Aménagement de pistes rurales',
            'Construction d’ouvrages',
            'Construction de bâtiment',
            'Étude de sol de fondation',
            'Expertise de bâtiment',
            'Études géotechniques',
        ];

        $tyTs = [
            'Type de travaux',
            'Bitumage',
            'Renforcement et réhabilitation',
            'Entretien périodique',
            'Piste rurale',
            'Ouvrages',
            'Ensemble',
        ];

        $travauxR = [
            'Travaux réalisés',
            'Salles de classe Construites',
            'Écoles construites',
            'Dispensaire',
            'Mur de clôture',
            'Forages',
            'Construction de DPID',
            'Maison de la femme',
            'Logement',
            'Construction de maison des jeunes',
            'Construction de l’atelier multi fonctionnel',
            'Éclairage public (en ml)',
        ];

        $typR = [
            'Type de travaux',
            'Étude',
            'Bitumage',
            'Renforcement et réhabilitation',
            'Entretien périodique',
            'Entretien courant',
            'Piste rurale',
            'Ouvrages',
            'Total',
        ];

        $rubriques = [
            'Perturbés',
            'Suspendus',
            'Arrêtés',
        ];

        $RNs = [
            'Route bitumée',
            'Route nationale N°1',
            'Route nationale N°2',
            'Route nationale N°3',
            'Route nationale N°4',
            'Route nationale N°5',
            'Route nationale N°6',
            'Route nationale N°7',
            'Route nationale N°8',
            'Route nationale N°9',
            'Route nationale N°10',
            'Route nationale N°12',
            'Route nationale N°13',
            'Route nationale N°14',
            'Route nationale N°15',
            'Route nationale N°16',
            'Route nationale N°17',
            'Route nationale N°18',
            'Route nationale N°19',
            'Route nationale N°20',
            'Route nationale N°21',
            'Route nationale N°22',
            'Route nationale N°23',
            'Route nationale N°27',
            'Route nationale N°28',
            'Route nationale N°29',
            'Route Régionale 18',
            'Route Régionale 21',
            'Route Départementale 40',
            'Ensemble'
        ];

        $ps = [
            'Types personne',
            'Personnes physiques',
            'Personnes morales',
        ];

        $emplois = [
            'Type de Travaux',
            'Entretien courant',
            'Entretien périodique',
            'Construction et bitumage',
            'Réhabilitation et Renforcement',
            'Aménagement de pistes rurales',
            'Travaux HIMO',
            'Construction d’ouvrages',
            'Études',
            'Gardiennage de barrières de pluies',
            'Travaux en régie',
            'Comptage de trafic routier',
            'Ensemble',
        ];


        $tableau1 = Tableau::create(
            [
                'libelle' => 'Tableau I-1: Réseau routier classé et de pistes rurales (en km)',
                'description' => 'Sources : - Décret No2000-269/PRES/PM/MIHU portant classification des routes au
                                Burkina Faso du 21 juin 2000 ;
                                - Répertoire général des pistes rurales, Juillet 1999.',
                'categorie_id' => $categorie1->id
            ]
        );
        LigneTableau::create(
            [
                'nom_ligne' => 'Classe de routes',
                'ordre' => 1,
                'tableau_id' => $tableau1->id
            ]
        );
        $ligne2 = LigneTableau::create(
            [
                'nom_ligne' => 'Routes classées',
                'ordre' => 2,
                'tableau_id' => $tableau1->id
            ]
        );
        $ligne3 = LigneTableau::create(
            [
                'nom_ligne' => 'Routes nationales',
                'ordre' => 3,
                'tableau_id' => $tableau1->id,
                'parent_id' => $ligne2->id
            ]
        );
        $ligne4 = LigneTableau::create(
            [
                'nom_ligne' => 'Routes régionales',
                'ordre' => 4,
                'tableau_id' => $tableau1->id,
                'parent_id' => $ligne2->id
            ]
        );
        $ligne5 = LigneTableau::create(
            [
                'nom_ligne' => 'Routes départementales',
                'ordre' => 5,
                'tableau_id' => $tableau1->id,
                'parent_id' => $ligne2->id
            ]
        );
        $ligne6 = LigneTableau::create(
            [
                'nom_ligne' => 'Pistes rurales',
                'ordre' => 6,
                'tableau_id' => $tableau1->id
            ]
        );
        $ligne7 = LigneTableau::create(
            [
                'nom_ligne' => 'Ensemble',
                'ordre' => 7,
                'tableau_id' => $tableau1->id
            ]
        );

        $colonne1 = ColonneTableau::create(
            [
                'colonne_ligne' => 'Linéaire',
                'ordre' => 1,
                'tableau_id' => $tableau1->id
            ]
        );
        Cellule::create([
            'ligne_id' => $ligne2->id,
            'colonne_id' => $colonne1->id,
        ]);
        Cellule::create([
            'ligne_id' => $ligne3->id,
            'colonne_id' => $colonne1->id,
        ]);
        Cellule::create([
            'ligne_id' => $ligne4->id,
            'colonne_id' => $colonne1->id,
        ]);
        Cellule::create([
            'ligne_id' => $ligne5->id,
            'colonne_id' => $colonne1->id,
        ]);
        Cellule::create([
            'ligne_id' => $ligne6->id,
            'colonne_id' => $colonne1->id,
        ]);
        Cellule::create([
            'ligne_id' => $ligne7->id,
            'colonne_id' => $colonne1->id,
        ]);

        $tableau2 = Tableau::create(
            [
                'libelle' => 'Tableau I-2: Linéaire des routes classées par région (en km)',
                'description' => 'Source : Décret No2000-269/PRES/PM/MIHU portant classification des routes au Burkina Faso du 21 juin 2000',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($regions as $index => $region) {
            LigneTableau::create([
                'nom_ligne'   => $region,
                'ordre'       => $index + 1, // incrément de 1 à 13
                'tableau_id'  => $tableau2->id,
            ]);
        }

        ColonneTableau::create(
            [
                'colonne_ligne' => 'Routes nationales',
                'ordre' => 1,
                'tableau_id' => $tableau2->id
            ]
        );
        ColonneTableau::create(
            [
                'colonne_ligne' => 'Routes régionales',
                'ordre' => 2,
                'tableau_id' => $tableau2->id
            ]
        );
        ColonneTableau::create(
            [
                'colonne_ligne' => 'Routes départementales',
                'ordre' => 3,
                'tableau_id' => $tableau2->id
            ]
        );
        ColonneTableau::create(
            [
                'colonne_ligne' => 'Total linéaire',
                'ordre' => 4,
                'tableau_id' => $tableau2->id
            ]
        );

        $tableau3 = Tableau::create(
            [
                'libelle' => 'Tableau I-3: Réseau routier selon la classification technique (en km)',
                'description' => 'Source : DGNET',
                'categorie_id' => $categorie1->id
            ]
        );
        LigneTableau::create(
            [
                'nom_ligne' => 'Type de routes',
                'ordre' => 1,
                'tableau_id' => $tableau3->id
            ]
        );
        LigneTableau::create(
            [
                'nom_ligne' => 'Routes bitumées',
                'ordre' => 2,
                'tableau_id' => $tableau3->id
            ]
        );
        LigneTableau::create(
            [
                'nom_ligne' => 'Routes en terre moderne',
                'ordre' => 3,
                'tableau_id' => $tableau3->id
            ]
        );
        LigneTableau::create(
            [
                'nom_ligne' => 'Routes en terre ordinaire',
                'ordre' => 3,
                'tableau_id' => $tableau3->id
            ]
        );
        LigneTableau::create(
            [
                'nom_ligne' => 'Pistes améliorées de type A',
                'ordre' => 4,
                'tableau_id' => $tableau3->id
            ]
        );
        LigneTableau::create(
            [
                'nom_ligne' => 'Pistes améliorées de type B',
                'ordre' => 5,
                'tableau_id' => $tableau3->id
            ]
        );
        LigneTableau::create(
            [
                'nom_ligne' => 'Pistes ordinaires',
                'ordre' => 6,
                'tableau_id' => $tableau3->id
            ]
        );
        LigneTableau::create(
            [
                'nom_ligne' => 'Ensemble',
                'ordre' => 7,
                'tableau_id' => $tableau3->id
            ]
        );

        foreach ($annees as $index => $annee) {
            ColonneTableau::create([
                'colonne_ligne'   => $annee,
                'ordre'       => $index + 1, // incrément de 1 à 13
                'tableau_id'  => $tableau3->id,
            ]);
        }

        $tableau4 = Tableau::create(
            [
                'libelle' => 'Tableau I-4: Linéaire de routes bitumées par an (en km)',
                'description' => 'Source : DGIT',
                'categorie_id' => $categorie1->id
            ]
        );
        LigneTableau::create(
            [
                'nom_ligne' => 'Routes',
                'ordre' => 1,
                'tableau_id' => $tableau4->id
            ]
        );
        LigneTableau::create(
            [
                'nom_ligne' => 'Bitumées réceptionnées',
                'ordre' => 2,
                'tableau_id' => $tableau4->id
            ]
        );
        LigneTableau::create(
            [
                'nom_ligne' => 'En cours de bitumage',
                'ordre' => 3,
                'tableau_id' => $tableau4->id
            ]
        );
        foreach ($annees as $index => $annee) {
            ColonneTableau::create([
                'colonne_ligne'   => $annee,
                'ordre'       => $index + 1, // incrément de 1 à 13
                'tableau_id'  => $tableau4->id,
            ]);
        }

        $tableau5 = Tableau::create(
            [
                'libelle' => 'Tableau I-5: Linéaire de routes bitumées par région (en km)',
                'description' => 'Source : DGNET',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($regions as $index => $region) {
            LigneTableau::create([
                'nom_ligne'   => $region,
                'ordre'       => $index + 1, // incrément de 1 à 13
                'tableau_id'  => $tableau5->id,
            ]);
        }

        foreach ($annees as $index => $annee) {
            ColonneTableau::create([
                'colonne_ligne'   => $annee,
                'ordre'       => $index + 1, // incrément de 1 à 13
                'tableau_id'  => $tableau5->id,
            ]);
        }

        $tableau6 = Tableau::create(
            [
                'libelle' => 'Tableau I-6: Linéaire de routes bitumées par province (en km)',
                'description' => 'Source : DGNET',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($provinces as $index => $province) {
            LigneTableau::create([
                'nom_ligne'   => $province,
                'ordre'       => $index + 1, // incrément de 1 à 13
                'tableau_id'  => $tableau6->id,
            ]);
        }

        foreach ($annees as $index => $annee) {
            ColonneTableau::create([
                'colonne_ligne'   => $annee,
                'ordre'       => $index + 1, // incrément de 1 à 13
                'tableau_id'  => $tableau6->id,
            ]);
        }

        $tableau7 = Tableau::create(
            [
                'libelle' => 'Tableau I-7: Linéaire de routes en terre par région (en km)',
                'description' => 'Source : DGNET',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($regions as $index => $region) {
            LigneTableau::create([
                'nom_ligne'   => $region,
                'ordre'       => $index + 1, // incrément de 1 à 13
                'tableau_id'  => $tableau7->id,
            ]);
        }

        foreach ($annees as $index => $annee) {
            ColonneTableau::create([
                'colonne_ligne'   => $annee,
                'ordre'       => $index + 1, // incrément de 1 à 13
                'tableau_id'  => $tableau7->id,
            ]);
        }

        $tableau8 = Tableau::create(
            [
                'libelle' => 'Tableau I-8: Linéaire de routes en terre par province (en km)',
                'description' => 'Source : DGNET',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($provinces as $index => $region) {
            LigneTableau::create([
                'nom_ligne'   => $region,
                'ordre'       => $index + 1, // incrément de 1 à 13
                'tableau_id'  => $tableau8->id,
            ]);
        }

        foreach ($annees as $index => $annee) {
            ColonneTableau::create([
                'colonne_ligne'   => $annee,
                'ordre'       => $index + 1, // incrément de 1 à 13
                'tableau_id'  => $tableau8->id,
            ]);
        }

        // 1️⃣ Récupérer toutes les lignes et colonnes créées pour ce tableau
        $lignes2 = LigneTableau::where('tableau_id', $tableau2->id)->get();
        $colonnes2 = ColonneTableau::where('tableau_id', $tableau2->id)->get();

        // 2️⃣ Parcourir toutes les combinaisons (ligne x colonne)
        foreach ($lignes2 as $ligne) {
            foreach ($colonnes2 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        // 1️⃣ Récupérer toutes les lignes et colonnes créées pour ce tableau
        $lignes3 = LigneTableau::where('tableau_id', $tableau3->id)->get();
        $colonnes3 = ColonneTableau::where('tableau_id', $tableau3->id)->get();

        // 2️⃣ Parcourir toutes les combinaisons (ligne x colonne)
        foreach ($lignes3 as $ligne) {
            foreach ($colonnes3 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }
        // 1️⃣ Récupérer toutes les lignes et colonnes créées pour ce tableau
        $lignes4 = LigneTableau::where('tableau_id', $tableau4->id)->get();
        $colonnes4 = ColonneTableau::where('tableau_id', $tableau4->id)->get();

        // 2️⃣ Parcourir toutes les combinaisons (ligne x colonne)
        foreach ($lignes4 as $ligne) {
            foreach ($colonnes4 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }
        // 1️⃣ Récupérer toutes les lignes et colonnes créées pour ce tableau
        $lignes5 = LigneTableau::where('tableau_id', $tableau5->id)->get();
        $colonnes5 = ColonneTableau::where('tableau_id', $tableau5->id)->get();

        // 2️⃣ Parcourir toutes les combinaisons (ligne x colonne)
        foreach ($lignes5 as $ligne) {
            foreach ($colonnes5 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }
        // 1️⃣ Récupérer toutes les lignes et colonnes créées pour ce tableau
        $lignes6 = LigneTableau::where('tableau_id', $tableau6->id)->get();
        $colonnes6 = ColonneTableau::where('tableau_id', $tableau6->id)->get();

        // 2️⃣ Parcourir toutes les combinaisons (ligne x colonne)
        foreach ($lignes6 as $ligne) {
            foreach ($colonnes6 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }
        // 1️⃣ Récupérer toutes les lignes et colonnes créées pour ce tableau
        $lignes7 = LigneTableau::where('tableau_id', $tableau7->id)->get();
        $colonnes7 = ColonneTableau::where('tableau_id', $tableau7->id)->get();

        // 2️⃣ Parcourir toutes les combinaisons (ligne x colonne)
        foreach ($lignes7 as $ligne) {
            foreach ($colonnes7 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }
        // 1️⃣ Récupérer toutes les lignes et colonnes créées pour ce tableau
        $lignes8 = LigneTableau::where('tableau_id', $tableau8->id)->get();
        $colonnes8 = ColonneTableau::where('tableau_id', $tableau8->id)->get();

        // 2️⃣ Parcourir toutes les combinaisons (ligne x colonne)
        foreach ($lignes8 as $ligne) {
            foreach ($colonnes8 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }


        foreach ($tabRB as $index => $titre) {
            // Création du tableau avec numérotation automatique
            $tableau = Tableau::create([
                'libelle'      => $titre,
                'description'  => 'Source : DGNET',
                'categorie_id' => $categorie1->id,
            ]);
            foreach ($RoutesB as $i => $route) {
                LigneTableau::create([
                    'nom_ligne'  => $route,
                    'ordre'      => $i + 1, // incrémentation de 1 à 13
                    'tableau_id' => $tableau->id,
                ]);
            }
            foreach ($annees as $i => $annee) {
                ColonneTableau::create([
                    'colonne_ligne'  => $annee,
                    'ordre'      => $i + 1, // incrémentation de 1 à 13
                    'tableau_id' => $tableau->id,
                ]);
            }
            // Création des cellules (croisement ligne × colonne)
            $lignes = LigneTableau::where('tableau_id', $tableau->id)->get();
            $colonnes = ColonneTableau::where('tableau_id', $tableau->id)->get();

            foreach ($lignes as $ligne) {
                foreach ($colonnes as $colonne) {
                    Cellule::create([
                        'ligne_id'   => $ligne->id,
                        'colonne_id' => $colonne->id,
                    ]);
                }
            }
        }

        foreach ($tabR as $index => $titre) {
            // Création du tableau avec numérotation automatique
            $tableau = Tableau::create([
                'libelle'      => $titre,
                'description'  => 'Source : DGNET',
                'categorie_id' => $categorie1->id,
            ]);
            foreach ($regions as $i => $region) {
                LigneTableau::create([
                    'nom_ligne'  => $region,
                    'ordre'      => $i + 1, // incrémentation de 1 à 13
                    'tableau_id' => $tableau->id,
                ]);
            }
            foreach ($annees as $i => $annee) {
                ColonneTableau::create([
                    'colonne_ligne'  => $annee,
                    'ordre'      => $i + 1, // incrémentation de 1 à 13
                    'tableau_id' => $tableau->id,
                ]);
            }
            // Création des cellules (croisement ligne × colonne)
            $lignes = LigneTableau::where('tableau_id', $tableau->id)->get();
            $colonnes = ColonneTableau::where('tableau_id', $tableau->id)->get();

            foreach ($lignes as $ligne) {
                foreach ($colonnes as $colonne) {
                    Cellule::create([
                        'ligne_id'   => $ligne->id,
                        'colonne_id' => $colonne->id,
                    ]);
                }
            }
        }

        foreach ($tabE as $index => $titre) {
            // Création du tableau avec numérotation automatique
            $tableau = Tableau::create([
                'libelle'      => $titre,
                'description'  => 'Source : DGNET',
                'categorie_id' => $categorie1->id,
            ]);
            foreach ($etats as $i => $etat) {
                LigneTableau::create([
                    'nom_ligne'  => $etat,
                    'ordre'      => $i + 1, // incrémentation de 1 à 13
                    'tableau_id' => $tableau->id,
                ]);
            }
            foreach ($annees as $i => $annee) {
                ColonneTableau::create([
                    'colonne_ligne'  => $annee,
                    'ordre'      => $i + 1, // incrémentation de 1 à 13
                    'tableau_id' => $tableau->id,
                ]);
            }
            // Création des cellules (croisement ligne × colonne)
            $lignes = LigneTableau::where('tableau_id', $tableau->id)->get();
            $colonnes = ColonneTableau::where('tableau_id', $tableau->id)->get();

            foreach ($lignes as $ligne) {
                foreach ($colonnes as $colonne) {
                    Cellule::create([
                        'ligne_id'   => $ligne->id,
                        'colonne_id' => $colonne->id,
                    ]);
                }
            }
        }

        foreach ($tabA as $index => $titre) {
            // Création du tableau avec numérotation automatique
            $tableau = Tableau::create([
                'libelle'      => $titre,
                'description'  => 'Source : DGNET',
                'categorie_id' => $categorie1->id,
            ]);
            foreach ($regions as $i => $region) {
                LigneTableau::create([
                    'nom_ligne'  => $region,
                    'ordre'      => $i + 1, // incrémentation de 1 à 13
                    'tableau_id' => $tableau->id,
                ]);
            }
            foreach ($annees as $i => $annee) {
                ColonneTableau::create([
                    'colonne_ligne'  => $annee,
                    'ordre'      => $i + 1, // incrémentation de 1 à 13
                    'tableau_id' => $tableau->id,
                ]);
            }
            // Création des cellules (croisement ligne × colonne)
            $lignes = LigneTableau::where('tableau_id', $tableau->id)->get();
            $colonnes = ColonneTableau::where('tableau_id', $tableau->id)->get();

            foreach ($lignes as $ligne) {
                foreach ($colonnes as $colonne) {
                    Cellule::create([
                        'ligne_id'   => $ligne->id,
                        'colonne_id' => $colonne->id,
                    ]);
                }
            }
        }

        foreach ($tabP as $index => $titre) {
            // Création du tableau avec numérotation automatique
            $tableau = Tableau::create([
                'libelle'      => $titre,
                'description'  => 'Source : DGNET',
                'categorie_id' => $categorie1->id,
            ]);
            foreach ($provinces as $i => $province) {
                LigneTableau::create([
                    'nom_ligne'  => $province,
                    'ordre'      => $i + 1, // incrémentation de 1 à 13
                    'tableau_id' => $tableau->id,
                ]);
            }
            foreach ($annees as $i => $annee) {
                ColonneTableau::create([
                    'colonne_ligne'  => $annee,
                    'ordre'      => $i + 1, // incrémentation de 1 à 13
                    'tableau_id' => $tableau->id,
                ]);
            }
            // Création des cellules (croisement ligne × colonne)
            $lignes = LigneTableau::where('tableau_id', $tableau->id)->get();
            $colonnes = ColonneTableau::where('tableau_id', $tableau->id)->get();

            foreach ($lignes as $ligne) {
                foreach ($colonnes as $colonne) {
                    Cellule::create([
                        'ligne_id'   => $ligne->id,
                        'colonne_id' => $colonne->id,
                    ]);
                }
            }
        }

        $tableau56 = Tableau::create(
            [
                'libelle' => 'Tableau I-56 : Nombre d’ouvrages (d’art et/ou de franchissement) construits par année',
                'description' => 'Source : DGESS à partir des données de DRI, DGIR, DGPR, Agetib et DGER',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($typeO as $i => $ouvrage) {
            LigneTableau::create([
                'nom_ligne'  => $ouvrage,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau56->id,
            ]);
        }
        foreach ($annees as $i => $annee) {
            ColonneTableau::create([
                'colonne_ligne'  => $annee,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau56->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes56 = LigneTableau::where('tableau_id', $tableau56->id)->get();
        $colonnes56 = ColonneTableau::where('tableau_id', $tableau56->id)->get();

        foreach ($lignes56 as $ligne) {
            foreach ($colonnes56 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $tableau12 = Tableau::create(
            [
                'libelle' => 'Tableau I-12: Répertoire du réseau communautaire de l’UEMOA (CU) au Burkina Faso en 2023',
                'description' => 'Source : DGNET',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($routesC as $i => $route) {
            LigneTableau::create([
                'nom_ligne'  => $route,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau12->id,
            ]);
        }
        foreach ($rb as $i => $r) {
            ColonneTableau::create([
                'colonne_ligne'  => $r,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau12->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes12 = LigneTableau::where('tableau_id', $tableau12->id)->get();
        $colonnes12 = ColonneTableau::where('tableau_id', $tableau12->id)->get();

        foreach ($lignes12 as $ligne) {
            foreach ($colonnes12 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $tableau13 = Tableau::create(
            [
                'libelle' => 'Tableau I-13: État des routes communautaires de l’UEMOA (CU) en 2023 (en km)',
                'description' => 'Source : DGNET',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($routesC as $i => $route) {
            LigneTableau::create([
                'nom_ligne'  => $route,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau13->id,
            ]);
        }
        foreach ($etats as $i => $etat) {
            ColonneTableau::create([
                'colonne_ligne'  => $etat,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau13->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes13 = LigneTableau::where('tableau_id', $tableau13->id)->get();
        $colonnes13 = ColonneTableau::where('tableau_id', $tableau13->id)->get();

        foreach ($lignes13 as $ligne) {
            foreach ($colonnes13 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $tableau16 = Tableau::create(
            [
                'libelle' => 'Tableau I-16: Linéaire de routes bitumées renforcées/réhabilitées (en km)',
                'description' => 'Source : DGESS à partir des données de DGIR et DGER',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($tr as $i => $r) {
            LigneTableau::create([
                'nom_ligne'  => $r,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau16->id,
            ]);
        }
        foreach ($annees as $i => $annee) {
            ColonneTableau::create([
                'colonne_ligne'  => $annee,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau16->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes16 = LigneTableau::where('tableau_id', $tableau16->id)->get();
        $colonnes16 = ColonneTableau::where('tableau_id', $tableau16->id)->get();

        foreach ($lignes16 as $ligne) {
            foreach ($colonnes16 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $tableau31 = Tableau::create(
            [
                'libelle' => 'Tableau I-31: Évolution de l’état des routes nationales par type (en km)',
                'description' => 'Source : DGNET',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($etats as $i => $etat) {
            $ligne = LigneTableau::create([
                'nom_ligne'  => $etat,
                'ordre'      => $i + 2, // incrémentation de 1 à 13
                'tableau_id' => $tableau31->id,
            ]);
            LigneTableau::Create(
                [
                    'nom_ligne'  => 'Route bitumée',
                    'ordre'      => $i + 1, // incrémentation de 1 à 13
                    'tableau_id' => $tableau31->id,
                    'parent_id' => $ligne->id,
                ]
            );
            LigneTableau::Create(
                [
                    'nom_ligne'  => 'Route en terre',
                    'ordre'      => $i + 1, // incrémentation de 1 à 13
                    'tableau_id' => $tableau31->id,
                    'parent_id' => $ligne->id,
                ]
            );
        }
        foreach ($annees as $i => $annee) {
            ColonneTableau::create([
                'colonne_ligne'  => $annee,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau31->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes31 = LigneTableau::where('tableau_id', $tableau31->id)
            ->whereNotNull('parent_id')->get();
        $colonnes31 = ColonneTableau::where('tableau_id', $tableau31->id)->get();

        foreach ($lignes31 as $ligne) {
            foreach ($colonnes31 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }


        $tableau32 = Tableau::create(
            [
                'libelle' => 'Tableau I-32: Évolution de l’état des routes régionales par type (en km)',
                'description' => 'Source : DGNET',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($etats as $i => $etat) {
            $ligne = LigneTableau::create([
                'nom_ligne'  => $etat,
                'ordre'      => $i + 2, // incrémentation de 1 à 13
                'tableau_id' => $tableau32->id,
            ]);
            LigneTableau::Create(
                [
                    'nom_ligne'  => 'Route bitumée',
                    'ordre'      => $i + 1, // incrémentation de 1 à 13
                    'tableau_id' => $tableau32->id,
                    'parent_id' => $ligne->id,
                ]
            );
            LigneTableau::Create(
                [
                    'nom_ligne'  => 'Route en terre',
                    'ordre'      => $i + 1, // incrémentation de 1 à 13
                    'tableau_id' => $tableau32->id,
                    'parent_id' => $ligne->id,
                ]
            );
        }
        foreach ($annees as $i => $annee) {
            ColonneTableau::create([
                'colonne_ligne'  => $annee,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau32->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes32 = LigneTableau::where('tableau_id', $tableau32->id)
            ->whereNotNull('parent_id')->get();
        $colonnes32 = ColonneTableau::where('tableau_id', $tableau32->id)->get();

        foreach ($lignes32 as $ligne) {
            foreach ($colonnes32 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $tableau33 = Tableau::create(
            [
                'libelle' => 'Tableau I-33: Évolution de l’état des routes départementales par type (en km)',
                'description' => 'Source : DGNET',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($etats as $i => $etat) {
            $ligne = LigneTableau::create([
                'nom_ligne'  => $etat,
                'ordre'      => $i + 2, // incrémentation de 1 à 13
                'tableau_id' => $tableau33->id,
            ]);
            LigneTableau::Create(
                [
                    'nom_ligne'  => 'Route bitumée',
                    'ordre'      => $i + 1, // incrémentation de 1 à 13
                    'tableau_id' => $tableau33->id,
                    'parent_id' => $ligne->id,
                ]
            );
            LigneTableau::Create(
                [
                    'nom_ligne'  => 'Route en terre',
                    'ordre'      => $i + 1, // incrémentation de 1 à 13
                    'tableau_id' => $tableau33->id,
                    'parent_id' => $ligne->id,
                ]
            );
        }
        foreach ($annees as $i => $annee) {
            ColonneTableau::create([
                'colonne_ligne'  => $annee,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau33->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes33 = LigneTableau::where('tableau_id', $tableau33->id)
            ->whereNotNull('parent_id')->get();
        $colonnes33 = ColonneTableau::where('tableau_id', $tableau33->id)->get();

        foreach ($lignes33 as $ligne) {
            foreach ($colonnes33 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $tableau37 = Tableau::create(
            [
                'libelle' => 'Tableau I-37: Nombre de chantiers suivis par le LNBTP par type de travaux',
                'description' => 'Source : LNBTP',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($entretiens as $i => $entretien) {
            $ligne = LigneTableau::create([
                'nom_ligne'  => $entretien,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau37->id,
            ]);
        }
        foreach ($annees as $i => $annee) {
            ColonneTableau::create([
                'colonne_ligne'  => $annee,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau37->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes37 = LigneTableau::where('tableau_id', $tableau37->id)->get();
        $colonnes37 = ColonneTableau::where('tableau_id', $tableau37->id)->get();

        foreach ($lignes37 as $ligne) {
            foreach ($colonnes37 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $tableau38 = Tableau::create(
            [
                'libelle' => 'Tableau I-38: Nombre de contrats réalisés par le LNBTP',
                'description' => 'Source : LNBTP',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($typesT as $i => $type) {
            $ligne = LigneTableau::create([
                'nom_ligne'  => $type,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau38->id,
            ]);
        }
        foreach ($annees as $i => $annee) {
            ColonneTableau::create([
                'colonne_ligne'  => $annee,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau38->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes38 = LigneTableau::where('tableau_id', $tableau38->id)->get();
        $colonnes38 = ColonneTableau::where('tableau_id', $tableau38->id)->get();

        foreach ($lignes38 as $ligne) {
            foreach ($colonnes38 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $tableau39 = Tableau::create(
            [
                'libelle' => 'Tableau I-39: Nombre d’études de projets réalisés par type de travaux',
                'description' => 'Source : DGNET',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($tyTs as $i => $tyT) {
            $ligne = LigneTableau::create([
                'nom_ligne'  => $tyT,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau39->id,
            ]);
        }
        foreach ($annees as $i => $annee) {
            ColonneTableau::create([
                'colonne_ligne'  => $annee,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau39->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes39 = LigneTableau::where('tableau_id', $tableau39->id)->get();
        $colonnes39 = ColonneTableau::where('tableau_id', $tableau39->id)->get();

        foreach ($lignes39 as $ligne) {
            foreach ($colonnes39 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $tableau40 = Tableau::create(
            [
                'libelle' => 'Tableau I-40: Linéaire de routes ayant fait l’objet d’une étude par type de travaux (en km)',
                'description' => 'Source : DGNET',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($tyTs as $i => $tyT) {
            $ligne = LigneTableau::create([
                'nom_ligne'  => $tyT,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau40->id,
            ]);
        }
        foreach ($annees as $i => $annee) {
            ColonneTableau::create([
                'colonne_ligne'  => $annee,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau40->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes40 = LigneTableau::where('tableau_id', $tableau40->id)->get();
        $colonnes40 = ColonneTableau::where('tableau_id', $tableau40->id)->get();

        foreach ($lignes40 as $ligne) {
            foreach ($colonnes40 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $tableau41 = Tableau::create(
            [
                'libelle' => 'Tableau I-41: Taux d’accroissement du trafic sur le réseau routier classé (en %)',
                'description' => 'Source : DGNET',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($RoutesB as $i => $route) {
            $ligne = LigneTableau::create([
                'nom_ligne'  => $route,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau41->id,
            ]);
        }
        foreach ($annees as $i => $annee) {
            ColonneTableau::create([
                'colonne_ligne'  => $annee,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau41->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes41 = LigneTableau::where('tableau_id', $tableau41->id)->get();
        $colonnes41 = ColonneTableau::where('tableau_id', $tableau41->id)->get();

        foreach ($lignes41 as $ligne) {
            foreach ($colonnes41 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $tableau42 = Tableau::create(
            [
                'libelle' => 'Tableau I-42: Proportion du trafic poids lourd sur le réseau routier classé (en %)',
                'description' => 'Source : DGNET',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($RoutesB as $i => $route) {
            $ligne = LigneTableau::create([
                'nom_ligne'  => $route,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau42->id,
            ]);
        }
        foreach ($annees as $i => $annee) {
            ColonneTableau::create([
                'colonne_ligne'  => $annee,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau42->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes42 = LigneTableau::where('tableau_id', $tableau42->id)->get();
        $colonnes42 = ColonneTableau::where('tableau_id', $tableau42->id)->get();

        foreach ($lignes42 as $ligne) {
            foreach ($colonnes42 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $tableau43 = Tableau::create(
            [
                'libelle' => 'Tableau I-43: Travaux connexes réalisés par les projets routiers en 2023',
                'description' => 'Source : DGESS à partir des données de DGIR, Agetib, DGER et DGPR',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($travauxR as $i => $TrR) {
            $ligne = LigneTableau::create([
                'nom_ligne'  => $TrR,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau43->id,
            ]);
        }
        foreach ($annees as $i => $annee) {
            ColonneTableau::create([
                'colonne_ligne'  => $annee,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau43->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes43 = LigneTableau::where('tableau_id', $tableau43->id)->get();
        $colonnes43 = ColonneTableau::where('tableau_id', $tableau43->id)->get();

        foreach ($lignes43 as $ligne) {
            foreach ($colonnes43 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $tableau45 = Tableau::create(
            [
                'libelle' => 'Tableau I-45: Nombre de chantiers affectés / suspendus ou arrêtés pour cause d’insécurité par type de travaux',
                'description' => 'Source : DGESS à partir des données de DGIR, Agetib, DGNET, DGER et DGPR',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($typR as $i => $typ) {
            $ligne = LigneTableau::create([
                'nom_ligne'  => $typ,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau45->id,
            ]);
            foreach ($rubriques as $i => $rub) {
                LigneTableau::create([
                    'nom_ligne'  => $rub,
                    'ordre'      => $i + 1, // incrémentation de 1 à 13
                    'tableau_id' => $tableau45->id,
                    'parent_id' => $ligne->id,
                ]);
            }
        }
        foreach ($annees as $i => $annee) {
            ColonneTableau::create([
                'colonne_ligne'  => $annee,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau45->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes45 = LigneTableau::where('tableau_id', $tableau45->id)
            ->whereNotNull('parent_id')->get();
        $colonnes45 = ColonneTableau::where('tableau_id', $tableau45->id)->get();

        foreach ($lignes45 as $ligne) {
            foreach ($colonnes45 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $tableau46 = Tableau::create(
            [
                'libelle' => 'Tableau I-46: Nombre de ralentisseurs par route bitumée',
                'description' => 'Source : DGESS',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($RNs as $i => $RN) {
            $ligne = LigneTableau::create([
                'nom_ligne'  => $RN,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau46->id,
            ]);
        }
        foreach ($annees as $i => $annee) {
            ColonneTableau::create([
                'colonne_ligne'  => $annee,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau46->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes46 = LigneTableau::where('tableau_id', $tableau46->id)->get();
        $colonnes46 = ColonneTableau::where('tableau_id', $tableau46->id)->get();

        foreach ($lignes46 as $ligne) {
            foreach ($colonnes46 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $tableau47 = Tableau::create(
            [
                'libelle' => 'Tableau I-47: Nombre d’emplois créés par type de travaux routiers selon le genre',
                'description' => 'Source : DGESS à partir des données de DGIR, Agetib, DGER et DGPR',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($emplois as $i => $emploi) {
            $ligne = LigneTableau::create([
                'nom_ligne'  => $emploi,
                'ordre'      => $i + 2, // incrémentation de 1 à 13
                'tableau_id' => $tableau47->id,
            ]);
            LigneTableau::create([
                'nom_ligne'  => 'H',
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau47->id,
                'parent_id' => $ligne->id,
            ]);
            LigneTableau::create([
                'nom_ligne'  => 'F',
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau47->id,
                'parent_id' => $ligne->id,
            ]);
        }
        foreach ($annees as $i => $annee) {
            ColonneTableau::create([
                'colonne_ligne'  => $annee,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau47->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes47 = LigneTableau::where('tableau_id', $tableau47->id)
            ->whereNotNull('parent_id')->get();
        $colonnes47 = ColonneTableau::where('tableau_id', $tableau47->id)->get();

        foreach ($lignes47 as $ligne) {
            foreach ($colonnes47 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $tableau48 = Tableau::create(
            [
                'libelle' => 'Tableau I-48: Nombre de personnes dédommagées à travers les projets routiers',
                'description' => 'Source : DGF',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($ps as $i => $p) {
            $ligne = LigneTableau::create([
                'nom_ligne'  => $p,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau48->id,
            ]);
        }
        foreach ($annees as $i => $annee) {
            ColonneTableau::create([
                'colonne_ligne'  => $annee,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau48->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes48 = LigneTableau::where('tableau_id', $tableau48->id)->get();
        $colonnes48 = ColonneTableau::where('tableau_id', $tableau48->id)->get();

        foreach ($lignes48 as $ligne) {
            foreach ($colonnes48 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $tableau49 = Tableau::create(
            [
                'libelle' => 'Tableau I-49: Nombre de campagnes de sensibilisation sur le VIH-SIDA à travers les projets routiers',
                'description' => 'Source : DGESS à partir des données de DGIR, Agetib, DGER et DGPR',
                'categorie_id' => $categorie1->id
            ]
        );

        LigneTableau::create([
            'nom_ligne'  => 'Libelle',
            'ordre'      => 1, // incrémentation de 1 à 13
            'tableau_id' => $tableau49->id,
        ]);

        LigneTableau::create([
            'nom_ligne'  => 'Nombre de campagnes de sensibilisation',
            'ordre'      => 2, // incrémentation de 1 à 13
            'tableau_id' => $tableau49->id,
        ]);

        foreach ($annees as $i => $annee) {
            ColonneTableau::create([
                'colonne_ligne'  => $annee,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau49->id,
            ]);
        }
        // Création des cellules (croisement ligne × colonne)
        $lignes49 = LigneTableau::where('tableau_id', $tableau49->id)->get();
        $colonnes49 = ColonneTableau::where('tableau_id', $tableau49->id)->get();

        foreach ($lignes49 as $ligne) {
            foreach ($colonnes49 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $tableau50 = Tableau::create(
            [
                'libelle' => 'Tableau I-50: Linéaire de pistes rurales inventoriées par région et par province (en km)',
                'description' => 'Source : DGESS à partir des données de DGIR, Agetib, DGER et DGPR',
                'categorie_id' => $categorie1->id
            ]
        );
        foreach ($provinces as $i => $province) {
            $ligne = LigneTableau::create([
                'nom_ligne'  => $province,
                'ordre'      => $i + 1, // incrémentation de 1 à 13
                'tableau_id' => $tableau50->id,
            ]);
        }

        ColonneTableau::create([
            'colonne_ligne'  => 'Linéaire',
            'ordre'      =>  1, // incrémentation de 1 à 13
            'tableau_id' => $tableau50->id,
        ]);

        // Création des cellules (croisement ligne × colonne)
        $lignes50 = LigneTableau::where('tableau_id', $tableau50->id)->get();
        $colonnes50 = ColonneTableau::where('tableau_id', $tableau50->id)->get();

        foreach ($lignes50 as $ligne) {
            foreach ($colonnes50 as $colonne) {
                Cellule::create([
                    'ligne_id'   => $ligne->id,
                    'colonne_id' => $colonne->id,
                ]);
            }
        }

        $categorie2 = Categorie::create(
            ['libelle' => 'LES INFRASTRUCTURES FERROVIAIRES']
        );
    }
}
