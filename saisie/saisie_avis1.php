<?php
/*
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Laurent Viénot-Hauger
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
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

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
// initialisation
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :NULL);

include "../lib/periodes.inc.php";

$msg="";

$gepi_denom_mention=getSettingValue("gepi_denom_mention");
if($gepi_denom_mention=="") {
	$gepi_denom_mention="mention";
}

if (isset($_POST['is_posted'])) {
	check_token();


	if(isset($_POST['enregistrer_ajout_a_textarea_vide'])) {
		if (isset($NON_PROTECT["ajout_a_textarea_vide"])) {
			$ajout_a_textarea_vide=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["ajout_a_textarea_vide"]));
			$ajout_a_textarea_vide=preg_replace('/(\\\r\\\n){3,}+/',"\r\n",$ajout_a_textarea_vide);
			$ajout_a_textarea_vide=preg_replace('/(\\\r)+/',"\r",$ajout_a_textarea_vide);
			$ajout_a_textarea_vide=preg_replace('/(\\\n)+/',"\n",$ajout_a_textarea_vide);

			if(!saveSetting('default_mass_appreciation', $ajout_a_textarea_vide)) {
				$msg.="Erreur lors de l'enregistrement de 'default_mass_appreciation'<br />\n";
			}
		}
	}

	// Synthèse
	$i = '1';
	while ($i < $nb_periode) {
		if ($ver_periode[$i] != "O"){
			if (isset($NON_PROTECT["synthese_".$i])){
				// On enregistre la synthese
				$synthese=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["synthese_".$i]));
		
				//$synthese=my_ereg_replace('(\\\r\\\n)+',"\r\n",$synthese);
				$synthese=preg_replace('/(\\\r\\\n)+/',"\r\n",$synthese);
				$synthese=preg_replace('/(\\\r)+/',"\r",$synthese);
				$synthese=preg_replace('/(\\\n)+/',"\n",$synthese);

				$sql="SELECT 1=1 FROM synthese_app_classe WHERE id_classe='$id_classe' AND periode='$i';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)==0) {
					$sql="INSERT INTO synthese_app_classe SET id_classe='$id_classe', periode='$i', synthese='$synthese';";
					$insert=mysql_query($sql);
					if(!$insert) {$msg.="Erreur lors de l'enregistrement de la synthèse.";}
					//else {$msg.="La synthèse a été enregistrée.";}
				}
				else {
					$sql="UPDATE synthese_app_classe SET synthese='$synthese' WHERE id_classe='$id_classe' AND periode='$i';";
					$update=mysql_query($sql);
					if(!$update) {$msg.="Erreur lors de la mise à jour de la synthèse.";}
					//else {$msg.="La synthèse a été mise à jour.";}
				}
			}
		}
		$i++;
	}

	if (($_SESSION['statut'] == 'scolarite') or ($_SESSION['statut'] == 'secours')) {
		$quels_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c
		WHERE (c.id_classe='$id_classe' AND
		c.login = e.login
		) ORDER BY nom");
	} else {
		$quels_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c, j_eleves_professeurs p
		WHERE (c.id_classe='$id_classe' AND
		c.login = e.login AND
		p.login = c.login AND
		p.professeur = '".$_SESSION['login']."'
		) ORDER BY nom");
	}
	$lignes = mysql_num_rows($quels_eleves);
	$j = '0';
	$pb_record = 'no';
	while($j < $lignes) {
		$reg_eleve_login = mysql_result($quels_eleves, $j, "login");
		$i = '1';
		while ($i < $nb_periode) {
			if ($ver_periode[$i] != "O"){
				$call_eleve = mysql_query("SELECT login FROM j_eleves_classes WHERE (login = '$reg_eleve_login' and id_classe='$id_classe' and periode='$i')");
				$result_test = mysql_num_rows($call_eleve);
				if ($result_test != 0) {

					//=========================
					// AJOUT: boireaus 20071010
					unset($log_eleve);
					$log_eleve=$_POST['log_eleve_'.$i];

					// Récupération du numéro de l'élève dans les saisies:
					$num_eleve=-1;
					//for($k=0;$k<count($log_eleve);$k++){
					for($k=0;$k<$lignes;$k++){
						if(isset($log_eleve[$k])) {
							if("$reg_eleve_login"."_t".$i=="$log_eleve[$k]"){
								$num_eleve=$k;
								break;
							}
						}
					}
					if($num_eleve!=-1){
						//$nom_log = $reg_eleve_login."_t".$i;
						$nom_log = "avis_eleve_".$i."_".$num_eleve;
						//=========================
						// ***** AJOUT POUR LES MENTIONS *****
						unset($mention);
						$id_mention = isset($_POST['mention_eleve_'.$j.'_'.$i]) ? $_POST['mention_eleve_'.$j.'_'.$i] : NULL;
						// ***** FIN DE L'AJOUT POUR LES MENTIONS *****

						$avis = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));
						$test_eleve_avis_query = mysql_query("SELECT * FROM avis_conseil_classe WHERE (login='$reg_eleve_login' AND periode='$i')");
						$test = mysql_num_rows($test_eleve_avis_query);
						if ($test != "0") {
							$sql="UPDATE avis_conseil_classe SET avis='$avis',";
							if(isset($id_mention)) {$sql.="id_mention='$id_mention',";}
							$sql.="statut='' WHERE (login='$reg_eleve_login' AND periode='$i')";
							$register = mysql_query($sql);
						} else {
							$sql="INSERT INTO avis_conseil_classe SET login='$reg_eleve_login',periode='$i',avis='$avis',";
							if(isset($id_mention)) {$sql.="id_mention='$id_mention',";}
							$sql.="statut=''";
							$register = mysql_query($sql);
						}

						if (!$register) {
							$msg.="Erreur lors de l'enregistrement des données de la période $i pour $reg_eleve_login<br />\n";
							$pb_record = 'yes';
						}
					}
				}
			}

			$i++;
		}
		$j++;
	}
	if ($pb_record == 'no') $affiche_message = 'yes';
}
$themessage = 'Des appréciations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont été enregistrées !";
$javascript_specifique = "saisie/scripts/js_saisie";
//**************** EN-TETE *****************
$titre_page = "Saisie des avis | Saisie";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

$tmp_timeout=(getSettingValue("sessionMaxLength"))*60;

?>
<script type="text/javascript" language="javascript">
change = 'no';
</script>
<?php
// On teste si un professeur peut saisir les avis
if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiRubConseilProf")!='yes') {
	die("Droits insuffisants pour effectuer cette opération");
}

// On teste si le service scolarité peut saisir les avis
if (($_SESSION['statut'] == 'scolarite') and getSettingValue("GepiRubConseilScol")!='yes') {
	die("Droits insuffisants pour effectuer cette opération");
}
?>
<form enctype="multipart/form-data" action="saisie_avis1.php" name="form1" method='post'>
<p class='bold'><a href="saisie_avis.php" onclick="return confirm_abandon(this, change, '<?php echo $themessage; ?>')"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Mes classes</a>

<?php

// Ajout lien classe précédente / classe suivante
if($_SESSION['statut']=='scolarite'){
	$sql = "SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
}
elseif($_SESSION['statut']=='professeur'){

	// On a filtré plus haut les profs qui n'ont pas getSettingValue("GepiRubConseilProf")=='yes'
	$sql="SELECT DISTINCT c.id,c.classe FROM classes c,
										j_eleves_classes jec,
										j_eleves_professeurs jep
								WHERE jec.id_classe=c.id AND
										jep.login=jec.login AND
										jep.professeur='".$_SESSION['login']."'
								ORDER BY c.classe;";
}
elseif($_SESSION['statut']=='cpe'){
	// On ne devrait pas arriver ici en CPE...
	// Il n'y a pas de droit de saisie des avis du conseil.
	$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
		p.id_classe = c.id AND
		jec.id_classe=c.id AND
		jec.periode=p.num_periode AND
		jecpe.e_login=jec.login AND
		jecpe.cpe_login='".$_SESSION['login']."'
		ORDER BY classe";
}
elseif($_SESSION['statut'] == 'autre'){
	// On recherche toutes les classes pour ce statut qui n'est accessible que si l'admin a donné les bons droits
	$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe";
}
elseif($_SESSION['statut'] == 'secours'){
	$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe";
}

$chaine_options_classes="";

$cpt_classe=0;
$num_classe=-1;

$res_class_tmp=mysql_query($sql);
$nb_classes_suivies=mysql_num_rows($res_class_tmp);
if($nb_classes_suivies>0){
	$id_class_prec=0;
	$id_class_suiv=0;
	$temoin_tmp=0;
	while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
		if($lig_class_tmp->id==$id_classe){
			// Index de la classe dans les <option>
			$num_classe=$cpt_classe;

			$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
			$temoin_tmp=1;
			if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
				$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
				$id_class_suiv=$lig_class_tmp->id;
			}
			else{
				$id_class_suiv=0;
			}
		}
		else {
			$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
		}
		if($temoin_tmp==0){
			$id_class_prec=$lig_class_tmp->id;
		}

		$cpt_classe++;

	}
}

// =================================
if(isset($id_class_prec)){
	if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe précédente</a>";}
}

if(($chaine_options_classes!="")&&($nb_classes_suivies>1)) {

	echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('id_classe').selectedIndex=$num_classe;
			}
		}
	}
</script>\n";

	//echo " | <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
	echo " | <select name='id_classe' id='id_classe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
	echo $chaine_options_classes;
	echo "</select>\n";
}

if(isset($id_class_suiv)){
	if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe suivante</a>";}
}
//fin ajout lien classe précédente / classe suivante
echo "</p>\n";

echo "</form>\n";


echo "<form enctype='multipart/form-data' action='saisie_avis1.php' method='post'>\n";
echo add_token_field(true);

if ($id_classe) {
	$classe = sql_query1("SELECT classe FROM classes WHERE id = '$id_classe'");
	?>
	<p class= 'grand'>Avis du conseil de classe. Classe : <?php echo $classe; ?></p>
	<?php
	$test_periode_ouverte = 'no';
	$i = "1";
	while ($i < $nb_periode) {
		if ($ver_periode[$i] != "O") {
			$test_periode_ouverte = 'yes';
		}
		$i++;
	}
	?>
	<?php
	if (($_SESSION['statut'] == 'scolarite') or ($_SESSION['statut'] == 'secours')) {
		$appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c
		WHERE (c.id_classe='$id_classe' AND
		c.login = e.login
		) ORDER BY nom");
	} else {
		$appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c, j_eleves_professeurs p
		WHERE (c.id_classe='$id_classe' AND
		c.login = e.login AND
		p.login = c.login AND
		p.professeur = '".$_SESSION['login']."'
		) ORDER BY nom");
	}
	$nombre_lignes = mysql_num_rows($appel_donnees_eleves);



/*
	CommentairesTypesPP
	CommentairesTypesScol
*/

	// Fonction de renseignement du champ qui doit obtenir le focus après validation
	echo "<script type='text/javascript'>

