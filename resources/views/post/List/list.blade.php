@extends('layouts.employer-master')
@section('content')
<!-- Begin emoji-picker Stylesheets -->
<?php
// echo "<pre>";
// print_r($allPosts);
// die;
?>
<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Begin Page Content -->
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-md-3 col-sm-12">
                    <h1 class="h3 mb-0 page-title">MANAGE JOBS</h1>
                </div>
                <div class="offset-md-6 col-md-3 col-sm-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/company/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">manage</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- Page Heading -->

            <!-- <h1 class="dashboard-title">MANAGE JOBS</h1>
            <div class="d-sm-flex align-items-center justify-content-between mb-2"></div> -->

            <!-- HEADER SECTION -->
            <div class="row">
                <div class="col-lg-8 col-md-12">
                    <div class="post-list-row">

                        <!-- <div class="col-lg-8 col-md-12"> -->
                        <!-- top bar of table -->

                        <div class="col-12">
                            <div class="row">
                                <div class="col-12">
                                    @if (\Session::has('success'))
                                    <div class="alert alert-success">
                                        <ul>
                                            <li>{!! \Session::get('success') !!}</li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                                <div class="col">
                                    <div class="form-group has-search">
                                        <span class="fa fa-search form-control-feedback"></span>
                                        <input type="text" class="form-control header-search" id="myInputTextField" placeholder="Search...">
                                    </div>
                                </div>
                                <div class="col">
                                    <a href="/company/post/createfree"> <button class="post-list-post-button">Post a <b>FREE</b> Job!</button></a>
                                </div>
                            </div>
                        </div>
                        <!-- end -->
                        <table id="example" class="display table-post-management">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th></th>
                                    <th>Appliccations</th>
                                    <th>Created & Expired</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allPosts as $item)
                                <tr>
                                    <td><span class="post-list-u-name"><i class="fa fa-user" aria-hidden="true"></i></span></td>
                                    <td>{{$item->title}}</td>
                                    <td>4 Applied</td>
                                    <td>
                                        <?php
                                        $date = strtotime($item->created_at);
                                        ?>
                                        {{ date('d M Y h:i:s', $date)}}
                                    </td>
                                    <td class="action-buttons">
                                        <span>
                                            <a href="/company/post/view/{{$item->id}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        </span>
                                        <span><a href="/company/post/edit/{{$item->id}}"><i class="fa fa-pencil" aria-hidden="true"></i></a></span>
                                        <span><a href="#" onclick="deletePost({{$item->id}})"><i class="fa fa-trash" aria-hidden="true"></i></a></span>
                                    </td>
                                </tr>
                                @endforeach
                                </tfoot>
                        </table>
                        <!-- </div> -->
                        <!-- <div class="col-lg-4 col-md-12"> -->

                        <!-- </div> -->
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="card card-post-menagement">
                        <h5 class="card-title">Manage Applicants</h5>
                        <div class="card-body">
                            Manage your job posting applicants
                        </div>
                        <div style="padding: 8px 8px;    margin-top: 30px;">
                            <a href="#" class="btn btn-primary">Manage Applicants</a>

                        </div>
                    </div>

                    <div class="card card-post-menagement" style="margin-top: 30px;">
                        <div class="card-title">
                            Contact Summary
                        </div>
                        <table class="table">

                            <tbody>
                                <tr>
                                    <td scope="row">Number of posted jobs :
                                    </td>
                                    <td>108</td>

                                </tr>
                                <tr>
                                    <td scope="row">Number of applicants:</td>
                                    <td>157</td>

                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- HEADER  END -->

            <!-- BODY SECTION -->
            <!-- <div class="row"> -->

            <!-- </div> -->
            <!-- BODY END -->
        </div>

        <!-- Modal -->

        <!-- <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        ...
                    </div>
                    <div class="modal-body">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <a class="btn btn-danger btn-ok">Delete</a>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- /.container-fluid -->
    </div>
    <!-- End of Main Content -->

    @endsection
    @section('after_styles')

    <link href="/css/employer_dashboard.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel="stylesheet" />
    <style>
        .dataTables_wrapper .dataTables_paginate {
            text-align: center !important;
            float: none !important;
            font-size: 13px !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 5px 11px !important;
            border-radius: 50% !important;
        }

        .dataTables_wrapper .dataTables_paginate .current {
            background: #556EE6 !important;
            border: 1px solid #556EE6 !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .current:hover {
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #3964e1b8 !important;
            border: 1px solid #556EE6 !important;
        }

        .action-buttons a {
            margin: 5px;
            font-weight: 400;
            color: #495057;
            font-size: 16px;
        }

        .card-post-menagement .card-body {
            box-shadow: none;
        }
    </style>
    @endsection
    @section('after_scripts')
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var oTable = $('#example').DataTable({
                "paging": true,
                "ordering": false,
                "info": false,
                'sDom': 'tp',
                searching: true,
                oLanguage: {
                    oPaginate: {
                        sNext: '<span class="pagination-fa"><i class="fa fa-chevron-right" ></i></span>',
                        sPrevious: '<span class="pagination-fa"><i class="fa fa-chevron-left" ></i></span>'
                    }
                }

            });
            $('#myInputTextField').keyup(function() {
                oTable.search($(this).val()).draw();
            })

        });


        function deletePost(id) {
            var base_url = window.location.origin;
            var retVal = confirm("Are you sure, Want to delete this item?");
            if (retVal == true) {
                console.log('base_url', base_url)
                window.location = `${base_url}/company/post/delete/${id}`
                return true;
            } else {
                return false;
            }
        }
    </script>
    @endsection