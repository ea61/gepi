<?php
/**
 * Plan de classe
 * 
 * $Id$
 *
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
 * 
 * @package Trombinoscopes
 * @subpackage Conteneur
 * @license GNU/GPL 
 * @see check_token()
 * @see checkAccess()
 * @see get_groups_for_prof()
 * @see getSettingValue()
 * @see Session::security_check()
 */

/* This file is part of GEPI.
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

/**
 * Fichiers d'initialisation
 */
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_trombinoscopes/plan_de_classe.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/mod_trombinoscopes/plan_de_classe.php',
	administrateur='F',
	professeur='V',
	cpe='F',
	scolarite='F',
	eleve='F',
	responsable='F',
	secours='F',
	autre='F',
	description='Plan de classe',
	statut='';";
	$insert=mysql_query($sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_trombinoscopes")!='y') {
	die("Le module n'est pas activé.");
}

$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : NULL;

$sql="CREATE TABLE IF NOT EXISTS t_plan_de_classe (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
id_groupe INT(11) NOT NULL ,
login_prof VARCHAR(50) NOT NULL ,
dim_photo INT(11) NOT NULL);";
$create_table=mysql_query($sql);

$sql="CREATE TABLE IF NOT EXISTS t_plan_de_classe_ele (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
id_plan INT( 11 ) NOT NULL,
login_ele VARCHAR(50) NOT NULL ,
x INT(11) NOT NULL ,
y INT(11) NOT NULL);";
$create_table=mysql_query($sql);

// On ne va afficher l'entête que pour le choix du groupe, pas sur la partie réalisation du plan de classe
if(!isset($id_groupe)) {
	//**************** EN-TETE *****************
	$titre_page = "Plan de classe";
	/**
	* Entête de la page
	*/
	require_once("../lib/header.inc");
	//**************** FIN EN-TETE *************
	
	echo "<p class='bold'>\n";
	echo "<a href=\"trombinoscopes.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a> \n";
	echo "</p>\n";

	$tab_groups = get_groups_for_prof($_SESSION['login'],"classe puis matière");

	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();
	echo "<p>Choisissez l'enseignement pour lequel vous souhaitez réaliser le plan de classe&nbsp;:</p>\n";

	echo "<table class='boireaus'>\n";
	echo "<tr>\n";
	echo "<th>Choix</th>\n";
	echo "<th>Enseignement</th>\n";
	echo "<th>Dimension<br />des photos</th>\n";
	echo "</tr>\n";
	$alt=1;
	for($loop=0;$loop<count($tab_groups);$loop++) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td>\n";
		echo "<input type='radio' name='id_groupe' id='id_groupe_".$tab_groups[$loop]['id']."' value='".$tab_groups[$loop]['id']."' ";
		if($loop==0) {echo "checked ";}
		echo "/>\n";
		echo "</td>\n";

		echo "<td>\n";
		echo "<label for='id_groupe_".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['name']." (".$tab_groups[$loop]['description'].") en ".$tab_groups[$loop]['classlist_string']."</label>\n";
		echo "</td>\n";

		echo "<td>\n";
		$dim_photo=100;
		$sql="SELECT dim_photo FROM t_plan_de_classe WHERE id_groupe='".$tab_groups[$loop]['id']."' AND login_prof='".$_SESSION['login']."';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$dim_photo=mysql_result($res,0);
		}
		echo "<input type='text' name='dim_photo_".$tab_groups[$loop]['id']."' value='$dim_photo' size='3' />";
		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";

	echo "<p><input type='submit' name='Valider' value='Valider' /></p>\n";
	echo "</form>\n";


	require("../lib/footer.inc.php");
	die();
}

//======================================
/**
* Entête de la page
*/
require_once("../lib/header.inc");
//======================================

//debug_var();

$dim_photo=isset($_POST['dim_photo_'.$id_groupe]) ? $_POST['dim_photo_'.$id_groupe] : 100;

$dim_photo=preg_replace('/[^0-9]/','',$dim_photo);
if(($dim_photo=="")||($dim_photo==0)) {$dim_photo=100;}


$sql="SELECT * FROM t_plan_de_classe WHERE id_groupe='$id_groupe' AND login_prof='".$_SESSION['login']."';";
$res=mysql_query($sql);
if(mysql_num_rows($res)>0) {
	$lig=mysql_fetch_object($res);

	$id_plan=$lig->id;

	$tmp_dim_photo=$lig->dim_photo;
	if($tmp_dim_photo!=$dim_photo) {
		//$sql="UPDATE t_plan_de_classe SET dim_photo='$dim_photo' WHERE id_groupe='$id_groupe' AND login_prof='".$_SESSION['login']."';";
		$sql="UPDATE t_plan_de_classe SET dim_photo='$dim_photo' WHERE id='$id_plan' AND id_groupe='$id_groupe' AND login_prof='".$_SESSION['login']."';";
		//echo "$sql<br />";
		$update=mysql_query($sql);
	}
}
else {
	$sql="INSERT INTO t_plan_de_classe SET dim_photo='$dim_photo', id_groupe='$id_groupe', login_prof='".$_SESSION['login']."';";
	//echo "$sql<br />";
	$insert=mysql_query($sql);

	$id_plan=mysql_insert_id();
}

