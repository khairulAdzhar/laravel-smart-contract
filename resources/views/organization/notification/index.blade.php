@extends('organization.layouts.main')
@section('container')
    <div class="main-content app-content">
        <div class="container">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <h1 class="page-title fw-semibold fs-18 mb-0">Email Notification</h1>
                <div class="ms-md-1 ms-0">
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Pages</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Email Notification</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Your Email Blockchain Notification Logs</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-nowrap data-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Email Type</th>
                                    <th>Name</th>
                                    <th>Sender Email</th>
                                    <th>Recipient Email</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach ($datas as $data)
        <div class="modal modal-lg fade" id="modalView-{{ $data->id }}" tabindex="-1" aria-labelledby="modalViewLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="modalViewLabel">Email To -
                            {{ strtoupper($data->name) }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4">
                        <div class="row">
                            <div class="col-xl-6 mb-2">
                                <div class="form-floating">
                                    <input type="text" class="form-control" placeholder="Enter Content Name"
                                        value="{{ $data->from_email }}" name="from" disabled>
                                    <label for="contentName">From</label>
                                </div>
                            </div>
                            <div class="col-xl-6 mb-2">
                                <div class="form-floating">
                                    <input type="text" class="form-control" placeholder="Enter Content Name"
                                        value="{{ $data->recipient_email }}" name="to_email" disabled>
                                    <label for="contentName">To</label>
                                </div>
                            </div>
                            <div class="col-xl-12 mb-2 mt-2">
                                <label for="contentName">Message</label>
                                <div class="form-control" style="height:300px; overflow:auto;">{!! $data->response_data !!}</div>
                            </div>


                        </div>
                    </div>
                    <div class="modal-footer ">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        $(document).ready(function() {
            $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('showNotificationOrg') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'email_type',
                        name: 'email_type'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        render: function(data, type, row) {
                            return data ? data.toUpperCase() :
                            ''; 
                        }
                    },
                    {
                        data: 'from_email',
                        name: 'from_email'
                    },
                    {
                        data: 'recipient_email',
                        name: 'recipient_email'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    }
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        text: 'Copy Data'
                    },
                    {
                        extend: 'csv',
                        text: 'Export CSV'
                    },
                    {
                        extend: 'excel',
                        text: 'Export Excel'
                    },
                    {
                        extend: 'pdf',
                        text: 'Export PDF'
                    },
                    {
                        extend: 'print',
                        text: 'Print Data'
                    }
                ],
                language: {
                    emptyTable: "No logs available"
                }
            });
        });
    </script>

    <style>
        .table td {
            word-wrap: break-word;
            white-space: normal;
        }
    </style>
@endsection
