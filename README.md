# 🎓 Gestion de Stages - Application Web

> Application de gestion des stages et comptes rendus développée en PHP/MySQL avec Bootstrap

[![PHP Version](https://img.shields.io/badge/PHP-8.1+-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com)

## 📋 Table des matières

- [Vue d'ensemble](#vue-densemble)
- [Fonctionnalités](#fonctionnalités)
- [Technologies utilisées](#technologies-utilisées)
- [Architecture](#architecture)
- [Captures d'écran](#captures-décran)

## 🎯 Vue d'ensemble

Cette application web permet la gestion complète des stages étudiants et de leurs comptes rendus. Elle offre des interfaces distinctes pour les étudiants et les professeurs, avec des fonctionnalités avancées de suivi et de commentaires.

**Projet réalisé dans le cadre d'un examen de développement web.**

### 🌟 Points forts

- **Interface moderne** avec Bootstrap 5.3 et Font Awesome
- **Messages d'accueil personnalisés** selon l'activité de l'étudiant
- **Attribution automatique** des professeurs aux stages
- **Système de commentaires contrôlé** avec restrictions intelligentes
- **Design responsive** adapté mobile et desktop

## ⚡ Fonctionnalités

### 👨‍🎓 Espace Étudiant

- ✅ **Accueil personnalisé** avec messages d'activité
- ✅ **Gestion des comptes rendus** (création, modification, consultation)
- ✅ **Visualisation des commentaires** des professeurs
- ✅ **Profil utilisateur** et informations de stage
- ✅ **Validation des données** côté client et serveur

### 👨‍🏫 Espace Professeur

- ✅ **Vue d'ensemble des élèves** assignés automatiquement
- ✅ **Consultation des comptes rendus** triés par date
- ✅ **Système de commentaires** avec limitations intelligentes
- ✅ **Filtrage par étudiant** pour un suivi personnalisé
- ✅ **Interface dédiée** pour la gestion des stages

### 🔧 Fonctionnalités Administratives

- ✅ **Attribution automatique** des professeurs aux stages
- ✅ **Gestion des utilisateurs** (étudiants, professeurs)
- ✅ **Triggers de base de données** pour l'automatisation
- ✅ **Système de sessions** sécurisé

## 🛠 Technologies utilisées

### Backend
- **PHP 8.1+** - Langage serveur principal
- **MySQL 8.0+** - Base de données relationnelle
- **PDO/MySQLi** - Accès aux données sécurisé

### Frontend
- **HTML5/CSS3** - Structure et style
- **Bootstrap 5.3.0** - Framework CSS responsive
- **Font Awesome 6.4.0** - Icônes vectorielles
- **JavaScript ES6** - Interactions côté client

### Sécurité
- **Sessions PHP** - Gestion de l'authentification
- **Requêtes préparées** - Protection contre l'injection SQL
- **Validation des données** - Côté client et serveur
- **Échappement HTML** - Protection XSS

## 🏗 Architecture

### Structure du projet

```
📦 gestion-stages/
├── 📄 index.php              # Page d'accueil/connexion
├── 📄 inscription.php         # Inscription utilisateurs
├── 📄 accueil_eleve.php      # Dashboard étudiant
├── 📄 accueil_prof.php       # Dashboard professeur
├── 📄 liste_comptes_rendus.php        # CR étudiants
├── 📄 liste_comptes_rendus_prof.php   # CR professeurs
├── 📄 liste_eleves_prof.php          # Élèves assignés
├── 📄 creer_modifier_compte_rendu.php # Gestion CR
├── 📄 commentaires.php       # Système de commentaires
├── 📄 information_stage.php  # Infos stage
├── 📄 perso.php / perso_prof.php     # Profils
├── 📄 _conf.php              # Configuration BDD
├── 📄 bdd.sql                # Structure initiale
└── 📄 modifications_bdd.sql  # Modifications BDD
```

### Base de données

#### Tables principales

- **`user`** - Utilisateurs (étudiants, professeurs)
- **`stage`** - Informations des stages
- **`CR`** - Comptes rendus
- **`commentaires`** - Commentaires des professeurs
- **`statut`** - Statuts utilisateurs

## 📸 Captures d'écran

### Interface Étudiant
- **Accueil personnalisé** avec messages d'activité
- **Gestion des comptes rendus** intuitive
- **Visualisation des commentaires** professeurs

### Interface Professeur  
- **Dashboard** avec élèves assignés
- **Consultation des CR** avec filtres
- **Système de commentaires** contrôlé

---

## 🛠 Technologies et bonnes pratiques

**Backend** : PHP 8.1, MySQL 8.0, Requêtes préparées, Sessions sécurisées  
**Frontend** : Bootstrap 5.3, Font Awesome 6.4, JavaScript ES6  
**Sécurité** : Protection XSS, Anti-injection SQL, Validation des données  

---

**Projet développé pour démontrer la maîtrise du développement web PHP/MySQL**

*Dernière mise à jour : septembre 2025*
