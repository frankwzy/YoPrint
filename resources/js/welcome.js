import Dropzone from 'dropzone';
import 'dropzone/dist/dropzone.css';
import Swal from 'sweetalert2';

$(document).ready(function () {
    const COMPLETE = 2;
    $('#productsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '/products/get-products-list',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { data: 'title' },
            { data: 'description' },
            { data: 'style' },
            { data: 'sanmar' },
            { data: 'size' },
            { data: 'color' },
            {
                data: 'price', render: function (data, type, row) {
                    return '$' + data
                }
            }
        ]
    });

    function pollImportStatus(batchId) {
        const pollInterval = 3000; // Poll every 3 seconds

        function checkStatus() {
            fetch(`/import-status/${batchId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status != COMPLETE) {
                        setTimeout(checkStatus, pollInterval);
                    } else {
                        // Refresh the DataTable when import is completed
                        $('#productsTable').DataTable().ajax.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
        }
        checkStatus();
    }

    $('<button id="showDropzone" style="background-color: #4CAF50; border: none; color: white; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 4px;">Upload CSV</button>')
        .insertBefore('#productsTable');

    Dropzone.autoDiscover = false;
    let myDropzone;


    $('#showDropzone').click(function () {
        Swal.fire({
            title: 'Upload CSV File',
            html: '<div id="csvDropzone" class="dropzone"></div><button id="uploadFile" class="btn btn-success mt-3">Upload File</button>',
            showConfirmButton: false,
            showCloseButton: true,
            didOpen: () => {
                myDropzone = new Dropzone("#csvDropzone", {
                    url: "/products/bulk-upload-product-csv",
                    acceptedFiles: ".csv",
                    maxFiles: 1,
                    autoProcessQueue: false,
                    addRemoveLinks: true,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Process the file when upload button is clicked
                $('#uploadFile').click(function () {
                    myDropzone.processQueue();
                });

                // Handle upload success
                myDropzone.on("success", function (file, response) {
                    Swal.fire({
                        title: 'Upload Successful',
                        text: 'CSV file is being processed...',
                        icon: 'success',
                        showConfirmButton: true,
                        showCloseButton: false,
                    });
                    // Start polling for import status
                    pollImportStatus(response.batch_id);
                });

                // Handle upload error
                myDropzone.on("error", function (file, errorMessage) {
                    console.error("Error uploading file", errorMessage);
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: errorMessage
                    });
                });
            }
        });
    });
});