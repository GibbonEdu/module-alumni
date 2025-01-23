<?php
/*
Gibbon: the flexible, open school platform
Founded by Ross Parker at ICHK Secondary. Built by Ross Parker, Sandra Kuipers and the Gibbon community (https://gibbonedu.org/about/)
Copyright © 2010, Gibbon Foundation
Gibbon™, Gibbon Education Ltd. (Hong Kong)

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
use Gibbon\Services\Format;
use Gibbon\Domain\System\SettingGateway;
use Gibbon\Module\Alumni\AlumniGateway;
use Gibbon\Forms\CustomFieldHandler;

include '../../gibbon.php';

$settingGateway = $container->get(SettingGateway::class);
$enableDescriptors = $settingGateway->getSettingByScope('Behaviour', 'enableDescriptors');
$enableLevels = $settingGateway->getSettingByScope('Behaviour', 'enableLevels');

$URL = $session->get('absoluteURL').'/index.php?q=/modules/'.getModuleName($_POST['address']).'/alumni_manage_add.php&graduatingYear='.$_GET['graduatingYear'];

if (isActionAccessible($guid, $connection2, '/modules/Alumni/alumni_manage_add.php') == false) {
    //Fail 0
    $URL .= '&return=error0';
    header("Location: {$URL}");
} else {
    //Proceed!
    $title = $_POST['title'] ?? '';
    $surname = $_POST['surname'] ?? '';
    $firstName = $_POST['firstName'] ?? '';
    $officialName = $_POST['officialName'] ?? '';
    $maidenName = $_POST['maidenName'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $username = $_POST['username2'] ?? '';
    $dob = !empty($_POST['dob']) ? Format::dateConvert($_POST['dob']) : null;
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $address1Country = $_POST['address1Country'] ?? '';
    $profession = $_POST['profession'] ?? '';
    $employer = $_POST['employer'] ?? '';
    $jobTitle = $_POST['jobTitle'] ?? '';
    $graduatingYear = $_POST['graduatingYear'] ?? null;
    $formerRole = $_POST['formerRole'] ?? null;
    $gibbonPersonID = $_POST['gibbonPersonID'] ?? null;

    if (empty($surname) or empty($firstName) or empty($gender) or empty($email) or empty($formerRole)) {
        //Fail 3
        $URL .= '&return=error3';
        header("Location: {$URL}");
    } else {

        $customRequireFail = false;
        $fields = $container->get(CustomFieldHandler::class)->getFieldDataFromPOST('Alumni', [], $customRequireFail);

        if ($customRequireFail) {
            $URL .= '&return=error1';
            header("Location: {$URL}");
            exit;
        }
        
        $alumniGateway = $container->get(AlumniGateway::class);
        //Write to database
        $dataAlumni = ['title' => $title, 'surname' => $surname, 'firstName' => $firstName, 'officialName' => $officialName, 'maidenName' => $maidenName, 'gender' => $gender, 'username' => $username, 'dob' => $dob, 'email' => $email, 'address1Country' => $address1Country, 'profession' => $profession, 'employer' => $employer, 'jobTitle' => $jobTitle, 'graduatingYear' => $graduatingYear, 'formerRole' => $formerRole, 'gibbonPersonID' => $gibbonPersonID, 'fields' => $fields];

        $AI = $alumniGateway->insertAndUpdate($dataAlumni, $dataAlumni);

        //Success 0
        $URL .= '&return=success0&editID='.$AI;
        header("Location: {$URL}");
    }
}
