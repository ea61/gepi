<?php
/* $Id$ */
/*
* Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* GEPI is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with GEPI; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");


// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}



$sql="SELECT 1=1 FROM droits WHERE id='/mod_epreuve_blanche/index.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_epreuve_blanche/index.php',
administrateur='V',
professeur='V',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Epreuve blanche: Accueil',
statut='';";
$insert=mysql_query($sql);
}




//======================================================================================
// Section checkAccess() � d�commenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

include('lib_eb.php');

//=========================================================

// Cr�ation des tables

$sql="CREATE TABLE IF NOT EXISTS eb_epreuves (
id int(11) unsigned NOT NULL auto_increment,
intitule VARCHAR( 255 ) NOT NULL ,
description TEXT NOT NULL ,
type_anonymat VARCHAR( 255 ) NOT NULL ,
date DATE NOT NULL default '0000-00-00',
etat VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( id )
);";
$create_table=mysql_query($sql);

$sql="CREATE TABLE IF NOT EXISTS eb_copies (
id int(11) unsigned NOT NULL auto_increment,
login_ele VARCHAR( 255 ) NOT NULL ,
n_anonymat VARCHAR( 255 ) NOT NULL,
id_salle INT( 11 ) NOT NULL default '-1',
login_prof VARCHAR( 255 ) NOT NULL ,
note float(10,1) NOT NULL default '0.0',
statut VARCHAR(255) NOT NULL default '',
id_epreuve int(11) unsigned NOT NULL,
PRIMARY KEY ( id )
);";
$create_table=mysql_query($sql);

$sql="CREATE TABLE IF NOT EXISTS eb_salles (
id int(11) unsigned NOT NULL auto_increment,
salle VARCHAR( 255 ) NOT NULL ,
id_epreuve int(11) unsigned NOT NULL,
PRIMARY KEY ( id )
);";
$create_table=mysql_query($sql);

$sql="CREATE TABLE IF NOT EXISTS eb_groupes (
id int(11) unsigned NOT NULL auto_increment,
id_epreuve int(11) unsigned NOT NULL,
id_groupe int(11) unsigned NOT NULL,
transfert varchar(1) NOT NULL DEFAULT 'n',
PRIMARY KEY ( id )
);";
//echo "$sql<br />";
$create_table=mysql_query($sql);

$sql="CREATE TABLE IF NOT EXISTS eb_profs (
id int(11) unsigned NOT NULL auto_increment,
id_epreuve int(11) unsigned NOT NULL,
login_prof VARCHAR(255) NOT NULL default '',
PRIMARY KEY ( id )
);";
//echo "$sql<br />";
$create_table=mysql_query($sql);

//=========================================================

$id_epreuve=isset($_POST['id_epreuve']) ? $_POST['id_epreuve'] : (isset($_GET['id_epreuve']) ? $_GET['id_epreuve'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

//$modif_epreuve=isset($_POST['modif_epreuve']) ? $_POST['modif_epreuve'] : (isset($_GET['modif_epreuve']) ? $_GET['modif_epreuve'] : NULL);

if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {

	if(isset($id_epreuve)) {
		$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			$msg="L'�preuve choisie (<i>$id_epreuve</i>) n'existe pas.\n";
		}
		else {
			$lig=mysql_fetch_object($res);
			$etat=$lig->etat;
		
			if($etat=='clos') {
				/*
				if((isset($mode))&&($mode=='declore')) {

				}
				*/
				if(isset($_POST['modif_epreuve'])) {unset($_POST['modif_epreuve']);}
				if((isset($mode))&&($mode!='clore')&&($mode!='declore')&&($mode!='modif_epreuve')) {$mode=NULL;}
			}
		}
	}

	// T�moin d'une modification de num�ros anonymat (pour informer qu'il faut reg�n�rer les �tiquettes,...)
	$temoin_n_anonymat='n';
	// T�moin d'une erreur anonymat pour un �l�ve au moins
	$temoin_erreur_n_anonymat='n';

	//if(isset($_POST['creer_epreuve'])) {
	if((isset($_POST['creer_epreuve']))||(isset($_POST['modif_epreuve']))) {
		// Correction, modification des param�tres d'une �preuve

		$intitule=isset($_POST['intitule']) ? $_POST['intitule'] : "Epreuve blanche";
		$date=isset($_POST['date']) ? $_POST['date'] : "";
		$description=isset($_POST['description']) ? $_POST['description'] : "";
		$type_anonymat=isset($_POST['type_anonymat']) ? $_POST['type_anonymat'] : "ele_id";

		if(strlen(ereg_replace("[A-Za-z _.-]","",remplace_accents($intitule,'all')))!=0) {$intitule=ereg_replace("[^A-Za-z���������������������զ����ݾ�������������������������������_.-]"," ",$intitule);}
		if($intitule=="") {$intitule="Epreuve blanche";}

		$tab_anonymat=array('elenoet','ele_id','no_gep','alea');
		if(!in_array($type_anonymat,$tab_anonymat)) {$type_anonymat="ele_id";}

		if (isset($NON_PROTECT["description"])){
			$description=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["description"]));
		}
		else {
			$description="";
		}

		$tab=explode("/",$date);
		if(checkdate($tab[1],$tab[0],$tab[2])) {
			$date=$tab[2]."-".$tab[1]."-".$tab[0];
		}
		else {
			$date="0000-00-00";
		}

		if(!isset($id_epreuve)) {
			//$sql="INSERT INTO eb_epreuves SET intitule='$intitule', description='".addslashes($description)."', type_anonymat='$type_anonymat', date='', etat='';";
			$sql="INSERT INTO eb_epreuves SET intitule='$intitule', description='$description', type_anonymat='$type_anonymat', date='$date', etat='';";
			if($insert=mysql_query($sql)) {
				$id_epreuve=mysql_insert_id();
				$msg="Epreuve n�$id_epreuve : '$intitule' cr��e.<br />";
			}
			else {
				$msg="ERREUR lors de la cr�ation de l'�preuve '$intitule'.<br />";
				//$msg.="<br />$sql";
			}
		}
		else {
			//********************************************
			// A FAIRE: POUVOIR INTERDIRE OU ALERTER SUR LA MODIFICATION DU TYPE_ANONYMAT UNE FOIS LES LISTINGS/ETIQUETTES GENERES
			//********************************************

			$sql="SELECT type_anonymat FROM eb_epreuves WHERE id='$id_epreuve';";
			$res=mysql_query($sql);
			$lig=mysql_fetch_object($res);
			$old_type_anonymat=$lig->type_anonymat;

			$sql="UPDATE eb_epreuves SET intitule='$intitule', description='$description', type_anonymat='$type_anonymat', date='$date' WHERE id='$id_epreuve';";
			if($update=mysql_query($sql)) {
				$msg="Epreuve n�$id_epreuve : '$intitule' mise � jour.";

				if($type_anonymat!=$old_type_anonymat) {
					$tab_n_anonymat_affectes=array();

					// On commence par vider les num�ros d'anonymat avant de refaire l'affectation
					$sql="UPDATE eb_copies SET n_anonymat='' WHERE id='$id_epreuve';";
					$nettoyage=mysql_query($sql);

					// Mettre � jour le type anonymat pour les copies d�j� inscrites
					$sql="SELECT e.* FROM eb_copies ec, eleves e WHERE ec.id_epreuve='$id_epreuve' AND ec.login_ele=e.login;";
					//echo "$sql<br />";
					$res=mysql_query($sql);
					while($lig=mysql_fetch_object($res)) {
						// T�moin d'une erreur anonymat pour l'�l�ve courant
						$temoin_erreur="n";
						if($type_anonymat=='alea') {
							$n_anonymat=chaine_alea(3,4);
							while(in_array($n_anonymat,$tab_n_anonymat_affectes)) {$n_anonymat=chaine_alea(3,4);}
							$tab_n_anonymat_affectes[]=$n_anonymat;
						}
						else {
							$n_anonymat=$lig->$type_anonymat;
							if(in_array($n_anonymat,$tab_n_anonymat_affectes)) {
								$msg.="Erreur: Le num�ro '$n_anonymat' de $lig->login est d�j� affect� � un autre �l�ve.<br />";
								$temoin_erreur="y";
								$temoin_erreur_n_anonymat="y";
							}
							$tab_n_anonymat_affectes[]=$n_anonymat;
						}

						if($temoin_erreur=="n") {
							$sql="UPDATE eb_copies SET n_anonymat='$n_anonymat' WHERE id_epreuve='$id_epreuve' AND login_ele='$lig->login';";
							$update=mysql_query($sql);
							if($update) {
								$temoin_n_anonymat='y';
							}
						}
					}
				}
			}
			else {
				$msg="ERREUR lors de la modification de l'�preuve '$intitule'.";
				//$msg.="<br />$sql";
			}
		}
		$mode="modif_epreuve";
	}
	elseif((isset($id_epreuve))&&($mode=='suppr_epreuve')) {
		// Suppression d'une �preuve
		//echo "gloups";
		//$tab_tables=array('eb_profs', 'eb_salles', 'eb_groupes', 'eb_copies', 'eb_epreuves');
		$tab_tables=array('eb_profs', 'eb_salles', 'eb_groupes', 'eb_copies');
		for($i=0;$i<count($tab_tables);$i++) {
			//$sql="DELETE FROM eb_epreuves WHERE id='$id_epreuve';";
			$sql="DELETE FROM $tab_tables[$i] WHERE id_exam='$id_exam';";
			$suppr=mysql_query($sql);
			if(!$suppr) {
				$msg="ERREUR lors de la suppression de l'�preuve $id_epreuve";
				//for($j=0;$j<$i;$j++) {$msg.=""}
				unset($id_epreuve);
				unset($mode);
				break;
			}
		}
		if($msg=='') {
			$sql="DELETE FROM eb_epreuves WHERE id='$id_epreuve';";
			$suppr=mysql_query($sql);
			if(!$suppr) {
				$msg="ERREUR lors de la suppression de l'�preuve $id_epreuve";
			}
			else {
				$msg="Suppression de l'�preuve $id_epreuve effectu�e.";
			}
		}
		unset($id_epreuve);
		unset($mode);
	}
	elseif((isset($id_epreuve))&&($mode=='ajout_groupes')) {
		// Ajout de groupes pour l'�preuve s�lectionn�e
		$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : array());

		$sql="DELETE FROM eb_groupes WHERE id_epreuve='$id_epreuve';";
		$suppr=mysql_query($sql);
		if(!$suppr) {
			$msg="ERREUR lors de la r�initialisation des groupes inscrits.";
		}
		else {
			$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				$msg="L'�preuve n�$id_epreuve n'existe pas.<br />";
			}
			else {
				$lig=mysql_fetch_object($res);
				$type_anonymat=$lig->type_anonymat;
				$tab_anonymat=array('elenoet','ele_id','no_gep','alea');
				if(!in_array($type_anonymat,$tab_anonymat)) {$type_anonymat="ele_id";}

				// On ne supprime que les enregistrements de copies pour lesquelles aucune note n'est encore saisie
				$sql="DELETE FROM eb_copies WHERE id_epreuve='$id_epreuve' AND statut='v';";
				$suppr=mysql_query($sql);

				$tab_n_anonymat_affectes=array();
				$sql="SELECT n_anonymat FROM eb_copies WHERE id_epreuve='$id_epreuve';";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {
					while($lig=mysql_fetch_object($res)) {
						$tab_n_anonymat_affectes[]=$lig->n_anonymat;
					}
				}

				$msg="";
				for($i=0;$i<count($id_groupe);$i++) {
					$sql="INSERT INTO eb_groupes SET id_epreuve='$id_epreuve', id_groupe='$id_groupe[$i]';";
					$insert=mysql_query($sql);
					if(!$insert) {
						$msg.="Erreur lors de l'ajout du groupe n�$id_groupe[$i]<br />";
					}

					if($type_anonymat=='alea') {
						$sql="SELECT DISTINCT login FROM j_eleves_groupes WHERE id_groupe='$id_groupe[$i]';";
					}
					else {
						$sql="SELECT DISTINCT j.login,e.$type_anonymat FROM j_eleves_groupes j, eleves e WHERE j.id_groupe='$id_groupe[$i]' AND j.login=e.login;";
					}
					// Il faudra voir comment g�rer le cas d'�l�ves partis en cours d'ann�e... faire choisir la p�riode?
					$res=mysql_query($sql);
					while($lig=mysql_fetch_object($res)) {
						$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND login_ele='$lig->login';";
						$test=mysql_query($sql);
						if(mysql_num_rows($test)==0) {
							if($type_anonymat=='alea') {
								$n_anonymat=chaine_alea(3,4);
								while(in_array($n_anonymat,$tab_n_anonymat_affectes)) {$n_anonymat=chaine_alea(3,4);}
								$tab_n_anonymat_affectes[]=$n_anonymat;
							}
							else {
								$n_anonymat=$lig->$type_anonymat;
								if(in_array($n_anonymat,$tab_n_anonymat_affectes)) {$msg.="Erreur: Le num�ro '$n_anonymat' de $lig->login est d�j� affect� � un autre �l�ve.<br />";}
								$tab_n_anonymat_affectes[]=$n_anonymat;
							}
							$sql="INSERT INTO eb_copies SET id_epreuve='$id_epreuve', login_ele='$lig->login', n_anonymat='$n_anonymat', statut='v';";
							$insert=mysql_query($sql);
	
							if(!$insert) {
								$msg.="Erreur lors de l'ajout de l'�l�ve $lig->login<br />";
							}
							else {
								$temoin_n_anonymat='y';
							}
						}
					}
				}
				if($msg=='') {$msg="Ajout de(s) groupe(s) effectu�.<br />";}
			}
		}
		$mode='modif_epreuve';
	}
	elseif((isset($id_epreuve))&&($mode=='suppr_groupe')) {
		// Ajout de groupes pour l'�preuve s�lectionn�e
		$id_groupe=isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL;

		if(isset($id_groupe)) {
			$sql="SELECT 1=1 FROM eb_copies ec, eb_groupes eg WHERE ec.id_epreuve='$id_epreuve' AND eg.id_epreuve='$id_epreuve' AND eg.id_groupe='$id_groupe' AND statut!='v';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==1) {
				$msg="Une note a d�j� �t� saisie pour une copie associ�e au groupe.";
			}
			elseif(mysql_num_rows($test)>1) {
				$msg=mysql_num_rows($test)." notes ont d�j� �t� saisies pour des copies associ�es au groupe.";
			}
			else {
				$sql="DELETE FROM eb_copies ec, eb_groupes eg WHERE ec.id_epreuve='$id_epreuve' AND eg.id_epreuve='$id_epreuve' AND eg.id_groupe='$id_groupe';";
				$suppr=mysql_query($sql);
				if(!$suppr) {
					$msg="ERREUR lors de la suppression des copies associ�es au groupe n�$id_groupe.";
				}
				else {
					$sql="DELETE FROM eb_groupes WHERE id_epreuve='$id_epreuve' AND id_groupe='$id_groupe';";
					$suppr=mysql_query($sql);
					if(!$suppr) {
						$msg="ERREUR lors de la suppression du groupe n�$id_groupe.";
					}
					else {
						$msg="Suppression du groupe n�$id_groupe effectu�e.";
					}
				}
			}
		}
		$mode='modif_epreuve';
	}
	elseif((isset($id_epreuve))&&($mode=='ajout_profs')) {
		// Ajout de groupes pour l'�preuve s�lectionn�e
		$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : (isset($_GET['login_prof']) ? $_GET['login_prof'] : array());

		$sql="DELETE FROM eb_profs WHERE id_epreuve='$id_epreuve';";
		$suppr=mysql_query($sql);
		if(!$suppr) {
			$msg="ERREUR lors de la r�initialisation des professeurs inscrits.";
		}
		else {
			$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				$msg="L'�preuve n�$id_epreuve n'existe pas.<br />";
			}
			else {
				$tab_profs_inscrits=array();
				$msg="";
				for($i=0;$i<count($login_prof);$i++) {
					// On peut s�lectionner plusieurs fois le m�me prof, mais il ne faut pas l'ins�rer plusieurs fois dans la table eb_profs
					if(!in_array($login_prof[$i],$tab_profs_inscrits)) {
						$tab_profs_inscrits[]=$login_prof[$i];
						$sql="INSERT INTO eb_profs SET id_epreuve='$id_epreuve', login_prof='$login_prof[$i]';";
						$insert=mysql_query($sql);
						if(!$insert) {
							$msg.="Erreur lors de l'ajout du professeur $login_prof[$i]<br />";
						}
					}
				}
				if(($msg=='')&&(count($login_prof)>0)) {$msg="Ajout de(s) professeur(s) effectu�.";}

				// V�rification:
				// A-t-on supprim� un prof qui �tait associ� � des copies?
				$sql="SELECT DISTINCT login_prof FROM eb_copies WHERE id_epreuve='$id_epreuve' AND login_prof!='';";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {
					//$tab_profs_associes_copies=array();
					while($lig=mysql_fetch_object($res)) {
						//$tab_profs_associes_copies
						if(!in_array($lig->login_prof,$tab_profs_inscrits)) {
							$sql="UPDATE eb_copies SET login_prof='' WHERE id_epreuve='$id_epreuve' AND login_prof='$lig->login_prof';";
							$update=mysql_query($sql);
							$msg.="Suppression de professeur(s) qui �tai(en)t associ�(s) � des copies.<br />";
						}
					}
				}
			}
		}
		$mode='modif_epreuve';
	}
	elseif((isset($id_epreuve))&&($mode=='clore')) {
		// Cloture d'une �preuve
		$sql="UPDATE eb_epreuves SET etat='clos' WHERE id='$id_epreuve';";
		$cloture=mysql_query($sql);
		if(!$cloture) {
			$msg="ERREUR lors de la cloture de l'�preuve $id_epreuve";
			unset($id_epreuve);
			unset($mode);
			break;
		}
		else {$msg="Cloture de l'�preuve n�$id_epreuve effectu�e.";}
		unset($id_epreuve);
		unset($mode);
	}
	elseif((isset($id_epreuve))&&($mode=='declore')) {
		// R�ouverture d'une �preuve
		$sql="UPDATE eb_epreuves SET etat='' WHERE id='$id_epreuve';";
		$cloture=mysql_query($sql);
		if(!$cloture) {
			$msg="ERREUR lors de la r�ouverture de l'�preuve $id_epreuve";
			unset($id_epreuve);
			unset($mode);
			break;
		}
		else {
			$msg="R�ouverture de l'�preuve n�$id_epreuve effectu�e.";
			$mode='modif_epreuve';
		}
	}


	if($temoin_erreur_n_anonymat=='y') {
		if(!isset($msg)) {$msg="";}
		$msg.="<br />Une ou des erreurs se sont produites sur l'anonymat.<br />Vous devriez contr�ler les num�ros anonymat.";
	}
	elseif($temoin_n_anonymat=='y') {
		if(!isset($msg)) {$msg="";}
		$msg.="<br />Des num�ros anonymat ont �t� modifi�s. Reg�n�rez si n�cessaire les �tiquettes/listes d'�margement.";
	}
}

