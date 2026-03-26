<?php

namespace App\Controllers;

// NOTE: l'autoloading PSR-4 doit fournir les classes modèles via `App\Models`.
use App\Models\Entreprise;

// MANQUE: connexion/session pour vérifier si l'utilisateur est connecté et son rôle (administrateur, pilote, etc.).
class EntrepriseController {
    private Entreprise $model;
    private $twig;

    public function __construct($twig = null) {
        $this->twig  = $twig; // injection optionnelle de l'environnement Twig
        $this->model = new Entreprise();
    }

    public function handle(): void {
        $action = $_GET['action'] ?? 'liste';

        match ($action) {
            'liste'    => $this->liste(),
            'voir'     => $this->voir(),
            'creer'    => $this->creer(),
            'modifier' => $this->modifier(),
            'supprimer'=> $this->supprimer(),
            default    => $this->liste(),
        };
    }

    private function liste(): void {
        $motCle  = trim($_GET['q'] ?? '');
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;

        if ($motCle !== '') {
            $entreprises = $this->model->search($motCle, $page, $perPage);
            $total       = $this->model->countSearch($motCle);
        } else {
            $entreprises = $this->model->getAll($page, $perPage);
            $total       = $this->model->countAll();
        }

        $totalPages = (int) ceil($total / $perPage);

        // Préparer variables pour la vue
        $succesKey = $_GET['succes'] ?? null;
        $message_succes = '';
        if ($succesKey === 'cree') {
            $message_succes = ' Entreprise créée avec succès.';
        } elseif ($succesKey === 'modifie') {
            $message_succes = ' Entreprise modifiée avec succès.';
        } elseif ($succesKey === 'supprime') {
            $message_succes = ' Entreprise supprimée.';
        }

        $vars = [
            'entreprises' => $entreprises,
            'total' => $total,
            'motCle' => $motCle,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
            'estConnecte' => estConnecte(),
            'utilisateur' => $_SESSION['utilisateur'] ?? null,
            'role' => getRoleUtilisateur(),
            'is_granted' => in_array(getRoleUtilisateur(), ['administrateur', 'pilote']),
            'succes' => $succesKey !== null,
            'message_succes' => $message_succes,
        ];

        if ($this->twig) {
            echo $this->twig->render('entreprises/liste.twig', $vars);
        } else {
            // compat fallback si aucun Twig n'est injecté
            render('entreprises/liste.twig', $vars);
        }
    }

    private function voir(): void {
        $id = (int)($_GET['id'] ?? 0);
        $entreprise = $this->model->getById($id);

        if (!$entreprise) {
            die("Entreprise introuvable.");
        }

        $vars = [
            'entreprise' => $entreprise,
            'estConnecte' => estConnecte(),
            'utilisateur' => $_SESSION['utilisateur'] ?? null,
            'role' => getRoleUtilisateur(),
            'is_granted' => in_array(getRoleUtilisateur(), ['administrateur', 'pilote']),
        ];

        if ($this->twig) {
            echo $this->twig->render('entreprises/voir.twig', $vars);
        } else {
            render('entreprises/voir.twig', $vars);
        }
    }

    private function creer(): void {
        autoriser(['administrateur', 'pilote']);

        $erreurs = [];
        $donnees = ['nom' => '', 'description' => '', 'email' => '', 'telephone' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $donnees['nom']         = trim($_POST['nom'] ?? '');
            $donnees['description'] = trim($_POST['description'] ?? '');
            $donnees['email']       = trim($_POST['email'] ?? '');
            $donnees['telephone']   = trim($_POST['telephone'] ?? '');

            if (empty($donnees['nom'])) {
                $erreurs[] = "Le nom de l'entreprise est obligatoire.";
            }
            if (!empty($donnees['email']) && !filter_var($donnees['email'], FILTER_VALIDATE_EMAIL)) {
                $erreurs[] = "L'adresse email n'est pas valide.";
            }

            if (empty($erreurs)) {
                $this->model->create(
                    $donnees['nom'],
                    $donnees['description'],
                    $donnees['email'],
                    $donnees['telephone']
                );
                header('Location: index.php?action=liste&succes=cree');
                exit;
            }
        }

        $vars = [
            'estModification' => false,
            'titrePage' => "Ajouter une entreprise",
            'actionUrl' => 'index.php?action=creer',
            'erreurs' => $erreurs,
            'donnees' => $donnees,
            'entreprise' => null,
            'estConnecte' => estConnecte(),
            'utilisateur' => $_SESSION['utilisateur'] ?? null,
            'role' => getRoleUtilisateur(),
            'is_granted' => in_array(getRoleUtilisateur(), ['administrateur', 'pilote']),
        ];

        if ($this->twig) {
            echo $this->twig->render('entreprises/formulaire.twig', $vars);
        } else {
            render('entreprises/formulaire.twig', $vars);
        }
    }

    private function modifier(): void {
        autoriser(['administrateur', 'pilote']);

        $id = (int)($_GET['id'] ?? 0);
        $entreprise = $this->model->getById($id);

        if (!$entreprise) {
            die("Entreprise introuvable.");
        }

        $erreurs = [];
        $donnees = [
            'nom'         => $entreprise['nom'],
            'description' => $entreprise['description'],
            'email'       => $entreprise['email'],
            'telephone'   => $entreprise['telephone'],
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $donnees['nom']         = trim($_POST['nom'] ?? '');
            $donnees['description'] = trim($_POST['description'] ?? '');
            $donnees['email']       = trim($_POST['email'] ?? '');
            $donnees['telephone']   = trim($_POST['telephone'] ?? '');

            if (empty($donnees['nom'])) {
                $erreurs[] = "Le nom de l'entreprise est obligatoire.";
            }
            if (!empty($donnees['email']) && !filter_var($donnees['email'], FILTER_VALIDATE_EMAIL)) {
                $erreurs[] = "L'adresse email n'est pas valide.";
            }

            if (empty($erreurs)) {
                $this->model->update(
                    $id,
                    $donnees['nom'],
                    $donnees['description'],
                    $donnees['email'],
                    $donnees['telephone']
                );
                header('Location: index.php?action=voir&id=' . $id . '&succes=modifie');
                exit;
            }
        }

        $vars = [
            'estModification' => true,
            'titrePage' => 'Modifier l\'entreprise',
            'actionUrl' => 'index.php?action=modifier&id=' . $id,
            'erreurs' => $erreurs,
            'donnees' => $donnees,
            'entreprise' => $entreprise,
            'estConnecte' => estConnecte(),
            'utilisateur' => $_SESSION['utilisateur'] ?? null,
            'role' => getRoleUtilisateur(),
            'is_granted' => in_array(getRoleUtilisateur(), ['administrateur', 'pilote']),
        ];

        if ($this->twig) {
            echo $this->twig->render('entreprises/formulaire.twig', $vars);
        } else {
            render('entreprises/formulaire.twig', $vars);
        }
    }

    private function supprimer(): void {
        autoriser(['administrateur', 'pilote']);

        $id = (int)($_GET['id'] ?? 0);

        if ($this->model->getById($id)) {
            $this->model->delete($id);
        }

        header('Location: index.php?action=liste&succes=supprime');
        exit;
    }
}
