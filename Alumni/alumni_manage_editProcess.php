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

include "../../functions.php" ;
include "../../config.php" ;

//New PDO DB connection
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}

@session_start() ;

$enableDescriptors=getSettingByScope($connection2, "Behaviour", "enableDescriptors") ;
$enableLevels=getSettingByScope($connection2, "Behaviour", "enableLevels") ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

$alumniAlumnusID=$_GET["alumniAlumnusID"] ;
$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) . "/alumni_manage_edit.php&alumniAlumnusID=$alumniAlumnusID&graduatingYear=" . $_GET["graduatingYear"] ;

if (isActionAccessible($guid, $connection2, "/modules/Alumni/alumni_manage_edit.php")==FALSE) {
	//Fail 0
	$URL.="&updateReturn=fail0" ;
	header("Location: {$URL}");
}
else {
	//Proceed!
	//Check if school year specified
	if ($alumniAlumnusID=="") {
		//Fail1
		$URL.="&updateReturn=fail1" ;
		header("Location: {$URL}");
	}
	else {
		try {
			$data=array("alumniAlumnusID"=>$alumniAlumnusID); 
			$sql="SELECT alumniAlumnus.* FROM alumniAlumnus WHERE alumniAlumnusID=:alumniAlumnusID" ; 
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			//Fail2
			$URL.="&updateReturn=fail2" ;
			header("Location: {$URL}");
			exit() ;
		}
	
		if ($result->rowCount()!=1) {
			//Fail 2
			$URL.="&updateReturn=fail2" ;
			header("Location: {$URL}");
		}
		else {
			//Proceed!
			$title=$_POST["title"] ;
			$surname=$_POST["surname"] ;
			$firstName=$_POST["firstName"] ;
			$officialName=$_POST["officialName"] ;
			$maidenName=$_POST["maidenName"] ;
			$gender=$_POST["gender"] ;
			$username=$_POST["username2"] ;
			$dob=$_POST["dob"] ;
			if ($dob=="") {
				$dob=NULL ;
			}
			else {
				$dob=dateConvert($guid, $dob) ;
			}
			$email=$_POST["email"] ;
			$address1Country=$_POST["address1Country"] ;
			$profession=$_POST["profession"] ;
			$employer=$_POST["employer"] ;
			$jobTitle=$_POST["jobTitle"] ;
			$graduatingYear=NULL ;
			if ($_POST["graduatingYear"]!="") {
				$graduatingYear=$_POST["graduatingYear"] ;
			}
			$formerRole=$_POST["formerRole"] ;
			$gibbonPersonID=NULL ;
			if ($_POST["gibbonPersonID"]!="") {
				$gibbonPersonID=$_POST["gibbonPersonID"] ;
			}
	
			if ($surname=="" OR $firstName=="" OR $gender=="" OR $email=="" OR $formerRole=="") {
				//Fail 3
				$URL.="&updateReturn=fail3" ;
				header("Location: {$URL}");
			}
			else {
				//Write to database
				try {
					$data=array("title"=>$title, "surname"=>$surname, "firstName"=>$firstName, "officialName"=>$officialName, "maidenName"=>$maidenName, "gender"=>$gender, "username"=>$username, "dob"=>$dob, "email"=>$email, "address1Country"=>$address1Country, "profession"=>$profession, "employer"=>$employer, "jobTitle"=>$jobTitle, "graduatingYear"=>$graduatingYear, "formerRole"=>$formerRole, "gibbonPersonID"=>$gibbonPersonID, "alumniAlumnusID"=>$alumniAlumnusID); 
					$sql="UPDATE alumniAlumnus SET title=:title, surname=:surname, firstName=:firstName, officialName=:officialName, maidenName=:maidenName, gender=:gender, username=:username, dob=:dob, email=:email, address1Country=:address1Country, profession=:profession, employer=:employer, jobTitle=:jobTitle, graduatingYear=:graduatingYear, formerRole=:formerRole, gibbonPersonID=:gibbonPersonID WHERE alumniAlumnusID=:alumniAlumnusID" ;
					$result=$connection2->prepare($sql);
					$result->execute($data);
				}
				catch(PDOException $e) { 
					//Fail 2
					$URL.="&updateReturn=fail2" ;
					header("Location: {$URL}");
					exit() ;
				}
			
				//Success 0
				$URL.="&updateReturn=success0" ;
				header("Location: {$URL}");
			}
		}
	}
}
?>