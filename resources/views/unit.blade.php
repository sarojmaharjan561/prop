

@extends('layouts.app')

<!-- @section('title', 'Dashboard') -->

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2">Unit</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group mr-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#unitFormModal">
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
            <table id="table_id" class="table table-striped table-sm">
                <thead>
                    <tr>
                    <th>#</th>
                    <th>Unit</th>
                    <th>Description</th>
                    <th>Action</th>
                    </tr>
                </thead>
                <tbody>                   
                </tbody>
            </table>
        </div>
        
        <!-- Modal for Create Unit Form -->
        <div class="modal fade" id="unitFormModal" tabindex="-1" aria-labelledby="unitModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUnitModalLabel">Add New Unit</h5>
                        <h5 class="modal-title" id="updateUnitModalLabel" style="display:none">Update Unit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul id="errorUl"></ul>
                        </div>
                        <div class="form-group">
                            <label for="unitName" class="col-form-label">Unit <sub class="required-symbol">*</sub> : </label>
                            <input type="text" class="form-control" id="unitName" >
                        </div>
                        <div class="form-group">
                            <label for="unitDescription" class="col-form-label">Description <sub class="required-symbol">*</sub> :</label>
                            <textarea class="form-control" id="unitDescription"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                        <button type="submit" id="addNewUnit" class="btn btn-primary btn-add-new">Add Unit</button>
                        <button type="submit" id="updateUnit" class="btn btn-primary btn-update" style="display:none">Update Unit</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
@endsection

@section('footer')
    <script>

        var update_unit_id = '';
        // Select active tab      
        var url_path = window.location.pathname;
        if(url_path == '/unit'){
            var element = document.getElementById('unit');
            element.classList.add('active');
        }

        $('#unitFormModal').on('hidden.bs.modal', function () {
            $('#addNewUnit').show();
            $('#updateUnit').hide();
            $('#addUnitModalLabel').show();
            $('#updateUnitModalLabel').hide();        
            $('#unitName').val('');
            $('#unitDescription').val('');

            var ul = document.getElementById("errorUl");
            ul.innerHTML = "";
            $(".print-error-msg").css('display','none');

        });

        // Load Datatable
        $(document).ready( function () {
            var t = $('#table_id').DataTable( {
                // dom: 'Bfrtip',   
                ajax: {
                    url: '/get-units',
                    dataSrc: 'units'
                },
                columns: [ 
                    { data: 'created_at',},
                    { data: 'name' },
                    { data: 'description' },
                    // { data: 'id' },
                ],
                columnDefs: [
                    { 
                        'targets': 3, 
                        "data": null, 
                        'defaultContent': '<button class="btn btn-sm btn-outline-primary edit-unit"><span data-feather="plus-circle"></span>Edit</button>  <button class="btn btn-sm btn-outline-danger delete-unit"><span data-feather="plus-circle"></span>Delete</button>'
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

            // edit unit
            $(document).on('click', '.edit-unit', function(){
                var data = t.row($(this).parents('tr')).data();

                $.ajax({
                    url: "/get-unit",
                    type:"GET",
                    data:{
                        "_token": "{{ csrf_token() }}",
                        id:data.id,
                    },
                    success:function(response){
                        $('#unitName').val(response.unit.name);
                        $('#unitDescription').val(response.unit.description);
                        update_unit_id = response.unit.id;

                        $('#addNewUnit').hide();
                        $('#updateUnit').show();

                        $('#addUnitModalLabel').hide();
                        $('#updateUnitModalLabel').show();        
                        
                        $('#unitFormModal').modal('toggle');
                    },
                    error: function(response) {
                    },
                });
            });        

            // delete unit
            $(document).on('click', '.delete-unit', function(){
                
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
                            url: "/delete-unit",
                            type:"DELETE",
                            data:{
                                "_token": "{{ csrf_token() }}",
                                id:data.id,
                            },
                            success:function(response){
                                $('#table_id').DataTable().ajax.reload();
                            },
                            error: function(response) {
                            },
                        });
                    }
                });
            });   
        } );

        // add unit
        $('#addNewUnit').on('click',function(e){
            
            e.preventDefault();            

            let name = $('#unitName').val();
            let description = $('#unitDescription').val();
            
            $.ajax({
                url: "/unit",
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    name:name,
                    description:description,
                },
                success:function(response){
                    $('#table_id').DataTable().ajax.reload();
                    $('#unitFormModal').modal('toggle');
                    console.log('success',response);
                },
                error: function(response) {
                    var myProp = 'errors';
                    
                    if(response.responseJSON.hasOwnProperty(myProp)){
                        printErrorMsg(response.responseJSON.errors);
                    }
                },
            });
        });

        // update unit
        $('#updateUnit').on('click',function(e){
            e.preventDefault();

            let name = $('#unitName').val();
            let description = $('#unitDescription').val();
            
            $.ajax({
                url: "/update-unit",
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    name:name,
                    description:description,
                    id: update_unit_id
                },
                success:function(response){
                    $('#table_id').DataTable().ajax.reload();
                    $('#unitFormModal').modal('toggle');
                },
                error: function(response) {
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