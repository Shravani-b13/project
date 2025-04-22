<?php
ob_start();
require_once 'dompdf/vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

header("Content-Type: application/pdf");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$servername = 
$username =
$password =
$dbname = "pbl_project";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// File upload function
function uploadFile($file) {
    $uploadDirectory = 'uploads/';

    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0755, true);
    }

    // Get file properties
    $fileName = basename($file['name']);
    $fileTmpName = $file['tmp_name'];
    $fileDestination = $uploadDirectory . uniqid() . "_" . $fileName;

    // Validate and move uploaded file
    if (move_uploaded_file($fileTmpName, $fileDestination)) {
        return realpath($fileDestination); // Return absolute path
    } else {
        return null; // Return null on failure
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize variables for file paths
    $residenceProofPath = null;
    $passportPhotoPath = null;
    $idphotopath=null;

    // Upload proof of residence
    if (isset($_FILES['residenceProof'])) {
        $residenceProofPath = uploadFile($_FILES['residenceProof']);
        if (!$residenceProofPath) {
            echo "Error uploading proof of residence.";
            exit;
        }
    }
    if (isset($_FILES['idproof'])) {
        $residenceProofPath = uploadFile($_FILES['idproof']);
        if (!$idphotopath) {
            echo "Error uploading proof of residence.";
            exit;
        }
    }


    // Upload passport photo
    if (isset($_FILES['passportPhoto'])) {
        $passportPhotoPath = uploadFile($_FILES['passportPhoto']);
        if (!$passportPhotoPath) {
            
            echo "Error uploading passport photo.";
            exit;
        }
    }

    // Collect form data
    $gender = htmlspecialchars($_POST['gender']);
    $fullname = htmlspecialchars($_POST['fullName']);
    $dateTime = htmlspecialchars($_POST['date']);
    $email = htmlspecialchars($_POST['email']);
    $aadhar = htmlspecialchars($_POST['aadhar']);
    $mobile = htmlspecialchars($_POST['mobile']);
    $address = htmlspecialchars($_POST['address']);
    $collegename = htmlspecialchars($_POST['collegename']);

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO student_pass (fullname, email, mobileno, address, gender, aadharno, date, collegename) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $fullname, $email, $mobile, $address, $gender, $aadhar, $dateTime,$collegename);

    if ($stmt->execute()) {
        // Generate PDF after successful database entry
       // $dompdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true); 
        $options->set('debugPng', true);
        $options->set('debugKeepTemp', true);
        $options->set('isRemoteEnabled', true); // Enable external images
        $dompdf= new Dompdf($options);
        $dompdf->setOptions($options);
        if ($passportPhotoPath) {

            $passportPhotoPath = 'file://' . $_SERVER['DOCUMENT_ROOT'] . '/' . $passportPhotoPath;
            $imageData = base64_encode(file_get_contents($passportPhotoPath));
            $imageSrc = 'data:image/jpeg;base64,' . $imageData;
            
         // Debugging: check the final image path
        //echo "Image Path: " . $passportPhotoPath . "<br>";

       // $monthly .= "<p><strong>Passport Photo:</strong></p>
            //<img src=. $imageSrc . '" alt="Electric Bus" width="200" />
        }

       /* $html = "
        
        <h1 align='center'>Monthly Pass</h1>
        <p><strong>Full Name:</strong> $fullname </p>
        <p><strong>College Name:</strong> $collegename</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Mobile No.:</strong> $mobile</p>
        <p><strong>Address:</strong> $address</p>
        <p><strong>Gender:</strong> $gender</p>
        <p><strong>Aadhar No.:</strong> $aadhar</p>
        <p><strong>Date & Time:</strong> $dateTime</p>
        <p><strong>Passport Photo:</strong></p>
        <img src='$imageSrc' style='width:150px; height:auto;' />
        ";*/
        $html = "
    <div style='border: 2px solid blue; padding: 10px; border-radius: 10px;'>
        <h1 align='center'>Monthly Pass</h1>
        <p><strong>Full Name:</strong> $fullname</p>
        <p><strong>College Name:</strong> $collegename</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Mobile No.:</strong> $mobile</p>
        <p><strong>Address:</strong> $address</p>
        <p><strong>Gender:</strong> $gender</p>
        <p><strong>Aadhar No.:</strong> $aadhar</p>
        <p><strong>Date & Time:</strong> $dateTime</p>
        <p><strong>Passport Photo:</strong></p>
        <img src='$imageSrc' style='width:150px; height:auto;' />
    </div>
";


        $dompdf->loadHtml($html);
        $dompdf->setPaper('A6', 'landscape');
                $dompdf->render();

        // Clear output buffer and stream the PDF
        ob_end_clean();
        $dompdf->stream("monthly_Pass.pdf", ["Attachment" => false]);
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();

?>