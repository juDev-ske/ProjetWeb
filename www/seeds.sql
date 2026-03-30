USE monsite;

-- =============================================
-- LOCATIONS
-- =============================================
INSERT INTO location (city, department, postal_code, region) VALUES
('Paris',     'Paris',              '75000', 'Île-de-France'),
('Lyon',      'Rhône',              '69000', 'Auvergne-Rhône-Alpes'),
('Bordeaux',  'Gironde',            '33000', 'Nouvelle-Aquitaine'),
('Toulouse',  'Haute-Garonne',      '31000', 'Occitanie'),
('Nantes',    'Loire-Atlantique',   '44000', 'Pays de la Loire');

-- =============================================
-- COMPANIES
-- =============================================
INSERT INTO company (name, description, email, phone, rating) VALUES
('Google',      'Leader mondial de la technologie.',                          'recrutement@google.com',   '+33 1 42 00 00 00', 4.8),
('Microsoft',   'Éditeur de logiciels et services cloud.',                   'rh@microsoft.fr',          '+33 1 42 00 00 01', 4.5),
('Amazon',      'E-commerce et cloud computing.',                             'jobs@amazon.fr',           '+33 1 42 00 00 02', 4.2),
('Capgemini',   'Conseil et services numériques.',                            'carrieres@capgemini.com',  '+33 1 42 00 00 03', 4.0),
('Orange',      'Opérateur télécom et services numériques.',                  'recrutement@orange.fr',    '+33 1 42 00 00 04', 4.1);

-- =============================================
-- SKILLS
-- =============================================
INSERT INTO skill (name) VALUES
('React'), ('TypeScript'), ('PHP'), ('MySQL'), ('Docker'),
('Python'), ('SQL'), ('Power BI'), ('Node.js'), ('AWS'),
('Figma'), ('Vue.js'), ('Git'), ('Linux'), ('Java');

-- =============================================
-- OFFERS
-- =============================================
INSERT INTO offer (title, description, salary, type, mode, publication_date, company_id, location_id) VALUES
('Développeur Front-end',
 'Rejoignez notre équipe pour développer des interfaces modernes à grande échelle.',
 '18 000 – 22 000 €/an', 'apprenticeship', 'on_site', '2025-03-27', 1, 1),

('Data Analyst',
 'Analysez les données clients pour améliorer nos produits et services.',
 '800 – 1 200 €/mois', 'internship', 'hybrid', '2025-03-26', 2, 2),

('Développeur Back-end',
 'Développez et maintenez nos APIs REST et microservices.',
 '20 000 – 25 000 €/an', 'apprenticeship', 'remote', '2025-03-28', 3, 3),

('UX/UI Designer',
 'Concevez des expériences utilisateur intuitives et esthétiques.',
 '16 000 – 19 000 €/an', 'apprenticeship', 'hybrid', '2025-03-24', 4, 4),

('Développeur Mobile',
 'Développez des applications mobiles iOS et Android.',
 '600 – 900 €/mois', 'internship', 'on_site', '2025-03-25', 5, 5);

-- =============================================
-- OFFER SKILLS
-- =============================================
INSERT INTO offer_skill (offer_id, skill_id) VALUES
(1, 1), (1, 2), (1, 11), (1, 13),  -- Front-end: React, TypeScript, Figma, Git
(2, 7), (2, 6), (2, 8),             -- Data: SQL, Python, Power BI
(3, 9), (3, 10), (3, 15),           -- Back-end: Node.js, AWS, Java
(4, 11), (4, 12),                   -- UX: Figma, Vue.js
(5, 1), (5, 13);                    -- Mobile: React, Git

-- =============================================
-- USERS (passwords hashés avec bcrypt)
-- admin123 / pilote123 / etudiant123
-- =============================================
INSERT INTO user (email, password, role) VALUES
('admin@cesitionjob.fr',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('pilote@cesitionjob.fr',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote'),
('etudiant@cesitionjob.fr','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant');

-- =============================================
-- PROFILES
-- =============================================
INSERT INTO profile (user_id, first_name, last_name, is_active) VALUES
(1, 'Super',  'Admin',   TRUE),
(2, 'Marie',  'Martin',  TRUE),
(3, 'Jean',   'Dupont',  TRUE);

-- =============================================
-- PROMOTION
-- =============================================
INSERT INTO promotion (name, year, pilot_id) VALUES
('BUT Informatique 2024', 2024, 2),
('Master Cybersécurité 2025', 2025, 2);

-- =============================================
-- STUDENT IN PROMOTION
-- =============================================
INSERT INTO student_promotion (student_id, promotion_id) VALUES
(3, 1);
