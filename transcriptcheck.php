<?php
require_once('connection.php');
require_once('cann.php');
require_once('check.php');

$query = "
    SELECT a.matricno, a.namex, a.amount, a.sessionname, a.remita_rrr, a.phone, a.locationx, a.destination, a.destinationadd,
           a.cgpa, a.finalresult, a.destemail, b.program, b.SchoolName
    FROM [Transcript].[dbo].[Transcript_order] AS a
    INNER JOIN EBPORTAL.dbo.vw_biodata AS b ON a.matricno COLLATE database_default = b.Matricnum
    WHERE a.download IS NULL AND a.status = 1";
$result = sqlsrv_query($conn, $query);

if ($result === false) {
    die(print_r(sqlsrv_errors(), true));
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="Smarthr - Bootstrap Admin Template">
    <meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
    <meta name="author" content="Dreamguys - Bootstrap Admin Template">
    <meta name="robots" content="noindex, nofollow">
    <title>Transcript download </title>


    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.13.2/datatables.min.css" />



    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/new.css">
    <!-- Lineawesome CSS -->
    <link rel="stylesheet" href="assets/css/line-awesome.min.css">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="assets/css/select2.min.css">

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <script type="text/javascript" src="jquery.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.13.2/datatables.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"> </script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"> </script>
    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"> </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"> </script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"> </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"> </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"> </script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"> </script>


    <script src="assets/js/yola.js"></script>
    <script src="assets/js/rerun.js"></script>
    <script src="assets/js/shola.js"></script>


    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
			<script src="assets/js/html5shiv.min.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->
    <style>
        /* Style for the "Download Results" button */
        #downloadResultsBtn {
            background-color: #f7f7f7;
            /* Set your desired background color */
            color: #000;
            /* Set your desired text color */
            border: 2px solid #000;
            padding: 2px 23px;
            /* Adjust the padding for both height and width */
            border-radius: 5px;
            /* Set border-radius for rounded corners */
            cursor: pointer;
            margin-right: 5px;
            /* * Set margin-right for spacing */
            margin-bottom: 10px
        }


        <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><style>.scrolling-text {
            overflow: hidden;
            color: red;
            border: 1px solid red;
            white-space: nowrap;
        }

        @keyframes scroll {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .scrolling-text span {
            display: inline-block;
            padding: 5px;
            animation: scroll 6s linear infinite;
        }
    </style>

    <script>
        var DataTableJQ = jQuery.noConflict(true);

        DataTableJQ(document).ready(function() {
            var table = DataTableJQ('#example2').DataTable({
                dom: 'Bfrtip',
                scrollX: 'auto',
                scrollY: 'auto',
                buttons: [{
                    extend: 'excel',
                    title: 'Transcript Student_List',
                    text: 'Download Student List', // Set the text displayed on the button
                    action: function(e, dt, button, config) {
                        // Check if there is any data in the DataTable
                        if (dt.data().any()) {
                            // Trigger the Excel export only when there is data
                            DataTableJQ.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                            // Your custom action when the Excel button is clicked and there is data
                            showalertpop();
                            updateInformation();
                        } else {
                            // Show a message or handle the case when there is no data
                            alert('No data available for download.');
                        }
                    }
                }]
            });
        });
    </script>


</head>

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <?php require_once('hedad3.php'); ?>
        <!-- /Header -->

        <!-- Sidebar -->
        <?php require_once('siderd.php'); ?>
        <!-- Page Wrapper -->
        <div class="page-wrapper">

            <!-- Page Content -->
            <div class="content container-fluid">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="page-title">Transcript Download</h3>
                            <div class="scrolling-text" style="color: red;">
                                <span><b>Dear <?php echo $uname; ?>, Please ensure you click on download the result first before downloading the student list,after which the page refreshes.</b></span>
                            </div>
                            <ul class="breadcrumb">

                                <li class="breadcrumb-item active"></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->
                <hr>


                <table id="example2" class="display" style="width:100%">
                    <button id="downloadResultsBtn" onclick="downloadZip()">Generate Result Download Link</button>
                    <button id="resetAndRerunBtn">Click Rerun Download,If Failed</button>
                    <div>
                        <?php

                        if (isset($_SESSION['downloadLink'])) {
                            $downloadLink = $_SESSION['downloadLink'];
                            echo $downloadLink;
                            // Clear the session variable
                            unset($_SESSION['downloadLink']);
                        } 
                        ?>
                    </div>
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <?php
                            // Output table headers
                            foreach (sqlsrv_field_metadata($result) as $fieldMetadata) {
                                echo "<th>{$fieldMetadata['Name']}</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count = 1;
                        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>$count</td>";
                            foreach ($row as $fieldKey => $fieldValue) {
                                if ($fieldKey === 'SchoolName') {
                                    // Special handling for SchoolName
                                    echo "<td>{$fieldValue}</td>";
                                } else {
                                    echo "<td>$fieldValue</td>";
                                }
                            }
                            echo "</tr>";
                            $count++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        </tr>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </section>
    </div>
    </div>


    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="assets/js/jquery-3.2.1.min.js"></script>

    <!-- Bootstrap Core JS -->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    <!-- Slimscroll JS -->
    <script src="assets/js/jquery.slimscroll.min.js"></script>

    <!-- Select2 JS -->
    <script src="assets/js/select2.min.js"></script>

    <!-- Datetimepicker JS -->
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>

    <!-- Custom JS -->
    <script src="assets/js/app.js"></script>
    <script src="assets/js/app.js"></script>


</body>

</html>