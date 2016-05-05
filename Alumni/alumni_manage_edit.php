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

@session_start();

//Module includes
include './modules/'.$_SESSION[$guid]['module'].'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Alumni/alumni_manage_edit.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo __($guid, 'You do not have access to this action.');
    echo '</div>';
} else {
    //Proceed!
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>".__($guid, 'Home')."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".__($guid, getModuleName($_GET['q']))."</a> > <a href='".$_SESSION[$guid]['absoluteURL']."/index.php?q=/modules/Alumni/alumni_manage.php'>".__($guid, 'Manage Alumni')."</a> > </div><div class='trailEnd'>".__($guid, 'Edit').'</div>';
    echo '</div>';

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    $alumniAlumnusID = $_GET['alumniAlumnusID'];
    if ($alumniAlumnusID == 'Y') { echo "<div class='error'>";
        echo __($guid, 'You have not specified one or more required parameters.');
        echo '</div>';
    } else {
        try {
            $data = array('alumniAlumnusID' => $alumniAlumnusID);
            $sql = 'SELECT alumniAlumnus.* FROM alumniAlumnus WHERE alumniAlumnusID=:alumniAlumnusID';
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }

        if ($result->rowCount() != 1) {
            echo "<div class='error'>";
            echo __($guid, 'The selected record does not exist, or you do not have access to it.');
            echo '</div>';
        } else {
            if ($_GET['graduatingYear'] != '') {
                echo "<div class='linkTop'>";
                  echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Alumni/alumni_manage.php&graduatingYear='.$_GET['graduatingYear']."'>".__($guid, 'Back to Search Results').'</a>';
                echo '</div>';
            }

            //Let's go!
            $row = $result->fetch();
            ?>
			<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module']."/alumni_manage_editProcess.php?alumniAlumnusID=$alumniAlumnusID&graduatingYear=".$_GET['graduatingYear'] ?>">
				<table class='smallIntBorder' cellspacing='0' style="width: 100%">
					<tr class='break'>
						<th colspan=2>
							<?php echo __($guid, 'Personal Details'); ?>
						</td>
					</tr>
					<tr>
						<td style='width: 275px'>
							<b><?php echo __($guid, 'Title') ?></b><br/>
						</td>
						<td class="right">
							<select style="width: 302px" name="title">
								<option value=""></option>
								<option <?php if ($row['title'] == 'Ms.') { echo 'selected'; } ?> value="Ms."><?php echo __($guid, 'Ms.') ?></option>
								<option <?php if ($row['title'] == 'Miss') { echo 'selected'; } ?> value="Miss"><?php echo __($guid, 'Miss') ?></option>
								<option <?php if ($row['title'] == 'Mr.') { echo 'selected'; } ?> value="Mr."><?php echo __($guid, 'Mr.') ?></option>
								<option <?php if ($row['title'] == 'Mrs.') { echo 'selected'; } ?> value="Mrs."><?php echo __($guid, 'Mrs.') ?></option>
								<option <?php if ($row['title'] == 'Dr.') { echo 'selected'; } ?> value="Dr."><?php echo __($guid, 'Dr.') ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<b><?php echo __($guid, 'First Name') ?> *</b><br/>
						</td>
						<td class="right">
							<input name="firstName" id="firstName" maxlength=30 value="<?php echo htmlPrep($row['firstName'])?>" type="text" style="width: 300px">
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
							<input name="surname" id="surname" maxlength=30 value="<?php echo htmlPrep($row['surname'])?>" type="text" style="width: 300px">
							<script type="text/javascript">
								var surname=new LiveValidation('surname');
								surname.add(Validate.Presence);
							</script>
						</td>
					</tr>
					<tr>
						<td>
							<b><?php echo __($guid, 'Official Name') ?></b><br/>
							<span style="font-size: 90%"><i><?php echo __($guid, 'Full name as shown in ID documents.') ?></i></span>
						</td>
						<td class="right">
							<input name="officialName" id="officialName" maxlength=150 value="<?php echo htmlPrep($row['officialName'])?>" type="text" style="width: 300px">
						</td>
					</tr>
					<tr>
						<td>
							<b><?php echo __($guid, 'Email') ?> *</b><br/>
						</td>
						<td class="right">
							<input name="email" id="email" maxlength=50 value="<?php echo htmlPrep($row['email'])?>" type="text" style="width: 300px">
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
								<option <?php if ($row['gender'] == 'F') { echo 'selected'; } ?> value="F"><?php echo __($guid, 'Female') ?></option>
								<option <?php if ($row['gender'] == 'M') { echo 'selected'; } ?> value="M"><?php echo __($guid, 'Male') ?></option>
								<option <?php if ($row['gender'] == 'Other') { echo 'selected'; } ?> value="F"><?php echo __($guid, 'Other') ?></option>
								<option <?php if ($row['gender'] == 'Unspecified') { echo 'selected'; } ?> value="M"><?php echo __($guid, 'Unspecified') ?></option>
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
								<option <?php if ($row['formerRole'] == 'Student') { echo 'selected'; } ?> value="Student"><?php echo __($guid, 'Student') ?></option>
								<option <?php if ($row['formerRole'] == 'Staff') { echo 'selected'; } ?> value="Staff"><?php echo __($guid, 'Staff') ?></option>
								<option <?php if ($row['formerRole'] == 'Parent') { echo 'selected'; } ?> value="Parent"><?php echo __($guid, 'Parent') ?></option>
								<option <?php if ($row['formerRole'] == 'Other') { echo 'selected'; } ?> value="Other"><?php echo __($guid, 'Other') ?></option>
							</select>
							<script type="text/javascript">
								var formerRole=new LiveValidation('formerRole');
								formerRole.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php echo __($guid, 'Select something!') ?>"});
							</script>
						</td>
					</tr>
					<tr>
						<td>
							<b><?php echo __($guid, 'Date of Birth') ?></b><br/>
							<span style="font-size: 90%"><i><?php echo __($guid, 'Format:').' '.$_SESSION[$guid]['i18n']['dateFormat']  ?></i></span>
						</td>
						<td class="right">
							<input name="dob" id="dob" maxlength=10 value="<?php echo dateConvertBack($guid, $row['dob'])?>" type="text" style="width: 300px">
							<script type="text/javascript">
								$(function() {
									$( "#dob" ).datepicker();
								});
							</script>
						</td>
					</tr>

					<tr class='break'>
						<th colspan=2>
							<?php echo __($guid, 'Tell Us More About Yourself'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><?php echo __($guid, 'Maiden Name') ?></b><br/>
							<span style="font-size: 90%"><i><?php echo __($guid, 'Your surname prior to marriage.') ?></i></span>
						</td>
						<td class="right">
							<input name="maidenName" id="maidenName" maxlength=30 value="<?php echo htmlPrep($row['maidenName'])?>" type="text" style="width: 300px">
						</td>
					</tr>
					<tr>
						<td>
							<b><?php echo __($guid, 'Username') ?></b><br/>
							<span style="font-size: 90%"><i><?php echo __($guid, 'If you are young enough, this is how you logged into computers.') ?></i></span>
						</td>
						<td class="right">
							<input name="username2" id="username2" maxlength=20 value="<?php echo htmlPrep($row['username'])?>" type="text" style="width: 300px">
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
									$selected = '';
									if ($row['graduatingYear'] == $i) {
										$selected = 'selected';
									}
									echo "<option $selected value='$i'>$i</option>";
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
									$selected = '';
									if ($row['address1Country'] == $rowSelect['printable_name']) {
										$selected = 'selected';
									}
									echo "<option $selected value='".$rowSelect['printable_name']."'>".htmlPrep(__($guid, $rowSelect['printable_name'])).'</option>';
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
							<input name="profession" id="profession" maxlength=30 value="<?php echo htmlPrep($row['profession'])?>" type="text" style="width: 300px">
						</td>
					</tr>
					<tr>
						<td>
							<b><?php echo __($guid, 'Employer') ?></b><br/>
						</td>
						<td class="right">
							<input name="employer" id="employer" maxlength=30 value="<?php echo htmlPrep($row['employer'])?>" type="text" style="width: 300px">
						</td>
					</tr>
					<tr>
						<td>
							<b><?php echo __($guid, 'Job Title') ?></b><br/>
						</td>
						<td class="right">
							<input name="jobTitle" id="jobTitle" maxlength=30 value="<?php echo htmlPrep($row['jobTitle'])?>" type="text" style="width: 300px">
						</td>
					</tr>

					<tr class='break'>
						<th colspan=2>
							<?php echo __($guid, 'Link To Gibbon User'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><?php echo __($guid, 'Existing User') ?></b><br/>
						</td>
						<td class="right">
							<select name="gibbonPersonID" id="gibbonPersonID" style="width: 302px">
								<?php
                                echo "<option value=''></option>";
								try {
									$dataSelect = array();
									$sqlSelect = 'SELECT gibbonPersonID, surname, preferredName, dob, username FROM gibbonPerson ORDER BY surname, preferredName';
									$resultSelect = $connection2->prepare($sqlSelect);
									$resultSelect->execute($dataSelect);
								} catch (PDOException $e) {
									echo 'error'.$e->getMessage();
								}
								while ($rowSelect = $resultSelect->fetch()) {
									$selected = '';
									if ($row['gibbonPersonID'] == $rowSelect['gibbonPersonID']) {
										$selected = 'selected';
									}
									echo "<option $selected value='".$rowSelect['gibbonPersonID']."'>".formatName('', $rowSelect['preferredName'], $rowSelect['surname'], 'Student', true).' ('.$rowSelect['username'];
									if ($rowSelect['dob'] != '') {
										echo  ' | '.dateConvertBack($guid, $rowSelect['dob']);
									}
									echo ')</option>';
								}
								?>
							</select>
						</td>
					</tr>

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
    }
}
?>