/*
$truncate_tables=isset($_GET['truncate_tables']) ? $_GET['truncate_tables'] : NULL;
if($truncate_tables=='y') {
	$msg="<p>Nettoyage des tables G�n�se des classes... <font color='red'>A FAIRE</font></p>\n";
	$sql="TRUNCATE TABLE ...;";
	//$del=mysql_query($sql);
}
*/

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$themessage  = 'Des informations ont �t� modifi�es. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Epreuve blanche: Accueil";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

//echo "<div class='noprint'>\n";
//echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
echo "<p class='bold'><a href='../accueil.php'>Accueil</a>";
//echo "</p>\n";
//echo "</div>\n";

include("../lib/calendrier/calendrier.class.php");


if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {
	if(!isset($id_epreuve)) {

		//echo "<h2>Epreuve blanche</h2>\n";
		//echo "<blockquote>\n";

		if(!isset($mode)) {
			echo "</p>\n";

			echo "<ul>\n";
			// Cr�er une �preuve blanche
			echo "<li>\n";
			echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=creer_epreuve'>Cr�er une nouvelle �preuve</a></p>\n";
			echo "</li>\n";

			// Acc�der aux �preuves blanches: closes ou non
			$sql="SELECT * FROM eb_epreuves WHERE etat!='clos' ORDER BY date, intitule;";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				echo "<li>\n";
				echo "<p><b>Epreuves en cours&nbsp;:</b><br />\n";
				while($lig=mysql_fetch_object($res)) {
					//echo "Modifier <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$lig->id&amp;modif_epreuve=y'";
					echo "Modifier <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$lig->id&amp;mode=modif_epreuve'";
					if($lig->description!='') {
						echo " onmouseover=\"delais_afficher_div('div_epreuve_".$lig->id."','y',-100,20,1000,20,20)\" onmouseout=\"cacher_div('div_epreuve_".$lig->id."')\"";

						$titre="Epreuve n�$lig->id";
						$texte="<p><b>".$lig->intitule."</b><br />";
						$texte.=$lig->description;
						$tabdiv_infobulle[]=creer_div_infobulle('div_epreuve_'.$lig->id,$titre,"",$texte,"",30,0,'y','y','n','n');

					}
					echo ">$lig->intitule</a> (<i>".formate_date($lig->date)."</i>)";
					echo " - <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$lig->id&amp;mode=suppr_epreuve' onclick=\"return confirm('Etes vous s�r de vouloir supprimer l �preuve?')\">Supprimer</a><br />\n";
				}
				echo "</li>\n";
			}
			// Pouvoir consulter/modifier:
			// - etat: clos ou non
			// - date
			// - intitule
			// - description
			// - liste des classes, groupes, profs
			// - Affecter les copies aux profs...

			$sql="SELECT * FROM eb_epreuves WHERE etat='clos' ORDER BY date, intitule;";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				echo "<li>\n";
				echo "<p><b>Epreuves closes&nbsp;:</b><br />\n";
				while($lig=mysql_fetch_object($res)) {
					echo "Epreuve $lig->intitule(<i>".formate_date($lig->date)."</i>)\n";

					echo " - <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$lig->id&amp;mode=modif_epreuve'>Consulter</a>\n";

					echo " - <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$lig->id&amp;mode=declore' onclick=\"return confirm('Etes vous s�r de vouloir rouvrir l �preuve?')\">Rouvrir</a><br />\n";
				}

				//echo "<p style='color:red'>Permettre par la suite de rouvrir une �preuve close (pour correction).</p>\n";
				echo "</li>\n";
			}
			echo "</ul>\n";

			echo "<p style='color:red'>A FAIRE ENCORE&nbsp;: Un lien pour vider toutes les tables d'�preuves blanches.<br />Est-ce qu'il faut vider ces tables lors de l'initialisation?<br />Si oui, peut-�tre ajouter une conservation dans les tables archivages (ann�es ant�rieures).</p>\n";
		}
		//===========================================================================
		// Cr�ation d'une �preuve
		elseif($mode=='creer_epreuve') {
			echo " | <a href='".$_SERVER['PHP_SELF']."'>Menu �preuves blanches</a>\n";
			echo "</p>\n";

			echo "<p class='bold'>Cr�ation d'une �preuve blanche&nbsp;:</p>\n";

			echo "<blockquote>\n";
			echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";

			echo "<table summary='Param�tres'>\n";
			echo "<tr>\n";
			echo "<td>Intitule&nbsp;:</td>\n";
			echo "<td><input type='text' name='intitule' value='Epreuve blanche' /></td>\n";
			echo "</tr>\n";

			$cal = new Calendrier("form1", "date");
		
			$annee=strftime("%Y");
			$mois=strftime("%m");
			$jour=strftime("%d");
			$date_defaut=$jour."/".$mois."/".$annee;

			echo "<tr>\n";
			echo "<td>Date de l'�preuve&nbsp;:</td>\n";
			echo "<td>\n";
			//echo "<input type='text' name='date' value='$date_defaut' />\n";
			echo "<input type='text' name='date' id='date_epreuve' value='$date_defaut' size='10' onchange='changement()' onKeyDown=\"clavier_date_plus_moins(this.id,event);\" />\n";
			echo "<a href=\"#calend\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td>Description&nbsp;:</td>\n";
			echo "<td>\n";
			//echo "<input type='text' name='description' value='' />";
			echo "<textarea class='wrap' name=\"no_anti_inject_description\" rows='4' cols='40'></textarea>\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td>Mode anonymat&nbsp;:</td>\n";
			echo "<td>\n";
			echo "<select name='type_anonymat'>\n";
			echo "<option value='elenoet'>Identifiant Elenoet</option>\n";
			echo "<option value='ele_id'>Identifiant Ele_id</option>\n";
			echo "<option value='no_gep'>Num�ro INE</option>\n";
			echo "<option value='alea'>Chaine al�atoire</option>\n";
			echo "</select>\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td colspan='2' align='center'><input type='submit' name='creer_epreuve' value='Valider' /></td>\n";
			echo "</tr>\n";
			echo "</table>\n";

			//echo "<input type='hidden' name='is_posted' value='2' />\n";
			//echo "<p align='center'><input type='submit' name='creer_epreuve' value='Valider' /></p>\n";
			echo "</form>\n";
			echo "</blockquote>\n";

			echo "<p style='color:red'>NOTES&nbsp;:</p>";
			echo "<ul>";
			echo "<li><p style='color:red'>Le type_anonymat devrait �tre fix� une fois que l'on a contr�l� si l'ELENOET, l'INE sont renseign�s pour les �l�ves choisis.</p></li>\n";
			echo "</ul>";
		}
	}
	//===========================================================================
	// Modification/compl�ments sur une �preuve
	elseif($mode=='modif_epreuve') {
		echo " | <a href='".$_SERVER['PHP_SELF']."'>Menu �preuves blanches</a>\n";

		$aff=isset($_POST['aff']) ? $_POST['aff'] : (isset($_GET['aff']) ? $_GET['aff'] : NULL);
		if(!isset($aff)) {
			echo "</p>\n";

			echo "<p><b>Modification d'une �preuve blanche&nbsp;:</b> Epreuve n�$id_epreuve</p>\n";

			$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				echo "<p style='color:red;'>ERREUR&nbsp;: L'�preuve $id_epreuve n'existe pas.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			$lig=mysql_fetch_object($res);
			$etat=$lig->etat;

			//==============================================
			// Requ�tes exploit�es plus bas
			$sql="SELECT g.* FROM eb_groupes eg, groupes g WHERE eg.id_epreuve='$id_epreuve' AND eg.id_groupe=g.id ORDER BY g.name;";
			$res_groupes=mysql_query($sql);

			$sql="SELECT u.* FROM eb_profs ep, utilisateurs u WHERE ep.id_epreuve='$id_epreuve' AND ep.login_prof=u.login ORDER BY u.nom,u.prenom;";
			$res_profs=mysql_query($sql);

			if(mysql_num_rows($res_groupes)>0) {
				echo "<div style='float:right; width:15em; border: 1px solid black;'>\n";

				echo "<ol>\n";
				echo "<li>\n";
				echo "<a href='definir_salles.php?id_epreuve=$id_epreuve'";
				echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
				if($etat!='clos') {
					echo ">D�finir les salles</a><br />\n";
				}
				else {
					echo ">Consulter les salles</a><br />\n";
				}
				// Proposer d'enregistrer des param�tres
				// Permettre d'organiser les �l�ves en salles
				// G�n�rer CSV, PDF
				echo "</li>\n";
				echo "<li>\n";
				echo "<a href='genere_etiquettes.php?id_epreuve=$id_epreuve'";
				echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
				echo ">G�n�rer les �tiquettes</a><br />\n";
				// Proposer d'enregistrer des param�tres
				// Choisir les champs suppl�mentaires � afficher (date et lieu de naissance, INE, classe,...)
				// Permettre d'organiser les �l�ves en salles
				// G�n�rer CSV, PDF
				echo "</li>\n";
				echo "<li>\n";
				echo "<a href='genere_emargement.php?id_epreuve=$id_epreuve'";
				echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
				echo ">G�n�rer les feuilles d'�margement</a><br />\n";
				// Fiches avec:
				// - NOM_PRENOM;Champ_signature
				// - NOM_PRENOM;N_ANONYMAT;Champ_signature
				// Permettre d'organiser les �l�ves en salles
				// G�n�rer CSV, PDF
				echo "</li>\n";

				if($etat!='clos') {
					if(mysql_num_rows($res_profs)>0) {
						echo "<li>\n";
						echo "<a href='attribuer_copies.php?id_epreuve=$id_epreuve'";
						echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
						echo ">Attribuer les copies aux professeurs</a><br />\n";
						echo "</li>\n";
					}
					else {
						echo "<li>\n";
						echo "Aucun correcteur n'est encore choisi\n";
						echo "</li>\n";
					}
				}

				echo "<li>\n";
				echo "<a href='.php?id_epreuve=$id_epreuve'";
				echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
				echo ">&nbsp;</a>Passer l'�preuve en phase starting_block<br />\n";
				echo "</li>\n";

				echo "<li>\n";
				echo "<a href='saisie_notes.php?id_epreuve=$id_epreuve'";
				echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
				if($etat!='clos') {
					echo ">Saisie des notes</a><br />\n";
				}
				else {
					echo ">Consulter les notes</a><br />\n";
				}
				echo "</li>\n";

				if($etat!='clos') {
					$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND statut='v';";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)==0) {
						echo "<li>\n";
						echo "<a href='transfert_cn.php?id_epreuve=$id_epreuve'";
						echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
						echo ">Transfert vers carnets de notes</a><br />\n";
						echo "</li>\n";

						echo "<li>\n";
						echo "<a href='bilan.php?id_epreuve=$id_epreuve'";
						echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
						echo ">Bilan de l'�preuve</a><br />\n";
						echo "</li>\n";

						echo "<li>\n";
						echo "<a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;mode=clore'";
						echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
						echo ">Clore l'�preuve</a><br />\n";
						echo "</li>\n";
					}
					else {
						echo "<li>\n";
						echo mysql_num_rows($test)." note(s) non encore saisie(s).<br />\n";
						echo "Les choix suivants ne sont donc pas encore accessibles&nbsp;:";
						echo "<ul>\n";
						echo "<li>Transfert vers carnets de notes</li>\n";
						echo "<li>Bilan de l'�preuve</li>\n";
						echo "<li>Clore l'�preuve</li>\n";
						echo "</ul>\n";
					}
				}
				echo "</ol>\n";

				echo "</div>\n";

				/*
				echo "<div style='float:right; width:15em; border: 1px solid black;'>\n";
				echo "Proposer de passer l'�preuve en phase starting_block";
				echo "</div>\n";
				*/
			}
			//==============================================

			echo "<blockquote>\n";
			echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
	
			echo "<table summary='Param�tres'>\n";
			echo "<tr>\n";
			echo "<td style='font-weight:bold;'>Intitule&nbsp;:</td>\n";
			if($etat!='clos') {
				echo "<td><input type='text' name='intitule' value='$lig->intitule' size='34' onchange='changement()' /></td>\n";
			}
			else {
				echo "<td>$lig->intitule</td>\n";
			}
			echo "</tr>\n";
	
			$cal = new Calendrier("form1", "date");
	
			/*
			$annee = strftime("%Y");
			$mois = strftime("%m");
			$jour = strftime("%d");
			$date_defaut=$jour."/".$mois."/".$annee;
			*/
			$tab=explode("-",$lig->date);
			$annee=$tab[0];
			$mois=$tab[1];
			$jour=$tab[2];
			$date_defaut=$jour."/".$mois."/".$annee;
	
			echo "<tr>\n";
			echo "<td style='font-weight:bold;'>Date de l'�preuve&nbsp;:</td>\n";
			echo "<td>\n";

			if($etat!='clos') {
				echo "<input type='text' name='date' id='date_epreuve' value='$date_defaut' size='10' onchange='changement()' onKeyDown=\"clavier_date_plus_moins(this.id,event);\" />\n";
				echo "<a href=\"#calend\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";
			}
			else {
				echo $date_defaut;
			}

			echo "</td>\n";
			echo "</tr>\n";
	
			echo "<tr>\n";
			echo "<td style='font-weight:bold; vertical-align:top;'>Description&nbsp;:</td>\n";
			echo "<td>\n";
			//echo "<input type='text' name='description' value='' />";
			if($etat!='clos') {
				echo "<textarea class='wrap' name=\"no_anti_inject_description\" rows='4' cols='40' onchange='changement()'>".$lig->description."</textarea>\n";
			}
			else {
				echo nl2br($lig->description);
			}
			echo "</td>\n";
			echo "</tr>\n";
	
			echo "<tr>\n";
			echo "<td style='font-weight:bold;'>Mode anonymat&nbsp;:</td>\n";
			echo "<td>\n";
			if($etat!='clos') {
				echo "<select name='type_anonymat' onchange='changement()'>\n";
		
				echo "<option value='elenoet'";
				if($lig->type_anonymat=='elenoet') {echo " selected='true'";}
				echo ">Identifiant Elenoet</option>\n";
		
				echo "<option value='ele_id'";
				if($lig->type_anonymat=='ele_id') {echo " selected='true'";}
				echo ">Identifiant Ele_id</option>\n";
		
				echo "<option value='no_gep'";
				if($lig->type_anonymat=='no_gep') {echo " selected='true'";}
				echo ">Num�ro INE</option>\n";
		
				echo "<option value='alea'";
				if($lig->type_anonymat=='alea') {echo " selected='true'";}
				echo ">Chaine al�atoire</option>\n";
		
				echo "</select>\n";
			}
			elseif($lig->type_anonymat=='no_gep') {
				echo "Num�ro INE";
			}
			else {
				echo "Identifiant ".strtoupper($lig->type_anonymat);
			}
			echo "</td>\n";
			echo "</tr>\n";
	
			echo "<tr>\n";
			echo "<td colspan='2' align='center'><input type='submit' name='modif_epreuve' value='Valider' /></td>\n";
			echo "</tr>\n";
			echo "</table>\n";

			echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
			echo "<input type='hidden' name='mode' value='modif_epreuve' />\n";
			//echo "<input type='hidden' name='is_posted' value='2' />\n";
			//echo "<p align='center'><input type='submit' name='modif_epreuve' value='Valider' /></p>\n";
			echo "</form>\n";
			echo "</blockquote>\n";

			/*
			echo "<p class='bold'>Compl�ter les �l�ments de l'�preuve&nbsp;:</p>\n";
			echo "<ul>\n";
			echo "<li><a href=''></a></li>\n";
			echo "</ul>\n";

			echo "<p><b>Compl�ter les �l�ments de l'�preuve&nbsp;:</b> <a href=''></a></p>\n";
			*/

			//=====================================

			//$sql="SELECT g.* FROM eb_groupes eg, groupes g WHERE eg.id_epreuve='$id_epreuve' AND eg.id_groupe=g.id ORDER BY g.name;";
			//$res_groupes=mysql_query($sql);
			if(mysql_num_rows($res_groupes)>0) {
				echo "<p><b>Liste des groupes inscrits � l'�preuve&nbsp;:</b></p>\n";
				echo "<blockquote>\n";
				while($lig=mysql_fetch_object($res_groupes)) {

					//$current_group=get_group($lig->id);

					$sql="SELECT DISTINCT c.classe FROM classes c, j_groupes_classes jgc WHERE jgc.id_groupe='".$lig->id."' AND jgc.id_classe=c.id ORDER BY classe;";
					$res_classes=mysql_query($sql);
					$cpt=0;
					$classlist_string="";
					while($lig_class=mysql_fetch_object($res_classes)) {
						if($cpt>0) {$classlist_string.=", ";}
						$classlist_string.=$lig_class->classe;
						$cpt++;
					}

					//echo "<b>".$current_group['classlist_string']."</b> <a href='#'>".htmlentities($lig->name)."</a> (<i>".htmlentities($lig->description)."</i>)";
					//echo "<b>".$current_group['classlist_string']."</b> ".htmlentities($lig->name)." (<i>".htmlentities($lig->description)."</i>)";
					echo "<b>".$classlist_string."</b> ".htmlentities($lig->name)." (<i>".htmlentities($lig->description)."</i>)";
					if($etat!='clos') {
						echo " - <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$lig->id&amp;id_groupe=$lig->id&amp;mode=suppr_groupe' onclick=\"return confirm('Etes vous s�r de vouloir supprimer le groupe de l �preuve?')\">Supprimer</a>\n";
					}
					echo "<br />\n";
					// Afficher les �l�ves inscrits/non inscrits en infobulle
					// Permettre de cocher/d�cocher les �l�ves dans ces infobulles
				}
			}
			else {
				echo "<p><b>Aucun groupe n'est encore inscrit � l'�preuve&nbsp;:</b></p>\n";
				echo "<blockquote>\n";
			}
			if($etat!='clos') {
				echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve&amp;aff=classes'>Ajouter des groupes</a></p>\n";
			}
			echo "</blockquote>\n";

			//=====================================

			// Effectuer un contr�le de l'anonymat:
			// Nombre d'inscrits et nombre de num�ros distincts
			// Pouvoir modifier le type anonymat

			//=====================================

			//$sql="SELECT u.* FROM eb_profs ep, utilisateurs u WHERE ep.id_epreuve='$id_epreuve' AND ep.login_prof=u.login ORDER BY u.nom,u.prenom;";
			//$res_profs=mysql_query($sql);
			if(mysql_num_rows($res_profs)>0) {
				echo "<p><b>Liste des professeurs heureux correcteurs d�sign�s pour l'�preuve&nbsp;:</b></p>\n";
				echo "<blockquote>\n";
				while($lig=mysql_fetch_object($res_profs)) {
					//echo "<a href='#'>".$lig->civilite." ".$lig->nom." ".substr($lig->prenom,0,1)."<br />\n";
					echo $lig->civilite." ".$lig->nom." ".substr($lig->prenom,0,1);
					//echo " <span style='color:red'>Compter les copies attribu�es</span>";

					$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND login_prof='".$lig->login."';";
					$compte_total=mysql_num_rows(mysql_query($sql));

					if($compte_total==0) {
						echo " (<span style='font-style:italic;color:red;'>aucune copie attribu�e</span>)";
					}
					else {
						$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND login_prof='".$lig->login."' AND statut!='v';";
						$compte_saisie=mysql_num_rows(mysql_query($sql));
						echo " (<span style='font-style:italic;";
						if($compte_saisie==$compte_total) {
							echo "color:green;";
						}
						else {
							echo "color:red;";
						}
						echo "'>$compte_saisie/$compte_total</span>)";
					}
					echo "<br />\n";
				}

				if($etat!='clos') {
					echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve&amp;aff=profs'>Ajouter/supprimer des professeurs correcteurs</a></p>\n";
				}
			}
			else {
				echo "<p><b>Aucun correcteur n'est encore d�sign� pour l'�preuve&nbsp;:</b></p>\n";
				echo "<blockquote>\n";

				if($etat!='clos') {
					echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve&amp;aff=profs'>Ajouter des professeurs correcteurs</a></p>\n";
				}
			}
			//echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve&amp;aff=profs'>Ajouter des professeurs correcteurs</a></p>\n";
			echo "</blockquote>\n";

			//=====================================

			if(mysql_num_rows($res_groupes)>0) {
				$sql="SELECT * FROM eb_salles es WHERE es.id_epreuve='$id_epreuve' ORDER BY es.salle;";
				$res_salles=mysql_query($sql);
				if(mysql_num_rows($res_salles)>0) {
					echo "<p><b>Liste des salles choisies pour l'�preuve&nbsp;:</b></p>\n";
					echo "<blockquote>\n";
					while($lig=mysql_fetch_object($res_salles)) {
						$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND id_salle='$lig->id';";
						$res_eff=mysql_query($sql);
						echo "Salle '<b>$lig->salle</b>' (<i>eff.".mysql_num_rows($res_eff)."</i>)";
						echo "<br />\n";
					}
					echo "</blockquote>\n";
				}
				else {
					echo "<p><b>Aucun salle n'est encore choisie pour l'�preuve.</b></p>\n";
				}
			}
			echo "<p><br /></p>";

			echo "<p style='color:red'>NOTES&nbsp;:</p>";
			echo "<ul>";
			echo "<li><p style='color:red'>A FAIRE: Pouvoir interdire (ou alerter) la modification du type_anonymat une fois les listings_�margement/etiquettes g�n�r�s.<br />Mettre par exemple eb_epreuves.etat='starting_block' et interdire alors les modifs d'anonymat.</p></li>\n";
			//echo "<li><p style='color:red'>Ajouter des changement() et confirm_abandon() quand on quitte la page sans valider une modif.</p></li>\n";
			//echo "<li><p style='color:red'>A FAIRE: afficher la liste des salles associ�es et les effectifs dans les salles.</p></li>\n";
			echo "</ul>";

		}
		//=============================================================================
		elseif($aff=='classes') {
			// Choix des classes

			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve'>Epreuve $id_epreuve</a>\n";
			echo "</p>\n";

			$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				echo "<p style='color:red;'>ERREUR&nbsp;: L'�preuve $id_epreuve n'existe pas.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			$lig=mysql_fetch_object($res);
			$etat=$lig->etat;

			if($etat=='clos') {
				echo "<p class='bold'>L'�preuve $id_epreuve est close.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			echo "<p class='bold'>Choix des classes pour l'�preuve $id_epreuve&nbsp;:</p>\n";

			if($_SESSION['statut']=='administrateur') {
				$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
			}
			elseif($_SESSION['statut']=='scolarite') {
				$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes j WHERE p.id_classe = c.id AND j.id_classe=c.id ORDER BY classe";
				// Permettre aussi de voir toutes les classes...
			}
			$classes_list = mysql_query($sql);
			$nb = mysql_num_rows($classes_list);
			if ($nb==0) {
				echo "<p>Aucune classe ne semble d�finie.</p>\n";
			}
			else {
				// Liste des classes d�j� associ�es � l'�preuve via des groupes inscrits dans eb_groupes
				$tab_id_classe=array();
				$sql="SELECT DISTINCT j.id_classe FROM eb_groupes eg, j_groupes_classes j WHERE eg.id_epreuve='$id_epreuve' AND eg.id_groupe=j.id_groupe";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {
					while($lig=mysql_fetch_object($res)) {
						$tab_id_classe[]=$lig->id_classe;
					}
				}

				// Choix des classes dont il faudra lister les groupes
				echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
	
				$nb_class_par_colonne=round($nb/3);
				echo "<table width='100%' summary='Choix des classes'>\n";
				echo "<tr valign='top' align='center'>\n";
	
				$i=0;
				echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
				echo "<td align='left'>\n";
	
				/*
				while ($i < $nb) {
					$id_classe = mysql_result($classes_list, $i, 'id');
					$temp = "case_".$id_classe;
					$classe = mysql_result($classes_list, $i, 'classe');
	
					if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
						echo "</td>\n";
						//echo "<td style='padding: 0 10px 0 10px'>\n";
						echo "<td align='left'>\n";
					}
	
					echo "<label for='$temp' style='cursor: pointer;'>";
					echo "<input type='checkbox' name='id_classe[]' id='$temp' value='$id_classe' ";
					if(in_array($id_classe,$tab_id_classe)) {echo "checked ";}
					echo "/>";
					echo "Classe : $classe</label><br />\n";
					$i++;
				}
				*/
				while ($i < $nb) {
					$id_classe=mysql_result($classes_list, $i, 'id');
					//$temp = "id_classe_".$id_classe;
					$classe=mysql_result($classes_list, $i, 'classe');
	
					if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
						echo "</td>\n";
						//echo "<td style='padding: 0 10px 0 10px'>\n";
						echo "<td align='left'>\n";
					}
	
					echo "<input type='checkbox' name='id_classe[]' id='id_classe_$i' value='$id_classe' ";
					echo "onchange=\"checkbox_change($i)\" ";
					if(in_array($id_classe,$tab_id_classe)) {echo "checked ";$temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
					echo "/><label for='id_classe_$i'><span id='texte_id_classe_$i'$temp_style>Classe : ".$classe.".</span></label><br />\n";
					$i++;
				}

				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";

				//echo "<input type='hidden' name='is_posted' value='2' />\n";

				echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
				echo "<input type='hidden' name='mode' value='modif_epreuve' />\n";
				echo "<input type='hidden' name='aff' value='groupes' />\n";
				echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
				echo "</form>\n";

				echo "<script type='text/javascript'>
function checkbox_change(cpt) {
	if(document.getElementById('id_classe_'+cpt)) {
		if(document.getElementById('id_classe_'+cpt).checked) {
			document.getElementById('texte_id_classe_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_id_classe_'+cpt).style.fontWeight='normal';
		}
	}
}
</script>\n";

			}
		}
		//=============================================================================
		elseif($aff=='groupes') {
			//Choix des groupes

			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve'>Epreuve $id_epreuve</a>\n";
			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve&amp;aff=classes'>Choix des classes</a>\n";
			echo "</p>\n";


			$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				echo "<p style='color:red;'>ERREUR&nbsp;: L'�preuve $id_epreuve n'existe pas.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			$lig=mysql_fetch_object($res);
			$etat=$lig->etat;

			if($etat=='clos') {
				echo "<p class='bold'>L'�preuve $id_epreuve est close.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}


			$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;

			if(!isset($id_classe)) {
				echo "<p style='color:red'>ERREUR&nbsp;: Aucune classe n'a �t� choisie.</p>\n";
			}

			echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";

			echo "<p class='bold'>Choix des groupes pour l'�preuve $id_epreuve&nbsp;:</p>\n";

			$sql="SELECT type_anonymat FROM eb_epreuves WHERE id='$id_epreuve';";
			$res=mysql_query($sql);
			$lig=mysql_fetch_object($res);
			if($lig->type_anonymat=='alea') {
				echo "<div style='float:right; width:20em; border: 1px solid black;'>\n";
				echo "<p>Le type d'anonymat choisi est un num�ro 'al�atoire'.</p>\n";
				echo "<p><b>Attention</b>&nbsp;: Lors de la validation de ce formulaire, les num�ros d'anonymat sont g�n�r�s/reg�n�r�s.<br />Vous ne devriez pas valider ce formulaire une fois que des �tiquettes ont �t� coll�es ou des copies anonym�es.</p>\n";
				echo "</div>\n";
			}

			$tab_groupes_inscrits=array();
			$sql="SELECT id_groupe FROM eb_groupes eg WHERE eg.id_epreuve='$id_epreuve';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				while($lig=mysql_fetch_object($res)) {
					$tab_groupes_inscrits[]=$lig->id_groupe;
				}
			}

			echo "<table class='boireaus' summary='Groupes tri�s par classes'>\n";
			echo "<tr>\n";
			for($i=0;$i<count($id_classe);$i++) {
				echo "<th>".get_class_from_id($id_classe[$i])."</th>\n";
			}
			echo "</tr>\n";
			echo "<tr>\n";
			$alt=1;
			$cpt=0;
			for($i=0;$i<count($id_classe);$i++) {
				$alt=$alt*(-1);
				echo "<td class='lig$alt' style='text-align:left;vertical-align:top;'>\n";
				$sql="SELECT g.* FROM groupes g, j_groupes_classes jgc WHERE jgc.id_groupe=g.id AND jgc.id_classe='$id_classe[$i]' ORDER BY g.name;";
				//echo "$sql<br />\n";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {
					while($lig=mysql_fetch_object($res)) {
						echo "<input type='checkbox' name='id_groupe[]' id='id_groupe_$cpt' value='$lig->id' ";
						echo "onchange=\"checkbox_change($cpt)\" ";
						if(in_array($lig->id,$tab_groupes_inscrits)) {echo "checked ";$temp_style="style='font-weight:bold;'";} else {$temp_style="";}
						echo "/><label for='id_groupe_$cpt' style='cursor: pointer;'><span id='texte_id_groupe_$cpt' $temp_style>".htmlentities($lig->name)." (<span style='font-style:italic;font-size:x-small;'>".htmlentities($lig->description)."</span>)</span></label><br />\n";
						$cpt++;
					}
				}
				echo "</td>\n";
			}
			echo "</tr>\n";
			echo "</table>\n";

			echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
			//echo "<input type='hidden' name='mode' value='modif_epreuve' />\n";
			echo "<input type='hidden' name='mode' value='ajout_groupes' />\n";
			//echo "<input type='hidden' name='aff' value='groupes' />\n";
			echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
			echo "</form>\n";

			echo "<script type='text/javascript'>
