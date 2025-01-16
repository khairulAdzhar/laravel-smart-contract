<?php

namespace App\Http\Controllers;

use App\Jobs\DeploySmartContractJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

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
                    $shortenedTxHash = strlen($row->smart_contract_tx_hash) > 15
                        ? substr($row->smart_contract_tx_hash, 0, 18) . '...'
                        : $row->smart_contract_tx_hash;
    
                    $button = '
                    <div class="d-flex align-items-center">
                        <span class="p-2 fw-bold">
                            ' . $shortenedTxHash . '
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
    
    // public function showContentBlockchainOrg(Request $request)
    // {
    //     $user = Auth::user();
    //     $user_data = DB::table('contents')->where('contents.user_id', $user->id)
    //         ->join('content_types', 'contents.content_type_id', '=', 'content_types.id')
    //         ->leftJoin('smart_contract', 'contents.id', '=', 'smart_contract.content_id')
    //         ->join('organization_user', 'contents.user_id', '=', 'organization_user.user_id')
    //         ->join('organization', 'organization_user.organization_id', '=', 'organization.id')
    //         ->where('contents.reason_phrase', 'APPROVED')
    //         ->select(
    //             'contents.id',
    //             'contents.name',
    //             'contents.created_at',
    //             'contents.link',
    //             'contents.status',
    //             'contents.user_id',
    //             'contents.enrollment_price',
    //             'contents.place',
    //             'contents.reason_phrase',
    //             'contents.reject_reason',
    //             'contents.participant_limit',
    //             'contents.content_type_id',
    //             'content_types.type',
    //             'smart_contract.tx_hash as smart_contract_tx_hash',
    //             'smart_contract.id as smart_contract_id',
    //             'smart_contract.block_no as smart_contract_block_no',
    //             'smart_contract.contract_address as smart_contract_contract_address',
    //             'smart_contract.address as smart_contract_address',
    //             'smart_contract.status_contract as smart_contract_status_contract',
    //             'smart_contract.contract_verified_at as smart_contract_verfied_at',
    //             'smart_contract.created_at as smart_contract_created_at',
    //             'smart_contract.tx_id as smart_contract_tx_id',
    //             'organization.name as organization_name'
    //         )
    //         ->orderBy('smart_contract.created_at', 'desc')
    //         ->get();

    //     // dd($user_data);

    //     if ($request->ajax()) {
    //         $table = DataTables::of($user_data)->addIndexColumn();

    //         $table->addColumn('status', function ($row) {
    //             if ($row->smart_contract_status_contract === 1) {
    //                 $button = '
    //                             <button class="btn btn-icon btn-sm btn-success-transparent rounded-pill ms-2"
    //                                 data-bs-toggle="modal" data-bs-target="#modalView-' . $row->id . '">
    //                                 <i class="ri-eye-line fw-bold"></i>
    //                             </button>';
    //                 // $button = '<span class="badge bg-success p-2 fw-bold">SUCCESS</span>
    //                 //             <button class="btn btn-icon btn-sm btn-success-transparent rounded-pill ms-2"
    //                 //                 data-bs-toggle="modal" data-bs-target="#modalView-' . $row->id . '">
    //                 //                 <i class="ri-eye-line fw-bold"></i>
    //                 //             </button>';
    //             } elseif ($row->smart_contract_status_contract === 0) {
    //                 $button =  '<span class="badge bg-danger p-2 fw-bold">FAILED</span>';
    //             } elseif ($row->smart_contract_status_contract === 2) {
    //                 $button =  '<span class="badge bg-warning p-2 fw-bold">WAITING FOR ETHEREUM</span>';
    //             } elseif ($row->smart_contract_status_contract === '' || $row->smart_contract_status_contract === null) {
    //                 $button =  '<span class="p-2 fw-bold">-</span>';
    //             }
    //             return $button;
    //         });

    //         $table->addColumn('action', function ($row) {
    //             if ($row->smart_contract_status_contract === 1) {
    //                 $button = '<span class="badge bg-success p-2 fw-bold">DEPLOY</span>';
    //             } elseif ($row->smart_contract_status_contract === 0) {
    //                 // $button =  '<span class="badge bg-danger p-2 fw-bold me-1">FAILED</span>
                  
    //                 //             <button class="btn btn-icon btn-success-transparent btn-md rounded-pill me-2"
    //                 //                 data-bs-toggle="modal" data-bs-target="#confirmation-' . $row->id . '">
    //                 //                 <i class="bi bi-file-earmark-lock-fill fw-bold"></i>
    //                 //             </button>
    //                 //         ';
    //                 $button =  '<span class="badge bg-danger p-2 fw-bold me-1">FAILED</span>
                  
    //                         ';
    //             } elseif ($row->smart_contract_status_contract === 2) {
    //                 $button =  '<span class="badge bg-warning p-2 fw-bold">WAITING FOR ETHEREUM</span>';
    //             } elseif ($row->smart_contract_status_contract === '' || $row->smart_contract_status_contract === null) {
    //                 $button =  '<div class="d-flex justify-content-start">
    //                             <button class="btn btn-icon btn-success-transparent btn-md rounded-pill me-2"
    //                                 data-bs-toggle="modal" data-bs-target="#confirmation-' . $row->id . '">
    //                                 <i class="bi bi-file-earmark-lock-fill fw-bold"></i>
    //                             </button>
    //                         </div>';
    //             }
    //             return $button;
    //         });

    //         $table->addColumn('network', function ($row) {
    //             return '<span class="text-dark p-2 fw-bold">Sepolia Network</span';
    //         });

    //         $table->addColumn('tx_hash', function ($row) {
    //             if ($row->smart_contract_status_contract === 1) {
    //                 $shortenedTxHash = strlen($row->smart_contract_tx_hash) > 15
    //                     ? substr($row->smart_contract_tx_hash, 0, 18) . '...'
    //                     : $row->smart_contract_tx_hash;

    //                 $button = '
    //                 <div class="d-flex align-items-center">
    //                     <span class="p-2 fw-bold">
    //                         ' . $shortenedTxHash . '
    //                     </span>
    //                     <button class="btn btn-light btn-sm ms-1 copy-btn" data-copy="' . $row->smart_contract_tx_hash . '" title="Copy to clipboard">
    //                         <i class="bi bi-clipboard"></i>
    //                     </button>
    //                     <a href="https://sepolia.etherscan.io/tx/' . $row->smart_contract_tx_hash . '"
    //                        target="_blank"
    //                        class="text-dark text-decoration-none fw-bold ms-1 bg-light btn btn-sm"
    //                        title="View on Etherscan">
    //                        <i class="bi bi-box-arrow-up-right"></i>
    //                     </a>
    //                 </div>
    //                 <script>
    //                     document.querySelectorAll(".copy-btn").forEach(button => {
    //                         button.addEventListener("click", () => {
    //                             const textToCopy = button.getAttribute("data-copy");
    //                             navigator.clipboard.writeText(textToCopy)
    //                                 .then(() => toastr.success("Copied: " + textToCopy))
    //                                 .catch(err => console.error("Failed to copy: ", err));
    //                         });
    //                     });
    //                 </script>
    //             ';
    //             } elseif ($row->smart_contract_status_contract === 0) {
    //                 $button =  '<span class="p-2 fw-bold">-</span>';
    //             } elseif ($row->smart_contract_status_contract === 2) {
    //                 $button =  '<span class="p-2 fw-bold">-</span>';
    //             } elseif ($row->smart_contract_status_contract === '' || $row->smart_contract_status_contract === null) {
    //                 $button =  '<span class="p-2 fw-bold">-</span>';
    //             }
    //             return $button;
    //         });
    //         $table->addColumn('block_id', function ($row) {
    //             if ($row->smart_contract_status_contract === 1) {

    //                 $button = '
    //                 <span class="p-2 fw-bold">
    //                     ' . $row->smart_contract_block_no . '
    //                 </span>
    //                 <button class="btn btn-light btn-sm ms-1 copy-btn" data-copy="' . $row->smart_contract_block_no . '">
    //                     <i class="bi bi-clipboard"></i>
    //                 </button>
    //                 <script>
    //                     document.querySelectorAll(".copy-btn").forEach(button => {
    //                         button.addEventListener("click", () => {
    //                             const textToCopy = button.getAttribute("data-copy");
    //                             navigator.clipboard.writeText(textToCopy)
    //                                 .then(() => toastr.success("Copied: " + textToCopy))
    //                                 .catch(err => console.error("Failed to copy: ", err));
    //                         });
    //                     });
    //                 </script>
    //             ';
    //             } elseif ($row->smart_contract_status_contract === 0) {
    //                 $button =  '<span class="p-2 fw-bold">-</span>';
    //             } elseif ($row->smart_contract_status_contract === 2) {
    //                 $button =  '<span class="p-2 fw-bold">-</span>';
    //             } elseif ($row->smart_contract_status_contract === '' || $row->smart_contract_status_contract === null) {
    //                 $button =  '<span class="p-2 fw-bold">-</span>';
    //             }
    //             return $button;
    //         });
    //         $table->addColumn('blockchain_id', function ($row) {
    //             if ($row->smart_contract_status_contract === 1) {

    //                 $button = '
    //                 <span class="p-2 fw-bold">
    //                     ' . $row->smart_contract_tx_id . '
    //                 </span>
    //                 <button class="btn btn-light btn-sm ms-1 copy-btn" data-copy="' . $row->smart_contract_tx_id . '">
    //                     <i class="bi bi-clipboard"></i>
    //                 </button>
    //                       <a href="https://sepolia.etherscan.io/address/' . $row->smart_contract_contract_address . '#readContract#F2"
    //                        target="_blank"
    //                        class="text-dark text-decoration-none fw-bold ms-1 bg-light btn btn-sm"
    //                        title="View on Etherscan">
    //                        <i class="bi bi-box-arrow-up-right"></i>
    //                     </a>
    //                 <script>
    //                     document.querySelectorAll(".copy-btn").forEach(button => {
    //                         button.addEventListener("click", () => {
    //                             const textToCopy = button.getAttribute("data-copy");
    //                             navigator.clipboard.writeText(textToCopy)
    //                                 .then(() => toastr.success("Copied: " + textToCopy))
    //                                 .catch(err => console.error("Failed to copy: ", err));
    //                         });
    //                     });
    //                 </script>
    //             ';
    //             } elseif ($row->smart_contract_status_contract === 0) {
    //                 $button =  '<span class="p-2 fw-bold">-</span>';
    //             } elseif ($row->smart_contract_status_contract === 2) {
    //                 $button =  '<span class="p-2 fw-bold">-</span>';
    //             } elseif ($row->smart_contract_status_contract === '' || $row->smart_contract_status_contract === null) {
    //                 $button =  '<span class="p-2 fw-bold">-</span>';
    //             }
    //             return $button;
    //         });

    //         $table->addColumn('log', function ($row) {
    //             if ($row->smart_contract_status_contract === 1) {
    //                 $button = '
    //                             <button class="btn btn-icon btn-sm btn-success-transparent rounded-pill ms-2"
    //                                 data-bs-toggle="modal" data-bs-target="#viewLogs-' . $row->smart_contract_id . '">
    //                                 <i class="ri-eye-line fw-bold"></i>
    //                             </button>';
    //             } elseif ($row->smart_contract_status_contract === 0) {
    //                 $button =  '<span class="badge bg-danger p-2 fw-bold">FAILED</span>';
    //             } elseif ($row->smart_contract_status_contract === 2) {
    //                 $button =  '<span class="badge bg-warning p-2 fw-bold">WAITING FOR ETHEREUM</span>';
    //             } elseif ($row->smart_contract_status_contract === '' || $row->smart_contract_status_contract === null) {
    //                 $button =  '<span class="p-2 fw-bold">-</span>';
    //             }
    //             return $button;
    //         });

    //         $table->rawColumns(['status', 'action', 'network', 'tx_hash', 'block_id', 'blockchain_id', 'log']);
    //         return $table->make(true);
    //     }
    //     return view('organization.contentBlockchain.index', [
    //         'content_data' => $user_data,
    //     ]);
    // }
    public function showContentBlockchainlogsOrg(Request $request)
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

        // dd($user_data);

        if ($request->ajax()) {
            $table = DataTables::of($user_data)->addIndexColumn();

            $table->addColumn('status', function ($row) {
                if ($row->smart_contract_status_contract === 1) {
                    // $button = '
                    //             <button class="btn btn-icon btn-sm btn-success-transparent rounded-pill ms-2"
                    //                 data-bs-toggle="modal" data-bs-target="#modalView-' . $row->id . '">
                    //                 <i class="ri-eye-line fw-bold"></i>
                    //             </button>';
                    $button = '<span class="badge bg-success p-2 fw-bold">SUCCESS</span>
                                ';
                } elseif ($row->smart_contract_status_contract === 0) {
                    $button =  '<span class="badge bg-danger p-2 fw-bold">FAILED</span>';
                } elseif ($row->smart_contract_status_contract === 2) {
                    $button =  '<span class="badge bg-warning p-2 fw-bold">WAITING FOR ETHEREUM</span>';
                } elseif ($row->smart_contract_status_contract === '' || $row->smart_contract_status_contract === null) {
                    $button =  '<span class="p-2 fw-bold">-</span>';
                }
                return $button;
            });

            $table->addColumn('action', function ($row) {
                if ($row->smart_contract_status_contract === 1) {
                    $button = '<span class="badge bg-success p-2 fw-bold">DEPLOY</span>';
                } elseif ($row->smart_contract_status_contract === 0) {
                    $button =  '<span class="badge bg-danger p-2 fw-bold me-1">FAILED</span>
                  
                                <button class="btn btn-icon btn-success-transparent btn-md rounded-pill me-2"
                                    data-bs-toggle="modal" data-bs-target="#confirmation-' . $row->id . '">
                                    <i class="bi bi-file-earmark-lock-fill fw-bold"></i>
                                </button>
                            ';
                } elseif ($row->smart_contract_status_contract === 2) {
                    $button =  '<span class="badge bg-warning p-2 fw-bold">WAITING FOR ETHEREUM</span>';
                } elseif ($row->smart_contract_status_contract === '' || $row->smart_contract_status_contract === null) {
                    $button =  '<div class="d-flex justify-content-start">
                                <button class="btn btn-icon btn-success-transparent btn-md rounded-pill me-2"
                                    data-bs-toggle="modal" data-bs-target="#confirmation-' . $row->id . '">
                                    <i class="bi bi-file-earmark-lock-fill fw-bold"></i>
                                </button>
                            </div>';
                }
                return $button;
            });

            $table->addColumn('network', function ($row) {
                return '<span class="text-dark p-2 fw-bold">Sepolia Network</span';
            });

            $table->addColumn('tx_hash', function ($row) {
                if ($row->smart_contract_status_contract === 1) {
                    $shortenedTxHash = strlen($row->smart_contract_tx_hash) > 15
                        ? substr($row->smart_contract_tx_hash, 0, 18) . '...'
                        : $row->smart_contract_tx_hash;

                    $button = '
                    <div class="d-flex align-items-center">
                        <span class="p-2 fw-bold">
                            ' . $shortenedTxHash . '
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
                } elseif ($row->smart_contract_status_contract === 0) {
                    $button =  '<span class="p-2 fw-bold">-</span>';
                } elseif ($row->smart_contract_status_contract === 2) {
                    $button =  '<span class="p-2 fw-bold">-</span>';
                } elseif ($row->smart_contract_status_contract === '' || $row->smart_contract_status_contract === null) {
                    $button =  '<span class="p-2 fw-bold">-</span>';
                }
                return $button;
            });
            $table->addColumn('block_id', function ($row) {
                if ($row->smart_contract_status_contract === 1) {

                    $button = '
                    <span class="p-2 fw-bold">
                        ' . $row->smart_contract_block_no . '
                    </span>
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
                } elseif ($row->smart_contract_status_contract === 0) {
                    $button =  '<span class="p-2 fw-bold">-</span>';
                } elseif ($row->smart_contract_status_contract === 2) {
                    $button =  '<span class="p-2 fw-bold">-</span>';
                } elseif ($row->smart_contract_status_contract === '' || $row->smart_contract_status_contract === null) {
                    $button =  '<span class="p-2 fw-bold">-</span>';
                }
                return $button;
            });
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
                } elseif ($row->smart_contract_status_contract === 0) {
                    $button =  '<span class="p-2 fw-bold">-</span>';
                } elseif ($row->smart_contract_status_contract === 2) {
                    $button =  '<span class="p-2 fw-bold">-</span>';
                } elseif ($row->smart_contract_status_contract === '' || $row->smart_contract_status_contract === null) {
                    $button =  '<span class="p-2 fw-bold">-</span>';
                }
                return $button;
            });

            $table->addColumn('log', function ($row) {
                if ($row->smart_contract_status_contract === 1) {
                    $button = '
                                <button class="btn btn-icon btn-sm btn-success-transparent rounded-pill ms-2"
                                    data-bs-toggle="modal" data-bs-target="#viewLogs-' . $row->id . '">
                                    <i class="ri-eye-line fw-bold"></i>
                                </button>';
                } elseif ($row->smart_contract_status_contract === 0) {
                    $button =  '<span class="badge bg-danger p-2 fw-bold">FAILED</span>';
                } elseif ($row->smart_contract_status_contract === 2) {
                    $button =  '<span class="badge bg-warning p-2 fw-bold">WAITING FOR ETHEREUM</span>';
                } elseif ($row->smart_contract_status_contract === '' || $row->smart_contract_status_contract === null) {
                    $button =  '<span class="p-2 fw-bold">-</span>';
                }
                return $button;
            });

            $table->rawColumns(['status', 'action', 'network', 'tx_hash', 'block_id', 'blockchain_id', 'log']);
            return $table->make(true);
        }
        return view('organization.contentBlockchain.logs', [
            'content_data' => $user_data,
        ]);
    }

    public function deploySmartContract(Request $request, $id)
    {
        // Ambil data content berdasarkan ID
        $content_data = DB::table('contents')->where('contents.id', $id)
            ->join('content_types', 'contents.content_type_id', '=', 'content_types.id')
            ->join('organization_user', 'contents.user_id', '=', 'organization_user.user_id')
            ->join('users', 'contents.user_id', '=', 'users.id')
            ->join('organization', 'organization_user.organization_id', '=', 'organization.id')
            ->where('contents.reason_phrase', 'APPROVED')
            ->select(
                'contents.name as content_name',
                'contents.created_at as content_created_at',
                'contents.link as content_link',
                'contents.enrollment_price as content_enrollment_price',
                'contents.place as content_place',
                'contents.participant_limit as content_participant_limit',
                'content_types.type as content_type',
                'organization.name as organization_name',
                'users.name as user_name',
                'contents.user_id as user_id'
            )
            ->first();

        if (!$content_data) {
            return response()->json(['error' => 'Content not found or not approved.'], 400);
        }

        // Insert ke table smart_contract dengan status_contract = 2 (pending)
        $smartContractId = DB::table('smart_contract')->insertGetId([
            'content_id' => $id,
            'user_id' => $content_data->user_id,
            'provider' => 'xBug', // Sesuaikan jika perlu
            'tx_hash' => null,
            'block_no' => null,
            'address' => env('ETH_ADRRESS'),
            'tx_id' => null,
            'contract_address' => env('CONTRACT_ADDRESS'), // Sesuaikan jika perlu
            'status_contract' => 2, // 2 = Pending
            'contract_verified_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Siapkan data untuk dikirim ke Express.js
        $deploymentData = [
            'content_id' => $id,
            'content_name' => $content_data->content_name,
            'content_created_at' => $content_data->content_created_at,
            'content_link' => $content_data->content_link,
            'content_enrollment_price' => $content_data->content_enrollment_price,
            'content_place' => $content_data->content_place,
            'content_participant_limit' => $content_data->content_participant_limit,
            'content_type' => $content_data->content_type,
            'provider' => 'xBug',
            'organization_name' => $content_data->organization_name,
            'user_name' => $content_data->user_name,
            'user_id' => $content_data->user_id,
        ];

        $user = DB::table('users')->select([ 'is_smart_contract_status', 'is_gpt'])->where('id', Auth::id())->first();
        // Dispatch job untuk deploy smart contract
        DeploySmartContractJob::dispatch($deploymentData, $smartContractId);

        // Kembalikan respons JSON untuk AJAX
        return response()->json(['message' => 'Smart contract deployment initiated.'], 200);
    }
    // public function deploySmartContract(Request $request, $id)
    // {
    //     // Ambil data content berdasarkan ID
    //     $content_data = DB::table('contents')->where('contents.id', $id)
    //         ->join('content_types', 'contents.content_type_id', '=', 'content_types.id')
    //         ->join('organization_user', 'contents.user_id', '=', 'organization_user.user_id')
    //         ->join('users', 'contents.user_id', '=', 'users.id')
    //         ->join('organization', 'organization_user.organization_id', '=', 'organization.id')
    //         ->where('contents.reason_phrase', 'APPROVED')
    //         ->select(
    //             'contents.name as content_name',
    //             'contents.created_at as content_created_at',
    //             'contents.link as content_link',
    //             'contents.enrollment_price as content_enrollment_price',
    //             'contents.place as content_place',
    //             'contents.participant_limit as content_participant_limit',
    //             'content_types.type as content_type',
    //             'organization.name as organization_name',
    //             'users.name as user_name',
    //             'contents.user_id as user_id'
    //         )
    //         ->first();

    //     if (!$content_data) {
    //         return response()->json(['error' => 'Content not found or not approved.'], 400);
    //     }

    //     // Insert ke table smart_contract dengan status_contract = 2 (pending)
    //     $smartContractId = DB::table('smart_contract')->insertGetId([
    //         'content_id' => $id,
    //         'user_id' => $content_data->user_id,
    //         'provider' => 'xBug', // Sesuaikan jika perlu
    //         'tx_hash' => null,
    //         'block_no' => null,
    //         'address' => env('ETH_ADRRESS'),
    //         'tx_id' => null,
    //         'contract_address' => env('CONTRACT_ADDRESS'), // Sesuaikan jika perlu
    //         'status_contract' => 2, // 2 = Pending
    //         'contract_verified_at' => null,
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);

    //     // Siapkan data untuk dikirim ke Express.js
    //     $deploymentData = [
    //         'content_id' => $id,
    //         'content_name' => $content_data->content_name,
    //         'content_created_at' => $content_data->content_created_at,
    //         'content_link' => $content_data->content_link,
    //         'content_enrollment_price' => $content_data->content_enrollment_price,
    //         'content_place' => $content_data->content_place,
    //         'content_participant_limit' => $content_data->content_participant_limit,
    //         'content_type' => $content_data->content_type,
    //         'provider' => 'xBug',
    //         'organization_name' => $content_data->organization_name,
    //         'user_name' => $content_data->user_name,
    //         'user_id' => $content_data->user_id,
    //     ];

    //     $user = DB::table('users')->select([ 'is_smart_contract_status', 'is_gpt'])->where('id', Auth::id())->first();
    //     if ($user->is_gpt === 0) {
    //         DB::table('smart_contract_logs')->insert([
    //             'smart_contract_id' => $smartContractId,
    //             'log_message' => '[ERROR] This feature is available exclusively for premium account holders. Please upgrade to a premium account to access this functionality. For assistance or more details, contact us at [help-center@xbug.online]',
    //             'created_at' => now(),
    //         ]);
    //         DB::table('smart_contract')->where('id', $smartContractId)->update([
    //             'status_contract' => 0,
    //             'updated_at' => now(),
    //         ]);
    //         return response()->json(['error' => '[ERROR] This feature is available exclusively for premium account holders. Please upgrade to a premium account to access this functionality. For assistance or more details, contact us at [help-center@xbug.online]'], 401);
    //     }
    //     if ($user->is_smart_contract_status === 0) {
    //         DB::table('smart_contract_logs')->insert([
    //             'smart_contract_id' => $smartContractId,
    //             'log_message' => '[ERROR] Access to xBug Smart Contract has been restricted for your account. For further assistance or inquiries, please contact us at [help-center@xbug.online]',
    //             'created_at' => now(),
    //         ]);
    //         DB::table('smart_contract')->where('id', $smartContractId)->update([
    //             'status_contract' => 0,
    //             'updated_at' => now(),
    //         ]);
    //         return response()->json(['error' => '[ERROR] Access to xBug Smart Contract has been restricted for your account. For further assistance or inquiries, please contact us at [help-center@xbug.online]'], 401);
    //     }
    //     // Dispatch job untuk deploy smart contract
    //     DeploySmartContractJob::dispatch($deploymentData, $smartContractId);

    //     // Kembalikan respons JSON untuk AJAX
    //     return response()->json(['message' => 'Smart contract deployment initiated.'], 200);
    // }

    public function getLogs($id)
    {
        sleep(4);
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
