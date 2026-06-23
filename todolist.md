# Todolist — Miamy

> Suivi des fonctionnalités · Admin & Restaurateur
> Priorités : 🔴 haute · 🟠 moyenne · 🟢 basse
> Avancement global : **14 / 22** ✅

---

## ✅ Fonctionnalités réalisées (14)

### Authentification & sécurité
- [x] **Inscription restaurateur et client** — validation serveur, création en 2 tables liées (`utilisateurs` + fiche métier)
- [x] **Connexion / déconnexion** — sessions PHP, redirection par profil, message d'erreur générique
- [x] **Hachage des mots de passe** — bcrypt (`password_hash`), sel automatique, jamais en clair
- [x] **Contrôle d'accès par profil** — gardes en début de méthode (admin / restaurateur / client) + vérifs côté serveur sur l'AJAX
- [x] **Contrôle d'ownership** — un restaurateur n'agit que sur ses propres restaurants et plats (`getOwnedBy` / `isOwnedBy`)
- [x] **Journalisation des actions** — table `user_logs` (connexions, échecs, créations, suppressions)

### Espace restaurateur
- [x] **Dashboard restaurateur** — accès à ses restaurants et à leurs cartes
- [x] **CRUD restaurants** — créer, modifier, supprimer sa fiche (avec upload d'image)
- [x] **CRUD des plats (gestion de la carte)** — créer, lire, modifier, supprimer, avec upload de photo
- [x] **Toggle disponibilité d'un plat en AJAX** — bascule dispo/indispo sans recharger la page (JSON)
- [x] **Drag & Drop des plats entre catégories** — SortableJS + persistance AJAX immédiate
- [x] **Page horaires** — saisie sur 7 jours, désactivation JS des champs d'un jour fermé, enregistrement `ON DUPLICATE KEY UPDATE`

### Espace admin
- [x] **Tableau de bord administrateur** — statistiques globales (utilisateurs, restaurants…)
- [x] **Gestion des comptes & des restaurants (vue admin)** — superviser/modifier les utilisateurs et établissements, changement de profil, suppression

---

## ⬜ À faire (8)

### Admin
- [ ] 🟠 **Validation des nouveaux restaurants** — workflow en attente / approuvé / rejeté avant publication
- [ ] 🟠 **Visualisation des logs** — page pour consulter la table `user_logs` (actions, dates, IPs)
- [ ] 🟢 **Gestion des catégories** — créer/modifier/supprimer les catégories de plats depuis l'interface
- [ ] 🟢 **Gestion des codes promo** — créer/modifier/supprimer des codes promo

### Restaurateur
- [ ] 🔴 **Gestion des commandes** — voir les commandes liées à ses restaurants, changer le statut
- [ ] 🟠 **Statistiques de vente** — plats les plus commandés, CA par période, évolution des ventes
- [ ] 🟢 **Photo de profil restaurateur** — remplacer le placeholder par un vrai upload

### Technique
- [ ] 🟠 **Refactoriser en PSR-4 (autoloader Composer)** — namespaces `Miamy\`, dossiers `src/Models`, `src/Controllers`, `src/Services`

---

### Suivi

| Section | Fait | Total |
|---|---|---|
| Authentification & sécurité | 6 | 6 |
| Espace restaurateur | 6 | 7 |
| Espace admin | 2 | 4 |
| Technique | 0 | 1 |
| **Global** | **14** | **22** |
