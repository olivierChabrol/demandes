<?php

$db_id=strip_tags($_GET['id']);

$qry=$db->prepare("SELECT * FROM `tincidents` WHERE `id`=:id");
$qry->execute(array('id' => $db_id));
$globalrow=$qry->fetch();
$qry->closeCursor();

//on récupére les infos d'état du ticket
$qry=$db->prepare("SELECT `name` FROM `tstates` WHERE id=:id");
$qry->execute(array('id' => $globalrow['state']));
$staterow=$qry->fetch();
$qry->closeCursor();

//on récupére les infos de catégorie du ticket
$qry=$db->prepare("SELECT `name` FROM `tcategory` WHERE id=:id");
$qry->execute(array('id' => $_POST['category']));
$catrow=$qry->fetch();
$qry->closeCursor();

$qry=$db->prepare("SELECT `name` FROM `tsubcat` WHERE id=:id");
$qry->execute(array('id' => $_POST['subcat']));
$subcatrow=$qry->fetch();
$qry->closeCursor();

$technician_services='';
$qry=$db->prepare("SELECT `name` FROM `tservices`,`tusers_services` WHERE `tservices`.`id`=`tusers_services`.`service_id` AND `tusers_services`.`user_id`=:user_id");
$qry->execute(array('user_id' => $globalrow['technician']));
while($row=$qry->fetch()) {$technician_services.=$row['name'].' ';}
$qry->closeCursor();

$technician_service='';
$qry=$db->prepare("SELECT `name` FROM `tservices`,`tusers_services` WHERE `tservices`.`id`=`tusers_services`.`service_id` AND `tusers_services`.`user_id`=:user_id ORDER BY `tusers_services`.`id` LIMIT 1");
$qry->execute(array('user_id' => $globalrow['technician']));
while($row=$qry->fetch()) {$technician_service=$row['name'];}
$qry->closeCursor();

$qry=$db->prepare("SELECT `name` FROM `tcompany` WHERE disable='0' AND id=:id");
$qry->execute(array('id' => $userrow['company']));
$companyrow=$qry->fetch();
$qry->closeCursor();

$placerow = null;
//case place parameter
if($rparameters['ticket_places']==1)
{
	$qry=$db->prepare("SELECT `id`,`name` FROM `tplaces` WHERE id=:id");
	$qry->execute(array('id' => $globalrow['place']));
	$placerow=$qry->fetch();
	$qry->closeCursor();
}

$qry=$db->prepare("SELECT `name` FROM `tpriority` WHERE id=:id");
$qry->execute(array('id' => $globalrow['priority']));
$priorityrow=$qry->fetch();
$qry->closeCursor();

$qry=$db->prepare("SELECT `name` FROM `tcriticality` WHERE id=:id");
$qry->execute(array('id' => $globalrow['criticality']));
$criticalityrow=$qry->fetch();
$qry->closeCursor();

