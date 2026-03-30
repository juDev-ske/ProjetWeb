USE monsite;

-- Mot de passe pour tous les comptes : "password"
SET @pwd = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

-- ============================================================
-- UTILISATEURS
-- ============================================================

INSERT INTO user (email, password, role) VALUES
  ('admin@cesi.fr',            @pwd, 'admin'),
  ('marie.martin@cesi.fr',     @pwd, 'pilote'),
  ('paul.durand@cesi.fr',      @pwd, 'pilote'),
  ('lea.simon@cesi.fr',        @pwd, 'pilote'),
  ('lucas.bernard@cesi.fr',    @pwd, 'etudiant'),
  ('emma.petit@cesi.fr',       @pwd, 'etudiant'),
  ('hugo.rousseau@cesi.fr',    @pwd, 'etudiant'),
  ('chloe.leblanc@cesi.fr',    @pwd, 'etudiant'),
  ('tom.moreau@cesi.fr',       @pwd, 'etudiant'),
  ('alice.garnier@cesi.fr',    @pwd, 'etudiant'),
  ('maxime.henry@cesi.fr',     @pwd, 'etudiant'),
  ('sarah.dupont@cesi.fr',     @pwd, 'etudiant'),
  ('nathan.girard@cesi.fr',    @pwd, 'etudiant'),
  ('camille.robert@cesi.fr',   @pwd, 'etudiant');

-- ============================================================
-- PROFILS
-- ============================================================

INSERT INTO profile (user_id, first_name, last_name, is_active, stage_status) VALUES
  (1,  'Admin',   'CESI',     1, 'not_searching'),
  (2,  'Marie',   'Martin',   1, 'not_searching'),
  (3,  'Paul',    'Durand',   1, 'not_searching'),
  (4,  'Lea',     'Simon',    1, 'not_searching'),
  (5,  'Lucas',   'Bernard',  1, 'searching'),
  (6,  'Emma',    'Petit',    1, 'searching'),
  (7,  'Hugo',    'Rousseau', 1, 'found'),
  (8,  'Chloe',   'Leblanc',  1, 'searching'),
  (9,  'Tom',     'Moreau',   1, 'searching'),
  (10, 'Alice',   'Garnier',  1, 'found'),
  (11, 'Maxime',  'Henry',    1, 'searching'),
  (12, 'Sarah',   'Dupont',   1, 'not_searching'),
  (13, 'Nathan',  'Girard',   1, 'searching'),
  (14, 'Camille', 'Robert',   1, 'searching');

-- ============================================================
-- LOCALISATIONS
-- ============================================================

INSERT INTO location (city, department, postal_code, region) VALUES
  ('Paris',       'Paris',            '75000', 'Ile-de-France'),
  ('Lyon',        'Rhone',            '69000', 'Auvergne-Rhone-Alpes'),
  ('Marseille',   'Bouches-du-Rhone', '13000', 'Provence-Alpes-Cote d Azur'),
  ('Toulouse',    'Haute-Garonne',    '31000', 'Occitanie'),
  ('Bordeaux',    'Gironde',          '33000', 'Nouvelle-Aquitaine'),
  ('Nantes',      'Loire-Atlantique', '44000', 'Pays de la Loire'),
  ('Lille',       'Nord',             '59000', 'Hauts-de-France'),
  ('Strasbourg',  'Bas-Rhin',         '67000', 'Grand Est'),
  ('Rennes',      'Ille-et-Vilaine',  '35000', 'Bretagne'),
  ('Grenoble',    'Isere',            '38000', 'Auvergne-Rhone-Alpes'),
  ('Montpellier', 'Herault',          '34000', 'Occitanie'),
  ('Nice',        'Alpes-Maritimes',  '06000', 'Provence-Alpes-Cote d Azur');

-- ============================================================
-- ENTREPRISES
-- ============================================================

INSERT INTO company (name, description, email, phone, rating) VALUES
  ('Capgemini',    'Leader mondial des services de conseil et transformation numerique.',       'stages@capgemini.fr',    '01 47 54 50 00', 4.2),
  ('Thales',       'Groupe industriel specialise dans l aerospatiale et la defense.',           'rh@thales.com',          '01 57 77 80 00', 3.9),
  ('Sopra Steria', 'Groupe europeen de conseil et services numeriques.',                        'alternance@sopra.fr',    '01 40 67 29 29', 4.0),
  ('Airbus',       'Constructeur aeronautique et spatial europeen de premier plan.',            'stages@airbus.com',      '05 61 93 33 33', 4.5),
  ('Orange',       'Operateur telecom et services numeriques present dans 26 pays.',            'recrutement@orange.com', '01 44 44 22 22', 3.7),
  ('Renault',      'Constructeur automobile innovant dans la mobilite durable.',                'stages@renault.com',     '01 76 84 04 04', 4.1),
  ('SNCF',         'Operateur ferroviaire national et acteur de la mobilite en France.',        'alternance@sncf.fr',     '01 53 25 60 00', 3.5),
  ('BNP Paribas',  'Premier groupe bancaire europeen.',                                         'rh@bnpparibas.com',      '01 40 14 45 46', 3.8),
  ('Decathlon',    'Leader mondial dans la conception et vente d articles de sport.',           'stages@decathlon.fr',    '03 28 33 33 33', 4.3),
  ('Ubisoft',      'Editeur de jeux video francais reconnu mondialement.',                      'jobs@ubisoft.com',       '01 48 18 50 00', 4.6);

