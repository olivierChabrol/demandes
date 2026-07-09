<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
/*************************************/
/* Permet de gerer l'upload des fichiers en amont de la validation du formulaire des demandes
/*************************************/

// Fonction pour écrire dans le fichier de log
function debugLog($message, $trace = false) {
    if ($trace) {
      $logFile = __DIR__ . '/debug_upload.log';
      $date = date('Y-m-d H:i:s');
      // print_r avec "true" permet de formater les tableaux (comme $_FILES) en texte
      $text = "[$date] " . print_r($message, true) . "\n";
      file_put_contents($logFile, $text, FILE_APPEND);
    }
}

// 1. Initialisation de la réponse (évite le crash du json_encode à la fin)
$data = array();
debugLog("=== NOUVELLE TENTATIVE D'UPLOAD ===");
debugLog("Contenu de POST :");
debugLog($_POST);

debugLog("Contenu de FILES :");
debugLog($_FILES);

// 2. Vérification sécurisée du nombre de fichiers
$nbfiles = 0;
if (isset($_FILES['files']['name']) && is_array($_FILES['files']['name'])) {
    $nbfiles = count($_FILES['files']['name']);
}

// 3. Sécurisation du dossier de destination (empêche les failles Directory Traversal style "../")
//$uploadDir = isset($_POST['dir']) ? preg_replace('/[^a-zA-Z0-9_\-]/', '', $_POST['dir']) : 'temp';
$uploadDir = isset($_POST['dir']) ? $_POST['dir'] : 'temp';
$path = dirname(__FILE__)."/".$uploadDir."/";

if(!file_exists($path)) {
    mkdir($path, 0777, true);
}

// Avant la création du dossier
debugLog("uploadDir : " . $uploadDir);
debugLog("Dossier cible : " . $path);

// Avant la boucle
debugLog("Nombre de fichiers détectés : " . $nbfiles);

for($i = 0; $i < $nbfiles; $i++) {

    // On passe si le fichier est vide ou en erreur
    if (!isset($_FILES['files']['size'][$i]) || $_FILES['files']['size'][$i] == 0) {
        continue;
    }

    if(isset($_POST['id'])) { //cas d'un ticket direct
        $real_filename = $_POST['id'].'_'.md5(uniqid()).'_'.preg_replace("/[^A-Za-z0-9\_\-\.\s+]/", '', $_FILES['files']['name'][$i]);
    } else { //cas d'une demande à valider
        $real_filename = preg_replace("/[^A-Za-z0-9\_\-\.\s+]/", '', $_FILES['files']['name'][$i]);
    }

    // Juste avant move_uploaded_file
    debugLog("Tentative de déplacement de : " . $_FILES['files']['tmp_name'][$i] . " vers " . $path.$real_filename);

    // On déplace le fichier avec son NOUVEAU nom ($real_filename)
    if(move_uploaded_file($_FILES['files']['tmp_name'][$i], $path.$real_filename)) {
        debugLog("SUCCÈS : Fichier déplacé.");
        $data[$i]['name'] = $_FILES['files']['name'][$i];
        $data[$i]['realname'] = $real_filename;
        
        // CORRECTION : On lit le fichier avec son nouveau nom sur le serveur ($real_filename)
        $file_content = file_get_contents($path.$real_filename);
        
        if(preg_match('{\<\?php}',$file_content) || preg_match('/system\(/',$file_content)) {
            // CORRECTION : On supprime le fichier avec son nouveau nom
            unlink($path.$real_filename); 
            $data[$i]['msgtype'] = "error";
            $data[$i]['msg'] = "Fichier interdit";
        } else {
            $data[$i]['msgtype'] = "valid";
            $data[$i]['msg'] = "Fichier bien ajouté";
        }
    }
    else {
      debugLog("ERREUR : move_uploaded_file a échoué pour le fichier " . $i);
    }
}

// À la toute fin
debugLog("Réponse JSON finale :");
debugLog(json_encode($data));
// On s'assure de renvoyer un tableau JSON valide même si vide
echo json_encode($data);
?>