//generate resolution
if($rparameters['mail_order']==1) {
	$qry=$db->prepare("SELECT * FROM `tthreads` WHERE ticket=:ticket AND private=0 ORDER BY date DESC");
} else {
	$qry=$db->prepare("SELECT * FROM `tthreads` WHERE ticket=:ticket AND private=0 ORDER BY date ASC");
}
$qry->execute(array('ticket' => $db_id));
$datePrevious = null;
$date = null;
$lastComment = '';
while($row=$qry->fetch())
{
	//remove display date from old post
	$find_old=explode(" ", $row['date']);
	$find_old=$find_old[1];
	if($find_old!='12:00:00') $date_thread=date_convert($row['date']); else $date_thread='';
    $date = new DateTime($row['date']);

	if($row['type']==0)
	{
		//text back-line format
		//$text=nl2br($row['text']); #5129
		$text=$row['text'];

		//test if author is not the technician
		if($row['author']!=$globalrow['technician'])
		{
			//find author name
			$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
			$qry2->execute(array('id' => $row['author']));
			$rauthor=$qry2->fetch();
			$qry2->closeCursor();

            $resolution="$resolution <b> $date_thread $rauthor[firstname] $rauthor[lastname] : </b><br /> $text  <hr />";
            if ($date >= $datePrevious) {
                $lastComment="<b> $date_thread $rauthor[firstname] $rauthor[lastname] : </b><br /> $text  <hr />";
                $datePrevious = $date;
            }
		} else {
			if($date_thread!='')
			{
                $resolution="$resolution <b>$date_thread :</b><br />$text<hr />";
                if ($date >= $datePrevious) {
                    $lastComment = "<b>$date_thread :</b><br />$text<hr />";
                    $datePrevious = $date;
                }
			} else {
                $resolution="$resolution  $text <hr />";
                if ($date >= $datePrevious) {
                    $lastComment = "$text <hr />";
                    $datePrevious = $date;
                }
			}
		}
	} elseif($row['type']==1) {
		//generate attribution thread
		if($row['group1']!=0)
		{
			$qry2=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
			$qry2->execute(array('id' => $row['group1']));
			$rtechgroup=$qry2->fetch();
			$qry2->closeCursor();

            $resolution=$resolution.' <b>'.$date_thread.' :</b> '.T_('Attribution du ticket au groupe').' '.$rtechgroup['name'].'.<br /><br />';
            if ($date >= $datePrevious) {
                $lastComment = ' <b>' . $date_thread . ' :</b> ' . T_('Attribution du ticket au groupe') . ' ' . $rtechgroup['name'] . '.<br /><br />';
                $datePrevious = $date;
            }
		} else {
			$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
			$qry2->execute(array('id' => $row['tech1']));
			$rtech3=$qry2->fetch();
			$qry2->closeCursor();

            $resolution=$resolution.' <b>'.$date_thread.' :</b> '.T_('Attribution du ticket à').' '.$rtech3['firstname'].' '.$rtech3['lastname'].'.<br /><br />';
            if ($date >= $datePrevious) {
                $lastComment = ' <b>' . $date_thread . ' :</b> ' . T_('Attribution du ticket à') . ' ' . $rtech3['firstname'] . ' ' . $rtech3['lastname'] . '.<br /><br />';
                $datePrevious = $date;
            }
		}
	} elseif($row['type']==2) {
		//generate transfert thread
		if($row['group1']!=0 && $row['group2']!=0) //case group to group
		{
			$qry2=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
			$qry2->execute(array('id' => $row['group1']));
			$rtechgroup1=$qry2->fetch();
			$qry2->closeCursor();

			$qry2=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
			$qry2->execute(array('id' => $row['group2']));
			$rtechgroup2=$qry2->fetch();
			$qry2->closeCursor();

            $resolution=$resolution.' <b>'.$date_thread.' :</b> '.T_('Transfert du ticket du groupe').' '.$rtechgroup1['name'].' '.T_('au groupe ').' '.$rtechgroup2['name'].'. <br /><br />';
            if ($date >= $datePrevious) {
                $lastComment = ' <b>' . $date_thread . ' :</b> ' . T_('Transfert du ticket du groupe') . ' ' . $rtechgroup1['name'] . ' ' . T_('au groupe ') . ' ' . $rtechgroup2['name'] . '. <br /><br />';
                $datePrevious = $date;
            }
		} elseif(($row['tech1']==0 || $row['tech2']==0) && ($row['group1']==0 || $row['group2']==0)) { //case group to tech
			if($row['tech1']!=0) {
				$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
				$qry2->execute(array('id' => $row['tech1']));
				$rtech4=$qry2->fetch();
				$qry2->closeCursor();
			}
			if($row['tech2']!=0) {
				$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
				$qry2->execute(array('id' => $row['tech2']));
				$rtech5=$qry2->fetch();
				$qry2->closeCursor();
			}
			if($row['group1']!=0) {
				$qry2=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
				$qry2->execute(array('id' => $row['group1']));
				$rtechgroup4=$qry2->fetch();
				$qry2->closeCursor();
			}
			if($row['group2']!=0) {
				$qry2=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
				$qry2->execute(array('id' => $row['group2']));
				$rtechgroup5=$qry2->fetch();
				$qry2->closeCursor();
			}
            $resolution=$resolution.' <b>'.$date_thread.' :</b> '.T_('Transfert du ticket de').' '.$rtechgroup4['name'].$rtech4['firstname'].' '.$rtech4['lastname'].' '.T_('à').' '.$rtechgroup5['name'].$rtech5['firstname'].' '.$rtech5['lastname'].'. <br /><br />';
            if ($date >= $datePrevious) {
                $lastComment = ' <b>' . $date_thread . ' :</b> ' . T_('Transfert du ticket de') . ' ' . $rtechgroup4['name'] . $rtech4['firstname'] . ' ' . $rtech4['lastname'] . ' ' . T_('à') . ' ' . $rtechgroup5['name'] . $rtech5['firstname'] . ' ' . $rtech5['lastname'] . '. <br /><br />';
                $datePrevious = $date;
            }
	} elseif($row['tech1']!=0 && $row['tech2']!=0) { //case tech to tech
			$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
			$qry2->execute(array('id' => $row['tech1']));
			$rtech1=$qry2->fetch();
			$qry2->closeCursor();

			$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
			$qry2->execute(array('id' => $row['tech2']));
			$rtech2=$qry2->fetch();
			$qry2->closeCursor();

            $resolution=$resolution.' <b>'.$date_thread.' :</b> '.T_('Transfert du ticket de').' '.$rtech1['firstname'].' '.$rtech1['lastname'].' '.T_('à').' '.$rtech2['firstname'].' '.$rtech2['lastname'].'. <br /><br />';
            if ($date >= $datePrevious) {
                $lastComment = ' <b>' . $date_thread . ' :</b> ' . T_('Transfert du ticket de') . ' ' . $rtech1['firstname'] . ' ' . $rtech1['lastname'] . ' ' . T_('à') . ' ' . $rtech2['firstname'] . ' ' . $rtech2['lastname'] . '. <br /><br />';
                $datePrevious = $date;
            }
		}
	}
}
$qry->closeCursor();

