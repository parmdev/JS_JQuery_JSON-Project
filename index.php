 <?php 
require_once "pdo.php";
session_start();
 ?>
  
<html>
<head>
<title>Pedro Pablo Aruata Mamani</title>
<?php require_once "head.php"; ?>
</head>
<body>
<div class="container">  		
<h2>Resume Registry </h2>

	<?php 
        if(isset($_SESSION['success'])){
            echo '<p style="color:green">' . $_SESSION['success'] . '</p>';
            unset($_SESSION['success']);
        }

if(!isset($_SESSION['name']))	{
	echo '<p><a href="login.php">Please log in<a></p>' . "\n";
}else{
	echo '<p><a href="logout.php">Log Out<a></p>'. "\n";
}





$sql="SELECT * FROM Profile";
$stmt=$pdo->query($sql);
$row=$stmt->fetch(PDO::FETCH_ASSOC);
if($row!==false){

		echo  '<table border="1">';
		echo  '<tr><th>Name</th><th>Headline</th>';

		if(isset($_SESSION['name'])){

		echo '<th>Action</th>';			
			
		}else{
			echo '</tr>';
		}
	
	
			$sql="SELECT * FROM Profile";
			$stmt=$pdo->query($sql);
			while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
					//<a href=""></a>

		echo '<tr><td><a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name']).'  '.htmlentities($row['last_name']).'</a></td><td>'.htmlentities($row['headline']).'</td>';
		
		if(isset($_SESSION['name'])){

		echo '<td><a href="edit.php?profile_id='.$row['profile_id'].'"> Edit <a> / <a href="delete.php?profile_id='.$row['profile_id'].'"> Delete <a></td>';
		

			
		}else{
			echo '</tr>';
		}




		}

		echo '</table>';
		

}

if(isset($_SESSION['name'])){

		echo "<br>";
		echo '<p><a href="add.php">Add New Entry<a></p>';
		
			
		}

		
?>


		
</div></body></html>
