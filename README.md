# 🐹 Hamsters – Symfony Project

Application web développée avec **Symfony** et **MySQL** dans le cadre d’un projet scolaire.

---

## 🔧 Prérequis

Avant l'installation, assurez-vous d'avoir :

- PHP ≥ 8.1  
- Extensions PHP : `pdo_mysql`, `openssl`, `intl`  
- Composer  
- Serveur MySQL (ou MariaDB)  
- (Optionnel) Symfony CLI  
- (Optionnel) Node.js + npm si vous souhaitez recompiler les assets

---

## 📥 Installation du projet

### 1️⃣ Cloner le dépôt

```bash
git clone https://github.com/KarimHannaallah/Hamsters_Karim_Hannaallah.git
cd Hamsters_Karim_Hannaallah
```

### 2️⃣ Installer les dépendances

```bash
composer install
```

### 3️⃣ Créer le fichier `.env.local`

Créer un fichier `.env.local` à la racine du projet et y copier ceci :

```dotenv
###> symfony/framework-bundle ###
APP_SECRET=e7f7803ccd694355aecb061b547fd067
DATABASE_URL="mysql://root:@127.0.0.1:3306/hamster?charset=utf8mb4"
###< symfony/framework-bundle ###

JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=karim
```

> Adapter `DATABASE_URL` si votre MySQL utilise un mot de passe ou un autre port.

### 4️⃣ Créer la base de données

```bash
php bin/console doctrine:database:create
```

### 5️⃣ Appliquer les migrations

```bash
php bin/console doctrine:migrations:migrate
```

### 6️⃣ Génération des clés JWT (si nécessaires)

```bash
mkdir -p config/jwt

# Clé privée (mot de passe : karim)
openssl genrsa -out config/jwt/private.pem -aes256 4096

# Clé publique
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

Le mot de passe doit être **karim** pour correspondre à `JWT_PASSPHRASE` dans `.env.local`.

---

## 🚀 Lancement de l’application

### Avec Symfony CLI (recommandé)

```bash
symfony serve
```

➡️ http://127.0.0.1:8000

### Sans Symfony CLI

```bash
php -S 127.0.0.1:8000 -t public
```

➡️ http://127.0.0.1:8000

---

## 👤 Comptes de test

| Rôle      | Email              | Mot de passe   |
|----------|--------------------|----------------|
| Admin    | admin@hamster.com  | admin1234      |
| User     | user@hamster.com   | password123    |

---

## 🧪 Commandes utiles

| Action | Commande |
|--------|----------|
| Vider le cache | `php bin/console cache:clear` |
| Voir l’état des migrations | `php bin/console doctrine:migrations:status` |
| Générer une migration | `php bin/console make:migration` |
| Lancer les tests (si présents) | `php bin/phpunit` |

---

## ⚠️ Notes importantes

- Ce projet est destiné à un **usage local / pédagogique**.  
- Les informations sensibles (`APP_SECRET`, clés JWT, mot de passe MySQL, etc.) **ne doivent pas être utilisées en production**.  
- En cas d’erreur, vérifier :
  - `.env.local`
  - Connexion MySQL
  - Permissions des fichiers `config/jwt/*.pem`

---

## 👨‍💻 Auteur

Projet réalisé par **Karim Fayez Hannaallah**.

---

### 🎉 Merci et bonne correction !
