<?php
/*
$Id$
 */

//echo "<ul class='css-tabs' id='menutabs'>\n";

// $onglet_abs = reset(explode("?", basename($_SERVER["REQUEST_URI"])));
$basename_serveur=explode("?", basename($_SERVER["REQUEST_URI"]));
$onglet_abs = reset($basename_serveur);

$_SESSION['abs2_onglet'] = $onglet_abs;
// Tests � remplacer par des tests sur les droits attribu�s aux statuts
if(($_SESSION['statut']=='cpe')||
    ($_SESSION['statut']=='scolarite')) {

    echo "<ul class='css-tabs' id='menutabs' style='font-size:85%'>\n";

    echo "<li><a href='tableau_des_appels.php' ";
    if($onglet_abs=='tableau_des_appels.php') {echo "class='current' ";}
    echo "title='Tableau des appels'>Tableau des appels</a></li>\n";

    echo "<li><a href='absences_du_jour.php' ";
    if($onglet_abs=='absences_du_jour.php') {echo "class='current' ";}
    echo "title='Absences du jour'>Absences du jour</a></li>\n";

    echo "<li><a href='bilan_du_jour.php' ";
    if($onglet_abs=='bilan_du_jour.php') {echo "class='current' ";}
    echo "title='Bilan du jour'>Bilan du jour</a></li>\n";

    echo "<li><a href='extraction_saisies.php' ";
    if($onglet_abs=='extraction_saisies.php') {echo "class='current' ";}
    echo "title='Extraction des saisies'>Extraction des saisies</a></li>\n";

    echo "</ul>\n";

}

?>