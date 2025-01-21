@extends('organization.layouts.main')

@section('container')
    {{-- Pastikan memanggil Vite untuk compile JS --}}
    @vite(['resources/js/indexBlockchain.js', 'resources/css/app.css'])

    <div class="main-content app-content">
        <div class="container">

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <h1 class="page-title fw-semibold fs-18 mb-0">Smart Contract Blockchain</h1>
                ...
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-header d-flex flex-column flex-sm-row align-items-center justify-content-between">
                            <div class="card-title mb-2 mb-sm-0">
                                List Transaction Smart Contract Content
                            </div>

                            <!-- Tombol Connect Wallet -->
                            <div class="d-flex flex-wrap align-items-center">
                                <appkit-network-button class="btn btn-dark btn-sm me-2 mb-2"></appkit-network-button>
                                <appkit-button class="btn btn-dark btn-sm me-2 mb-2"></appkit-button>
                            </div>
                        </div>

                        <div class="card-body">
                            <button id="add-content-btn" class="btn btn-success">
                                Tambah Content ke Smart Contract
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
