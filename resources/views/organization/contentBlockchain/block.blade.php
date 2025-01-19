<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="color" data-header-styles="light"
    data-menu-styles="color" data-toggled="close" style="--primary-rgb: 17,28,67;">

<head>
    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=no'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>xBug</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ url('assets/images/logo.ico') }}" type="image/x-icon">
    <!-- Choices JS -->
    <script src="{{ url('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <!-- Main Theme Js -->
    <script src="{{ url('assets/js/main.js') }}"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap Css -->
    <link id="style" href="{{ url('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Style Css -->
    <link href="{{ url('assets/css/styles.min.css') }}" rel="stylesheet">
    <!-- Icons Css -->
    <link href="{{ url('assets/css/icons.css') }}" rel="stylesheet">

    <style>
        body {
            background-color: #ffffff;
            color: rgb(17, 28, 67);
        }

        /* Bagian "loading" 5 detik pertama (teks info) */
        #initialInfoSection {
            margin-top: 3rem;
        }

        .initial-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #111C43;
        }

        .initial-subtitle {
            font-size: 1rem;
            font-weight: 400;
        }

        /* Bagian hasil validasi (error / countdown) */
        #validationResultSection {
            display: none;
            /* Tersembunyi di awal */
            margin-top: 3rem;
        }

        /* .error-message {
            color: #B02A37;
            font-weight: 600;
            margin-top: 1rem;
        } */

        .countdown-container {
            margin-top: 1rem;
            text-align: center;
        }

        .countdown {
            font-size: 2rem;
            font-weight: 700;
            color: #111C43;
        }

        .btn-go {
            background-color: #111C43;
            color: #ffffff;
            border: none;
            font-weight: 600;
            transition: 0.3s;
            margin-top: 1rem;
        }

        .btn-go:hover {
            background-color: #0d1430;
        }

        .btn-back {
            margin-top: 1rem;
        }
    </style>
    <style>
        .dots span {
            display: inline-block;
            animation: bounce 0.8s infinite ease-in-out;
        }

        .dots span:nth-child(1) {
            animation-delay: 0s;
        }

        .dots span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .dots span:nth-child(3) {
            animation-delay: 0.4s;
        }

        .dots span:nth-child(4) {
            animation-delay: 0.6s;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
                /* Posisi awal dan akhir */
            }

            50% {
                transform: translateY(-10px);
                /* Gerakan ke atas */
            }
        }
    </style>
</head>

<body class="bg-white">
    <!-- Start Switcher -->
    @include('organization.layouts.switcher')
    <!-- End Switcher -->

    <div class="page">
        <div class="landing-banner" id="home">
            <section class="section text-dark">
                <div class="container main-banner-container pb-lg-0">
                    <div class="row">
                        <div class="col-xxl-7 col-xl-7 col-lg-7 col-md-8">
                            <div class="p-0 ">
                                <!-- TAHAP PERTAMA (5 detik) -->
                                <div id="initialInfoSection" class="mb-4">
                                    <h1 class="initial-title mb-0">xBug Blockchain Gateway: Connecting<span
                                        class="dots">
                                        <span>.</span>
                                        <span>.</span>
                                        <span>.</span>
                                        <span>.</span>
                                    </span></h1>
                                    <div class="row align-items-center">
                                        <!-- Kolom untuk teks -->
                                        <div class="col-md-10">
                                            <p class="initial-subtitle mt-3 mb-0">
                                                Connecting you to xBug's secure blockchain infrastructure. Please wait
                                                while
                                                we validate your account credentials and access rights for the smart
                                                contract system.
                                            </p>
                                        </div>
                                        <!-- Kolom untuk spinner -->
                                        {{-- <div class="col-md-2 text-center">
                                            <div class="spinner-border text-primary mt-3" role="status"
                                                style="width: 1.8rem; height: 1.8rem;">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div> --}}
                                    </div>
                                    <p class="initial-subtitle mt-3">
                                        While you wait, ensure your account is verified and meets the required criteria
                                        for smart contract deployment. If you encounter any issues, please contact our
                                        support team at
                                        <a href="mailto:help-center@xbug.online">help-center@xbug.online</a>.
                                    </p>
                                </div>



                                <!-- TAHAP KEDUA (setelah 5 detik) -->
                                <div id="validationResultSection">
                                    <!-- Periksa data dari Controller -->

                                    @if ($errorMessage)
                                        <h1 class="initial-title mb-2">xBug Blockchain Gateway: <span
                                                class="text-danger">Access Denied</span></h1>
                                        <!-- Validasi gagal -->
                                        <div class="initial-subtitle alert error-message">
                                            {{ $errorMessage }}
                                        </div>
                                        <!-- Tombol Back -->
                                        <a href="{{ env('XBUG_URL') }}/organization/dashboard"
                                            class="btn btn-primary btn-back w-100 py-3">
                                            Back to Dashboard
                                        </a>
                                    @elseif ($redirect)
                                        <!-- Validasi lolos -> Countdown -->
                                        <h1 class="initial-title mb-2">xBug Blockchain Gateway: <span
                                                class="text-success">Access Granted</span></h1>
                                        <div class="initial-subtitle ">
                                            <p class="mb-0">
                                            <p class="mt-3">
                                                Your access to the xBug Blockchain App ecosystem has been successfully
                                                verified, you will redirect to the xBug Blockchain App in
                                            </p>
                                            </p>
                                            <span id="countdown" class="countdown">5</span>
                                            <span>seconds</span>
                                            <br>
                                        </div>
                                    @else
                                        <!-- Fallback jika tak ada data sama sekali -->
                                        <div class="alert alert-info">
                                            No validation data found.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-5 col-xl-5 col-lg-5 col-md-4 mt-2">
                            <div class="col-xxl-5 col-xl-5 col-lg-5 col-md-4 mt-2 w-100">
                                <div class="text-end">
                                    <img id="animated-image" src="/assets/images/landing-page/redirect-2.png"
                                        alt="redirect illustration" class="img-fluid w-100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="{{ url('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        // Ambil data dari Blade
        const hasErrorMessage = @json($errorMessage) ? true : false;
        const isRedirect = @json($redirect);
        const xBugBlockchainUrl = @json($xBugBlockchainUrl);

        // Fungsi untuk menampilkan hasil validasi setelah 5 detik
        window.addEventListener("load", function() {
            // Tunda 5 detik sebelum menampilkan section hasil validasi
            setTimeout(() => {
                document.getElementById("initialInfoSection").style.display = "none";
                document.getElementById("validationResultSection").style.display = "block";

                // Jika validasi lolos, jalankan countdown
                if (isRedirect) {
                    startCountdown();
                }
            }, 5000);
        });

        // Countdown 5 detik jika isRedirect true
        function startCountdown() {
            let countdownNumber = 5;
            const countdownElement = document.getElementById("countdown");
            const redirectNowBtn = document.getElementById("redirectNowBtn");

            const countdownTimer = setInterval(() => {
                countdownNumber--;
                countdownElement.textContent = countdownNumber;

                if (countdownNumber <= 0) {
                    clearInterval(countdownTimer);
                    if (xBugBlockchainUrl) {
                        window.location.href = xBugBlockchainUrl;
                    }
                }
            }, 1000);

        }
    </script>
</body>

</html>
