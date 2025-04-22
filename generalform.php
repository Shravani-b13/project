<?php
require_once('TCPDF-main\TCPDF-main\tcpdf.php'); // Include TCPDF library

$servername = 
$username = "Shravani"; 
$password =  
$dbname = "pbl_project";

$conn = new mysqli($servername, $username, $password, $dbname);


try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Define the upload directory
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];
    $gender = $_POST['gender']; // Capture the selected gender
    $date = $_POST['date'];
    $aadhar = $_POST['aadhar'];

    // Handle file uploads
    $residenceProofPath = $uploadDir . basename($_FILES['residenceProof']['name']);
    $passportPhotoPath = $uploadDir . basename($_FILES['passportPhoto']['name']);

    if (move_uploaded_file($_FILES['residenceProof']['tmp_name'], $residenceProofPath) &&
        move_uploaded_file($_FILES['passportPhoto']['tmp_name'], $passportPhotoPath)) {
        
        // Create a PDF with TCPDF
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Bus Pass System');
        $pdf->SetTitle('Bus Pass Application');
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 12);

        $html = "
        <h1>General Bus Monthly Pass Application</h1>
        <p><strong>Full Name:</strong> $fullName</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Mobile No:</strong> $mobile</p>
        <p><strong>Address:</strong> $address</p>
        <p><strong>Gender:</strong> $gender</p> <!-- Display selected gender -->
        <p><strong>Date:</strong> $date</p>
        <p><strong>Aadhar No (Last 4 digits):</strong> $aadhar</p>
        ";

        $pdf->writeHTMLCell(100, 0, 10, 30, $html, 0, 0, 0, true, 'L', true);

        $pdf->Image($passportPhotoPath, 120, 30, 60, 60, '', '', '', true);


        $sql = "INSERT INTO finalgeneralpass(
                    full_name, email, mobile, address, gender, application_date, 
                    aadhar_last4, residence_proof_path, passport_photo_path, pdf_path
                ) VALUES (
                    :full_name, :email, :mobile, :address, :gender, :application_date, 
                    :aadhar_last4, :residence_proof_path, :passport_photo_path, ''
                )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':full_name' => $fullName,
            ':email' => $email,
            ':mobile' => $mobile,
            ':address' => $address,
            ':gender' => $gender, 
            ':application_date' => $date,
            ':aadhar_last4' => $aadhar,
            ':residence_proof_path' => $residenceProofPath,
            ':passport_photo_path' => $passportPhotoPath
        ]);

        // Directly output the PDF to the browser
        $pdf->Output('BusPass_' . time() . '.pdf', 'I'); // 'I' displays the PDF in the browser
        exit;
    } else {
        echo "<h2>Error</h2><p>File upload failed. Please try again.</p>";
    }
}
?>