function checkbox_change(cpt) {
	if(document.getElementById('id_groupe_'+cpt)) {
		if(document.getElementById('id_groupe_'+cpt).checked) {
			document.getElementById('texte_id_groupe_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_id_groupe_'+cpt).style.fontWeight='normal';
		}
	}
}
</script>\n";

		}
		/*
		elseif($aff=='eleves') {
			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve'>Epreuve $id_epreuve</a>\n";
			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve&amp;aff=classes'>Choix des classes</a>\n";
			echo "</p>\n";

			$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;

			if(!isset($id_classe)) {
				echo "<p style='color:red'>ERREUR&nbsp;: Aucune classe n'a �t� choisie.</p>\n";
			}

			for($i=0;$i<count($id_classe);$i++) {


			}

		}
		*/
		//=============================================================================
		elseif($aff=='profs') {
			// Choix des profs

			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve'>Epreuve $id_epreuve</a>\n";
			echo "</p>\n";


			$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				echo "<p style='color:red;'>ERREUR&nbsp;: L'�preuve $id_epreuve n'existe pas.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			$lig=mysql_fetch_object($res);
			$etat=$lig->etat;

			if($etat=='clos') {
				echo "<p class='bold'>L'�preuve $id_epreuve est close.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}


			echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";

			$tab_profs_deja_punis=array();
			$sql="SELECT u.login FROM eb_profs ep, utilisateurs u WHERE ep.id_epreuve='$id_epreuve' AND ep.login_prof=u.login ORDER BY u.nom,u.prenom;";
			$res_profs=mysql_query($sql);
			if(mysql_num_rows($res_profs)>0) {
				while($lig=mysql_fetch_object($res_profs)) {
					$tab_profs_deja_punis[]=$lig->login;
				}
			}

			$cpt=0;
			$sql="SELECT DISTINCT u.login,u.nom,u.prenom,u.civilite FROM eb_groupes eg, j_groupes_professeurs jgp, utilisateurs u WHERE eg.id_epreuve='$id_epreuve' AND eg.id_groupe=jgp.id_groupe AND u.login=jgp.login ORDER BY u.nom,u.prenom;";
			$res_profs_groupes=mysql_query($sql);
			if(mysql_num_rows($res_profs_groupes)>0) {
				echo "<p>Les professeurs appel�s � corriger sont probablement les enseignants des groupes s�lectionn�s.</p>\n";
				//$cpt=0;
				while($lig=mysql_fetch_object($res_profs_groupes)) {
					echo "<input type='checkbox' name='login_prof[]' id='login_prof_$cpt' value='$lig->login' ";
					echo "onchange=\"checkbox_change($cpt)\" ";
					//if(in_array($lig->login,$tab_profs_deja_punis)) {echo "checked ";}
					//echo "/><label for='login_prof_$cpt'>".$lig->civilite." ".$lig->nom." ".substr($lig->prenom,0,1).".</span></label><br />\n";
					if(in_array($lig->login,$tab_profs_deja_punis)) {echo "checked ";$temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
					echo "/><label for='login_prof_$cpt'><span id='texte_login_prof_$cpt'$temp_style>".$lig->civilite." ".$lig->nom." ".substr($lig->prenom,0,1).".</span></label><br />\n";
					$cpt++;
				}
			}

			$sql="SELECT DISTINCT u.login,u.nom,u.prenom,u.civilite FROM utilisateurs u WHERE u.statut='professeur' ORDER BY u.nom,u.prenom;";
			$res_profs=mysql_query($sql);
			if(mysql_num_rows($res_profs)>0) {
				echo "<p>S�lectionner des professeurs sans pr�occupation de groupes&nbsp;:</p>\n";

				$nb=mysql_num_rows($res_profs);
				$nb_prof_par_colonne=round($nb/3);
				echo "<table width='100%' summary='Choix des professeurs'>\n";
				echo "<tr valign='top' align='center'>\n";
	
				$i=0;
				echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
				echo "<td align='left'>\n";
	
				while ($i < $nb) {

					$lig=mysql_fetch_object($res_profs);

					if(($i>0)&&(round($i/$nb_prof_par_colonne)==$i/$nb_prof_par_colonne)){
						echo "</td>\n";
						//echo "<td style='padding: 0 10px 0 10px'>\n";
						echo "<td align='left'>\n";
					}
	
					echo "<input type='checkbox' name='login_prof[]' id='login_prof_$cpt' value='$lig->login' ";
					echo "onchange=\"checkbox_change($cpt)\" ";
					if(in_array($lig->login,$tab_profs_deja_punis)) {echo "checked ";$temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
					echo "/><label for='login_prof_$cpt'><span id='texte_login_prof_$cpt'$temp_style>".$lig->civilite." ".$lig->nom." ".substr($lig->prenom,0,1).".</span></label><br />\n";
					$cpt++;

					$i++;
				}
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";

				/*
				while($lig=mysql_fetch_object($res_profs)) {
					echo "<input type='checkbox' name='login_prof[]' id='login_prof_$cpt' value='$lig->login' ";
					if(in_array($lig->login,$tab_profs_deja_punis)) {echo "checked ";}
					echo "/><label for='login_prof_$cpt'>".$lig->civilite." ".$lig->nom." ".substr($lig->prenom,0,1).".</label><br />\n";
					$cpt++;
				}
				*/
			}

			echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
			//echo "<input type='hidden' name='mode' value='modif_epreuve' />\n";
			echo "<input type='hidden' name='mode' value='ajout_profs' />\n";
			echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
			echo "</form>\n";

			echo "<script type='text/javascript'>
function checkbox_change(cpt) {
	if(document.getElementById('login_prof_'+cpt)) {
		if(document.getElementById('login_prof_'+cpt).checked) {
			document.getElementById('texte_login_prof_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_login_prof_'+cpt).style.fontWeight='normal';
		}
	}
}
</script>\n";

		}
	}
}
//=============================================================================
elseif($_SESSION['statut']=='professeur') {
	// Menu professeur

	// Acc�der aux �preuves blanches qui lui sont affect�es
	$sql="SELECT ee.* FROM eb_epreuves ee, eb_profs ep WHERE ep.login_prof='".$_SESSION['login']."' AND ee.id=ep.id_epreuve AND ee.etat!='clos' ORDER BY ee.date, ee.intitule;";
	//echo "$sql<br />\n";

	// Afficher les �preuves auxquelles est associ� le prof
	// Pointer vers des pages:
	// - saisie
	// - g�n�ration d'un listing n_anonymat,note,statut

	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		echo "<p><b>Epreuves en cours&nbsp;:</b><br />\n";
		echo "<ul>\n";
		while($lig=mysql_fetch_object($res)) {
			echo "<li>\n";

			$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$lig->id';";
			$test1=mysql_query($sql);
			
			$sql="SELECT DISTINCT n_anonymat FROM eb_copies WHERE id_epreuve='$lig->id';";
			$test2=mysql_query($sql);
			if(mysql_num_rows($test1)!=mysql_num_rows($test2)) {
				echo "<span style='color:red;'>Les num�ros anonymats ne sont pas uniques sur l'�preuve (<i>cela ne devrait pas arriver</i>).<br />La saisie n'est pas possible sur l'�preuve </span>";
				if($lig->description!='') {
					echo "<a href='#'";
					echo " onmouseover=\"delais_afficher_div('div_epreuve_".$lig->id."','y',-100,20,1000,20,20)\" onmouseout=\"cacher_div('div_epreuve_".$lig->id."')\"";
	
					$titre="Epreuve n�$lig->id";
					$texte="<p><b>".$lig->intitule."</b><br />";
					$texte.=$lig->description;
					$tabdiv_infobulle[]=creer_div_infobulle('div_epreuve_'.$lig->id,$titre,"",$texte,"",30,0,'y','y','n','n');
	
					echo ">$lig->intitule</a> (<i>".formate_date($lig->date)."</i>)<br />\n";
				}
				else {
					echo "$lig->intitule (<i>".formate_date($lig->date)."</i>)<br />\n";
				}
			}
			else {
				//echo "Modifier <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$lig->id&amp;modif_epreuve=y'";
				echo "Saisir les notes pour <a href='saisie_notes.php?id_epreuve=$lig->id'";
				if($lig->description!='') {
					echo " onmouseover=\"delais_afficher_div('div_epreuve_".$lig->id."','y',-100,20,1000,20,20)\" onmouseout=\"cacher_div('div_epreuve_".$lig->id."')\"";
	
					$titre="Epreuve n�$lig->id";
					$texte="<p><b>".$lig->intitule."</b><br />";
					$texte.=$lig->description;
					$tabdiv_infobulle[]=creer_div_infobulle('div_epreuve_'.$lig->id,$titre,"",$texte,"",30,0,'y','y','n','n');
	
				}
				echo ">$lig->intitule</a> (<i>".formate_date($lig->date)."</i>)<br />\n";
			}
			echo "</li>\n";
		}
		echo "</ul>\n";
	}
}

require("../lib/footer.inc.php");
?>