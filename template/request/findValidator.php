<?php
	require_once('../../connect.php');
       // ---------- Recuperation des Infos de la ligne d'appel ----------------
       if ( isset ($_GET["idSubCat"]) ) { $idSubCat = $_GET["idSubCat"]; } else { $idSubCat="ERREUR"; }

       // ----------------- Interrogation de la BdD -------
	$qry = $db->prepare('SELECT id_tuser FROM vprojets WHERE id_tsubcat=:idSubCat');
	$qry->execute(array(':idSubCat' => $idSubCat));
	$i=0;
	$retourJason="{\"nom\":[";
	$retourOK="";

	while( $resultat = $qry->fetch() )
	{
		$retourJason=$retourJason."\"$resultat[0]\",";
		$i++;
       }
	$retourJason=$retourJason." ],";
	$retourJason=$retourJason." \"nbPoints\": [{\"nb\":\"$i\"} ]}";
	$retourOK=str_replace(", ]","]",$retourJason);
	echo $retourOK;
	error_log($retourOK);
	error_log("Ending Retour Jason - Loaded $i validators");
       // ----------------------------------------------------------------------
  ?>
