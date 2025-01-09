<?php include ('sidebar.php');?>
<?php include('Back-end/information_file.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDD</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../../dist/css/nav.css">
    <link rel="stylesheet" href="../../dist/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="../../plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<body>
<div class="wrapper">
    <!-- Navbar -->
    <?php include ('../../include/nav.php'); ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Machine Documents</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">Machine Document</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- User Table Card -->
                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body">
                                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#inlineForm">
                                    Add Data
                                </button>
                               
            <section class="section">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                        <table id="partsTable" class="table table-bordered table-striped">
                       <thead>
                        <tr>
                            <th>Machine Name</th>
                            <th>Master Molding Data</th>
                            <th>Current Parameter</th>
                            <th>First Piece Approval</th>
                            <th>Visual Mapping</th>
                            <th class="text-center" style="width: 120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM machine_documents";
                        $result = mysqli_query($conn, $sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $master_molding_url = 'file_proxy.php?path=' . urlencode($row["master_molding_data_path"]);
                                $current_parameter_url = 'file_proxy.php?path=' . urlencode($row["current_parameter_path"]);
                                $first_piece_url = 'file_proxy.php?path=' . urlencode($row["first_piece_path"]);
                                $visual_mapping_url = 'file_proxy.php?path=' . urlencode($row["visual_mapping_path"]);

                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row["machine_name"]) . "</td>";
                                echo "<td><a href='" . htmlspecialchars($master_molding_url) . "' target='_blank'>" . htmlspecialchars(basename($row["master_molding_data_path"])) . "</a></td>";
                                echo "<td><a href='" . htmlspecialchars($current_parameter_url) . "' target='_blank'>" . htmlspecialchars(basename($row["current_parameter_path"])) . "</a></td>";
                                echo "<td><a href='" . htmlspecialchars($first_piece_url) . "' target='_blank'>" . htmlspecialchars(basename($row["first_piece_path"])) . "</a></td>";
                                echo "<td><a href='" . htmlspecialchars($visual_mapping_url) . "' target='_blank'>" . htmlspecialchars(basename($row["visual_mapping_path"])) . "</a></td>";
                                echo "<td class='text-center'>
                                        <div class='btn-group'>
                                            <a href='#' class='btn btn-warning btn-sm edit-part mx-1'
                                                data-id='" . htmlspecialchars($row["id"]) . "' 
                                                data-part-number='" . htmlspecialchars($row["machine_name"]) . "' 
                                                data-master-molding-data-path='" . htmlspecialchars($row["master_molding_data_path"]) . "' 
                                                data-current-parameter-path='" . htmlspecialchars($row["current_parameter_path"]) . "' 
                                                data-first-piece-path='" . htmlspecialchars($row["first_piece_path"]) . "' 
                                                data-visual-mapping-path='" . htmlspecialchars($row["visual_mapping_path"]) . "' 
                                                data-toggle='modal' 
                                                data-target='#editPartNumberModal'>
                                                Edit
                                            </a>
                                            <a href='#' class='btn btn-danger btn-sm deleteBtn mx-1' data-id='" . htmlspecialchars($row["id"]) . "'>Delete</a>
                                        </div>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Data not found</td></tr>";
                        }
                        ?>
                    </tbody>
                    </table>
                        </div>
                            </div>
                                </div>
                                     </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="modal fade" id="inlineForm" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Machine Document</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addPartNumberForm" action="add_machine_documen.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <?php include 'Back-end/koneksi.php'; ?>

                        <!-- Machine ID -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #666; margin-bottom: 8px;">Machine ID</label>
                            <select class="form-control" id="machineIDInput" name="machine_name" required 
                                    style="width: 100%; padding: 5px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="" disabled selected>Select Machine ID</option>
                                <?php
                                $sql = "SELECT machine_name FROM lines_machines";
                                $result = mysqli_query($conn, $sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . htmlspecialchars($row['machine_name']) . '">' . htmlspecialchars($row['machine_name']) . '</option>';
                                    }
                                } else {
                                    echo '<option value="" disabled>No Machine IDs found</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Master Molding Data -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #666; margin-bottom: 8px;">Master Molding Data</label>
                            <div style="display: flex; gap: 10px; margin-bottom: 5px;">
                                <input type="text" id="Master_Molding_data_path" name="Master_Molding_data_path" 
                                    style="flex: 1; padding: 5px; border: 1px solid #ddd; border-radius: 4px;" 
                                    placeholder="Input path here">
                                <div style="position: relative; width: 200px;">
                                    <input type="file" id="Master_Molding_data_file" name="Master_Molding_data_file" 
                                        style="opacity: 0; position: absolute; width: 100%; height: 100%; cursor: pointer;"
                                        onchange="checkInput('Master_Molding_data_path', 'Master_Molding_data_file')">
                                    <div style="display: flex; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                                        <span style="padding: 5px; background: #f8f9fa; border-right: 1px solid #ddd;">Choose File</span>
                                        <span style="padding: 5px; color: #666;">No file chosen</span>
                                    </div>
                                </div>
                            </div>
                            <small style="color: #666; font-size: 0.875em;">Choose to input a path or upload a file</small>
                        </div>

                        <!-- Current Parameter -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #666; margin-bottom: 8px;">Current Parameter</label>
                            <div style="display: flex; gap: 10px; margin-bottom: 5px;">
                                <input type="text" id="current_parameter_path" name="current_parameter_path" 
                                    style="flex: 1; padding: 5px; border: 1px solid #ddd; border-radius: 4px;" 
                                    placeholder="Input path here">
                                <div style="position: relative; width: 200px;">
                                    <input type="file" id="current_parameter_file" name="current_parameter_file" 
                                        style="opacity: 0; position: absolute; width: 100%; height: 100%; cursor: pointer;"
                                        onchange="checkInput('current_parameter_path', 'current_parameter_file')">
                                    <div style="display: flex; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                                        <span style="padding: 5px; background: #f8f9fa; border-right: 1px solid #ddd;">Choose File</span>
                                        <span style="padding: 5px; color: #666;">No file chosen</span>
                                    </div>
                                </div>
                            </div>
                            <small style="color: #666; font-size: 0.875em;">Choose to input a path or upload a file</small>
                        </div>

                        <!-- First Piece Approval -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #666; margin-bottom: 8px;">First Piece Approval</label>
                            <div style="display: flex; gap: 10px; margin-bottom: 5px;">
                                <input type="text" id="first_piece_path" name="first_piece_path" 
                                    style="flex: 1; padding: 5px; border: 1px solid #ddd; border-radius: 4px;" 
                                    placeholder="Input path here">
                                <div style="position: relative; width: 200px;">
                                    <input type="file" id="first_piece_file" name="first_piece_file" 
                                        style="opacity: 0; position: absolute; width: 100%; height: 100%; cursor: pointer;"
                                        onchange="checkInput('first_piece_path', 'first_piece_file')">
                                    <div style="display: flex; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                                        <span style="padding: 5px; background: #f8f9fa; border-right: 1px solid #ddd;">Choose File</span>
                                        <span style="padding: 5px; color: #666;">No file chosen</span>
                                    </div>
                                </div>
                            </div>
                            <small style="color: #666; font-size: 0.875em;">Choose to input a path or upload a file</small>
                        </div>

                        <!-- Visual Mapping -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #666; margin-bottom: 8px;">Visual Mapping</label>
                            <div style="display: flex; gap: 10px; margin-bottom: 5px;">
                                <input type="text" id="visual_mapping_path" name="visual_mapping_path" 
                                    style="flex: 1; padding: 5px; border: 1px solid #ddd; border-radius: 4px;" 
                                    placeholder="Input path here">
                                <div style="position: relative; width: 200px;">
                                    <input type="file" id="visual_mapping_file" name="visual_mapping_file" 
                                        style="opacity: 0; position: absolute; width: 100%; height: 100%; cursor: pointer;"
                                        onchange="checkInput('visual_mapping_path', 'visual_mapping_file')">
                                    <div style="display: flex; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                                        <span style="padding: 5px; background: #f8f9fa; border-right: 1px solid #ddd;">Choose File</span>
                                        <span style="padding: 5px; color: #666;">No file chosen</span>
                                    </div>
                                </div>
                            </div>
                            <small style="color: #666; font-size: 0.875em;">Choose to input a path or upload a file</small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Machine Document</button>
                    </div>
                </form>
            </div>
    </div>
</div>

<!-- Modal Edit Machine Document -->
<div class="modal fade" id="editPartNumberModal" tabindex="-1" aria-labelledby="editPartNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Machine Document</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editPartNumberForm" method="POST" action="update_machine_document.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_part_id" value="">

                    <!-- Machine ID -->
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; color: #666; margin-bottom: 8px;">Machine ID</label>
                        <input type="text" id="editMachineIDInput" name="part_number"
                               class="form-control"
                               style="width: 100%; padding: 5px; border: 1px solid #ddd; border-radius: 4px;"
                               placeholder="Input Machine ID here" required>
                    </div>

                    <!-- Master Molding Data -->
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; color: #666; margin-bottom: 8px;">Master Molding Data</label>
                        <div style="display: flex; gap: 10px; margin-bottom: 5px;">
                            <input type="text" id="editMaster_Molding_data_path" name="Master_Molding_data_path"
                                   style="flex: 1; padding: 5px; border: 1px solid #ddd; border-radius: 4px;"
                                   placeholder="Input path here">
                            <div style="position: relative; width: 200px;">
                                <input type="file" id="editMaster_Molding_data_file" name="Master_Molding_data_file"
                                       style="opacity: 0; position: absolute; width: 100%; height: 100%; cursor: pointer;"
                                       onchange="checkInput('editMaster_Molding_data_path', 'editMaster_Molding_data_file')">
                                <div style="display: flex; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                                    <span style="padding: 5px; background: #f8f9fa; border-right: 1px solid #ddd;">Choose File</span>
                                    <span style="padding: 5px; color: #666;">No file chosen</span>
                                </div>
                            </div>
                        </div>
                        <small style="color: #666; font-size: 0.875em;">Choose to input a path or upload a file</small>
                    </div>

                    <!-- Current Parameter -->
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; color: #666; margin-bottom: 8px;">Current Parameter</label>
                        <div style="display: flex; gap: 10px; margin-bottom: 5px;">
                            <input type="text" id="editCurrent_parameter_path" name="current_parameter_path"
                                   style="flex: 1; padding: 5px; border: 1px solid #ddd; border-radius: 4px;"
                                   placeholder="Input path here">
                            <div style="position: relative; width: 200px;">
                                <input type="file" id="editCurrent_parameter_file" name="current_parameter_file"
                                       style="opacity: 0; position: absolute; width: 100%; height: 100%; cursor: pointer;"
                                       onchange="checkInput('editCurrent_parameter_path', 'editCurrent_parameter_file')">
                                <div style="display: flex; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                                    <span style="padding: 5px; background: #f8f9fa; border-right: 1px solid #ddd;">Choose File</span>
                                    <span style="padding: 5px; color: #666;">No file chosen</span>
                                </div>
                            </div>
                        </div>
                        <small style="color: #666; font-size: 0.875em;">Choose to input a path or upload a file</small>
                    </div>

                    <!-- First Piece Approval -->
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; color: #666; margin-bottom: 8px;">First Piece Approval</label>
                        <div style="display: flex; gap: 10px; margin-bottom: 5px;">
                            <input type="text" id="editFirst_piece_path" name="first_piece_path"
                                   style="flex: 1; padding: 5px; border: 1px solid #ddd; border-radius: 4px;"
                                   placeholder="Input path here">
                            <div style="position: relative; width: 200px;">
                                <input type="file" id="editFirst_piece_file" name="first_piece_file"
                                       style="opacity: 0; position: absolute; width: 100%; height: 100%; cursor: pointer;"
                                       onchange="checkInput('editFirst_piece_path', 'editFirst_piece_file')">
                                <div style="display: flex; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                                    <span style="padding: 5px; background: #f8f9fa; border-right: 1px solid #ddd;">Choose File</span>
                                    <span style="padding: 5px; color: #666;">No file chosen</span>
                                </div>
                            </div>
                        </div>
                        <small style="color: #666; font-size: 0.875em;">Choose to input a path or upload a file</small>
                    </div>

                    <!-- Visual Mapping -->
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; color: #666; margin-bottom: 8px;">Visual Mapping</label>
                        <div style="display: flex; gap: 10px; margin-bottom: 5px;">
                            <input type="text" id="editVisual_mapping_path" name="visual_mapping_path"
                                   style="flex: 1; padding: 5px; border: 1px solid #ddd; border-radius: 4px;"
                                   placeholder="Input path here">
                            <div style="position: relative; width: 200px;">
                                <input type="file" id="editVisual_mapping_file" name="visual_mapping_file"
                                       style="opacity: 0; position: absolute; width: 100%; height: 100%; cursor: pointer;"
                                       onchange="checkInput('editVisual_mapping_path', 'editVisual_mapping_file')">
                                <div style="display: flex; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                                    <span style="padding: 5px; background: #f8f9fa; border-right: 1px solid #ddd;">Choose File</span>
                                    <span style="padding: 5px; color: #666;">No file chosen</span>
                                </div>
                            </div>
                        </div>
                        <small style="color: #666; font-size: 0.875em;">Choose to input a path or upload a file</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include ('../../include/footer.php'); ?>
</div>

<script src="../../plugins/jquery/jquery.min.js"></script>
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../plugins/select2/js/select2.full.min.js"></script>
<script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../../dist/js/adminlte.min.js"></script>
<script src="../../dist/sweetalert2/sweetalert2.min.js"></script>
<script src="../../dist/sweetalert2/sweetalert2.js"></script>
<script src="../../dist/js/dark_buton.js"></script>
<script>

$(document).ready(function () {
    // Initialize DataTable with proper variable scope
    const table = $('#partsTable').DataTable({
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        ordering: true,
        pageLength: 10
    });

    // Handle "Add Data" button click to open modal
    $('button[data-bs-toggle="modal"]').on('click', function () {
        $('#inlineForm').modal('show');
    });

});
    $('#addPartNumberForm').on('submit', function (e) {
    e.preventDefault(); // Prevent normal form submission

    var formData = new FormData(this);

    // Add Part Number Form Submission
    $.ajax({
        url: 'add_machine_documen.php', // PHP file to handle the request
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json', // Tambahkan ini untuk mendapatkan data dalam format JSON
        success: function (response) {
            // Pastikan respons memiliki format yang diharapkan
            if (response.status === 'success') {
                Swal.fire({
                    position: 'top-center',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#inlineForm').modal('hide'); // Menutup modal setelah sukses
                // Reload tabel atau lakukan aksi lain setelah menambahkan data
                location.reload(); // Atau panggil fungsi reload yang spesifik untuk tabel
            } else {
                Swal.fire({
                    position: 'top-center',
                    icon: 'error',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        },

        error: function (xhr, status, error) {
            Swal.fire({
                position: 'top-center',
                icon: 'error',
                title: 'An error occurred. Please try again.',
                showConfirmButton: false,
                timer: 1500
            });
            console.error(`Error adding part number: ${status} - ${error}`);
        }
    });
});

  // Event delegation for delete button with proper table reference
$(document).on('click', '.deleteBtn', function() {
        var partId = $(this).data('id');
        var row = $(this).closest('tr');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'Back-end/delete_machine_document.php',
                    type: 'GET',
                    data: { id: partId },
                    success: function(response) {
                        var res = JSON.parse(response);
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            
                            // Remove the row from DataTable and redraw
                            table.row(row).remove().draw(false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'There was a problem deleting the data.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });
    });

// Populate Edit Modal with Existing Data
$(document).on('click', '.edit-part', function () {
    const id = $(this).data('id');
    const partNumber = $(this).data('part-number');
    const masterMoldingDataPath = $(this).data('master-molding-data-path');
    const masterMoldingDataFile = $(this).data('master-molding-data-file');
    const currentParameterPath = $(this).data('current-parameter-path');
    const currentParameterFile = $(this).data('current-parameter-file');
    const firstPiecePath = $(this).data('first-piece-path');
    const firstPieceFile = $(this).data('first-piece-file');
    const visualMappingPath = $(this).data('visual-mapping-path');
    const visualMappingFile = $(this).data('visual-mapping-file');

    console.log(id, partNumber, masterMoldingDataPath, masterMoldingDataFile, currentParameterPath, currentParameterFile, firstPiecePath, firstPieceFile, visualMappingPath, visualMappingFile);

    // Isi data ke modal
    $('#editPartNumberModal').find('#edit_part_id').val(id);
    $('#editPartNumberModal').find('#editMachineIDInput').val(partNumber);
    $('#editPartNumberModal').find('#editMaster_Molding_data_path').val(masterMoldingDataPath);
    $('#editPartNumberModal').find('#editMaster_Molding_data_file').val(masterMoldingDataFile);
    $('#editPartNumberModal').find('#editCurrent_parameter_path').val(currentParameterPath);
    $('#editPartNumberModal').find('#editCurrent_parameter_file').val(currentParameterFile);
    $('#editPartNumberModal').find('#editFirst_piece_path').val(firstPiecePath);
    $('#editPartNumberModal').find('#editFirst_piece_file').val(firstPieceFile);
    $('#editPartNumberModal').find('#editVisual_mapping_path').val(visualMappingPath);
    $('#editPartNumberModal').find('#editVisual_mapping_file').val(visualMappingFile);

    // Tampilkan modal
    $('#editPartNumberModal').modal('show');
});

// Edit Part Number Form Submission
$('#editPartNumberModal').on('shown.bs.modal', function () {
    $('#editMachineIDInput').trigger('focus');
});

$('#editPartNumberForm').on('submit', function (e) {
    e.preventDefault(); // Prevent normal form submission

    var formData = new FormData(this);

    $.ajax({
        url: 'update_machine_document.php', // PHP file to handle the request
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json', // Expect JSON response
        success: function (response) {
    console.log(response); 
    if (response.status === 'success') {
        Swal.fire({
            position: 'top-center',
            icon: 'success',
            title: response.message,
            showConfirmButton: false,
            timer: 2000
        });
        $('#editPartNumberModal').modal('hide');
        location.reload();
    } else {
        Swal.fire({
            position: 'top-center',
            icon: 'error',
            title: response.message,
            showConfirmButton: false,
            timer: 2000
        });
    }
},

        error: function () {
            Swal.fire({
                position: 'top-center',
                icon: 'error',
                title: 'An error occurred. Please try again.',
                showConfirmButton: false,
                timer: 1500
            });
        }
    });
});

function checkInput(pathId, fileId) {
    const fileInput = document.getElementById(fileId);
    const fileName = fileInput.files[0] ? fileInput.files[0].name : 'No file chosen';
    fileInput.nextElementSibling.lastElementChild.textContent = fileName;
}

</script>

</body>
</html>
