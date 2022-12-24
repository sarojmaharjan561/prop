

@extends('layouts.app')

<!-- @section('title', 'Dashboard') -->

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2">Bill Type</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group mr-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#billTypeFormModal">
                        <span data-feather="plus-circle"></span>
                        Add
                    </button>
                </div>
                <div class="btn-group mr-2">
                    <button class="btn btn-sm btn-outline-secondary" disabled>Share</button>
                    <button class="btn btn-sm btn-outline-secondary" disabled>Export</button>
                </div>
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" disabled>
                    <span data-feather="calendar"></span>
                    This week
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="bill_type_table_id" class="table table-striped table-sm">
                <thead>
                    <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Action</th>
                    </tr>
                </thead>
                <tbody>                   
                </tbody>
            </table>
        </div>
        
        <!-- Modal for Create Bill Type Form -->
        <div class="modal fade" id="billTypeFormModal" tabindex="-1" aria-labelledby="billTypeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addBillTypeModalLabel">Add New Bill Type</h5>
                        <h5 class="modal-title" id="updateBillTypeModalLabel" style="display:none">Update Bill Type</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul id="errorBillType"></ul>
                        </div>
                        <div class="form-group">
                            <label for="billType" class="col-form-label">Type <sub class="required-symbol">*</sub> : </label>
                            <input type="text" class="form-control" id="billType" >
                        </div>
                        <div class="form-group">
                            <label for="billTypeDescription" class="col-form-label">Description <sub class="required-symbol">*</sub> :</label>
                            <textarea class="form-control" id="billTypeDescription"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                        <button type="submit" id="addNewBillType" class="btn btn-primary btn-add-new">Add Bill Type</button>
                        <button type="submit" id="updateBillType" class="btn btn-primary btn-update" style="display:none">Update Bill Type</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
@endsection

@section('footer')
    <script>

        var update_bill_type_id = '';
        // Select active tab      
        var url_path = window.location.pathname;
        if(url_path == '/bill-type'){
            var element = document.getElementById('billTypeNav');
            element.classList.add('active');
        }

        $('#billTypeFormModal').on('hidden.bs.modal', function () {
            $('#addNewBillType').show();
            $('#updateBillType').hide();
            $('#addBillTypeModalLabel').show();
            $('#updateBillTypeModalLabel').hide();        
            $('#billType').val('');
            $('#billTypeDescription').val('');

            var ul = document.getElementById("errorBillType");
            ul.innerHTML = "";
            $(".print-error-msg").css('display','none');

        });

        // Load Datatable
        $(document).ready( function () {
            var t = $('#bill_type_table_id').DataTable( {  
                ajax: {
                    url: '/get-bill-types',
                    dataSrc: 'bill_types'
                },
                columns: [ 
                    { data: 'created_at',},
                    { data: 'type' },
                    { data: 'description' },
                ],
                columnDefs: [
                    { 
                        'targets': 3, 
                        "data": null, 
                        'defaultContent': '<button class="btn btn-sm btn-outline-primary edit-bill-type"><span data-feather="plus-circle"></span>Edit</button>  <button class="btn btn-sm btn-outline-danger delete-bill-type"><span data-feather="plus-circle"></span>Delete</button>'
                    },
                ],
                order: [[3, 'asc']],
                
            } );
            
            t.on('order.dt search.dt', function () {
                let i = 1;
        
                t.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
                    this.data(i++);
                });
            }).draw();

            // edit bill type
            $(document).on('click', '.edit-bill-type', function(){
                var data = t.row($(this).parents('tr')).data();

                $.ajax({
                    url: "/get-bill-type",
                    type:"GET",
                    data:{
                        "_token": "{{ csrf_token() }}",
                        id:data.id,
                    },
                    success:function(response){
                        $('#billType').val(response.bill_type.type);
                        $('#billTypeDescription').val(response.bill_type.description);
                        
                        update_bill_type_id = response.bill_type.id;

                        $('#addNewBillType').hide();
                        $('#updateBillType').show();

                        $('#addBillTypeModalLabel').hide();
                        $('#updateBillTypeModalLabel').show();        
                        
                        $('#billTypeFormModal').modal('toggle');
                    },
                    error: function(response) {
                        
                    },
                });
            });        

            // delete bill type
            $(document).on('click', '.delete-bill-type', function(){
                
                Swal.fire({
                    icon: 'warning',
                    text: 'Do you want to delete this?',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    confirmButtonColor: '#e3342f',
                }).then((result) => {
                    if (result.isConfirmed) {
                        var data = t.row($(this).parents('tr')).data();
        
                        $.ajax({
                            url: "/delete-bill-type",
                            type:"DELETE",
                            data:{
                                "_token": "{{ csrf_token() }}",
                                id:data.id,
                            },
                            success:function(response){
                                $('#bill_type_table_id').DataTable().ajax.reload();
                            },
                            error: function(response) {
                            },
                        });
                    }
                });
            });   
        } );

        // add bill type
        $('#addNewBillType').on('click',function(e){
            
            e.preventDefault();            

            let type = $('#billType').val();
            let description = $('#billTypeDescription').val();
            
            $.ajax({
                url: "/bill-type",
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    type:type,
                    description:description,
                },
                success:function(response){
                    $('#bill_type_table_id').DataTable().ajax.reload();
                    $('#billTypeFormModal').modal('toggle');
                },
                error: function(response) {
                    var myProp = 'errors';
                    
                    if(response.responseJSON.hasOwnProperty(myProp)){
                        printErrorMsg(response.responseJSON.errors);
                    }
                },
            });
        });

        // update bill type
        $('#updateBillType').on('click',function(e){
            e.preventDefault();

            let type = $('#billType').val();
            let description = $('#billTypeDescription').val();
            
            $.ajax({
                url: "/update-bill-type",
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    type:type,
                    description:description,
                    id: update_bill_type_id
                },
                success:function(response){
                    $('#bill_type_table_id').DataTable().ajax.reload();
                    $('#billTypeFormModal').modal('toggle');
                },
                error: function(response) {
                    var myProp = 'errors';
                    
                    if(response.responseJSON.hasOwnProperty(myProp)){
                        printErrorMsg(response.responseJSON.errors);
                    }
                    // $('#nameErrorMsg').text(response.responseJSON.errors.name);
                    // $('#descriptionErrorMsg').text(response.responseJSON.errors.description);
                },
            });
        });


        function printErrorMsg (msg) {
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").css('display','block');
            $.each( msg, function( key, value ) {
                $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
            });
        }

    </script>
    
@endsection