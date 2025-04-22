<?php
ob_start();
require_once 'dompdf/vendor/autoload.php';
use Dompdf\Dompdf;

header("Content-Type: application/pdf");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pbl_project";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
 {
    $gender = htmlspecialchars($_POST['gender']);
    $aadhar=htmlspecialchars($_POST['aadhar']);
    $dateTime = htmlspecialchars($_POST['dateTime']);
   // $sql = "INSERT INTO daily_pass(gender, aadharno, datetime) VALUES ('$gender', '$aadhar', '$dateTime')";
    $stmt = $conn->prepare("INSERT INTO daily_pass (gender, aadharno, datetime) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $gender, $aadhar, $dateTime);
    if ($stmt->execute())  {
        echo "New record created successfully";
        $last_id = $stmt->insert_id;
        $dompdf = new Dompdf();
   
       /* $daily = "
            <h2 align='center' style='margin: 0;'>DAILY PASS</h2>
            <p><strong>Gender:</strong> $gender</p>
            <p><strong>Aadhar No.:</strong> $aadhar</p>
            <p><strong>Date & Time:</strong> $dateTime</p>
        ";*/
       /* $daily = "
    <html>
        <body style='background: linear-gradient(to bottom, #62cff4, #2c67f2); padding: 20px; font-family: Arial, sans-serif;'>
            <div style='background-color: #f0f8ff; padding: 20px; border-radius: 10px; color: black;'>
                <h2 align='center' style='margin: 0;'>DAILY PASS</h2>
                <p><strong>Gender:</strong> $gender</p>
                <p><strong>Aadhar No.:</strong> $aadhar</p>
                <p><strong>Date & Time:</strong> $dateTime</p>
            </div>
        </body>
    </html>
";*/$daily = "
    <html>
        <body style='background-color: #6CB4EE; padding: 5px; font-family: Arial, sans-serif;'>
            <div style='background-color: #6CB4EE; padding: 5px; border-radius: 10px; color: white;'>
                <h2 align='center' style='margin: 0%;'>DAILY PASS</h2>
                <p><strong>Gender:</strong> $gender</p>
                <p><strong>Aadhar No.:</strong> $aadhar</p>
                <p><strong>Date & Time:</strong> $dateTime</p>
            </div>
        </body>
    </html>
";
        $dompdf->loadHtml($daily);
        $dompdf->setPaper('A7', 'landscape');
        $dompdf->render();
        ob_end_clean();
        $dompdf->stream("Daily_Pass.pdf", ["Attachment" => false]);  
    } 
    else {
        echo "Error: " . $stmt->error;
    }
    // Close statement
    $stmt->close();
}
// Close connection
$conn->close();
?>