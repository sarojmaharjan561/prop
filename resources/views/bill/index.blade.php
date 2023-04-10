

@extends('layouts.app')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2">Bill</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group mr-2">
                    <a id="billNav" class="btn btn-sm btn-outline-secondary" href="/add-bill">
                        <span data-feather="plus-circle"></span>
                        Add
                    </a>
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
            <table id="bill_table_id" class="table table-striped table-sm">
                <thead>
                    <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Shop Name</th>
                    <th>Sub Total</th>
                    <th>Discount</th>
                    <th>Total Amount</th>
                    <th>Action</th>
                    </tr>
                </thead>
                <tbody>                   
                </tbody>
            </table>
        </div>
        
        <!-- Modal for Create Item Type Form -->
        <!-- <div class="modal fade" id="itemFormModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addItemModalLabel">Add New Item</h5>
                        <h5 class="modal-title" id="updateItemModalLabel" style="display:none">Update Item</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul id="errorItem"></ul>
                        </div>
                        <div class="form-group">
                            <label for="type" class="col-form-label">Type <sub class="required-symbol">*</sub> : </label>
                            <select id="type" class="selectpicker form-control" data-live-search="type" required>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-form-label">Name <sub class="required-symbol">*</sub> : </label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="form-group">
                            <label for="lastPrice" class="col-form-label">Last Price :</label>
                            <input type="text" class="form-control" id="lastPrice" required>
                        </div>
                        <div class="form-group">
                            <label for="itemDescription" class="col-form-label">Description :</label>
                            <textarea class="form-control" id="itemDescription" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                        <button type="submit" id="addNewitem" class="btn btn-primary btn-add-new">Add Item</button>
                        <button type="submit" id="updateitem" class="btn btn-primary btn-update" style="display:none">Update Item</button>
                    </div>
                </div>
            </div>
        </div> -->
    </main>
    
@endsection

