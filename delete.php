<?php 
session_start();
require_once "pdo.php";


if(isset($_POST['cancel'])){
	header("Location:index.php");
	return;
}

if(isset($_POST['profile_id']) && isset($_POST['delete'])){

			$sql="DELETE FROM Profile WHERE profile_id=:xyz";
			$stmt=$pdo->prepare($sql);
			$stmt->execute(array(':xyz'=>$_POST['profile_id']));

			$_SESSION['success'] = 'Record deleted';
    		header( 'Location: index.php' ) ;
   			return;

}





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
		 $id=$row['profile_id'];


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

	<h1>Deleteing Profile</h1>
	<p>First Name: <?=$fn?></p>
	<p>Last Name: <?=$ln?></p>


	<form method="post">
		<p>	<input type="hidden" name="profile_id" value="<?=$id?>">
			<input type="submit" name="delete" value="Delete">
			<input type="submit" name="cancel" value="Cancel"></a></p>
		
	</form>

 
 </body>
 </html>

