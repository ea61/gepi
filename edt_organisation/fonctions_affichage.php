<?php

/**
 * Fonctions pour l' affichage des EdT
 *
 * @version     $Id: fonctions_affichage.php $
 * @package		GEPI
 * @subpackage	EmploisDuTemps
 * @copyright	Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal, Pascal Fautrero
 * @license		GNU/GPL, see COPYING.txt
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

// =============================================================================
//
//                                  PROTOS
//
// void     function AfficherEDT($tab_data, $type_edt, $login_edt, $period)
// void     function AfficheBarCommutateurSemaines($login_edt, $visioedt, $type_edt_2, $week_min, $week_selected)
// void     function AfficheBarCommutateurPeriodes($login_edt, $visioedt, $type_edt_2)
// void     function AfficheImprimante($display_print)
// void     function AfficheIconePlusNew($type_edt,$heuredeb_dec,$login_edt,$jour_sem,$id_creneaux, $period)
// void     function AfficheIconePlusAdd($type_edt,$heuredeb_dec,$login_edt,$jour_sem,$id_creneaux, $period)
// void     function AfficheModifierIcone($type_edt,$login_edt,$id_cours, $period)
// void     function AfficheEffacerIcone($type_edt,$login_edt,$id_cours, $period)


// =============================================================================
//
//                  Permet d'afficher un emploi du temps 
//
// =============================================================================
function AfficherEDT($tab_data, $entetes, $creneaux, $type_edt, $login_edt, $period) 
{

    echo ("<div class=\"fenetre\">\n");

    echo("<div class=\"contenu\">

		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
		<div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>

        <div class=\"tableau\">\n");


// ===== affichage des colonnes
// ===== Les "display:none" sont utilis�s pour l'accessibilit�
    $jour = 0;
    $isIconeAddUsable = true;
    while (isset($entetes['entete'][$jour])) {

        echo("<div class=\"colonne".$creneaux['nb_creneaux']."\">\n");
        $jour_sem = $entetes['entete'][$jour];
        echo("<h2 class=\"entete\"><div class=\"cadre\"><strong>".$jour_sem."</strong></div></h2>\n");
        $index_box = 0;
        while (isset($tab_data[$jour]['type'][$index_box]))
        {
            if ($tab_data[$jour]['type'][$index_box] == "vide") {
                
                echo("<div class=\"".$tab_data[$jour]['duree'][$index_box]."\">");
                echo("<div style=\"display:none;\">".$tab_data[$jour]['affiche_creneau'][$index_box]." - dur�e = ".$tab_data[$jour]['duree_valeur'][$index_box]." heure(s)</div>\n");
                echo ("<div class=\"cadre\">\n");
                echo ("<div class=\"ButtonBar\">");
                AfficheIconePlusNew($type_edt,$tab_data[$jour]['heuredeb_dec'][$index_box],$login_edt,$jour_sem,$tab_data[$jour]['id_creneau'][$index_box], $period);
                echo ("</div>\n");
                echo ("</div></div>\n");  
 
            }
            else if ($tab_data[$jour]['type'][$index_box] == "erreur")
            {
    
                echo("<div class=\"".$tab_data[$jour]['duree'][$index_box]."\">");
                echo("<div style=\"display:none;\">".$tab_data[$jour]['affiche_creneau'][$index_box]." - dur�e = ".$tab_data[$jour]['duree_valeur'][$index_box]." heure(s)</div>\n");
                echo("<div class=\"cadreRouge\">\n");
                echo $tab_data[$jour]['contenu'][$index_box];
                echo ("<div class=\"ButtonBar\">");
                echo ("</div>\n");
                echo ("</div></div>\n");  
    
            }
            else if ($tab_data[$jour]['type'][$index_box] == "conteneur")
            {
                echo("<div class=\"".$tab_data[$jour]['duree'][$index_box]."\">\n");
                $isIconeAddUsable = false;
        
            }
            else if ($tab_data[$jour]['type'][$index_box] == "cours")
            {
                echo("<div class=\"".$tab_data[$jour]['duree'][$index_box]."\">");
                echo("<div style=\"display:none;\">".$tab_data[$jour]['affiche_creneau'][$index_box]." - dur�e = ".$tab_data[$jour]['duree_valeur'][$index_box]." heure(s)</div>\n");
                echo ("<div class=\"".$tab_data[$jour]['couleur'][$index_box]."\">");
                echo $tab_data[$jour]['contenu'][$index_box];
                echo ("<div class=\"ButtonBar\">");
                AfficheEffacerIcone($type_edt,$login_edt,$tab_data[$jour]['id_cours'][$index_box], $period);
                AfficheModifierIcone($type_edt,$login_edt,$tab_data[$jour]['id_cours'][$index_box], $period);
                if ($isIconeAddUsable)
                {
                    AfficheIconePlusAdd($type_edt,0,$login_edt,$jour_sem,$tab_data[$jour]['id_creneau'][$index_box], $period);
                }
                echo ("</div>\n");
                echo ("</div></div>\n");   
   
            }
            else if ($tab_data[$jour]['type'][$index_box] == "fin_conteneur")
            {
                echo("</div>\n");
                $isIconeAddUsable = true;
            }
            else 
            {
                // ========= type de box non impl�ment�e
    
            }


            $index_box++;
        }

        echo("</div>\n");
        $jour++;
    }

// ===== affichage de la colonne cr�neaux

    echo ("<div class=\"creneaux".$creneaux['nb_creneaux']."\">\n");
    echo ("<div class=\"entete_creneaux\"></div>\n");
    for ($i = 0; $i < $creneaux['nb_creneaux']; $i++)
    {
        echo("<div class=\"horaires\"><div class=\"cadre\"><strong>".$creneaux['creneaux'][$i]."</strong></div></div>\n");
    }

    echo("</div></div><div class=\"spacer\"></div></div></div>");

}

// ======================================================
//
//      Lorsqu'on est en mode "emplois du temps semaines"
//      permet de passer d'une semaine � l'autre
//
// ======================================================
function AfficheBarCommutateurSemaines($login_edt, $visioedt, $type_edt_2, $week_min, $week_selected)
{
    $range = 8;

    if ($week_min == NULL) {
        if (($week_selected < 33 + $range) AND ($week_selected >= 33)) {
            $week_min = 33;
        }
        else {
            $week_min = $week_selected - $range;
        }
    }
    if ($week_min < 1) {
        $week_min = $week_min + 1 + NumLastWeek();
    }
    if (($week_selected < 28) AND ($week_selected >= 28 - $range)) {
        $week_max = 28;
    }
    else {
        $week_max = $week_min + $range*2;
    }


    echo ("<div id=\"ButtonBarArrows\">");
    echo "<ul style=\"float:left;margin:5px;list-style-type:none;border:0px solid black;\">";
    for ($i = $week_min; $i < $week_max ; $i++) {
        if ($i > NumLastWeek()) {
            $j = $i - NumLastWeek();
        }
        else {
            $j = $i;
        }
        if ($j == $week_selected) {
            echo ("<li class=\"WeekCellYellow\"><a href=\"./index_edt.php?week_selected=".$j."&amp;visioedt=".$visioedt."&amp;login_edt=".$login_edt."&amp;type_edt_2=".$type_edt_2."\">".$j."</a></li>");        
        }
        else {
            echo ("<li class=\"WeekCellWhite\"><a href=\"./index_edt.php?week_selected=".$j."&amp;visioedt=".$visioedt."&amp;login_edt=".$login_edt."&amp;type_edt_2=".$type_edt_2."\">".$j."</a></li>");        
        }
    }
    echo "</ul>";
    echo ("</div>");

    echo "<div class=\"spacer\"></div>";

    echo "<div style=\"float:left;width:100%;\";>";
    echo "<p>Semaine s�lectionn�e : ";
    $tab = RecupereLundisVendredis();
    echo $tab[$week_selected-1]["lundis"]." - ";      
    echo $tab[$week_selected-1]["vendredis"];
    echo "</p>";
    echo "</div>";
    echo "<div class=\"spacer\"></div>";
}



// ======================================================
//
//      Lorsqu'on est en mode "emplois du temps p�riodes"
//      permet de passer d'une p�riode � l'autre
//
// ======================================================
function AfficheBarCommutateurPeriodes($login_edt, $visioedt, $type_edt_2)
{
    if (isset($_SESSION['period_id'])) {
        $period_next = ReturnNextIdPeriod($_SESSION['period_id']);
        $period_previous = ReturnPreviousIdPeriod($_SESSION['period_id']);
    }
    else {
        $period_next = ReturnNextIdPeriod(ReturnIdPeriod(date("U")));
        $period_previous = ReturnPreviousIdPeriod(ReturnIdPeriod(date("U")));

    }
    echo ("<div id=\"ButtonBarArrows\">");

    echo "<ul class=\"ButtonBarArrowLeft\">";
    echo "<li class=\"ButtonBarArrowLeft1\">";
    echo ("<a href=\"./index_edt.php?period_id=".$period_previous."&amp;visioedt=".$visioedt."&amp;login_edt=".$login_edt."&amp;type_edt_2=".$type_edt_2."\"></a>");
    echo "</li>";
    echo "</ul>";


    $req_periode = mysql_query("SELECT nom_calendrier FROM edt_calendrier WHERE id_calendrier='".$_SESSION['period_id']."'");
    $rep_periode = mysql_fetch_array($req_periode);

    echo "<ul class=\"Period\">";
    echo "<li>P�riode visualis�e : ".$rep_periode['nom_calendrier']."</li>";
    echo "</ul>";


    echo "<ul class=\"ButtonBarArrowRight\">";
    echo "<li class=\"ButtonBarArrowRight1\">";
    echo ("<a href=\"./index_edt.php?period_id=".$period_next."&amp;visioedt=".$visioedt."&amp;login_edt=".$login_edt."&amp;type_edt_2=".$type_edt_2."\"></a>");
    echo "</li>";
    echo "</ul>";


    echo ("</div>");
    echo "<div class=\"spacer\"></div>";
}


// ======================================================
//
//      Lorsqu'on est en mode "emploi du temps"
//      permet de passer d'une p�riode � l'autre
//      fonction associ�e � gepi/edt_organisation/edt_eleve.php
//
// ======================================================
function AfficheBarCommutateurPeriodesEleve()
{
    if (isset($_SESSION['period_id'])) {
        $period_next = ReturnNextIdPeriod($_SESSION['period_id']);
        $period_previous = ReturnPreviousIdPeriod($_SESSION['period_id']);
    }
    else {
        $period_next = ReturnNextIdPeriod(ReturnIdPeriod(date("U")));
        $period_previous = ReturnPreviousIdPeriod(ReturnIdPeriod(date("U")));

    }
    echo ("<div id=\"ButtonBarArrows\">");

    echo "<ul class=\"ButtonBarArrowLeft\">";
    echo "<li class=\"ButtonBarArrowLeft1\">";
    echo ("<a href=\"./edt_eleve.php?period_id=".$period_previous."\"></a>");
    echo "</li>";
    echo "</ul>";


    $req_periode = mysql_query("SELECT nom_calendrier FROM edt_calendrier WHERE id_calendrier='".$_SESSION['period_id']."'");
    $rep_periode = mysql_fetch_array($req_periode);

    echo "<ul class=\"Period\">";
    echo "P�riode visualis�e : ".$rep_periode['nom_calendrier'];
    echo "</ul>";


    echo "<ul class=\"ButtonBarArrowRight\">";
    echo "<li class=\"ButtonBarArrowRight1\">";
    echo ("<a href=\"./edt_eleve.php?period_id=".$period_next."\"></a>");
    echo "</li>";
    echo "</ul>";


    echo ("</div>");
    echo "<div class=\"spacer\"></div>";
}
// =============================================================================
//
//          Affiche une petite imprimante
//
// =============================================================================
function AfficheImprimante($display_print)
{

    if ($display_print) {

        echo "<ul id=\"ButtonBarPrint\">";
        echo "<li id=\"ButtonBarPrint1\">";
	    echo "<a href='javascript:window.print()'></a>";
        echo "</li>";
        echo "</ul>";

    }
}

// =============================================================================
//
//          Affiche la bascule pour passer des emplois du temps p�riodes aux
//          emplois du temps semaines
//
// =============================================================================
function AfficheBascule($display_commutator, $login_edt, $visioedt, $type_edt_2)
{

    if ($display_commutator) {
        if (!isset($_SESSION['bascule_edt'])) {
            echo "<div class=\"ButtonBarCommutator\">";
            echo "<a href=\"./index_edt.php?bascule_edt=semaine&amp;visioedt=".$visioedt."&amp;login_edt=".$login_edt."&amp;type_edt_2=".$type_edt_2."\"><img src=\"../templates/".NameTemplateEDT()."/images/bascule_periode2.png\" title=\"Bascule vers emploi du temps semaine\" alt=\"Bascule vers emploi du temps semaine\" /></a>";
            echo "</div>";
        }
        else if ($_SESSION['bascule_edt'] == 'periode') {
            echo "<div class=\"ButtonBarCommutator\">";
            echo "<a href=\"./index_edt.php?bascule_edt=semaine&amp;visioedt=".$visioedt."&amp;login_edt=".$login_edt."&amp;type_edt_2=".$type_edt_2."\"><img src=\"../templates/".NameTemplateEDT()."/images/bascule_periode2.png\" title=\"Bascule vers emploi du temps semaine\" alt=\"Bascule vers emploi du temps semaine\" /></a>";
            echo "</div>";
        }
        else if ($_SESSION['bascule_edt'] == 'semaine') {
            echo "<div class=\"ButtonBarCommutator\">";
            echo "<a href=\"./index_edt.php?bascule_edt=periode&amp;visioedt=".$visioedt."&amp;login_edt=".$login_edt."&amp;type_edt_2=".$type_edt_2."\"><img src=\"../templates/".NameTemplateEDT()."/images/bascule_semaine2.png\" title=\"Bascule vers emploi du temps periode\" alt=\"Bascule vers emploi du temps periode\" /></a>";
            echo "</div>";
        }
    }
}

// =============================================================================
//
//          Affiche un "+" pour cr�er un nouveau cours sur un cr�neau vide
//
// =============================================================================
function AfficheIconePlusNew($type_edt,$heuredeb_dec,$login_edt,$jour_sem,$id_creneaux, $period)
{

    // On envoie le lien si et seulement si c'est un administrateur ou un scolarite ou si l'admin a donn� le droit aux professeurs
    if (($_SESSION["statut"] == "administrateur" OR $_SESSION["statut"] == "scolarite" OR ($_SESSION["statut"] == "professeur" AND getSettingValue("edt_remplir_prof") == 'y' AND strtolower($login_edt) == strtolower($_SESSION["login"]))) AND $type_edt == "prof") {
        $deb = "milieu";
        if ($heuredeb_dec == 0) 
        {
            $deb = "debut";
        }
        echo ("<span class=\"image\">");
	    //echo "<a href='javascript:centrerpopup(\"modifier_cours_popup.php?period_id=".$period."&amp;cours=aucun&amp;identite=".$login_edt."&amp;horaire=".$jour_sem."|".$id_creneaux."|".$deb."\",700,205,\"scrollbars=no,statusbar=no,resizable=no,menubar=no,toolbar=no,status=no\")'>
        //<img src=\"../templates/".NameTemplateEDT()."/images/ico_plus2.png\" title=\"Cr&eacute;er un cours\" alt=\"Cr&eacute;er un cours\" /></a>";
	    echo "<a href='modifier_cours.php?period_id=".$period."&amp;cours=aucun&amp;identite=".$login_edt."&amp;horaire=".$jour_sem."|".$id_creneaux."|".$deb."' onClick='centrerpopup(\"modifier_cours_popup.php?period_id=".$period."&amp;cours=aucun&amp;identite=".$login_edt."&amp;horaire=".$jour_sem."|".$id_creneaux."|".$deb."\",700,205,\"scrollbars=no,statusbar=no,resizable=no,menubar=no,toolbar=no,status=no\");return false;'>
        <img src=\"../templates/".NameTemplateEDT()."/images/ico_plus2.png\" title=\"Cr&eacute;er un cours\" alt=\"Cr&eacute;er un cours\" /></a>";
        echo ("</span>\n");
    }
}

// =============================================================================
//
//              Affiche un "+" pour ajouter un cours sur un cr�neau contenant d�j� quelque chose
//
// =============================================================================
function AfficheIconePlusAdd($type_edt,$heuredeb_dec,$login_edt,$jour_sem,$id_creneaux, $period)
{

    // On envoie le lien si et seulement si c'est un administrateur ou un scolarite ou si l'admin a donn� le droit aux professeurs
    if (($_SESSION["statut"] == "administrateur" OR $_SESSION["statut"] == "scolarite" OR ($_SESSION["statut"] == "professeur" AND getSettingValue("edt_remplir_prof") == 'y' AND strtolower($login_edt) == strtolower($_SESSION["login"]))) AND $type_edt == "prof") {
        $deb = "milieu";
        if ($heuredeb_dec == 0) 
        {
            $deb = "debut";
        }
        echo ("<span class=\"image\">");
	    //echo "<a href='javascript:centrerpopup(\"modifier_cours_popup.php?period_id=".$period."&amp;cours=aucun&amp;identite=".$login_edt."&amp;horaire=".$jour_sem."|".$id_creneaux."|".$deb."\",700,205,\"scrollbars=no,statusbar=no,resizable=no,menubar=no,toolbar=no,status=no\")'>
	    echo "<a href='modifier_cours.php?period_id=".$period."&amp;cours=aucun&amp;identite=".$login_edt."&amp;horaire=".$jour_sem."|".$id_creneaux."|".$deb."' onClick='centrerpopup(\"modifier_cours_popup.php?period_id=".$period."&amp;cours=aucun&amp;identite=".$login_edt."&amp;horaire=".$jour_sem."|".$id_creneaux."|".$deb."\",700,205,\"scrollbars=no,statusbar=no,resizable=no,menubar=no,toolbar=no,status=no\");return false;'>
        <img src=\"../templates/".NameTemplateEDT()."/images/ico_plus2.png\" title=\"Ajouter un cours\" alt=\"Ajouter un cours\" /></a>";
        echo ("</span>\n");
    }
}
// =============================================================================
//
//          Affiche un petit crayon pour �diter le cours
//
// =============================================================================
function AfficheModifierIcone($type_edt,$login_edt,$id_cours, $period)
{

    // On envoie le lien si et seulement si c'est un administrateur ou un scolarite ou si l'admin a donn� le droit aux professeurs
    if (($_SESSION["statut"] == "administrateur" OR $_SESSION["statut"] == "scolarite" OR ($_SESSION["statut"] == "professeur" AND getSettingValue("edt_remplir_prof") == 'y' AND strtolower($login_edt) == strtolower($_SESSION["login"]))) AND $type_edt == "prof") {
        echo ("<span class=\"image\">");
	    //echo "<a href='javascript:centrerpopup(\"modifier_cours_popup.php?period_id=".$period."&amp;id_cours=".$id_cours."&amp;type_edt=".$type_edt."&amp;identite=".$login_edt."\",700,205,\"scrollbars=no,statusbar=no,resizable=no,menubar=no,toolbar=no,status=no\")'>
	    echo "<a href='modifier_cours.php?period_id=".$period."&amp;id_cours=".$id_cours."&amp;type_edt=".$type_edt."&amp;identite=".$login_edt."' onClick='centrerpopup(\"modifier_cours_popup.php?period_id=".$period."&amp;id_cours=".$id_cours."&amp;type_edt=".$type_edt."&amp;identite=".$login_edt."\",700,205,\"scrollbars=no,statusbar=no,resizable=no,menubar=no,toolbar=no,status=no\");return false;'>
        <img src=\"../templates/".NameTemplateEDT()."/images/edit16.png\" title=\"Modifier\" alt=\"Modifier\" /></a>";
        echo ("</span>\n");
    }
}


// =============================================================================
//
//          Affiche un "X" pour supprimer le cours
//
// =============================================================================
function AfficheEffacerIcone($type_edt,$login_edt,$id_cours, $period)
{

    // On envoie le lien si et seulement si c'est un administrateur ou un scolarite ou si l'admin a donn� le droit aux professeurs
    if (($_SESSION["statut"] == "administrateur" OR $_SESSION["statut"] == "scolarite" OR ($_SESSION["statut"] == "professeur" AND getSettingValue("edt_remplir_prof") == 'y' AND strtolower($login_edt) == strtolower($_SESSION["login"]))) AND $type_edt == "prof") {
        echo ("<span class=\"image\">");
	    //echo "<a href=\"./index_edt.php?visioedt=prof1&amp;login_edt=".$login_edt."&amp;type_edt_2=prof&amp;supprimer_cours=".$id_cours."&amp;type_edt=".$type_edt."&amp;identite=".$login_edt."\"onclick=\"return confirm('Confirmez-vous cette suppression ?')\")'>
        echo "<a href='javascript:centrerpopup(\"effacer_cours.php?period_id=".$period."&amp;supprimer_cours=".$id_cours."&amp;type_edt=".$type_edt."&amp;identite=".$login_edt."\",600,55,\"scrollbars=0,statusbar=0,resizable=0,menubar=no,toolbar=no,status=no\")'>        
        <img src=\"../templates/".NameTemplateEDT()."/images/delete2.png\" title=\"Supprimer\" alt=\"Supprimer\" /></a>";
        echo ("</span>\n");
    }
}








?>