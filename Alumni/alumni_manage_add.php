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
use Gibbon\Forms\DatabaseFormFactory;

//Module includes
include './modules/'.$session->get('module').'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Alumni/alumni_manage_add.php') == false) {
    //Acess denied
    $page->addError(__m('You do not have access to this action.'));
} else {
    $page->breadcrumbs
      ->add(__m('Manage Alumni'), 'alumni_manage.php')
      ->add(__m('Add'));

    $graduatingYear = $_GET['graduatingYear'] ?? '';
    $alumniAlumnusID = $_GET['alumniAlumnusID'] ?? '';

    $editLink = '';
    if (isset($_GET['editID'])) {
        $editLink = $session->get('absoluteURL').'/index.php?q=/modules/Alumni/alumni_manage_edit.php&alumniAlumnusID='.$_GET['editID'].'&graduatingYear='.$graduatingYear;
    }
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], $editLink, null);
    }

    $form = Form::create('action', $session->get('absoluteURL').'/modules/'.$session->get('module').'/alumni_manage_addProcess.php?graduatingYear='.$graduatingYear);
    $form->setFactory(DatabaseFormFactory::create($pdo));
    
    if (!empty($graduatingYear)) { 
        $form->addHeaderAction('back', __m('Back to Search Results'))
            ->setURL('/modules/Alumni/alumni_manage.php')
            ->addParam('graduatingYear', $graduatingYear)
            ->displayLabel();
    }

    $form->addHiddenValue('address', $session->get('address'));

    $form->addRow()->addHeading(__m('Personal Details'));

    $row = $form->addRow();
        $row->addLabel('title', __m('Title'));
        $row->addSelectTitle('title');

    $row = $form->addRow();
        $row->addLabel('firstName', __m('First Name'));
        $row->addTextField('firstName')->isRequired()->maxLength(30);

    $row = $form->addRow();
        $row->addLabel('surname', __m('Surname'));
        $row->addTextField('surname')->isRequired()->maxLength(30);

    $row = $form->addRow();
        $row->addLabel('officialName', __m('Official Name'))->description(__m('Full name as shown in ID documents.'));
        $row->addTextField('officialName')->maxLength(150);

    $row = $form->addRow();
        $row->addLabel('email', __m('Email'));
        $email = $row->addEmail('email')->isRequired()->maxLength(50);

    $row = $form->addRow();
        $row->addLabel('gender', __m('Gender'));
        $row->addSelectGender('gender')->isRequired();

    $row = $form->addRow();
        $row->addLabel('dob', __m('Date of Birth'));
        $row->addDate('dob');

    $formerRoles = [
        'Student' => __m('Student'),
        'Staff' => __m('Staff'),
        'Parent' => __m('Parent'),
        'Other' => __m('Other'),
    ];
    $row = $form->addRow();
        $row->addLabel('formerRole', __m('Main Role'))->description(__m('In what way, primarily, were you involved with the school?'));
        $row->addSelect('formerRole')->fromArray($formerRoles)->isRequired()->placeholder();

    $form->addRow()->addHeading(__m('Tell Us More About Yourself'));

    $row = $form->addRow();
        $row->addLabel('maidenName', __m('Maiden Name'))->description(__m('Your surname prior to marriage.'));
        $row->addTextField('maidenName')->maxLength(30);

    $row = $form->addRow();
        $row->addLabel('username2', __m('Username'))->description(__m('If you are young enough, this is how you logged into computers.'));
        $row->addTextField('username2')->maxLength(20);

    $row = $form->addRow();
        $row->addLabel('graduatingYear', __m('Graduating Year'));
        $row->addSelect('graduatingYear')->fromArray(range(date('Y'), date('Y')-100, -1))->selected($graduatingYear)->placeholder();

    $row = $form->addRow();
        $row->addLabel('address1Country', __m('Current Country of Residence'));
        $row->addSelectCountry('address1Country')->placeholder('');

    $row = $form->addRow();
        $row->addLabel('profession', __m('Profession'));
        $row->addTextField('profession')->maxLength(30);

    $row = $form->addRow();
        $row->addLabel('employer', __m('Employer'));
        $row->addTextField('employer')->maxLength(30);

    $row = $form->addRow();
        $row->addLabel('jobTitle', __m('Job Title'));
        $row->addTextField('jobTitle')->maxLength(30);

    $form->addRow()->addHeading(__m('Link To Gibbon User'));

    $row = $form->addRow();
        $row->addLabel('gibbonPersonID', __m('Existing User'));
        $row->addSelectUsers('gibbonPersonID', $gibbon->session->get('gibbonSchoolYearID'))->placeHolder();

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
}
