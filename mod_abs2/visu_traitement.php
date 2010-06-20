<?php
/**
 *
 * @version $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stephane Boireau, Julien Jocal
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

// Initialisation des feuilles de style apr�s modification pour am�liorer l'accessibilit�
$accessibilite="y";

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
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

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On v�rifie si le module est activ�
if (getSettingValue("active_module_absence")!='2') {
    die("Le module n'est pas activ�.");
}

if ($utilisateur->getStatut()!="cpe") {
    die("acces interdit");
}

//r�cup�ration des param�tres de la requ�te
$id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :(isset($_SESSION["id_traitement"]) ? $_SESSION["id_traitement"] : NULL));
if (isset($id_traitement) && $id_traitement != null) $_SESSION['id_traitement'] = $id_traitement;

//==============================================
$style_specifique[] = "mod_abs2/lib/abs_style";
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";
$titre_page = "Les absences";
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

include('menu_abs2.inc.php');
//===========================
echo "<div class='css-panes' id='containDiv' style='overflow : auto;'>\n";


$traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
if ($traitement == null) {
    $criteria = new Criteria();
    $criteria->addDescendingOrderByColumn(AbsenceElevetraitementPeer::UPDATED_AT);
    $criteria->setLimit(1);
    $traitement = $utilisateur->getAbsenceEleveTraitements($criteria)->getFirst();
    if ($traitement == null) {
	echo "traitement non trouv�e";
	die();
    }
}

if (isset($message_enregistrement)) {
    echo $message_enregistrement;
}

echo '<table class="normal">';
echo '<TBODY>';
echo '<tr><TD>';
echo 'N�';
echo '</TD><TD>';
echo $traitement->getPrimaryKey();
echo '</TD></tr>';

echo '<tr><TD>';
echo 'Saisies : ';
echo '</TD><TD>';
echo '<table>';
$eleve_prec_id = null;
if ($traitement->getAbsenceEleveSaisies()->isEmpty()) {
    echo '<form method="post" action="liste_saisies_selection_traitement.php">';
    echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
    echo '<button type="submit">Ajouter</button>';
    echo '</form>';
}
foreach ($traitement->getAbsenceEleveSaisies() as $saisie) {
    //$saisie = new AbsenceEleveSaisie();
    if ($saisie->getEleve() == null) {
	echo '<tr><td>';
	echo 'Aucune absence';
	if ($saisie->getGroupe() != null) {
	    echo ' pour le groupe ';
	    echo $saisie->getGroupe()->getDescription();
	}
	if ($saisie->getClasse() != null) {
	    echo ' pour la classe ';
	    echo $saisie->getClasse()->getNomComplet();
	}
	if ($saisie->getAidDetails() != null) {
	    echo ' pour l\'aid ';
	    echo $saisie->getClasse()->getNomComplet();
	}
	echo '<tr><td>';
    } elseif ($eleve_prec_id != $saisie->getEleve()->getPrimaryKey()) {
	if (!$traitement->getAbsenceEleveSaisies()->isFirst()) {
	    echo '</td></tr>';
	}
	echo '<tr><td>';
	echo '<div>';
	echo $saisie->getEleve()->getCivilite().' '.$saisie->getEleve()->getNom().' '.$saisie->getEleve()->getPrenom();
	if ((getSettingValue("active_module_trombinoscopes")=='y') && $saisie->getEleve() != null) {
	    $nom_photo = $saisie->getEleve()->getNomPhoto(1);
	    $photos = "../photos/eleves/".$nom_photo;
	    if (($nom_photo == "") or (!(file_exists($photos)))) {
		    $photos = "../mod_trombinoscopes/images/trombivide.jpg";
	    }
	    $valeur = redimensionne_image_petit($photos);
	    echo ' <img src="'.$photos.'" style="width: '.$valeur[0].'px; height: '.$valeur[1].'px; border: 0px; vertical-align: middle;" alt="" title="" />';
	}
	echo '<div style="float: right; margin-top:0.35em; margin-left:0.2em;">';
	echo '<form method="post" action="liste_saisies_selection_traitement.php">';
	echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
	echo '<input type="hidden" name="filter_eleve" value="'.$saisie->getEleve()->getNom().'"/>';
	echo '<button type="submit">Ajouter</button>';
	echo '</form>';
	echo '</div>';
	echo '</div>';
	echo '<br/>';
    }
    echo '<div>';
    echo $saisie->getDateDescription();
    echo '<div style="float: right;  margin-top:-0.22em; margin-left:0.2em;">';
    echo '<form method="post" action="enregistrement_modif_traitement.php">';
    echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
    echo '<input type="hidden" name="modif" value="enlever_saisie"/>';
    echo '<input type="hidden" name="id_saisie" value="'.$saisie->getPrimaryKey().'"/>';
    echo '<button type="submit">Enlever</button>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
    if (!$traitement->getAbsenceEleveSaisies()->isLast()) {
	echo '<br/>';
    }
    $eleve_prec_id = $saisie->getEleve()->getPrimaryKey();
}
echo '</table>';

echo '</TD></tr>';

echo '<tr><TD>';
echo 'Type : ';
echo '</TD><TD>';
//on ne modifie le type que si aucun envoi n'a ete fait
if ($traitement->getAbsenceEleveEnvois()->isEmpty()) {
    $type_autorises = AbsenceEleveTypeStatutAutoriseQuery::create()->filterByStatut($utilisateur->getStatut())->find();
    if ($type_autorises->count() != 0) {
	echo '<form method="post" action="enregistrement_modif_traitement.php">';
	echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
	echo '<input type="hidden" name="modif" value="type"/>';
	echo ("<select name=\"id_type\">");
	echo "<option value='-1'></option>\n";
	$type_in_list = false;
	foreach ($type_autorises as $type) {
	    //$type = new AbsenceEleveTypeStatutAutorise();
		echo "<option value='".$type->getAbsenceEleveType()->getId()."'";
		if ($type->getAbsenceEleveType()->getId() == $traitement->getATypeId()) {
		    echo "selected";
		    $type_in_list = true;
		}
		echo ">";
		echo $type->getAbsenceEleveType()->getNom();
		echo "</option>\n";
	}
	if (!$type_in_list && $traitement->getAbsenceEleveType() != null) {
	    echo "<option value='".$traitement->getAbsenceEleveType()->getId()."'";
	    echo "selected";
	    echo ">";
	    echo $traitement->getAbsenceEleveType()->getNom();
	    echo "</option>\n";
	}
	echo "</select>";
	echo '<button type="submit">Modifier</button>';
	echo '</form>';
    }
} else {
    if ($traitement->getAbsenceEleveType() != null) {
	echo $traitement->getAbsenceEleveType()->getNom();
    }
}
echo '</TD></tr>';

echo '<tr><TD>';
echo 'Motif : ';
echo '</TD><TD>';
$motifs = AbsenceEleveMotifQuery::create()->find();
echo '<form method="post" action="enregistrement_modif_traitement.php">';
echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
echo '<input type="hidden" name="modif" value="motif"/>';
echo ("<select name=\"id_motif\">");
echo "<option value='-1'></option>\n";
foreach ($motifs as $motif) {
    //$justification = new AbsenceEleveJustification();
    echo "<option value='".$motif->getId()."'";
    if ($motif->getId() == $traitement->getAMotifId()) {
	echo "selected";
    }
    echo ">";
    echo $motif->getNom();
    echo "</option>\n";
}
echo "</select>";
echo '<button type="submit">Modifier</button>';
echo '</form>';
echo '</TD></tr>';

echo '<tr><TD>';
echo 'Justification : ';
echo '</TD><TD>';
$justifications = AbsenceEleveJustificationQuery::create()->find();
echo '<form method="post" action="enregistrement_modif_traitement.php">';
echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
echo '<input type="hidden" name="modif" value="justification"/>';
echo ("<select name=\"id_justification\">");
echo "<option value='-1'></option>\n";
foreach ($justifications as $justification) {
    //$justification = new AbsenceEleveJustification();
    echo "<option value='".$justification->getId()."'";
    if ($justification->getId() == $traitement->getAJustificationId()) {
	echo "selected";
    }
    echo ">";
    echo $justification->getNom();
    echo "</option>\n";
}
echo "</select>";
echo '<button type="submit">Modifier</button>';
echo '</form>';
echo '</TD></tr>';

echo '<tr><TD>';
echo 'Commentaire : ';
echo '</TD><TD>';
echo '<form method="post" action="enregistrement_modif_traitement.php">';
echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
echo '<input type="hidden" name="modif" value="commentaire"/>';
echo '<input type="text" name="commentaire" size="30" value="'.$traitement->getCommentaire().'" />';
echo '<button type="submit">Modifier</button>';
echo '</form>';
echo '</TD></tr>';

echo '<tr><TD>';
echo 'Envois : ';
echo '</TD><TD>';
echo '<table>';
$eleve_prec_id = null;
foreach ($traitement->getAbsenceEleveEnvois() as $envoi) {
    $envoi = new AbsenceEleveEnvoi();
    echo '<tr><td>';
    echo (strftime("%a %d %b %Y %H:%M", $envoi->getDateEnvoi('U')));
    echo ' '.$envoi->getCommentaire();
    echo ' '.$envoi->getStatutEnvoi();
    echo '</td></tr>';
}
echo '</table>';
echo '</TD></tr>';

echo '<tr><TD>';
echo 'Cr�� par : ';
echo '</TD><TD>';
if ($traitement->getUtilisateurProfessionnel() != null) {
    echo $traitement->getUtilisateurProfessionnel()->getCivilite();
    echo ' ';
    echo $traitement->getUtilisateurProfessionnel()->getNom();
}
echo '</TD></tr>';

echo '<tr><TD>';
echo 'Cr�� le : ';
echo '</TD><TD>';
echo (strftime("%a %d %b %Y %H:%M", $traitement->getCreatedAt('U')));
echo '</TD></tr>';

if ($traitement->getCreatedAt() != $traitement->getUpdatedAt()) {
    echo '<tr><TD>';
    echo 'Modifi�e le : ';
    echo '</TD><TD>';
    echo (strftime("%a %d %b %Y %H:%M", $traitement->getUpdatedAt('U')));
    echo '</TD></tr>';
}

echo '</TBODY>';

echo '</table>';

//fonction redimensionne les photos petit format
function redimensionne_image_petit($photo)
 {
    // prendre les informations sur l'image
    $info_image = getimagesize($photo);
    // largeur et hauteur de l'image d'origine
    $largeur = $info_image[0];
    $hauteur = $info_image[1];
    // largeur et/ou hauteur maximum � afficher
             $taille_max_largeur = 35;
             $taille_max_hauteur = 35;

    // calcule le ratio de redimensionnement
     $ratio_l = $largeur / $taille_max_largeur;
     $ratio_h = $hauteur / $taille_max_hauteur;
     $ratio = ($ratio_l > $ratio_h)?$ratio_l:$ratio_h;

    // d�finit largeur et hauteur pour la nouvelle image
     $nouvelle_largeur = $largeur / $ratio;
     $nouvelle_hauteur = $hauteur / $ratio;

   // on renvoit la largeur et la hauteur
    return array($nouvelle_largeur, $nouvelle_hauteur);
 }
?>