if(isset($_POST['enregistrer_position'])) {
	$pos_div_x=isset($_POST['pos_div_x']) ? $_POST['pos_div_x'] : NULL;
	$pos_div_y=isset($_POST['pos_div_y']) ? $_POST['pos_div_y'] : NULL;
	if((isset($pos_div_x))&&(isset($pos_div_y))) {

		// A FAIRE: VERIFIER SI id_plan APPARTIENT BIEN AU PROF

		$sql="DELETE FROM t_plan_de_classe_ele WHERE id_plan='$id_plan';";
		//echo "$sql<br />";
		$menage=mysql_query($sql);

		foreach($pos_div_x as $login_ele => $x) {
			if($x!="") {
				if(isset($pos_div_y[$login_ele])) {
					$y=$pos_div_y[$login_ele];

					$x=preg_replace("/px$/","",$x);
					if(preg_match("/^[0-9.]*$/", $x)) {
						$x=round($x);

						if($y!="") {
							$y=preg_replace("/px$/","",$y);
							if(preg_match("/^[0-9.]*$/", $y)) {
								$y=round($y);

								//$sql="INSERT INTO t_plan_de_classe SET id_groupe='$id_groupe', login='".$_SESSION['login']."', nom='div_".$login_ele."_x', valeur='$x';";
								$sql="INSERT INTO t_plan_de_classe_ele SET id_plan='$id_plan', login_ele='".$login_ele."', x='$x', y='$y';";
								$insert=mysql_query($sql);
							}
						}
					}
				}
			}
		}

	}
}


$current_group=get_group($id_groupe);
echo "<h1 style='text-align:center; margin-top: 0.2em;'>".$current_group['name']." (".$current_group['description'].") en ".$current_group['classlist_string']."</h1>";

$grp_order_by="c.classe, e.nom, e.prenom";

$sql="SELECT jeg.login, jeg.id_groupe, jeg.periode, e.login, e.nom, e.prenom, e.elenoet, g.id, g.name, g.description, c.classe
FROM eleves e, groupes g, j_eleves_groupes jeg, j_eleves_classes jec, classes c
WHERE jeg.login = e.login
AND jec.login = e.login
AND jec.id_classe=c.id
AND jeg.id_groupe = g.id
AND g.id = '".$id_groupe."'
GROUP BY nom, prenom
ORDER BY $grp_order_by;";

$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "<p>Erreur lors de la requête $sql</p>\n";
	require("../lib/footer.inc.php");
	die();
}

//================================
$largeur_img_fond=$dim_photo+30;
$hauteur_img_fond=$dim_photo+30;
//Création de l'image:
$img=imageCreate($largeur_img_fond,$hauteur_img_fond);
//$img=imagecreatetruecolor($largeur_img_fond,$hauteur_img_fond);

// Epaisseur initiale des traits...
imagesetthickness($img,2);

$x1=0;
$x2=$dim_photo+30;

$y1=0;
$y2=$dim_photo+30;

$couleur_fond=imageColorAllocate($img,255,255,255);
//imagecolorallocatealpha($img, 255, 255, 255, 127);

$couleur_trait=imageColorAllocate($img,0,0,0);
imageLine($img,$x1,$y1,$x2,$y1,$couleur_trait);
imageLine($img,$x1,$y1,$x1,$y2,$couleur_trait);

$chemin_img_fond="../temp/".get_user_temp_directory()."/fond_plan_classe.png";
imagePNG($img, $chemin_img_fond);

imageDestroy($img);
//================================

echo "<form action='".$_SERVER['PHP_SELF']."' name='form_reg_pos' method='post'>\n";
echo "<div style='position:absolute; top:0.5em; left:0.5em; width:5em; text-align:center;'>\n";
echo "<a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a><br />\n";
echo "<input type='button' name='Enregistrer' value='Enregistrer' class='noprint' onclick='enregistrement_position_div_photo()' />\n";
echo "</div>\n";

echo add_token_field();
echo "<input type='hidden' name='dim_photo_$id_groupe' value='$dim_photo' />\n";

$chaine_affichage_div="";
$unite_div_infobulle="px";
$chaine_login_ele="";

