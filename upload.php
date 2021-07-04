<?php

//starting session

session_start();

//initializing message variable

$message = "";

//database connection

$server = "localhost";
$user = "root";
$pass = ""; //no password
$db = "upload";


// Create connection
$conn = new mysqli($server, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "Connected successfully";

if(isset($_POST['upload']) && $_POST['upload'] == 'Upload'){

//file 


  if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK){
	
	//file information
	
	$filePath = $_FILES['file']['tmp_name'];
	$fileName = $_FILES['file']['name'];
	$fileSize = $_FILES['file']['size'];
	$fileType = $_FILES['file']['type'];
        $fileNameCmps = explode(".", $fileName); //spiliting strings
        $fileExtension = strtolower(end($fileNameCmps));
	
	
	
    // sanitize file-name

    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

    // check if file has one of the following extensions

    $allowedfileExtensions = array('jpg', 'gif', 'png', 'txt', 'xls', 'doc', 'pptx');
		
		
if (in_array($fileExtension, $allowedfileExtensions))

    {

      // File Directory For Upload

      $directory = './files/';

      $dest_path = $directory . $newFileName;
			
			//if the file uploads to the folder
			
      if(move_uploaded_file($filePath, $dest_path)) 

      {

        $message ='File is successfully uploaded.';
				
				//preventing sql injection
				
				$newfile = mysqli_real_escape_string($conn, $fileName);
				
				$sql = "INSERT INTO uploads (file) VALUES ('$newfile');"
				
				$result = mysqli_query($conn, $sql);
				
				if($result){
				
				$message = "The file was uploaded into the database";
				
				}else{
				
				$message = "The file was uploaded but failed to insert into the database";
				}
				

      }
			else{
			//folder failure
			 $message = 'There was some error moving the file to upload directory. Check if the folder exists';
			}
			
		}
		
		else

    {
      $message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);

    }
			}

  else
  {
	//other errors
    $message = 'There is an error in the file upload.<br>';
    $message .= 'Error:' . $_FILES['uploadedFile']['error'];
  }
			
	}

//storing temporarily
$_SESSION['message'] = $message;

header("Location: index.php");
?>
