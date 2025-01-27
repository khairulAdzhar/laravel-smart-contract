@extends('organization.layouts.main')
@section('container')
    <!-- Start::app-content -->
    <div class="main-content app-content">
        <div class="container">
            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <h1 class="page-title fw-semibold fs-18 mb-0">xBUG Blockchain Main Dashboard</h1>
                <div class="ms-md-1 ms-0">
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Pages</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Main Dashboard</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- Page Header Close -->
            <!-- Start::row-1 -->
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                    </svg>
                    <div class="ms-3"> {{ session('success') }} </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('errorEkyc'))
                <div class="alert alert-danger alert-dismissible d-flex align-items-center" role="alert">
                    <i class="bi bi-dash-circle-fill fs-4"></i>
                    <div class="ms-3"> {{ session('errorEkyc') }} </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible d-flex align-items-center" role="alert">
                    <i class="bi bi-dash-circle-fill fs-4"></i>
                    <div class="ms-3"> {{ session('error') }} </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (Auth::user()->ekyc_status === 0)
                <div class="row" id="tasks-container">
                    <div class="col-xl-12 task-card">
                        <div class="row justify-content-center">
                            <div class="col-md-12 ">
                                <ul class="list-unstyled mb-0 notification-container">
                                    <li>
                                        <div class="card custom-card un-read">
                                            <div class="card-body p-3">
                                                <a href="javascript:void(0);">
                                                    <div class="d-flex align-items-top mt-0 flex-wrap">
                                                        <div class="row">
                                                            <div class="col-md-1">
                                                                <div
                                                                    class="lh-1 d-flex justify-content-center align-items-center mt-3">
                                                                    <span class="avatar avatar-md online avatar-rounded">
                                                                        <img alt="avatar"
                                                                            src="../../assets/images/user/avatar-1.jpg">
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-11">
                                                                <div class="flex-fill">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="row">
                                                                            <div class="col-md-9">
                                                                                <div class="mt-sm-0 mt-2">
                                                                                    <p class="mb-0 fs-14 fw-semibold">
                                                                                        {{ Auth::user()->name }}</p>
                                                                                    <p class="mb-0 text-muted">Before you
                                                                                        continue, we
                                                                                        require users to complete eKYC
                                                                                        (Electronic Know Your Customer)
                                                                                        verification. This process involves
                                                                                        a
                                                                                        quick and easy upload of your
                                                                                        identification documents and facial
                                                                                        recognition to verify your identity.
                                                                                        This is for ensure a
                                                                                        secure and seamless experience in
                                                                                        our system.
                                                                                        Click  Go To xBUG WEB button to get started ekyc at xBUG WEB Portal
                                                                                        and
                                                                                        enhance your security.</p>
                                                                                    <span
                                                                                        class="mb-0 d-block text-muted fs-12 mt-1"><span
                                                                                            class="badge bg-danger-transparent fw-bold fs-12">Pending...</span></span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="text-end col-md-3">
                                                                                <button 
                                                                                onclick="window.location.href='{{ env('XBUG_URL') }}/organization/dashboard'" 
                                                                                class="btn btn-success">
                                                                                Go To xBUG WEB
                                                                            </button>
                                                                            
                                                                            </div>
                                                                       
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card custom-card">
                                    <div class="card-body p-0">
                                        <div class="row g-0">
                                            <p class="text-primary m-3">Blockchain Smart Contract</p>
                                            <div class="col-xl-3 border-end border-inline-end-dashed">
                                                <div class="d-flex flex-wrap align-items-top p-1 mb-3 ms-4">
                                                    <div class="me-3 lh-1">
                                                        <span class="avatar avatar-md avatar-rounded bg-primary shadow-sm">
                                                            <i class="ti ti-files fs-18"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill mt-1">
                                                        <h5 class="fw-semibold mb-1">{{ $approvedContents }}</h5>
                                                        <p class="text-muted mb-0 fs-12">Can Deploy</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3 border-end border-inline-end-dashed">
                                                <div class="d-flex flex-wrap align-items-top p-1 mb-3 ms-4">
                                                    <div class="me-3 lh-1">
                                                        <span class="avatar avatar-md avatar-rounded bg-light shadow-sm">
                                                            <i class="ti ti-file-check fs-18 text-dark"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill mt-1">
                                                        <h5 class="fw-semibold mb-1">{{ $totalSC }}</h5>
                                                        <p class="text-muted mb-0 fs-12">Requested</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3 border-end border-inline-end-dashed">
                                                <div class="d-flex flex-wrap align-items-top p-1 mb-3 ms-4">
                                                    <div class="me-3 lh-1">
                                                        <span class="avatar avatar-md avatar-rounded bg-success shadow-sm">
                                                            <i class="ti ti-file fs-18"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill mt-1">
                                                        <h5 class="fw-semibold mb-1">{{ $approvedCountSC }}</h5>
                                                        <p class="text-muted mb-0 fs-12">Success Deployed</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3">
                                                <div class="d-flex flex-wrap align-items-top p-1 mb-3 ms-4">
                                                    <div class="me-3 lh-1">
                                                        <span class="avatar avatar-md avatar-rounded bg-danger shadow-sm">
                                                            <i class="ti ti-file-dislike fs-18"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill mt-1">
                                                        <h5 class="fw-semibold mb-1">{{ $rejectedCountSC }}</h5>
                                                        <p class="text-muted mb-0 fs-12">Fail Deployed</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
        </div>
        @endif
        <!--End::row-1 -->
    </div>
    </div>
@endsection
