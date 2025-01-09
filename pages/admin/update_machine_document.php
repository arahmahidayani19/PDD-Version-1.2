<?php
include 'Back-end/koneksi.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base upload directory
define('BASE_UPLOAD_DIR', '../../PDD/');

// Define allowed folders and their corresponding upload paths
$allowedFolders = [
    'master_molding' => BASE_UPLOAD_DIR . 'master_molding/',
    'current_parameter' => BASE_UPLOAD_DIR . 'current_parameter/',
    'first_piece' => BASE_UPLOAD_DIR . 'first_piece/',
    'visual_mapping' => BASE_UPLOAD_DIR . 'visual_mapping/'
];

// Function to validate and create upload directory
function validateAndCreateDirectory($folder) {
    global $allowedFolders;
    
    if (!isset($allowedFolders[$folder])) {
        throw new Exception("Invalid folder specified: " . $folder);
    }
    
    $uploadDir = $allowedFolders[$folder];
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception("Failed to create directory: " . $uploadDir);
        }
    }
    
    if (!is_writable($uploadDir)) {
        throw new Exception("Directory is not writable: " . $uploadDir);
    }
    
    return $uploadDir;
}

// Function to process and convert files
function processAndConvertFile($file, $folder) {
    if (!$file || $file['error'] !== 0) {
        return null;
    }

    try {
        // Validate and get upload directory
        $uploadDir = validateAndCreateDirectory($folder);
        
        // Get file details
        $originalName = basename($file["name"]);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $timestamp = date('Ymd_His');
        $newFileName = pathinfo($originalName, PATHINFO_FILENAME) . '_' . $timestamp;
        
        // Set file paths
        $targetFile = $uploadDir . $newFileName . '.' . $extension;
        $pdfFile = $uploadDir . $newFileName . '.pdf';

        // Validate file type
        $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception("Invalid file type. Allowed types: " . implode(', ', $allowedExtensions));
        }

        // Upload file
        if (!move_uploaded_file($file["tmp_name"], $targetFile)) {
            throw new Exception("Failed to upload file: " . $originalName);
        }

        // Convert if necessary
        if ($extension !== 'pdf') {
            require_once '../../vendor/autoload.php';

            if (in_array($extension, ['xlsx', 'xls'])) {
                // Convert Excel to PDF
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($targetFile);
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
                $writer->save($pdfFile);
                
                // Verify PDF was created
                if (!file_exists($pdfFile)) {
                    throw new Exception("Failed to create PDF from Excel file");
                }
                
                unlink($targetFile); // Remove original Excel file
                return $pdfFile;

            } elseif (in_array($extension, ['doc', 'docx'])) {
                // Convert Word to PDF
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($targetFile);
                
                // Convert to HTML first
                $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);
                $tempHtmlFile = $uploadDir . $newFileName . '.html';
                $htmlWriter->save($tempHtmlFile);
                
                // Convert HTML to PDF
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml(file_get_contents($tempHtmlFile));
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                
                file_put_contents($pdfFile, $dompdf->output());
                
                // Verify PDF was created
                if (!file_exists($pdfFile)) {
                    throw new Exception("Failed to create PDF from Word file");
                }
                
                // Clean up temporary files
                unlink($tempHtmlFile);
                unlink($targetFile);
                return $pdfFile;
            }
        }
        
        return $targetFile;

    } catch (Exception $e) {
        // Clean up any files if there was an error
        if (isset($targetFile) && file_exists($targetFile)) unlink($targetFile);
        if (isset($pdfFile) && file_exists($pdfFile)) unlink($pdfFile);
        if (isset($tempHtmlFile) && file_exists($tempHtmlFile)) unlink($tempHtmlFile);
        
        throw new Exception("File processing error: " . $e->getMessage());
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Get the machine name from the form
        $machine_name = $_POST['part_number'];
        
        // Fetch the line name
        $line_name = '';
        $sql = "SELECT line_name FROM lines_machines WHERE machine_name = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $machine_name);
            $stmt->execute();
            $stmt->bind_result($line_name);
            $stmt->fetch();
            $stmt->close();
        } else {
            throw new Exception("Failed to prepare line name query");
        }

        // Process each file upload
        $uploadResults = [];
        
        // Master Molding Data
        if (isset($_FILES['Master_Molding_data_file']) && $_FILES['Master_Molding_data_file']['error'] == 0) {
            $master_molding_path = processAndConvertFile($_FILES['Master_Molding_data_file'], 'master_molding');
            $uploadResults['master_molding'] = $master_molding_path;
        } else {
            $master_molding_path = $_POST['Master_Molding_data_path'] ?? null;
        }

        // Current Parameter
        if (isset($_FILES['current_parameter_file']) && $_FILES['current_parameter_file']['error'] == 0) {
            $current_parameter_path = processAndConvertFile($_FILES['current_parameter_file'], 'current_parameter');
            $uploadResults['current_parameter'] = $current_parameter_path;
        } else {
            $current_parameter_path = $_POST['current_parameter_path'] ?? null;
        }

        // First Piece
        if (isset($_FILES['first_piece_file']) && $_FILES['first_piece_file']['error'] == 0) {
            $first_piece_path = processAndConvertFile($_FILES['first_piece_file'], 'first_piece');
            $uploadResults['first_piece'] = $first_piece_path;
        } else {
            $first_piece_path = $_POST['first_piece_path'] ?? null;
        }

        // Visual Mapping
        if (isset($_FILES['visual_mapping_file']) && $_FILES['visual_mapping_file']['error'] == 0) {
            $visual_mapping_path = processAndConvertFile($_FILES['visual_mapping_file'], 'visual_mapping');
            $uploadResults['visual_mapping'] = $visual_mapping_path;
        } else {
            $visual_mapping_path = $_POST['visual_mapping_path'] ?? null;
        }

        // Update database
        $sql = "UPDATE machine_documents SET 
                line_name = ?, 
                master_molding_data_path = ?, 
                current_parameter_path = ?, 
                first_piece_path = ?, 
                visual_mapping_path = ? 
                WHERE machine_name = ?";
                
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssss", 
                $line_name, 
                $master_molding_path, 
                $current_parameter_path, 
                $first_piece_path, 
                $visual_mapping_path, 
                $machine_name
            );

            if (!$stmt->execute()) {
                throw new Exception("Failed to update database: " . $stmt->error);
            }
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Machine data updated successfully!',
                'uploads' => $uploadResults
            ]);
            
            $stmt->close();
        } else {
            throw new Exception("Failed to prepare update query: " . $conn->error);
        }

    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    $conn->close();
}
?>