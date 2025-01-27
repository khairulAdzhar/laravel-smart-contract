@extends('organization.layouts.main')
@section('container')
    {{-- Pastikan Vite mem-bundle walletconnect.js + contentBlockchainPage.js --}}
    @vite(['resources/js/indexBlockchain.js', 'resources/css/app.css'])
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
    <style>
        .dots span {
            display: inline-block;
            animation: bounce 0.6s infinite ease-in-out;
        }

        .dots span:nth-child(1) {
            animation-delay: 0s;
        }

        .dots span:nth-child(2) {
            animation-delay: 0.4s;
        }

        .dots span:nth-child(3) {
            animation-delay: 0.8s;
        }

        .dots span:nth-child(4) {
            animation-delay: 0.12s;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
                /* Posisi awal dan akhir */
            }

            50% {
                transform: translateY(-5px);
                /* Gerakan ke atas lebih pendek */
            }
        }
    </style>
    <style>
        .break-all {
            word-break: break-all;
        }
    </style>
    <div class="main-content app-content">
        <div class="container">
            {{-- Page Header, dsb (seperti di code Anda) --}}
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <h1 class="page-title fw-semibold fs-18 mb-0">Smart Contract Blockchain</h1>
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
            <div class="row">
                <div class="col-md-12">
                    <div class="card custom-card">
                        <div class="card-header d-flex flex-column flex-sm-row align-items-center justify-content-between">
                            <div class="card-title mb-2 mb-sm-0">List Transaction Smart Contract Content</div>
                            <div class="d-flex flex-wrap align-items-center">
                                {{-- Reown WalletConnect Components --}}
                                
                                <appkit-network-button class="btn btn-dark btn-sm me-2 mb-2"></appkit-network-button>
                                <appkit-button class="btn btn-dark btn-sm me-2 mb-2"></appkit-button>
                            </div>
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
                                            <th scope="col">BlockChain ID</th>
                                            <th scope="col">log</th>
                                            <th scope="col">Block No</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Deploy</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
            {{-- MODAL CONTOH (loop data) --}}
            @foreach ($content_data as $data)
                <!-- Modal View -->
                <div class="modal fade" id="modalView-{{ $data->id }}" aria-labelledby="exampleModalToggleLabel"
                    tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow-lg p-1">
                            <div class="modal-header">
                                <h6 class="modal-title" id="successModalLabel">Contract Detail</h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-2">
                                    The smart contract has been successfully deployed.
                                </p>
                                <ul class="border rounded-2 p-3 bg-light list-unstyled">
                                    <li class="mb-2">
                                        <span class="fw-normal">Network:</span>
                                        <span class="text-success fw-bold">Sepolia Network</span>
                                    </li>
                                    <li class="mb-2">
                                        <span class="fw-normal">Transaction Hash:</span>
                                        <a href="https://sepolia.etherscan.io/tx/{{ $data->smart_contract_tx_hash }}"
                                            target="_blank"
                                            class="text-dark text-decoration-none fw-bold transaction-hash">
                                            {{ $data->smart_contract_tx_hash }}
                                            <i class="bi bi-box-arrow-up-right ms-1"></i>
                                        </a>
                                    </li>
                                    <li class="mb-2">
                                        <span class="fw-normal">Block Number: </span>
                                        <a href="https://sepolia.etherscan.io/block/{{ $data->smart_contract_block_no }}"
                                            target="_blank" class="text-dark text-decoration-none fw-bold ms-1">
                                            {{ $data->smart_contract_block_no }}
                                            <i class="bi bi-box-arrow-up-right ms-1"></i> <!-- Icon tautan -->
                                        </a>
                                    </li>
                                    <li class="mb-2">
                                        <span class="fw-normal">Contract Address:</span>
                                        <a href="https://sepolia.etherscan.io/address/{{ $data->smart_contract_contract_address }}"
                                            target="_blank"
                                            class="text-dark text-decoration-none fw-bold transaction-hash">
                                            {{ $data->smart_contract_contract_address }}
                                            <i class="bi bi-box-arrow-up-right ms-1"></i> <!-- Icon untuk tautan -->
                                        </a>
                                    </li>
                                    <li class="mb-2">
                                        <span class="fw-normal">Deployed Address:</span>
                                        <a href="https://sepolia.etherscan.io/address/{{ $data->smart_contract_address }}"
                                            target="_blank"
                                            class="text-dark text-decoration-none fw-bold transaction-hash">
                                            {{ $data->smart_contract_address }}
                                            <i class="bi bi-box-arrow-up-right ms-1"></i>
                                        </a>
                                    </li>

                                    <li class="mb-2">
                                        <span class="fw-normal">BlockChain ID: </span>
                                        <span class="fw-bold text-dark">{{ $data->smart_contract_tx_id }}</span>
                                    </li>
                                    <li class="mb-2">
                                        <span class="fw-normal">Deployment Time:</span>
                                        <span>{{ $data->smart_contract_updated_at }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="modal-footer d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-danger px-4" data-bs-dismiss="modal">
                                    Close
                                </button>
                                <a href="https://sepolia.etherscan.io/inputdatadecoder?tx={{ $data->smart_contract_tx_hash }}"
                                    target="_blank" class="btn btn-primary px-4">
                                    <i class="bi bi-info-circle me-1"></i> Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Confirmation -->
                <div class="modal fade bg-light" id="confirmation-{{ $data->id }}"
                    aria-labelledby="exampleModalToggleLabel" tabindex="-1" aria-hidden="true" data-bs-keyboard="false"
                    data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow-lg p-2">
                            <div class="modal-header ">
                                <h6 class="modal-title" id="exampleModalToggleLabel">Confirmation Message</h6>
                                <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-2">Please review the details below before confirming the deployment:</p>
                                <div class="border rounded-2 p-3 bg-light">
                                    <ul class="list-unstyled mb-0">
                                        {{-- <li class="mb-2">
                                        <span class="fw-semibold">xBUG Address:</span>
                                        <span class="text-primary transaction-hash">{{ env('ETH_ADRRESS') }}</span>
                                    </li> --}}
                                        <li class="mb-2">
                                            <span class="fw-semibold">Contract Address:</span>
                                            <span
                                                class="text-primary transaction-hash">{{ env('CONTRACT_ADDRESS') }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold">Network:</span>
                                            <span class="text-success fw-bold">Sepolia Network</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold">Cryptocurrency:</span>
                                            <span class="text-dark fw-bold">Ethereum (ETH)</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold">Smart Contract Name:</span>
                                            <span class="text-danger fw-bold">xBugContentVerification</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold">Deployer Name:</span>
                                            <span class="text-primary">xBUG</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold">Organization Name:</span>
                                            <span class="text-primary">{{ $data->organization_name }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold">Your Name:</span>
                                            <span class="text-primary">{{ Auth::user()->name }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold">Content Name:</span>
                                            <span class="text-primary">{{ $data->name }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold">Date:</span>
                                            <span>{{ now()->format('d F Y, H:i:s') }}</span>
                                        </li>
                                    </ul>
                                </div>
                                <p class="text-danger mt-2 small bg-danger-transparent p-2 rounded-2">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    Note: Once deployed, the smart contract becomes immutable as it resides on the
                                    Blockchain Network. Please ensure all details are accurate before proceeding.
                                </p>
                            </div>
                            <div class="modal-footer d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-danger px-5"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button class="btn btn-primary  px-3" data-bs-target="#confirm-{{ $data->id }}"
                                    data-bs-toggle="modal">
                                    <i class="bi bi-check-circle-fill me-1"></i> Confirm Sign
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Deploy Confirmation -->
                <div class="modal fade bg-light" id="confirm-{{ $data->id }}"
                    aria-labelledby="exampleModalToggleLabel2" tabindex="-1" aria-hidden="true"
                    data-bs-keyboard="false" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow-lg p-1">
                            <div class="modal-header ">
                                <h6 class="modal-title" id="exampleModalToggleLabel">Confirm Sign</h6>
                                <button type="button" id="xCancel" class="btn-close btn-close-dark"
                                    data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-primary small">
                                <p><span class="fw-bold">Content Name:</span> {{ $data->name }}</p>
                                <i class="bi bi-shield-lock-fill text-danger"></i>
                                Reminder: Once deployed, the smart contract will be securely stored on the <span
                                    class="text-dark fw-bold">Blockchain Network</span> and cannot be <span
                                    class="text-danger fw-bold">changed</span> or <span
                                    class="text-danger fw-bold">removed</span>. Please verify all details carefully
                                before proceeding.
                                </p>
                            </div>
                            <div class="modal-footer d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-danger px-4" data-bs-dismiss="modal"
                                    id="cancelBtnC">Cancel</button>
                                <button id="" class="add-content-btn btn btn-primary px-4 deploy-button"
                                    data-content-id="{{ $data->id }}" data-content-name="{{ $data->name }}"
                                    data-content-created-at="{{ $data->created_at }}"
                                    data-content-link="{{ $data->link }}"
                                    data-content-enrollment-price="{{ $data->enrollment_price }}"
                                    data-content-place="{{ $data->place }}" data-content-type="{{ $data->type }}"
                                    data-content-privoder="xBug"
                                    data-content-organization-name="{{ $data->organization_name }}"
                                    data-content-username="{{ Auth::user()->name }}">
                                    <i class="bi bi-check-circle-fill me-1"></i> Sign
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- JS tambahan: misalnya, set route global di window. --}}
    <script>
        window._DATATABLES_AJAX_URL = "{{ route('showContentBlockchainOrg') }}"
        window._SAVE_DEPLOY_ROUTE = "{{ route('saveDeployedData') }}"
    </script>
    <script>
        $(document).ready(function() {

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
                    url: `{{ route('smartContract.getLogs', ['id' => '__placeholder__']) }}`
                        .replace('__placeholder__', smartContractId),
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        // Check if logs are present
                        if (response.logs && response.logs.length > 0) {
                            // Iterate over logs and append to the list
                            response.logs.forEach(function(log) {
                                // Format the timestamp
                                var date = new Date(log.created_at);

                                // Buat tiga variabel
                                var dayMonth = date.toLocaleDateString('en-US', {
                                    month: 'long',
                                    day: 'numeric'
                                });
                                var yearPart = date.toLocaleDateString('en-US', {
                                    year: 'numeric'
                                });
                                var timePart = date.toLocaleTimeString('en-US', {
                                    hour12: true,
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit'
                                });
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


                                // Remove the log type prefix from the message
                                var logMessage = log.log_message.replace(/^\[.*?\]\s*/,
                                    '');

                                // Append log to the list
                                $('#log-content').append(`
                                    <li class="mt-0">
                                        <div class="timeline-time text-end">
                                            <span class="date text-dark fs-12 me-2">${dayMonth}</span><br/>
                                            <span class="time d-inline-block text-dark fs-12 fw-bold">${yearPart} at</span><br/>
                                            <span class="time d-inline-block text-dark fs-12 fw-bold">${timePart}</span>
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


        });
    </script>

    {{-- Toastr & Sweetalert2 (bila pakai CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr/build/toastr.min.js"></script>
@endsection
