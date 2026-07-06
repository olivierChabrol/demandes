<?php
/*************************************/
/* Permet de gerer la suppression des fichiers en amont de la validation du formulaire des demandes
/*************************************/

// Fonction de log pour le débugging
function debugLog($message, $trace = false) {
    if ($trace) {
      $logFile = __DIR__ . '/debug_delete.log';
      $date = date('Y-m-d H:i:s');
      $text = "[$date] " . print_r($message, true) . "\n";
      file_put_contents($logFile, $text, FILE_APPEND);
    }
}

// Vérification de la présence des variables
if (!isset($_POST['dir']) || !isset($_POST['file'])) {
    echo json_encode(['msgtype' => 'error', 'msg' => 'Paramètres manquants']);
    exit;
}

// 1. Sécurisation stricte des variables
$uploadDir = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_POST['dir']);
// On utilise la même expression régulière que pour l'upload
$fileName = preg_replace('/[^A-Za-z0-9\_\-\.\s+]/', '', $_POST['file']); 

// 2. Construction du chemin absolu (comme pour l'upload)
$path = __DIR__ . "/" . $uploadDir . "/" . $fileName;

debugLog("Tentative de suppression : " . $path);

// 3. Vérification de l'existence du fichier avant suppression
if (file_exists($path)) {
    if (unlink($path)) { // remove file
        debugLog("SUCCÈS : Fichier supprimé");
        $data[0]['msgtype'] = 'valid';
        $data[0]['msg'] = 'Fichier supprimé';
        echo json_encode($data);
    } else {
        debugLog("ERREUR : Le fichier existe, mais unlink() a échoué (problème de permissions ?)");
        echo json_encode(['msgtype' => 'error', 'msg' => 'Erreur de droits serveur']);
    }
} else {
    debugLog("ERREUR : Le fichier est introuvable à ce chemin.");
    echo json_encode(['msgtype' => 'error', 'msg' => 'Fichier introuvable sur le serveur']);
}
?>