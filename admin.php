<!doctype html>
<html lang="en-US">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Transcripto Admin</title>
<link href="styles/singlePage.css" rel="stylesheet" type="text/css">
<script src="http://use.edgefonts.net/source-sans-pro:n2:default.js" type="text/javascript"></script>

</head>
<body>
<div class="container"> 
  <header> <a href="">
    </a></header>
	
	<h2 class ="">Transcripts
	<br>
	<?php
	$conn=mysqli_connect('localhost','root','','audiolibdb');
	 if(!$conn)
	 {
		 die('server not connected');
	 }
	 else
	 {
$query="select * from audios order by (id) desc";
$result=mysqli_query($conn,$query);
echo '<table border=1px>';  
echo'<th>ID</th><th>Directory</th><th>Text</th>'; 

while($data = mysqli_fetch_array($result))
{

echo'<tr>'; 
echo '<td>'.$data['id'].'</td><td>'.$data['filename'].'</td><td>'.$data['text'].'</td>'; 
echo'</tr>'; 
}
mysqli_close($conn);

echo '</table>'; 
	 } 
?></h2>
</body>
</html>