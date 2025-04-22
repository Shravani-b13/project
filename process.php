<?php
ob_start(); 
require 'TCPDF-main\TCPDF-main\tcpdf.php'; // Include the library

$servername = 
$username = "Shravani"; 
$password = 
$dbname = "pbl_project";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Handle file upload
    $targetDir = "uploads/";
    $imagePath = $targetDir . basename($_FILES["image"]["name"]);
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
        // Insert data into MySQL
        $stmt = $conn->prepare("INSERT INTO users (name, email, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $imagePath);
        $stmt->execute();
        $stmt->close();

        // Generate PDF
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);

        // Add text content
        $pdf->Cell(0, 10, "Name: $name", 0, 1);
        $pdf->Cell(0, 10, "Email: $email", 0, 1);

        // Add image to PDF
        $pdf->Image($imagePath, 15, 40, 50, 50);

        // Output PDF to browser or save it
        $pdf->Output("output.pdf", 'I'); // 'I' for inline view, 'F' for saving to file
    } else {
        echo "File upload failed.";
    }
    ob_end_flush();
}

$conn->close();
?>
