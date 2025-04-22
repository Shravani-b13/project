<?php
/*require_once('TCPDF-main\TCPDF-main\tcpdf.php'); // Include TCPDF library


$servername = "localhost";
$username = 
$password = 
$dbname = "pbl_project";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $date = $_POST['date'];
    $aadhar = $_POST['aadhar'];

    // Handle uploaded files
    $passportPhoto = $_FILES['passportPhoto'];
    $passportPhotoPath = "uploads/" . uniqid() . "_" . $passportPhoto['name'];
    move_uploaded_file($passportPhoto['tmp_name'], $passportPhotoPath);

    // Store data in MySQL
    $stmt = $conn->prepare("INSERT INTO general_pass (fullName, email, mobile, address, gender, date, aadhar, passportPhoto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $fullName, $email, $mobile, $address, $gender, $date, $aadhar, $passportPhotoPath);
    if ($stmt->execute()) {
        // PDF generation
        $pdf = new TCPDF('L', 'mm', 'A7');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Application System');
        $pdf->SetTitle('General Bus Monthly Pass Application');
        $pdf->SetMargins(5, 5, 5);
        $pdf->AddPage();

        // Add text information
      // Generate and display the PDF in the browser
$pdf = new TCPDF('L', 'mm', 'A7');
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Application System');
$pdf->SetTitle('General Bus Monthly Pass ');
$pdf->SetMargins(5, 5, 5);
$pdf->AddPage();

// Add text information
$pdf->SetFont('helvetica', '', 10);
$info = "General Bus Monthly Pass ".
        "Full Name: $fullName\n" .
        "Email: $email\n" .
        "Mobile No: $mobile\n" .
        "Address: $address\n" .
        "Gender: $gender\n" .
        "Date: $date\n" .
        "Aadhar (Last 4 digits): $aadhar";
$pdf->MultiCell(70, 5, $info, 0, 'L', false, 0, 5, 20, true);

// Add the passport photo
$pdf->Image($passportPhotoPath, 80, 20, 30, 25, '', '', '', true);

// Stream the PDF to the browser
$pdf->Output('application.pdf', 'I');


        echo "Application successfully submitted! PDF generated.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connection
    $stmt->close();
    $conn->close();
}*/

require_once('TCPDF-main\TCPDF-main\tcpdf.php'); // Include TCPDF library

$servername = "localhost";
$username = "Shravani"; 
$password = "Shravani@13"; 
$dbname = "pbl_project";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $date = $_POST['date'];
    $aadhar = $_POST['aadhar'];

    // Handle uploaded files with checks
    $passportPhoto = isset($_FILES['passportPhoto']) ? $_FILES['passportPhoto'] : null;
    $residenceProof = isset($_FILES['residenceProof']) ? $_FILES['residenceProof'] : null;
    $ageProof = isset($_FILES['ageproof']) ? $_FILES['ageproof'] : null;

    // Define file paths
    $passportPhotoPath = "";
    $residenceProofPath = "";
    $ageProofPath = "";

    // Check and move passport photo file
    if ($passportPhoto && $passportPhoto['error'] == 0) {
        $passportPhotoPath = "uploads/" . uniqid() . "_" . $passportPhoto['name'];
        move_uploaded_file($passportPhoto['tmp_name'], $passportPhotoPath);
    }

    // Check and move residence proof file
    if ($residenceProof && $residenceProof['error'] == 0) {
        $residenceProofPath = "uploads/" . uniqid() . "_" . $residenceProof['name'];
        move_uploaded_file($residenceProof['tmp_name'], $residenceProofPath);
    }

    // Check and move age proof file
    if ($ageProof && $ageProof['error'] == 0) {
        $ageProofPath = "uploads/" . uniqid() . "_" . $ageProof['name'];
        move_uploaded_file($ageProof['tmp_name'], $ageProofPath);
    }

    // Store data in MySQL
    $stmt = $conn->prepare("INSERT INTO general_pass (fullName, email, mobile, address, gender, date, aadhar, passportPhoto, residenceProof, ageProof) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $fullName, $email, $mobile, $address, $gender, $date, $aadhar, $passportPhotoPath, $residenceProofPath, $ageProofPath);

    if ($stmt->execute()) {
        // PDF generation
        $pdf = new TCPDF('L', 'mm', 'A7');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Application System');
        $pdf->SetTitle('General Bus Monthly Pass Application');
        $pdf->SetMargins(5, 5, 5);
        $pdf->AddPage();

        // Add text information
        $pdf->SetFont('helvetica', '', 8);
        $info = "Full Name: $fullName\n" .
                "Email: $email\n" .
                "Mobile No: $mobile\n" .
                "Address: $address\n" .
                "Gender: $gender\n" .
                "Date: $date\n" .
                "Aadhar (Last 4 digits): $aadhar";
        $pdf->MultiCell(70, 5, $info, 0, 'L', false, 0, 5, 20, true);

        // Add the passport photo (if uploaded)
        if ($passportPhotoPath) {
            $pdf->Image($passportPhotoPath, 80, 20, 30, 25, '', '', '', true);
        }

        // Output the PDF to the browser
        $pdf->Output('application.pdf', 'I');
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>
