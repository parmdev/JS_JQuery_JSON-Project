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


if( isset($_POST['first_name']) && isset($_POST['last_name']) && 
	isset($_POST['email']) && isset($_POST['headline']) && 
	isset($_POST['summary'])){

// ======función para validar datos============
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
			header("Location:add.php");
			return;
		}

// ======función para validar datos============
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
			header("Location:add.php");
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
			header("Location:add.php");
			return;
		}


//Data is valid  - time to insert
		$sql="INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES ( :uid, :fn, :ln, :em, :he, :su)";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(
		  ':uid' => $_SESSION['user_id'],
		  ':fn' => $_POST['first_name'],
		  ':ln' => $_POST['last_name'],
		  ':em' => $_POST['email'],
		  ':he' => $_POST['headline'],
		  ':su' => $_POST['summary'])
		);

		$profile_id=$pdo->lastInsertId();

// Insert the position entries
		$rank1=1;
		for($i=1; $i<=9; $i++){
			if(! isset($_POST['year'.$i])) continue;
			if(! isset($_POST['desc'.$i])) continue;
			$year=$_POST['year'.$i];
			$desc=$_POST['desc'.$i];

			$sql="INSERT INTO Position (profile_id,rank1, year, description) VALUES (:pid, :rank1, :year, :des)";
			$stmt=$pdo->prepare($sql);
			$stmt->execute(array(
					':pid'=> $profile_id,
					':rank1'=>$rank1,
					':year'=>$year,
					':des'=> $desc
			));

			$rank1++;
		}

//Insert the Education entries	
//=================================
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
                 			':pid'=>$profile_id,
                 			':rank1'=>$rank1,
                 			':year'=>$year,
                 			':iid'=>$institution_id
                 	));
                 	$rank1++;
                 }


     $_SESSION['success']="Record added";
     header("Location:index.php");
     return;
		
}
?>
<html>
<head>
<title>Pedro Pablo Aruata Mamani</title>
<head>
<?php require_once "head.php"; ?>
</head>
<body>
<h1>Adding Profile for <?=$_SESSION['name']?></h1>

<?php 
	if(isset($_SESSION['error'])){
			echo '<p style="color:red">'.$_SESSION['error'].'</p>';
			unset($_SESSION['error']);
		}
?>

<form method="post">
<p>First Name: <input type="text" name="first_name" size="60"/></p>
<p>Last Name: <input type="text" name="last_name" size="60"/></p>
<p>Email: <input type="text" name="email" size="30"/></p>
<p>Headline:<br/> <input type="text" name="headline" size="80"/></p>
<p>Summary:<br/> <textarea name="summary" rows="8" cols="80"></textarea></p>


<p>Education:<input type="submit" id="addEdu" value="+">
<div id="edu_fields">
</div>
</p>


<p>
Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
</div>
</p>


<p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="Cancel">
</p> 

</form>
<script>

countPos=0;
countEdu=0;

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