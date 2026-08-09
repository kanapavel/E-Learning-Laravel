# 🎓 Skillora - Plateforme E-Learning Intelligente

[![Laravel](https://img.shields.io/badge/Laravel-13.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-blue.svg)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38bdf8.svg)](https://tailwindcss.com)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-4e56a6.svg)](https://livewire.laravel.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.x-4479a1.svg)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## 📖 À propos

**Skillora** est une plateforme e‑learning moderne, flexible et intelligente qui connecte étudiants, instructeurs et administrateurs. Elle intègre des fonctionnalités avancées d'intelligence artificielle pour accompagner aussi bien les apprenants que les formateurs dans leur parcours.

> 🚀 **Le sanctuaire intellectuel pour les esprits curieux**

### 🎯 Objectifs

- Offrir une expérience d'apprentissage en ligne complète et intuitive
- Faciliter la création et la gestion de cours pour les instructeurs
- Accompagner les étudiants avec un assistant IA contextuel
- Permettre des sessions en direct interactives
- Fournir des outils d'analyse et de suivi de progression

---

## ✨ Fonctionnalités principales

### 👨‍🎓 Pour les Étudiants

- 📚 **Catalogue de cours** : Parcourir, filtrer et rechercher des cours
- 📝 **Inscription aux cours** : S'inscrire et accéder au contenu
- 📖 **Leçons interactives** : Vidéo, texte ou mixte
- ✅ **Suivi de progression** : Marquage des leçons terminées
- 📊 **Quiz auto-corrigés** : QCM, vrai/faux avec correction automatique
- 💬 **Forum par cours** : Poser des questions, échanger avec la communauté
- 🤖 **Assistant IA étudiant** : Chatbot contextuel avec synthèse de cours
- 🔔 **Notifications** : Alertes en temps réel
- 📱 **Interface responsive** : Accessible sur tous les appareils

### 👨‍🏫 Pour les Instructeurs

- 🎯 **Gestion de cours** : Créer, modifier, publier des cours
- 📂 **Chapitres et leçons** : Organisation modulaire
- 🎥 **Leçons variées** : Vidéo, texte, mixte avec ressources
- 📝 **Quiz paramétrables** : Score de passage, temps limite, tentatives
- 🤖 **Assistant IA instructeur** : Génération de plan, contenu, quiz, optimisation
- 📺 **Sessions en direct** : Streaming YouTube, visioconférence Yandex Telemost
- 📊 **Tableau de bord** : Statistiques, inscriptions, revenus
- 🎥 **Simulation de caméra** : Tests et démonstrations

### 🔧 Pour les Administrateurs

- 👥 **Gestion des utilisateurs** : Modification des rôles, suppression
- 📊 **Statistiques globales** : Superviser l'activité de la plateforme
- 🛡️ **Modération** : Gérer les contenus inappropriés

---

## 🧠 Intelligence Artificielle

Skillora intègre deux assistants IA via l'**API Groq** (modèle `llama-3.1-8b-instant`) :

### 🤖 Assistant Étudiant
- Chatbot contextuel (connaît le cours en cours)
- Synthèse structurée de cours
- Suggestions de questions pour approfondir
- Recommandations personnalisées

### 🎯 Assistant Instructeur
- **Plan** : Génération automatique de plan de cours
- **Contenu** : Création de contenu de leçon structuré
- **Quiz** : Génération de QCM avec réponses
- **Optimiser** : Suggestions d'amélioration du cours

---

## 🛠️ Technologies

### Backend
| Technologie | Version | Rôle |
|-------------|---------|------|
| Laravel | 13.x | Framework principal |
| PHP | 8.3+ | Langage |
| MySQL | 8.x | Base de données |

### Frontend
| Technologie | Version | Rôle |
|-------------|---------|------|
| Tailwind CSS | 3.x | Framework CSS |
| Alpine.js | 3.x | Interactions dynamiques |
| Livewire | 3.x | Widgets flottants |
| Vite | 5.x | Build tool |

### IA & Services externes
| Service | Rôle |
|---------|------|
| Groq API | Assistants IA (modèle llama-3.1-8b-instant) |
| Yandex Telemost | Visioconférence interactive |
| YouTube Live | Streaming en direct |
| Railway | Hébergement (production) |

---

## 📦 Installation

### Prérequis

- PHP 8.3+
- Composer
- Node.js 18+ / NPM
- MySQL 8+

### Étapes d'installation

```bash
# 1. Cloner le dépôt
git clone https://github.com/votre-repo/skillora.git
cd skillora

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances frontend
npm install

# 4. Configurer l'environnement
cp .env.example .env

# 5. Générer la clé d'application
php artisan key:generate

# 6. Configurer la base de données dans .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=elearning
DB_USERNAME=root
DB_PASSWORD=rootpassword

# 7. Configurer la clé API Groq dans .env
GROQ_API_KEY=gsk_votre_cle_ici

# 8. Créer le lien symbolique pour le stockage
php artisan storage:link

# 9. Lancer les migrations et les seeders
php artisan migrate --seed

# 10. Lancer le serveur de développement
php artisan serve
# et dans un autre terminal
npm run dev
```

---

## 🔑 Accès par défaut (après seeding)

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| **Administrateur** | admin@elearning.com | password |
| **Instructeur** | instructor@elearning.com | password |
| **Étudiant** | student@elearning.com | password |

---

## 🏗️ Structure du projet

```
Skillora/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Contrôleurs
│   │   ├── Livewire/             # Widgets Livewire
│   │   │   ├── AiAssistant.php   # Assistant étudiant
│   │   │   └── InstructorAssistant.php # Assistant instructeur
│   │   └── Middleware/           # Middleware personnalisé
│   ├── Livewire/                 # Composants Livewire
│   ├── Models/                   # Modèles Eloquent
│   ├── Services/                 # Services (AIService, etc.)
│   └── Policies/                 # Politiques d'autorisation
├── config/                       # Configuration
├── database/
│   ├── migrations/               # Migrations DB
│   └── seeders/                  # Données de test
├── resources/
│   ├── views/                    # Vues Blade
│   │   ├── layouts/              # Layout principal
│   │   ├── livewire/             # Vues Livewire
│   │   ├── instructor/           # Vues instructeur
│   │   └── courses/              # Vues cours
│   └── css/                      # Assets Tailwind
├── routes/
│   └── web.php                   # Routes
├── public/                       # Fichiers publics
└── .env                          # Variables d'environnement
```

---

## 🚀 Déploiement sur Railway

1. Pousser le code sur GitHub
2. Créer un projet sur Railway
3. Lier le dépôt GitHub
4. Configurer les variables d'environnement :
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=https://skillora.railway.app`
   - `DATABASE_URL=mysql://...`
   - `GROQ_API_KEY=gsk_...`
5. Déployer automatiquement
6. Exécuter les migrations :
   ```bash
   php artisan migrate --force
   ```

---

## 📱 Widgets flottants

Skillora dispose de deux widgets flottants accessibles en bas à droite de l'écran :

| Widget | Icône | Utilisateur | Fonction |
|--------|-------|-------------|----------|
| **Assistant IA étudiant** | 🤖 | Étudiant / Instructeur | Chatbot contextuel, synthèse de cours |
| **Assistant IA instructeur** | 🎯 | Instructeur | Génération de plan, contenu, quiz, optimisation |

---

## 🤝 Contribution

Les contributions sont les bienvenues ! Voici comment procéder :

1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commiter vos modifications (`git commit -m 'Add some AmazingFeature'`)
4. Pousser la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

---

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus d'informations.

---


## 🔗 Liens utiles

- [Laravel Documentation](https://laravel.com/docs)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Livewire Documentation](https://livewire.laravel.com/docs)
- [Groq API Documentation](https://console.groq.com/docs)
- [Railway Documentation](https://docs.railway.app)

---

## 📊 Aperçu des fonctionnalités

| Fonctionnalité | Statut |
|----------------|--------|
| Authentification (login/register) | ✅ |
| Gestion des rôles (student/instructor/admin) | ✅ |
| Catalogue de cours avec filtres | ✅ |
| Inscription aux cours | ✅ |
| Suivi de progression | ✅ |
| Leçons (vidéo/texte/mixte) | ✅ |
| Quiz auto-corrigés | ✅ |
| Forum par cours | ✅ |
| Notifications en temps réel | ✅ |
| Assistant IA étudiant (chatbot/synthèse) | ✅ |
| Assistant IA instructeur (plan/contenu/quiz/optimisation) | ✅ |
| Sessions en direct (YouTube Live) | ✅ |
| Yandex Telemost (visioconférence) | ✅ |
| Simulation de caméra | ✅ |
| Tableaux de bord (étudiant/instructeur/admin) | ✅ |
| Widgets flottants (Livewire) | ✅ |
| Déploiement sur Railway | ✅ |

---

## 🎯 Roadmap

- [ ] Intégration des paiements en ligne (Stripe, CinetPay)
- [ ] Certificats de réussite
- [ ] Application mobile (Flutter)
- [ ] Classes virtuelles (WebRTC)
- [ ] Badges et gamification
- [ ] Export des données (PDF, CSV)
- [ ] Support multilingue (anglais, français)
- [ ] API publique pour les développeurs tiers

---

📌 **Skillora** - *Le sanctuaire intellectuel pour les esprits curieux.*
