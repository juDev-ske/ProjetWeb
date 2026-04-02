<?php
/**
 * Seeds — charge le schéma (database.sql) puis insère les données de test.
 * Lancer : docker-compose exec web php seeds.php
 */

$host     = 'db';
$dbname   = 'monsite';
$user     = 'user';
$password = 'userpass';

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// ============================================================
// SCHÉMA — exécution de database.sql
// ============================================================
echo "Création des tables (database.sql)...\n";
$sql = file_get_contents(__DIR__ . '/database.sql');
$pdo->exec($sql);
echo "  Tables prêtes.\n\n";

// ============================================================
// VIDAGE DES DONNÉES
// ============================================================
echo "Vidage des tables...\n";

$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
foreach (['student_promotion','promotion','wishlist','spontaneous_application','company_rating','application','offer_skill','offer','skill','company','location','profile','user'] as $table) {
    $pdo->exec("TRUNCATE TABLE $table");
    echo "  TRUNCATE $table\n";
}
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

// ============================================================
// MOT DE PASSE
// ============================================================
$pwd = password_hash('password', PASSWORD_BCRYPT);
echo "\nHash généré : $pwd\n\n";

// ============================================================
// UTILISATEURS
// ============================================================
echo "Insertion des utilisateurs...\n";
$stmt = $pdo->prepare("INSERT INTO user (email, password, role) VALUES (?, ?, ?)");

$users = [
    ['admin@cesi.fr',          $pwd, 'admin'],
    ['marie.martin@cesi.fr',   $pwd, 'pilote'],
    ['paul.durand@cesi.fr',    $pwd, 'pilote'],
    ['lea.simon@cesi.fr',      $pwd, 'pilote'],
    ['lucas.bernard@cesi.fr',  $pwd, 'etudiant'],
    ['emma.petit@cesi.fr',     $pwd, 'etudiant'],
    ['hugo.rousseau@cesi.fr',  $pwd, 'etudiant'],
    ['chloe.leblanc@cesi.fr',  $pwd, 'etudiant'],
    ['tom.moreau@cesi.fr',     $pwd, 'etudiant'],
    ['alice.garnier@cesi.fr',  $pwd, 'etudiant'],
    ['maxime.henry@cesi.fr',   $pwd, 'etudiant'],
    ['sarah.dupont@cesi.fr',   $pwd, 'etudiant'],
    ['nathan.girard@cesi.fr',  $pwd, 'etudiant'],
    ['camille.robert@cesi.fr', $pwd, 'etudiant'],
];
foreach ($users as $u) { $stmt->execute($u); }

// ============================================================
// PROFILS
// ============================================================
echo "Insertion des profils...\n";
$stmt = $pdo->prepare("INSERT INTO profile (user_id, first_name, last_name, is_active, stage_status) VALUES (?, ?, ?, 1, ?)");

$profiles = [
    [1,  'Admin',   'CESI',     'not_searching'],
    [2,  'Marie',   'Martin',   'not_searching'],
    [3,  'Paul',    'Durand',   'not_searching'],
    [4,  'Léa',     'Simon',    'not_searching'],
    [5,  'Lucas',   'Bernard',  'searching'],
    [6,  'Emma',    'Petit',    'searching'],
    [7,  'Hugo',    'Rousseau', 'found'],
    [8,  'Chloé',   'Leblanc',  'searching'],
    [9,  'Tom',     'Moreau',   'searching'],
    [10, 'Alice',   'Garnier',  'found'],
    [11, 'Maxime',  'Henry',    'searching'],
    [12, 'Sarah',   'Dupont',   'not_searching'],
    [13, 'Nathan',  'Girard',   'searching'],
    [14, 'Camille', 'Robert',   'searching'],
];
foreach ($profiles as $p) { $stmt->execute($p); }

// ============================================================
// LOCALISATIONS
// ============================================================
echo "Insertion des localisations...\n";
$stmt = $pdo->prepare("INSERT INTO location (city, department, postal_code, region) VALUES (?, ?, ?, ?)");

