<?php 
require_once "pdo.php";
session_start();

if(isset($_POST['cancel'])){
			header("Location:index.php");
			return;
}

if(! isset($_SESSION['name'])){
	 die("ACCESS DENIED");
	 return;
}

if(! isset($_REQUEST['profile_id'])){
	$_SESSION['error']="Missing profile_id";
	header("Location:index.php");
	return;
}

//Load up the profile in question
$sql="SELECT * FROM Profile WHERE profile_id=:prof AND user_id=:uid";
$stmt=$pdo->prepare($sql);
$stmt->execute(array(
				':prof'=>$_REQUEST['profile_id'],
				':uid'=>$_SESSION['user_id']
				));
$profile=$stmt->fetch(PDO::FETCH_ASSOC);
if($profile===false){
	$_SESSION['error']="Could not load profile";
	header('Location:index.php');
	return;
}


//================Handle incoming data================================
if( isset($_POST['first_name']) && isset($_POST['last_name']) && 
	isset($_POST['email']) && isset($_POST['headline']) &&
	isset($_POST['summary'])){

	// ======función para validar datos del profile============
	function validateProfile(){
			if( strlen($_POST['first_name'])<1 || 
				strlen($_POST['last_name'])<1 || 
				strlen($_POST['email'])<1 || 
				strlen($_POST['headline'])<1 || 
				strlen($_POST['summary'])<1 ) {
				return "All fields are required"; //"All fields are required";
			}
			if(strpos($_POST['email'],'@')===false){
				return "Email address must contain @"; //"Email must have an at-sign(@)";
			}
			return true;
	} 
//=====================

		$msg=validateProfile();
		if(is_string($msg)){
			$_SESSION['error']=$msg;
			header("Location:edit.php?profile_id=".$_REQUEST['profile_id']);
			return;
		}

// ======función para validar datos de las posiciones============
function validatePos(){
	for($i=1; $i<=9; $i++){
		if(! isset($_POST['year'.$i])) continue;
		if(! isset($_POST['desc'.$i])) continue;

		$year=$_POST['year'.$i];
		$desc=$_POST['desc'.$i];
		if(strlen($year)==0 || strlen($desc)==0){
				return "All fields are required";
		}    

		if(! is_numeric($year)){
			return "Position year must be numeric";
		}
	}
	return true;
}
//=====================
		$msg=validatePos();
		if(is_string($msg)){
			$_SESSION['error']=$msg;
			header("Location:edit.php?profile_id=".$_REQUEST['profile_id']);
			return;
		}

// ======función para validar datos de Education============

//FALTA=====================================================
		function validateEdu(){
		for($i=1; $i<=9; $i++){
		if(! isset($_POST['edu_year'.$i])) continue;
		if(! isset($_POST['edu_school'.$i])) continue;

		$year=$_POST['edu_year'.$i];
		$school=$_POST['edu_school'.$i];
		if(strlen($year)==0 || strlen($school)==0){
				return "All fields are required";
		}

		if(! is_numeric($year)){
			return "Education year must be numeric";
		}
	}
	return true;
}
//=====================
		$msg=validateEdu();
		if(is_string($msg)){
			$_SESSION['error']=$msg;
			header("Location:edit.php?profile_id=".$_REQUEST['profile_id']);
			return;
		}

//======= Time to update data

                 $sql="UPDATE Profile SET first_name=:fn,last_name=:ln,email=:em, headline=:hd,summary=:sm WHERE profile_id=:pid AND user_id=:uid";
                 $stmt=$pdo->prepare($sql);
                 $stmt->execute(array(
                 			':pid'=>$_REQUEST['profile_id'],
                 			':uid'=>$_SESSION['user_id'],
                 			':fn'=>$_POST['first_name'],
                 			':ln'=>$_POST['last_name'],
                 			':em'=>$_POST['email'],
                 			':hd'=>$_POST['headline'],
                 			':sm'=>$_POST['summary'],
                 			
                 ));

                 //Clear out the old position entries
                 $sql="DELETE FROM Position WHERE profile_id=:pid";
                 $stmt=$pdo->prepare($sql);
                 $stmt->execute(array(':pid'=>$_REQUEST['profile_id']));


                 //Insert the position entries	
                $rank1=1;
				for($i=1; $i<=9; $i++){
					if(! isset($_POST['year'.$i])) continue;
					if(! isset($_POST['desc'.$i])) continue;
					$year=$_POST['year'.$i];
					$desc=$_POST['desc'.$i];

					$sql="INSERT INTO Position (profile_id,rank1, year, description) VALUES (:pid, :rank1, :year, :des)";
					$stmt=$pdo->prepare($sql);
					$stmt->execute(array(
							':pid'=> $_REQUEST['profile_id'],
							':rank1'=>$rank1,
							':year'=>$year,
							':des'=> $desc
					));

					$rank1++;
				}

				//Clear out the old educations entries
				$sql="DELETE FROM Education WHERE profile_id=:pid";
                 $stmt=$pdo->prepare($sql);
                 $stmt->execute(array(':pid'=>$_REQUEST['profile_id']));


                 //Insert the Education entries	
//FALTA====hecho=============================================
                 $rank1=1;
                 for($i=1;$i<=9;$i++){
                 	if( ! isset($_POST['edu_year'.$i]))   continue;
                 	if( ! isset($_POST['edu_school'.$i])) continue;

                 	$year=$_POST['edu_year'.$i];
                 	$school=$_POST['edu_school'.$i];

                 	$institution_id=false;
                 	$sql="SELECT institution_id FROM Institution WHERE name=:name" ;
                 	$stmt=$pdo->prepare($sql);
                 	$stmt->execute(array(':name'=>$school));
                 	$row=$stmt->fetch(PDO::FETCH_ASSOC);
                 	if($row!== false) $institution_id=$row['institution_id'];

                 	//If there was no institution, insert it
                 	if($institution_id===false){
                 		$stmt=$pdo->prepare('INSERT INTO Institution (name) VALUES (:name)');
                 		$stmt->execute(array(':name'=>$school));
                 		$institution_id=$pdo->lastInsertId();
                 	}

                 	$stmt=$pdo->prepare('INSERT INTO Education(profile_id,rank1,year,institution_id) VALUES (:pid, :rank1, :year, :iid)');
                 	$stmt->execute(array(
                 			':pid'=>$_POST['profile_id'],
                 			':rank1'=>$rank1,
                 			':year'=>$year,
                 			':iid'=>$institution_id
                 	));
                 	$rank1++;
                 }

                 $_SESSION['success']="Profile updated";
                 header("Location:index.php");
                 return;
}

