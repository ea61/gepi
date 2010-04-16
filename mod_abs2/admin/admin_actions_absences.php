<?php
/*
 *
 * $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel, Josselin Jacquard
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

$niveau_arbo = 2;
// Initialisations files
include("../../lib/initialisationsPropel.inc.php");
require_once("../../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
};

// Check access
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}

if (empty($_GET['action']) and empty($_POST['action'])) { $action="";}
    else { if (isset($_GET['action'])) {$action=$_GET['action'];} if (isset($_POST['action'])) {$action=$_POST['action'];} }
if (empty($_GET['id']) and empty($_POST['id'])) { $id="";}
    else { if (isset($_GET['id'])) {$id=$_GET['id'];} if (isset($_POST['id'])) {$id=$_POST['id'];} }
if (empty($_GET['statut_id']) and empty($_POST['statut_id'])) { $statut_id="";}
    else { if (isset($_GET['statut_id'])) {$statut_id=$_GET['statut_id'];} if (isset($_POST['statut_id'])) {$statut_id=$_POST['statut_id'];} }
if (empty($_GET['nom']) and empty($_POST['nom'])) { $nom="";}
    else { if (isset($_GET['nom'])) {$nom=$_GET['nom'];} if (isset($_POST['nom'])) {$nom=$_POST['nom'];} }
if (empty($_GET['commentaire']) and empty($_POST['commentaire'])) { $commentaire="";}
    else { if (isset($_GET['commentaire'])) {$commentaire=$_GET['commentaire'];} if (isset($_POST['commentaire'])) {$commentaire=$_POST['commentaire'];} }

//$Action = new AbsenceEleveAction();
$Action = AbsenceEleveActionQuery::create()->findPk($id);
if ($action == 'supprimer') {
    if ($Action != null) {
	$Action->delete();
    }
} elseif ($action == "monter") {
    if ($Action != null) {
	$Action->moveUp();
    }
} elseif ($action == 'descendre') {
    if ($Action != null) {
	$Action->moveDown();
    }
} elseif ($action == 'ajouterdefaut') {
    include("function.php");
    ajoutActionsParDefaut();
} else {
    if ($nom != '') {
	$Action = AbsenceEleveActionQuery::create()->findPk($id);
	if ($Action == null) {
	    $Action = new AbsenceEleveAction();
	}
	$Action->setNom(unslashes($nom));
	$Action->setCommentaire(unslashes($commentaire));
	$Action->save();
    }
}

// header
$titre_page = "Gestion des Actions d'absence";
require_once("../../lib/header.inc");

echo "<p class=bold>";
echo "<a href=\"index.php\">";
echo "<img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>";
?>

<div style="text-align:center">
    <h2>D�finition des Actions d'absence</h2>
<?php if ($action == "ajouter" OR $action == "modifier" OR $action == "supprimer_statut") { ?>
<div style="text-align:center">
    <?php
    	if($action=="ajouter") { 
	    echo "<h2>Ajout d'une Action</h2>";
	} elseif ($action=="modifier") {
	    echo "<h2>Modifier une Action</h2>";
	}
	?>

    <form action="admin_actions_absences.php" method="post" name="form2" id="form2">
      <table cellpadding="2" cellspacing="2" class="menu">
        <tr>
          <td>Nom (obligatoire)</td>
          <td>Commentaire (facultatif)</td>
       </tr>
        <tr>
          <td>
           <?php
           //$Action = AbsenceEleveActionQuery::create()->findPk($id);
	   if ($Action != null) { ?>
	      <input name="id" type="hidden" id="id" value="<?php echo $id ?>" />
	   <?php } ?>
	      <input name="nom" type="text" id="nom" size="14" maxlength="50" value="<?php  if ($Action != null) {echo $Action->getNom();} ?>" />
           </td>
           <td><textarea name="commentaire"><?php  if ($Action != null) {echo $Action->getCommentaire();} ?></textarea></td>
        </tr>
      </table>
     <input type="submit" name="Submit" value="Enregistrer" />
    </form>
<br/><br/>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php
} ?>
	<a href="admin_actions_absences.php?action=ajouter"><img src='../../images/icons/add.png' alt='' class='back_link' /> Ajouter une nouvelle action</a>
	<br/><br/>
	<a href="admin_actions_absences.php?action=ajouterdefaut"><img src='../../images/icons/add.png' alt='' class='back_link' /> Ajouter les actions par defaut</a>
	<br/><br/>
    <table cellpadding="0" cellspacing="1" class="menu">
      <tr>
        <td>Nom</td>
        <td>Commentaire</td>
        <td style="width: 25px;"></td>
        <td style="width: 25px;"></td>
        <td style="width: 25px;"></td>
        <td style="width: 25px;"></td>
      </tr>
    <?php
    $Action_collection = new PropelCollection();
    $Action_collection = AbsenceEleveActionQuery::create()->findList();
    $Action = new AbsenceEleveAction();
    $i = '1';
    foreach ($Action_collection as $Action) { ?>
        <tr>
	  <td><?php echo $Action->getNom(); ?></td>
	  <td><?php echo $Action->getCommentaire(); ?></td>
          <td><a href="admin_actions_absences.php?action=modifier&amp;id=<?php echo $Action->getId(); ?>"><img src="../../images/icons/configure.png" title="Modifier" border="0" alt="" /></a></td>
          <td><a href="admin_actions_absences.php?action=supprimer&amp;id=<?php echo $Action->getId(); ?>" onClick="return confirm('Etes-vous s�r de vouloir supprimer cette action ?')"><img src="../../images/icons/delete.png" width="22" height="22" title="Supprimer" border="0" alt="" /></a></td>
          <td><a href="admin_actions_absences.php?action=monter&amp;id=<?php echo $Action->getId(); ?>"><img src="../../images/up.png" width="22" height="22" title="monter" border="0" alt="" /></a></td>
          <td><a href="admin_actions_absences.php?action=descendre&amp;id=<?php echo $Action->getId(); ?>"><img src="../../images/down.png" width="22" height="22" title="descendre" border="0" alt="" /></a></td>
        </tr>
     <?php } ?>
    </table>
    <br/><br/>
</div>


<?php require("../../lib/footer.inc.php");?>