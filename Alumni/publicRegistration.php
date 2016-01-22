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

$proceed=FALSE ;

if (isset($_SESSION[$guid]["username"])==FALSE) {
	$enablePublicRegistration=getSettingByScope($connection2, 'Alumni', 'showPublicRegistration') ;
	if ($enablePublicRegistration=="Y") {
		$proceed=TRUE ;
	}
}

if ($proceed==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
		print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > </div><div class='trailEnd'>" . $_SESSION[$guid]["organisationNameShort"] . " " . _('Alumni Registration') . "</div>" ;
	print "</div>" ;
	
	$publicRegistrationMinimumAge=getSettingByScope($connection2, 'User Admin', 'publicRegistrationMinimumAge') ;
	
	if (isset($_GET["addReturn"])) { $addReturn=$_GET["addReturn"] ; } else { $addReturn="" ; }
	$addReturnMessage="" ;
	$class="error" ;
	if (!($addReturn=="")) {
		if ($addReturn=="fail0") {
			$addReturnMessage=_("Your request failed because you do not have access to this action.") ;	
		}
		else if ($addReturn=="fail2") {
			$addReturnMessage=_("Your request failed due to a database error.") ;	
		}
		else if ($addReturn=="fail3") {
			$addReturnMessage=_("Your request failed because your inputs were invalid.") ;	
		}
		else if ($addReturn=="fail5") {
			$addReturnMessage=sprintf(_('Your request failed because you do not meet the minimum age for joining this site (%1$s years of age).'), $publicRegistrationMinimumAge) ;	
		}
		else if ($addReturn=="success0") {
			$addReturnMessage=_("Your registration was successfully submitted: a member of our alumni team will be in touch shortly.") ;
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $addReturnMessage;
		print "</div>" ;
	} 
	
	?>
	<p>
		<?php
		print sprintf(_('This registration form is for former members of the %1$s community who wish to reconnect. Please fill in your details here, and someone from our alumni team will get back to you.'), $_SESSION[$guid]["organisationNameShort"]) ;
		?>
	</p>
	<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/Alumni/publicRegistrationProcess.php" ?>" enctype="multipart/form-data">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
			<tr class='break'>
				<th colspan=2> 
					<?php print _("Personal Details") ; ?>
				</td>
			</tr>
			<tr>
				<td style='width: 275px'> 
					<b><?php print _('Title') ?></b><br/>
				</td>
				<td class="right">
					<select style="width: 302px" name="title">
						<option value=""></option>
						<option value="Ms."><?php print _('Ms.') ?></option>
						<option value="Miss"><?php print _('Miss') ?></option>
						<option value="Mr."><?php print _('Mr.') ?></option>
						<option value="Mrs."><?php print _('Mrs.') ?></option>
						<option value="Dr."><?php print _('Dr.') ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('First Name') ?> *</b><br/>
				</td>
				<td class="right">
					<input name="firstName" id="firstName" maxlength=30 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var firstName=new LiveValidation('firstName');
						firstName.add(Validate.Presence);
					</script>
				</td>
			</tr>
			<tr>
				<td style='width: 275px'> 
					<b><?php print _('Surname') ?> *</b><br/>
				</td>
				<td class="right">
					<input name="surname" id="surname" maxlength=30 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var surname=new LiveValidation('surname');
						surname.add(Validate.Presence);
					</script>
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Official Name') ?> *</b><br/>
					<span style="font-size: 90%"><i><?php print _('Full name as shown in ID documents.') ?></i></span>
				</td>
				<td class="right">
					<input name="officialName" id="officialName" maxlength=150 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var officialName=new LiveValidation('officialName');
						officialName.add(Validate.Presence);
					</script>
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Email') ?> *</b><br/>
				</td>
				<td class="right">
					<input name="email" id="email" maxlength=50 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var email=new LiveValidation('email');
						email.add(Validate.Email);
						email.add(Validate.Presence);
					</script>
				</td>
			</tr>
			
			<tr>
				<td> 
					<b><?php print _('Gender') ?> *</b><br/>
				</td>
				<td class="right">
					<select name="gender" id="gender" style="width: 302px">
						<option value="Please select..."><?php print _('Please select...') ?></option>
						<option value="F"><?php print _('Female') ?></option>
						<option value="M"><?php print _('Male') ?></option>
						<option value="F"><?php print _('Other') ?></option>
						<option value="M"><?php print _('Unspecified') ?></option>
					</select>
					<script type="text/javascript">
						var gender=new LiveValidation('gender');
						gender.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print _('Select something!') ?>"});
					</script>
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Main Role') ?> *</b><br/>
					<span style="font-size: 90%"><i><?php print _('In what way, primarily, were you involved with the school?') ?></i></span>
				</td>
				<td class="right">
					<select name="formerRole" id="formerRole" style="width: 302px">
						<option value="Please select..."><?php print _('Please select...') ?></option>
						<option value="Student"><?php print _('Student') ?></option>
						<option value="Staff"><?php print _('Staff') ?></option>
						<option value="Parent"><?php print _('Parent') ?></option>
						<option value="Other"><?php print _('Other') ?></option>
					</select>
					<script type="text/javascript">
						var formerRole=new LiveValidation('formerRole');
						formerRole.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print _('Select something!') ?>"});
					</script>
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Date of Birth') ?> *</b><br/>
					<span style="font-size: 90%"><i><?php print _('Format:') . " " . $_SESSION[$guid]["i18n"]["dateFormat"]  ?></i></span>
				</td>
				<td class="right">
					<input name="dob" id="dob" maxlength=10 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var dob=new LiveValidation('dob');
						dob.add( Validate.Format, {pattern: <?php if ($_SESSION[$guid]["i18n"]["dateFormatRegEx"]=="") {  print "/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i" ; } else { print $_SESSION[$guid]["i18n"]["dateFormatRegEx"] ; } ?>, failureMessage: "Use <?php if ($_SESSION[$guid]["i18n"]["dateFormat"]=="") { print "dd/mm/yyyy" ; } else { print $_SESSION[$guid]["i18n"]["dateFormat"] ; }?>." } ); 
					 	dob.add(Validate.Presence);
					</script>
					 <script type="text/javascript">
						$(function() {
							$( "#dob" ).datepicker();
						});
					</script>
				</td>
			</tr>
			
			<tr class='break'>
				<th colspan=2> 
					<?php print _("Tell Us More About Yourself") ; ?>
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Maiden Name') ?></b><br/>
					<span style="font-size: 90%"><i><?php print _('Your surname prior to marriage.') ?></i></span>
				</td>
				<td class="right">
					<input name="maidenName" id="maidenName" maxlength=30 value="" type="text" style="width: 300px">
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Username') ?></b><br/>
					<span style="font-size: 90%"><i><?php print _('If you are young enough, this is how you logged into computers.') ?></i></span>
				</td>
				<td class="right">
					<input name="username2" id="username2" maxlength=20 value="" type="text" style="width: 300px">
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Graduating Year') ?></b><br/>
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
			<tr>
				<td> 
					<b><?php print _('Current Country of Residence') ?></b><br/>
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
							print "<option value='" . $rowSelect["printable_name"] . "'>" . htmlPrep(_($rowSelect["printable_name"])) . "</option>" ;
						}
						?>				
					</select>
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Profession') ?></b><br/>
				</td>
				<td class="right">
					<input name="profession" id="profession" maxlength=30 value="" type="text" style="width: 300px">
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Employer') ?></b><br/>
				</td>
				<td class="right">
					<input name="employer" id="employer" maxlength=30 value="" type="text" style="width: 300px">
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Job Title') ?></b><br/>
				</td>
				<td class="right">
					<input name="jobTitle" id="jobTitle" maxlength=30 value="" type="text" style="width: 300px">
				</td>
			</tr>
			
			<?php
			//Privacy statement
			$privacyStatement=getSettingByScope($connection2, 'User Admin', 'publicRegistrationPrivacyStatement') ;
			if ($privacyStatement!="") {
				print "<tr class='break'>" ;
					print "<th colspan=2>" ; 
						print _("Privacy Statement") ;
					print "</th>" ;
				print "</tr>" ;
				print "<tr>" ;
					print "<td colspan=2>" ; 
						print "<p>" ;
							print $privacyStatement ;
						print "</p>" ;
					print "</td>" ;
				print "</tr>" ;
			}
	
			//Get agreement
			$agreement=getSettingByScope($connection2, 'User Admin', 'publicRegistrationAgreement') ;
			if ($agreement!="") {
				print "<tr class='break'>" ;
					print "<th colspan=2>" ; 
						print _("Agreement") ;
					print "</td>" ;
				print "</tr>" ;
				
				print "<tr>" ;
					print "<td colspan=2>" ; 
						print $agreement ;
					print "</td>" ;
				print "</tr>" ;
				print "<tr>" ;
					print "<td>" ; 
						print "<b>" . _('Do you agree to the above?') . "</b><br/>" ;
					print "</td>" ;
					print "<td class='right'>" ;
						print "Yes <input type='checkbox' name='agreement' id='agreement'>" ;
						?>
						<script type="text/javascript">
							var agreement=new LiveValidation('agreement');
							agreement.add( Validate.Acceptance );
						</script>
						 <?php
					print "</td>" ;
				print "</tr>" ;
			}
			
			?>
			<tr>
				<td>
					<span style="font-size: 90%"><i>* <?php print _("denotes a required field") ; ?></i></span>
				</td>
				<td class="right">
					<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
					<input type="submit" value="<?php print _("Submit") ; ?>">
				</td>
			</tr>
		</table>
	</form>	
	
	<?php
}
?>