// ======================agregado más arriba=================================
		// $sql="SELECT * FROM Profile WHERE profile_id=:xyz";
		// $stmt=$pdo->prepare($sql);
		// $stmt->execute(array(':xyz'=>$_GET['profile_id']));
		// $row=$stmt->fetch(PDO::FETCH_ASSOC);
		// if($row===false){
		// 	$_SESSION['error'] = 'Bad value for user_id';
  //   		header( 'Location: index.php' ) ;
  //  			return;
		// }
			 
		 $fn=htmlentities($profile['first_name']);
		 $ln=htmlentities($profile['last_name']);
		 $em=htmlentities($profile['email']);
		 $hd=htmlentities($profile['headline']);
		 $sm=htmlentities($profile['summary']);
		 $id=$profile['profile_id'];

function loadPos($pdo,$profile_id){
		$stmt=$pdo->prepare('SELECT * FROM Position WHERE profile_id=:prof ORDER BY rank1');
		$stmt->execute(array(':prof'=>$profile_id));
		$positions=array();
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$positions[]=$row;
		}
		return $positions;
}

// NOTE:What fetchAll() does...
// $positions=array();
// 		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
// 			$positions[]=$row;
// 		}

$positions=loadPos($pdo,$_REQUEST['profile_id']);

function loadEdu($pdo,$profile_id){
		$stmt=$pdo->prepare('SELECT year,name,rank1 FROM Education JOIN Institution ON Education.institution_id=Institution.institution_id WHERE profile_id=:prof ORDER BY rank1');
		$stmt->execute(array(':prof'=>$profile_id));
		$educations=$stmt->fetchAll(PDO::FETCH_ASSOC);
		return $educations;
}