-- ============================================================
-- COMPETENCES
-- ============================================================

INSERT INTO skill (name) VALUES
  ('Python'),
  ('JavaScript'),
  ('React'),
  ('Java'),
  ('SQL'),
  ('Docker'),
  ('Git'),
  ('PHP'),
  ('C++'),
  ('Machine Learning'),
  ('Cybersecurite'),
  ('UX/UI Design'),
  ('Gestion de projet'),
  ('DevOps'),
  ('Angular');

-- ============================================================
-- OFFRES
-- ============================================================

INSERT INTO offer (title, description, salary, type, mode, publication_date, is_active, company_id, location_id) VALUES
  ('Developpeur Full-Stack',         'Developpement de nouvelles fonctionnalites pour notre plateforme SaaS en React et Node.js.',    '600 euros/mois',  'internship',    'hybrid',  '2026-01-10', 1, 1,  1),
  ('Alternant DevOps',               'Mise en place de pipelines CI/CD et gestion des infrastructures cloud AWS.',                    '1200 euros/mois', 'apprenticeship','remote',  '2026-01-15', 1, 1,  2),
  ('Data Scientist',                 'Analyse de donnees massives et entrainement de modeles ML pour optimiser nos processus.',       '650 euros/mois',  'internship',    'on_site', '2026-01-20', 1, 2,  3),
  ('Alternant Cybersecurite',        'Audit de securite, gestion des vulnerabilites et politiques de securite SI.',                   '1300 euros/mois', 'apprenticeship','on_site', '2026-01-22', 1, 2,  1),
  ('Developpeur Backend Java',       'Developpement de microservices en Java Spring Boot pour notre core banking.',                   '580 euros/mois',  'internship',    'hybrid',  '2026-02-01', 1, 3,  7),
  ('Alternant UX Designer',          'Conception d interfaces utilisateur pour nos applications mobiles et web.',                     '1100 euros/mois', 'apprenticeship','remote',  '2026-02-03', 1, 3,  2),
  ('Ingenieur Systemes Embarques',   'Integration et validation de systemes embarques pour le secteur avionique.',                    '700 euros/mois',  'internship',    'on_site', '2026-02-05', 1, 4,  4),
  ('Alternant Developpeur C++',      'Developpement de logiciels embarques temps reel pour systemes de navigation.',                  '1250 euros/mois', 'apprenticeship','on_site', '2026-02-10', 1, 4,  4),
  ('Developpeur Web PHP',            'Creation et maintenance de sites web et applications internes en PHP.',                         '560 euros/mois',  'internship',    'remote',  '2026-02-12', 1, 5,  6),
  ('Alternant Data Analyst',         'Analyse de donnees clients et creation de tableaux de bord pour la direction.',                 '1150 euros/mois', 'apprenticeship','hybrid',  '2026-02-15', 1, 5,  1),
  ('Marketing Digital',              'Gestion de campagnes digitales, reseaux sociaux et SEO.',                                       '530 euros/mois',  'internship',    'hybrid',  '2026-02-18', 1, 6,  5),
  ('Alternant Developpeur Python',   'Automatisation de processus industriels et developpement d outils d analyse.',                  '1200 euros/mois', 'apprenticeship','on_site', '2026-02-20', 1, 6,  5),
  ('Chef de Projet SI',              'Pilotage de projets de transformation numerique et animation d equipes.',                       '620 euros/mois',  'internship',    'on_site', '2026-02-22', 1, 7,  7),
  ('Alternant Developpeur Angular',  'Developpement d applications web en Angular pour le portail client.',                           '1300 euros/mois', 'apprenticeship','hybrid',  '2026-03-01', 1, 8,  1),
  ('Developpeur Mobile',             'Developpement d applications mobiles cross-platform en React Native.',                          '600 euros/mois',  'internship',    'remote',  '2026-03-05', 1, 9,  8),
  ('Alternant Game Developer',       'Conception et developpement de mecaniques de jeu pour notre prochain titre AAA.',              '1400 euros/mois', 'apprenticeship','on_site', '2026-03-08', 1, 10, 2),
  ('Intelligence Artificielle',      'Recherche et developpement sur des algorithmes ML pour optimiser nos recommandations.',         '680 euros/mois',  'internship',    'hybrid',  '2026-03-10', 1, 10, 1),
  ('Alternant Admin Reseau',         'Administration et securisation de l infrastructure reseau nationale.',                          '1250 euros/mois', 'apprenticeship','on_site', '2026-03-12', 1, 7,  3),
  ('Developpeur Java',               'Developpement et maintenance d APIs RESTful en Java pour nos systemes de reservation.',         '590 euros/mois',  'internship',    'hybrid',  '2026-03-15', 1, 7,  9),
  ('Alternant QA Engineer',          'Mise en place de tests automatises et gestion de la qualite logicielle.',                       '1100 euros/mois', 'apprenticeship','remote',  '2026-03-18', 1, 1,  10);

