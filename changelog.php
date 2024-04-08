<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<style type="text/css">@media screen and (max-width: 980px) {body{font-size: 18px}}</style>
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 15/09/2020             <br />
# @Version : 3.2.4	    	 	 <br />
#################################<br />
<br />
<u>Update :</u><br />
- Ticket : Sélection automatique du demandeur lorsqu'il vient d'être ajouté (./includes/ticket_user.php)<br />
- Ticket : Plus de rechargement de page lors de la sélection d'une catégorie (./ticket.php)<br />
- Ticket : Les pièces jointe de type image s'ouvre directement sans téléchargement préalable (./core/download.php)<br />
- Liste des tickets : Il est possible d'afficher le prénom complet via le droit dashboard_firstname (./dashboard.php)<br />
- Liste des tickets : Affichage d'une icône trombone dans la colonne titre, si le ticket possède une pièce jointe (./dashboard.php)<br />
- Listes : Amélioration de l'affichage de toutes les listes (./*)<br />
- Système : Ajout de la version du protocole HTTP utilisée (./système/*)<br />
- Mail : Amélioration de la prise en charge des messages d'erreur liée au connecteur SMTP (./core/mail.php)<br />
- Mail automatique : Ajout de la société dans le mail automatique à une adresse mail lors de l'ouverture d'une ticket par un demandeur (./core/auto_mail.php)<br />
- Modèle de mail : Nouveau tag #ticket_id# disponible (./core/mail.php)<br />
- Administration : Dans la liste des utilisateurs il est possible de lancer une recherche par nom de société (./admin/user.php)<br />
- Administration : Dans la liste des utilisateurs et sur la fiche utilisateur, des badges de couleur ont été ajoutés (./admin/user.php)<br />
- Barre utilisateur : Les demandeurs ont une notification visuel de ticket non lus, si le technicien ajoute un élément de résolution(./admin/userbar.php)<br />
- Thème : Améliorations du thème sombre (./template/ace/*)<br />
- Connecteur IMAP : Modification automatique de l'état du ticket de résolu vers en cours, lors de l'ajout d'une réponse par le demandeur.(./mail2ticket.php)
- Composant : FullCalendar v5.3.2 (./components/fullcalendar/*)<br />
- Composant : Ace v3.0.1 (./template/ace/*)<br />
- Composant : Highcharts v8.2.0 (./components/Highcharts/*)<br />
- Composant : Moment v2.28.0 (./components/moment/*)<br />
<u>Bugfix :</u><br />  
- Ticket : Message d'erreur lors de l'ajout d'un utilisateur sur un nouveau ticket dans certains cas (./includes/ticket_user.php)<br />
- Ticket : Perte des informations saisies lors de l'ajout d'un utilisateur dans certains cas (./includes/ticket_user.php)<br />
- Ticket : Erreur javascript sur la fonction d'enregistrement du ticket via CTRL+S (./ticket.php)<br />
- Ticket : Perte de la sélection de la pièce jointe lors d'un changement de catégorie (./ticket.php)<br />
- Liste des tickets : La liste était vide lors de l'utilisation d'un double cloisonnement agence et service pour les utilisateurs (./main.php ./userbar.php)<br />
- Liste des tickets : Erreur de sens de trie sur la colonne date de résolution, lors de la définition d'un paramètre personnel (./dashboard.php)<br />
- Liste des tickets : Erreur lors de l'activation du droit side_company sur un profil ayant la vue activité (./dashboard.php)<br />
- Sondage : Erreur de définition de l'intitulé de l'émetteur (./core/message.php)<br />
- Sondage : Erreur d'envoi de mail si l'adresse de l'émetteur n'était pas renseignée (./core/message.php)<br />
- Administration liste: Lors de la suppression d'un service, le service n'était pas supprimé de la liste des catégories (./admin/list.php)<br />
- Administration liste: Lors de la suppression d'une catégorie, la catégorie n'était pas supprimée de la liste des sous-catégories (./admin/list.php)<br />
- Administration utilisateurs : Erreur de trie par nom dans la liste des utilisateurs (./admin/user.php)<br />
- Statistiques : Erreur d'affichage avec un double cloisonnement par agence et service pour un profil utilisateur (./stat.php)<br />
- Mail : Erreur d'accès à la page de prévisualisation du mail avec le cloisonnement par agence avec un profil utilisateur. (./main.php)<br />
- Mail : Envoi de mail à l'agence si le champ est affiché sinon envoi à l'agence de l'utilisateur. (./core/mail.php)<br />
- Projet : Ajout d'un contrôle sur l'existante d'un ticket. (./project.php)<br />
- Sondage : Défaut du texte de message d'erreur sur l'interface utilisateur. (./survey.php)<br />
- Barre titre : Erreur d'affichage du bouton menu avec un nom de société long sur mobile. (./main.php)<br />
- Connecteur LDAP : Pas d'affichage de message d'erreur lors de l'authentification d'un utilisateur AD non présent dans la base GestSup (./login.php)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 12/08/2020             <br />
# @Version : 3.2.3	    	 	 <br />
#################################<br />
<br />
<u>Update :</u><br />
- Système : Ajout d'un contrôle sur le changement du mot de passe de l'utilisateur admin(./system.php)<br />
- Système : Ajout d'un contrôle sur l'utilisation d'un compte administrateur pour le connecteur LDAP (./system.php)<br />
- Mail : Ajout d'un contrôle sur la présence d'une adresse mail d'émission (./core/mail.php)<br />
- Composant : Ace v3.0.0 (./template/ace/*)<br />
- Composant : FullCalendar v5.2.0 (./components/fullcalendar/*)<br />
- Composant : PHPmailer v6.1.7 (./components/phpmailer/*)<br />
- Composant : Fontawesome v5.14.0 (./components/fontawesome/*)<br />
- Composant : Bootstrap v4.5.2 (./components/bootstrap/*)<br />
<u>Bugfix :</u><br />  
- Connecteur IMAP : Incompatibilité avec exchange 2013 (./mail2ticket.php)<br />
- Connecteur IMAP : Erreur d'encodage des mails en plaintext encodés en iso-8859-1, downgrade phpimap 3.1.0 (./components/phpimap/*)<br />
- Connecteur IMAP : Erreur avec la version de PHP 7.2 non supportée (./mail2ticket.php)<br />
- Connecteur IMAP : Erreur de définition de l'adresse mail de l'expéditeur dans le cas d'un envoi de mail à l'administrateur (./mail2ticket.php)<br />
- Connecteur IMAP : Erreur d'affichage d'image issue des signatures lors d'une réponse (./mail2ticket.php)<br />
- Connecteur IMAP : Erreur d'encodage de la date affichée sur certains mois en français (./mail2ticket.php)<br />
- Fiche utilisateur : Défaut d'affichage qu'aucun service n'est associé (./admin/list.php)<br />
- Calendrier : Défaut d'affichage dans certains cas, lorsqu'un qu'aucun événement n'était planifié (./calendar.php)<br />
- Ticket : Dans certains cas le ticket ne passait pas en non lu pour le technicien (./core/ticket.php)<br />
- Ticket : Dans certains cas l'impression ne fonctionnait pas (./ticket_print.php)<br />
- Ticket : Dans certains cas lors de l'utilisation d'un modèle de ticket certains champs n'étaient pas dupliqués (./include/ticket_template.php)<br />
- Ticket : Défaut d'initialisation de variable PHP avec PHP 7.4.8 (./attachement.php)<br />
- Ticket : Lors de l'utilisation des modèles de tickets les pièces jointes n'étaient pas dupliquées (./includes/ticket_template.php)<br />
- Mail automatique : Erreur d'émission du mail automatique au technicien lors de la modification du ticket par le demandeur, avec un groupe de techniciens (./core/aut_mail.php)<br />
- Statistiques : Erreur de traduction de la ligne titre de l'export CSV (./core/export_tickets.php)<br />
- Liste tickets : Pour un profil technicien sur la vue vos tickets, le filtre par demandeur ne fonctionnait pas si le cloisonnement par service était activé pour ce profil (./dashboard.php)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 30/06/2020             <br />
# @Version : 3.2.2	    	 	 <br />
#################################<br />
<br />
<u>Notice :</u><br />
- Version de minimum PHP supportée : 7.3<br />
<u>Update :</u><br />
- Liste des tickets : une nouvelle colonne "Temps passé" est disponible cf droits (./dashboard.php)<br />
- Administration : Sur la fiche utilisateur l'ergonomie des champs service et agence à été améliorée (./dashboard.php)<br />
- Administration : Sur la page système, ajout d'un contrôle de configuration de l'adresse mail de l'émetteur. (./system.php)<br />
- Système : Ajout de la détection de l'obsolescence de version de GestSup (./system.php)<br />
- Logs : Centralisation de tous les logs dans la section Administration > Logs, cf paramètre généraux (./system.php)<br />
- Calendrier : Coloration des Week-End (./calendar.php)<br />
- Connecteur IMAP : Optimisation des performances (./mail2ticket.php)<br />
- Connecteur IMAP : Amélioration de la gestion des erreurs (./mail2ticket.php)<br />
- Thème : optimisation du thème sombre (./index.php)<br />
- Système : La clé privé est par défaut cachée (./index.php)<br />
- Tous : Correction de certains libellés (*)<br />
- Composant : Fontawesome v5.13.1 (./components/fontawesome/*)<br />
- Composant : Moment v2.27.0 (./components/moment/*)<br />
- Composant : PHPmailer v6.1.6 (./components/phpmailer/*)<br />
- Composant : FullCalendar v4.4.2 (./components/fullcalendar/*)<br />
- Composant : Highcharts v8.1.2 (./components/Highcharts/*)<br />
- Composant : PhpImap v4.1.0 (./components/phpimap/*)<br />
<u>Bugfix :</u><br />  
- Administration Liste : Sur les listes des services et des agences, la suppression d'une entrée, entraîne également la suppression des associations avec les utilisateurs (./admin/list.php)<br />
- Administration utilisateurs : Défaut de pagination lorsque un nombre important d'utilisateur est enregistré (./admin/user.php)<br />
- Connecteur LDAP : Erreur de synchronisation sur les versions de PHP antérieur à la 7.3.0 (./core/ldap.php)<br />
- Connecteur IMAP : Dans certains cas le connecteur le paramètre d'activation de la gestion des réponse ne fonctionnait pas correctement (./core/mail2ticket.php)<br />
- Connecteur SMTP : Lors de l'envoi de mail avec des images issues du connecteur IMAP, défaut d'affichage de l'image sur les mails émis. (./core/mail.php)<br />
- Liste des équipements : Erreur d'affichage avec la découverte réseau (./asset_list.php)<br />
- Équipements : Sur la fonction de scan réseau erreur d'initialisation de variable (./core/asset_network_scan.php)<br />
- Équipements : Erreur sur le champ installateur (./asset.php)<br />
- Équipements : Erreur sur le champ numéro de commande (./asset.php)<br />
- Tickets : Conservation des espaces dans les pièces jointes (./core/upload.php)<br />
- Tickets : Erreur de définition de variable dans certains cas (./attachment.php)<br />
- Tickets : Les libellé des catégories et sous-catégorie pouvait être tronqués(./ticket.php)<br />
- Export CSV tickets : Le cloisonnement par agence est pris en compte dans l'export pour les utilisateurs ayant un accès restreint (./core/export_tickets.php)<br />
- Timeout : Bug de connexion lorsque le timeout est supérieur à 150000 minutes (./index.php)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 15/05/2020             <br />
# @Version : 3.2.1	    	 	 <br />
#################################<br />
<br />
<u>Notice :</u><br />
- Version de minimum PHP supportée : 7.2 (https://www.php.net/eol.php)<br />
<u>Update :</u><br />
- Administration : Sur une fiche utilisateur il est possible d'activer ou désactiver l'utilisateur (./admin/user.php)<br />
- Administration : Sur l'administration de la liste des états des nouveaux styles sont disponibles (./admin/list.php)<br />
- Administration : Amélioration de la fonction de récupération de mot de passe administrateur (./admin/pwd_recovery.php)<br />
- Log : Il est possible d'activer des logs d'erreur et de sécurité via Administration > Paramètres > Généraux cf documentation (./admin/log.php)<br />
- Ticket : Toutes les notifications automatiques par mail sont tracées dans la section résolution (./core/auto_mail.php)<br />
- Ticket : Il est possible d'enregistrer le ticket via le raccourcis clavier CTRL+S (./ticket.php)<br />
- Mail : Amélioration de la prise en charge des messages d'erreurs (./core/mail.php, ./core/mail.php)<br />
- Système : Ajout d'un contrôle sur les droits d'écriture sur les sous-répertoires du dossier /upload (./system.php)<br />
- Connecteur IMAP : Le nombre de caractères sur les adresses à exclure à été doublé (SQL)<br />
- Thème : Amélioration du thème sombre (index.php)<br />
- Composant : Ace v2.1.4 (./template/ace/*)<br />
- Composant : Fontawesome v5.13.0 (./components/fontawesome/*)<br />
- Composant : PHPmysqldump v2.9 (./components/PHPmysqldump/*)<br />
- Composant : PhpImap v4.0.0 (./components/phpimap/*)<br />
- Composant : JQuery v3.5.1 (./components/jquery/*)<br />
- Composant : Moment v2.25.3 (./components/moment/*)<br />
- Composant : Highcharts v8.1.0 (./components/Highcharts/*)<br />
- Composant : Bootstrap v4.5.0 (./components/bootstrap/*)<br />
<u>Bugfix :</u><br />  
- Ticket : Ticket, variable non initialisée. (./core/function.php)<br />
- Ticket : Lors de l'utilisation d'un modèle de ticket, les groupes d'utilisateurs ou de techniciens n'étaient pas dupliqués. (./include/ticket_template.php)<br />
- Ticket : Lors de l'utilisation d'un modèle de ticket, les associations automatique de technicien par rapport à la catégorie n'étaient pas prises en compte. (./include/ticket_template.php)<br />
- Ticket : Défaut de blocage de sécurité dans le chargement des pièces jointes, le nom en plus de l'extension pouvait être bloqué. (./core/upload.php)<br />
- Connecteur LDAP : Dans certains cas la création de service pouvait être doublé à la suite d'une désactivation manuelle. (./core/init_post.php)<br />
- Connecteur LDAP : Erreur d'initialisation de variable dans certains cas en mode debug. (./core/ldap.php)<br />
- Connecteur IMAP : Erreur d'initialisation de variable sur un mail avec des adresses en copie. (./mail2ticket.php)<br />
- Connecteur IMAP : Erreur de délimiteurs avec la gestion multilangues. (./mail2ticket.php)<br />
- Liste des tickets : Sur les filtres, technicien et utilisateur la valeur "Aucun" est disponible dans les listes déroulantes. (./dashboard.php)<br />
- Liste des tickets : Dans certains cas erreur d'affichage de la page pour un profil utilisateur avec le cloisonnement par service d'activé. (./index.php)<br />
- Liste des tickets : Dans certains l'affichage du warning concernant la date de résolution estimée non renseignée ne s'affichait pas. (./dashboard.php)<br />
- Liste des tickets : Défaut d'affichage des badges d'états avec Firefox. (SQL)<br />
- Mail : Sur les dates disponibles dans les mails les valeurs 00/00/0000 ne sont plus affichées. (./core/mail.php)<br />
- Mail : Erreur de traduction sur les valeurs "Aucune" et les états. (./core/mail.php)<br />
- Mail : Amélioration de l'affichage de la prévisualisation des mails sur mobile. (./preview_mail.php)<br />
- Mail : Sur l'envoi automatique à l'administrateur lors de la création d'un ticket par un utilisateur, le mail n'était pas émit si l'utilisateur déclare son ticket par mail. (./mail2ticket.php)<br />
- Système : Erreur de récupération de version d'Apache en HTTP/2. (./system.php)<br />
- Système : Défaut de détection du listing des répertoire lors de l'installation. (./system.php)<br />
- Administration Listes : Erreur d'initialisation de variables sur la liste des sociétés dans certains cas. (./admin/list.php)<br />
- Administration Listes : Erreur dans la taille des icônes des boutons d'action. (./admin/list.php)<br />
- Administration paramètres : Contrôle numérique sur l'ajout d'un numéro d'incrémentation. (./admin/parameters.php)<br />
- Moniteur : L'actualisation automatique toutes les 60 secondes de la page moniteur ne fonctionnait plus. (./monitor.php)<br />
- Tous : Erreurs de traductions. (./*)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 18/03/2020             <br />
# @Version : 3.2.0	    	 	 <br />
#################################<br />
<br />
<u>Notice :</u><br />
- Si vous avez personnalisé la couleur d'affichage des états il sera nécessaire de les modifier via l'administration avec les nouveaux styles disponibles<br />
<u>Update :</u><br />
- Interface : Mise à jour de l'ensemble de l'interface graphique (./*)<br />
- Administration : Il est possible d'activer une limite d'heures par société, cf paramètres généraux (./admin/parameters.php)<br />
- Administration : Il est possible de supprimer définitivement un utilisateur désactivé (./admin/user.php)<br />
- Administration : Il est possible de lancer une recherche de droits (./admin/profile.php)<br />
- Administration : Dans la liste des sociétés il est possible de définir un numéro de SIRET et TVA (./admin/list.php)<br />
- Administration : Dans la liste des sociétés, les champs relatifs à limitation des tickets par société ne sont visible que si le paramètre est activé (./admin/list.php)<br />
- Procédure : Il est possible de lancer des recherches dans le contenu des procédures (./procédure.php)<br />
- Connecteur LDAP : Mise à jour de la fonction de contrôle de pagination pour compatibilité avec les prochaines versions de PHP (./core/ldap.php)<br />
- Méta état à traiter : Il est possible de personnaliser les états à prendre en compte via l'administration de la liste des états (./dashboard.php)<br />
- Système : La taille de l'ensemble des fichiers chargés est disponible (./system.php)<br />
- Statistiques : Affichage des tableaux priorité et criticité uniquement si le champ est affiché sur le ticket (./stat/tables.php)<br />
- Mail : Les notifications par mail automatique au technicien lors de l'attribution d'un ticket à un technicien font apparaître le prénom et nom du demandeur (./core/mail_auto.php)<br />
- Composant : PHPMailer 6.1.5 (./components/phpmailer/*)<br />
<u>Bugfix :</u><br />  
- Expiration de session : Désactivation de la popup si le SSO est activé. (./index.php)<br />
- Import équipement : Erreur de définition de variables dans certains cas. (./core/import_asset.php)<br />
- Liste des tickets : Lors de la définition d'un ordre de trie personnel sur un utilisateur, l'ordre était dans certains cas inversé (./dashboard.php)<br />
- Liste des tickets : Le filtre technicien était vide dans la vue "Ma société" (./dashboard.php)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 10/03/2020             <br />
# @Version : 3.1.50     	 	 <br />
#################################<br />
<br />
<u>Update :</u><br />
- Fiche utilisateur : Sur le champ société il est possible de lancer une recherche dans la liste déroulante ( ./admin/user.php)<br />
- Liste des tickets : Pour les colonnes demandeur, technicien et société, il est possible de lancer une recherche dans le filtre ( ./index.php ./dashboard.php)<br />
- Mail : Dans les paramètres du mail il est possible de définir plusieurs adresses mails avec le séparateur point virgule ( ./core/mail.php ./preview_mail.php)<br />
- Mail : Dans les paramètres du mail les listes déroulantes font apparaître l'adresse mail en plus du nom et du prénom, la société est également affichée si rattachée à l'utilisateur ( ./core/mail.php ./preview_mail.php)<br />
- Mail : Il est possible d'ajouter des destinataires en copie cachée, cf paramètres généraux ( ./core/mail.php ./preview_mail.php)<br />
- Connecteur IMAP : Ajout d'informations additionnelles issues du mail dans la section description du ticket( ./mail2ticket.php)<br />
- Composant : Highcharts v8.0.2 (./components/Highcharts/*)<br />
<br />
<u>Bugfix :</u><br />  
- Ticket : Dans certains cas le droit d'obligation de saisie d'un technicien lorsqu'un groupe de technicien est sélectionné, ne fonctionnait pas . (./core/ticket.php)<br />
- Ticket : Sur le champ demandeur, erreur de traduction du libellé si la recherche est infructueuse. (./ticket.php)<br />
- Ticket : Dans certains cas les pièces jointes ne se chargeaient pas. (./core/upload.php)<br />
- Statistiques : Les techniciens désactivés n'apparaissait plus dans les statistiques . (./stat/*.php)<br />
- Mail : Erreur de définition de variable dans certains cas sur les mail automatique. (./core/auto_mail.php)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 25/02/2020             <br />
# @Version : 3.1.49     	 	 <br />
#################################<br />
<br />
<u>Update :</u><br />
- Ticket : Le nombre maximum de pièces jointes par ticket est désormais de 50. (./attachment.php ./core/upload.php ./core/download.php ./core/mail.php ./preview_mail.php ./dashboard.php ./core/ticket.php)<br />
- Déconnexion : Une fenêtre popup indique à l'utilisateur que sa session est expirée. (./index.php)<br />
- Modèle de mail : Nouveaux tags disponibles. cf readme (./template/mail/readme.txt)<br />
- Composant : FullCalendar v4.4.0 (./components/fullcalendar/*)<br />
- Enregistrement utilisateur : le champ téléphone est disponible (./register.php)<br />
<br />
<u>Bugfix :</u><br />  
- Ticket : Lors de l'ajout d'un utilisateur ou d'une catégorie le bouton "Fermer", la fenêtre s'ouvrait de nouveau après un rechargement de page. (./ticket_useradd.php ./ticket_catadd.php)<br />
- Ticket : Sur les informations demandeur, l'infobulle de l'agence pouvait être erronée dans certains cas. (./ticket.php)<br />
- Ticket : Erreur de prise en compte du paramètre d'état par défaut à la création des tickets pour les utilisateurs dans certains cas. (./core/ticket.php)<br />
- Ticket : Erreur de prise en compte de certaines informations dans certains cas avec l'utilisation du bouton "Clôture" . (./core/ticket.php)<br />
- Ticket : Dans certains cas erreur sur la fonction d'attribution automatique du ticket à un technicien en fonction d'une catégorie ne fonctionnait pas correctement, si un paramétrage était appliqué à la catégorie et à la sous-catégorie. (./core/ticket.php)<br />
- Liste tickets : Les drapeaux de changement d'états n'était pas ajoutés, lors de la modification multiple de tickets. (./dashboard.php)<br />
- Liste tickets : La fonction de recherche de tickets dans le menu "Ma société" fonctionne pour les utilisateurs. (./core/searchengine_ticket.php)<br />
- Liste tickets : La colonne date de résolution ne s'affichait pas sur certains états (./dashboard.php)<br />
- Équipement : Dans certains cas la sélection d'un modèle ne fonctionnait pas (./asset.php)<br />
- Équipement : Dans certains cas la recherche d'adresse IP disponible sur une carte wifi ne fonctionnait pas (./core/asset.php)<br />
- Connecteur IMAP : L'envoi de mail automatique à destination du technicien en cas de modification d'un ticket par un utilisateur, ne fonctionnait pas avec lorsqu'une réponse était apportée par mail(./mail2ticket.php ./core/messages.php)<br />
- Connecteur IMAP : Erreur de définition de variables dans certains cas (./core/mail.php)<br />
- Administration des groupes : Dans certains cas la suppression d'un utilisateur entraînait la désactivation de son groupe. (./admin/group.php)<br />
- Administration des listes : Dans certains cas la suppression de société pouvait entraîner une erreur de définition de variable. (./admin/list.php)<br />
- Logs : Modification du moteur de stockage de la table tlogs en InnoDB. (SQL)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 16/12/2019             <br />
# @Version : 3.1.48     	 	 <br />
#################################<br />
<br />
<u>Notice :</u><br />
- Extensions PHP : Les extensions PHP "mbstring" et "gd" sont désormais requises (apt install php7.3-mbstring php7.3-gd)
<br />
<u>Update :</u><br />
- Ticket : Nouveau droit de désactivation du cloisonnement par service pour le champ priorité cf ticket_priority_service_limit(./ticket.php)<br />
- Ticket : Nouveau droit de désactivation du cloisonnement par service pour le champ criticité cf ticket_criticality_service_limit (./ticket.php)<br />
- Ticket : Nouveau champ disponible permettant de spécifier le type de réponse cf code droit "ticket_type_answer_disp" (./ticket.php ./core/ticket.php ./core/export_ticket.php)<br />
- Statistiques : Nouveau camembert de répartition des tickets par type, si le paramètre est activé (./ticket_stat.php ./stats/pie_tickets_type.php)<br />
- Enregistrement des utilisateurs : Si le paramètre est activé, une validation par mail + captcha est requise (./register.php)<br />
- Mail : Nouveaux tags pour les modèles de mails (./template/mail/readme.txt ./core/mail.php)<br />
- Traductions : Améliorations des traductions (./locale/* )<br />
- SQL : Optimisation de la structure de la base de données (./* )<br />
- Composant : PHPMailer v6.1.4 (./components/PHPMailer/*)<br />
- Composant : PhpImap v3.0.33 (./components/phpimap/*)<br />
- Composant : Highcharts v8.0.0 (./components/Highcharts/*)<br />
- PHP : Support PHP 7.4.0 (./*)<br />
<br />
<u>Bugfix :</u><br />  
- Utilisateur : Avec MySQL 8.x erreurs SQL lors de l'ajout ou modification d'un utilisateur, MariaDB le seul SGBDR supporté. (./admin/user.php)<br />
- Ticket : Avec MySQL 8.x erreur lors de génération d'un nouveau numéro de ticket (./core/ticket.php)<br />
- Ticket : Erreur lors de l'utilisation du bouton de rappel sur les nouveaux tickets, disponible désormais uniquement sur l'édition comme les autres boutons (./ticket.php)<br />
- Ticket : Lors de l'enregistrement dans certains cas le mot "case" pouvait apparaître (./core/auto_mail.php)<br />
- Ticket : Lors d'un envoi de mail automatique l'adresse mail de destination n'apparaissait pas en info-bulle sur le drapeau d'envoi de mail (./core/mail.php)<br />
- Ticket : Dans certains cas le droit d'obligation de saisie du champ "service" ne fonctionnait pas (./ticket.php)<br />
- Liste tickets : Perte de l'encodage dans certains cas sur la première lettre du prénom du demandeur (./core/dashboard.php)<br />
- Liste tickets : Perte de la vue par groupe de technicien sur la pagination (./core/dashboard.php)<br />
- Impression ticket : Suppression de de l'heure dans la date de résolution estimée (./core/dashboard.php)<br />
- Recherche tickets : Dans certains cas la recherche ne retournait aucun résultats (./core/dashboard.php)<br />
- Liste équipements: Lors de l'utilisation du trie par utilisateur la flèche indiquant le sens n'apparaissait pas (./core/asset_list.php)<br />
- Liste équipements: Lors de l'utilisation du trie par utilisateur défaut de trie lorsque les utilisateurs n'ont que le prénom de renseignés (./core/asset_list.php)<br />
- Mail : suppression du lien vers le ticket si l'URL du serveur n'est pas configuré dans les paramétrés généraux (./core/mail.php)<br />
- Système : Erreur sur le texte d'information sur le paramètre "php_expose" (./system.php)<br />
- Connecteur LDAP : La mise à jour des téléphones ne fonctionnait pas si une valeur GestSup était renseignée. (./core/ldap.php)<br />
- Connecteur IMAP : suppression des attributs de la balise body des mails HTML. (./mail2ticket.php)<br />
- Sondage : Lors d'une modification de l'ordre des questions, les modifications n'était pas prises en compte sur l'interface de réponse. (./survey.php)<br />
- Calendrier : Lors de la modification d'une intervention issue d'un ticket, si le titre du ticket possédait des guillemets, le texte était tronqué. (./calendar.php)<br />
- Administration : Dans certains cas une erreur d'affichage des listes des service depuis l'administration de la liste des catégorie pouvait apparaître. (./admin/list.php)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 25/11/2019             <br />
# @Version : 3.1.47     	 	 <br />
#################################<br />
<br />
<u>Update :</u><br />
- Ticket : Attribution automatique d'un technicien ou d'un groupe de technicien en fonction d'une catégorie ou d'une sous-catégorie cf Administration > Paramètre > Général > Tickets cf documentation (./core/ticket.php ./admin/list.php ./core/auto_mail.php)<br />
- Ticket : Lors de l'utilisation du cloisonnement par service, si le champ service n'est pas affiché sur le ticket, alors le champ catégorie filtre les catégories en fonction des services associés à l'utilisateur connecté (./ticket.php)<br />
- Fonction mot de passe oublié : Suppression du formulaire lors d'une disparité de jeton (./core/forgot_pwd.php)<br />
- SQL : Optimisation de la structure de la base de données (./* )<br />
- Composant : PHPMailer v6.1.3 (./components/PHPMailer/*)<br />
- Composant : PhpImap v3.0.32 (./components/phpimap/*)<br />
- Composant : Chosen v1.8.7 (./components/chosen/*)<br />
<br />
<u>Bugfix :</u><br />  
- Ticket : La recherche dans le champ demandeur ne fonctionnait pas si le nom ou le prénom possédait un espace (./components/chosen/*)<br />
- Ticket : Lorsque la date de création était manuellement définie sans valeur, la valeur enregistrée était le 30/11/0001 (./core/ticket.php)<br />
- Ticket : Lorsque le droit ticket_cat_mandatory est activé l'accès à prévisualisation du mail était possible (./core/ticket.php)<br />
- Rappel : Dans certains cas les accréditations ne fonctionnaient plus (./event.php)<br />
- Calendrier : Erreur de définition de variable lors de la modification de la durée d'un événement (./core/calendar.php)<br />
- Mail : Mise à jour de la couleur de fond sur le thème 2, pour mieux faire apparaître le délimiteur (./template/theme2.htm)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 08/11/2019             <br />
# @Version : 3.1.46     	 	 <br />
#################################<br />
<br />
<u>Update :</u><br />
- Authentification : Les utilisateurs peuvent réinitialiser leurs mots de passe cf paramètres utilisateur (./forgot_pwd.php)<br />
- Modèle de mail : Deux nouveaux thèmes sont disponibles, cf paramètres des mails (./template/mail/* )<br />
- Paramètre : Il est possible de restreindre l'accès à l'application à une adresse IP ou une plage d'adresse cf paramètres généraux (./index.php)<br />
- SQL : Optimisation des requêtes SQL (./* )<br />
- SQL : Optimisation de la structure de la base de données (./* )<br />
- Composant : MySqlDump v2.8 (./components/mysqldump-php/*)<br />
- Composant : PhpImap v3.0.31 (./components/phpimap/*)<br />
- Composant : Highcharts v7.2.1 (./components/Highcharts/*)<br />
<br />
<u>Bugfix :</u><br />  
- Liste tickets : lors de l'utilisation de la sélection multiple avec l'état résolu, les mails automatiques n'étaient pas émit (./dashboard.php )<br />
- Administration : Erreur dans certain cas lors de la désactivation des utilisateurs (./admin/user.php )<br />
- Équipement : Erreur de définition DOM pour certains boutons (./asset.php )<br />
- Statistiques : Correction affichage des tableaux avec certaines données (./stat/tables.php )<br />
- Ticket : Erreur d'affichage des secondes sur certain datetimepicker lors de la sélection uniquement (./ticket.php )<br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 22/10/2019             <br />
# @Version : 3.1.45     	 	 <br />
#################################<br />
<br />
<u>Update :</u><br />
- Mail : Possibilité de personnaliser les mails de notifications, cf paramètre modèle de mail (./core/mail.php ./admin/parameters.php ./template/mail/*)<br />
- Mise à jour : Ajout d'un contrôle sur la présence l'extension ftp (./admin/update.php)<br />
- Mise à jour : Amélioration de l'affichage de l'installation en ligne de commande (./admin/update.php)<br />
- Ticket : Nouveaux droits obligation de saisie des champs "type" et "catégorie" cf ticket_type_mandatory, ticket_cat_mandatory (./ticket.php ./core/ticket.php) <br />
- Ticket : Les utilisateurs et utilisateurs avec pouvoir dispose du bouton de chargement de fichier joint sur l'ouverture d'un ticket (./attachement.php) <br />
- Statistiques tickets : Nouveau filtre par états des tickets disponible <br />
- Statistiques tickets : Nouveau tableau "Top jour de la semaine" (./stat/tables.php) <br />
- Administration : Ajout d'une confirmation lors de la suppression d'un groupe (./admin/group.php) <br />
- Procédure : Ajout d'une confirmation lors de la suppression d'une procédure (./procedure.php) <br />
<br />
<u>Bugfix :</u><br />  
- Ticket : Erreur de définition DOM pour certains boutons (./ticket.php ./threads.php)<br />
- Connecteur LDAP : dans certaines configuration des doublons dans la création des utilisateurs pouvait être observé (./core/ldap.php)<br />
- Statistiques tickets : Dans certains cas une erreur lors de l'export était observé avec le cloisonnement par service (./admin/export_ticket.php) <br />
- Mail : Dans certains cas erreur d'initialisation de variable (./core/mail.php) <br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 02/10/2019             <br />
# @Version : 3.1.44     	 	 <br />
#################################<br />
<br />
<u>Update :</u><br />
- Composant : PHPMailer v6.1.1 (./components/PHPMailer/*)<br />
- Composant : Highcharts v7.2.0 (./components/Highcharts/*)<br />
- Composant : FullCalendar v4.3.1 (./components/fullcalendar/*)<br />
- Composant : PhpImap v3.0.30 (./components/phpimap/*)<br />
- Ticket : Amélioration de l'affichage du champ description lorsque le droit d'édition du champ est désactivé (./ticket.php)<br />
- Ticket : Lors de la planification d'une intervention, suppression de la scrollbar (./event.php)<br />
- Ticket : Suppression de l'affichage du champ "Fichier joint" si le ticket est dans l'état "Résolu" sans pièce jointe. (./ticket.php)<br />
- Connecteur IMAP : Augmentation du champ d'exclusion des adresses mails . (./ticket.php)<br />
- Connecteur LDAP : Sur les serveurs LDAP AD il est possible de paramétrer le champ login à utiliser lors de la synchronisation UserPrincipalName ou SamAcountName. (./core/ldap.php)<br />
- Connecteur LDAP : Sur les serveurs LDAP AD sur lesquels le paramètre Champ login est positionné sur "UserPrincipalName" gestion de multiple UPN. (./core/ldap.php ./login.php)<br />
<br />
<u>Bugfix :</u><br />  
- Ticket : Erreur de définition de variable (./index.php)<br />
- Ticket : Dans certains cas le droit d'ouverture de ticket ne fonctionnait pas (./index.php)<br />
- Ticket : Dans certains cas le droit d'ouverture de ticket via le menu Tous les tickets du service ne fonctionnait pas correctement (./index.php)<br />
- Ticket : Dans certains cas après la suppression d'un ticket puis d'un enregistrement d'un nouveau ticket le mail automatique n'était pas envoyé (./core/ticket.php)<br />
- Statistiques : Erreur de définition de variable dans l'export CSV (./core/export_ticket.php)<br />
- Liste des tickets : Le menu "Ma société" ne s'affiche plus quand aucune société n'est déclarer dans la liste des société.(./menu.php ./dashboard.php)<br />
- Connecteur IMAP : Gestion du dernier point virgule sur les exclusions .(./mail2ticket.php)<br />
- Connecteur SMTP : Des erreurs d'envoi pouvait être observés dans certaines configuration de mails automatique. .(./mail2ticket.php)<br />
- Menu : Lors de la suppression d'un groupe de technicien, ce dernier apparaissaient toujours dans le menu .(./menu.php)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 06/08/2019             <br />
# @Version : 3.1.43     	 	 <br />
#################################<br />
<br />
<u>Update :</u><br />
- Ticket : Affiche des informations de tickets restants dans le cadre de la limitation de ticket par société, affiché sur les informations du demandeur (ticket.php)<br />
- Fiche utilisateur : Amélioration de la détection d'adresse mail erronée. (./admin/user.php)<br />
<br />
<u>Bugfix :</u><br />  
- Équipements : Lors de la désactivation de la gestion IP des équipements le champ interface et prise sont supprimés de la fiche équipement. (./asset.php)<br />
- Mail : Lors de l'utilisation de la notification automatique d'attribution à un technicien, cette dernière était émise même si l'utilisateur connecté était le technicien. (./core/auto_mail.php)<br />
- Mail : Ajout d'un contrôle sur la présence de pièces jointes avant l'ajout sur les mails. (./mail.php)<br />
- Connecteur IMAP : Dans certains cas deux répertoires de pièce jointe du même nom prouvait être crée (./mail2ticket.php)<br />
- Ticket : La taille et les espaces des boutons de la barre d'outils n'étaient pas exactement identique (./ticket.php)<br />
- Ticket : Dans certains cas il était possible sur la création d'un nouveau ticket de créer un doublon. (./ticket.php ./core/ticket.php)<br />
- Ticket : Erreur de définition de variable dans certains cas sur l'impression d'un ticket. (./ticket_print.php)<br />
- Liste tickets : Erreur de définition de variable sur les filtres dates erronées. (./dashboard.php)<br />
- Liste des tickets : Dans certaines configurations la gestion de plusieurs agences par utilisateur pouvait provoqué un conflit avec le cloisonnement par service. (./index.php)<br />
- Administration : Les droits admin_list_* ne fonctionnait pas dans toutes les configurations. (./admin/list.php)<br />
- Administration : Lors de l'édition de la fiche d'un utilisateur par un administrateur, le bouton "Retour" redirigeait sur la liste des tickets. (./admin/user.php)<br />
- Système : Erreur de détection des versions obsolètes de PHP. (./system.php)<br />
- Composant : Downgrade PhpImap v3.0.6, erreur avec les dernières versions #4574 (./components/phpimap/*)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 08/07/2019             <br />
# @Version : 3.1.42     	 	 <br />
#################################<br />
<br />
<u>Update :</u><br />
- Composant : Highcharts v7.1.2 (./components/Highcharts/*)<br />
- Composant : FullCalendar v4.2.0 (./components/Fullcalendar/*)<br />
- Ticket : Les notifications automatiques par mail sont tracées dans le fil de résolution (./preview_mail.php, ./core/mail.php, ./thread.php ./core/auto_mail.php)<br />
- Ticket : Uniformisation des icônes dans la barre d'outils (./ticket.php)<br />
- Connecteur LDAP : Amélioration de la prise en charge du protocole LDAPS sur les serveurs LDAP Windows. (./core/ldap.php, ./core/ldap_services.php ./core/ldap_agencies.php)<br />
- Système : Ajout de contrôles de sécurité pour les connecteurs. (./system.php)<br />
- Administration : Dans la liste des lieux, augmentation du nombre de caractères maximale d'un lieu. (SQL)<br />
- Équipement : La fonction globalping met à jour l'information sur les interfaces d'un équipement. (./core/ping.php)<br />
- Utilisateurs : Possibilité de définir une politique de mot de passe via l'administration des paramètres cf documentation. (./admin/user.php ./modify_pwd.php)<br />
<br />
<u>Bugfix :</u><br />
- Mail automatique : Dans certains cas une erreur de définition de variable était observée (./auto_mail.php)<br />   
- Mail automatique : Dans certains cas l'émission du mail au demandeur lors de la création d'un ticket par le demandeur ne fonctionnait pas si l'utilisateur avait un profil superviseur (./auto_mail.php)<br />   
- Mail : Défaut lors de l'utilisation de la gestion des lieux, la marge était manquante (./core/mail.php)<br />   
- Statistiques : Erreur d'affichage lors de l'utilisation du cloisonnement par service avec un profil technicien qui n'a pas le droit d'accès à l'administration dans certains cas (./ticket_stat.php)<br />   
- Ticket : Dans certains cas erreur de droit d'accès lorsqu'un technicien, ouvre un ticket de son groupe (./index.php)<br />   
- Ticket : Dans certains cas erreur d'affichage du bouton privée sur l'ajout d'un élément de résolution lorsque le droit est activé (./index.php)<br />   
- Ticket : Vérification de la présence de modèle de ticket, avant l'affichage du bouton (./ticket.php)<br />   
- Ticket : Lors d'un changement de technicien et de la planification du ticket sans sauvegarde préalable la sélection du technicien était perdu (./ticket.php ./core/ticket.php ./event.php ./modalbox.php)<br />   
- Ticket : Lors de la désactivation d'un utilisateur, perte de l'information d'ouverture du ticket dans la section résolution  (./threads.php)<br />   
- Ticket : Lorsque le ticket passe de l'état résolu à un autre état, la date de résolution était conservée. (./core/ticket.php)<br />   
- Ticket : Sur un ticket sans équipements de sélectionné, le warning rouge n'apparaît uniquement si le champ est obligatoire. (./ticket.php)<br />   
- Liste ticket : Erreur SQL dans certains cas avec la gestion des agences (./dashboard.php)<br />   
- Calendrier : Erreur lors du déplacement d'un événement planifié sur un moment de la journée, vers "toute la journée"  (./calendar.php ./core/calendar.php)<br />   
- Connexion : Dans certains cas erreur d'affichage de la liste des tickets, lorsque l'utilisateur dispose d'une seule agence et d'un seul service avec cloisonnement par service et gestion des agences non cloisonnées. (./index.php)<br />   
- Timeout : Dans certains cas le délais d'inactivité ne fonctionnait pas. (./index.php)<br />   
- Fermeture automatique : Pas de prise en compte lors l'utilisation du SSO. (./index.php ./login.php)<br />   
- Connecteur LDAP : Erreur dans la récupération du login sur les annuaires OpenLDAP. (./ldap.php)<br />   
- Connecteur LDAP : Dans certains cas lors de l'activation de la fonction SSO un message d'erreur pouvait apparaître. (./index.php)<br />   
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 13/05/2019             <br />
# @Version : 3.1.41     	 	 <br />
#################################<br />
<br />
<u>Update :</u><br />
- Composant : MySqlDump v2.7 (./components/mysqldump-php/*)<br />
- Composant : FullCalendar v4.1.0 (./components/Fullcalendar/*)<br />
- Composant : Highcharts v7.1.1 (./components/Highcharts/*)<br />
- Composant : Jquery v3.4.1 (./components/Jquery/*)<br />
- Mail automatique : à destination du technicien lors de l'ajout d'un élément de résolution même si ce n'est pas le demandeur du ticket (./auto_mail.php)<br />
- Calendrier : Les rappels intègre le numéro de ticket dans le titre (./event.php)<br />
- Ticket : Optimisations SQL (./ticket.php)<br />
- Ticket : Fonction de clôture automatique des tickets, cf doc paramètres généraux des tickets (./login.php ./core/cron.php ./core/auto_mail.php ./threads.php)<br />
<br />
<u>Bugfix :</u><br />
- Menu : Sur la page du calendrier, il n'était plus possible de réduire le menu latéral (./index.php)<br />  
- Ticket : Dans certaines configurations une erreur lors de l'ouverture d'un nouveau ticket par un technicien pouvait être observé (./ticket.php)<br />  
- Ticket : Dans certains cas une erreur SQL pouvait être observée avec le paramètre de lien VNC (./ticket.php)<br />  
- Ticket : Dans certains cas une erreur SQL pouvait être avec un cloisonnement par service sans service associé à l'utilisateur (./ticket.php)<br />  
- Ticket : Dans certains cas un changement de technicien n'était pas pris en compte dans l'usage concomitant avec un utilisateur (./core/ticket.php)<br />  
- Ticket : Erreur de droit d'accès avec un technicien qui consulte un ticket associé à un groupe de technicien dont il fait partie sans avoir le droit de visualiser tous les tickets (./index.php)<br />  
- Ticket : Sur le champ technicien, défaut lors de l'utilisation des droits ticket_tech_admin et ticket_tech_super (./ticket.php)<br />  
- Ticket : Le champ type est par défaut sur aucun (./ticket.php)<br />  
- Équipement : Erreur de définition de fonction (./asset.php)<br />  
- Connecteur LDAP : Suppression de caractères spéciaux lors de la synchronisation pour éviter les problèmes de blocages (./core/ldap.php)<br />  
- Connecteur IMAP : Lorsque le connecteur est désactivé, l'appel du connecteur en ligne de commande est désactivé (./mail2ticket.php)<br />  
- Connecteur IMAP : Erreur de définition d'une variable(./mail.php)<br />  
- Mail automatique : Dans certains cas une erreur de définition de variable était observée (./auto_mail.php)<br /> 
- Composant : PhpImap v3.0.6 erreur avec certains serveurs de messagerie avec les versions plus récentes.(./components/phpimap/*)<br /> 
- Système : Variable non initialisée (./system.php)<br />  
- Paramètre : Doublement du nombre de caractère autorisés, dans le nom du fichier du logo de l'entreprise (SQL)<br />  
- Enregistrement utilisateur : Mise en forme du formulaire d'enregistrement n'avais pas une mise en forme identique au formulaire de connexion (./register.php)<br />  
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 02/04/2019             <br />
# @Version : 3.1.40     	 	 <br />
#################################<br />
<br />
<u>Update :</u><br />
- SQL : Optimisation des requêtes (/*)<br />
- Ticket : Prise en charge du copier coller d'image avec chrome (./ticket.php)<br />
- Ticket : Certains champs obligatoire sont gérés avec les attributs HTML5 (./ticket.php)<br />
- Ticket : Dans la section résolution les drapeaux concernant les envois de mails, intègrent l'information des adresses mails des destinataires en infobulle (./preview_mail.php)<br />
- Ticket : Augmentation de la limite à 100 000 tickets (SQL)<br />
- Système : Support Nginx (./system.php)<br />
- Authentification : Nouveau paramètre permettant de désactiver un utilisateur après X tentatives d'authentification infructueuses (./login.php)<br />
- Authentification : Augmentation du niveau de sécurité du hash des mots de passes (./login.php)<br />
- Fiche utilisateur : Une confirmation de mot de passe est nécessaire pour la création d'un utilisateur ou la modification d'un mot de passe. (./login.php)<br />
- Composant : FullCalendar v4.0.1 (./components/Fullcalendar/*)<br />
- Composant : Highcharts v7.1.0 (./components/Highcharts/*)<br />
<br />
<u>Bugfix :</u><br />
- Ticket : Le warning de dépassement de date de résolution estimée ne s'affichait plus (./ticket.php)<br /> 
- Ticket : L'application d'un modèle de ticket ne fonctionnait pas dans certains cas. (./ticket.php)<br /> 
- Ticket : Erreur de récupération du service pour le cloisonnement dans certains cas. (./ticket.php)<br /> 
- Ticket : Le libellé "Résolution" n'était pas aligné. (./ticket.php)<br /> 
- Liste des tickets : blocage de l'accès à la liste des tickets "Ma société" pour les techniciens lorsque le droit "side_all" était désactivé (./index.php)<br /> 
- Liste des tickets : Erreur d'impression les liens étaient imprimés (./components/bootstrap)<br /> 
- Mail : Erreur de détection des mails automatique de transfert techniciens dans certains cas (./core/auto_mail.php)<br /> 
- Système : Erreur chargement de logos dans certains cas (./system.php)<br /> 
- Système : Icône MySQL manquant (./system.php)<br /> 
- API : Erreur requête SQL (./gestsup_api.php)<br /> 
- Connecteur LDAP : défaut d'authentification avec les serveurs OpenLDAP (./login.php)<br /> 
- Connecteur IMAP : Dans certains cas les images étaient importées en tant que pièces jointes, même si elles étaient dans le corp du mail (./mail2ticket.php)<br /> 
- Administration : Affichage des styles dans la liste des états des équipements (./admin/list.php)<br /> 
- Équipement : Erreur lors de l'ajout d'une adresse IP avec le module de recherche d'adresse IP disponible dans le réseau (./core/asset.php)<br /> 
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 04/03/2019             <br />
# @Version : 3.1.39     	 	 <br />
#################################<br />
<br />
<u>Notice:</u><br />
- API : Mise à jour de l'API (./gestsup_api.php)<br />
- Système : Mise à jour des prérequis serveur PHP 7.X  (./system.php)<br />
<br />
<u>Update :</u><br />
- Thème : Nouveau thème sombre (bêta) (./template/asset/css/ace-skins-min.css)<br />
- Composant : Highcharts 7.0.3 (./composants/Highcharts)<br />
- Composant : Bootstrap 3.4.1 (./composants/Bootstrap)<br />
- Paramètre : Ajout de timezones: Guyana,Martinique,Miquelon,St_Barthelemy,Mayotte,Tahiti (./admin/parameters.php)<br />
- Erreur Apache : intégration des pages d'erreurs Apache par défaut dans le .htaccess (.htaccess)<br />
<br />
<u>Bugfix :</u><br />
- Projet : Mise à jour du favicon (./index.php)<br /> 
- Connecteur IMAP : Erreur de détection de certaines pièces jointes avec les mails émit depuis les appareils Apple (./mail2ticket.php)<br /> 
- Connecteur IMAP : Prise en compte de l'horloge du serveur GestSup lors de la création des tickets si un fuseau horaire est paramétré (./mail2ticket.php)<br /> 
- Ticket : Sur le champ technicien, trie d'abord sur l'utilisateur "Aucun" puis Alphabétique (./ticket.php)<br /> 
- Ticket : Lors de l'utilisation de l'ajout d'utilisateur ou de catégorie depuis le ticket dans certains cas la validation ne fonctionnait pas (./attachment.php)<br /> 
- Ticket : Le cloisonnement par service pour les groupes de techniciens ne fonctionnait pas (./ticket.php)<br /> 
- Liste des tickets : Sur la vue activité la sélection de date inférieur à 3 jours ne fonctionnait pas (./dashboard.php)<br /> 
- Liste des tickets : La couleur du numéro de ticket est verte lorsque le ticket est résolu par un groupe de technicien (./dashboard.php)<br /> 
- Liste des tickets : La confirmation de suppression de tickets ne pouvait pas être annulé (./dashboard.php)<br /> 
- Moniteur : Gestion de plusieurs criticité "critique" dans les compteurs (./monitor.php)<br /> 
- Équipement : Perte du champ installateur lors de l'enregistrement dans certains cas (./asset.php)<br /> 
- Administration : Erreur de droit d'accès avec les accès partiel à l'administration des listes et groupes pour les techniciens. (./admin/group.php)<br /> 
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 04/02/2019             <br />
# @Version : 3.1.38     	 	 <br />
#################################<br />
<br />
<u>Update :</u><br />
- Ticket : Récupération des informations demandeur en Ajax, suppression du rechargement de la page lors de la sélection d'un demandeur (./ticket.php ./includes/*)<br />
- Ticket : Ajout d'un contrôle sur la taille des pièces jointes (./attachement.php)<br />
- Calendrier : Gestion des événements sur une ou plusieurs journées (./calendar.php)<br />
- Fonction : Nouvelle fonction projet, permet d'avoir une vue de plusieurs tickets, cf documentation. (./project.php)<br />
- Composant : PHPMailer 6.0.7(./composants/PHPMailer)<br />
- Composant : FullCalendar 3.10.0 (./composants/FullCalendar)<br />
- Composant : Highcharts 7.0.2 (./composants/Highcharts)<br />
- Composant : DateTimePicker 4.17.47 (./composants/datetimepicker)<br />
- Composant : Moment 2.24.0 (./composants/moment)<br />
<br />
<br />
<u>Bugfix :</u><br />
- Équipements : Les champs dates ne pouvaient pas être modifiés avec des valeurs passées dans certains cas (./asset.php)<br /> 
- Équipements : Erreur sur l'import d'un fichier contenant des adresses MAC avec tiret ou deux points (./core/import_asset.php)<br /> 
- Monitor : Le compteur de nouveaux tickets ne tenait pas compte de l'attribution à un technicien (./monitor.php)<br /> 
- Monitor : Le compteur de nouveaux tickets restai gris alors qu'il doit être vert ou rouge (./monitor.php)<br /> 
- Mail : Les mails automatiques aux groupes de technicien n'étaient pas pris en compte (./core/auto_mail.php)<br /> 
- Mail : Lors de l'ouverture d'un ticket avec une erreur de champ obligatoire les mails automatiques sont désactivés (./core/auto_mail.php)<br /> 
- Liste des tickets : Pas d'affichage du menu "Tous les tickets" avec le cloisonnement par service et le droit dashboard_service_only désactive (./index.php)<br /> 
- Ticket : Sur les champs dates dans certains cas, l'heure ne pouvait être modifiée (./ticket.php)<br /> 
- Ticket : Erreur lors de droit d'accès lors de l'impression deux fois de suite sur un ticket (./ticket.php)<br /> 
- Ticket : Erreur d'initialisation de variable dans certains cas (./ticket_print.php)<br /> 
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 07/01/2019             <br />
# @Version : 3.1.37     	 	 <br />
#################################<br />
<br />

<u>Update :</u><br />
- Paramètres : Ajout du fuseau horaire America/Guadeloupe (./admin/parameters.php)<br />
- Composant : PHPMailer en version 6.0.6 (./components/PHPMailer/*)<br />
- Composant : Highcharts 7.0.1 (./components/Highcharts/*)<br />
- Composant : PHPmysqldump 2.6 (./components/PHPmysqldump/*)<br />
- Composant : Bootstrap 3.3.7(./components/bootstrap/*)<br />
- Composant : Jquery 3.3.1 (./components/jquery/*)<br />
- Composant : jQuery-ui 1.12.1 (./components/jquery-ui/*)<br />
- Composant : TimePicker 0.5.2 (./components/timepicker/*)<br />
- Composant : DatepPicker 1.8.0 (./components/datepicker/*)<br />
- Mail : Timeout forcé à 30s en cas d'erreur de connexion au serveur de messagerie (./core/mail.php)<br />
- Mail automatique : Avec le paramètre "Au technicien lors de l'attribution d'un ticket à un technicien" le mail est émit également lors de d'un changement de technicien (./core/auto_mail.php)<br />
- Calendrier : Les rappels reste affichés dans le calendrier après accréditation (./core/calendar.php)<br />
- Connecteur SMTP : Améliorations de la prise en charge des erreurs de communication avec les serveurs de messageries. (./core/mail.php)<br /> 
- Liste des tickets : Fenêtre de confirmation lors de la suppression des tickets sélectionnés  (./dashboard.php)<br /> 
<br />
<br />
<u>Bugfix :</u><br />
- Connecteur LDAP : Erreur de synchronisation avec les UO possédant des accents (./core/ldap.php)<br /> 
- Connecteur LDAP : Erreur de synchronisation avec des utilisateurs à désactiver possédant des accents dans leurs logins (./core/ldap.php)<br /> 
- Connecteur SMTP : Dans certains cas l'émission de plusieurs mails pouvaient simultanée être bloqué. (./core/messages.php)<br /> 
- Ticket : Lors de l'insertion d'image dans le champs description certaines images pouvaient être tronqués (./ticket.php)<br /> 
- Ticket : Perte du groupe de technicien lors de l'ajout d'un élément de résolution par utilisateur (./ticket.php)<br /> 
- Ticket : Sur les date l'outil de sélection prend en compte la langue de l'utilisateur (./ticket.php)<br /> 
- Procédure : Erreur de requête SQL (./procedure.php)<br /> 
- Paramètre : Le nombre ligne maximum ne peut plus être définit à 0. (./admin/parameters.php)<br /> 
- Droit : Erreur de détection du droit "side_all" sur certains profils. (./index.php)<br /> 
- Calendrier : Conservation de l'affichage des rappel après accréditation. (./calendar.php)<br /> 
- Ticket : Erreur sur l'ajout d'un rappel, lorsque le ticket possède un accent dans son titre. (./event.php)<br /> 

<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 07/11/2018             <br />
# @Version : 3.1.36     	 	 <br />
#################################<br />
<br />
<u>Update :</u><br />
- Liste des tickets et équipement: Augmentation de la performance d'affichage des listes, sur les grosses bases de données (./dashboard.php)<br />
- Composant : Highcharts 6.2.0 (./components/Highcharts/*)<br />
- Connecteur LDAP : Affichage de la date lors de synchronisation (./core/ldap.php)<br />
- Système : Ajout de la taille de la base de données (./system.php)<br />
- Droits : Nouveaux droits permettant la désactivation de l'insertion d'image sur les champs description et résolution d'un ticket ticket_description_insert_image, ticket_resolution_insert_image (./wysiwyg.php)<br />
- Login : Suite à une erreur de saisie de l'identifiant ou du mot de passe, la fenêtre de login ne s'affiche plus (./login.php)<br />
- Fiche utilisateur : Ajout de contrôle sur la formation du domaine de messagerie (./admin/user.php)<br />
<br />
<br />
<u>Bugfix :</u><br />
- Ticket : La taille des éditeurs est limitée en largeur (./ticket.php)<br />
- Ticket : Erreur lors de la saisie manuel de date sans heures. (./ticket.php)<br />
- Ticket : En utilisant les champs de saisie obligatoire, lors de la modification de l'état en résolu avec une obligation de saisie un drapeau de clôture était inséré dans le fil de résolution (./core/ticket.php)<br />
- Ticket impression: Erreur syntaxe HTML (./ticket_print.php)<br />
- Administration : Lors de la création d'utilisateur avec une agence et un service un défaut pouvait apparaître dans l'enregistrement de l'agence (./admin/user.php)<br />
- Liste des tickets : Sur le filtre de date de création certaines valeurs pouvaient provoquer une erreur (./dashboard.php)<br />
- Liste des tickets : la recherche avec apostrophe ne fonctionnait pas (./dashboard.php)<br />
- Procédure : Ajout de la fonction d'ajout de pièce jointe lors de la création d'une nouvelle procédure (./procedure.php)<br />
- Mail : Sur la notification automatique d'attribution d'un ticket à un technicien, défaut lors de l'utilisation d'un groupe de technicien (./core/auto_mail.php ./core/message.php)<br />
- Mail : Erreur syntaxe HTML (./core/mail.php)<br />
- Connecteur IMAP : La notification automatique à l'utilisateur lors de l'ouverture d'un ticket par un utilisateur ne fonctionnait pas lors de l'import de mail en ligne de commande. (./core/mail.php ./mail2ticket.php)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 24/09/2018             <br />
# @Version : 3.1.35     	 	 <br />
#################################<br />
<br />
<u>Notice:</u><br />
- Système: L'extension PHP curl est désormais requise<br />
<br />
<u>Update :</u><br />
- Connecteur IMAP : La date de création du ticket correspond à la date du mail (./mail2ticket.php ./components/PHPImap/mailbox.php)<br />
- Composant : Highcharts 6.1.4 (./components/Highcharts/*)<br />
- Administration : des nouveaux styles sont disponible dans la liste des états des tickets (./admin/list.php)<br />
- Système : Ajout d'un contrôle sur le listing des répertoires. (./system.php)<br />
<br />
<br />
<u>Bugfix :</u><br />
- URL : mise en conformité avec la RFC1738 (menu.php)<br />
- Paramètres : Une erreur SQL pouvait apparaître lors de la validation des paramètres de la fonction disponibilité (plugin/availability/admin/parameters.php)<br />
- Liste des tickets : Dans certains cas le filtre utilisateur était vide (SQL)<br />
- Fil d'Ariane : Dans certains une erreur de redirection était observée (./index.php)<br />
- Ticket : Les libellé d'indentation des éditeurs de texte était inversé (./wysiwyg.php)<br />
- Ticket : Lors de la saisie d'un titre possédant des guillemets, si un changement de catégorie était réalisée le texte était tronqué (./ticket.php)<br />
- Ticket : Lors d'une copie de lien hypertext possédant une balise href dans la résolution, erreur d'affichage du lien. (./thread.php)<br />
- Ticket : Des erreurs pouvait apparaître avec l'utilisation du droit ticket_date_hope_disp (./ticket.php)<br />
- Ticket : Désactivation de la mémorisation des champs date qui masquaient les datepicker (./ticket.php)<br />
- Ticket : Lors de la création de ticket avec la planification sans enregistrement des problèmes apparaissaient (./ticket.php)<br />
- Liste des tickets : Certains tickets caractère dans le titre des tickets entraînait des défaut d'affichage dans la liste (./dashboard.php)<br />
- Équipement : Désactivation de la mémorisation des champs date qui masquaient les datepicker (./asset.php)<br />
- Statistiques : Erreur de prise en compte du filtre agence sur l'export CSV sur certaines configurations. (./core/export_ticket.php)<br />
- Calendrier : Erreur de double chargement des styles fullcalendar. (./index.php)<br />
- Administration : Ajout d'un contrôle sur la vérification de la formation des adresses mails sur la fiche utilisateur. (./admin/user.php)<br />
- Administration : Ajout d'un contrôle sur l'existence du login lors de l'ajout ou la modification d'un utilisateur. (./admin/user.php)<br />
- Fiche utilisateur : La section "Membre du groupe" de l'onglet paramètre conservait les groupes supprimés. (./admin/user.php)<br />
- Mail : La notification automatique d'attribution au technicien ne fonctionnait pas lorsque toutes les notifications automatiques étaient activées. (./core/auto_mail.php)<br />
- Mail : Contrôle de la présence d'une adresse mail de destination avant d'envoyer le mail. (./core/mail.php)<br />
- Sauvegardes : Sur certaines configurations une erreur lors du dump SQL se produisait (./admin/backup.php)<br />
- Connecteur LDAP : Gestion des comptes AD avec les valeurs UserAccountControl à 544, 546, 66080, 66082 (./core/ldap.php)<br />
- Connecteur LDAP : Lors de l'utilisation de l'authentification, un message d'erreur apparaissait dans les logs apache en cas d'erreur d'identifiant ou de mot de passe (./login.php)<br />
- Connecteur IMAP : Erreur sur l'import de certains mails avec certaines pièces jointe (./mail2ticket.php)<br />
- Session : Erreur de déconnexion automatique lorsque l'actualisation automatique était activée sur la liste des tickets (./index.php)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 17/08/2018             <br />
# @Version : 3.1.34     	 	 <br />
#################################<br />
<br />
<u>Update :</u><br />
- SSO : Compatibilité avec IIS Server (./index.php)<br />
- LDAP : Suppression de limite de synchronisation à 1000 utilisateurs pour les serveurs LDAP AD (./core/ldap.php)<br />
- Composant : Highcharts 6.1.1 (./components/Highcharts/*)<br />
- Composant : PHPmysqldump 2.5 (./components/PHPmysqldump/*)<br />
- Composant : FullCalendar 3.0.9 (./components/Fullcalendar/*)<br />
<br />
<br />
<u>Bugfix :</u><br />
- Mail : Envoi de mail automatique de clôture fonctionne en utilisant le bouton "clôturer ticket" pour un profil technicien ou administrateur(./core/auto_mail.php)<br />
- Mail : Envoi de mail automatique de sondage fonctionne en utilisant le bouton "clôturer ticket" pour un profil technicien ou administrateur(./core/auto_mail.php)<br />
- Mail : L'affichage de l'ordre ante-chronologique ne fonctionnait pas dans certains cas (./core/mail.php)<br />
- Ticket : les saut de lignes n'était pas enregistrés dans certains cas.(./core/ticket.php)<br />
- Ticket : Sur le champ type la sélection de l'entrée "aucun" n'était pas conservée lors de la sélection d'une catégorie.(./ticket.php)<br />
- Ticket : Le passage multiple en privée d'un élément de résolution n'était pas pris en compte .(./thread.php)<br />
- Traduction : Certains libellé n'était pas traduit.(./calendar.php)<br />
- Équipement : lors de l'utilisation de la fonction d'import la création du fabricant ne fonctionnait pas dans certains cas.(./core/import_assets.php)<br />
- Équipement : lors de l'utilisation de la fonction d'import la comparaison avec les données du logiciel ne tiennent plus compte de la casse.(./core/import_assets.php)<br />
- Statistiques : Message d'erreur dans certains cas lors de l'export de ticket, si un ticket était associé à un groupe de technicien (./core/export_ticket.php)<br />
- Administration : Sur les paramètres du connecteur IMAP le dossier n'était pas sauvegardé (./admin/parameters.php)<br />
- Calendrier : Le calendrier gère le multi-langues (./calendar.php)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 11/06/2018             <br />
# @Version : 3.1.33     	 	 <br />
#################################<br />
<br />
<u>Update :</u><br />
- Statistiques : Mise à jour du composant Highcharts en version 6.1.0 (./components/Highcharts/* ./stat.php)<br />
- Ticket : mise en forme de l'impression identique aux mails (./ticket_print.php)<br />
- Ticket : Sur le champ titre, limite de la saisie à 100 caractères. (./ticket.php)<br />
<br />
<br />
<u>Bugfix :</u><br />
- Mail : le mail automatique lors de l'assignation d'un ticket à un technicien est émit aussi lorsque le bouton sauvegarder et quitter est utilisé (./core/auto_mail.php)<br />
- Mail : le mail automatique lors de l'assignation d'un ticket à un technicien est émit depuis l'adresse mail de l'utilisateur connecté si non renseigné dans les paramètres (./core/auto_mail.php)<br />
- Mail : Erreur SQL lors de l'utilisation d'agences (./core/mail.php)<br />
- Menu : Sur la page des statistique il n'était pas possible de réduire le menu de gauche (./index.php)<br />
- Connecteur LDAP : Erreur SQL sur la synchronisation de groupe de service AD (./core/ldap_services.php)<br />
- Équipement : la recherche multi-critère ne fonctionne pas dans certains cas. (./core/searchengine_asset.php)<br />
- Équipement : Export lors de l'export . (./core/searchengine_asset.php)<br />
- Administration : Dans les listes l'id interne 0 n'apparaît plus . (./admin/list.php)<br />
- Administration : Dans les paramètres généraux, le logo est redimensionné . (./admin/parameters.php)<br />
- Droit : Erreur de droit sur le droit side_all_agency_disp . (./index.php)<br />
- Ticket : Dans certains cas avec les navigateurs Microsoft, une résolution vide était ajouté . (./core/ticket.php)<br />
- Ticket : Erreur dans le nom du technicien sur un transfert . (./core/ticket.php)<br />
- Moniteur : Erreur d'accès avec certaines clés privés . (./monitor.php)<br />
- Traduction : Erreur de traduction du téléphone sur la fiche utilisateur . (./locale/.php)<br />
- Traduction : Sur PHP7 et un serveur Windows des pages non traduites pouvait apparaître de manière aléatoire. (./localization.php)<br />
- Système : Amélioration du contrôle du post_max_size et upload_max_filesize . (./system.php)<br />
- Fiche utilisateur : Sur le champ service lors de l'ajout d'un nouveau service la liste contenait des services désactivés . (./admin/user.php)<br />
- Procédure : Dans certains cas le texte n'était pas sauvegarder . (./wysiwyg.php)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 08/05/2018             <br />
# @Version : 3.1.32     	 	 <br />
#################################<br />

<br />
<u>Update :</u><br />
- Administration: Dans les paramètres généraux les listes: "État par défaut à la connexion" et "État par défaut lors de la création de tickets" tient compte des nouveaux états crée dans la liste des états des tickets (./admin/parameters.php) <br />
- Calendrier: Un nouveau bouton est "Ouvrir le ticket est disponible" (./admin/parameters.php) <br />
- Calendrier: Lors de la planification d'un ticket depuis le ticket, le titre de l'événement intègre le numéro du ticket (./event.php) <br />
- Composant: Mise à jour de PHPMailer en version 6.0.5 (./components/PHPmailer/*)<br />
- Ticket : Sur l'ajout ou modification d'un utilisateur la liste déroulante était trop large avec des noms sociétés longs. (./ticket_adduser.php)<br />
- Ticket : Sur un ticket avec une intervention planifié, lien disponible qui ouvre un nouvel onglet vers le calendrier. (./ticket.php)<br />
- Ticket : Une confirmation est demandée avant la suppression d'un ticket. (./ticket.php)<br />
- Ticket : Lors de la suppression d'un ticket, si des événements dans le calendrier ils sont aussi supprimés. (./core/ticket.php)<br />
- Ticket : Mise à jour de l'icône des pièces jointes. (./attachement.php)<br />
- Mail : Envoi automatique de mail au technicien lors de l'attribution d'une ticket au technicien. (./admin/auto_mail.php.php)<br />
<br />
<br />
<u>Bugfix :</u><br />
- Menu: Erreur menu déroulant "Tous les tickets" depuis la page Calendrier (./index.php) <br />
- Ticket : Erreur lors de la planification d'un ticket si ce dernier à un apostrophe dans le titre (./event.php) <br />
- Ticket : Erreur du chargement de certaines pièces jointes possédant certains caractères spéciaux (./core/upload.php) <br />
- Ticket : Erreur dans le fil de résolution lors de l'ouverture d'un ticket par le connecteur IMAP dans le nom de l'éxécutant (./thread.php)
- Connecteur IMAP: Erreur d'affichage des tickets sur les mails possédant la balise "base" (./mail2ticket.php) <br />
- Connecteur IMAP: Erreur d'envoi de mail automatique à l'utilisateur lors de l'utilisation en ligne de commande (./mail2ticket.php ./core/mail.php) <br />
- Fonction Sondage: Le ticket passait en non lu pour le technicien lors de la clôture du sondage" (./survey.php) <br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 20/03/2018             <br />
# @Version : 3.1.31     	 	 <br />
#################################<br />

<br />
<u>Update :</u><br />
- Composant: Mise à jour de PHPimap en version 3.0.6 (/components/PHPimap/*)<br />
- Mail : Sur les mails manuels il est possible de spécifier une adresse saisie manuellement dans les paramètres du message (./core/mail.php ./preview_mail.php)<br />
- Mail : Ajout d'un contrôle sur la présence du mail de l'administrateur dans les envois de mail automatique à l'administrateur (./core/auto_mail.php)<br />
- Calendrier: Refonte complète de la fonction (./calendar.php ./index.php ./menu.php ./core/calendar.php ./template/assets/js/bootbox.min.js ./template/assets/js/jquery-ui.custom.min.js ./template/assets/js/jquery.min.js  ./template/assets/css/fullcalendar.css ./template/assets/js/uncompressed/fullcalendar.js)<br />
- Export Ticket: Ajout de la colonne lieu (./core/export_ticket.php)<br />
- Erreurs Apache: Mise à disposition des pages d'erreur apache répertoire /error/* cf documentation apache<br />
- Fiche utilisateur: Gestion du téléphone mobile en plus du téléphone fixe (./admin/user.php ./ticket_adduser.php ./core/ldap.php)<br />
- Mobile: Optimisation mobile (./thread.php ./ticket.php ./admin/user.php)<br />
- Sauvegarde: intégration du composant mysqldump-php, pour la sauvegarde de la base de données (./admin/backup.php ./components/mysqldump-php/*)<br />
- Administration des listes: La liste des sous-catégories est trié par catégorie puis sous-catégorie (./admin/list.php )<br />
<br />
<br />
<u>Bugfix :</u><br />
- Traduction: Le contenu de la liste déroulante de la sous-catégorie sur un ticket n'était pas traduite (./ticket.php) <br />
- Mail : Pas d'envoi de message automatique si le demandeur est un groupe (./core/auto_update)<br />
- Mail : Sur certaines pièce jointes avec certains caractères spéciaux l'extension était mal formé (./core/upload.php)<br />
- Ticket : l'ordre des types n'était pas pris en compte (./ticket.php)<br />
- Ticket : Alignement du cadre de la description par rapport à la résolution (./ticket.php)<br />
- Administration: La liste des utilisateur était trié par nom uniquement ajout du trie par nom et prénom (./admin/user.php)<br />
- Statistiques : Certains graphique ne s'affichait pas si un simple guillemet était présent dans le nom du technicien (./stat/histo_load.php + pie_tickets_tech.php)<br />
- Import équipement: insertion de multiple états (./core/import_asset.php)<br />
- Fonction sondage: prise en compte du timezone définit dans les paramètres (./survey.php)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 19/02/2018             <br />
# @Version : 3.1.30     	 	 <br />
#################################<br />
<br />
<u>Notice:</u><br />
- Monitor: Page déplacée, utiliser le nouveau lien présent dans les paramètres.<br />
<br />
<u>Update :</u><br />
-  Paramètre: un nouveau paramètre permet de définir le timeout de la session (./index.php ./admin/parameters.php)<br />
-  Paramètre: un nouveau paramètre permet de forcer le fuseau horaire, sur une valeur différente de celle de php.ini(./index.php ./admin/parameters.php)<br />
-  Système: Mise à jour des informations de sécurité (./system.php)<br />
-  Barre utilisateur: Mise à jour des avatars des profils (./images/avatar/*)<br />
-  Composant: Mise à jour du composant WOL version 2.1 (./components/wol/*)<br />
-  Sauvegarde manuelle: Ajout d'un contrôle sur la présence du dump SQL (./admin/backup.php)<br />
-  Mail automatique: Nouveau paramètre envoi a l'utilisateur lors de l'ouverture d'un ticket par l'utilisateur (./core/auto_mail.php ./core/ticket.php ./mail2ticket.php)<br />
<br />
<br />
<u>Bugfix :</u><br />
-  Fiche utilisateur: Erreur sur la liste des vues (./admin/user.php) <br />
-  Mail: Certains mails automatique n'était pas envoyé à l'adresse de copie si le demandeur n'avait pas d'adresse mail (./core/auto_mail.php) <br />
-  Connecteur IMAP: Lors de la reception d'un mail non HTML, l'adresse mail de l'émetteur n'était pas affiché dans la description du ticket (./mail2ticket.php) <br />
-  Liste des tickets: la priorité aucune n'avait pas la couleur grise (SQL) <br />
-  Ticket: lors de la sélection du demandeur, la liste des priorité faisait apparaître deux fois la valeur sélectionnée dans la liste (./ticket.php) <br />

<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 20/01/2018             <br />
# @Version : 3.1.29     	 	 <br />
#################################<br />
<br />

<u>Update :</u><br />
- Composant PHPMailer: Nouvelle version 6.0.3 (./components/PhpMailer)<br />
- Passage en requêtes préparées: (./admin/* ./register.php ./login.php)<br />
- Connecteur LDAP: Compatibilité avec Samba4 (./core/ldap.php)<br />
- Connecteur LDAP: Amélioration de la prise en charge des connexion chiffrés (./core/ldap.php)<br />
- Connecteur LDAP: SSO disponible cf FAQ (./index.php)<br />
- Système: Contrôle de version de PHP pour respecter les prérequis serveur (./system.php)<br />
- Paramètre: Augmentation de la taille maximal des adresses mail en copie à 150 char. (SQL)<br />
- Tickets: Insertion de lien possible dans la description, click droit pour ouvrir. (./wysiwyg.php)<br />

<br />
<br />
<u>Bugfix :</u><br />
- Paramètres Généraux: La liste des états par défaut ne tenait pas compte du renommage dans la liste des états (./admin/parameters.php) <br />
- Connecteur IMAP: erreur lors de la réception des mails (./mail2ticket.php) <br />
- Connecteur LDAP: Erreur de modification d'une fiche utilisateur suite synchro AD(./login.php) <br />
- Mail : Message d'erreur si le champ début de mail était à vide et l'utilisateur avec la langue anglaise (./core/mail.php) <br />
- Mail : Erreur lors de l'utilisation du bouton mail par l'utilisateur (./core/mail.php) <br />
- Mail : Erreur lors de l'envoi de mail avec le profile utilisateur avec pouvoir sur les mails d'ouverture automatique  (./core/auto_mail.php) <br />
- Variable non initialisée: La variable user_agent n'était pas initialisée, si non transmise par le navigateur (./index.php) <br />
- Ticket : Le champ résolution pouvaient ne pas être pris en compte sur les nouveaux tickets (./core/ticket.php) <br />
- Enregistrement utilisateurs: Certains message d'erreur n'était pas traduit si une langue différente du français était détectée (./register.php) <br />
- Liste des réseaux des équipements: Sur certaines colonnes la traduction n'était pas correct (./admin/list.php) <br />
- Équipements : Gestion de la date de derniers ping par interface (./core/asset_network_scan.php) <br />
- Fonction sondage: erreur SQL sur l'utilisation du lien présent dans le mail (./survey.php) <br />
- Statistiques : Le filtre par technicien n'était pas appliqué sur certains tableaux (./stat/tables.php) <br />
- Installation: Erreur de détection de l'HTTPS (./install/index.php) <br />
- Liste des équipements : La liste des équipements pouvait être mélangé si aucune ip n'étaient renseignés (./asset_list.php) <br />

<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 04/12/2017             <br />
# @Version : 3.1.28     	 	 <br />
#################################<br />
<br />
<u>Update :</u><br />
- Composant PHPImap: Nouvelle version 3.0.5 (./components/PHPImap/*)<br />
- Équipement : Le bouton ping sur l'équipement IP, lance désormais un ping sur toutes les interfaces et affiche le drapeau à coté (./core/ping.php, ./asset.php)<br />
- Ticket : L'ordre d'affichage de la liste des catégories peut être définie manuellement dans l'administration des listes (./admin/list.php, ./ticket.php)<br />
- Fiche utilisateur: La saisie de mot de passe vide n'est plus autorisé (./admin/user.php)<br />

<br />
<br />
<u>Bugfix :</u><br />
- Menu Société: l'ouverture des tickets du menu société s'affichait avec une page blanche (./index.php) <br />
- Variable non initialisée: Si User-Agent header est filtré par un firewall (./index.php) <br />
- Variable non initialisée: La variable imap_user n'était pas initialisée(./admin/parameters.php) <br />
- Ticket : Dans certains cas des erreurs s'affichaient dans la liste des demandeurs (./ticket.php) <br />
- Ticket : AccessKey du bouton "Enregistrer et fermer" est désormais F (./ticket.php) <br />

<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 17/10/2017             <br />
# @Version : 3.1.27     	 	 <br />
#################################<br />
<br />
<u>Update :</u><br />
- Composant PHPMailer: Nouvelle version 6.0.1 (./components/PHPMailer/*)<br />
- Ticket : Pour les utilisateurs du champ "service du demandeur" ce dernier est désormais associé au service du demandeur par défaut, si il n'en dispose que d'un (./ticket.php)<br />
- Profile utilisateur: Si la gestion des agences est activé alors il est possible de visualiser et modifier les associations d'agences sur la fiche utilisateur (./ticket.php)<br />
- Système: Ajout d'une section sécurité. (./system.php)<br />
<br />
<br />
<u>Bugfix :</u><br />
- Procédure: Les fichiers de plus de 10MO n'étaient pas transférés (./procedure.php) <br />
- Mails : Erreur lors de l'envoi de mail automatique alors que l'utilisateur ne dispose pas de mail ou que le demandeur "Aucun" a été sélectionné (./core/auto_mail.php) <br />
- Ticket : Suppression de l'affichage du pourcentage d'avancement du ticket dans le titre si le temps passé et temps estimé ne sont pas affichés (./core/ticket.php) <br />
- Ticket : L'ordre de trie des criticité tien uniquement compte de l'ordre définit dans la gestion des listes. (./ticket.php) <br />
- Liste des tickets: L'icône horloge rouge indiquant un retard, ne s'affiche plus si le droit sur la date de résolution estimée du ticket est désactivé. (./dashboard.php) <br />
- Barre utilisateur: Suppression de l'affichage du bloc "Charge" si l'affichage du temps passé et estimé ne sont pas affichés (./index.php) <br />
- Ticket non lu: erreur lors de l'utilisation du bouton clôture de ticket sur un profil technicien ou administrateur (./core/ticket.php)<br />
- Système: Erreur de contrôle de la quantité de mémoire quand la valeur était en gigabytes. (./system.php)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 14/09/2017             <br />
# @Version : 3.1.26     	 	 <br />
#################################<br />

<br />
<u>Update :</u><br />
- Ticket : Deux icônes sont présent dans la barre des titre si un ticket est planifié ou si une alerte est positionnée (./ticket.php)<br />
- Ticket : Possibilité d'activer ou de désactiver le cloisonnement par service du champ type via un nouveau droit ticket_type_service_limit  (./ticket.php)<br />
- Ticket : Lors de la suppression d'une entrée dans la liste des temps, la valeur était perdu sur le ticket lors d'un nouvel enregistrement (./ticket.php)<br />
- Équipements : Il est possible de cloisonner les équipements par société, jonction entre un équipement et une société via l'utilisateur associé cf droit asset_list_company_only et doc. (./menu.php ./asset_list.php)<br />
- Procédure: Il est possible de cloisonner les procédure par société cf droit procedure_list_company_only et procedure_company. (./procedure.php)<br />
- Procédure: Le bouton de création d'une nouvelle procédure est désormais dans le menu de gauche, afin de s'uniformiser avec les création de ticket ou d'équipement. (./menu.php ./procedure)<br />
- Statistique Équipements: Il est possible de filtrer les graphiques par société a l'aide d'un nouveau filtre. (./procedure.php)<br />
- Export CSV Équipements: Le fichier CSV exporté contenant les équipement dispose d'une nouvelle colonne société. (./core/export_asset.php)<br />

<br /><br />
<u>Bugfix :</u><br />
- Ticket : Les tickets supprimés était encore accessible via le lien sur un mail (./index.php) <br />
- Calendrier: Les tickets supprimés était encore visible (./planning.php) <br />

<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 04/09/2017             <br />
# @Version : 3.1.25     	 	 <br />
#################################<br />

<br />
<u>Update :</u><br />
- Composant PHPImap: Nouvelle version 2.0.9 (./components/PHPimap/*)<br />
- Composant PHPMailer: Nouvelle version majeure 6.0.0 (./components/PHPMailer/*)<br />
- Mobile: Optimisation de l'affichage des tickets et des matériels sur mobile<br />
- Ticket et Fiche équipement: Il est possible d'utiliser les raccourcis clavier ALT+SHIFT+ X consulter les infos bulles des boutons pour connaître les raccourcis (./ticket.php asset.php)<br />
- Ticket : Il est possible de cloisonner le type et la priorité par service (./ticket.php)<br />
- Liste des tickets: Sur les colonnes techniciens et utilisateur affichage du prénom et nom complet au survol de la souris (./dashboard.php)<br />

<br /><br />
<u>Bugfix :</u><br />
- Ticket : Nouveau droit permettant d'activer ou désactiver le verrouillage du champ technicien lorsque la limite par ticket est activée et qu'il déclare un ticket pour un autre service (./ticket.php) <br />
- Ticket : Lors de l'utilisation d'un modèle de ticket le lieu n'était pas dupliqué (./ticket_template.php) <br />
- Ticket : Lors de la modification d'un ticket, lors de la modification du service le premier changement n'était pas pris en compte (./ticket.php) <br />
- Liste des tickets: Dans la vue activité erreur lors de l'utilisation du filtre technicien deux fois de suite (./index.php ./dashboard.php) <br />
- Liste des tickets: Dans la vue activité lors de l'utilisation du bouton nouveau ticket si l'on envoi un mail la redirection n'était pas faites sur la vue activité (./menu.php ./core/ticket.php ./core/mail.php) <br />
- Mobile: Les champs d'auto complétion ne fonctionnait pas sur mobile (./asset.php ./ticket.php) <br />
- Statistique: La courbe des tickets résolu tenait compte uniquement de la date de résolution et pas de l'état(./stat/line_tickets.php ) <br />
- Système: Variable non initialisée si apache est en mode ServerTokens=Prod (./stat/system.php ) <br />
- Mails : Défaut d'affichage des mail dans outlook lors de l'impression liée à la taille du cadre (./core/mail.php ) <br />

<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 04/08/2017             <br />
# @Version : 3.1.24     	 	 <br />
#################################<br />

<br />
<u>Update :</u><br />
- Composant PHPMailer: Nouvelle version disponible https://github.com/PHPMailer/PHPMailer/releases/tag/v5.2.24 (./components/PHPMailer/*)<br />
- Liste des tickets: Il est possible de personnaliser pour l'ensemble des utilisateurs de l'application l'état par défaut à la connexion de l'application (nouveau paramètre). (./parameters.php ./login.php)<br />
- Tickets: Dans les éléments de résolution si un texte type lien est détecté, il est alors convertit en lien hypertexte. (./threads.php)<br />
- Équipements : les champs demandeur et localisation ont été optimisés avec de l'auto-complétion (./index.php ./asset.php)<br />
- Équipements : Amélioration de la vue garantie (./index.php ./asset.php)<br />
- Gestion des agences: Un utilisateur peut faire partie d'un service et d'une agence (./index.php)<br />
<br /><br />
<u>Bugfix :</u><br />
- Liste des tickets: Un défaut d'affichage pouvait apparaître lors l'affichage de la colonne société (./dashboard.php) <br />
- Liste des tickets: Sur la vue activité certains anciens tickets pouvaient s'afficher en non lus (./dashboard.php) <br />
- Ticket : le bouton impression ne lançait plus la fenêtre système d'impression (./ticket_print.php) <br />
- Composant: le fichier de version de php-gettext pouvais ne pas être présent (components\php-gettext\VERSION) <br />
- Impressions: Les impressions dans le navigateur n'affiche plus les URL de tous les liens (./template/bootstrap) <br />
- Mail automatique: Avec certaines combinaison de paramétrage dans les mail automatique, l'option d'envoi de mail au technicien lors de la modification d'un ticket par un utilisateur ne fonctionnait pas (./core/auto_mail.php) <br />
- Statistiques : Sur le premier graphique d'évolution des tickets, les compteurs sous le titre étaient erronés et la courbe des tickets avancés ne donnait pas le nombre de tickets distinct (./stat_line.php ./stat/line_ticket.php)<br  />
- Statistiques : Sur le premier graphique la courbe des tickets avancés tenait compte des tickets fermés également (./core/ticket_line.php )<br  />
- Statistiques : Sur le filtre par technicien les administrateurs n'était pas présent dans la liste si paramétré  (./ticket_stat.php)<br  />
- Administration: Dans la gestion des listes les nom des colonnes sont désormais traduite (./admin/list.php)<br />
- Connecteur LDAP: la mise a jour d'adresse mail avec des apostrophes posait problème (./core/ldap.php)<br />

<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 18/07/2017             <br />
# @Version : 3.1.23     	 	 <br />
#################################<br />

<br />
<u>Update :</u><br />
- Liste des tickets: Nouveaux compteurs disponible dans la vue activité, avec les tickets ouverts, fermés et avancé dans la période (./dashboard.php)<br />
- Liste des tickets: Nouvelles couleurs sur les numéros de tickets voir le détail en passant le curseur sur le numéro, un nouvel indicateur de nouveau ticket est présent: une pastille rouge, les couleurs sont valables sur la vue activité prenant en compte la période sélectionnée et sur les listes des tickets (./dashboard.php)<br />
- Liste des tickets: Nouvelle colonne disponible "service du demandeur" activable par un nouveau droit "dashboard_col_user_service" (./dashboard.php)<br />
- Ticket : Sur le champ agence avec un profil technicien lors de l'enregistrement d'un ticket, la liste des agences reste limité à celles associées au demandeur (./ticket.php)<br />
- Statistiques : Changement des couleurs des courbes d'évolution des tickets, rouge ouverts, vert fermés, bleu avancés (./stats/line_ticket.php)<br />
- Statistiques : Ajout de deux nouvelles colonnes dans le tableau des répartition des temps par statuts (./stats/tables.php)<br />
- Gestion des agences: Un technicien peut faire partie d'une agence et d'un service (./admin/parameters.php ./core/mail.php)<br />
<br /><br />
<u>Bugfix :</u><br />
- Ticket : l'ordre de trie du champ criticité après validation était inversé (./ticket.php)<br />
- Ticket : Le champ équipement restait affiché si la fonction équipement était active et que le champ était désactivé dans les droits (./ticket.php)<br />
- Liste des tickets: le filtre par lieu avoir des dysfonctionnements dans certains cas (./dashboard.php)<br />

<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 05/07/2017             <br />
# @Version : 3.1.22     	 	 <br />
#################################<br />

<br />
<u>Update :</u><br />
- Connecteur IMAP: Il est possible de supprimer l'indicateur "--- vous pouvez répondre..." dans les messages émit via une nouvelle option dans Administration > Paramètres > Connecteur IMAP "Gérer les réponses dans les mails" (./admin/parameters.php ./core/mail.php)<br />
- Connecteur IMAP: Possibilité d'activer ou désactiver la vérification des certificats SSL du serveur de messagerie (./admin/parameters.php)<br />
- Connecteur LDAP: Il est possible de ne pas désactiver les utilisateurs crée manuellement dans GestSup lors d'une synchronisation LDAP cf paramètre du connecteur (./admin/parameters.php ./core/ldap.php)<br />
- Fonction sondage: L'adresse mail d'émission du mail de sondage est celle paramétré dans "Adresse de l'émetteur", si ce paramètre n'est pas renseigné alors c'est l'adresse mail du technicien qui est utilisé (./core/auto_mail.php)<br />
- Ticket : Il est possible d'associer un équipement à un ticket en fonction de l'utilisateur cf documentation et nouveaux droits (ticket_new_asset_disp,ticket_asset_disp,ticket_asset,ticket_asset_mandatory,dashboard_col_asset) (./ticket.php ./index.php ./core/ticket.php ./dashboard.php) <br />
- Ticket : Un nouveau droit permet d'afficher les techniciens sans les administrateurs dans la liste des techniciens cf "ticket_tech_admin" (./ticket.php) <br />
- Ticket : Un nouveau droit permet d'afficher les superviseurs dans la liste des techniciens cf "ticket_tech_super" (./ticket.php) <br />
- Ticket : Un nouveau paramètre permet de définir l'état par défaut des tickets (./ticket.php) <br />
- Menu: lors de l'utilisation du menu de gauche réduit avec le thème bleu affichage de l'icône de creation d'un nouveau ticket (./menu.php) <br />
<br /><br />
<u>Bugfix :</u><br />
- Lien nouvel onglet: Dans certains les liens sur nouveaux onglets ne fonctionnaient pas (./*) <br />
- Lien mail: Des anomalies pouvait être rencontré lors de l'utilisation du lien envoyé dans les mails aux utilisateurs (./login.php) <br />
- Fonction sondage: La date de résolution n'était pas automatiquement inséré lors de la clôture du ticket automatique par l'utilisateur (./survey.php) <br />
- Fonction sondage: Pas de mail de sondage à l'utilisateur si l'envoi de mail automatique à la création du ticket était paramétré et que le changement d'état avait lieu à l'ouverture du tickets (./survey.php) <br />
- Fonction sondage: L'utilisateur pouvait valider le sondage sans répondre à la dernière question (./survey.php) <br />
- Fonction sondage: Le mail à destination de l'utilisateur est émit avec l'adresse paramétré dans "Adresse de l'émetteur" dans les paramètres, si le champ est vide alors le mail sera émit avec l'adresse du technicien en charge du ticket (./core/auto_mail.php) <br />
- Fonction sondage: Le mail émit à destination de l'utilisateur gère désormais les serveurs de messagerie avec des certificats non vérifier (./core/messages.php) <br />
- Fonction disponibilité: La condition de prise en compte d'un ticket, ne fonctionnait pas avec le type (./ticket.php) <br />
- Fonction équipement: le WOL sur linux ne fonctionnait plus (./core/wol.php) <br />
- Fonction équipement: Dans l'administration de la liste des modèle lors de la création d'un nouvelle équipement la valeur IP n'était pas conservé. (./admin/list.php) <br />
- Fonction équipement: Augmentation du nombre de caractère disponible sur le champs numéro de prise de 10 à 50. (SQL) <br />
- Ticket : Erreur de changement automatique d'état lors de lors d'un transfert d'un technicien à un groupe de technicien (./survey.php) <br />
- Ticket : Lors de l'utilisation du paramètre "les utilisateurs ne voient que les tickets de leurs service" un technicien ne pouvait pas visualiser un ticket qu'il avait ouvert pour un autre service (./index.php ./dashboard.php ./menu.php) <br />
- Ticket : La liste des demandeurs n'affiche plus les utilisateurs n'ayant ni prénom ni nom.(./index.php ./dashboard.php ./menu.php) <br />
- Ticket : La liste priorité n'était pas trié par numéro.(./ticket.php) <br />
- Ticket : Erreur sur la priorité par défaut lors de la suppression des valeurs par défaut dans la liste des priorité.(./ticket.php) <br />
- Liste des tickets: Lorsque le demandeur ne possédait pas de prénom il n'était pas visible dans le filtre des demandeurs.(./dashboard.php) <br />
- Connecteur IMAP: Suppression de la gestion du protocole POP ne gérant pas les mails non lus (./admin/parameters.php) <br />
- Connecteur IMAP: Affichage des messages d'erreurs si le mode debug est activé (./mail2ticket.php) <br />
- Connecteur IMAP: erreur d'association avec l'utilisateur lorsque deux utilisateurs avait le même mail et l'un était désactivé (./mail2ticket.php) <br />
- Connecteur LDAP: Sur la synchronisation d'agences et de service le caractère Œ n'était pas gérée (./core/ldap_services.php, ./core/ldap_agencies.php) <br />
- Connecteur LDAP: Sur la synchronisation du champ société, gestion de la casse lors des mises à jours  (./ldap.php) <br />
- Connecteur LDAP: La liste des agences pour le déplacement n'était pas trié par ordre alphabétique (./admin/parameters.php) <br />
- Connecteur LDAP: Les unité d'organisations avec accents et espace sont géré (./core/ldap.php) <br />
- Statistique: Dans la répartition du temps par status, le temps total de traitement ne tenait pas compte du filtre par technicien (./stats/tables.php) <br />
- Statistique: Dans le tableau du top 10 demandeur la période sélectionnée n'était pas prise en compte (./stats/tables.php) <br />
- Enregistrement utilisateur: sur le formulaire d'enregistrement autonome des utilisateurs, les champs saisis étaient perdus en cas d'erreur (./register.php) <br />
- Utilisateur: Augmentation du nombre maximal de caractère pour le champ fonction, passage à 100 (SQL) <br />
- Service: Augmentation du nombre maximal de caractères pour le champ nom, passage à 100 (SQL) <br />
- Profil utilisateur: La liste des service ne tenait pas compte de la désactivation des services. (./admin/user.php) <br />

<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 23/05/2017             <br />
# @Version : 3.1.21      	 	 <br />
#################################<br />

<br />
<u>Update :</u><br />
- Équipements : Les équipements IP qui n'ont pas de modèle IP mais possèdent une adresse IP disposent des boutons d'action IP et du drapeau de dernier ping (./asset.php)<br />
- Équipements : Module de scan réseau permettant de créer et mettre à jour des équipements IP, module en ligne de commande uniquement cf doc (./core/asset_network_scan.php)<br />
- Équipements : Nouveau paramètre dans la fonction équipement pour activer la prise de contrôle web VNC vers un équipement distant cf doc (./asset.php ./ticket.php ./admin/parameters.php)<br />
- Équipements : La fonction d'import des équipements gère les mises à jour dans GestSup si l'adresse MAC est renseigné, l'import peut être automatisé en ligne de commande cf doc (./core/import_asset.php)<br /> 
- Liste des équipements : Amélioration du tri par adresses ip avec INET_ATON (./asset_list.php)<br />
- Ticket : Les champs: technicien, titre et description peuvent remplit de manière obligatoire cf droits: ticket_tech_mandatory, ticket_title_mandatory, ticket_description_mandatory (./ticket.php ./core/ticket.php)<br /> 
- Ticket : Focus par défaut sur le champ demandeur (./ticket.php)<br />
- Mails : Possibilité de paramétrer le texte de fin de mail (Administration > Paramètres généraux), des balises sont disponibles pour le prénom, nom et téléphone du technicien (./core/mail.php ./admin/parameters.php)<br />
- Moniteur : Ajout de nouveaux compteurs (./monitor.php)<br />
- Statistiques : Fusion des graphiques sur les tickets ouverts et fermés et les résolutions (./monitor.php)<br />
<br /><br />
<u>Bugfix :</u><br />
- Ticket : Dans certains cas le champ service était affiché alors que le droit était désactivé (./ticket.php) <br />
- Ticket : Erreur de redirection avec les boutons annuler et clôturer ticket avec un profil utilisateur et le paramètre "Les utilisateurs peuvent voir tous les tickets de leur société" (./index.php) <br />
- Mail : le lien vers le ticket n'était pas inséré dans le mail lorsqu'un technicien n'avait pas de téléphone (./core/mail.php) <br />
- Statistiques : Dans certains cas une variable pouvait ne pas être initialisée sur le tableau d'évolutions des résolutions (./stat/line_tickets_activity.php)<br />
- Statistiques : Le filtre par agence ne fonctionnait pas pour certains profil (./stat.php)<br />
- Statistiques : Dans la répartition des temps par status certains états n'avait pas de valeurs (./stat.php)<br />
- Statistiques : Avec la gestion des agences et la restriction par service l'export des tickets en admin était vide (./core/export.php)<br />
- Équipement : Désactivation des interfaces associées à un équipement supprimé (./core/asset.php)<br />
- Ajout utilisateur : Lors de l'ajout d'un utilisateur ayant le profil technicien ou admin la selection d'une vue personnel effaçait les valeurs du formulaire saisie (./admin/user.php)<br />

<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 03/05/2017             <br />
# @Version : 3.1.20        	 	 <br />
#################################<br />

<br />
<u>Update :</u><br />
- Répertoire logs : nouveau répertoire logs disponible pour certaines fonction du connecteur LDAP (./logs/ldap_agencies.log ./logs/ldap_services.log) <br />
- Droits : le bouton de désactivation du droit admin est supprimé pour le profile administrateur (./admin/profile.php) <br />
- Mise à jour : Optimisation de la detection des droits d'écriture pour l'installation des mises à jour semi-automatique (./admin/update.php) <br />
- Paramètres : Augmentation du nombre de caractères de l'adresse mail d'envoi passage à 200 caractères (SQL) <br />
- Paramètre : Cloisonnement des utilisateurs par service<br />
- Paramètre : Cloisonnement des utilisateurs par agences<br />
- Utilisateur : Gestion de plusieurs service par utilisateur<br />
- Connecteur LDAP : Synchronisation des groupe LDAP de service ou d'agence<br />
- Connecteur IMAP : Gestion multi-bal par service<br />
- Fonction sondage : Nouvelle fonction sondage permettant de demander à l'utilisateur de remplir un questionnaire.(./survey.php ./core/export_survey.php ./core/auto_mail.php ./core/ticket.php)<br />

<br /><br />
<u>Bugfix :</u><br />
- Administration : dans certains cas la liste des utilisateurs n'affichait pas la page 2 (./admin/user.php) <br />
- Liste des tickets : Avec certaines configurations apache les liens vers les tickets ne fonctionnaient pas (./dashboard.php) <br />
- Liste des tickets : lors de la selection des tickets en attente d'attribution dans le menu tous les tickets le menu vos tickets se dépliait (./menu.php) <br />
- Liste des tickets : Amélioration de l'affichage pour les résolution 1280*1024 (./dashboard.php) <br />
- Liste des tickets : Lors de l'utilisation du filtre par date de création ou date de résolution sur la vue activité redirection sur tous les ticket de la date sélectionnée (./core/ticket.php) <br />
- Liste des tickets : Lors de l'affichage de la colonne société avec un compte utilisateur un message d'erreur apparaissait (./dashboard.php) <br />
- Ticket : Lors de l'activation des champs obligatoire sur un ticket, les informations de changement d'état dans les éléments de résolution pouvaient être insérés en double (./core/ticket.php) <br />
- Ticket : Lors de la création d'un nouveau ticket le bouton impression est désactivé car aucune donnée n'est encore enregistrée (./ticket.php) <br />
- Ticket : Avec Internet explorer ajout de commentaire vide lors de l'enregistrement si l'on ajoute pas de texte (./core/ticket.php)<br />
- Ticket : Utilisateur Aucun était en double dans la liste des demandeurs lors de création d'un nouveau ticket dans certains cas (./ticket.php)<br />
- Ticket : Lorsqu'un utilisateur appuyai sur le bouton "clôture ticket" la date de résolution n'était pas inséré sur le ticket (./core/ticket.php)<br />
- Ticket : La liste des techniciens comportait aussi les administrateurs.(./ticket.php)<br />
- Ticket : La valeur par défaut du temps estimé sur un nouveau ticket crée par un utilisateur était d'un mois modification à 5 minutes.(./ticket.php)<br />
- Statistiques : Le chemin de fer de la sections statistique ne fonctionnait pas (./index.php)<br />
- Statistiques tickets : le tableaux top 10 des demandeurs de temps ne tenait pas compte du filtre global de service (./stats/tables.php)<br />
- Statistiques matériels : Le graphique d'évolution des équipements installés et recyclé ne tient plus compte des date d'installation à 0 sur la vue toutes les années  (./stats/line_asset.php)<br />
- Connecteur SMTP : Lors de l'envoi de mail contenant des images dans le corp quand elle n'était pas en base64 un message apparaissaient (./core/mail.php) <br />
- Connecteur IMAP : Lors de la reception d'un mail contenant une signature avec image en provenance d'un client outlook, défaut d'affichage dans les mail émit (./mail2ticket.php) <br />
- Connecteur LDAP : Suppression message d'avertissement T_() lors de l'utilisation de cron  (./core/ldap.php) <br />
- Connecteur LDAP : Gestion des adresses mail avec des apostrophes (./core/ldap.php) <br />
- Connecteur LDAP : Amélioration de la déconnexion serveur (./core/ldap*) <br />
- Rappel de ticket : Suppression de la description pouvant provoquer des difficultés d'affichage avec du code HTML (./event.php) <br />
- Rappel de ticket : L'ajout à la valeur demain ne fonctionnait pas (./event.php) <br />
- Rappel de ticket : L'utilisateur associé au rappel était le technicien du ticket, modification pour que ce soit l'utilisateur connecté.(./event.php) <br />
- Mise à jour automatique : un message d'avertissement apparaissait lors de l'installation de la mise à jour.(./admin/update.php) <br />
- Fonction disponibilité : un message d'erreur pouvait apparaître avec certaines valeurs nulles.(./plugins/availability/core.php) <br />
- Administration liste modèle équipement : Sur l'ajout d'un nouvelle entrée les valeurs IP et WIFI n'était pas prises en compte.(./admin/list.php) <br />
<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 06/04/2017             <br />
# @Version : 3.1.19        	 	 <br />
#################################<br />
<br />
<u>Notice:</u><br />Augmentation du prés-requis mémoire allouée à PHP, passage de 256MB à 512MB. <br />
<br />
<u>Update :</u><br />
- Liste des tickets: Le bouton ce jour devient activité, montrant tous les tickets ouverts, fermés et sur lequel un élément de résolution a été ajouté aujourd'hui. (./dashboard.php) <br />
- Liste des tickets: Modification de la couleur des numéro de tickets, vert pour les fermés du jour, orange pour les ouverts du jour, bleu pour les tickets sur lesquels un élément de résolution a été ajouté et rouge pour les tickets non lu par le technicien en charge (./dashboard.php) <br />
- Liste des tickets: Sur la vue activité une selection de période est possible (./dashboard.php) <br />
- Liste des tickets: Lorsque le droit d'affichage de l'heure est donnée à la colonne date de création les secondes n'apparaissent plus. (./dashboard.php) <br />
- Ticket : Lors de la création d'un ticket par un technicien, si ce dernier ajoute directement une résolution alors le ticket passe automatiquement à en cours. (./core/ticket.php) <br />
- Ticket : Lors de la création d'un ticket par un utilisateur ne disposant pas de droit de modification de l'état le ticket passai dans l'état en attente de PEC au lieu de en attente d'attribution. (./core/ticket.php) <br />
- Ticket : Lors de l'ajout d'une résolution par un technicien lorsque le ticket est dans l'état en attente de PEC, la modification automatique de statut vers l'état en cours ne crée pas automatiquement de balise de changement d'état dans le fil de résolution. (./core/ticket.php) <br />
- Statistique: L'export des tickets et des matériels tiennent compte du filtre global. (./core/export_asset.php ./core/export_tickets ./stat.php) <br />
- Statistique: Nouvelle courbe de l'évolution des éléments de résolution dans la list tickets. (./ticket_stat.php ./stat/line_tickets_activity.php) <br />
- Statistique: Nouveau tableau reflétant les temps par status sur les tickets. (./ticket_stat.php ./stat/table.php) <br />
- Liste des équipements : une nouvelle colonne localisation est disponible cf droit asset_list_col_location (./asset_list.php) <br />
- Équipements : Nouveau champ localisation disponible sur la fiche équipement voir droit asset_location_disp (./asset.php ./core/asset.php) <br />
- Équipements : Redimensionnement automatique de l'image associée au modèle si cette dernière dépasse 250px.  (./core/ticket.php) <br />
- Équipements : Possibilité d'ajouter plusieurs interfaces IP à un équipement, une gestion des rôles d'interface est disponible dans la gestion des listes. (./images/images/plug.png ./tasset_iface.php ./asset.php ./core/asset.php ./admin/liste.php) <br />
- Équipements : La liste des états est trié par ordre définit dans la liste des états. (./) <br />
- Équipements : Sur la recherche de nouvelle adresse IP disponible, il est possible de configurer les états d'équipement à exclure de la recherche, paramètre "block_ip_search" dans la liste des états des équipements . (./asset_findip.php ./core/asset.php) <br />
- Équipements : Gestion des équipements virtuels, voir la liste des types d'équipement pour l'activer sur un type et ajouter le droit asset_virtualization_disp, certains champs de la fiche équipement seront automatiquement masqué si l'équipement est marqué comme virtuel (./asset.php) <br />
- Équipements : sur la fonction ping des équipements IP une vérification de la bonne formation d'une IPv4 est réalisé avant de déclencher le ping (./core/ping.php) <br />
- Menu: Toutes les section du logiciel dispose d'un favicon(./index.php ./images/favicon*) <br />
- Mise à jour: Ajout d'un contrôle sur les droits d'écriture sur la page des mises à jour (./core/update.php) <br />
- SQL: Optimisation des requêtes et des index (SQL ./*) <br />
- Composants: Mise à jour de PHPMailer en version 5.2.23 (./components/PHPmailer/*) <br />
- Traduction: Amélioration des traductions (./locale/*) <br />
<br /><br />
<u>Bugfix :</u><br />
- Ticket : Lors de la désactivation d'un utilisateur ce dernier n'était pas conservé sur le ticket en cas de ré-enregistrement (./ticket.php) <br />
- Ticket : Lors de la création d'un ticket par un utilisateur, pour le technicien la liste déroulante technicien pouvait contenir deux fois la valeur aucun (./ticket.php) <br />
- Ticket : Lors de la création d'un nouveau ticket avec le navigateur edge un élément de résolution vide apparaissait (./core/ticket.php) <br />
- Liste des tickets: Certaines colonnes n'étaient pas centrées (./dashboard.php) <br />
- Liste des tickets: Sur le filtre des lieux la page 2 ne fonctionnai pas, perte du filtre.(./dashboard.php) <br />
- Liste des tickets: Les filtres de date affichaient la valeur au format SQL "YYYY-MM-DD" au lieu de "DD/MM/YYYY".(./dashboard.php) <br />
- Liste des tickets: Le filtre titre ne fonctionnai plus. (./dashboard.php) <br />
- Liste des tickets: Le champ filtre de la date de création pouvait ne pas être centré sur les grands écrans.(./dashboard.php) <br />
- Liste des équipements : Lors du changement de filtre du status le choix vide (tous) n'était pas conservé. (./asset_list.php) <br />
- Liste des équipements : la selection courante ou la recherche pouvait être perdu à la suite de la suite de certaines modification d'un ticket. (./index.php ./asset_list.php ./asset.php ./core/asset.php) <br />
- Recherche tickets: Lors de la recherche de ticket avec un mot clé égale à un nom d'utilisateur, la recherche pouvait être erronée si deux utilisateurs avec le même nom étaient présent. (./core/searchengine_ticket.php) <br />
- Recherche tickets: Perte de la recherche lors de l'utilisation d'un filtre. (./core/searchengine_ticket.php) <br />
- Recherche équipement: Perte de la recherche lors de l'utilisation d'un filtre. (./core/searchengine_asset.php) <br />
- Équipement import: le fichier modèle d'import CSV avait un défaut d'encodage avec Excel (./downloads/tassets_template.csv)<br />
- Connecteur SMTP: Les messages émit par l'application possédant des images intégré dans les champs description ou résolution n'apparaissaient dans certain clients de messagerie (./core/mail.php) <br />
- Connecteur SMTP: Erreur sur certains serveurs de messagerie lors de l'envoi de message, cf nouveau paramètre connecteur "Vérification SSL" (./core/mail.php ./admin/parameters.php) <br />
- Connecteur LDAP: Lors de la synchronisation LDAP les utilisateurs disposant de login possédant des simple quote bloquai la synchronisation (./core/ldap.php) <br />
- Système: Les valeurs de mémoire alloué à PHP n'était pas vérifié dans certains cas (./core/mail.php) <br />
- Statistiques : Le graphique de répartition des tickets par société pouvait être affiché même lorsqu'il y en avait aucune (./ticket_stat.php) <br />
- Administration: Dans la liste des utilisateurs un défaut d'affichage sur la fiche prouvai apparaître dans certains cas (./admin/user.php) <br />
- Fonction disponibilité: certaines variable pouvaient être non initialisé en l'absence de tickets (./plugins/availability/core.php) <br />

<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 01/03/2017             <br />
# @Version : 3.1.18        	 	 <br />
#################################<br />

<br />
<u>Notice:</u><br />Pour les utilisateurs du connecteur IMAP, sachez que pour une meilleur gestion de la récupération des réponses dans les mails deux nouvelles lignes sont présentes sur les messages émit. <br />
<br />
<u>Update :</u><br />
- Ticket : Lien hypertexte de type "tel:" sur le numéro de téléphone, pour jonction application IPBX (./ticket.php) <br />
- Ticket : Le nom de la société peut être affichée dans la liste déroulante des demandeurs cf droit "ticket_user_company" (./ticket.php) <br />
- Liste des tickets: Les colonnes catégorie et sous-catégorie peuvent être masquées cf droit dashboard_col_subcat et dashboard_col_category (./dashboard.php)<br />
- Liste des tickets: Une nouvelle colonne avec la société associé à l'utilisateur est disponible cf droit dashboard_col_company (./dashboard.php)<br />
- Liste des tickets: Il est possible d'affiche l'heure de création des ticket en plus de la date cf droit dashboard_col_date_create_hour (./dashboard.php)<br />
- Liste des tickets: Il est possible d'arriver directement sur l'état "Tous les tickets à traiter", via le paramètre personnel "État par défaut" (./login.php ./admin/user.php)<br />
- Liste des sociétés: Ajout du champ "Country" dans la liste des sociétés dans la gestion des listes de la partie administration (SQL) <br />
- Paramètre ticket: Un nouveau paramètre "Numéro d'incrémentation" permet d'initialiser le compteur de ticket à une valeur souhaitée (./admin/parameters.php)<br />
- Connecteur IMAP: Il est possible d'exclure des adresses mails ou des domaines lors l'import de message, cf paramètre du connecteur (./mail2ticket.php /admin/parameters.php) <br />
- Connecteur IMAP: Il est possible de supprimer le mail ou le déplacer dans un dossier une fois convertit en ticket, cf paramètre du connecteur (./mail2ticket.php /admin/parameters.php) <br />
- Mails : L'ordre d'affichage des éléments de résolution dans les mails peut être antéchronologique, cf paramètre des messages (./core/mail.php /admin/parameters.php) <br />

<br /><br />
<u>Bugfix :</u><br />
- Ticket : Lors de la désactivation d'un technicien perte du technicien sur l'édition d'un ticket lui appartenant (./ticket.php) <br />
- Ticket : Problème d'alignement du champ mail sur l'édition d'un utilisateur (./ticket_useradd.php) <br />
- Ticket : Suppression de l'édition de l'utilisateur "Aucun" dans la liste des demandeurs (./ticket.php) <br />
- Liste des tickets: Dans la liste déroulante des techniciens les groupes de techniciens ne comportaient pas le préfixe [G] (./dashboard.php) <br />
- Mail : Le lien intégré vers le ticket n'était pas présent quand un groupe de technicien était en charge du ticket. (./core/mail.php) <br />
- Ajout utilisateur: Lors de l'ajout d'un nouvel utilisateur via "Administration" > "Nouvel Utilisateur" dans le champ téléphone un simple quote apparaissait. (./admin/user.php) <br />
- Connecteur IMAP: Ajout de tag sur les mails envoyés pour une meilleur récupération de la réponse (./core/mail.php ./mail2ticket.php) <br />
- Connecteur SMTP: Erreur d'envoi de message error:14090086:SSL avec certaines ancienne version de PHP (./core/mail.php) <br />
- Connecteur LDAP: Une variable pouvait être non initialisée (./core/ldap.php) <br />
- Statistiques : Le filtre sur le service n'était pas pris en compte sur le graphique de l'évolution dans le temps  (./stats/line_tickets.php ./stats/asset_stat.php) <br />
- W3C: Correction HTML balise font size obsolète<br />

<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 10/02/2017             <br />
# @Version : 3.1.17        	 	 <br />
#################################<br />

<br />
<u>Update :</u><br />
- Ticket : Les messages de résolution peuvent être masqués pour les utilisateurs cf droit ticket_thread_private ticket_thread_private_button (./core/ticket.php ./core/auto_mail.php ./ticket.php) <br />
- Affichage: Optimisations de l'affichage pour smart phones. (./index.php ./ticket.php)<br />
- Procédure: Ajout de fichiers joint.(./procedure.php)<br />
- Équipements : Possibilité de gérer des équipements non IP uniquement, cf paramètre de la fonction équipements.<br /> 

<br /><br />
<u>Bugfix :</u><br />
- Import d'équipement: Le répertoire d'import est automatiquement crée si il n'existe pas (./admin/parameters.php) <br />
- Ticket : La modification d'état ne fonctionnait pas si un technicien venait d'être attribué (./core/ticket.php) <br />
- Ticket : lors de la création d'un ticket issue du connecteur IMAP, si l'utilisateur est connu de l'application il devient le créateur du ticket (./mail2ticket.php)<br />
- Liste tickets: couleur grise sur la priorité aucune au lieu de la blanche qui n'était pas visible.<br />
- Liste tickets: Erreur sur l'affichage de la catégorie ou sous-catégorie aucune lors de l'affichage avec une autre langue (./dashboard.php).<br />
- Liste tickets: Le titre des vues n'apparaissaient pas (./dashboard.php).<br />
- Impression ticket: La traduction n'était pas prise en compte. (./ticket_print.php).<br />
- Statistique: Erreur d'affichage sur la charge par technicien (./stat_histo.php)<br />
- Statistique: Lors de la selection du service dans le filtre global une valeur erroné restai sélectionner (./ticket_stat.php)<br />
- Mail : Dans le contenu du mail, le tableau n'était pas bien dimensionné par certains clients de messagerie (./core/mail.php)<br />
- Menu: La sélection de l'état est conservé depuis un ticket ou la fiche d'un équipement (./ticket.php ./menu.php) <br />
- Menu: Le menu vos tickets restai ouvert même si l'on était sur la vue tous les tickets (./menu.php)<br />
- Paramètre: Le logo était supprimée lors de la modification d'un paramètre de l'onglet général (./parameters.php)<br />
- Moniteur : La page n'était pas traduite (./parameters.php ./monitor.php)<br />
- Fiche utilisateur: Lors du rattachement d'un utilisateur à un technicien, le nom de l'utilisateur apparaissait toujours dans la liste des utilisateurs  (./admin/user.php)<br />
- LDAP: Lors de la synchronisation LDAP le compte admin GestSup était désactivé si non présent dans l'annuaire AD (./core/ldap.php)<br />
- LDAP: Lors de la synchronisation LDAP erreur lors de la création d'utilisateur possédant des apostrophe dans le nom de leur ville (./core/ldap.php)<br />
- Connecteur IMAP: Défaut de nettoyage html dans certains mails émit par les clients outlook (./mail2ticket.php)<br />
- Administration: Dans la liste des utilisateurs si il y avait 31 utilisateurs alors une page 3 était affiché (./admin/user.php)<br />

<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 30/01/2017             <br />
# @Version : 3.1.16        	 	 <br />
#################################<br />

<br />
<u>Update :</u><br />
- Logo: Les logo trop grands sont automatiquement redimensionnés sur la page de login et dans le logiciel (./login.php ./index.php).<br />
- Procédure: un nouveau droit nommé "procedure_modify" permet de bloquer l'accès en modification sur les procédures à certains profils (./procedures.php)<br />
- PhpMailer: mise à jour 5.2.22 (./components/phpmailer/*.php)<br />
- Liste des tickets: Les colonnes "Type" et "Criticité" sont activables ou désactivables via les droits "dashboard_col_type" et dashboard_col_criticality (./dashboard.php ./index.php)<br />
- Équipement : Bouton retour liste disponible sur la fiche d'un équipement (./index.php)<br />
- Import de équipements depuis un csv: (./admin/parameters.php ./downloads/tassets_template.csv ./upload/asset ./core/import_assets.php)<br />
- Équipement : changement de nom des matériels pour équipements, car ils peuvent être virtuels (./locale/* ./stat/* ./asset*)<br />

<br /><br />
<u>Bugfix :</u><br />
- Ticket : Les noms des états dans la résolution lors d'un changement d'état n'étaient pas traduit. (./thread.php)<br />
- Ticket : Lors de la planification d'une intervention, l'utilisateur associé était celui connecté et non le technicien rattaché au ticket . (./thread.php)<br />
- Ticket : Le bouton retour liste ne conservait pas le numéro de la page. (./index.php ./dashboard.php ./core/ticket.php)<br />
- Liste tickets: Perte du filtre lors de l'appui sur le bouton "annuler" ou "Enregistrer fermer"  sur le ticket.(./core/ticket.php ./index.php ./dashboard.php)<br />
- Liste tickets: Perte de l'ordre de trie sur retour d'un ticket.(./core/ticket.php ./index.php ./dashboard.php)<br />
- Login: La langue du navigateur était affiché. (./localization.php)<br />
- Planning: la liste déroulante des techniciens ne conservait pas la selection. (./planning.php)<br />
- Planning: la liste déroulante des techniciens pouvait contenir des techniciens désactivés (./planning.php)<br />
- Planning: Variables numérique non initialisé avec PHP 7 (./planning.php)<br />
- Statistiques : Le camembert concernant la répartition de la charge de travail par catégorie restait à 100% sur une catégorie (./stats/pie_load.php)<br />
- Connecteur IMAP: Certaines images dans le contenu du message ne n'affichai pas correctement (./mail2ticket.php)<br />
- Connecteur IMAP: Lors de l'intégration de réponse depuis des clients Outlook certaines informations était en trop (./mail2ticket.php)<br />
- Login: La variable de langue pouvait ne pas être initialisé (./localization.php)<br />
- Écran de supervision: variable non initialisé au premier lancement (./monitor.php)<br />
- Rappel: variable non initialisé dans certains cas(./event.php)<br />
- Système: l'icône de MySQL ou MariaDB pouvait dans certain cas ne pas s'afficher (./images/MySQL.png ./images/MariaDB.png)<br />
- Équipement : sur la fiche équipement perte des données saisie avec le bouton "Enregistrer et Fermer" en cas d'erreur de l'adresse MAC (./core/asset.php)<br />
- Équipement : sur la fiche équipement sur le champ date de fin de garantie il n'était plus possible de le remettre à vide si les années de garantie était spécifié sur le modèle (./asset.php)<br />
- Équipement : Lors de la création d'un nouveau équipement la date d'installation n'apparaissait pas (./asset.php ./core/asset.php)<br />
- Liste équipements: Perte du numéro de la page en cours lors de l'utilisation du bouton "Annuler" ou "Enregistrer et Fermer" sur la fiche d'un équipement (./asset_list.php ./core/asset.php)<br />
- Liste équipements: Le trie par numéro n'affichait pas les flèches de trie (./asset_list.php)<br />
- Liste équipements: Perte du trie lors de l'utilisation de la flèche retour sur une fiche équipement (./core/asset.php)<br />
- Administration des listes: Les listes n'étaient pas triées par ordre alphabétique des noms (./admin/list.php)<br />
- Administration des groupes: variable type pouvait ne pas être initialisée (./admin/group.php)<br />
- Thème: L'arrière plan du thème gris possédait des rayures horizontales. (./template/assets/css/ace-skins.min.css)<br />

<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 05/01/2017             <br />
# @Version : 3.1.15        	 	 <br />
#################################<br />

<br />
<u>Update :</u><br />
- Traduction: L'application est complètement disponible en Anglais, Allemand, Espagnol  (./*).<br />
- Composant: Mise à jour de PHPMailer version 5.2.21(./components/phpmailer/*).<br />
- Système: Affichage de la version des composants dans l'état système(./system.php ./components/*).<br />
- Mail2ticket: Diminution de l'affichage des informations d'import lorsque le mode debug n'est pas activé(./mail2ticket.php).<br />
- Ticket : Les changements d'état sont enregistrés dans la résolution (./core/ticket.php ./thread.php)<br />
- Équipement : Il est possible de planifier un ping pour tous les équipements réseau afin de remonter les équipements obsolète cf FAQ (./asset.php ./core/export_asset.php ./core/ping.php)<br />
- SGBDR: Compatibilité avec MariaDB, port personnalisable à l'installation de la version 3.1.15 (./images/mariadb.png ./images/mysql.png ./install/index.php ./system.php)<br />

<br /><br />
<u>Bugfix :</u><br />
- Menu des équipements: les compteurs par état ne fonctionnai pas. (./menu.php)<br />
- WOL: Le réveil des équipements par le réseau ne fonctionnai pas avec un serveur Windows quand le répertoire de l'application possédait un espace. (./core/wol.php)<br />
- Statistiques : Dans l'onglet équipement le comptage des équipements recyclés n'étaient pas correcte. (./stat/line_assets.php)<br />
- Statistiques : Sur le filtre la liste déroulante concernant les années n'était pas trié. (./asset_stat.php ./ticket_stat.php)<br />
- Liste des tickets: lors de la modification en lot vers l'état résolu, la date de résolution n'était pas renseignée sur le ticket (./dashboard.php)<br />
- Connecteur LDAP: Pas de création d'utilisateur GestSup si la valeur de l'identifiant retourné est vide (./core/ldap.php)<br />
- Planning: Erreur de numérotation de certains jours de la semaine en janvier 2017 (./planning.php)<br />
- Ticket : Erreur lors de la planification d'une intervention la date de début était à 0 (./event.php)<br />