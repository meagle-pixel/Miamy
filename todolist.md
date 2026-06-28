# Todolist — Miamy

> Suivi des fonctionnalités · Admin & Restaurateur
> Priorités : 🔴 haute · 🟠 moyenne · 🟢 basse
> Avancement global : **14 / 22** ✅

---

## ✅ Fonctionnalités réalisées (14)

### Authentification & sécurité
- **Inscription restaurateur et client**  validation serveur, création en 2 tables liées (`utilisateurs` + fiche métier)
- **Connexion / déconnexion** sessions PHP, redirection par profil, message d'erreur générique
- **Hachage des mots de passe**  bcrypt (`password_hash`), sel automatique, jamais en clair
- **Contrôle d'accès par profil**  gardes en début de méthode (admin / restaurateur / client) + vérifs côté serveur sur l'AJAX
- **Contrôle d'ownership**  un restaurateur n'agit que sur ses propres restaurants et plats (`getOwnedBy` / `isOwnedBy`)
- **Journalisation des actions**  table `user_logs` (connexions, échecs, créations, suppressions)

### Espace restaurateur
- **Dashboard restaurateur**  accès à ses restaurants et à leurs cartes
- **CRUD restaurants**  créer, modifier, supprimer sa fiche 
- 🔴 **CRUD des plats (gestion de la carte)**  créer, lire, modifier, supprimer
- 🟠 **Toggle disponibilité d'un plat en AJAX**  bascule dispo/indispo sans recharger la page (JSON)
- 🟠 **Drag & Drop des plats entre catégories**  SortableJS + persistance AJAX immédiate

### Espace admin
- 🔴 **Tableau de bord administrateur**  statistiques globales (utilisateurs, restaurants…)
- 🟠 **Gestion des comptes & des restaurants (vue admin)**  superviser/modifier les utilisateurs et établissements, changement de profil, suppression

---

## ⬜ À faire (8)

### Admin
- [ ] 🟠 **Validation des nouveaux restaurants**  workflow en attente / approuvé / rejeté avant publication
- [ ] 🟠 **Visualisation des logs**  page pour consulter la table `user_logs` (actions, dates, IPs)
- [ ] 🟢 **Gestion des catégories**  créer/modifier/supprimer les catégories de plats depuis l'interface
- [ ] 🟢 **Gestion des codes promo**  créer/modifier/supprimer des codes promo

### Restaurateur
- [ ] 🔴 **Gestion des commandes**  voir les commandes liées à ses restaurants, changer le statut
- [ ] 🟠 **Statistiques de vente**  plats les plus commandés, CA par période, évolution des ventes
- [ ] 🟢 **Photo de profil restaurateur**  remplacer le placeholder par un vrai upload