function focus_suivant(num){
	temoin='';
	// La variable 'dernier' peut dépasser de l'effectif de la classe... mais cela n'est pas dramatique
	dernier=num+".$nombre_lignes."
	// On parcourt les champs à partir de celui de l'élève en cours jusqu'à rencontrer un champ existant
	// (pour réussir à passer un élève qui ne serait plus dans la période)
	// Après validation, c'est ce champ qui obtiendra le focus si on n'était pas à la fin de la liste.
	for(i=num;i<dernier;i++){
		suivant=i+1;
		if(temoin==''){
			if(document.getElementById('n'+suivant)){
				document.getElementById('info_focus').value=suivant;
				temoin=suivant;
			}
		}
	}

	document.getElementById('info_focus').value=temoin;
}

</script>\n";

//=========================
$insert_mass_appreciation_type=getSettingValue("insert_mass_appreciation_type");
if ($insert_mass_appreciation_type=="y") {
	// INSERT INTO setting SET name='insert_mass_appreciation_type', value='y';

	$sql="CREATE TABLE IF NOT EXISTS b_droits_divers (login varchar(50) NOT NULL default '', nom_droit varchar(50) NOT NULL default '', valeur_droit varchar(50) NOT NULL default '');";
	$create_table=mysql_query($sql);

	// Pour tester:
	// INSERT INTO b_droits_divers SET login='toto', nom_droit='insert_mass_appreciation_type', valeur_droit='y';

	$sql="SELECT 1=1 FROM b_droits_divers WHERE login='".$_SESSION['login']."' AND nom_droit='insert_mass_appreciation_type' AND valeur_droit='y';";
	$res_droit=mysql_query($sql);
	if(mysql_num_rows($res_droit)>0) {
		$droit_insert_mass_appreciation_type="y";
	}
	else {
		$droit_insert_mass_appreciation_type="n";
	}

	if($droit_insert_mass_appreciation_type=="y") {
		$default_mass_appreciation="";
		if(getSettingValue('default_mass_appreciation')!="") {
			$default_mass_appreciation=getSettingValue('default_mass_appreciation');
		}

		echo "<div style='margin:1em; padding:0.2em; width:40em; border: 1px solid black; background-color: white; font-size: small; text-align:center;'>\n";
		echo "Insérer l'avis-type suivant pour tous les avis vides&nbsp;: ";
		echo "<textarea name='no_anti_inject_ajout_a_textarea_vide' id='ajout_a_textarea_vide' cols='50'>$default_mass_appreciation</textarea><br />\n";

		echo "<input type='checkbox' name='enregistrer_ajout_a_textarea_vide' id='enregistrer_ajout_a_textarea_vide' value='y' /><label for='enregistrer_ajout_a_textarea_vide'>Enregistrer cet avis-type comme avis-type par défaut</label><br />\n";

		echo "<input type='button' name='ajouter_a_textarea_vide' value='Ajouter' onclick='ajoute_a_textarea_vide(); changement()' /><br />\n";

		echo "<input type='button' name='button_vider_tous_les_avis' value='Vider tous les avis' onclick='vider_tous_les_avis(); changement()' /><br />\n";
		echo "</div>\n";

		echo "<script type='text/javascript'>
	function ajoute_a_textarea_vide() {
		champs_textarea=document.getElementsByTagName('textarea');
		//alert('champs_textarea.length='+champs_textarea.length);
		for(i=0;i<champs_textarea.length;i++){
			if(champs_textarea[i].name!='no_anti_inject_ajout_a_textarea_vide') {
				if(champs_textarea[i].value=='') {
					champs_textarea[i].value=document.getElementById('ajout_a_textarea_vide').value;
				}
			}
		}
	}

	function vider_tous_les_avis() {
		var is_confirmed = confirm('ATTENTION : Vous avez demandé à vider tous les avis saisis pour cette classe ! Etes-vous sûr de vouloir vider ces avis ?');
		if(is_confirmed){
			champs_textarea=document.getElementsByTagName('textarea');
			for(i=0;i<champs_textarea.length;i++){
				if(champs_textarea[i].name!='no_anti_inject_ajout_a_textarea_vide') {
					champs_textarea[i].value='';
				}
			}
		}
	}
</script>\n";
	}
}
//=========================


	$k=1;
	$commentaires_type_classe_periode=array();
	while ($k < $nb_periode) {
		// Existe-t-il des commentaires-types pour cette classe et cette période?
		$sql="select 1=1 from commentaires_types WHERE num_periode='$k' AND id_classe='$id_classe'";
		$res_test=mysql_query($sql);
		if(mysql_num_rows($res_test)!=0){
			$commentaires_type_classe_periode[$k]="y";
		}
		else{
			$commentaires_type_classe_periode[$k]="n";
		}
		$k++;
	}


	echo "<table width=\"750\" class='boireaus' border='1' cellspacing='2' cellpadding='5' summary=\"Synthèse de classe\">\n";
	echo "<tr>\n";
	echo "<th width=\"200\"><div align=\"center\"><b>&nbsp;</b></div></th>\n";
	echo "<th><div align=\"center\"><b>Synthèse de classe</b>\n";
	echo "</div></th>\n";
	echo "</tr>\n";
	//========================

	$k='1';
	while ($k < $nb_periode) {
		$sql="SELECT * FROM synthese_app_classe WHERE (id_classe='$id_classe' AND periode='$k');";
		//echo "$sql<br />";
		$res_current_synthese=mysql_query($sql);
		$current_synthese[$k] = @mysql_result($res_current_synthese, 0, "synthese");
		if ($current_synthese[$k] == '') {$current_synthese[$k] = ' -';}

		$k++;
	}

	//$i = "0";
	$num_id=10;

	$k='1';
	$alt=1;
	while ($k < $nb_periode) {
		$alt=$alt*(-1);
		if ($ver_periode[$k] != "N") {
			echo "<tr class='lig$alt'>\n<td><span title=\"$gepiClosedPeriodLabel\">$nom_periode[$k]</span></td>\n";
		} else {
			echo "<tr class='lig$alt'>\n<td>$nom_periode[$k]</td>\n";
		}

		if ($ver_periode[$k] != "O") {
			echo "<td>\n";
			echo "<textarea id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\"  name=\"no_anti_inject_synthese_".$k."\" rows='2' cols='120' class='wrap' onchange=\"changement()\"";
			echo ">";
			//=========================

			echo "$current_synthese[$k]";
			echo "</textarea>\n";
			echo "</td>\n";
		}
		else {
			echo "<td><p class=\"medium\">";
			echo nl2br($current_synthese[$k]);
			echo "</p></td>\n";
		}
		echo "</tr>\n";
		$k++;
	}
	$num_id++;
	//$i++;
	echo "</table>\n<br />\n<br />\n";


	$chaine_test_vocabulaire="";

	$i = "0";
	//$num_id=10;
	while($i < $nombre_lignes) {
		$current_eleve_login = mysql_result($appel_donnees_eleves, $i, "login");
		$current_eleve_nom = mysql_result($appel_donnees_eleves, $i, "nom");
		$current_eleve_prenom = mysql_result($appel_donnees_eleves, $i, "prenom");

		//========================
		// AJOUT boireaus 20071115
		$sql="SELECT elenoet FROM eleves WHERE login='$current_eleve_login';";
		$res_ele=mysql_query($sql);
		$lig_ele=mysql_fetch_object($res_ele);
		$current_eleve_elenoet=$lig_ele->elenoet;

		// Photo...
		$photo=nom_photo($current_eleve_elenoet);
		$temoin_photo="";
		if("$photo"!=""){
			$titre="$current_eleve_nom $current_eleve_prenom";
			$texte="<div align='center'>\n";
			//$texte.="<img src='../photos/eleves/".$photo."' width='150' alt=\"$current_eleve_nom $current_eleve_prenom\" />";
			$texte.="<img src='".$photo."' width='150' alt=\"$current_eleve_nom $current_eleve_prenom\" />";
			$texte.="<br />\n";
			$texte.="</div>\n";

			$temoin_photo="y";

			$tabdiv_infobulle[]=creer_div_infobulle('photo_'.$current_eleve_login,$titre,"",$texte,"",14,0,'y','y','n','n');
		}
		//========================


		//========================
		// AJOUT boireaus 20071115
		/*
		echo "<table width=\"750\" border=1 cellspacing=2 cellpadding=5>\n";
		echo "<tr>\n";
		echo "<td width=\"200\"><div align=\"center\"><b>&nbsp;</b></div></td>\n";
		echo "<td><div align=\"center\"><b>$current_eleve_nom $current_eleve_prenom</b></div></td>\n";
		echo "</tr>\n";
		*/
		echo "<table width=\"750\" class='boireaus' border='1' cellspacing='2' cellpadding='5' summary=\"Elève $current_eleve_nom $current_eleve_prenom\">\n";
		echo "<tr>\n";
		echo "<th width=\"200\"><div align=\"center\"><b>&nbsp;</b></div></th>\n";
		echo "<th><div align=\"center\"><b>$current_eleve_nom $current_eleve_prenom</b>\n";

		//==========================
		// AJOUT: boireaus 20071115
		// Lien photo...
		if($temoin_photo=="y"){
			//echo " <a href='#' onmouseover=\"afficher_div('photo_$current_eleve_login','y',-100,20);\"";
			echo " <a href='#' onmouseover=\"delais_afficher_div('photo_$current_eleve_login','y',-100,20,1000,10,10);\"";
			echo ">";
			echo "<img src='../images/icons/buddy.png' alt='$current_eleve_nom $current_eleve_prenom' />";
			echo "</a>";
		}
		//==========================

		echo "</div></th>\n";
		echo "</tr>\n";
		//========================

		$k='1';
		while ($k < $nb_periode) {
			$current_eleve_avis_query[$k]= mysql_query("SELECT * FROM avis_conseil_classe WHERE (login='$current_eleve_login' AND periode='$k')");
			$current_eleve_avis_t[$k] = @mysql_result($current_eleve_avis_query[$k], 0, "avis");
			// ***** AJOUT POUR LES MENTIONS *****
			$current_eleve_mention_t[$k] = @mysql_result($current_eleve_avis_query[$k], 0, "id_mention");
			// ***** FIN DE L'AJOUT POUR LES MENTIONS *****
			$current_eleve_login_t[$k] = $current_eleve_login."_t".$k;
			$k++;
		}

		$k='1';
		$alt=1;
		while ($k < $nb_periode) {
			$alt=$alt*(-1);
			if ($ver_periode[$k] != "N") {
				echo "<tr class='lig$alt'>\n<td><span title=\"$gepiClosedPeriodLabel\">$nom_periode[$k]</span></td>\n";
			} else {
				echo "<tr class='lig$alt'>\n<td>$nom_periode[$k]</td>\n";
			}
			if ($ver_periode[$k] != "O") {
				$call_eleve = mysql_query("SELECT login FROM j_eleves_classes WHERE (login = '$current_eleve_login' and id_classe='$id_classe' and periode='$k')");
				$result_test = mysql_num_rows($call_eleve);
				if ($result_test != 0) {
					echo "<td>\n";
					echo "<input type='hidden' name='log_eleve_".$k."[$i]' value=\"".$current_eleve_login_t[$k]."\" />\n";
					echo "<textarea id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\"  name=\"no_anti_inject_avis_eleve_".$k."_".$i."\" rows='2' cols='120' class='wrap' onchange=\"changement()\"";

					echo " onBlur=\"ajaxVerifAppreciations('".$current_eleve_login_t[$k]."', '".$id_classe."', 'n".$k.$num_id."');\"";
		
					$chaine_test_vocabulaire.="ajaxVerifAppreciations('".$current_eleve_login_t[$k]."', '".$id_classe."', 'n".$k.$num_id."');\n";

					echo ">";
					//=========================

					echo "$current_eleve_avis_t[$k]";
					echo "</textarea>\n";
					// ***** AJOUT POUR LES MENTIONS *****
					if(test_existence_mentions_classe($id_classe)) {
						echo ucfirst($gepi_denom_mention)." : ";
						echo champ_select_mention('mention_eleve_'.$i.'_'.$k,$id_classe,$current_eleve_mention_t[$k]);
						/*
						$selectedF="";
						$selectedM="";
						$selectedE="";
						$selectedB="";
						if($current_eleve_mention_t[$k]=='F') {$selectedF=" selected";}
						else if($current_eleve_mention_t[$k]=='M') {$selectedM=" selected";}
						else if($current_eleve_mention_t[$k]=='E') {$selectedE=" selected";}
						else {$selectedB=" selected";}
						echo "<select name='mention_eleve_".$i."_".$k."'>\n";
						echo "<option value='B'$selectedB> </option>\n";
						echo "<option value='E'$selectedE>Encouragements</option>\n";
						echo "<option value='M'$selectedM>Mention honorable</option>\n";
						echo "<option value='F'$selectedF>Félicitations</option>\n";
						echo "</select>\n";
						*/
					}
					// **** FIN DE L'AJOUT POUR LES MENTIONS ****

					//echo "<a href='#' onClick=\"document.getElementById('textarea_courant').value='no_anti_inject_".$current_eleve_login_t[$k]."';afficher_div('commentaire_type','y',30,-150);return false;\">Ajout CC</a>";

					if((file_exists('saisie_commentaires_types.php'))
						&&(($_SESSION['statut'] == 'professeur')&&(getSettingValue("GepiRubConseilProf")=='yes')&&(getSettingValue('CommentairesTypesPP')=='yes'))
						||(($_SESSION['statut'] == 'scolarite')&&(getSettingValue("GepiRubConseilScol")=='yes')&&(getSettingValue('CommentairesTypesScol')=='yes'))) {

						if($commentaires_type_classe_periode[$k]=="y"){
							echo "<a href='#' onClick=\"document.getElementById('textarea_courant').value='n".$k.$num_id."';afficher_div('commentaire_type','y',30,-50);return false;\">Ajouter un commentaire-type</a>\n";
						}
					}

					echo "<div id='div_verif_n".$k.$num_id."' style='color:red;'></div>\n";

					echo "</td>\n";
				} else {
					echo "<td><p>$current_eleve_avis_t[$k]&nbsp;</p></td>\n";
				}
			}
			else {
				echo "<td><p class=\"medium\">";
				echo "$current_eleve_avis_t[$k]";
				echo "</p>\n";
				// ***** AJOUT POUR LES MENTIONS *****
				if((!isset($tableau_des_mentions_sur_le_bulletin))||(!is_array($tableau_des_mentions_sur_le_bulletin))||(count($tableau_des_mentions_sur_le_bulletin)==0)) {
					$tableau_des_mentions_sur_le_bulletin=get_mentions();
				}

				if(isset($tableau_des_mentions_sur_le_bulletin[$current_eleve_mention_t[$k]])) {
					echo "<p class=\"medium\"><b> ".ucfirst($gepi_denom_mention)." : ";
					echo $tableau_des_mentions_sur_le_bulletin[$current_eleve_mention_t[$k]];
					echo "</b></p>\n";
				}
				// ***** FIN DE L'AJOUT POUR LES MENTIONS *****
				echo "</td>\n";
			}
			echo "</tr>\n";
			$k++;
		}
		//echo "</tr>";
		$num_id++;
		$i++;
		echo "</table>\n<br />\n<br />\n";

	}


	if((file_exists('saisie_commentaires_types.php'))
		&&(($_SESSION['statut'] == 'professeur')&&(getSettingValue("GepiRubConseilProf")=='yes')&&(getSettingValue('CommentairesTypesPP')=='yes'))
		||(($_SESSION['statut'] == 'scolarite')&&(getSettingValue("GepiRubConseilScol")=='yes')&&(getSettingValue('CommentairesTypesScol')=='yes'))) {
		//include('saisie_commentaires_types.php');
		//include('saisie_commentaires_types2.php');
		include('saisie_commentaires_types2b.php');
		//echo "AAAAAAAAAAAAA";
	}


	if ($test_periode_ouverte == 'yes') {
		?>
		<input type='hidden' name='is_posted' value="yes" />
		<input type='hidden' name='id_classe' value=<?php echo "$id_classe";?> />
		<center><div id="fixe"><input type='submit' value='Enregistrer' />

		<!-- DIV destiné à afficher un décompte du temps restant pour ne pas se faire piéger par la fin de session -->
		<div id='decompte'></div>

		<!-- Champ destiné à recevoir la valeur du champ suivant celui qui a le focus pour redonner le focus à ce champ après une validation -->
		<input type='hidden' id='info_focus' name='champ_info_focus' value='' size='3' />

		</div></center>
		<br /><br /><br /><br />

		<?php
			// Il faudra permettre de n'afficher ce décompte que si l'administrateur le souhaite.

			echo "<script type='text/javascript'>

$chaine_test_vocabulaire;

cpt=".$tmp_timeout.";
compte_a_rebours='y';

function decompte(cpt){
	if(compte_a_rebours=='y'){
		document.getElementById('decompte').innerHTML=cpt;
		if(cpt>0){
			cpt--;
		}

		setTimeout(\"decompte(\"+cpt+\")\",1000);
	}
	else{
		document.getElementById('decompte').style.display='none';
	}
}

decompte(cpt);

";

		// Après validation, on donne le focus au champ qui suivait celui qui vien d'être rempli
		if(isset($_POST['champ_info_focus'])){
			if($_POST['champ_info_focus']!=""){
				echo "// On positionne le focus...
			document.getElementById('n".$_POST['champ_info_focus']."').focus();
		\n";
			}
		}

		echo "</script>\n";

	}
}

?>
<div id="debug_fixe" style="position: fixed; bottom: 20%; right: 5%;"></div>
</form>
<?php require("../lib/footer.inc.php");?>