$description = $globalrow['description'];

//date conversion
$date_create = date_cnv("$globalrow[date_create]");
$date_hope = date_cnv("$globalrow[date_hope]");
$date_res = date_cnv("$globalrow[date_res]");

if($date_create=='00/00/0000') {$date_create='';}
if($date_hope=='00/00/0000') {$date_hope='';}
if($date_res=='00/00/0000') {$date_res='';}

    //display custom end text mail, else auto generate
    if($rparameters['mail_txt_end'])
    {
        //generate mail end text
        $mail_text_end=str_replace("[tech_name]", "$techrow[firstname] $techrow[lastname]", $rparameters['mail_txt_end']);
        $mail_text_end=str_replace("[tech_phone]", "$techrow[phone]", $mail_text_end);
        if($rparameters['mail_link'] && $rparameters['server_url']) {
            $link='<a href="'.$rparameters['server_url'].'/index.php?page=ticket&id='.$_GET['id'].'">'.$rparameters['server_url'].'/index.php?page=ticket&id='.$_GET['id'].'</a>';
            $mail_text_end=str_replace("[link]", "$link", $mail_text_end);
        }
    } else { //auto end mail
        if($rparameters['mail_link'] && $rparameters['server_url']) //integer link parameter
        {
            $link=', '.T_('ou consultez votre ticket sur ce lien').' : <a href="'.$rparameters['server_url'].'/index.php?page=ticket&id='.$_GET['id'].'">'.$rparameters['server_url'].'/index.php?page=ticket&id='.$_GET['id'].'</a>';
        } else $link=".";
        if(($techrow['lastname']!='Aucun') && ($techrow['phone']!='')) //case technician phone
        {$mail_text_end=T_('Pour toutes informations complémentaires sur votre ticket, vous pouvez joindre').' '.$techrow['firstname'].' '.$techrow['lastname'].' '.T_('au').' '.$techrow['phone'].' '.$link;}
        elseif($rparameters['mail_link']==1) //case technician no phone
        {$mail_text_end=T_("Vous pouvez suivre l'état d'avancement de votre ticket sur ce lien : ").'<a href="'.$rparameters['server_url'].'/index.php?page=ticket&id='.$_GET['id'].'">'.$rparameters['server_url'].'/index.php?page=ticket&id='.$_GET['id'].'</a>';}
    }

#template filename definition
$template_filename=__DIR__.'/../'.'template/mail/'.$rparameters['mail_template'];

