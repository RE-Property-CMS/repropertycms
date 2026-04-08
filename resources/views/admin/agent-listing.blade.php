@extends('admin.layouts.default')
@section('content')
    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <h5 class="mb-0">Agents Listing</h5>
    </div>
    <div class="table-responsive">
        <table class="table w-full table-striped table-auto">
            <thead>
            <tr>
                <th>Agent Name</th>
                <th>Agent Email</th>
                <th>SignUp Date</th>
                <th class="text-center">Status</th>
                <th class="text-center">Reset Password</th>
                <th class="text-center">Actions</th>
            </tr>
            </thead>
            @if (count($agents) > 0)
                @foreach($agents as $row)
                    <tr id="agent_data{{ $row['id'] }}">

                        <td>{{$row['first_name']}} {{$row['last_name']}}</td>

                        <td>{{$row['email']}}</td>

                        <td>{{ $row->created_at?->format('d M Y') }}</td>

                        <td class="text-center">
                            @if($row['active'] == 1)
                                <a href="#" class="save_status button button-green button-sm" id="{{ $row['id'] }}">ENABLE</a>
                            @else
                                <a href="#" class="save_status button button-green button-sm" id="{{ $row['id'] }}">DISABLE</a>
                            @endif
                        </td>

                        <td class="text-center">
                            <form action="{{url('admin/reset-password/'.$row['id'] )}}" method="get">
                                @csrf
                                <a class="button button-green button-sm reset_password" dialog-trigger="true" data-ripple-light="true" id="{{ $row['id'] }}">
                                    Reset Password
                                </a>
                                <div class="dialog">
                                    <div class="dialog-overlay" dialog-close="true"></div>
                                    <div class="modal-dialog dialog-box">
                                        <div class="dialog-content">
                                            <div class="dialog-header">
                                                <h6 class="mb-0">Reset Agent Password</h6>
                                                <button type="button" class="me-0 button-close" dialog-close="true" aria-label="Close">
                                                    <i class="material-icons">X</i>
                                                </button>
                                            </div>
                                            <div class="dialog-body">
                                                <div class="input-group-outline input-group my-3">
                                                    <span>New Password : </span>
                                                    <input type="password" name="newpassword" id="newpassword" maxlength="75" class="form-control" style="border:2px solid lightgrey;"/>
                                                </div>
                                                <div class="input-group-outline input-group my-3">
                                                    <span>Confirm Password : </span>
                                                    <input type="password" name="confirm_password" id="confirm_password" maxlength="75" class="form-control" style="border:2px solid lightgrey;"/>
                                                </div>
                                            </div>
                                            <div class="dialog-footer">
                                                <a class="button button-green button-sm mr-3" dialog-close="true">Close</a>
                                                <button class="button button-green button-sm">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </td>

                        <td class="text-center">
                            <a href="#" onclick="deleteAgent('{{ $row['id'] }}')" class="button button-red button-sm" >
                                Delete
                            </a>
                        </td>

                    </tr>
                @endforeach
            @endif
        </table>
    </div>
@stop