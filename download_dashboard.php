<?php
/**
 * This script generates an Excel file with data from a database query.
 * The Excel file is then downloaded by the user.
 *
 */


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// Filename for the Excel file to be downloaded
$filename = 'donnees.xlsx';

// Set headers to indicate to the browser that the content is an Excel file to be downloaded
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Initialize an array to hold the rows of the Excel file
$excelRow = array();

// Add columns to the Excel file based on user's profile and settings
$excelRow[] = T_('Numéro');
if ($_SESSION['profile_id'] != 0 || $_SESSION['profile_id'] != 4 || $_GET['userid'] == '%')
    $excelRow[] = T_('Gestionnaire');
if (($_SESSION['profile_id'] == 0 || $_SESSION['profile_id'] == 1 || $_SESSION['profile_id'] == 2 || $_SESSION['profile_id'] == 3 || $_SESSION['profile_id'] == 4) || ($rright['side_all'] && ($_GET['userid'] == '%' || $keywords != '')) || ($rparameters['user_company_view'] != 0 && $_GET['userid'] == '%' && ($rright['side_company'] || $keywords != '')))
    $excelRow[] = T_('Demandeur');
if ($rright['dashboard_col_user_service'])
    $excelRow[] = T_('Service du demandeur');
if ($rright['dashboard_col_type'])
    $excelRow[] = T_('Type');
if ($rright['dashboard_col_category'])
    $excelRow[] = T_('Catégorie');
if ($rright['dashboard_col_subcat'])
    $excelRow[] = T_('Financement');
if ($rright['dashboard_col_asset'])
    $excelRow[] = T_('Équipement');
if ($rparameters['ticket_places'] == 1)
    $excelRow[] = T_('Lieu');
if ($rright['dashboard_col_service'])
    $excelRow[] = T_('Service');
$excelRow[] = T_('Intitulé de la demande');
if ($rright['dashboard_col_date_create'])
    $excelRow[] = T_('Date demande');
if ($rright['dashboard_col_date_hope'])
    $excelRow[] = T_('Date de résolution estimée');
if ($rright['dashboard_col_date_res'])
    $excelRow[] = T_('Date de résolution');
if ($rright['dashboard_col_time'])
    $excelRow[] = T_('Temps passé');
$excelRow[] = T_('Date début');
$excelRow[] = T_('Date retour');
$excelRow[] = T_('Durée (en jours)');
$excelRow[] = ($_GET['view'] == 'activity') ? T_('État actuel') : T_('État');
if ($rright['dashboard_col_priority'])
    $excelRow[] = T_('Priorité');
if ($rright['dashboard_col_criticality'])
    $excelRow[] = T_('Criticité');

$excelData= [$excelRow];

// Execute a SQL query to fetch data from the database
$masterquery = $db->query($_POST['query']);

