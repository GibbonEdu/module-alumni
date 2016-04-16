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

@session_start() ;

//Module includes
include "./modules/" . $_SESSION[$guid]["module"] . "/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Alumni/alumni_manage.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print __($guid, "You do not have access to this action.") ;
	print "</div>" ;
}
else {
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . __($guid, "Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . __($guid, getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . __($guid, 'Manage Alumni') . "</div>" ;
	print "</div>" ;
	
	if (isset($_GET["deleteReturn"])) { $deleteReturn=$_GET["deleteReturn"] ; } else { $deleteReturn="" ; }
	$deleteReturnMessage="" ;
	$class="error" ;
	if (!($deleteReturn=="")) {
		if ($deleteReturn=="success0") {
			$deleteReturnMessage=__($guid, "Your request was completed successfully.") ;		
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $deleteReturnMessage;
		print "</div>" ;
	} 
	
	$graduatingYear=NULL ;
	if (isset($_GET["graduatingYear"])) {
		$graduatingYear=$_GET["graduatingYear"] ;
	}
	
	print "<h3>" ;
		print __($guid, "Filter") ;
	print "</h3>" ;
	print "<form method='get' action='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Alumni/alumni_manage.php'>" ;
		print "<table class='noIntBorder' cellspacing='0' style='width: 100%'>" ;
			?>
			<tr>
				<td> 
					<b><?php print __($guid, 'Graduating Year') ?></b><br/>
					<span style="font-size: 90%"><i></i></span>
				</td>
				<td class="right">
					<select name="graduatingYear" id="graduatingYear" style="width: 302px">
						<?php
						print "<option value=''></option>" ;
						for ($i=date("Y"); $i>(date("Y")-200); $i--) {
							print "<option value='$i'>$i</option>" ;
						}
						?>				
					</select>
				</td>
			</tr>
			<?php
		
			print "<tr>" ;
				print "<td class='right' colspan=2>" ;
					print "<input type='hidden' name='q' value='" . $_GET["q"] . "'>" ;
					print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Alumni/alumni_manage.php'>" . __($guid, 'Clear Filters') . "</a> " ;
					print "<input type='submit' value='" . __($guid, 'Go') . "'>" ;
				print "</td>" ;
			print "</tr>" ;
		print "</table>" ;
	print "</form>" ;
	
	
	print "<h3>" ;
		print __($guid, "Behaviour Records") ;
	print "</h3>" ;
	//Set pagination variable
	$page=1 ; if (isset($_GET["page"])) { $page=$_GET["page"] ; }
	if ((!is_numeric($page)) OR $page<1) {
		$page=1 ;
	}
	
	//Search with filters applied
	try {
		$data=array() ;
		$sqlWhere="AND " ;
		if ($graduatingYear!="") {
			$data["graduatingYear"]=$graduatingYear ;
			$sqlWhere.="alumniAlumnus.graduatingYear=:graduatingYear AND " ; 
		}
		if ($sqlWhere=="AND ") {
			$sqlWhere="" ;
		}
		else {
			$sqlWhere=substr($sqlWhere,0,-5) ;
		}
		$sql="SELECT * FROM alumniAlumnus $sqlWhere ORDER BY timestamp DESC" ; 
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
	$sqlPage=$sql . " LIMIT " . $_SESSION[$guid]["pagination"] . " OFFSET " . (($page-1)*$_SESSION[$guid]["pagination"]) ;
	
	print "<div class='linkTop'>" ;
		print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/alumni_manage_add.php&graduatingYear=$graduatingYear'>" . __($guid, 'Add') . "<img style='margin: 0 0 -4px 5px' title='" . __($guid, 'Add') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_new.png'/></a>" ;
	print "</div>" ;
	
	if ($result->rowCount()<1) {
		print "<div class='error'>" ;
		print __($guid, "There are no records to display.") ;
		print "</div>" ;
	}
	else {
		if ($result->rowCount()>$_SESSION[$guid]["pagination"]) {
			printPagination($guid, $result->rowCount(), $page, $_SESSION[$guid]["pagination"], "top", "graduatingYear=$graduatingYear") ;
		}
	
		print "<table cellspacing='0' style='width: 100%'>" ;
			print "<tr class='head'>" ;
				print "<th>" ;
					print __($guid, "Name") ;
				print "</th>" ;
				print "<th>" ;
					print __($guid, "Email") ;
				print "</th>" ;
				print "<th>" ;
					print __($guid, "Graduating Year") ;
				print "</th>" ;
				print "<th style='min-width: 70px'>" ;
					print __($guid, "Actions") ;
				print "</th>" ;
			print "</tr>" ;
			
			$count=0;
			$rowNum="odd" ;
			try {
				$resultPage=$connection2->prepare($sqlPage);
				$resultPage->execute($data);
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}		
			while ($row=$resultPage->fetch()) {
				if ($count%2==0) {
					$rowNum="even" ;
				}
				else {
					$rowNum="odd" ;
				}
				$count++ ;
				
				//COLOR ROW BY STATUS!
				print "<tr class=$rowNum>" ;
					print "<td>" ;
						print formatName($row["title"], $row["firstName"], $row["surname"], "Parent", FALSE, FALSE) . "</b><br/>" ;
					print "</td>" ;
					print "<td>" ;
						print $row["email"] ;
					print "</td>" ;
					print "<td>" ;
						print $row["graduatingYear"] ;
					print "</td>" ;
					print "<td>" ;
						print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/alumni_manage_edit.php&alumniAlumnusID=" . $row["alumniAlumnusID"] . "&graduatingYear=$graduatingYear'><img title='" . __($guid, 'Edit') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a> " ;
						print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/alumni_manage_delete.php&alumniAlumnusID=" . $row["alumniAlumnusID"] . "&graduatingYear=$graduatingYear'><img title='" . __($guid, 'Delete') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png'/></a> " ;
						print "<script type='text/javascript'>" ;	
							print "$(document).ready(function(){" ;
								print "\$(\".comment-$count\").hide();" ;
								print "\$(\".show_hide-$count\").fadeIn(1000);" ;
								print "\$(\".show_hide-$count\").click(function(){" ;
								print "\$(\".comment-$count\").fadeToggle(1000);" ;
								print "});" ;
							print "});" ;
						print "</script>" ;
						print "<a title='" . __($guid, 'View Details') . "' class='show_hide-$count' onclick='false' href='#'><img style='padding-right: 5px' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/Default/img/page_down.png' alt='" . __($guid, 'View Details') . "' onclick='return false;' /></a>" ;
					print "</td>" ;
				print "</tr>" ;
				print "<tr class='comment-$count' id='comment-$count'>" ;
					print "<td colspan=4>" ;
						print "<b>" . __($guid, 'Official Name') . ": </b>" . $row["officialName"] . "<br/>" ;
						print "<b>" . __($guid, 'Maiden Name') . ": </b>" . $row["maidenName"] . "<br/>" ;
						print "<b>" . __($guid, 'Gender') . ": </b>" . $row["gender"] . "<br/>" ;
						print "<b>" . __($guid, 'Username') . ": </b>" . $row["username"] . "<br/>" ;
						print "<b>" . __($guid, 'Date Of Birth') . ": </b>" ;
						if ($row["dob"]!="") {
							print dateConvertBack($guid, $row["dob"]) ;
						}
						print "<br/>" ;
						print "<b>" . __($guid, 'Country of Residence') . ": </b>" . $row["address1Country"] . "<br/>" ;
						print "<b>" . __($guid, 'Profession') . ": </b>" . $row["profession"] . "<br/>" ;
						print "<b>" . __($guid, 'Employer') . ": </b>" . $row["employer"] . "<br/>" ;
						print "<b>" . __($guid, 'Job Title') . ": </b>" . $row["jobTitle"] . "<br/>" ;
						print "<b>" . __($guid, 'Date Joined') . ": </b>" ;
						if ($row["timestamp"]!="") {
							print dateConvertBack($guid, substr($row["timestamp"], 0, 10)) ;
						}
						print "<br/>" ;
					print "</td>" ;
				print "</tr>" ;
			}
		print "</table>" ;
		
		if ($result->rowCount()>$_SESSION[$guid]["pagination"]) {
			printPagination($guid, $result->rowCount(), $page, $_SESSION[$guid]["pagination"], "bottom", "graduatingYear=$graduatingYear") ;
		}
	}
}	
?>