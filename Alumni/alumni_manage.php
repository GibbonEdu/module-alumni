<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

use Gibbon\Forms\Form;
use Gibbon\Services\Format;

//Module includes
include './modules/'.$session->get('module').'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Alumni/alumni_manage.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo __('You do not have access to this action.');
    echo '</div>';
} else {
    $page->breadcrumbs->add('Manage Alumni');

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    $graduatingYear = isset($_GET['graduatingYear'])? $_GET['graduatingYear'] : '';

    echo '<h3>';
    echo __('Filter');
    echo '</h3>';

    $form = Form::create('search', $session->get('absoluteURL').'/index.php', 'get');
    $form->setClass('noIntBorder fullWidth');

    $form->addHiddenValue('q', '/modules/'.$session->get('module').'/alumni_manage.php');

    $row = $form->addRow();
        $row->addLabel('graduatingYear', __('Graduating Year'));
        $row->addSelect('graduatingYear')->fromArray(range(date('Y'), date('Y')-100, -1))->selected($graduatingYear)->placeholder();

    $row = $form->addRow();
        $row->addSearchSubmit($gibbon->session, __('Clear Search'));

    echo $form->getOutput();

    echo '<h3>';
    echo __('View Records');
    echo '</h3>';
    //Set pagination variable
    $page = 1;
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    }
    if ((!is_numeric($page)) or $page < 1) {
        $page = 1;
    }

    //Search with filters applied
    try {
        $data = array();
        $sqlWhere = 'WHERE ';
        if ($graduatingYear != '') {
            $data['graduatingYear'] = $graduatingYear;
            $sqlWhere .= ' alumniAlumnus.graduatingYear=:graduatingYear AND ';
        }
        if ($sqlWhere == 'WHERE ') {
            $sqlWhere = '';
        } else {
            $sqlWhere = substr($sqlWhere, 0, -5);
        }
        $sql = "SELECT * FROM alumniAlumnus $sqlWhere ORDER BY timestamp DESC";
        $result = $connection2->prepare($sql);
        $result->execute($data);
    } catch (PDOException $e) { echo "<div class='error'>".$e->getMessage().'</div>';
    }
    $sqlPage = $sql.' LIMIT '.$session->get('pagination').' OFFSET '.(($page - 1) * $session->get('pagination'));

    echo "<div class='linkTop'>";
    echo "<a href='".$session->get('absoluteURL').'/index.php?q=/modules/'.$session->get('module')."/alumni_manage_add.php&graduatingYear=$graduatingYear'>".__('Add')."<img style='margin: 0 0 -4px 5px' title='".__('Add')."' src='./themes/".$session->get('gibbonThemeName')."/img/page_new.png'/></a>";
    echo '</div>';

    if ($result->rowCount() < 1) { echo "<div class='error'>";
        echo __('There are no records to display.');
        echo '</div>';
    } else {
        if ($result->rowCount() > $session->get('pagination')) {
            printPagination($guid, $result->rowCount(), $page, $session->get('pagination'), 'top', "graduatingYear=$graduatingYear");
        }

        echo "<table cellspacing='0' style='width: 100%'>";
        echo "<tr class='head'>";
        echo '<th>';
        echo __('Name');
        echo '</th>';
        echo '<th>';
        echo __('Email');
        echo '</th>';
        echo '<th>';
        echo __('Graduating Year');
        echo '</th>';
        echo "<th style='min-width: 70px'>";
        echo __('Actions');
        echo '</th>';
        echo '</tr>';

        $count = 0;
        $rowNum = 'odd';
        try {
            $resultPage = $connection2->prepare($sqlPage);
            $resultPage->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }
        while ($row = $resultPage->fetch()) {
            if ($count % 2 == 0) {
                $rowNum = 'even';
            } else {
                $rowNum = 'odd';
            }
            ++$count;

			//COLOR ROW BY STATUS!
			echo "<tr class=$rowNum>";
			echo '<td>';
			echo Format::name($row['title'], $row['firstName'], $row['surname'], 'Parent', false, false).'</b><br/>';
			echo '</td>';
			echo '<td>';
			echo $row['email'];
			echo '</td>';
			echo '<td>';
			echo $row['graduatingYear'];
			echo '</td>';
			echo '<td>';
			echo "<a href='".$session->get('absoluteURL').'/index.php?q=/modules/'.$session->get('module').'/alumni_manage_edit.php&alumniAlumnusID='.$row['alumniAlumnusID']."&graduatingYear=$graduatingYear'><img title='".__('Edit')."' src='./themes/".$session->get('gibbonThemeName')."/img/config.png'/></a> ";
			echo "<a class='thickbox' href='".$session->get('absoluteURL').'/fullscreen.php?q=/modules/'.$session->get('module').'/alumni_manage_delete.php&alumniAlumnusID='.$row['alumniAlumnusID']."&graduatingYear=$graduatingYear&width=650&height=135'><img title='".__('Delete')."' src='./themes/".$session->get('gibbonThemeName')."/img/garbage.png'/></a> ";
			echo "<script type='text/javascript'>";
			echo '$(document).ready(function(){';
			echo "\$(\".comment-$count\").hide();";
			echo "\$(\".show_hide-$count\").fadeIn(1000);";
			echo "\$(\".show_hide-$count\").click(function(){";
			echo "\$(\".comment-$count\").fadeToggle(1000);";
			echo '});';
			echo '});';
			echo '</script>';
			echo "<a title='".__('View Details')."' class='show_hide-$count' onclick='false' href='#'><img style='padding-right: 5px' src='".$session->get('absoluteURL')."/themes/Default/img/page_down.png' alt='".__('View Details')."' onclick='return false;' /></a>";
			echo '</td>';
			echo '</tr>';
			echo "<tr class='comment-$count' id='comment-$count'>";
			echo '<td colspan=4>';
			echo '<b>'.__('Official Name').': </b>'.$row['officialName'].'<br/>';
			echo '<b>'.__('Maiden Name').': </b>'.$row['maidenName'].'<br/>';
			echo '<b>'.__('Gender').': </b>'.$row['gender'].'<br/>';
			echo '<b>'.__('Username').': </b>'.$row['username'].'<br/>';
			echo '<b>'.__('Date Of Birth').': </b>';
			if ($row['dob'] != '') {
				echo dateConvertBack($guid, $row['dob']);
			}
			echo '<br/>';
			echo '<b>'.__('Country of Residence').': </b>'.$row['address1Country'].'<br/>';
			echo '<b>'.__('Profession').': </b>'.$row['profession'].'<br/>';
			echo '<b>'.__('Employer').': </b>'.$row['employer'].'<br/>';
			echo '<b>'.__('Job Title').': </b>'.$row['jobTitle'].'<br/>';
			echo '<b>'.__('Date Joined').': </b>';
			if ($row['timestamp'] != '') {
				echo dateConvertBack($guid, substr($row['timestamp'], 0, 10));
			}
			echo '<br/>';
			echo '</td>';
			echo '</tr>';
		}
        echo '</table>';

        if ($result->rowCount() > $session->get('pagination')) {
            printPagination($guid, $result->rowCount(), $page, $session->get('pagination'), 'bottom', "graduatingYear=$graduatingYear");
        }
    }
}
