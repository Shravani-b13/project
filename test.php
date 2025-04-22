<?php

// Include the Dompdf autoload file
require 'dompdf/vendor/autoload.php';

// Import the Dompdf namespace
use Dompdf\Dompdf;
use Dompdf\Options;

// Create a new Dompdf instance
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true); // Enable PHP to process local file
$dompdf = new Dompdf($options);

// Define the HTML content
$html = '
<html>
<head>
    <title>PDF with Local Image</title>
</head>
<body>
    <h1>PDF with Local Image Example</h1>
    <p>This is an example of a PDF with a local image in the htdocs folder.</p>
    <img src="file://' . $_SERVER['DOCUMENT_ROOT'] . '/PBL/css/electricbus.jpg" alt="Electric Bus" width="200" />
</body>
</html>';

// Load the HTML content into Dompdf
$dompdf->loadHtml($html);

// Set paper size (optional)
$dompdf->setPaper('A4', 'portrait');

// Render the PDF
$dompdf->render();

// Output the generated PDF (Dompdf will display the PDF in the browser)
$dompdf->stream('example.pdf', array('Attachment' => 0)); // Set 'Attachment' => 0 to display the PDF in the browser

?>
