<?php
/**
 * This script is used to generate a CSV file for the dashboard.
 *
 * It first sets the filename for the CSV file and sends headers to the browser to initiate file download.
 * Then, it creates an array to hold the rows of the CSV file.
 * Depending on the user's profile and the current settings, it adds different columns to the CSV file.
 * Finally, it writes the constructed row to the CSV file and executes a SQL query to fetch data from the database.
 * The fetched data is then processed and written to the CSV file.
 *
 */
$filename = 'donnees.csv';

// Indicate to the browser that the content is a CSV file to download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Initialize an array to hold the rows of the CSV file
$csvrow = array();

// Add columns to the CSV file based on user's profile and settings
$csvrow[] = T_('Numéro');
if ($_SESSION['profile_id'] != 0 || $_SESSION['profile_id'] != 4 || $_GET['userid'] == '%')
    $csvrow[] = T_('Gestionnaire');
if (($_SESSION['profile_id'] == 0 || $_SESSION['profile_id'] == 1 || $_SESSION['profile_id'] == 2 || $_SESSION['profile_id'] == 3 || $_SESSION['profile_id'] == 4) || ($rright['side_all'] && ($_GET['userid'] == '%' || $keywords != '')) || ($rparameters['user_company_view'] != 0 && $_GET['userid'] == '%' && ($rright['side_company'] || $keywords != '')))
    $csvrow[] = T_('Demandeur');
if ($rright['dashboard_col_user_service'])
    $csvrow[] = T_('Service du demandeur');
if ($rright['dashboard_col_type'])
    $csvrow[] = T_('Type');
if ($rright['dashboard_col_category'])
    $csvrow[] = T_('Catégorie');
if ($rright['dashboard_col_subcat'])
    $csvrow[] = T_('Financement');
if ($rright['dashboard_col_asset'])
    $csvrow[] = T_('Équipement');
if ($rparameters['ticket_places'] == 1)
    $csvrow[] = T_('Lieu');
if ($rright['dashboard_col_service'])
    $csvrow[] = T_('Service');
$csvrow[] = T_('Intitulé de la demande');
if ($rright['dashboard_col_date_create'])
    $csvrow[] = T_('Date demande');
if ($rright['dashboard_col_date_hope'])
    $csvrow[] = T_('Date de résolution estimée');
if ($rright['dashboard_col_date_res'])
    $csvrow[] = T_('Date de résolution');
if ($rright['dashboard_col_time'])
    $csvrow[] = T_('Temps passé');
$csvrow[] = T_('Date début');
$csvrow[] = ($_GET['view'] == 'activity') ? T_('État actuel') : T_('État');
if ($rright['dashboard_col_priority'])
    $csvrow[] = T_('Priorité');
if ($rright['dashboard_col_criticality'])
    $csvrow[] = T_('Criticité');

// Open a new CSV file for writing
$fp = fopen('php://output', 'w');

// Write the constructed row to the CSV file
fputcsv($fp, $csvrow);

// Execute a SQL query to fetch data from the database
$masterquery = $db->query($_POST['query']);

// Process the fetched data and write it to the CSV file
while ($row = $masterquery->fetch()) {
    //select name of states
    $csvrow = array();
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
        if ($row['om_for_guest']) {
            $resultcat['name'] = T_('Invitation');
        } elseif ($row['category'] == 0) {
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
    $start_date_query = $db->prepare("SELECT date_start from dmission_order WHERE incident_id=" . $row['id']);
    $start_date_query->execute();
    $result_date_query = $start_date_query->fetch();
    $start_date_query->closeCursor();

    $rowdate_start = date_create($result_date_query[0]);
    $rowdate_start = date_format($rowdate_start, 'd/m/Y');
    if ($rright['dashboard_col_date_create_hour']) //display hour in create date column
    {
        $rowdate_create = date_create($row['date_create']);
        $rowdate_create = date_format($rowdate_create, 'd/m/Y');
    } else {
        $rowdate_create = date_cnv($row['date_create']);
    }


    $csvrow[] = $row['id'];
    if ($_SESSION['profile_id'] != 0 || $_SESSION['profile_id'] != 4 || $_GET['userid'] == '%')
        $csvrow[] = $resulttech['firstname'] . ' ' . $resulttech['lastname'];
    if (($_SESSION['profile_id'] == 0 || $_SESSION['profile_id'] == 1 || $_SESSION['profile_id'] == 2 || $_SESSION['profile_id'] == 3 || $_SESSION['profile_id'] == 4) || ($rright['side_all'] && ($_GET['userid'] == '%' || $keywords != '')) || ($rparameters['user_company_view'] != 0 && $_GET['userid'] == '%' && ($rright['side_company'] || $keywords != '')))
        $csvrow[] = $resultuser['firstname'] . ' ' . $resultuser['lastname'];
    if ($rright['dashboard_col_user_service'])
        $csvrow[] = T_($name_sender_service);
    if ($rright['dashboard_col_type'])
        $csvrow[] = T_($resulttype['name']);
    if ($rright['dashboard_col_category'])
        $csvrow[] = $resultcat['name'];
    if ($rright['dashboard_col_subcat'])
        $csvrow[] = $resultscat['name'];
    if ($rright['dashboard_col_asset'])
        $csvrow[] = $resultasset['netbios'];
    if ($rparameters['ticket_places'])
        $csvrow[] = T_($nameplace);
    if ($rright['dashboard_col_service'])
        $csvrow[] = T_($nameservice);
    if ($rright['dashboard_col_agency'])
        $csvrow[] = T_($nameagency);
    $csvrow[] = $row['title'];
    if ($rright['dashboard_col_date_create'])
        $csvrow[] = $rowdate_create;
    $csvrow[] = $rowdate_start;
    if ($rright['dashboard_col_date_hope'])
        $csvrow[] = $rowdate_hope;
    if ($rright['dashboard_col_date_res'])
        $csvrow[] = $rowdate_res;
    if ($rright['dashboard_col_time'])
        $csvrow[] = MinToHour($row['time']);
    $csvrow[] = T_($resultstate['name']);
    if ($rright['dashboard_col_priority'])
        $csvrow[] = T_($resultpriority['name']);
    if ($rright['dashboard_col_criticality'])
        $csvrow[] = T_($resultcriticality['name']);
    fputcsv($fp, $csvrow);
}

// Close the CSV file
fclose($fp);
?>