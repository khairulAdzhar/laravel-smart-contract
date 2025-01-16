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
            <!-- Page Header and Alerts (omitted) -->
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
                    <div class="ms-3">{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible d-flex align-items-center" role="alert">
                    <i class="bi bi-dash-circle-fill fs-4"></i>
                    <div class="ms-3">{!! session('error') !!}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- DataTables -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="card-title">List Transaction Smart Contract Content</div>
                            <button id="connectMetamaskBtn"
                                class="btn btn-dark text-light fw-bold d-flex align-items-center">
                                <img src="{{ asset('assets/images/metamask.png') }}" alt=""
                                    style="width: 30px; margin-right: 8px;">
                                Connect to MetaMask
                            </button>
                        </div>
                        <div class="card-body">
                            <!-- Bagian Status -->
                            <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center mb-3">
                                <span id="usertext" class="text-muted me-sm-2" style="">MetaMask
                                    Status:</span>
                                <!-- Awalnya warna merah -->
                                <span id="userAddress" class="fw-bold text-break" style="white-space: normal; color: red;">
                                    None
                                </span>
                            </div>

                            <!-- Tabel -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover text-nowrap w-100 data-table">
                                    <thead class="table-borderless">
                                        <tr>
                                            <th>No.</th>
                                            <th>Content Name</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <hr>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/ethers@5.7.2/dist/ethers.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pageLength: 50,
                ajax: "{{ route('showContentBlockchainOrg') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        render: function(data, type, row) {
                            return `<div class="wrap-text">${(data || '').toUpperCase()}</div>`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-primary btn-sm add-content-btn"
                                        data-content-id="${row.id}"
                                        data-content-name="${row.name}"
                                        data-content-created-at="${row.created_at}"
                                        data-content-link="${row.link}"
                                        data-content-enrollment-price="${row.enrollment_price}"
                                        data-content-place="${row.place}"
                                        data-content-type="${row.type}"
                                        data-content-privoder="xBug"
                                        data-content-organization-name="${row.organization_name}"
                                        data-content-username="{{ Auth::user()->name }}">
                                    <i class="bi bi-box-arrow-in-right"></i> Add to Blockchain
                                </button>
                            `;
                        }
                    }
                ],
            });

            setInterval(function() {
                table.ajax.reload(null, false);
            }, 8000);

            let provider, signer, userAccount = null;

            $('#connectMetamaskBtn').on('click', async () => {
                if (typeof window.ethereum === 'undefined') {
                    Swal.fire({
                        title: 'MetaMask not found!',
                        html: `MetaMask not found! Please install extension.`,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                try {
                    const accounts = await window.ethereum.request({
                        method: 'eth_requestAccounts'
                    });
                    userAccount = accounts[0];
                    provider = new ethers.providers.Web3Provider(window.ethereum);
                    signer = provider.getSigner();

                    $('#userAddress')
                        .text(`Connected: ${userAccount}`)
                        .css('color', 'green');
                    toastr.success('Connected to MetaMask!');
                } catch (error) {
                    console.error(error);
                    toastr.error('Failed to connect MetaMask!');
                }
            });


            // Misal, diasumsikan variable "table" dan "userAccount" sudah didefinisikan di tempat lain
            // (karena tampak dari code snippet Anda, "table" dan "userAccount" sudah ada).
            $(document).on('click', '.add-content-btn', async function() {
                if (!signer) {
                    Swal.fire({
                        title: 'MetaMask Not Connected!',
                        html: `Please connect MetaMask first by clicking "Connect to MetaMask" button.`,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // 1) Siapkan array logs
                let logs = [];

                function addLog(message) {
                    // Setiap kali dipanggil, tambahkan object { message: "...", ... } ke array
                    logs.push({
                        message
                    });
                    console.log("[LOG]", message); // boleh tampilkan di console
                }

                // 2) Ambil data dari attribute HTML
                const contentId = $(this).data('content-id') || 'none';
                const _name = $(this).data('content-name') || 'none';
                const _createdAt = $(this).data('content-created-at') || 'none';
                const _link = $(this).data('content-link') || 'none';
                const _enrollmentPrice = $(this).data('content-enrollment-price') || 'none';
                const _place = $(this).data('content-place') || 'none';
                const _contentType = $(this).data('content-type') || 'none';
                const _provider = $(this).data('content-privoder') || 'none';
                const _organizationName = $(this).data('content-organization-name') || 'none';
                const _userName = $(this).data('content-username') || 'none';

                try {
                    addLog("[INFO] Deployment Blockchain process started...");

                    // 3) Kontrak Address & ABI
                    const CONTRACT_ADDRESS = "0x5faC1Aa6AF8e5d510cb6D971E87F0a2C57C2E992";
                    const contractABI = [{
                            "anonymous": false,
                            "inputs": [{
                                    "indexed": false,
                                    "internalType": "uint256",
                                    "name": "id",
                                    "type": "uint256"
                                },
                                {
                                    "indexed": false,
                                    "internalType": "address",
                                    "name": "user",
                                    "type": "address"
                                },
                                {
                                    "indexed": false,
                                    "internalType": "string",
                                    "name": "name",
                                    "type": "string"
                                },
                                {
                                    "indexed": false,
                                    "internalType": "string",
                                    "name": "createdAt",
                                    "type": "string"
                                },
                                {
                                    "indexed": false,
                                    "internalType": "string",
                                    "name": "link",
                                    "type": "string"
                                },
                                {
                                    "indexed": false,
                                    "internalType": "string",
                                    "name": "enrollmentPrice",
                                    "type": "string"
                                },
                                {
                                    "indexed": false,
                                    "internalType": "string",
                                    "name": "place",
                                    "type": "string"
                                },
                                {
                                    "indexed": false,
                                    "internalType": "string",
                                    "name": "contentType",
                                    "type": "string"
                                },
                                {
                                    "indexed": false,
                                    "internalType": "string",
                                    "name": "provider",
                                    "type": "string"
                                },
                                {
                                    "indexed": false,
                                    "internalType": "string",
                                    "name": "organizationName",
                                    "type": "string"
                                },
                                {
                                    "indexed": false,
                                    "internalType": "string",
                                    "name": "userName",
                                    "type": "string"
                                },
                                {
                                    "indexed": false,
                                    "internalType": "uint256",
                                    "name": "timestamp",
                                    "type": "uint256"
                                }
                            ],
                            "name": "ContentAdded",
                            "type": "event"
                        },
                        {
                            "inputs": [{
                                    "internalType": "string",
                                    "name": "_name",
                                    "type": "string"
                                },
                                {
                                    "internalType": "string",
                                    "name": "_createdAt",
                                    "type": "string"
                                },
                                {
                                    "internalType": "string",
                                    "name": "_link",
                                    "type": "string"
                                },
                                {
                                    "internalType": "string",
                                    "name": "_enrollmentPrice",
                                    "type": "string"
                                },
                                {
                                    "internalType": "string",
                                    "name": "_place",
                                    "type": "string"
                                },
                                {
                                    "internalType": "string",
                                    "name": "_contentType",
                                    "type": "string"
                                },
                                {
                                    "internalType": "string",
                                    "name": "_provider",
                                    "type": "string"
                                },
                                {
                                    "internalType": "string",
                                    "name": "_organizationName",
                                    "type": "string"
                                },
                                {
                                    "internalType": "string",
                                    "name": "_userName",
                                    "type": "string"
                                }
                            ],
                            "name": "addContent",
                            "outputs": [],
                            "stateMutability": "nonpayable",
                            "type": "function"
                        },
                        {
                            "inputs": [],
                            "name": "contentCount",
                            "outputs": [{
                                "internalType": "uint256",
                                "name": "",
                                "type": "uint256"
                            }],
                            "stateMutability": "view",
                            "type": "function"
                        },
                        {
                            "inputs": [{
                                "internalType": "uint256",
                                "name": "",
                                "type": "uint256"
                            }],
                            "name": "contents",
                            "outputs": [{
                                    "internalType": "uint256",
                                    "name": "id",
                                    "type": "uint256"
                                },
                                {
                                    "internalType": "address",
                                    "name": "user",
                                    "type": "address"
                                },
                                {
                                    "internalType": "string",
                                    "name": "name",
                                    "type": "string"
                                },
                                {
                                    "internalType": "string",
                                    "name": "createdAt",
                                    "type": "string"
                                },
                                {
                                    "internalType": "string",
                                    "name": "link",
                                    "type": "string"
                                },
                                {
                                    "internalType": "string",
                                    "name": "enrollmentPrice",
                                    "type": "string"
                                },
                                {
                                    "internalType": "string",
                                    "name": "place",
                                    "type": "string"
                                },
                                {
                                    "internalType": "string",
                                    "name": "contentType",
                                    "type": "string"
                                },
                                {
                                    "internalType": "string",
                                    "name": "provider",
                                    "type": "string"
                                },
                                {
                                    "internalType": "string",
                                    "name": "organizationName",
                                    "type": "string"
                                },
                                {
                                    "internalType": "string",
                                    "name": "userName",
                                    "type": "string"
                                },
                                {
                                    "internalType": "uint256",
                                    "name": "timestamp",
                                    "type": "uint256"
                                }
                            ],
                            "stateMutability": "view",
                            "type": "function"
                        },
                        {
                            "inputs": [{
                                "internalType": "uint256",
                                "name": "_id",
                                "type": "uint256"
                            }],
                            "name": "getContent",
                            "outputs": [{
                                "components": [{
                                        "internalType": "uint256",
                                        "name": "id",
                                        "type": "uint256"
                                    },
                                    {
                                        "internalType": "address",
                                        "name": "user",
                                        "type": "address"
                                    },
                                    {
                                        "internalType": "string",
                                        "name": "name",
                                        "type": "string"
                                    },
                                    {
                                        "internalType": "string",
                                        "name": "createdAt",
                                        "type": "string"
                                    },
                                    {
                                        "internalType": "string",
                                        "name": "link",
                                        "type": "string"
                                    },
                                    {
                                        "internalType": "string",
                                        "name": "enrollmentPrice",
                                        "type": "string"
                                    },
                                    {
                                        "internalType": "string",
                                        "name": "place",
                                        "type": "string"
                                    },
                                    {
                                        "internalType": "string",
                                        "name": "contentType",
                                        "type": "string"
                                    },
                                    {
                                        "internalType": "string",
                                        "name": "provider",
                                        "type": "string"
                                    },
                                    {
                                        "internalType": "string",
                                        "name": "organizationName",
                                        "type": "string"
                                    },
                                    {
                                        "internalType": "string",
                                        "name": "userName",
                                        "type": "string"
                                    },
                                    {
                                        "internalType": "uint256",
                                        "name": "timestamp",
                                        "type": "uint256"
                                    }
                                ],
                                "internalType": "struct ContentVerification.Content",
                                "name": "",
                                "type": "tuple"
                            }],
                            "stateMutability": "view",
                            "type": "function"
                        }
                    ];
                    addLog(`[INFO] Contract ABI loaded successfully. Preparing contract instance at address: ${CONTRACT_ADDRESS}.`);
                    addLog("[INFO] Initializing contract instance...");
                    const contract = new ethers.Contract(CONTRACT_ADDRESS, contractABI, signer);

                    toastr.info('Sending transaction... Please confirm in MetaMask.');
                    addLog("[INFO] Calling contract.addContent() with user data.");

                    // 4) Kirim transaksi
                    const tx = await contract.addContent(
                        _name,
                        _createdAt,
                        _link,
                        _enrollmentPrice,
                        _place,
                        _contentType,
                        _provider,
                        _organizationName,
                        _userName
                    );
                    addLog("[INFO] Transaction broadcasted. Waiting for mining...");

                    // 5) Tunggu konfirmasi (receipt)
                    const receipt = await tx.wait();
                    addLog(
                        `[INFO] Transaction confirmed! TxHash: ${receipt.transactionHash}, Block#: ${receipt.blockNumber}`);

                    // 6) Cek event ContentAdded
                    const eventSignature =
                        "ContentAdded(uint256,address,string,string,string,string,string,string,string,string,string,uint256)";
                    const contentAddedLog = receipt.logs.find(
                        (log) => log.topics[0] === ethers.utils.id(eventSignature)
                    );
                    addLog(`[INFO] Signature of ContentAdded event detected: ${eventSignature}`);

                    let block_id = null;
                    if (contentAddedLog) {
                        const decodedLog = contract.interface.parseLog(contentAddedLog);
                        block_id = decodedLog.args.id.toString();
                        addLog(`[DEBUG] ContentAdded event detected. block_id=${block_id}`);
                    } else {
                        addLog("[DEBUG] No ContentAdded event found in logs.");
                    }

                    toastr.success('Content added to Blockchain!');

                    // 7) Kirim data + logs ke server
                    const txHash = receipt.transactionHash;
                    const blockNumber = receipt.blockNumber;

                    addLog(`[INFO] Transaction hash successfully stored in Blockchain network with tx_hash: ${txHash}, block_number: ${blockNumber}.`);
                    addLog(`[SUCCESS] Deployment blockchain process completed successfully for user account ${userAccount}.`);
                    const response = await fetch('{{ route('saveDeployedData') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        body: JSON.stringify({
                            content_id: contentId,
                            user_id: '{{ Auth::user()->id }}',
                            user_metamask_address: userAccount,
                            tx_hash: txHash,
                            block_no: blockNumber,
                            contract_address: CONTRACT_ADDRESS,
                            provider: 'xBug',
                            status_contract: 1,
                            tx_id: block_id,
                            content_name: _name,
                            // Kirim logs ke server
                            logs: JSON.stringify(logs)
                        })
                    });

                    if (response.ok) {
                        const result = await response.json();
                        toastr.success(result.message || 'Contract data saved!');
                        table.ajax.reload(null, false);
                    } else {
                        toastr.error('Failed to save data/logs to server.');
                    }

                    Swal.fire({
                        title: 'Content Added To Blockchain!',
                        html: `TxHash: <strong>${txHash}</strong><br>Block#: <strong>${blockNumber}</strong>`,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });

                } catch (err) {
                    toastr.error('Add Content failed!');
                    Swal.fire({
                        title: 'Error',
                        html: `Transaction failed! Check console for details.`,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });


            // Optional: Toastr Options
            toastr.options = {
                "positionClass": "toast-top-right",
                "timeOut": "2200",
            };
        });
    </script>
@endsection
