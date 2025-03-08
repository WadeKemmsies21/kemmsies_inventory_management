<?php
require('fpdf.php'); // Include the FPDF library

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kemmsies_inventory";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all data from the items table
$sql = "SELECT item_number, item_description, quantity FROM items";
$result = $conn->query($sql);

// Create a new PDF document
$pdf = new FPDF();
$pdf->AddPage(); // Add a page

// Set font for the document
$pdf->SetFont('Arial', 'B', 12);

// Add title
$pdf->Cell(200, 10, 'Inventory List', 0, 1, 'C');

// Line break
$pdf->Ln(10);

// Set font for the table
$pdf->SetFont('Arial', 'B', 10);

// Table header
$pdf->Cell(60, 10, 'Item Number', 1, 0, 'C');
$pdf->Cell(80, 10, 'Item Description', 1, 0, 'C');
$pdf->Cell(40, 10, 'Quantity', 1, 1, 'C');

// Reset font to regular for table data
$pdf->SetFont('Arial', '', 10);

// Fetch and display data from the database
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(60, 10, $row['item_number'], 1, 0, 'C');
        $pdf->Cell(80, 10, $row['item_description'], 1, 0, 'C');
        $pdf->Cell(40, 10, $row['quantity'], 1, 1, 'C');
    }
} else {
    $pdf->Cell(0, 10, 'No data available', 0, 1, 'C');
}

// Output the PDF (either as a file or directly to the browser)
$pdf->Output('I', 'inventory_list.pdf');
// If you want to save it as a file, use: $pdf->Output('filename.pdf', 'D');
?>