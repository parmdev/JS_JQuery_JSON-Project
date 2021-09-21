<?php 
require_once "pdo.php";
session_start();



$sql="SELECT * FROM Profile WHERE profile_id=:xyz";
		$stmt=$pdo->prepare($sql);
		$stmt->execute(array(':xyz'=>$_GET['profile_id']));
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		if($row===false){
			$_SESSION['error'] = 'Bad value for user_id';
    		header( 'Location: index.php' ) ;
   			return;
		}

		 $fn=htmlentities($row['first_name']);
		 $ln=htmlentities($row['last_name']);
		 $em=htmlentities($row['email']);
		 $hd=htmlentities($row['headline']);
		 $sm=htmlentities($row['summary']);
		 $id=$row['profile_id'];

function loadPos($pdo,$profile_id){
		$stmt=$pdo->prepare('SELECT * FROM Position WHERE profile_id=:prof ORDER BY rank1');
		$stmt->execute(array(':prof'=>$profile_id));
		$positions=array();
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$positions[]=$row;
		}
		return $positions;
}

$positions=loadPos($pdo,$_REQUEST['profile_id']);



function loadEdu($pdo,$profile_id){
		$stmt=$pdo->prepare('SELECT year,name FROM Education JOIN Institution ON Education.institution_id=Institution.institution_id WHERE profile_id=:prof ORDER BY rank1');
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
<style type="text/css">
body {
width: 70%;
margin: 0 auto;
}
</style>
 </head>
 <body>

 	<h1>Profile Information</h1>
 	

 	<p>First Name: <?=$fn?></p>
 	<p>Last Name: <?=$ln?></p>
 	<p>Email: <?=$em?></p>
 	<p>Headline: <br> <?=$hd?></p>
 	<p style="color: red ;">Summary: <br><?=$sm?></p>

 	<p>Education</p>

 	<?php 
 	echo '<ul>';
 		foreach($schools as $pos){
 			
 			echo '<li>'. htmlentities($pos['year']).': '.htmlentities($pos['name']).'</li>';
 		}
 	echo '</ul>';	
 	 ?>

 	<p>Position</p>

 	<?php 
 	echo '<ul>';
 		foreach($positions as $pos){
 			
 			echo '<li>'. htmlentities($pos['year']).': '.htmlentities($pos['description']).'</li>';
 		}
 	echo '</ul>';	
 	 ?>
 	
 	<a href="index.php">Done</a>

 
 </body>
 </html>