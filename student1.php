<?php
require_once('TCPDF-main\TCPDF-main\tcpdf.php'); // Include TCPDF library

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
    $collegename = $_POST['collegename'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $date = $_POST['date'];
    $aadhar = $_POST['aadhar'];

    // Handle uploaded files with checks
    $passportPhoto = isset($_FILES['passportPhoto']) ? $_FILES['passportPhoto'] : null;
    $residenceProof = isset($_FILES['residenceProof']) ? $_FILES['residenceProof'] : null;
    $idPhoto = isset($_FILES['idphoto']) ? $_FILES['idphoto'] : null;

    // Define file paths
    $passportPhotoPath = "";
    $residenceProofPath = "";
    $idPhotoPath = "";

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

    // Check and move college ID file
    if ($idPhoto && $idPhoto['error'] == 0) {
        $idPhotoPath = "uploads/" . uniqid() . "_" . $idPhoto['name'];
        move_uploaded_file($idPhoto['tmp_name'], $idPhotoPath);
    }

    // Store data in MySQL
    $stmt = $conn->prepare("INSERT INTO students_pass (fullName, email, mobile, collegename, address, gender, date, aadhar, passportPhoto, residenceProof, idPhoto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $fullName, $email, $mobile, $collegename, $address, $gender, $date, $aadhar, $passportPhotoPath, $residenceProofPath, $idPhotoPath);

    if ($stmt->execute()) {
        // PDF generation
        $pdf = new TCPDF('L', 'mm', 'A7');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Application System');
        $pdf->SetTitle('Student Monthly Pass Application');
        $pdf->SetMargins(5, 5, 5);
        $pdf->AddPage();
        
        // Add a bold title at the top with extra space
        $pdf->SetFont('helvetica', 'B', 16); // Set font to bold and larger size
        $pdf->SetXY(10, 10); // Set position for the title
        $pdf->Cell(0, 10, 'Student Monthly Pass Application', 0, 1, 'C'); // Title at the top

        // Add more space between title and information
        $pdf->Ln(15);

        // Add text information
        $pdf->SetFont('helvetica', '', 10);
        $info = 
        "Full Name: $fullName\n" .
                "Email: $email\n" .
                "Mobile No: $mobile\n" .
                "College Name: $collegename\n" .
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
        $pdf->Output('student_application.pdf', 'I');
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>
