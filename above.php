<?php
require 'TCPDF-main\TCPDF-main\tcpdf.php'; // Ensure TCPDF is installed and the path is correct

$servername = 
$username = 
$password = 
$dbname = "pbl_project";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $fullName = $_POST['fullName'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $date = $_POST['date'];
    $aadhar = $_POST['aadhar'];

    // Handle the passport photo upload
    $targetDir = "uploads/";
    $passportPhotoPath = $targetDir . basename($_FILES["passportPhoto"]["name"]);

    if (move_uploaded_file($_FILES["passportPhoto"]["tmp_name"], $passportPhotoPath)) {
        // Insert data into the database
        $stmt = $conn->prepare("INSERT INTO above_pass (full_name, age, email, mobile, address, gender, date, aadhar, passport_photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssssss", $fullName, $age, $email, $mobile, $address, $gender, $date, $aadhar, $passportPhotoPath);
        if ($stmt->execute()) {
            // Generate the PDF
            $pdf = new TCPDF();
            /*$pdf = new TCPDF('L', 'mm', 'A6');
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Application System');
            $pdf->SetTitle('Above 60 Monthly Pass Application');
            $pdf->SetHeaderData('', 0, 'Above 60 Monthly Pass Application', '', [0, 0, 0], [255, 255, 255]);
            $pdf->setHeaderFont(['helvetica', '', 8]);
            $pdf->setFooterFont(['helvetica', '', 8]);
            $pdf->SetMargins(15, 27, 15);
            $pdf->SetAutoPageBreak(true, 25);
            $pdf->AddPage();

            // Add form information to the PDF
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Write(0, "Full Name: $fullName\n", '', 0, 'L', true, 0, false, false, 0);
            $pdf->Write(0, "Age: $age\n", '', 0, 'L', true, 0, false, false, 0);
            $pdf->Write(0, "Email: $email\n", '', 0, 'L', true, 0, false, false, 0);
            $pdf->Write(0, "Mobile No: $mobile\n", '', 0, 'L', true, 0, false, false, 0);
            $pdf->Write(0, "Address: $address\n", '', 0, 'L', true, 0, false, false, 0);
            $pdf->Write(0, "Gender: $gender\n", '', 0, 'L', true, 0, false, false, 0);
            $pdf->Write(0, "Date: $date\n", '', 0, 'L', true, 0, false, false, 0);
            $pdf->Write(0, "Aadhar (Last 4 digits): $aadhar\n", '', 0, 'L', true, 0, false, false, 0);

            // Add the passport photo to the PDF
            $pdf->Ln(10); // Add some spacing
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Write(0, "Passport Photo:\n", '', 0, 'L', true, 0, false, false, 0);
            $pdf->Image($passportPhotoPath, 15, $pdf->GetY(), 40, 50, '', '', '', true);*/
            // Generate the PDF
// Generate the PDF
$pdf = new TCPDF('L', 'mm', 'A7'); // 'L' for Landscape, 'mm' for millimeters, 'A7' for size
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Application System');
$pdf->SetTitle('Above 60 Monthly Pass Application');
$pdf->SetHeaderData('', 0, 'Above 60 Monthly Pass Application', '', [0, 0, 0], [255, 255, 255]);
$pdf->setHeaderFont(['helvetica', '', 8]); // Adjust font size for smaller page
$pdf->setFooterFont(['helvetica', '', 8]); // Adjust font size for smaller page
$pdf->SetMargins(5, 5, 5); // Smaller margins for A7 size
$pdf->SetAutoPageBreak(true, 5); // Smaller bottom margin
$pdf->AddPage();

// Add form information and passport photo side by side
$pdf->SetFont('helvetica', '', 10); // Use a smaller font for A7

// Left column (information)
$info = "Full Name: $fullName\n" .
        "Age: $age\n" .
        "Email: $email\n" .
        "Mobile No: $mobile\n" .
        "Address: $address\n" .
        "Gender: $gender\n" .
        "Date: $date\n" .
        "Aadhar (Last 4 digits): $aadhar";

$pdf->MultiCell(70, 5, $info, 0, 'L', false, 0, 5, 20, true); // Adjust width, line height, and starting position

// Right column (passport photo)
$pdf->Image($passportPhotoPath, 80, 20, 30, 30, '', '', '', true); // Adjust x, y, width, and height

            // Output the PDF
            $pdf->Output('Above_60_Monthly_Pass.pdf', 'I'); // Inline display
        } else {
            echo "Error inserting data into the database.";
        }
        $stmt->close();
    } else {
        echo "Error uploading passport photo. Please try again.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