$locations = [
    ['Paris',          'Paris',              '75000', 'Île-de-France'],
    ['Lyon',           'Rhône',              '69000', 'Auvergne-Rhône-Alpes'],
    ['Marseille',      'Bouches-du-Rhône',   '13000', 'Provence-Alpes-Côte d\'Azur'],
    ['Bordeaux',       'Gironde',            '33000', 'Nouvelle-Aquitaine'],
    ['Lille',          'Nord',               '59000', 'Hauts-de-France'],
    ['Toulouse',       'Haute-Garonne',      '31000', 'Occitanie'],
    ['Nantes',         'Loire-Atlantique',   '44000', 'Pays de la Loire'],
    ['Strasbourg',     'Bas-Rhin',           '67000', 'Grand Est'],
    ['Rennes',         'Ille-et-Vilaine',    '35000', 'Bretagne'],
    ['Montpellier',    'Hérault',            '34000', 'Occitanie'],
    ['Nice',           'Alpes-Maritimes',    '06000', 'Provence-Alpes-Côte d\'Azur'],
    ['Grenoble',       'Isère',              '38000', 'Auvergne-Rhône-Alpes'],
];
foreach ($locations as $l) { $stmt->execute($l); }

// ============================================================
// ENTREPRISES
// ============================================================
echo "Insertion des entreprises...\n";
$stmt = $pdo->prepare("INSERT INTO company (name, description, email, phone, rating) VALUES (?, ?, ?, ?, ?)");

$companies = [
    ['Capgemini',        'Société de conseil et services informatiques.',       'contact@capgemini.fr',   '01 40 12 30 30', 4.2],
    ['Sopra Steria',     'Groupe européen de conseil et services numériques.',  'rh@soprasteria.fr',      '01 40 67 29 29', 3.8],
    ['Thales',           'Groupe mondial de technologie pour la défense.',      'stages@thales.fr',       '01 57 77 80 00', 4.5],
    ['Orange',           'Opérateur télécom et services numériques.',           'recrutement@orange.fr',  '09 69 36 39 00', 3.9],
    ['Airbus',           'Constructeur aéronautique et spatial européen.',      'stages@airbus.fr',       '05 61 93 33 33', 4.7],
    ['BNP Paribas',      'Groupe bancaire international.',                      'rh@bnpparibas.fr',       '01 40 14 45 46', 3.5],
    ['Société Générale', 'Banque et services financiers.',                      'recrutement@socgen.fr',  '01 42 14 20 00', 3.6],
    ['Renault',          'Constructeur automobile français.',                   'stages@renault.fr',      '01 76 84 04 04', 4.0],
    ['SNCF',             'Entreprise ferroviaire nationale.',                   'rh@sncf.fr',             '36 35',          3.3],
    ['Dassault',         'Groupe aéronautique et informatique.',                'stages@dassault.fr',     '01 47 11 40 00', 4.6],
];
foreach ($companies as $c) { $stmt->execute($c); }

// ============================================================
// COMPÉTENCES
// ============================================================
echo "Insertion des compétences...\n";
$stmt = $pdo->prepare("INSERT INTO skill (name) VALUES (?)");

$skills = ['PHP', 'Python', 'Java', 'JavaScript', 'SQL', 'React', 'Symfony', 'Docker', 'Git', 'C++', 'Machine Learning', 'Cybersécurité', 'DevOps', 'Angular', 'Node.js'];
foreach ($skills as $s) { $stmt->execute([$s]); }

