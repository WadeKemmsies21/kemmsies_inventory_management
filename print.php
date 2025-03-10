<?php
require('fpdf.php'); 

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kemmsies_inventory";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT item_number, item_description, quantity FROM items";
$result = $conn->query($sql);

$pdf = new FPDF();
$pdf->AddPage(); 

$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(200, 10, 'Inventory List', 0, 1, 'C');

$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(50, 10, 'Item Number', 1, 0, 'C');
$pdf->Cell(70, 10, 'Item Description', 1, 0, 'C');
$pdf->Cell(30, 10, 'Quantity', 1, 0, 'C');
$pdf->Cell(15, 10, 'Act.', 1, 1, 'C');

$pdf->SetFont('Arial', '', 10);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        
        $pdf->Cell(50, 10, $row['item_number'], 1, 0, 'C');
        $pdf->Cell(70, 10, $row['item_description'], 1, 0, 'C');
        $pdf->Cell(30, 10, $row['quantity'], 1, 0, 'C');

        $pdf->Cell(15, 10, '', 1, 1, 'C'); 
    }
} else {
    $pdf->Cell(0, 10, 'No data available', 0, 1, 'C');
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="inventory_list.pdf"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

$pdf->Output('I', 'inventory_list.pdf');
?>
