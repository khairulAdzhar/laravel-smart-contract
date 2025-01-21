@extends('organization.layouts.main')
@section('container')
    @vite(['resources/js/walletconnect.js', 'resources/css/app.css'])
    <!-- Start::app-content -->
    <div class="main-content app-content">
        <div class="container">
            <!-- Page Header and Alerts (omitted for brevity) -->
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
            <!-- Start::row-1 -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-header d-flex flex-column flex-sm-row align-items-center justify-content-between">
                            <!-- Judul Kartu di Kiri -->
                            <div class="card-title mb-2 mb-sm-0">List Transaction Smart Contract Content</div>

                            <!-- Grup Tombol di Kanan -->
                            <div class="d-flex flex-wrap align-items-center">
                                <appkit-network-button class="btn btn-dark btn-sm me-2 mb-2"></appkit-network-button>
                                <appkit-button class="btn btn-dark btn-sm me-2 mb-2"></appkit-button>
                                <button id="connectMetamaskBtn"
                                    class="btn btn-dark text-light fw-bold d-flex align-items-center me-2 mb-2 py-2" style="height: 49px">
                                    <img src="{{ asset('assets/images/metamask.png') }}" alt=""
                                        style="width: 24px; margin-right: 8px;">
                                    Connect to MetaMask
                                </button>
                            </div>
                        </div>


                        <div class="card-body">
                            <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center mb-3">
                                <span id="usertext" class="text-muted me-sm-2" style="">MetaMask
                                    Status:</span>
                                <!-- Awalnya warna merah -->
                                <span id="userAddress" class="fw-bold text-break" style="white-space: normal; color: red;">
                                    None
                                </span>
                            </div>
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
            <!-- End::row-1 -->
        </div>
    </div>

    @foreach ($content_data as $data)

        <!-- Modal Confirmation -->
        <div class="modal fade bg-light" id="confirmation-{{ $data->id }}" aria-labelledby="exampleModalToggleLabel"
            tabindex="-1" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
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
        <div class="modal fade bg-light" id="confirm-{{ $data->id }}" aria-labelledby="exampleModalToggleLabel2"
            tabindex="-1" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg p-1">
                    <div class="modal-header ">
                        <h6 class="modal-title" id="exampleModalToggleLabel">Confirm Sign</h6>
                        <button type="button" id="xCancel" class="btn-close btn-close-dark" data-bs-dismiss="modal"
                            aria-label="Close"></button>
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
                            data-content-created-at="{{ $data->created_at }}" data-content-link="{{ $data->link }}"
                            data-content-enrollment-price="{{ $data->enrollment_price }}"
                            data-content-place="{{ $data->place }}" data-content-type="{{ $data->type }}"
                            data-content-privoder="xBug" data-content-organization-name="{{ $data->organization_name }}"
                            data-content-username="{{ Auth::user()->name }}">
                            <i class="bi bi-check-circle-fill me-1"></i> Sign
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <!-- CSRF Token Setup -->
    <script src="https://cdn.jsdelivr.net/npm/ethers@5.7.2/dist/ethers.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                ajax: "{{ route('showContentBlockchainOrg') }}",
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
                        data: 'blockchain_id',
                        name: 'blockchain_id',
                    },
                    {
                        data: 'logs', // **Changed from 'log' to 'logs'**
                        name: 'logs', // **Changed from 'log' to 'logs'**
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            // Ensure smart_contract_status_contract is 1 or 0
                            if (row.smart_contract_status_contract == 1 || row
                                .smart_contract_status_contract == 0) {
                                return `
                            <button class="btn btn-sm btn-primary view-logs-btn" data-id="${row.smart_contract_id}">
                                <i class="bi bi-eye"></i> View
                            </button>
                        `;

                            } else {
                                return '-';
                            }
                        }
                    },
                    {
                        data: 'block_id',
                        name: 'block_id'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                    }
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        text: 'Copy Data',
                        exportOptions: {
                            columns: ':not(:nth-child(6)):not(:nth-child(7))'
                        }
                    },
                    {
                        extend: 'csv',
                        text: 'Export CSV',
                        exportOptions: {
                            columns: ':not(:nth-child(6)):not(:nth-child(7))'
                        }
                    },
                    {
                        extend: 'excel',
                        text: 'Export Excel',
                        exportOptions: {
                            columns: ':not(:nth-child(6)):not(:nth-child(7))'
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print Data',
                        exportOptions: {
                            columns: ':not(:nth-child(6)):not(:nth-child(7))'
                        }
                    }
                ],
                language: {
                    emptyTable: "No logs available"
                }
            });
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
                        html: `Please connect MetaMask first by clicking "Connect to MetaMask" button at upper right datatable`,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    var modalId = '#confirm-' + $(this).data('content-id');
                    $(modalId).modal('hide');
                    return;
                }
                var xCancel = $('#xCancel');
                xCancel.prop('disabled', true);

                var cancelBtnC = $('#cancelBtnC');
                cancelBtnC.prop('disabled', true);

                var deployButton = $('.deploy-button');
                deployButton.prop('disabled', true);
                deployButton.html(
                    '<i class="bi bi-spinner-border text-white me-2" role="status"></i> Signing<span class="dots"> <span>.</span><span>.</span><span>.</span><span>.</span></span>'
                );
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

                const contentData = {
                    contentId: contentId,
                    name: _name,
                    createdAt: _createdAt,
                    link: _link,
                    enrollmentPrice: _enrollmentPrice,
                    place: _place,
                    contentType: _contentType,
                    provider: _provider,
                    organizationName: _organizationName,
                    userName: _userName
                };

                // Menampilkan objek JSON di console
                console.log(JSON.stringify(contentData, null, 4));

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
                    addLog(
                        `[INFO] Contract ABI loaded successfully. Preparing contract instance at address: ${CONTRACT_ADDRESS}.`
                    );
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
                        `[INFO] Transaction confirmed! TxHash: ${receipt.transactionHash}, Block#: ${receipt.blockNumber}`
                    );

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

                    addLog(
                        `[INFO] Transaction hash successfully stored in Blockchain network with tx_hash: ${txHash}, block_number: ${blockNumber}.`
                    );
                    addLog(
                        `[SUCCESS] Deployment blockchain process completed successfully for user account ${userAccount}.`
                    );
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
                    var modalId = '#confirm-' + $(this).data('content-id');
                    $(modalId).modal('hide');

                    var xCancel = $('#xCancel');
                    xCancel.prop('disabled', false);

                    var cancelBtnC = $('#cancelBtnC');
                    cancelBtnC.prop('disabled', false);

                    var deployButton = $('.deploy-button');
                    deployButton.prop('disabled', false);
                    deployButton.html(
                        '<i class="bi bi-check-circle-fill me-1"></i> Sign'
                    );

                } catch (err) {
                    toastr.error('Add Content failed!');
                    Swal.fire({
                        title: 'Error',
                        html: `Transaction failed or declined`,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    var modalId = '#confirm-' + $(this).data('content-id');
                    $(modalId).modal('hide');
                    var xCancel = $('#xCancel');
                    xCancel.prop('disabled', false);

                    var cancelBtnC = $('#cancelBtnC');
                    cancelBtnC.prop('disabled', false);

                    var deployButton = $('.deploy-button');
                    deployButton.prop('disabled', false);
                    deployButton.html(
                        '<i class="bi bi-check-circle-fill me-1"></i> Sign'
                    );
                }
            });

            // Optional: Initialize toastr for notifications
            toastr.options = {
                "positionClass": "toast-top-right",
                "timeOut": "2200",
            };
        });
    </script>
@endsection