// Process the fetched data and write it to the Excel file
while ($row = $masterquery->fetch()) {
    //select name of states
    $excelRow = array();
    $qry = $db->prepare("SELECT `display`,`description`,`name` FROM `tstates` WHERE `id`=:id");
    $qry->execute(array('id' => $row['state']));
    $resultstate = $qry->fetch();
    $qry->closeCursor();
    if (empty($resultstate['display'])) {
        $resultstate['display'] = '';
    }
    if (empty($resultstate['description'])) {
        $resultstate['description'] = '';
    }
    if (empty($resultstate['name'])) {
        $resultstate['name'] = '';
    }

    if ($rright['dashboard_col_priority']) {
        //select name of priority
        $qry = $db->prepare("SELECT `name`,`color` FROM `tpriority` WHERE `id`=:id");
        $qry->execute(array('id' => $row['priority']));
        $resultpriority = $qry->fetch();
        $qry->closeCursor();
        if (empty($resultpriority['name'])) {
            $resultpriority['name'] = '';
        }
        if (empty($resultpriority['color'])) {
            $resultpriority['color'] = '';
        }
        $qry = $db->prepare("SELECT `id`,`phone`,`lastname`,`firstname`,`address1` FROM `tusers` WHERE `id`=:id");
    }

    //select name of user
    $qry->execute(array('id' => $row['user']));
    $resultuser = $qry->fetch();
    $qry->closeCursor();
    if (empty($resultuser['id'])) {
        $resultuser['id'] = 0;
    }
    if (empty($resultuser['lastname'])) {
        $resultuser['lastname'] = '';
    }
    if (empty($resultuser['firstname'])) {
        $resultuser['firstname'] = '';
    }
    if (empty($resultuser['phone'])) {
        $resultuser['phone'] = '';
    }

    //select name of user group
    $qry = $db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE `id`=:id");
    $qry->execute(array('id' => $row['u_group']));
    $resultusergroup = $qry->fetch();
    $qry->closeCursor();
    if (empty($resultusergroup['id'])) {
        $resultusergroup['id'] = 0;
    }
    if (empty($resultusergroup['name'])) {
        $resultusergroup['name'] = '';
    }

    //select name of technician
    $qry = $db->prepare("SELECT `id`,`lastname`,`firstname` FROM `tusers` WHERE `id`=:id");
    $qry->execute(array('id' => $row['technician']));
    $resulttech = $qry->fetch();
    $qry->closeCursor();
    if (empty($resulttech['id'])) {
        $resulttech['id'] = 0;
    }
    if (empty($resulttech['lastname'])) {
        $resulttech['lastname'] = '';
    }
    if (empty($resulttech['firstname'])) {
        $resulttech['firstname'] = '';
    }

    if ($rright['dashboard_col_user_service']) {
        //get user service data
        $qry = $db->prepare("SELECT `name` FROM `tservices` WHERE `id`=:id");
        $qry->execute(array('id' => $row['sender_service']));
        $resultsenderservice = $qry->fetch();
        $qry->closeCursor();
        if (empty($resultsenderservice['name'])) {
            $name_sender_service = T_('Aucun');
        } else {
            $name_sender_service = $resultsenderservice['name'];
        }
    }
    if ($rright['dashboard_col_type']) {
        //select name of type
        $qry = $db->prepare("SELECT `name` FROM `ttypes` WHERE `id`=:id");
        $qry->execute(array('id' => $row['type']));
        $resulttype = $qry->fetch();
        $qry->closeCursor();
        if (empty($resulttype['name'])) {
            $resulttype['name'] = '';
        }
    }
    if ($rright['dashboard_col_category']) {
        //select name of category
        $qry = $db->prepare("SELECT `name` FROM `tcategory` WHERE `id`=:id");
        $qry->execute(array('id' => $row['category']));
        $resultcat = $qry->fetch();
        $qry->closeCursor();
        if ($row['category'] == 0) {
            $resultcat['name'] = T_($resultcat['name']);
        }
        if (empty($resultcat['name'])) {
            $resultcat['name'] = T_('Aucune');
        }
    }
    if ($rright['dashboard_col_subcat']) {
        //select name of subcategory
        $qry = $db->prepare("SELECT `name` FROM `tsubcat` WHERE `id`=:id");
        $qry->execute(array('id' => $row['subcat']));
        $resultscat = $qry->fetch();
        $qry->closeCursor();
        //if($row['subcat']==0) {$resultscat['name']=T_($resultscat['name']);}
        if (empty($resultscat['name'])) {
            $resultscat['name'] = T_('Aucune');
        }
    }
    if ($rright['dashboard_col_asset']) {
        //select name of asset
        $qry = $db->prepare("SELECT `netbios` FROM `tassets` WHERE `id`=:id");
        $qry->execute(array('id' => $row['asset_id']));
        $resultasset = $qry->fetch();
        $qry->closeCursor();
        if (empty($resultasset['netbios'])) {
            $resultasset['netbios'] = '';
        }
    }
    if ($rright['dashboard_col_criticality']) {
        //select name of criticality
        $qry = $db->prepare("SELECT `name`,`color` FROM `tcriticality` WHERE `id`=:id");
        $qry->execute(array('id' => $row['criticality']));
        $resultcriticality = $qry->fetch();
        $qry->closeCursor();
        if (empty($resultcriticality['name'])) {
            $resultcriticality['name'] = '';
        }
        if (empty($resultcriticality['color'])) {
            $resultcriticality['color'] = '';
        }
    }
    if ($rparameters['ticket_places']) {
        //select name of place
        $qry = $db->prepare("SELECT `name` FROM `tplaces` WHERE `id`=:id");
        $qry->execute(array('id' => $row['place']));
        $resultplace = $qry->fetch();
        $qry->closeCursor();
        if (empty($resultplace['name'])) {
            $nameplace = T_('Aucun');
        } else {
            $nameplace = $resultplace['name'];
        }
    }
    if ($rright['dashboard_col_service']) {
        //select name of service
        $qry = $db->prepare("SELECT `name` FROM `tservices` WHERE `id`=:id");
        $qry->execute(array('id' => $row['u_service']));
        $resultservice = $qry->fetch();
        $qry->closeCursor();
        if (empty($resultservice['name'])) {
            $nameservice = T_('Aucun');
        } else {
            $nameservice = $resultservice['name'];
        }
    }
    if ($rright['dashboard_col_agency']) {
        //select name of agency
        $qry = $db->prepare("SELECT `name` FROM `tagencies` WHERE `id`=:id");
        $qry->execute(array('id' => $row['u_agency']));
        $resultagency = $qry->fetch();
        $qry->closeCursor();
        if (empty($resultagency['name'])) {
            $nameagency = T_('Aucune');
        } else {
            $nameagency = $resultagency['name'];
        }
    }
    //convert SQL date to human readable date
    $rowdate_hope = date_cnv($row['date_hope']);
    $rowdate_res = date_cnv($row['date_res']);
    // OC
    $dates_query = $db->prepare("SELECT date_start,date_return from dmission_order WHERE incident_id=" . $row['id']);
    $dates_query->execute();
    $result_date_query = $dates_query->fetch();
    $dates_query->closeCursor();

    $rowdate_start = date_create($result_date_query['date_start']);
    $rowdate_return = date_create($result_date_query['date_return']);
    $diff = date_diff($rowdate_start, $rowdate_return);
    $rowdate_start = date_format($rowdate_start, 'd/m/Y');
    $rowdate_return = date_format($rowdate_return, 'd/m/Y');
    $days_diff = $diff->days ?: 0;
    error_log($days_diff);
    if ($rright['dashboard_col_date_create_hour']) //display hour in create date column
    {
        $rowdate_create = date_create($row['date_create']);
        $rowdate_create = date_format($rowdate_create, 'd/m/Y');
    } else {
        $rowdate_create = date_cnv($row['date_create']);
    }


    $excelRow[] = $row['id'];
    if ($_SESSION['profile_id'] != 0 || $_SESSION['profile_id'] != 4 || $_GET['userid'] == '%')
        $excelRow[] = $resulttech['firstname'] . ' ' . $resulttech['lastname'];
    if (($_SESSION['profile_id'] == 0 || $_SESSION['profile_id'] == 1 || $_SESSION['profile_id'] == 2 || $_SESSION['profile_id'] == 3 || $_SESSION['profile_id'] == 4) || ($rright['side_all'] && ($_GET['userid'] == '%' || $keywords != '')) || ($rparameters['user_company_view'] != 0 && $_GET['userid'] == '%' && ($rright['side_company'] || $keywords != '')))
        $excelRow[] = $resultuser['firstname'] . ' ' . $resultuser['lastname'];
    if ($rright['dashboard_col_user_service'])
        $excelRow[] = T_($name_sender_service);
    if ($rright['dashboard_col_type'])
        $excelRow[] = T_($resulttype['name']);
    if ($rright['dashboard_col_category'])
        $excelRow[] = $resultcat['name'];
    if ($rright['dashboard_col_subcat'])
        $excelRow[] = $resultscat['name'];
    if ($rright['dashboard_col_asset'])
        $excelRow[] = $resultasset['netbios'];
    if ($rparameters['ticket_places'])
        $excelRow[] = T_($nameplace);
    if ($rright['dashboard_col_service'])
        $excelRow[] = T_($nameservice);
    if ($rright['dashboard_col_agency'])
        $excelRow[] = T_($nameagency);
    $excelRow[] = $row['title'];
    if ($rright['dashboard_col_date_create'])
        $excelRow[] = $rowdate_create;
    $excelRow[] = $rowdate_start;
    $excelRow[] = $rowdate_return;
    $excelRow[] = strval($days_diff);
    if ($rright['dashboard_col_date_hope'])
        $excelRow[] = $rowdate_hope;
    if ($rright['dashboard_col_date_res']) {
        $excelRow[] = $rowdate_res;
    }
    if ($rright['dashboard_col_time'])
        $excelRow[] = MinToHour($row['time']);
    $excelRow[] = T_($resultstate['name']);
    if ($rright['dashboard_col_priority'])
        $excelRow[] = T_($resultpriority['name']);
    if ($rright['dashboard_col_criticality'])
        $excelRow[] = T_($resultcriticality['name']);
    $excelData[] = $excelRow;
}

// Create a new Excel spreadsheet
$spreadSheet = new Spreadsheet();
$spreadSheet->removeSheetByIndex(0);

// Create a new worksheet and add it to the spreadsheet
$worksheet = new Worksheet($spreadSheet,"Données");
$spreadSheet->addSheet($worksheet,0);

// Add the data to the worksheet
$worksheet->fromArray($excelData);

// Set the font of the first row to bold
$worksheet->getStyle('1')->getFont()->setBold(true);

// Calculate the width of the columns
$worksheet->calculateColumnWidths();

/**
 * Set the width of each column to automatically adjust to the maximum length of the data in it.
 * This is done using the getColumnDimension() method to get the column's dimension object,
 * and then calling the setAutoSize() method on that object with a parameter of true.
 */
foreach ($worksheet->getColumnIterator() as $column) {
    $worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
}

// Save the spreadsheet to the output
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadSheet);
$writer->save('php://output');
?>