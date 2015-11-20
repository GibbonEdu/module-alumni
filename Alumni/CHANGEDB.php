<?php
//USE ;end TO SEPERATE SQL STATEMENTS. DON'T USE ;end IN ANY OTHER PLACES!

$sql=array() ;
$count=0 ;

//v0.1.00 - FIRST VERSION, SO NO CHANGES
$sql[$count][0]="0.1.00" ;
$sql[$count][1]="" ;

//v0.2.00 - DOES NOT EXIST, JUST GETTING READY FOR AWESOMENESS TO COME
$count++ ;
$sql[$count][0]="0.2.00" ;
$sql[$count][1]="
INSERT INTO `gibbonAction` (`gibbonActionID`, `gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES (NULL, (SELECT gibbonModuleID FROM gibbonModule WHERE name='Alumni'), 'Manage Alumni', 0, 'Admin', 'Allows privileged users to manage all alumni records.', 'alumni_manage.php, alumni_manage_add.php, alumni_manage_edit.php, alumni_manage_delete.php','alumni_manage.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'Y', 'Y', 'Y', 'Y');end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '1', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Alumni' AND gibbonAction.name='Manage Alumni'));end
ALTER TABLE `alumniAlumnus` CHANGE `graduatingYear` `graduatingYear` INT(4) NULL DEFAULT NULL;end
UPDATE alumniAlumnus SET graduatingYear=NULL WHERE graduatingYear=0;end
" ;

?>