-- ============================================================
-- COMPETENCES PAR OFFRE
-- ============================================================

INSERT INTO offer_skill (offer_id, skill_id) VALUES
  (1,  2),(1,  3),(1,  7),
  (2,  6),(2,  7),(2,  14),
  (3,  1),(3,  5),(3,  10),
  (4,  11),(4, 7),
  (5,  4),(5,  5),(5,  7),
  (6,  12),(6, 2),
  (7,  9),(7,  7),
  (8,  9),(8,  7),
  (9,  8),(9,  5),(9,  7),
  (10, 1),(10, 5),
  (11, 13),(11,2),
  (12, 1),(12, 6),(12, 7),
  (13, 13),(13,5),
  (14, 15),(14,5),(14,7),
  (15, 2),(15, 3),(15, 7),
  (16, 9),(16, 2),(16, 7),
  (17, 1),(17, 10),(17,5),
  (18, 11),(18,14),(18,7),
  (19, 4),(19, 5),(19, 7),
  (20, 7),(20, 6),(20, 14);

-- ============================================================
-- CANDIDATURES
-- ============================================================

INSERT INTO application (student_id, offer_id, status, message, cv_path, lm_path) VALUES
  (5,  1,  'sent',     'Je suis passionne de developpement web et souhaite rejoindre votre equipe.', '', ''),
  (5,  3,  'accepted', 'Tres interesse par la data science et le machine learning.',                  '', ''),
  (6,  2,  'sent',     'Le DevOps me passionne, j ai deja travaille avec Docker et GitLab CI.',      '', ''),
  (6,  9,  'refused',  'Je cherche une alternance en developpement web full stack.',                  '', ''),
  (7,  5,  'accepted', 'Mon projet de fin d etudes porte sur Java Spring Boot.',                      '', ''),
  (7,  7,  'sent',     'Passionne d embarque, j ai deja realise des projets en C.',                   '', ''),
  (8,  6,  'sent',     'Le design d interface est ma specialite, j utilise Figma quotidiennement.',   '', ''),
  (8,  15, 'sent',     'Je developpe des apps React Native depuis 2 ans en autonomie.',               '', ''),
  (9,  4,  'refused',  'La cybersecurite est mon domaine de predilection.',                           '', ''),
  (9,  14, 'sent',     'Angular est le framework que j utilise dans mes projets personnels.',         '', ''),
  (10, 10, 'accepted', 'J ai de solides bases en SQL et Python pour l analyse de donnees.',           '', ''),
  (11, 12, 'sent',     'Python est mon langage principal, je l utilise pour l automatisation.',       '', ''),
  (11, 17, 'sent',     'Je travaille sur des projets de ML dans le cadre de ma formation.',           '', ''),
  (12, 11, 'refused',  'Je gere les reseaux sociaux de mon association depuis 3 ans.',                '', ''),
  (13, 19, 'sent',     'Java est le langage que j utilise dans tous mes projets de groupe.',          '', ''),
  (13, 20, 'sent',     'Je mets en place des tests automatises avec JUnit dans mes projets.',         '', ''),
  (14, 16, 'sent',     'Passionnee de jeux video et de programmation C++.',                           '', ''),
  (14, 1,  'sent',     'React est le framework que j utilise au quotidien dans mes projets.',         '', '');

-- ============================================================
-- WISHLIST
-- ============================================================

INSERT INTO wishlist (student_id, offer_id) VALUES
  (5,  2),(5,  4),
  (6,  1),(6,  3),(6,  6),
  (7,  8),(7,  16),
  (8,  14),(8, 17),
  (9,  1),(9,  5),(9,  12),
  (10, 3),(10, 17),
  (11, 1),(11, 2),(11, 20),
  (13, 4),(13, 19),
  (14, 16);

-- ============================================================
-- PROMOTIONS
-- ============================================================

INSERT INTO promotion (name, year, pilot_id) VALUES
  ('Informatique et Reseaux 2026',           2026, 2),
  ('Cybersecurite et DevOps 2026',           2026, 3),
  ('Data et Intelligence Artificielle 2026', 2026, 4);

-- ============================================================
-- ETUDIANTS PAR PROMOTION
-- ============================================================

INSERT INTO student_promotion (student_id, promotion_id) VALUES
  (5,  1),(6,  1),(7,  1),(8,  1),
  (9,  2),(10, 2),(11, 2),
  (12, 3),(13, 3),(14, 3);
