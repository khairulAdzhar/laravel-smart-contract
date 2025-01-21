// resources/js/contentBlockchainPage.js

import "./walletconnect.js"; // Pastikan inisialisasi appKit
import { appKit, getEthersSigner } from "./walletconnect.js";
// import { ethers } from "ethers"; // Agar sinkron dengan versi CDN / alias
import { ethers, keccak256, toUtf8Bytes } from "ethers";

// Jika Anda tidak memakai npm "ethers",
// maka Anda bisa mengandalkan <script src="...ethers.umd.min.js"></script> di Blade.
// Namun, idealnya kita gunakan bundling Vite.
// Sementara contoh ini asumsikan bundling,
//   maka "import { ethers } from 'ethers'" seharusnya jalan.

// *** HARAP pastikan versi Ethers di package.json sesuai
//     atau gunakan "npm i ethers@5.7.2" agar match code di snippet Anda.

window.addEventListener("DOMContentLoaded", () => {
    // Inisialisasi Datatables
    const table = $(".data-table").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 50,
        ajax: window._DATATABLES_AJAX_URL || "", // <-- ganti dengan route
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                searchable: false,
            },
            {
                data: "name",
                name: "name",
                render: (data, type, row) => {
                    return `<div class="wrap-text">${data.toUpperCase()}</div>`;
                },
            },
            {
                data: "network",
                name: "network",
            },
            {
                data: "tx_hash",
                name: "tx_hash",
            },
            {
                data: "blockchain_id",
                name: "blockchain_id",
            },
            {
                data: "logs", // **Changed from 'log' to 'logs'**
                name: "logs", // **Changed from 'log' to 'logs'**
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    // Ensure smart_contract_status_contract is 1 or 0
                    if (
                        row.smart_contract_status_contract == 1 ||
                        row.smart_contract_status_contract == 0
                    ) {
                        return `
                <button class="btn btn-sm btn-primary view-logs-btn" data-id="${row.smart_contract_id}">
                    <i class="bi bi-eye"></i> View
                </button>
            `;
                    } else {
                        return "-";
                    }
                },
            },
            {
                data: "block_id",
                name: "block_id",
            },
            {
                data: "status",
                name: "status",
            },
            {
                data: "action",
                name: "action",
            },
        ],
    });

    // *** LOGIC: On Deploy Button Click ***
    $(document).on("click", ".add-content-btn", async function () {
        const modalId = "#confirm-" + $(this).data("content-id");

        // Pastikan user terhubung ke wallet (appKit)
        const walletProvider = appKit.getWalletProvider();
        if (!walletProvider) {
            Swal.fire({
                html: `
                    <img src="https://upload.wikimedia.org/wikipedia/commons/1/13/Walletconnect-logo.png" 
                         alt="Wallet Logo" 
                         class="img-fluid mb-3" 
                         style="max-width: 100px;">
                    <h4 class="fw-bold text-primary">Wallet Not Connected!</h4>
                    <p class="text-dark mb-3">Please connect your wallet to continue.</p>
                `,
                confirmButtonText: '<span class="fw-semibold">OK</span>',
                buttonsStyling: false,
                customClass: {
                    confirmButton: "btn btn-primary px-5 py-2",
                    popup: "border rounded-3 shadow-lg p-4",
                },
            });

            // Swal.fire({
            //     html: `
            //         <div style="text-align: center;">
            //             <img src="../../assets/images/loading-deploy-2(1).gif"
            //                  alt="Loading"
            //                  class="img-fluid mb-3"
            //                  style="width: 40px; margin-left: 10px;">
            //             <h6 class="fw-bold text-primary" style="margin: 0;">Please confirm the transaction at your wallet</h6>
            //         </div>
            //     `,
            //     buttonsStyling: false,
            //     showConfirmButton: false, // Menghilangkan tombol OK
            //     customClass: {
            //         popup: 'border rounded-4 shadow-lg py-5 px-4',
            //     },
            // });

            $(modalId).modal("hide");
            return;
        }

        // Bagian UI (disable button dsb.)
        const xCancel = $("#xCancel");
        const cancelBtnC = $("#cancelBtnC");
        const deployButtons = $(".deploy-button");

        xCancel.prop("disabled", true);
        cancelBtnC.prop("disabled", true);
        deployButtons.prop("disabled", true);
        deployButtons.html(
            '<i class="bi bi-spinner-border text-white me-2" role="status"></i> Signing...'
        );

        // Array penampung logs
        let logs = [];
        function addLog(message) {
            logs.push({ message });
            console.log("[LOG]", message);
        }

        // Ambil data dari attribute HTML
        const contentId = $(this).data("content-id") || "none";
        const _name = String($(this).data("content-name")) || "none";
        const _createdAt = String($(this).data("content-created-at")) || "none";
        const _link = String($(this).data("content-link")) || "none";
        const _enrollmentPrice =
            String($(this).data("content-enrollment-price")) || "none";
        const _place = String($(this).data("content-place")) || "none";
        const _contentType = String($(this).data("content-type")) || "none";
        const _provider = String($(this).data("content-privoder")) || "none";
        const _organizationName =
            String($(this).data("content-organization-name")) || "none";
        const _userName = String($(this).data("content-username")) || "none";

        console.log("ContentData => ", {
            contentId,
            _name,
            _createdAt,
            _link,
            _enrollmentPrice,
            _place,
            _contentType,
            _provider,
            _organizationName,
            _userName,
        });

        try {
            addLog("[INFO] Deployment Blockchain process started...");

            // Alamat contract + ABI
            const CONTRACT_ADDRESS =
                "0x5faC1Aa6AF8e5d510cb6D971E87F0a2C57C2E992";
            const contractABI = [
                {
                    anonymous: false,
                    inputs: [
                        {
                            indexed: false,
                            internalType: "uint256",
                            name: "id",
                            type: "uint256",
                        },
                        {
                            indexed: false,
                            internalType: "address",
                            name: "user",
                            type: "address",
                        },
                        {
                            indexed: false,
                            internalType: "string",
                            name: "name",
                            type: "string",
                        },
                        {
                            indexed: false,
                            internalType: "string",
                            name: "createdAt",
                            type: "string",
                        },
                        {
                            indexed: false,
                            internalType: "string",
                            name: "link",
                            type: "string",
                        },
                        {
                            indexed: false,
                            internalType: "string",
                            name: "enrollmentPrice",
                            type: "string",
                        },
                        {
                            indexed: false,
                            internalType: "string",
                            name: "place",
                            type: "string",
                        },
                        {
                            indexed: false,
                            internalType: "string",
                            name: "contentType",
                            type: "string",
                        },
                        {
                            indexed: false,
                            internalType: "string",
                            name: "provider",
                            type: "string",
                        },
                        {
                            indexed: false,
                            internalType: "string",
                            name: "organizationName",
                            type: "string",
                        },
                        {
                            indexed: false,
                            internalType: "string",
                            name: "userName",
                            type: "string",
                        },
                        {
                            indexed: false,
                            internalType: "uint256",
                            name: "timestamp",
                            type: "uint256",
                        },
                    ],
                    name: "ContentAdded",
                    type: "event",
                },
                {
                    inputs: [
                        {
                            internalType: "string",
                            name: "_name",
                            type: "string",
                        },
                        {
                            internalType: "string",
                            name: "_createdAt",
                            type: "string",
                        },
                        {
                            internalType: "string",
                            name: "_link",
                            type: "string",
                        },
                        {
                            internalType: "string",
                            name: "_enrollmentPrice",
                            type: "string",
                        },
                        {
                            internalType: "string",
                            name: "_place",
                            type: "string",
                        },
                        {
                            internalType: "string",
                            name: "_contentType",
                            type: "string",
                        },
                        {
                            internalType: "string",
                            name: "_provider",
                            type: "string",
                        },
                        {
                            internalType: "string",
                            name: "_organizationName",
                            type: "string",
                        },
                        {
                            internalType: "string",
                            name: "_userName",
                            type: "string",
                        },
                    ],
                    name: "addContent",
                    outputs: [],
                    stateMutability: "nonpayable",
                    type: "function",
                },
                {
                    inputs: [],
                    name: "contentCount",
                    outputs: [
                        {
                            internalType: "uint256",
                            name: "",
                            type: "uint256",
                        },
                    ],
                    stateMutability: "view",
                    type: "function",
                },
                {
                    inputs: [
                        {
                            internalType: "uint256",
                            name: "",
                            type: "uint256",
                        },
                    ],
                    name: "contents",
                    outputs: [
                        {
                            internalType: "uint256",
                            name: "id",
                            type: "uint256",
                        },
                        {
                            internalType: "address",
                            name: "user",
                            type: "address",
                        },
                        {
                            internalType: "string",
                            name: "name",
                            type: "string",
                        },
                        {
                            internalType: "string",
                            name: "createdAt",
                            type: "string",
                        },
                        {
                            internalType: "string",
                            name: "link",
                            type: "string",
                        },
                        {
                            internalType: "string",
                            name: "enrollmentPrice",
                            type: "string",
                        },
                        {
                            internalType: "string",
                            name: "place",
                            type: "string",
                        },
                        {
                            internalType: "string",
                            name: "contentType",
                            type: "string",
                        },
                        {
                            internalType: "string",
                            name: "provider",
                            type: "string",
                        },
                        {
                            internalType: "string",
                            name: "organizationName",
                            type: "string",
                        },
                        {
                            internalType: "string",
                            name: "userName",
                            type: "string",
                        },
                        {
                            internalType: "uint256",
                            name: "timestamp",
                            type: "uint256",
                        },
                    ],
                    stateMutability: "view",
                    type: "function",
                },
                {
                    inputs: [
                        {
                            internalType: "uint256",
                            name: "_id",
                            type: "uint256",
                        },
                    ],
                    name: "getContent",
                    outputs: [
                        {
                            components: [
                                {
                                    internalType: "uint256",
                                    name: "id",
                                    type: "uint256",
                                },
                                {
                                    internalType: "address",
                                    name: "user",
                                    type: "address",
                                },
                                {
                                    internalType: "string",
                                    name: "name",
                                    type: "string",
                                },
                                {
                                    internalType: "string",
                                    name: "createdAt",
                                    type: "string",
                                },
                                {
                                    internalType: "string",
                                    name: "link",
                                    type: "string",
                                },
                                {
                                    internalType: "string",
                                    name: "enrollmentPrice",
                                    type: "string",
                                },
                                {
                                    internalType: "string",
                                    name: "place",
                                    type: "string",
                                },
                                {
                                    internalType: "string",
                                    name: "contentType",
                                    type: "string",
                                },
                                {
                                    internalType: "string",
                                    name: "provider",
                                    type: "string",
                                },
                                {
                                    internalType: "string",
                                    name: "organizationName",
                                    type: "string",
                                },
                                {
                                    internalType: "string",
                                    name: "userName",
                                    type: "string",
                                },
                                {
                                    internalType: "uint256",
                                    name: "timestamp",
                                    type: "uint256",
                                },
                            ],
                            internalType: "struct ContentVerification.Content",
                            name: "",
                            type: "tuple",
                        },
                    ],
                    stateMutability: "view",
                    type: "function",
                },
            ];

            addLog(
                `[INFO] Initializing contract at address: ${CONTRACT_ADDRESS}`
            );

            // 1) Dapatkan signer
            const signer = await getEthersSigner();
            // 2) Buat instance contract
            const contract = new ethers.Contract(
                CONTRACT_ADDRESS,
                contractABI,
                signer
            );

            addLog("[INFO] Calling addContent(...) method on smart contract.");
            Swal.fire({
                html: `
                    <div style="text-align: center;">
                        <img src="../../assets/images/loading-deploy-2(1).gif" 
                             alt="Loading" 
                             class="img-fluid mb-3" 
                             style="width: 40px; margin-left: 10px;">
                        <h6 class="fw-bold text-primary" style="margin: 0;">Please confirm the transaction at your wallet</h6>
                    </div>
                `,
                buttonsStyling: false,
                showConfirmButton: false, // Menghilangkan tombol OK
                allowOutsideClick: false, // Mencegah modal ditutup dengan klik di luar
                allowEscapeKey: false, // Mencegah modal ditutup dengan tombol Escape
                customClass: {
                    popup: "border rounded-4 shadow-lg py-5 px-4",
                },
            });

            // 3) Panggil transaksi
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
            addLog(`[INFO] Tx broadcasted: ${tx.hash}. Waiting for confirmation...`);

            // 4) Tunggu receipt
            const receipt = await tx.wait();
            console.log(receipt); 

            console.log(receipt.hash); 

            addLog(
                `[INFO] Tx confirmed! TxHash: ${receipt.hash}, Block#: ${receipt.blockNumber}`
            );

            // 5) Cari event ContentAdded
            const eventSignature =
                "ContentAdded(uint256,address,string,string,string,string,string,string,string,string,string,uint256)";

            // 1) Buat hash dari event signature
            const eventSignatureHash = keccak256(toUtf8Bytes(eventSignature));

            // 2) Cari log
            const contentAddedLog = receipt.logs.find(
                (log) => log.topics[0] === eventSignatureHash
            );

            // 3) Jika ketemu, decode isinya seperti biasa
            let block_id = null;
            if (contentAddedLog) {
                const decodedLog = contract.interface.parseLog(contentAddedLog);
                block_id = decodedLog.args.id.toString();
                addLog(`[DEBUG] ContentAdded event => block_id=${block_id}`);
            } else {
                addLog("[DEBUG] No ContentAdded event found in logs.");
            }

            // 6) Tampilkan notifikasi
            // Swal.fire({
            //     title: "Content Added To Blockchain!",
            //     html: `TxHash: <strong>${receipt.transactionHash}</strong><br>Block#: <strong>${receipt.blockNumber}</strong>`,
            //     icon: "success",
            //     confirmButtonText: "OK",
            // });

            // 7) Kirim data ke server
            const response = await fetch(window._SAVE_DEPLOY_ROUTE, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                body: JSON.stringify({
                    content_id: contentId,
                    user_metamask_address: signer.address || "", // Address user
                    tx_hash: receipt.hash,
                    block_no: receipt.blockNumber,
                    contract_address: CONTRACT_ADDRESS,
                    provider: "xBug",
                    status_contract: 1,
                    tx_id: block_id,
                    content_name: _name,
                    logs: JSON.stringify(logs),
                }),
            });

            if (response.ok) {
                const result = await response.json();
                addLog(`[INFO] Server save result: ${result.message}`);
                toastr.success(result.message || "Contract data saved!");
                table.ajax.reload(null, false);
            } else {
                addLog(`[ERROR] Gagal menyimpan data/logs ke server.`);
                toastr.error("Failed to save data/logs to server.");
            }
            Swal.fire({
                html: `
                    <img src="https://upload.wikimedia.org/wikipedia/commons/1/13/Walletconnect-logo.png" 
                         alt="Wallet Logo" 
                         class="img-fluid mb-3" 
                         style="max-width: 100px;">
                    <h6 class="fw-bold text-primary">Content and Transaction added To Blockchain</h6>
                    <p class="text-dark mb-3 fw-bold">TxHash: <strong class="text-success">${receipt.hash}</strong><br>Block#: <strong class="text-success">${receipt.blockNumber}</strong></p>
                `,
                confirmButtonText: '<span class="fw-semibold">OK</span>',
                buttonsStyling: false,
                customClass: {
                    confirmButton: "btn btn-primary px-5 py-2",
                    popup: "border rounded-3 shadow-lg p-4",
                },
            });
        } catch (error) {
            console.error(error);
            addLog(`[ERROR] ${error.message}`);
            // Swal.fire({
            //     title: "Error",
            //     html: `Transaction failed or declined.<br>${error.message}`,
            //     icon: "error",
            //     confirmButtonText: "OK",
            // });

            Swal.fire({
                html: `
                    <img src="https://upload.wikimedia.org/wikipedia/commons/1/13/Walletconnect-logo.png" 
                         alt="Wallet Logo" 
                         class="img-fluid mb-3" 
                         style="max-width: 100px;">
                    <h6 class="fw-bold text-danger">Failed</h6>
                    <span class="fw-bold text-primary">The transaction failed or declined.</span>
                `,
                confirmButtonText: '<span class="fw-semibold">OK</span>',
                buttonsStyling: false,
                customClass: {
                    confirmButton: "btn btn-primary px-5 py-2",
                    popup: "border rounded-3 shadow-lg p-4",
                },
            });
        }

        // Apapun hasilnya, re-enable button & modal
        $(modalId).modal("hide");
        xCancel.prop("disabled", false);
        cancelBtnC.prop("disabled", false);
        deployButtons.prop("disabled", false);
        deployButtons.html('<i class="bi bi-check-circle-fill me-1"></i> Sign');
        table.ajax.reload(null, false);
    });
});