// ============================================================
// OFFRES
// ============================================================
echo "Insertion des offres...\n";
$stmt = $pdo->prepare("INSERT INTO offer (title, description, salary, type, mode, publication_date, company_id, location_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

$offers = [
    ['Stage Développeur PHP',          'Rejoignez notre équipe backend pour développer des applications web.',  '600€/mois',  'internship',    'hybrid',   '2026-01-10', 1, 1],
    ['Stage Data Analyst',             'Analyse de données et création de dashboards pour nos clients.',        '650€/mois',  'internship',    'remote',   '2026-01-15', 2, 2],
    ['Alternance DevOps',              'Intégrez notre équipe infrastructure pour gérer les pipelines CI/CD.',  '1200€/mois', 'apprenticeship','on_site',  '2026-01-20', 3, 3],
    ['Stage Frontend React',           'Développement d\'interfaces utilisateur modernes avec React.',          '580€/mois',  'internship',    'hybrid',   '2026-01-22', 4, 1],
    ['Alternance Cybersécurité',       'Participez à la sécurisation des systèmes d\'information.',            '1300€/mois', 'apprenticeship','on_site',  '2026-02-01', 5, 5],
    ['Stage Machine Learning',         'Développez des modèles de Machine Learning pour l\'analyse prédictive.','700€/mois',  'internship',    'remote',   '2026-02-05', 1, 6],
    ['Alternance Développeur Java',    'Développement d\'applications Java pour le secteur bancaire.',          '1150€/mois', 'apprenticeship','on_site',  '2026-02-10', 6, 1],
    ['Stage UX/UI Designer',           'Conception de maquettes et prototypes pour nos applications mobiles.',  '550€/mois',  'internship',    'hybrid',   '2026-02-12', 7, 2],
    ['Stage Intégration Systèmes',     'Intégration et test de systèmes embarqués dans le secteur auto.',      '620€/mois',  'internship',    'on_site',  '2026-02-15', 8, 4],
    ['Alternance Réseau & Systèmes',   'Gestion et maintenance des réseaux informatiques.',                    '1100€/mois', 'apprenticeship','on_site',  '2026-02-18', 9, 5],
    ['Stage Développeur Python',       'Scripts d\'automatisation et outils internes en Python.',              '600€/mois',  'internship',    'remote',   '2026-02-20', 10, 7],
    ['Alternance Cloud Azure',         'Déploiement et gestion d\'infrastructures cloud sur Azure.',           '1250€/mois', 'apprenticeship','hybrid',   '2026-02-25', 3, 1],
    ['Stage Analyste Financier',       'Analyse financière et reporting pour les équipes de gestion.',         '640€/mois',  'internship',    'on_site',  '2026-03-01', 6, 1],
    ['Stage Sécurité Informatique',    'Audit de sécurité et tests d\'intrusion sur les applications.',        '680€/mois',  'internship',    'hybrid',   '2026-03-05', 10, 8],
    ['Alternance Full Stack JS',       'Développement full stack avec Node.js et Angular.',                    '1180€/mois', 'apprenticeship','remote',   '2026-03-10', 2, 9],
    ['Stage Développeur C++',          'Développement bas niveau pour systèmes embarqués aéronautiques.',      '650€/mois',  'internship',    'on_site',  '2026-03-12', 5, 6],
    ['Alternance Data Engineer',       'Construction et maintenance de pipelines de données.',                 '1200€/mois', 'apprenticeship','hybrid',   '2026-03-15', 4, 2],
    ['Stage Support IT',               'Support technique niveau 2 pour les utilisateurs internes.',           '500€/mois',  'internship',    'on_site',  '2026-03-18', 9, 10],
    ['Alternance Développeur Symfony', 'Développement d\'applications web avec Symfony et API Platform.',      '1150€/mois', 'apprenticeship','hybrid',   '2026-03-20', 1, 1],
    ['Stage Intelligence Artificielle','Recherche et développement en IA pour des projets innovants.',         '720€/mois',  'internship',    'remote',   '2026-03-25', 5, 5],
];
foreach ($offers as $o) { $stmt->execute($o); }

// ============================================================
// COMPÉTENCES DES OFFRES
// ============================================================
echo "Insertion des compétences par offre...\n";
$stmt = $pdo->prepare("INSERT INTO offer_skill (offer_id, skill_id) VALUES (?, ?)");

$offerSkills = [
    [1,1],[1,7],[1,5],         // PHP, Symfony, SQL
    [2,2],[2,5],               // Python, SQL
    [3,8],[3,9],[3,13],        // Docker, Git, DevOps
    [4,6],[4,9],               // React, Git
    [5,12],[5,9],              // Cybersécurité, Git
    [6,2],[6,11],              // Python, Machine Learning
    [7,3],[7,5],               // Java, SQL
    [8,6],[8,14],              // React, Angular
    [9,10],[9,9],              // C++, Git
    [10,9],[10,8],             // Git, Docker
    [11,2],[11,9],             // Python, Git
    [12,8],[12,13],            // Docker, DevOps
    [13,5],                    // SQL
    [14,12],[14,9],            // Cybersécurité, Git
    [15,15],[15,14],           // Node.js, Angular
    [16,10],                   // C++
    [17,2],[17,5],             // Python, SQL
    [18,9],                    // Git
    [19,1],[19,7],             // PHP, Symfony
    [20,2],[20,11],            // Python, Machine Learning
];
foreach ($offerSkills as $os) { $stmt->execute($os); }

// ============================================================
// CANDIDATURES
// ============================================================
echo "Insertion des candidatures...\n";
$stmt = $pdo->prepare("INSERT INTO application (student_id, offer_id, status, message, application_date) VALUES (?, ?, ?, ?, ?)");

$applications = [
    [5,  1,  'sent',     'Je suis très motivé par ce stage PHP.', '2026-01-20 10:00:00'],
    [5,  3,  'refused',  'Mon profil DevOps correspond à vos besoins.', '2026-01-25 14:00:00'],
    [6,  2,  'accepted', 'Passionnée par la data, je souhaite rejoindre votre équipe.', '2026-01-22 09:00:00'],
    [6,  4,  'sent',     'React est ma techno principale depuis 2 ans.', '2026-02-01 11:00:00'],
    [7,  5,  'sent',     'La cybersécurité est ma spécialité.', '2026-02-05 15:00:00'],
    [7,  12, 'accepted', 'J\'ai une expérience sur Azure via mes projets perso.', '2026-02-10 10:00:00'],
    [8,  6,  'sent',     'Je maîtrise Python et les bibliothèques ML.', '2026-02-08 13:00:00'],
    [8,  1,  'refused',  'Développement web est mon domaine de prédilection.', '2026-02-12 16:00:00'],
    [9,  7,  'sent',     'Java est mon langage principal en formation.', '2026-02-15 10:00:00'],
    [9,  9,  'sent',     'Systèmes embarqués m\'intéressent beaucoup.', '2026-02-18 11:00:00'],
    [10, 11, 'accepted', 'Python est la techno sur laquelle je travaille chaque jour.', '2026-02-20 09:00:00'],
    [10, 19, 'sent',     'Symfony est le framework que j\'utilise en cours.', '2026-02-25 14:00:00'],
    [11, 15, 'sent',     'Full stack JS est exactement ce que je recherche.', '2026-03-01 10:00:00'],
    [11, 17, 'sent',     'La data engineering m\'attire pour mon avenir pro.', '2026-03-05 15:00:00'],
    [12, 13, 'accepted', 'Finance et informatique sont mes deux domaines.', '2026-03-08 09:00:00'],
    [12, 18, 'sent',     'Support IT pour débuter ma carrière professionnelle.', '2026-03-10 11:00:00'],
    [13, 14, 'sent',     'La sécurité informatique est ma passion.', '2026-03-12 14:00:00'],
    [13, 20, 'sent',     'L\'IA m\'attire pour ses applications concrètes.', '2026-03-15 10:00:00'],
    [14, 8,  'sent',     'UX/UI design est ma formation principale.', '2026-03-18 13:00:00'],
    [14, 16, 'sent',     'C++ pour l\'aéronautique correspond à mon projet.', '2026-03-20 15:00:00'],
];
foreach ($applications as $a) { $stmt->execute($a); }

// ============================================================
// WISHLIST
// ============================================================
echo "Insertion de la wishlist...\n";
$stmt = $pdo->prepare("INSERT INTO wishlist (student_id, offer_id) VALUES (?, ?)");

$wishlist = [
    [5,2],[5,4],[5,6],
    [6,1],[6,3],[6,5],
    [7,7],[7,9],
    [8,11],[8,13],
    [9,14],[9,15],[9,16],
    [10,17],[10,18],
    [11,19],[11,20],
    [12,1],[12,2],
    [13,3],[13,5],
    [14,10],[14,12],
];
foreach ($wishlist as $w) { $stmt->execute($w); }

// ============================================================
// PROMOTIONS
// ============================================================
echo "Insertion des promotions...\n";
$pdo->exec("INSERT INTO promotion (name, year, pilot_id) VALUES ('BTS SIO 2026', 2026, 2), ('Licence Pro Dev 2026', 2026, 3), ('Master IA 2026', 2026, 4)");

$pdo->exec("INSERT INTO student_promotion (student_id, promotion_id) VALUES (5,1),(6,1),(7,1),(8,2),(9,2),(10,2),(11,3),(12,3),(13,3),(14,3)");

echo "\n✓ Seeds chargés avec succès !\n";
echo "\nComptes disponibles (mot de passe : password) :\n";
echo "  admin@cesi.fr          → admin\n";
echo "  marie.martin@cesi.fr   → pilote\n";
echo "  lea.simon@cesi.fr      → pilote\n";
echo "  lucas.bernard@cesi.fr  → étudiant\n";
