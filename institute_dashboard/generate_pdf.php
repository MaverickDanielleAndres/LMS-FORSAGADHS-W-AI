<?php
session_start();
require_once('../tcpdf/tcpdf.php'); // You'll need to install TCPDF library
include_once "../config.php";

// Check if user is authorized
if ($_SESSION['role'] != "Texas") {
    header("Location: ../index.php");
    exit();
}

// Get update ID from URL
if (!isset($_GET['updateid']) || empty($_GET['updateid'])) {
    header("Location: updates_list.php");
    exit();
}

$updateid = mysqli_real_escape_string($conn, $_GET['updateid']);

// Fetch update data
$query = "SELECT * FROM updatemaster WHERE UpdateId = '$updateid'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: updates_list.php");
    exit();
}

$update = mysqli_fetch_assoc($result);

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Update Management System');
$pdf->SetAuthor($update['UpdateUploadedBy']);
$pdf->SetTitle('Update: ' . $update['UpdateTitle']);
$pdf->SetSubject('Update Information');

// Set default header data
$pdf->SetHeaderData('', 0, 'Update Information', 'Generated on ' . date('Y-m-d H:i:s'));

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// Set font for title
$pdf->SetFont('helvetica', 'B', 20);
$pdf->Cell(0, 15, 'UPDATE INFORMATION', 0, 1, 'C');
$pdf->Ln(10);

// Update Title
$pdf->SetFont('helvetica', 'B', 16);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 10, 'Title', 1, 1, 'L', true);
$pdf->SetFont('helvetica', '', 12);
$pdf->MultiCell(0, 10, $update['UpdateTitle'], 1, 'L');
$pdf->Ln(5);

// Upload Information
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 8, 'Upload Details', 0, 1, 'L');
$pdf->Ln(2);

// Create a table for upload details
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetFillColor(230, 230, 230);

// Uploaded By
$pdf->Cell(40, 8, 'Uploaded By:', 1, 0, 'L', true);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 8, $update['UpdateUploadedBy'], 1, 1, 'L');

// Upload Date
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(40, 8, 'Upload Date:', 1, 0, 'L', true);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 8, date('F d, Y', strtotime($update['UpdateUploadDate'])), 1, 1, 'L');

// Upload Time (if available)
if (isset($update['UpdateUploadTime'])) {
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(40, 8, 'Upload Time:', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 8, $update['UpdateUploadTime'], 1, 1, 'L');
}

$pdf->Ln(10);

// Description (if available)
if (isset($update['UpdateDescription']) && !empty($update['UpdateDescription'])) {
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 8, 'Description', 0, 1, 'L');
    $pdf->Ln(2);
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetFillColor(248, 249, 250);
    $pdf->MultiCell(0, 8, $update['UpdateDescription'], 1, 'L', true);
    $pdf->Ln(10);
}

// Image
if (!empty($update['UpdateFile'])) {
    $imagePath = "../src/uploads/updates/" . $update['UpdateFile'];
    
    if (file_exists($imagePath)) {
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 8, 'Update Image', 0, 1, 'L');
        $pdf->Ln(5);
        
        // Get image dimensions and calculate appropriate size for PDF
        $imageInfo = getimagesize($imagePath);
        if ($imageInfo) {
            $maxWidth = 150; // Maximum width in PDF units
            $maxHeight = 100; // Maximum height in PDF units
            
            $imgWidth = $imageInfo[0];
            $imgHeight = $imageInfo[1];
            
            // Calculate scaling
            $widthScale = $maxWidth / $imgWidth;
            $heightScale = $maxHeight / $imgHeight;
            $scale = min($widthScale, $heightScale);
            
            $newWidth = $imgWidth * $scale;
            $newHeight = $imgHeight * $scale;
            
            // Center the image
            $x = ($pdf->getPageWidth() - $newWidth) / 2;
            
            try {
                $pdf->Image($imagePath, $x, $pdf->GetY(), $newWidth, $newHeight, '', '', '', false, 300, '', false, false, 1);
                $pdf->Ln($newHeight + 10);
            } catch (Exception $e) {
                $pdf->SetFont('helvetica', 'I', 10);
                $pdf->Cell(0, 8, 'Image could not be loaded: ' . $update['UpdateFile'], 0, 1, 'C');
                $pdf->Ln(5);
            }
        }
    } else {
        $pdf->SetFont('helvetica', 'I', 10);
        $pdf->Cell(0, 8, 'Image file not found: ' . $update['UpdateFile'], 0, 1, 'C');
        $pdf->Ln(5);
    }
}

// Additional Information (if any other fields exist)
$excludeFields = ['UpdateId', 'UpdateTitle', 'UpdateDescription', 'UpdateFile', 'UpdateUploadedBy', 'UpdateUploadDate', 'UpdateUploadTime'];
$hasAdditionalInfo = false;

foreach ($update as $key => $value) {
    if (!in_array($key, $excludeFields) && !empty($value)) {
        if (!$hasAdditionalInfo) {
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Cell(0, 8, 'Additional Information', 0, 1, 'L');
            $pdf->Ln(2);
            $hasAdditionalInfo = true;
        }
        
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(50, 8, ucfirst(str_replace('_', ' ', $key)) . ':', 1, 0, 'L', true);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 8, $value, 1, 'L');
    }
}

// Footer with generation info
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->SetTextColor(128, 128, 128);
$pdf->Cell(0, 5, 'This document was automatically generated on ' . date('Y-m-d H:i:s'), 0, 1, 'C');
$pdf->Cell(0, 5, 'Update Management System', 0, 1, 'C');

// Generate filename
$filename = 'Update_' . $update['UpdateId'] . '_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $update['UpdateTitle']) . '.pdf';

// Output PDF
$pdf->Output($filename, 'D'); // 'D' forces download
exit();
?>