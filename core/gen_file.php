<?php
################################################################################
# @Name : ./core/gen_file.php 
# @Description : generate file
# @Author : Olivier CHABROL
# @Create : 03/07/2026
# @Update : 03/07/2026
# @Version : 3.2.4 p2
################################################################################

require __DIR__ . '/../vendor/autoload.php';


define('DIR_TEMPLATES', __DIR__ . '/../template/');
define('DIR_OUTPUTS', __DIR__ . '/../upload/sifac/');

use PhpOffice\PhpSpreadsheet\IOFactory;

function toUpper($string) {
    if(isset($string) && !empty($string) && $string !== null) {
        $string = strtoupper(trim($string));
    } else {
        $string = "";
    }
    return $string;
}

/**
 * Génère le fichier de demande SIFAC à partir du modèle.
 *
 * @param string $guestName
 * @param string $guestBirthdate
 * @param string $guestPhonenumber
 * @param string $guestMail
 * @return string Le chemin absolu vers le fichier généré.
 * @throws Exception Si le fichier template est introuvable.
 */
function genererFicheSifac(string $guestFirstName, string $guestLastName, $guestBirthdate, string $guestPhonenumber, string $guestMail, string $administrator = null, string $ouputDir = DIR_OUTPUTS) {
    
    // 1. Pointage vers ton dossier template existant
    $templateName = 'fo-daf-623-demande_de_creation_de_matricule_sifac_2026.xlsx';
    $inputFilePath = DIR_TEMPLATES . $templateName;

    if (!file_exists($inputFilePath)) {
        throw new Exception("Erreur : Le modèle est introuvable dans " . $inputFilePath);
    }
    
    // 2. Vérification/Création du dossier de sortie (upload/sifac/)
    if (!is_dir($ouputDir)) {
        mkdir($ouputDir, 0755, true);
    }

    $guestFirstName   = toUpper($guestFirstName);
    $guestLastName    = toUpper($guestLastName);
    $guestPhonenumber = toUpper($guestPhonenumber);
    $guestMail        = toUpper($guestMail);
    $administrator    = toUpper($administrator);

    $guestName = trim($guestFirstName . ' ' . $guestLastName);

    // Préparation du nom du fichier de sortie
    $safeName = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($guestName));
    $outputFileName = "demande_sifac_2026_{$safeName}_" . time() . ".xlsx";
    $outputFilePath = $ouputDir . $outputFileName;

    // 3. Chargement et traitement avec PhpSpreadsheet
    $spreadsheet = IOFactory::load($inputFilePath);
    $worksheet = $spreadsheet->getActiveSheet();

    // 1. Uniformisation du format de la date de naissance (vers JJ/MM/AAAA)
    $dateNaissanceFormatee = '';

    if ($guestBirthdate instanceof DateTime) {
        // Cas A : Le modèle de la BDD a renvoyé un objet DateTime
        $dateNaissanceFormatee = $guestBirthdate->format('d/m/Y');
    } elseif (is_string($guestBirthdate) && !empty($guestBirthdate)) {
        // Cas B : La BDD a renvoyé une chaîne brute (ex: "1990-04-12")
        // On la convertit d'abord en objet, puis on la formate
        try {
            $dateObj = new DateTime($guestBirthdate);
            $dateNaissanceFormatee = $dateObj->format('d/m/Y');
        } catch (Exception $e) {
            // En cas d'échec de lecture de la date, on remet la chaîne d'origine par sécurité
            $dateNaissanceFormatee = $guestBirthdate; 
        }
    } else {
        // Cas C : Le champ est vide ou nul
        $dateNaissanceFormatee = (string) $guestBirthdate;
    }
    $replacements = [
        '{guest-firstname}'   => (string) $guestFirstName,
        '{guest-lastname}'    => (string) $guestLastName,
        '{guest-name}'        => (string) $guestName,
        '{guest-birthdate}'   => (string) $dateNaissanceFormatee,
        '{guest-phonenumber}' => (string) $guestPhonenumber,
        '{guest-mail}'        => (string) $guestMail,
        '{administrator}'     => (string) $administrator
    ];

    foreach ($worksheet->getRowIterator() as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true); 
        
        foreach ($cellIterator as $cell) {
            $value = $cell->getValue();
            if (is_string($value)) {
                $newValue = str_replace(
                    array_keys($replacements), 
                    array_values($replacements), 
                    $value
                );
                if ($newValue !== $value) {
                    $cell->setValue($newValue);
                }
            }
        }
    }

    // 4. Sauvegarde dans le sous-dossier upload/sifac/
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save($outputFilePath);
    
    return $outputFileName;
}

// --- Exemple d'exécution ---
// $fichierCree = genererFicheSifac('Jean Dupont', '12/04/1990', '0601020304', 'jean.dupont@univ-amu.fr');
// echo "Le fichier a été généré avec succès : " . $fichierCree;

?>