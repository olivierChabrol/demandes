# SIGED
## Prérequis
- Un serveur avec apache2, php7 (le 8 n'a pas été testé) et mysql
- Un serveur ldap

## Installation

##### Configurer le serveur Apache  
Modifier le php.ini pour permettre le transfert de fichier plus important
```sh
max_execution_time = 480
memory_limit = 512M
upload_max_filesize = 8M
date.timezone = Europe/Paris
```

installer les librairies suivantes
```sh
apt install php-dom php-ldap
```

##### Installer l'application

Télécharger ou cloner l'application depuis gitlab : https://gitlab.osupytheas.fr/adias/siged

Exécuter le fichier `install.sh` présent dans la racine pour créer l'arborescene du dossier upload servant à stocker les fichiers
```sh
./install.sh
```
Supprimer le fichier `install.sh`

Changer les droits des dossiers
```sh
chown -R root:www-data *
chmod -R 750 *
chmod -R 770 upload
```

##### Créer la base de donnée

créer une base de donnée en utf8
```sh
mysql -u root -p
CREATE DATABASE mydatabase CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
créer un user spécifique et lui donner les droits complets sur la base
```sh
CREATE USER 'user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON mydatabase.* TO 'user'@'localhost';
FLUSH PRIVILEGES;
exit
```
importer la structure de base dans la BDD (depuis la racine)
```sh
mysql -u user -p mabase < sql/siged-vierge.sql
```
modifier le fichier `connect.php` avec les paramètres de connection de la BDD

## Configuration de l'outil

Se connecter avec le compte admin par défaut :
-   login : admin
-   password : admin

##### LDAP et SMTP

Dans Administration>Paramètres aller dans l'onglet "Connecteurs"

Activer le SMTP et le LDAP et cliquer sur valider

Configurer le SMTP et LDAP en fonction de votre serveur
Dans un premier temps ne pas mettre oui à "Désactiver les utilisateurs  Gestsup lors de la synchronisation". Cette option devra être activer une fois que la configuration de l'outil aura été validée

 **/!\ Attention - même si la possibilité d'utiliser un AD est prévu à la base dans gestsup, SIGED a été conçu pour être utilisé avec LDAP donc il pourrait y avoir des erreurs en utilisant un AD**

##### Utilisateurs

Aller dans Administration>Utilisateurs et cliquer sur Synchronisation LDAP et suivre la procédure de synchronisation

Cliquer ensuite sur un utilisateur qui doit êter admin et dans Paramètres lui donner les droits Administrateurs 

Se reconnecter avec le nouveau compte Admin. Si la connection fonctionne activer la fonctionnalité de désactivation des comptes Gestsup

##### Donnéees budgétaires

Aller dans Administration > Listes > Données budgétaires et "ajouter une entrée" pour les catégories "Commandes" et "Ordres de mission"

Au moins une entrée est nécessaire dans ces catégories pour que l'application fonctionne

##### Synchronisation automatique des utilisateurs - optionnelle

Créer une tache dans la crontab du serveur 
```sh
0 * * * * php /var/www/html/core/ldap.php > /dev/null 2>&1
```

**SIGED est dorénavant pleinement fonctionnel !!**



## ANNEXE - doc LDAP
L'intégration du LDAP dans SIGED a du être beaucoup modifiée pour coller à notre annuaire. De ce fait il important que les champs de votre annuaire correspondent à ce qui a été intégré au code.

le fichier gérant la synchronisation LDAP est `core/ldap.php`

Pour résumer, des champs par défaut étaient prévu dans GESTSUP qui ne correspondaient pas à ceux de notre annuaire. Nous avons donc modifié le code (cf. plus bas) pour les remplacer par les champs de notre annuaire et intégrer certaines spécificités (Multi site, agent dans plusieurs équipes, annuaire multi laboratoire)

Voilà le contenu des champs importants pour l'application :
- departmentNumber : Lieu d'affecttation
- businessCategory : equipe/service (il peut être multiple)
- uidNumber : identifiant unique de l'utilsiateur
- supannaffectation : Nom du laboratoire
- supannorganisme : Nom de la tutelle employeur

Si ces champs sont déjà présent dans votre annuaire mais avec un autre nom il faudra alors changer le code du fichier `core/ldap.php`

Voici l'ensemble des informations qui ont été modifié pour notre instance :
```sh
ligne 145 : ajout des champs departmentNumber,businessCategory,uidNumber,supannaffectation et supannorganisme
ligne 213, 240 : recupération du champs uidNumber pour le ldap-gid de gestsup (identifie les users entre gestsup et ldap)
ligne 220, 246 : recuperation du champs departnumber pour l'adresse(site)
ligne 229, 263-270, 298-301 : récupération du businesscategory à la place du derpartment ( les services dans gestsup > les équipes pour nous)
ligne 225, 252-259 : modif du champs du ldap pour la company (supanaffectation chez nous) - Laboratoire pour nous.
ligne 230, 271 : ajout du champs shadowflag
ligne 232 , 272 : gestion de la tutelle
ligne 246 : gestion du departmentnumber
ligne 252-259 : gestion des laboratoires
ligne 263-270 : gestion des services
ligne 248 : on raccourci le code postal
ligne 305 : fix affichage du service (variable departments) en mode debug
ligne 310, 338 : récupération du custom1 (tutelle)
ligne 349, 362 : prise en charge des shadowflag pour la maj des user
ligne 351 : gestion du zip code complexe ???
ligne 465-473: mise à jour de la tutelle
ligne 549-591: mise à jour des services (gestion des services multiples)
ligne 640-689: synchronisation des services après l'insertion d'un nouvel utilisateur
```