$schools=loadEdu($pdo,$_REQUEST['profile_id']);


 ?>



 <html>
 <head>
 	<title>Pedro Pablo Aruata Mamani</title>
	<?php require_once "head.php"; ?>
 </head>
  <body style="font-family:sans-serif;">
 		<h1>Editing Automobile</h1>

 		<?php 
 				if(isset($_SESSION['error'])){
 					echo '<p style="color:red">'.$_SESSION['error'].'</p>';
 					unset($_SESSION['error']);
 				}

 		 ?>




 	<form method="post">

 		<p>First_name: <input type="text" name="first_name" value="<?=$fn?>"></p>
 		<p>Last_name: <input type="text" name="last_name" value="<?=$ln?>"></p>
 		<p>Email: <input type="text" name="email" value="<?=$em?>"></p>
 		<p>Headline:<br/> <input type="text" name="headline" value="<?=$hd?>"></p>
 		<p>Summary: <br/> <textarea name="summary" rows="8" cols="80"><?=$sm?></textarea>
 		<p><input type="hidden" name="profile_id" value="<?=$id?>">
 			

<p>Education:<input type="submit" id="addEdu" value="+">
<div id="edu_fields">
<?php
$contadorEdu=0;

if(count($schools)>0){
	
foreach($schools as $pos){
	echo '<div id="edu'.$pos['rank1'].'">';
	echo '<p>Year:';
	echo '<input type="text" name="edu_year'.$pos['rank1'].'" value="'.htmlentities($pos['year']).'">';
	echo '<input type="button" value="-" onclick="$(\'#edu'.$pos['rank1'].'\').remove(); return false;"></p>';

	echo '<p>School: <input type="text" size="80" name="edu_school'.$pos['rank1'].'" class="school" 
	value="'.htmlentities($pos['name']).'"</p></div>';

	$contadorEdu++;
}

}

 ?>	

</div></p>



<p>Position:<input type="submit" id="addPos" value="+">
<div id="position_fields">
<?php
$contadorPos=0;
foreach($positions as $pos){
	echo '<div id="position'.$pos['rank1'].'">';
	echo '<p>Year:';
	echo '<input type="text" name="year'.$pos['rank1'].'" value="'.htmlentities($pos['year']).'">';
	echo '<input type="button" value="-" onclick="$(\'#position'.$pos['rank1'].'\').remove(); return false;"></p>';

	echo '<p><textarea name="desc'.$pos['rank1'].'" rows="8" cols="80">'.htmlentities($pos['description']).'</textarea></p></div>';
	$contadorPos++;
}

 ?>	

</div>
</p>


<p>
<input type="submit" value="Save"> 
<input type="submit" name="cancel" value="Cancel"></p>
 
</form>

<script>


countPos= <?=$contadorPos?>;
countEdu= <?=$contadorEdu?>;

$(document).ready(function(){

		console.log('Document ready called');
		
		$('#addPos').click(function(event){
				event.preventDefault();
				if(countPos>=9){
					alert("Maximum of nine position entries exceeded");
					return;
				}
				countPos++;
				console.log("Adding position" + countPos);
				$('#position_fields').append(
						'<div id="position'+countPos+'"><p>Year: <input type="text" name="year'+countPos+'" value=""><input type="button" value="-" onclick="$(\'#position'+countPos+'\').remove(); return false;"></p><p><textarea name="desc'+countPos+'" rows="8" cols="80" ></textarea></p></div>');
		});


		$('#addEdu').click(function(event){
			event.preventDefault();
			if(countEdu>=9){
				alert("Maximum of nine education entries exceeded");
				return;
			}
			countEdu++;
			console.log("Adding education"+countEdu);

			//Grab some HTMLL with hot spots and insert into the DOM
			var source= $("#edu-template").html();
			$('#edu_fields').append(source.replace(/@COUNT@/g,countEdu));

			//Add the ven handler to the new ones
				$('.school').autocomplete({
					source: "school.php"
				});
			});

			$('.school').autocomplete({
				source:"school.php"
			});


	});

</script>

<script id="edu-template" type="text">
	<div id="edu@COUNT@">
		<p>Year: <input type="text" name="edu_year@COUNT@" value="">
			<input type="button" value="-" onclick="$('#edu@COUNT@').remove(); return false;"><br>

			<p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value=""></p>
		
	</div>
</script>

 </body>
 </html>