<!doctype html>
<html lang="en-US">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Transcribe</title>
<link href="styles/singlePage.css" rel="stylesheet" type="text/css">
<style type="text/css">


</style>


<script src="http://use.edgefonts.net/source-sans-pro:n2:default.js" type="text/javascript"></script>

</head>
<body>
<div class="container"> 
  <header> <a href=""><br>
    </a></header>

<h2 class="fon">
<?php


if(isset($_POST['save_audio']) && $_POST['save_audio']=="Upload Audio")
{
	 $dir='uploads/';
	 $types=array('audio/wav','audio/flac');
	 $flag=1;
	 $target_file=$dir.basename($_FILES['audioFile']['name']);
	 if(in_array($_FILES['audioFile']['type'],$types))
		 {
		$flag=1;
		echo " <br>";
		}
	 else
	 {
		 $flag=0;
	 }
	if(file_exists($target_file)) {
    $flag=-1;
	
	}
	 if($flag==0)
	 {
		 echo "<br>Media type not accepted <br>";
	 }
	 else if($flag==-1)
	 {
		 echo "<br><b>Sorry, file already exists </b><br>";
	 }
	 else
	 {
	 $audio_path=$dir.basename($_FILES['audioFile']['name']);
	 
	 if(move_uploaded_file($_FILES['audioFile']['tmp_name'],$audio_path))
	 {
		  echo" <br>";
		  echo 'The file '.basename($_FILES['audioFile']['name']).' has been uploaded succesfully <br>';
		  
		  $id=saveAudio($audio_path);
		  
	 }
	 else
	 {
		 echo "<br> There was a problem uploading your file";
	 }
	 }

}




function saveAudio($fileName)
{
	 $conn=mysqli_connect('localhost','root','','audiolibdb');
	 if(!$conn)
	 {
		 die('server not connected');
	 }
	 else
		 echo "<br>";
	 $query= "insert into audios(filename)values('{$fileName}')";
	 mysqli_query($conn,$query);
	 $query1="select id from audios where(filename='{$fileName}')";
	 $sqlr= mysqli_query($conn,$query1);
	
	/* while(){ */
	$row = mysqli_fetch_assoc($sqlr);
    foreach($row as $cname => $cvalue){
        $id1=$cvalue;
    }
	transcribe($fileName,$id1);
	
	 
	 
	 mysqli_close($conn);
}	 
function transcribe($fileName,$id1)
{
	echo "<br>";
$stturl ="https://speech.googleapis.com/v1/speech:recognize?key=<API KEY>";
$g= $fileName;
$upload = file_get_contents($g);
$bname=basename($g);
if(substr_compare($bname,"flac",-4)==0)
{
	$bitRate=44100;
	$encoding="FLAC";
}
else{
	$bitRate=16000;
	$encoding="LINEAR16";
}


$data = array(
    "config" => array(
        "encoding" => $encoding,
        "sampleRateHertz" =>$bitRate,
        "languageCode" => "en-us"
    ),
   "audio" => array(
        "content" =>base64_encode($upload)
    )
);

$data_string = json_encode($data);                                                              

$ch = curl_init($stturl);                                                                      
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
   'Content-Type: application/json',                                                                                
   'Content-Length: ' . strlen($data_string))                                                                       
);                                                                                                                   

$result = curl_exec($ch);
$result_array = json_decode($result, true);
// var_dump($result_array);
$n=sizeof($result_array['results']);
// echo "<br> ".$n."<br>";
$text=$result_array['results'][0]['alternatives'][0]['transcript'];

if($n>1)
{
for($i=1;$i<$n;$i++)
	$text=$text." ".($result_array['results'][$i]['alternatives'][0]['transcript']);
}

var_dump($result_array);
echo "<br> Transcript: ".$text;
//audiolibdb is a database in mysql
$conn=mysqli_connect('localhost','root','','audiolibdb');
	 if(!$conn)
	 {
		 die('server not connected');
	 }
	 else
	 { echo "<br> <br> Uploaded to database <br>";
$query2="update audios set text='{$text}' where id='{$id1}' ";
mysqli_query($conn,$query2);
if($text==NULL)
{
	$query3="delete from audios where id='{$id1}'";
	mysqli_query($conn,$query3);
	
}

mysqli_close($conn);
	 }

}
?>
</h2>

</body>
</html>