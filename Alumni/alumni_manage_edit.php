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

if (isActionAccessible($guid, $connection2, "/modules/Alumni/alumni_manage_edit.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print __($guid, "You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . __($guid, "Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . __($guid, getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Alumni/alumni_manage.php'>" . __($guid, 'Manage Alumni') . "</a> > </div><div class='trailEnd'>" . __($guid, 'Edit') . "</div>" ;
	print "</div>" ;
	
	if (isset($_GET["updateReturn"])) { $updateReturn=$_GET["updateReturn"] ; } else { $updateReturn="" ; }
	$updateReturnMessage="" ;
	$class="error" ;
	if (!($updateReturn=="")) {
		if ($updateReturn=="fail0") {
			$updateReturnMessage=__($guid, "Your request failed because you do not have access to this action.") ;	
		}
		else if ($updateReturn=="fail1") {
			$updateReturnMessage=__($guid, "Your request failed because your inputs were invalid.") ;	
		}
		else if ($updateReturn=="fail2") {
			$updateReturnMessage=__($guid, "Your request failed due to a database error.") ;	
		}
		else if ($updateReturn=="fail3") {
			$updateReturnMessage=__($guid, "Your request failed because your inputs were invalid.") ;	
		}
		else if ($updateReturn=="fail4") {
			$updateReturnMessage=__($guid, "Your request failed because your inputs were invalid.") ;	
		}
		else if ($updateReturn=="fail5") {
			$updateReturnMessage=__($guid, "Your request failed due to an attachment error.") ;	
		}
		else if ($updateReturn=="success0") {
			$updateReturnMessage=__($guid, "Your request was completed successfully.") ;	
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $updateReturnMessage;
		print "</div>" ;
	} 
	
	
	$alumniAlumnusID=$_GET["alumniAlumnusID"];
	if ($alumniAlumnusID=="Y") {
		print "<div class='error'>" ;
			print __($guid, "You have not specified one or more required parameters.") ;
		print "</div>" ;
	}
	else {
		try {
			$data=array("alumniAlumnusID"=>$alumniAlumnusID); 
			$sql="SELECT alumniAlumnus.* FROM alumniAlumnus WHERE alumniAlumnusID=:alumniAlumnusID" ; 
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}
	
		if ($result->rowCount()!=1) {
			print "<div class='error'>" ;
				print __($guid, "The selected record does not exist, or you do not have access to it.") ;
			print "</div>" ;
		}
		else {
			print "<div class='linkTop'>" ;
				$policyLink=getSettingByScope($connection2, "Behaviour", "policyLink") ;
				if ($policyLink!="") {
					print "<a target='_blank' href='$policyLink'>" . __($guid, 'View Behaviour Policy') . "</a>" ;
				}
				if ($_GET["graduatingYear"]!="") {
					if ($policyLink!="") {
						print " | " ;
					}
					print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Alumni/alumni_manage.php&graduatingYear=" . $_GET["graduatingYear"] . "'>" . __($guid, 'Back to Search Results') . "</a>" ;
				}
			print "</div>" ;
	
			//Let's go!
			$row=$result->fetch() ;
			?>
			<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/alumni_manage_editProcess.php?alumniAlumnusID=$alumniAlumnusID&graduatingYear=" . $_GET["graduatingYear"] ?>">
				<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
					<tr class='break'>
						<th colspan=2> 
							<?php print __($guid, "Personal Details") ; ?>
						</td>
					</tr>
					<tr>
						<td style='width: 275px'> 
							<b><?php print __($guid, 'Title') ?></b><br/>
						</td>
						<td class="right">
							<select style="width: 302px" name="title">
								<option value=""></option>
								<option <?php if ($row["title"]=="Ms.") { print "selected" ; } ?> value="Ms."><?php print __($guid, 'Ms.') ?></option>
								<option <?php if ($row["title"]=="Miss") { print "selected" ; } ?> value="Miss"><?php print __($guid, 'Miss') ?></option>
								<option <?php if ($row["title"]=="Mr.") { print "selected" ; } ?> value="Mr."><?php print __($guid, 'Mr.') ?></option>
								<option <?php if ($row["title"]=="Mrs.") { print "selected" ; } ?> value="Mrs."><?php print __($guid, 'Mrs.') ?></option>
								<option <?php if ($row["title"]=="Dr.") { print "selected" ; } ?> value="Dr."><?php print __($guid, 'Dr.') ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'First Name') ?> *</b><br/>
						</td>
						<td class="right">
							<input name="firstName" id="firstName" maxlength=30 value="<?php print htmlPrep($row["firstName"])?>" type="text" style="width: 300px">
							<script type="text/javascript">
								var firstName=new LiveValidation('firstName');
								firstName.add(Validate.Presence);
							</script>
						</td>
					</tr>
					<tr>
						<td style='width: 275px'> 
							<b><?php print __($guid, 'Surname') ?> *</b><br/>
						</td>
						<td class="right">
							<input name="surname" id="surname" maxlength=30 value="<?php print htmlPrep($row["surname"])?>" type="text" style="width: 300px">
							<script type="text/javascript">
								var surname=new LiveValidation('surname');
								surname.add(Validate.Presence);
							</script>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Official Name') ?></b><br/>
							<span style="font-size: 90%"><i><?php print __($guid, 'Full name as shown in ID documents.') ?></i></span>
						</td>
						<td class="right">
							<input name="officialName" id="officialName" maxlength=150 value="<?php print htmlPrep($row["officialName"])?>" type="text" style="width: 300px">
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Email') ?> *</b><br/>
						</td>
						<td class="right">
							<input name="email" id="email" maxlength=50 value="<?php print htmlPrep($row["email"])?>" type="text" style="width: 300px">
							<script type="text/javascript">
								var email=new LiveValidation('email');
								email.add(Validate.Email);
								email.add(Validate.Presence);
							</script>
						</td>
					</tr>
			
					<tr>
						<td> 
							<b><?php print __($guid, 'Gender') ?> *</b><br/>
						</td>
						<td class="right">
							<select name="gender" id="gender" style="width: 302px">
								<option value="Please select..."><?php print __($guid, 'Please select...') ?></option>
								<option <?php if ($row["gender"]=="F") { print "selected" ; } ?> value="F"><?php print __($guid, 'Female') ?></option>
								<option <?php if ($row["gender"]=="M") { print "selected" ; } ?> value="M"><?php print __($guid, 'Male') ?></option>
								<option <?php if ($row["gender"]=="Other") { print "selected" ; } ?> value="F"><?php print __($guid, 'Other') ?></option>
								<option <?php if ($row["gender"]=="Unspecified") { print "selected" ; } ?> value="M"><?php print __($guid, 'Unspecified') ?></option>
							</select>
							<script type="text/javascript">
								var gender=new LiveValidation('gender');
								gender.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
							</script>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Main Role') ?> *</b><br/>
							<span style="font-size: 90%"><i><?php print __($guid, 'In what way, primarily, were you involved with the school?') ?></i></span>
						</td>
						<td class="right">
							<select name="formerRole" id="formerRole" style="width: 302px">
								<option value="Please select..."><?php print __($guid, 'Please select...') ?></option>
								<option <?php if ($row["formerRole"]=="Student") { print "selected" ; } ?> value="Student"><?php print __($guid, 'Student') ?></option>
								<option <?php if ($row["formerRole"]=="Staff") { print "selected" ; } ?> value="Staff"><?php print __($guid, 'Staff') ?></option>
								<option <?php if ($row["formerRole"]=="Parent") { print "selected" ; } ?> value="Parent"><?php print __($guid, 'Parent') ?></option>
								<option <?php if ($row["formerRole"]=="Other") { print "selected" ; } ?> value="Other"><?php print __($guid, 'Other') ?></option>
							</select>
							<script type="text/javascript">
								var formerRole=new LiveValidation('formerRole');
								formerRole.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
							</script>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Date of Birth') ?></b><br/>
							<span style="font-size: 90%"><i><?php print __($guid, 'Format:') . " " . $_SESSION[$guid]["i18n"]["dateFormat"]  ?></i></span>
						</td>
						<td class="right">
							<input name="dob" id="dob" maxlength=10 value="<?php print dateConvertBack($guid, $row["dob"])?>" type="text" style="width: 300px">
							<script type="text/javascript">
								$(function() {
									$( "#dob" ).datepicker();
								});
							</script>
						</td>
					</tr>
			
					<tr class='break'>
						<th colspan=2> 
							<?php print __($guid, "Tell Us More About Yourself") ; ?>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Maiden Name') ?></b><br/>
							<span style="font-size: 90%"><i><?php print __($guid, 'Your surname prior to marriage.') ?></i></span>
						</td>
						<td class="right">
							<input name="maidenName" id="maidenName" maxlength=30 value="<?php print htmlPrep($row["maidenName"])?>" type="text" style="width: 300px">
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Username') ?></b><br/>
							<span style="font-size: 90%"><i><?php print __($guid, 'If you are young enough, this is how you logged into computers.') ?></i></span>
						</td>
						<td class="right">
							<input name="username2" id="username2" maxlength=20 value="<?php print htmlPrep($row["username"])?>" type="text" style="width: 300px">
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Graduating Year') ?></b><br/>
						</td>
						<td class="right">
							<select name="graduatingYear" id="graduatingYear" style="width: 302px">
								<?php
								print "<option value=''></option>" ;
								for ($i=date("Y"); $i>(date("Y")-200); $i--) {
									$selected="" ;
									if ($row["graduatingYear"]==$i) {
										$selected="selected" ;
									}
									print "<option $selected value='$i'>$i</option>" ;
								}
								?>				
							</select>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Current Country of Residence') ?></b><br/>
						</td>
						<td class="right">
							<select name="address1Country" id="address1Country" style="width: 302px">
								<?php
								print "<option value=''></option>" ;
								try {
									$dataSelect=array(); 
									$sqlSelect="SELECT printable_name FROM gibbonCountry ORDER BY printable_name" ;
									$resultSelect=$connection2->prepare($sqlSelect);
									$resultSelect->execute($dataSelect);
								}
								catch(PDOException $e) { }
								while ($rowSelect=$resultSelect->fetch()) {
									$selected="" ;
									if ($row["address1Country"]==$rowSelect["printable_name"]) {
										$selected="selected" ;
									}
									print "<option $selected value='" . $rowSelect["printable_name"] . "'>" . htmlPrep(__($guid, $rowSelect["printable_name"])) . "</option>" ;
								}
								?>				
							</select>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Profession') ?></b><br/>
						</td>
						<td class="right">
							<input name="profession" id="profession" maxlength=30 value="<?php print htmlPrep($row["profession"])?>" type="text" style="width: 300px">
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Employer') ?></b><br/>
						</td>
						<td class="right">
							<input name="employer" id="employer" maxlength=30 value="<?php print htmlPrep($row["employer"])?>" type="text" style="width: 300px">
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Job Title') ?></b><br/>
						</td>
						<td class="right">
							<input name="jobTitle" id="jobTitle" maxlength=30 value="<?php print htmlPrep($row["jobTitle"])?>" type="text" style="width: 300px">
						</td>
					</tr>
					
					<tr class='break'>
						<th colspan=2> 
							<?php print __($guid, "Link To Gibbon User") ; ?>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Existing User') ?></b><br/>
						</td>
						<td class="right">
							<select name="gibbonPersonID" id="gibbonPersonID" style="width: 302px">
								<?php
								print "<option value=''></option>" ;
								try {
									$dataSelect=array(); 
									$sqlSelect="SELECT gibbonPersonID, surname, preferredName, dob, username FROM gibbonPerson ORDER BY surname, preferredName" ;
									$resultSelect=$connection2->prepare($sqlSelect);
									$resultSelect->execute($dataSelect);
								}
								catch(PDOException $e) { print "error" . $e->getMessage() ; }
								while ($rowSelect=$resultSelect->fetch()) {
									$selected="" ;
									if ($row["gibbonPersonID"]==$rowSelect["gibbonPersonID"]) {
										$selected="selected" ;
									}
									print "<option $selected value='" . $rowSelect["gibbonPersonID"] . "'>" . formatName("", $rowSelect["preferredName"], $rowSelect["surname"], "Student", TRUE) . " (" . $rowSelect["username"] ;
									if ($rowSelect["dob"]!="") {
										print  " | " . dateConvertBack($guid, $rowSelect["dob"]) ;
									}
									print ")</option>" ;
								}
								?>				
							</select>
						</td>
					</tr>

					<tr>
						<td>
							<span style="font-size: 90%"><i>* <?php print __($guid, "denotes a required field") ; ?></i></span>
						</td>
						<td class="right">
							<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
							<input type="submit" value="<?php print __($guid, "Submit") ; ?>">
						</td>
					</tr>
				</table>
			</form>
			<?php
		}
	}
}
?>