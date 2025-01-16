@extends('organization.layouts.main')
@section('container')
    <style>
        .wrap-text {
            white-space: normal !important;
            word-wrap: break-word;
        }

        .transaction-hash {
            word-wrap: break-word;
            word-break: break-all;
            white-space: normal;
            max-width: 100%;
            display: block;
        }

        .timeline-badge {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .modal-dialog {
                max-width: 100%;
                margin: 0;
            }

            .timeline-body {
                width: 100% !important;
            }
        }
    </style>
    <!-- Start::app-content -->
    <div class="main-content app-content">
        <div class="container">
            <!-- Page Header and Alerts (omitted for brevity) -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <h1 class="page-title fw-semibold fs-18 mb-0">Smart Contract Blockchain Logs</h1>
                <div class="ms-md-1 ms-0">
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Pages</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Smart Contract</li>
                        </ol>
                    </nav>
                </div>
            </div>
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                    <div class="ms-3"> {{ session('success') }} </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible d-flex align-items-center" role="alert">
                    <i class="bi bi-dash-circle-fill fs-4"></i>
                    <div class="ms-3"> {!! session('error') !!} </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Start::row-1 -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">List of Smart Contract Logs</div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover text-nowrap w-100 data-table">
                                    <thead class="table-borderless">
                                        <tr>
                                            <th scope="col">No.</th>
                                            <th scope="col">Content Name</th>
                                            <th scope="col">Network</th>
                                            <th scope="col">Transaction Hash</th>
                                            <th scope="col">BlockChain No</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">log</th>
                                            {{-- <th scope="col">Deploy</th> --}}
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End::row-1 -->

            <!-- Single Log Viewer Modal -->
            <div class="modal fade" id="logViewerModal" tabindex="-1" aria-labelledby="logViewerModalLabel"
                aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down modal-dialog-scrollable modal-lg">
                    <div class="modal-content shadow-lg bg-light">
                        <div class="modal-header">
                            <h6 class="modal-title" id="logViewerModalLabel">Deployment Logs</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Loading Indicator -->
                            <div id="log-loading" class="text-center my-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 fw-semi-bold">Please wait, Connecting to xBug BlockChain Server.....</p>
                            </div>

                            <!-- Logs Content -->
                            <ul class="timeline list-unstyled" id="log-content" style="display: none;">
                                <!-- Logs will be injected here via AJAX -->
                            </ul>
                        </div>
                        <div class="modal-footer d-flex justify-content-end">
                            <button type="button" class="btn btn-outline-danger px-4" data-bs-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CSRF Token Setup -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <!-- Your JavaScript Code -->
    <script>
        $(document).ready(function() {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pageLength: 50,
                ajax: "{{ route('showContentBlockchainlogsOrg') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        render: (data, type, row) => {
                            return `<div class="wrap-text">${data.toUpperCase()}</div>`;
                        }
                    },
                    {
                        data: 'network',
                        name: 'network',
                    },
                    {
                        data: 'tx_hash',
                        name: 'tx_hash',
                    },
                    {
                        data: 'block_id',
                        name: 'block_id',
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'log',
                        name: 'log',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-primary view-logs-btn" data-id="${row.smart_contract_id}">
                                    <i class="bi bi-eye"></i> View Logs
                                </button>
                            `;
                        }
                    }
                    // {
                    //     data: 'action',
                    //     name: 'action',
                    // }
                ],
            });

            // Flag to track AJAX request status
            var isRequestInProgress = false;

            // Polling: Reload DataTable every 117 seconds
            setInterval(function() {
                if (!isRequestInProgress) {
                    table.ajax.reload(null, false); // false to retain pagination
                }
            }, 117000); // 117000 milliseconds = 117 seconds

            // Handle "Deploy" form submission via AJAX
            $(document).on('submit', '.deploy-form', function(e) {
                e.preventDefault(); // Prevent default form submission

                // Check if a request is already in progress
                if (isRequestInProgress) {
                    return; // Do not proceed if a request is in progress
                }

                var form = $(this);
                var formData = form.serialize(); // Serialize form data

                // Show loading status
                var deployButton = form.find('.deploy-button');
                deployButton.prop('disabled', true);
                deployButton.html(
                    '<i class="bi bi-spinner-border text-white me-2" role="status"></i> Deploying...');

                // Mark request as in progress
                isRequestInProgress = true;

                // Send AJAX request
                $.ajax({
                    url: form.attr('action'),
                    method: form.attr('method'),
                    data: formData,
                    success: function(response) {
                        // Close the modal
                        var modalId = form.data('id');
                        $('#confirm-' + modalId).modal('hide');

                        // Show success alert
                        toastr.success(response.message, null, { timeOut: 7000 });

                        // Reload the DataTable immediately after success
                        table.ajax.reload(null, false); // Reload without resetting pagination
                    },
                    error: function(xhr, status, error) {
                        // Close the modal
                        var modalId = form.data('id');
                        $('#confirm-' + modalId).modal('hide');

                        if(status == 401 && xhr.responseJSON && xhr.responseJSON.message) {
                            toastr.error(xhr.responseJSON.message, null, { timeOut: 7000 });
                        }

                        // Show error alert
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            toastr.error(xhr.responseJSON.error, null, { timeOut: 7000 });
                        } else {
                            toastr.error('An error occurred while deploying the smart contract.',null, { timeOut: 7000 });
                        }
                        table.ajax.reload(null, false);
                    },
                    complete: function() {
                        // Reset the Deploy button
                        deployButton.prop('disabled', false);
                        deployButton.html(
                            '<i class="bi bi-check-circle-fill me-1"></i> Deploy');

                        // Mark request as completed
                        isRequestInProgress = false;
                    }
                });
            });

            // Handle "View Logs" button click
            $(document).on('click', '.view-logs-btn', function() {
                var smartContractId = $(this).data('id');

                // Open the modal
                $('#logViewerModal').modal('show');

                // Show loading indicator and hide logs content
                $('#log-loading').show();
                $('#log-content').hide().empty();
                toastr.info('Connecting to xBug BlockChain Server.....');

                // Send AJAX request to fetch logs
                $.ajax({
                    url: `{{route('smartContract.getLogs', ['id' => '__placeholder__'])}}`.replace('__placeholder__', smartContractId),
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        // Check if logs are present
                        if (response.logs && response.logs.length > 0) {
                            // Iterate over logs and append to the list
                            response.logs.forEach(function(log) {
                                // Format the timestamp
                                var date = new Date(log.created_at);
                                var options = {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit',
                                    hour12: true
                                };
                                var formattedDate = date.toLocaleDateString('en-US',
                                    options);

                                // Determine log type for styling
                                var logType = 'INFO';
                                var logBadgeClass = 'p-2 bg-primary';
                                var text_class = 'text-primary';
                                if (log.log_message.startsWith('[DEBUG]')) {
                                    logType = 'DEBUG';
                                    logBadgeClass = 'p-2 bg-warning';
                                    var text_class = 'text-warning';
                                } else if (log.log_message.startsWith('[ERROR]')) {
                                    logType = 'ERROR';
                                    logBadgeClass = 'p-2 bg-danger';
                                    var text_class = 'text-danger';
                                } else if (log.log_message.startsWith('[SUCCESS]')) {
                                    logType = 'SUCCESS';
                                    logBadgeClass = 'p-2 bg-success';
                                    var text_class = 'text-success';
                                }

                                // Remove the log type prefix from the message
                                var logMessage = log.log_message.replace(/^\[.*?\]\s*/,
                                    '');

                                // Append log to the list
                                $('#log-content').append(`
                                    <li class="mt-0">
                                        <div class="timeline-time text-end">
                                            <span class="date text-dark fs-12">${formattedDate.split(',')[0]}</span>
                                            <span class="time d-inline-block text-dark fs-12 fw-bold">${formattedDate.split(',')[1]}</span>
                                        </div>
                                        <div class="timeline-icon">
                                            <a href="javascript:void(0);"></a>
                                        </div>
                                        <div class="timeline-body w-75 shadow-lg">
                                            <div class="d-flex align-items-top timeline-main-content flex-wrap mt-0">
                                                <div class="flex-fill">
                                                    <div class="">
                                                        <div class="mt-sm-0 mt-2 p-3">
                                                            <p class="mb-2 fs-14 fw-bold d-flex align-items-center justify-content-between">
                                                                <span class="${text_class}">${logType}</span>
                                                                <span class="float-end badge ${logBadgeClass} timeline-badge">
                                                                    ${formattedDate.split(',')[1]}
                                                                </span>
                                                            </p>
                                                            <p class="mb-0 text-dark transaction-hash">${logMessage}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </li>
                                `);
                            });

                            // Hide loading and show logs
                            $('#log-loading').hide();
                            toastr.success('Logs fetched successfully.');
                            $('#log-content').show();
                        } else {
                            // No logs found
                            $('#log-content').append(`
                                <li class="text-center">
                                    <p class="text-muted">No logs available for this smart contract.</p>
                                </li>
                            `);
                            $('#log-loading').hide();
                            $('#log-content').show();
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle errors
                        var errorMessage = 'An error occurred while fetching logs.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }
                        $('#log-content').append(`
                            <li class="text-center ">
                                <div class="bg-danger-transparent p-5 rounded-2">
                                    <i class="bi bi-exclamation-triangle-fill me-1 text-danger"></i>
                                    <span class="text-danger">${xhr.responseJSON.message || errorMessage}</span>
                                </div>
                            </li>
                        `);
                        $('#log-loading').hide();
                        $('#log-content').show();
                    }
                });
            });

            // Optional: Initialize toastr for notifications
            toastr.options = {
                "positionClass": "toast-top-right",
                "timeOut": "2200",
            };
        });
    </script>
@endsection