if(file_exists($template_filename))
{
	//load template
	$mail_template=file_get_contents($template_filename);

	//translate none values
	if($userrow['firstname']=='Aucun') {$userrow['firstname']=T_('Aucun');}
	if($userrow['lastname']=='Aucun') {$userrow['lastname']=T_('Aucun');}
	if($techrow['firstname']=='Aucun') {$techrow['firstname']=T_('Aucun');}
	if($techrow['lastname']=='Aucun') {$techrow['lastname']=T_('Aucun');}
	if($catrow['name']=='Aucune') {$catrow['name']=T_('Aucune');}
	if($subcatrow['name']=='Aucune') {$subcatrow['name']=T_('Aucune');}

	//replace mail tag
	$mail_template=str_replace('#mail_color_title#', $rparameters['mail_color_title'], $mail_template);
	$mail_template=str_replace('#mail_color_text#', $rparameters['mail_color_text'], $mail_template);
	$mail_template=str_replace('#mail_object#', $object, $mail_template);
	$mail_template=str_replace('#mail_txt#', T_($rparameters['mail_txt']), $mail_template);
	$mail_template=str_replace('#mail_txt_end#', $mail_text_end, $mail_template);
	$mail_template=str_replace('#mail_color_title#', $rparameters['mail_color_title'], $mail_template);
	$mail_template=str_replace('#mail_color_text#', $rparameters['mail_color_text'], $mail_template);
	$mail_template=str_replace('#mail_color_bg#', $rparameters['mail_color_bg'], $mail_template);

	//translate field name
	$mail_template=str_replace('#title#', T_('Titre'), $mail_template);
	$mail_template=str_replace('#category#', T_('Catégorie'), $mail_template);
	$mail_template=str_replace('#user#', T_('Demandeur'), $mail_template);
	$mail_template=str_replace('#technician#', T_('Technicien'), $mail_template);
	$mail_template=str_replace('#state#', T_('État'), $mail_template);
	$mail_template=str_replace('#place#', T_('Lieu'), $mail_template);
	$mail_template=str_replace('#date_create#', T_('Date de la demande'), $mail_template);
	$mail_template=str_replace('#description#', T_('Description'), $mail_template);
    $mail_template=str_replace('#resolution#', T_('Résolution'), $mail_template);
    $mail_template=str_replace('#last_comment#', T_('Dernier commentaire'), $mail_template);
	$mail_template=str_replace('#date_hope#', T_('Date de résolution estimée'), $mail_template);
	$mail_template=str_replace('#date_res#', T_('Date de résolution'), $mail_template);
	$mail_template=str_replace('#company#', T_('Société'), $mail_template);

	//replace ticket tag
	$mail_template=str_replace('#ticket_id#', $globalrow['id'], $mail_template);
	$mail_template=str_replace('#ticket_title#', $globalrow['title'], $mail_template);
	$mail_template=str_replace('#ticket_category#', $catrow['name'], $mail_template);
	$mail_template=str_replace('#ticket_subcat#', $subcatrow['name'], $mail_template);
	if($globalrow['u_group']) {$mail_template=str_replace('#ticket_user#', $groupuser['name'], $mail_template);} else {$mail_template=str_replace('#ticket_user#', $userrow['firstname'].' '.strtoupper($userrow['lastname']), $mail_template);}
	if($globalrow['t_group']) {$mail_template=str_replace('#ticket_technician#', $grouptech['name'], $mail_template);} else {$mail_template=str_replace('#ticket_technician#', $techrow['firstname'].' '.strtoupper($techrow['lastname']), $mail_template);}
	if(!$staterow) {
  	  $mail_template=str_replace('#ticket_state#', T_($staterow['name']), $mail_template);
	}
	$mail_template=str_replace('#ticket_priority#', $priorityrow['name'], $mail_template);
	$mail_template=str_replace('#ticket_criticality#', $criticalityrow['name'], $mail_template);
	if($$placerow != null && isset($placerow['name'])) {
		$mail_template=str_replace('#ticket_place#', $placerow['name'], $mail_template);
	}
	$mail_template=str_replace('#ticket_date_create#', $date_create, $mail_template);
	$mail_template=str_replace('#ticket_description#', $description, $mail_template);
    $mail_template=str_replace('#ticket_resolution#', $resolution, $mail_template);
    $mail_template=str_replace('#ticket_last_comment#', $lastComment, $mail_template);
	$mail_template=str_replace('#ticket_date_hope#', $date_hope, $mail_template);
	$mail_template=str_replace('#ticket_date_res#', $date_res, $mail_template);
	$mail_template=str_replace('#ticket_company#', $companyrow['name'], $mail_template);
	$mail_template=str_replace('#company_logo#', "$rparameters[server_url]/upload/logo/$rparameters[logo]", $mail_template);
	$mail_template=str_replace('#ticket_technician_phone#', $techrow['phone'], $mail_template);
	$mail_template=str_replace('#ticket_technician_mobile#', $techrow['mobile'], $mail_template);
	$mail_template=str_replace('#ticket_technician_custom1#', $techrow['custom1'], $mail_template);
	$mail_template=str_replace('#ticket_technician_custom2#', $techrow['custom2'], $mail_template);
	$mail_template=str_replace('#ticket_technician_mail#', $techrow['mail'], $mail_template);
	$mail_template=str_replace('#ticket_technician_function#', $techrow['function'], $mail_template);
	$mail_template=str_replace('#ticket_technician_services#', $technician_services, $mail_template);
	$mail_template=str_replace('#ticket_technician_service#', $technician_service, $mail_template);

	$message.=$mail_template;
} else {
	echo 'ERROR : unable to find mail template, check your /template/mail directory';
}
?>
