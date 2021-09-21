 <?php 

 		$sn="mysql:host=localhost;dbname=misc";
 		$us="fred";
 		$pas="zap";

 		$pdo= new PDO($sn,$us,$pas);

 		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



  ?>