@section('footer')
    <script>

        var update_bill_id = '';
        // Select active tab      
        var url_path = window.location.pathname;
        if(url_path == '/bill'){
            var element = document.getElementById('billNav');
            element.classList.add('active');
        }

        // Modal on hide event
        $('#itemFormModal').on('hidden.bs.modal', function () {
            $('#addNewitem').show();
            $('#updateitem').hide();
            $('#addItemModalLabel').show();
            $('#updateItemModalLabel').hide();        
            $('#item').val('');
            $('#itemDescription').val('');

            var ul = document.getElementById("errorItem");
            ul.innerHTML = "";
            $(".print-error-msg").css('display','none');

        });

        // Modal on show event
        $('#itemFormModal').on('shown.bs.modal', function () {
            $.ajax({
                url : "/get-item-types",
                type:'GET',
                dataType: 'json',
                success: function(response) {
                    // $("#breeds").attr('disabled', false);
                    $.each(response.item_types,function(key, value)
                    {
                        $("#type").append('<option value=' + value['id'] + '>' + value['type'] + '</option>');
                    });
                    $('.selectpicker').selectpicker('refresh');
                }
            });

            $('#type').trigger('focus')
            
        })

        // Load Datatable
        $(document).ready( function () {
            var t = $('#bill_table_id').DataTable( {  
                ajax: {
                    url: '/get-bills',
                    dataSrc: 'bills'
                },
                columns: [ 
                    { data: 'created_at',},
                    { data: 'date' },
                    { data: 'shop_name' },
                    { data: 'sub_total' },
                    { data: 'discount' },
                    { data: 'total_amount' },
                ],
                columnDefs: [
                    { 
                        'targets': 6, 
                        "data": null, 
                        'defaultContent': '<button class="btn btn-sm btn-outline-primary edit-bill"><span data-feather="plus-circle"></span>Edit</button>  <button class="btn btn-sm btn-outline-danger delete-bill"><span data-feather="plus-circle"></span>Delete</button>'
                    },
                ],
                order: [[1, 'asc']],
                
            } );
            
            t.on('order.dt search.dt', function () {
                let i = 1;
        
                t.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
                    this.data(i++);
                });
            }).draw();

            // edit bill
            $(document).on('click', '.edit-bill', function(){
                var data = t.row($(this).parents('tr')).data();
                console.log(data);
                window.location = "/add-bill/"+data.id;
                // $.ajax({
                //     url: "/get-bill",
                //     type:"GET",
                //     data:{
                //         "_token": "{{ csrf_token() }}",
                //         id:data.id,
                //     },
                //     success:function(response){
                //         $('#name').val(response.item.name);
                //         $('#type').val(response.item.type_id);
                //         $('#lastPrice').val(response.item.last_price);
                //         $('#itemDescription').val(response.item.description);

                        
                //         update_bill_id = response.item.id;
                        
                //         $('#addNewitem').hide();
                //         $('#updateitem').show();

                //         $('#addItemModalLabel').hide();
                //         $('#updateItemModalLabel').show();        
                        
                //         $('#itemFormModal').modal('toggle');
                //     },
                //     error: function(response) {
                        
                //     },
                //     complete: function() {
                //         setTimeout(() => {
                //             $('.selectpicker').selectpicker('refresh');
                //             console.log('here');                    
                //         }, 550);
                        
                //     }
                // });
            });        

            // delete bill
            $(document).on('click', '.delete-bill', function(){
                
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
                            url: "/delete-bill",
                            type:"DELETE",
                            data:{
                                "_token": "{{ csrf_token() }}",
                                id:data.id,
                            },
                            success:function(response){
                                $('#bill_table_id').DataTable().ajax.reload();
                            },
                            error: function(response) {
                            },
                        });
                    }
                });
            });   
        } );

        // add item
        // $('#addNewitem').on('click',function(e){
            
        //     e.preventDefault();            

        //     let name = $('#name').val();
        //     let type = $('#type').val();
        //     let last_price = $('#lastPrice').val();
        //     let description = $('#itemDescription').val();
            
        //     $.ajax({
        //         url: "/item",
        //         type:"POST",
        //         data:{
        //             "_token": "{{ csrf_token() }}",
        //             name:name,
        //             type_id:type,
        //             last_price:last_price,
        //             description:description,
        //         },
        //         success:function(response){
        //             $('#bill_table_id').DataTable().ajax.reload();
        //             $('#itemFormModal').modal('toggle');
        //         },
        //         error: function(response) {
        //             var myProp = 'errors';
                    
        //             if(response.responseJSON.hasOwnProperty(myProp)){
        //                 printErrorMsg(response.responseJSON.errors);
        //             }
        //         },
        //     });
        // });

        // update item
        // $('#updateitem').on('click',function(e){
        //     e.preventDefault();

        //     let name = $('#name').val();
        //     let type = $('#type').val();
        //     let last_price = $('#lastPrice').val();
        //     let description = $('#itemDescription').val();
            
        //     $.ajax({
        //         url: "/update-item",
        //         type:"POST",
        //         data:{
        //             "_token": "{{ csrf_token() }}",
        //             name:name,
        //             type_id:type,
        //             last_price:last_price,
        //             description:description,
        //             id: update_bill_id
        //         },
        //         success:function(response){
        //             $('#bill_table_id').DataTable().ajax.reload();
        //             $('#itemFormModal').modal('toggle');
        //         },
        //         error: function(response) {
        //             var myProp = 'errors';
                    
        //             if(response.responseJSON.hasOwnProperty(myProp)){
        //                 printErrorMsg(response.responseJSON.errors);
        //             }
        //             // $('#nameErrorMsg').text(response.responseJSON.errors.name);
        //             // $('#descriptionErrorMsg').text(response.responseJSON.errors.description);
        //         },
        //     });
        // });


        function printErrorMsg (msg) {
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").css('display','block');
            $.each( msg, function( key, value ) {
                $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
            });
        }

    </script>
    
@endsection