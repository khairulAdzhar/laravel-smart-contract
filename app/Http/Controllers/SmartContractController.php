<?php

namespace App\Http\Controllers;

use App\Mail\SmartContractNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class SmartContractController extends Controller
{
    public function showContentBlockchainOrg(Request $request)
    {
        $user = Auth::user();
        $user_data = DB::table('contents')->where('contents.user_id', $user->id)
            ->join('content_types', 'contents.content_type_id', '=', 'content_types.id')
            ->leftJoin('smart_contract', 'contents.id', '=', 'smart_contract.content_id')
            ->join('organization_user', 'contents.user_id', '=', 'organization_user.user_id')
            ->join('organization', 'organization_user.organization_id', '=', 'organization.id')
            ->where('contents.reason_phrase', 'APPROVED')
            ->select(
                'contents.id',
                'contents.name',
                'contents.created_at',
                'contents.link',
                'contents.status',
                'contents.user_id',
                'contents.enrollment_price',
                'contents.place',
                'contents.reason_phrase',
                'contents.reject_reason',
                'contents.participant_limit',
                'contents.content_type_id',
                'content_types.type',
                'smart_contract.tx_hash as smart_contract_tx_hash',
                'smart_contract.id as smart_contract_id',
                'smart_contract.block_no as smart_contract_block_no',
                'smart_contract.contract_address as smart_contract_contract_address',
                'smart_contract.address as smart_contract_address',
                'smart_contract.status_contract as smart_contract_status_contract',
                'smart_contract.contract_verified_at as smart_contract_verfied_at',
                'smart_contract.created_at as smart_contract_created_at',
                'smart_contract.tx_id as smart_contract_tx_id',
                'organization.name as organization_name'
            )
            ->orderBy('smart_contract.created_at', 'desc')
            ->get();

        if ($request->ajax()) {
            $table = DataTables::of($user_data)->addIndexColumn();

            // Add 'status' column
            $table->addColumn('status', function ($row) {
                if ($row->smart_contract_status_contract === 1) {
                    $button = '
                        <button class="btn btn-icon btn-sm btn-success-transparent rounded-pill ms-2"
                            data-bs-toggle="modal" data-bs-target="#modalView-' . $row->id . '">
                            <i class="ri-eye-line fw-bold"></i>
                        </button>';
                } elseif ($row->smart_contract_status_contract === 0) {
                    $button =  '<span class="badge bg-danger p-2 fw-bold">FAILED</span>';
                } elseif ($row->smart_contract_status_contract === 2) {
                    $button =  '<span class="badge bg-warning p-2 fw-bold">WAITING FOR ETHEREUM</span>';
                } else {
                    $button =  '<span class="p-2 fw-bold">-</span>';
                }
                return $button;
            });

            // Add 'action' column
            $table->addColumn('action', function ($row) {
                if ($row->smart_contract_status_contract === 1) {
                    $button = '<span class="badge bg-success p-2 fw-bold">DEPLOY</span>';
                } elseif ($row->smart_contract_status_contract === 0) {
                    $button =  '<span class="badge bg-danger p-2 fw-bold me-1">FAILED</span>';
                } elseif ($row->smart_contract_status_contract === 2) {
                    $button =  '<span class="badge bg-warning p-2 fw-bold">WAITING FOR ETHEREUM</span>';
                } else {
                    $button =  '<div class="d-flex justify-content-start">
                                <button class="btn btn-icon btn-success-transparent btn-md rounded-pill me-2"
                                    data-bs-toggle="modal" data-bs-target="#confirmation-' . $row->id . '">
                                    <i class="bi bi-file-earmark-lock-fill fw-bold"></i>
                                </button>
                            </div>';
                }
                return $button;
            });

            // Add 'network' column
            $table->addColumn('network', function ($row) {
                return '<span class="text-dark p-2 fw-bold">Sepolia Network</span>';
            });

            // Add 'tx_hash' column
            $table->addColumn('tx_hash', function ($row) {
                if ($row->smart_contract_status_contract === 1) {
                    // $shortenedTxHash = strlen($row->smart_contract_tx_hash) > 15
                    //     ? substr($row->smart_contract_tx_hash, 0, 18) . '...'
                    //     : $row->smart_contract_tx_hash;

                    $button = '
                    <div class="d-flex align-items-center">
                        <span class="p-2 fw-bold break-all">
                            ' . $row->smart_contract_tx_hash . '
                        </span>
                        <button class="btn btn-light btn-sm ms-1 copy-btn" data-copy="' . $row->smart_contract_tx_hash . '" title="Copy to clipboard">
                            <i class="bi bi-clipboard"></i>
                        </button>
                        <a href="https://sepolia.etherscan.io/tx/' . $row->smart_contract_tx_hash . '"
                           target="_blank"
                           class="text-dark text-decoration-none fw-bold ms-1 bg-light btn btn-sm"
                           title="View on Etherscan">
                           <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                    </div>
                    <script>
                        document.querySelectorAll(".copy-btn").forEach(button => {
                            button.addEventListener("click", () => {
                                const textToCopy = button.getAttribute("data-copy");
                                navigator.clipboard.writeText(textToCopy)
                                    .then(() => toastr.success("Copied: " + textToCopy))
                                    .catch(err => console.error("Failed to copy: ", err));
                            });
                        });
                    </script>
                ';
                } else {
                    $button =  '<span class="p-2 fw-bold">-</span>';
                }
                return $button;
            });

            // Add 'block_id' column
            $table->addColumn('block_id', function ($row) {
                if ($row->smart_contract_status_contract === 1) {
                    $button = '
                    <span class="p-2 fw-bold">
                        ' . $row->smart_contract_block_no . '
                    </span>
                    <button class="btn btn-light btn-sm ms-1 copy-btn" data-copy="' . $row->smart_contract_block_no . '">
                        <i class="bi bi-clipboard"></i>
                    </button>
                    <a href="https://sepolia.etherscan.io/block/' . $row->smart_contract_block_no . '"
                           target="_blank"
                           class="text-dark text-decoration-none fw-bold ms-1 bg-light btn btn-sm"
                           title="View on Etherscan">
                           <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                    <script>
                        document.querySelectorAll(".copy-btn").forEach(button => {
                            button.addEventListener("click", () => {
                                const textToCopy = button.getAttribute("data-copy");
                                navigator.clipboard.writeText(textToCopy)
                                    .then(() => toastr.success("Copied: " + textToCopy))
                                    .catch(err => console.error("Failed to copy: ", err));
                            });
                        });
                    </script>
                ';
                } else {
                    $button =  '<span class="p-2 fw-bold">-</span>';
                }
                return $button;
            });

            // Add 'blockchain_id' column
            $table->addColumn('blockchain_id', function ($row) {
                if ($row->smart_contract_status_contract === 1) {
                    $button = '
                    <span class="p-2 fw-bold">
                        ' . $row->smart_contract_tx_id . '
                    </span>
                    <button class="btn btn-light btn-sm ms-1 copy-btn" data-copy="' . $row->smart_contract_tx_id . '">
                        <i class="bi bi-clipboard"></i>
                    </button>
                          <a href="https://sepolia.etherscan.io/address/' . $row->smart_contract_contract_address . '#readContract#F2"
                           target="_blank"
                           class="text-dark text-decoration-none fw-bold ms-1 bg-light btn btn-sm"
                           title="View on Etherscan">
                           <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                    <script>
                        document.querySelectorAll(".copy-btn").forEach(button => {
                            button.addEventListener("click", () => {
                                const textToCopy = button.getAttribute("data-copy");
                                navigator.clipboard.writeText(textToCopy)
                                    .then(() => toastr.success("Copied: " + textToCopy))
                                    .catch(err => console.error("Failed to copy: ", err));
                            });
                        });
                    </script>
                ';
                } else {
                    $button =  '<span class="p-2 fw-bold">-</span>';
                }
                return $button;
            });

            // **Rename 'log' to 'logs'**
            $table->addColumn('logs', function ($row) {
                if ($row->smart_contract_status_contract === 1) {
                    $button = '
                        <button class="btn btn-icon btn-sm btn-success-transparent rounded-pill ms-2"
                            data-bs-toggle="modal" data-bs-target="#viewLogs-' . $row->id . '">
                            <i class="ri-eye-line fw-bold"></i> View Logs
                        </button>';
                } elseif ($row->smart_contract_status_contract === 0) {
                    $button =  '<span class="badge bg-danger p-2 fw-bold">FAILED</span>';
                } elseif ($row->smart_contract_status_contract === 2) {
                    $button =  '<span class="badge bg-warning p-2 fw-bold">WAITING FOR ETHEREUM</span>';
                } else {
                    $button =  '<span class="p-2 fw-bold">-</span>';
                }
                return $button;
            });

            // **Update rawColumns to include 'logs' instead of 'log'**
            $table->rawColumns(['status', 'action', 'network', 'tx_hash', 'block_id', 'blockchain_id', 'logs']);

            return $table->make(true);
        }

        return view('organization.contentBlockchain.index', [
            'content_data' => $user_data,
        ]);
    }
    // public function index(Request $request)
    // {
    //     // Jika request AJAX DataTables
    //     if ($request->ajax()) {
    //         // Ambil data content dari table 'contents' (contoh),
    //         // join dengan smart_contract jika ada
    //         // Sesuaikan kolom actual Anda
    //         $data = DB::table('contents')
    //             ->leftJoin('smart_contract', 'contents.id', '=', 'smart_contract.content_id')
    //             ->select(
    //                 'contents.id',
    //                 'contents.name',
    //                 // 'smart_contract.tx_hash', 'smart_contract.contract_address', dsb.
    //                 'smart_contract.status_contract',
    //             )
    //             ->orderBy('contents.id', 'desc')
    //             ->get();

    //         return datatables()->of($data)
    //             ->addIndexColumn()
    //             ->addColumn('status', function ($row) {
    //                 // Tampilkan status
    //                 if ($row->status_contract == 1) {
    //                     return '<span class="badge bg-success">ON CHAIN</span>';
    //                 } elseif ($row->status_contract == 0) {
    //                     return '<span class="badge bg-danger">FAILED</span>';
    //                 } else {
    //                     return '<span class="badge bg-secondary">NOT YET</span>';
    //                 }
    //             })
    //             ->addColumn('action', function ($row) {
    //                 // Kosong, akan di-render di Blade (JS)
    //                 return '';
    //             })
    //             ->rawColumns(['status'])
    //             ->make(true);
    //     }

    //     // Jika bukan request AJAX, kembalikan Blade
    //     return view('organization.contentBlockchain.index');
    // }

    /**
     * Menerima data setelah user berhasil memanggil addContent(...) di blockchain,
     * lalu menyimpan ke table smart_contract
     */
    public function saveDeployedData(Request $request)
    {
        try {

            $validated = $request->validate([
                'content_id'        => 'required|integer',
                'user_metamask_address' => 'nullable|string',
                'tx_hash'           => 'required|string',
                'block_no'          => 'nullable',
                'contract_address'  => 'required|string',
                'provider'          => 'nullable|string',
                'status_contract'   => 'nullable|integer',
                'tx_id'          => 'nullable',
                'content_name'     => 'required|string',
                'logs'                   => 'nullable',
            ]);

            // Contoh: asumsikan user_id = 1 (atau Anda bisa cari userId dari Auth)
            $userId =  Auth::user()->id;

            // Cek apakah ada data dengan content_id
            $existingSmartContract = DB::table('smart_contract')
                ->where('content_id', $validated['content_id'])
                ->first();

            if ($existingSmartContract) {
                // Jika ada, lakukan update
                DB::table('smart_contract')
                    ->where('id', $existingSmartContract->id)
                    ->update([
                        'user_id'         => $userId,
                        'provider'        => $validated['provider'] ?? 'xBug',
                        'tx_hash'         => $validated['tx_hash'],
                        'block_no'        => $validated['block_no'] ?? '',
                        'address'         => $request->input('user_metamask_address') ?? '',
                        'tx_id'           => $validated['tx_id'],
                        'contract_address' => $validated['contract_address'],
                        'status_contract' => $validated['status_contract'] ?? 1,
                        'updated_at'      => now(),
                    ]);

                // Dapatkan ID
                $smartContractId = $existingSmartContract->id;
            } else {
                // Jika tidak ada, lakukan insert dan dapatkan ID
                $smartContractId = DB::table('smart_contract')->insertGetId([
                    'user_id'         => $userId,
                    'content_id'      => $validated['content_id'],
                    'provider'        => $validated['provider'] ?? 'xBug',
                    'tx_hash'         => $validated['tx_hash'],
                    'block_no'        => $validated['block_no'] ?? '',
                    'address'         => $request->input('user_metamask_address') ?? '',
                    'tx_id'           => $validated['tx_id'],
                    'contract_address' => $validated['contract_address'],
                    'status_contract' => $validated['status_contract'] ?? 1,
                    'updated_at'      => now(),
                    'created_at'      => now(),
                ]);
            }

            // Proses logs
            $logsString = $request->input('logs'); // JSON string
            if ($logsString) {
                $logsArray = json_decode($logsString, true); // Decode to array
                if (is_array($logsArray)) {
                    // Loop each log
                    foreach ($logsArray as $logEntry) {
                        $logMessage = $logEntry['message'] ?? '[no message]';

                        DB::table('smart_contract_logs')->insert([
                            'smart_contract_id' => $smartContractId,
                            'log_message'       => $logMessage,
                            'created_at'        => now(),
                        ]);
                    }
                }
            }

            $email_status = DB::table('email_status')->where('email', 'blockchain@xbug.online')->first();
            if ($email_status && $email_status->status == 1) {
                $status = $validated['status_contract'];
                $rejection_reason = '';
                $tx_hsh = $validated['tx_hash'];
                $name = Auth::user()->name;
                $content_name = $validated['content_name'];
                Mail::to(Auth::user()->email)->send(new SmartContractNotificationMail($status, $rejection_reason, $tx_hsh, $name, $content_name));
                $logData = [
                    'email_type' => 'SMART CONTRACT',
                    'recipient_email' => Auth::user()->email,
                    'from_email' => 'blockchain@xbug.online',
                    'name' => Auth::user()->name,
                    'status' => 'SUCCESS',
                    'response_data' => 'MESSAGE SUCCESS SEND: TRANSACTION HASH - '.$validated['tx_hash'],
                    'created_at' => Carbon::now('Asia/Kuala_Lumpur')->toDateTimeString(),
                    'updated_at' => Carbon::now('Asia/Kuala_Lumpur')->toDateTimeString(),
                ];

                DB::table('email_logs')->insert($logData);
            }


            return response()->json([
                'message' => 'Smart contract data saved successfully!',
            ], 200);
        } catch (\Exception $e) {
            // Supaya tidak HTML error page
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getLogs($id)
    {
        // sleep(2);
        // Validate that $id is an integer
        if (!is_numeric($id)) {
            return response()->json(['error' => 'Invalid smart contract ID.'], 400);
        }

        // Fetch logs from the database, ordered by created_at ascending
        $logs = DB::table('smart_contract_logs')
            ->where('smart_contract_id', $id)
            ->orderBy('id', 'desc')
            ->get();

        // Check if logs exist
        if ($logs->isEmpty()) {
            return response()->json(['message' => 'No logs found for this smart contract.'], 404);
        }

        return response()->json(['logs' => $logs], 200);
    }
}
