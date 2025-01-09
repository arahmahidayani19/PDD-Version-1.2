<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'koneksi.php';

// Define constants
define('UPLOAD_DIR', '../../PDD/');
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'xls', 'xlsx']);

// Function to create directory if it doesn't exist
function createDirectory($path) {
    if (!file_exists($path)) {
        mkdir($path, 0755, true);
    }
    return is_dir($path) && is_writable($path);
}

// Function to handle file upload
function handleFileUpload($file, $folder) {
    if (!$file || $file['error'] !== 0) {
        return null;
    }

    // Create upload directory if it doesn't exist
    $uploadDir = UPLOAD_DIR . $folder . '/';
    if (!createDirectory($uploadDir)) {
        throw new Exception("Unable to create or write to directory: " . $uploadDir);
    }

    // Get file details
    $fileName = basename($file['name']);
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Validate file extension
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        throw new Exception("Invalid file type. Allowed types: " . implode(', ', ALLOWED_EXTENSIONS));
    }

    // Generate unique filename
    $newFileName = uniqid() . '_' . $fileName;
    $targetPath = $uploadDir . $newFileName;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception("Failed to move uploaded file.");
    }

    // Convert if necessary (not PDF)
    if ($extension !== 'pdf') {
        $pdfPath = $uploadDir . pathinfo($newFileName, PATHINFO_FILENAME) . '.pdf';
        
        require_once '../../vendor/autoload.php';

        try {
            if (in_array($extension, ['xls', 'xlsx'])) {
                // Convert Excel to PDF
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($targetPath);
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
                $writer->save($pdfPath);
            } 
            elseif (in_array($extension, ['doc', 'docx'])) {
                // Convert Word to PDF
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($targetPath);
                $pdfWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
                $pdfWriter->save($pdfPath);
            }

            // Delete original file after conversion
            unlink($targetPath);
            return $pdfPath;
            
        } catch (Exception $e) {
            if (file_exists($targetPath)) unlink($targetPath);
            if (file_exists($pdfPath)) unlink($pdfPath);
            throw new Exception("File conversion failed: " . $e->getMessage());
        }
    }

    return $targetPath;
}

// Main process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get machine name
        $machine_name = $_POST['machine_name'] ?? null;
        if (!$machine_name) {
            throw new Exception("Machine name is required");
        }

        // Get line name from database
        $stmt = $conn->prepare("SELECT line_name FROM lines_machines WHERE machine_name = ?");
        $stmt->bind_param("s", $machine_name);
        $stmt->execute();
        $result = $stmt->get_result();
        $line_name = $result->fetch_assoc()['line_name'] ?? null;
        $stmt->close();

        if (!$line_name) {
            throw new Exception("Invalid machine name");
        }

        // Process files
        $files = [
            'master_molding' => [
                'path' => $_POST['Master_Molding_data_path'] ?? null,
                'file' => $_FILES['Master_Molding_data_file'] ?? null
            ],
            'current_parameter' => [
                'path' => $_POST['current_parameter_path'] ?? null,
                'file' => $_FILES['current_parameter_file'] ?? null
            ],
            'first_piece' => [
                'path' => $_POST['first_piece_path'] ?? null,
                'file' => $_FILES['first_piece_file'] ?? null
            ],
            'visual_mapping' => [
                'path' => $_POST['visual_mapping_path'] ?? null,
                'file' => $_FILES['visual_mapping_file'] ?? null
            ]
        ];

        $updatedPaths = [];
        foreach ($files as $type => $data) {
            if (isset($data['file']) && $data['file']['error'] === 0) {
                $updatedPaths[$type] = handleFileUpload($data['file'], $type);
            } else {
                $updatedPaths[$type] = $data['path'];
            }
        }

        // Update database
        $sql = "UPDATE machine_documents SET 
                line_name = ?,
                master_molding_data_path = ?,
                current_parameter_path = ?,
                first_piece_path = ?,
                visual_mapping_path = ?
                WHERE machine_name = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss",
            $line_name,
            $updatedPaths['master_molding'],
            $updatedPaths['current_parameter'],
            $updatedPaths['first_piece'],
            $updatedPaths['visual_mapping'],
            $machine_name
        );

        if (!$stmt->execute()) {
            throw new Exception("Database update failed: " . $stmt->error);
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Machine document updated successfully',
            'paths' => $updatedPaths
        ]);

    } catch (Exception $e) {
        error_log("Error in update_machine_document.php: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
?>