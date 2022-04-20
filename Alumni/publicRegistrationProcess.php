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

use Gibbon\Services\Format;
use Gibbon\Module\Alumni\AlumniGateway;
use Gibbon\Domain\System\SettingGateway;
use Gibbon\Forms\CustomFieldHandler;

include '../../gibbon.php';

$URL = $session->get('absoluteURL').'/index.php?q=/modules/Alumni/publicRegistration.php';

$settingGateway = $container->get(SettingGateway::class);
$enablePublicRegistration = $settingGateway->getSettingByScope('Alumni', 'showPublicRegistration');
$loggedIn = $session->has('username');

if ($enablePublicRegistration != "Y" || ($enablePublicRegistration && !empty($loggedIn))) {
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
    $dob = $_POST['dob'] ? Format::dateConvert($_POST['dob']) : '';
    $email = $_POST['email'] ?? '';
    $address1Country = $_POST['address1Country'] ?? '';
    $profession = $_POST['profession'] ?? '';
    $employer = $_POST['employer'] ?? '';
    $jobTitle = $_POST['jobTitle'];
    $graduatingYear = $_POST['graduatingYear'] ?? '';
    $formerRole = $_POST['formerRole'];

    if (empty($surname) or empty($firstName) or empty($officialName) or empty($gender) or empty($dob) or empty($email) or empty($formerRole)) {
        //Fail 3
        $URL .= '&return=error3';
        header("Location: {$URL}");
    } else {
        //Check publicRegistrationMinimumAge
        $publicRegistrationMinimumAge = $settingGateway->getSettingByScope('User Admin', 'publicRegistrationMinimumAge');

        if (empty($publicRegistrationMinimumAge)) {
            $ageFail = true;
        } elseif ($publicRegistrationMinimumAge > 0 and $publicRegistrationMinimumAge > (new DateTime('@'.Format::timestamp($dob)))->diff(new DateTime())->y) {
            $ageFail = true;
        } else {
            $ageFail = false;
        }

        if ($ageFail == true) {
            //Fail 5
            $URL .= '&return=error5';
            header("Location: {$URL}");
        } else {
            $alumniGateway = $container->get(AlumniGateway::class);
            //Check for uniqueness of username
            $existEmail = $alumniGateway->selectBy(['email' => $email])->fetch();

            if (!empty($existEmail)) {
                //Fail 7
                $URL .= '&return=error7';
                header("Location: {$URL}");
                exit();
            }
            else {

                $customRequireFail = false;
                $fields = $container->get(CustomFieldHandler::class)->getFieldDataFromPOST('Alumni', [], $customRequireFail);

                if ($customRequireFail) {
                    $URL .= '&return=error1';
                    header("Location: {$URL}");
                    exit;
                }

                //Write to database
                $data = ['title' => $title, 'surname' => $surname, 'firstName' => $firstName, 'officialName' => $officialName, 'maidenName' => $maidenName, 'gender' => $gender, 'username' => $username, 'dob' => $dob, 'email' => $email, 'address1Country' => $address1Country, 'profession' => $profession, 'employer' => $employer, 'jobTitle' => $jobTitle, 'graduatingYear' => $graduatingYear, 'formerRole' => $formerRole, 'fields' => $fields];
                $dataAlumni = array_filter($data, function($field) { return !empty($field[0]); });
                
                $alumniGateway->insert($dataAlumni);

                //Success 0
                $URL .= '&return=success0';
                header("Location: {$URL}");
            }
        }
    }
}
