

@extends('layouts.app')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2">Item Type</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group mr-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#itemTypeFormModal">
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
            <table id="item_type_table_id" class="table table-striped table-sm">
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
        
        <!-- Modal for Create Item Type Form -->
        <div class="modal fade" id="itemTypeFormModal" tabindex="-1" aria-labelledby="itemTypeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addItemTypeModalLabel">Add New Item Type</h5>
                        <h5 class="modal-title" id="updateItemTypeModalLabel" style="display:none">Update Item Type</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul id="errorItemType"></ul>
                        </div>
                        <div class="form-group">
                            <label for="itemType" class="col-form-label">Type <sub class="required-symbol">*</sub> : </label>
                            <input type="text" class="form-control" id="itemType" >
                        </div>
                        <div class="form-group">
                            <label for="itemTypeDescription" class="col-form-label">Description <sub class="required-symbol">*</sub> :</label>
                            <textarea class="form-control" id="itemTypeDescription"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                        <button type="submit" id="addNewitemType" class="btn btn-primary btn-add-new">Add Item Type</button>
                        <button type="submit" id="updateitemType" class="btn btn-primary btn-update" style="display:none">Update Item Type</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
@endsection

@section('footer')
    <script>

        var update_item_type_id = '';
        // Select active tab      
        var url_path = window.location.pathname;
        if(url_path == '/item-type'){
            var element = document.getElementById('itemTypeNav');
            element.classList.add('active');
        }

        $('#itemTypeFormModal').on('hidden.bs.modal', function () {
            $('#addNewitemType').show();
            $('#updateitemType').hide();
            $('#addItemTypeModalLabel').show();
            $('#updateItemTypeModalLabel').hide();        
            $('#itemType').val('');
            $('#itemTypeDescription').val('');

            var ul = document.getElementById("errorItemType");
            ul.innerHTML = "";
            $(".print-error-msg").css('display','none');

        });

        // Load Datatable
        $(document).ready( function () {
            var t = $('#item_type_table_id').DataTable( {  
                ajax: {
                    url: '/get-item-types',
                    dataSrc: 'item_types'
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
                        'defaultContent': '<button class="btn btn-sm btn-outline-primary edit-item-type"><span data-feather="plus-circle"></span>Edit</button>  <button class="btn btn-sm btn-outline-danger delete-item-type"><span data-feather="plus-circle"></span>Delete</button>'
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

            // edit item type
            $(document).on('click', '.edit-item-type', function(){
                var data = t.row($(this).parents('tr')).data();

                $.ajax({
                    url: "/get-item-type",
                    type:"GET",
                    data:{
                        "_token": "{{ csrf_token() }}",
                        id:data.id,
                    },
                    success:function(response){
                        $('#itemType').val(response.item_type.type);
                        $('#itemTypeDescription').val(response.item_type.description);
                        
                        update_item_type_id = response.item_type.id;

                        $('#addNewitemType').hide();
                        $('#updateitemType').show();

                        $('#addItemTypeModalLabel').hide();
                        $('#updateItemTypeModalLabel').show();        
                        
                        $('#itemTypeFormModal').modal('toggle');
                    },
                    error: function(response) {
                        
                    },
                });
            });        

            // delete item type
            $(document).on('click', '.delete-item-type', function(){
                
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
                            url: "/delete-item-type",
                            type:"DELETE",
                            data:{
                                "_token": "{{ csrf_token() }}",
                                id:data.id,
                            },
                            success:function(response){
                                $('#item_type_table_id').DataTable().ajax.reload();
                            },
                            error: function(response) {
                            },
                        });
                    }
                });
            });   
        } );

        // add item type
        $('#addNewitemType').on('click',function(e){
            
            e.preventDefault();            

            let type = $('#itemType').val();
            let description = $('#itemTypeDescription').val();
            
            $.ajax({
                url: "/item-type",
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    type:type,
                    description:description,
                },
                success:function(response){
                    $('#item_type_table_id').DataTable().ajax.reload();
                    $('#itemTypeFormModal').modal('toggle');
                },
                error: function(response) {
                    var myProp = 'errors';
                    
                    if(response.responseJSON.hasOwnProperty(myProp)){
                        printErrorMsg(response.responseJSON.errors);
                    }
                },
            });
        });

        // update item type
        $('#updateitemType').on('click',function(e){
            e.preventDefault();

            let type = $('#itemType').val();
            let description = $('#itemTypeDescription').val();
            
            $.ajax({
                url: "/update-item-type",
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    type:type,
                    description:description,
                    id: update_item_type_id
                },
                success:function(response){
                    $('#item_type_table_id').DataTable().ajax.reload();
                    $('#itemTypeFormModal').modal('toggle');
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