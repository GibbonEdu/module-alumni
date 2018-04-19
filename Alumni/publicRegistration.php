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

$proceed = false;

if (isset($_SESSION[$guid]['username']) == false) {
    $enablePublicRegistration = getSettingByScope($connection2, 'Alumni', 'showPublicRegistration');
    if ($enablePublicRegistration == 'Y') {
        $proceed = true;
    }
}

if ($proceed == false) {
    //Acess denied
    echo "<div class='error'>";
    echo __($guid, 'You do not have access to this action.');
    echo '</div>';
} else {
    //Proceed!
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>".__($guid, 'Home')."</a> > </div><div class='trailEnd'>".$_SESSION[$guid]['organisationNameShort'].' '.__($guid, 'Alumni Registration').'</div>';
    echo '</div>';

    $publicRegistrationMinimumAge = getSettingByScope($connection2, 'User Admin', 'publicRegistrationMinimumAge');

    $returns = array();
    $returns['error5'] = sprintf(__($guid, 'Your request failed because you do not meet the minimum age for joining this site (%1$s years of age).'), $publicRegistrationMinimumAge);
    $returns['success0'] = __($guid, 'Your registration was successfully submitted: a member of our alumni team will be in touch shortly.');
    $editLink = '';
    if (isset($_GET['editID'])) {
        $editLink = $_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/User Admin/user_manage_edit.php&gibbonPersonID='.$_GET['editID'].'&search='.$_GET['search'];
    }
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], $editLink, $returns);
    }
    ?>
	<p>
		<?php
        echo sprintf(__($guid, 'This registration form is for former members of the %1$s community who wish to reconnect. Please fill in your details here, and someone from our alumni team will get back to you.'), $_SESSION[$guid]['organisationNameShort']);
    $facebookLink = getSettingByScope($connection2, 'Alumni', 'facebookLink');
    if ($facebookLink != '') {
        echo ' '.sprintf(__($guid, 'Please don\'t forget to take a look at, and like, our alumni %1$sFacebook page%2$s.'), "<a href='".htmlPrep($facebookLink)."' target='_blank'>", '</a>');
    }
    ?>
	</p>
	<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL'].'/modules/Alumni/publicRegistrationProcess.php' ?>" enctype="multipart/form-data">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">
			<tr class='break'>
				<th colspan=2>
					<?php echo __($guid, 'Personal Details');
    				?>
				</td>
			</tr>
			<tr>
				<td style='width: 275px'>
					<b><?php echo __($guid, 'Title') ?></b><br/>
				</td>
				<td class="right">
					<select style="width: 302px" name="title">
						<option value=""></option>
						<option value="Ms."><?php echo __($guid, 'Ms.') ?></option>
						<option value="Miss"><?php echo __($guid, 'Miss') ?></option>
						<option value="Mr."><?php echo __($guid, 'Mr.') ?></option>
						<option value="Mrs."><?php echo __($guid, 'Mrs.') ?></option>
						<option value="Dr."><?php echo __($guid, 'Dr.') ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo __($guid, 'First Name') ?> *</b><br/>
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
					<b><?php echo __($guid, 'Surname') ?> *</b><br/>
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
					<b><?php echo __($guid, 'Official Name') ?> *</b><br/>
					<span style="font-size: 90%"><i><?php echo __($guid, 'Full name as shown in ID documents.') ?></i></span>
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
					<b><?php echo __($guid, 'Email') ?> *</b><br/>
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
					<b><?php echo __($guid, 'Gender') ?> *</b><br/>
				</td>
				<td class="right">
					<select name="gender" id="gender" style="width: 302px">
						<option value="Please select..."><?php echo __($guid, 'Please select...') ?></option>
						<option value="F"><?php echo __($guid, 'Female') ?></option>
						<option value="M"><?php echo __($guid, 'Male') ?></option>
						<option value="F"><?php echo __($guid, 'Other') ?></option>
						<option value="M"><?php echo __($guid, 'Unspecified') ?></option>
					</select>
					<script type="text/javascript">
						var gender=new LiveValidation('gender');
						gender.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php echo __($guid, 'Select something!') ?>"});
					</script>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo __($guid, 'Main Role') ?> *</b><br/>
					<span style="font-size: 90%"><i><?php echo __($guid, 'In what way, primarily, were you involved with the school?') ?></i></span>
				</td>
				<td class="right">
					<select name="formerRole" id="formerRole" style="width: 302px">
						<option value="Please select..."><?php echo __($guid, 'Please select...') ?></option>
						<option value="Student"><?php echo __($guid, 'Student') ?></option>
						<option value="Staff"><?php echo __($guid, 'Staff') ?></option>
						<option value="Parent"><?php echo __($guid, 'Parent') ?></option>
						<option value="Other"><?php echo __($guid, 'Other') ?></option>
					</select>
					<script type="text/javascript">
						var formerRole=new LiveValidation('formerRole');
						formerRole.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php echo __($guid, 'Select something!') ?>"});
					</script>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo __($guid, 'Date of Birth') ?> *</b><br/>
					<span style="font-size: 90%"><i><?php echo __($guid, 'Format:').' '.$_SESSION[$guid]['i18n']['dateFormat']  ?></i></span>
				</td>
				<td class="right">
					<input name="dob" id="dob" maxlength=10 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var dob=new LiveValidation('dob');
						dob.add( Validate.Format, {pattern: <?php if ($_SESSION[$guid]['i18n']['dateFormatRegEx'] == '') {
							echo "/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i";
						} else {
							echo $_SESSION[$guid]['i18n']['dateFormatRegEx'];
						}
							?>, failureMessage: "Use <?php if ($_SESSION[$guid]['i18n']['dateFormat'] == '') {
							echo 'dd/mm/yyyy';
						} else {
							echo $_SESSION[$guid]['i18n']['dateFormat'];
						}
						?>." } );
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
					<?php echo __($guid, 'Tell Us More About Yourself');
    				?>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo __($guid, 'Maiden Name') ?></b><br/>
					<span style="font-size: 90%"><i><?php echo __($guid, 'Your surname prior to marriage.') ?></i></span>
				</td>
				<td class="right">
					<input name="maidenName" id="maidenName" maxlength=30 value="" type="text" style="width: 300px">
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo __($guid, 'Username') ?></b><br/>
					<span style="font-size: 90%"><i><?php echo __($guid, 'If you are young enough, this is how you logged into computers.') ?></i></span>
				</td>
				<td class="right">
					<input name="username2" id="username2" maxlength=20 value="" type="text" style="width: 300px">
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo __($guid, 'Graduating Year') ?></b><br/>
				</td>
				<td class="right">
					<select name="graduatingYear" id="graduatingYear" style="width: 302px">
						<?php
                        echo "<option value=''></option>";
						for ($i = date('Y'); $i > (date('Y') - 200); --$i) {
							echo "<option value='$i'>$i</option>";
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo __($guid, 'Current Country of Residence') ?></b><br/>
				</td>
				<td class="right">
					<select name="address1Country" id="address1Country" style="width: 302px">
						<?php
                        echo "<option value=''></option>";
						try {
							$dataSelect = array();
							$sqlSelect = 'SELECT printable_name FROM gibbonCountry ORDER BY printable_name';
							$resultSelect = $connection2->prepare($sqlSelect);
							$resultSelect->execute($dataSelect);
						} catch (PDOException $e) {
						}
						while ($rowSelect = $resultSelect->fetch()) {
							echo "<option value='".$rowSelect['printable_name']."'>".htmlPrep(__($guid, $rowSelect['printable_name'])).'</option>';
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo __($guid, 'Profession') ?></b><br/>
				</td>
				<td class="right">
					<input name="profession" id="profession" maxlength=30 value="" type="text" style="width: 300px">
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo __($guid, 'Employer') ?></b><br/>
				</td>
				<td class="right">
					<input name="employer" id="employer" maxlength=30 value="" type="text" style="width: 300px">
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo __($guid, 'Job Title') ?></b><br/>
				</td>
				<td class="right">
					<input name="jobTitle" id="jobTitle" maxlength=30 value="" type="text" style="width: 300px">
				</td>
			</tr>

			<?php
            //Privacy statement
            $privacyStatement = getSettingByScope($connection2, 'User Admin', 'publicRegistrationPrivacyStatement');
			if ($privacyStatement != '') {
				echo "<tr class='break'>";
				echo '<th colspan=2>';
				echo __($guid, 'Privacy Statement');
				echo '</th>';
				echo '</tr>';
				echo '<tr>';
				echo '<td colspan=2>';
				echo '<p>';
				echo $privacyStatement;
				echo '</p>';
				echo '</td>';
				echo '</tr>';
			}

            //Get agreement
            $agreement = getSettingByScope($connection2, 'User Admin', 'publicRegistrationAgreement');
			if ($agreement != '') {
				echo "<tr class='break'>";
				echo '<th colspan=2>';
				echo __($guid, 'Agreement');
				echo '</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td colspan=2>';
				echo $agreement;
				echo '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td>';
				echo '<b>'.__($guid, 'Do you agree to the above?').'</b><br/>';
				echo '</td>';
				echo "<td class='right'>";
				echo "Yes <input type='checkbox' name='agreement' id='agreement'>";
				?>
				<script type="text/javascript">
					var agreement=new LiveValidation('agreement');
					agreement.add( Validate.Acceptance );
				</script>
				 <?php
			echo '</td>';
        echo '</tr>';
    }

    ?>
	<tr>
		<td>
			<span style="font-size: 90%"><i>* <?php echo __($guid, 'denotes a required field'); ?></i></span>
				</td>
				<td class="right">
					<input type="hidden" name="address" value="<?php echo $_SESSION[$guid]['address'] ?>">
					<input type="submit" value="<?php echo __($guid, 'Submit'); ?>">
				</td>
			</tr>
		</table>
	</form>

	<?php

}
?>