$repertoire="eleves";
while($lig=mysql_fetch_object($res)) {

	$nom_photo = nom_photo($lig->elenoet,$repertoire);
	$photo = $nom_photo;

	$alt_nom_prenom_aff=strtoupper($lig->nom)." ".casse_mot($lig->prenom,'majf2');

	if (($nom_photo) and (file_exists($photo))) {
		$info_image = getimagesize($photo);
		// largeur et hauteur de l'image d'origine
		$largeur = $info_image[0];
		$hauteur = $info_image[1];

		if($largeur>$hauteur) {$dif_ref=$largeur;}
		else {$dif_ref=$hauteur;}
		$ratio=$dif_ref/$dim_photo;

		// définit largeur et hauteur pour la nouvelle image
		$nouvelle_largeur = $largeur / $ratio;
		$nouvelle_hauteur = $hauteur / $ratio;

		$valeur[0]=$nouvelle_largeur;
		$valeur[1]=$nouvelle_hauteur;

	} else {
		$valeur[0]=$dim_photo;
		$valeur[1]=$dim_photo;
	}

	//echo "<div>";
	$texte="<img src='";
	if (($nom_photo) and (file_exists($photo))) {
		$texte.=$photo;
	}
	else {
		$texte.="images/trombivide.jpg";
	}

	$texte.="' style='border: 0px; width: ".$valeur[0]."px; height: ".$valeur[1]."px;' alt=\"".$alt_nom_prenom_aff."\" title=\"".$alt_nom_prenom_aff."\" />\n";
	$titre="$lig->login";
	echo creer_div_infobulle("div_".$lig->login,$titre,"",$texte,"",$valeur[0],"","y","n","n","n",1000);
	$chaine_affichage_div.="document.getElementById('div_".$lig->login."').style.display='';\n";

	if($chaine_login_ele!='') {$chaine_login_ele.=",";}
	$chaine_login_ele.="'$lig->login'";

	echo "<input type='hidden' name='pos_div_x[".$lig->login."]' id='pos_div_".$lig->login."_x' value='' />\n";
	echo "<input type='hidden' name='pos_div_y[".$lig->login."]' id='pos_div_".$lig->login."_y' value='' />\n";
}

echo "<input type='hidden' name='enregistrer_position' value='y' />\n";
echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";

echo "</form>\n";

echo "<script type='text/javascript'>
	function afficher_les_photos() {
		$chaine_affichage_div
	}

	document.body.style.backgroundImage=\"url('$chemin_img_fond')\";

	setTimeout('afficher_les_photos()',1000);

	var tab_ele=new Array($chaine_login_ele);

	function enregistrement_position_div_photo() {
		for(i=0;i<tab_ele.length;i++) {
			if(document.getElementById('pos_div_'+tab_ele[i]+'_x')) {
				document.getElementById('pos_div_'+tab_ele[i]+'_x').value=document.getElementById('div_'+tab_ele[i]).style.left;
			}
			if(document.getElementById('pos_div_'+tab_ele[i]+'_y')) {
				document.getElementById('pos_div_'+tab_ele[i]+'_y').value=document.getElementById('div_'+tab_ele[i]).style.top;
			}
		}

		document.form_reg_pos.submit();
	}

</script>\n";


	echo "<script type='text/javascript'>
largeur_fenetre=window.innerWidth;

function positionner_les_photos() {
	// Positionnement initial des photos
	x_ini=10;
	x_courant=x_ini;
	y_courant=50;

	for(i=0;i<tab_ele.length;i++) {
		if(document.getElementById('div_'+tab_ele[i])) {
			if(eval(x_courant+$dim_photo)>largeur_fenetre) {
				x_courant=x_ini;
				y_courant=eval(y_courant+$dim_photo);
			}
			else {
				x_courant=eval(x_courant+$dim_photo);
			}
			document.getElementById('div_'+tab_ele[i]).style.left=x_courant+'px';
			document.getElementById('div_'+tab_ele[i]).style.top=y_courant+'px';
		}
	}
\n";

$sql="SELECT * FROM t_plan_de_classe_ele WHERE id_plan='$id_plan';";
$res_pos=mysql_query($sql);
if(mysql_num_rows($res_pos)>0) {
	echo "
	// Positionnement d apres ce qui est enregistre
";
	while($lig_pos=mysql_fetch_object($res_pos)) {
		echo "if(document.getElementById('div_".$lig_pos->login_ele."')) {
	document.getElementById('div_".$lig_pos->login_ele."').style.top='".$lig_pos->y."px';
	document.getElementById('div_".$lig_pos->login_ele."').style.left='".$lig_pos->x."px';
}";
	}
}

echo "}

// Il faut attendre que les div soient initialisés dans le footer.
setTimeout('positionner_les_photos()', 2000);
</script>\n";


echo "<br />";
/**
* Pied de page
*/
require("../lib/footer.inc.php");
?>
