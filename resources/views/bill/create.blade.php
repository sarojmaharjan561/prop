

@extends('layouts.app')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2">Add Bill</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group mr-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#itemFormModal">
                        <span data-feather="plus-circle"></span>
                        Back
                    </button>
                </div>
                <div class="btn-group mr-2">
                    <button id="saveBillBtn" type="button" style="display: none;" class="btn btn-sm btn-outline-secondary" onclick="saveBill()" >
                        <span data-feather="plus-circle"></span>
                        Save
                    </button>
                    <button id="updateBillBtn" type="button" style="display: none;" class="btn btn-sm btn-outline-secondary" onclick="updateBill()" >
                        <span data-feather="plus-circle"></span>
                        Update
                    </button>
                </div>
            </div>
        </div>        
        
        <!-- Billing Form -->
        <div>
            <div class="form-group row">
                <label for="labelForShopName" class="col-sm-2 col-form-label col-form-label-sm">Shop Name</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control form-control-sm" id="shopName" placeholder="Shop Name">
                </div>
                <label for="labelForBillType" class="col-sm-1 col-form-label col-form-label-sm">Bill Type</label>
                <div class="col-sm-2">
                    <select id="billType" class="selectpicker form-control" data-live-search="item" required>
                        <option disabled selected value> -- select an Bill Type -- </option>
                    </select>
                    <!-- <input type="text" class="form-control form-control-sm" id="billType" placeholder="Bill Type"> -->
                </div>
                <label for="labelForBillDate" class="col-sm-1 col-form-label col-form-label-sm">Bill Date</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control form-control-sm" id="billDate" placeholder="Bill Date">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-4">
                    <select id="item" class="selectpicker form-control" data-live-search="item" required>
                        <option disabled selected value> -- select an Item -- </option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <input id="rate" type="number" class="form-control form-control-sm" id="rate" placeholder="Rate">
                </div>
                <div class="col-sm-2">
                    <input type="number" class="form-control form-control-sm" id="quantity" placeholder="Quantity">
                </div>
                <button id="addItem" class="btn btn-primary mb-2">Add Item</button>
            </div>
            <table id="itemTable" class="table table-striped">
                <thead>
                    <tr>
                    <th scope="col-sm-1">#</th>
                    <th scope="col-sm-4">Item</th>
                    <th scope="col-sm-1">Rate</th>
                    <th scope="col-sm-1">Quantity</th>
                    <th scope="col-sm-3">Amount</th>
                    <th scope="col-sm-2">Action</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <hr class="mb-4">
            <div class="form-group row">
                <div class="col-sm-9"></div>
                <label for="labelForSubTotal" class=" col-sm-1 col-form-label col-form-label-sm">Sub-Total</label>
                <div class="col-sm-2">
                    <input id="subTotal" type="number" class="form-control form-control-sm" disabled>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-9"></div>
                <label for="labelForDiscount" class=" col-sm-1 col-form-label col-form-label-sm">Discount</label>
                <div class="col-sm-2">
                    <input id="discount" type="number" class="form-control form-control-sm" value="0" onchange="grandCalculation()">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-9"></div>
                <label for="labelForGrandTotal" class=" col-sm-1 col-form-label col-form-label-sm">Grand Total</label>
                <div class="col-sm-2">
                    <input id="grandTotal" type="number" class="form-control form-control-sm" disabled>
                </div>
            </div>
        </div>
        
    </main>
    
@endsection

@section('footer')
    <script>

        var update_bill_id = '';

        // Select active tab      
        var url_path = window.location.pathname.split('/');

        
        if(url_path[1] == '/add-bill'){
            var element = document.getElementById('billNav');
            element.classList.add('active');
        }

        if(url_path[2]!==undefined){
            update_bill_id = url_path[2];
        }

        $(document).ready(function(){
            if(update_bill_id != ''){
                $('#updateBillBtn').show();
                loadBillDetail();
            }else{
                $('#saveBillBtn').show();
            }
            loadItems();
            loadBillTypes();
        });

        function loadBillDetail(){
            $.ajax({
                url : "/get-bill",
                type:'GET',
                data:{
                        "_token": "{{ csrf_token() }}",
                        id:update_bill_id,
                    },
                dataType: 'json',
                success: function(response) {
                    shop_name = $('#shopName').val(response.bill.bill.shop_name);
                    bill_type = Number.parseInt($('#billType').val(response.bill.bill.type_id));
                    bill_date = $('#billDate').val(response.bill.bill.date.slice(0, 10));
                    sub_total = $('#subTotal').val(response.bill.bill.sub_total);
                    grand_total = $('#grandTotal').val(response.bill.bill.total_amount);
                    discount = $('#discount').val(response.bill.bill.discount);
                    
                    $.each(response.bill.bill_detail,function(key, value)
                    {
                        let item = {};
                        item.id = value.item_id;
                        item.name = value.items.name;
                        // item.name = $("#item").find("option:selected").attr('data-name');
                        item.quantity = value.quantity;
                        item.rate = Number.parseFloat(value.rate);
                        item.amount = Number.parseFloat(value.amount);;
                        items.push(item);
            
                        var table = document.getElementById("itemTable");
                        var rowCount = table.rows.length;
                        var row = table.insertRow(rowCount);
                        row.insertCell(0).innerHTML= rowCount;
                        row.insertCell(1).innerHTML= item.name;
                        row.insertCell(2).innerHTML= item.rate;
                        row.insertCell(3).innerHTML= item.quantity;
                        row.insertCell(4).innerHTML= item.amount;
                        row.insertCell(5).innerHTML= '<a id="item_'+item.id+'" item-id = '+item.id+' class="delete-icon delete-item" onclick="deleteItem('+item.id+')"><span data-feather="trash-2"></span></a>';
            
                        $("#item").val('default').selectpicker("refresh");
                        $('#rate').val(0);
                        $('#quantity').val(0);
                        feather.replace();
                    });
                    grandCalculation();

                    setTimeout(() => {
                        $('.selectpicker').selectpicker('refresh');               
                    }, 0);
                }
            });            
        }

        function printErrorMsg (msg) {
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").css('display','block');
            $.each( msg, function( key, value ) {
                $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
            });
        }

        function loadItems(){
            $.ajax({
                url : "/get-items",
                type:'GET',
                dataType: 'json',
                success: function(response) {
                    $.each(response.items,function(key, value)
                    {
                       $("#item").append('<option value=' + value['id'] + ' data-rate='+ value['last_price'] + ' data-name="'+ value['name'] +  '">' + value['name'] + '</option>');
                    });
                    $('.selectpicker').selectpicker('refresh');
                }
            });

            // $('#item').trigger('focus')
        }

        function loadBillTypes(){
            $.ajax({
                url : "/get-bill-types",
                type:'GET',
                dataType: 'json',
                success: function(response) {
                    $.each(response.bill_types,function(key, value)
                    {
                        // console.log(value);
                       $("#billType").append('<option value=' + value['id'] + ' data-type='+ value['type'] + '>' + value['type'] + '</option>');
                    });
                    $('.selectpicker').selectpicker('refresh');
                }
            });
            
            // $('#item').trigger('focus')
        }

        $('#item').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
            if(isSelected){
                if($("#item").find("option:selected").attr('data-rate') == 'null'){
                    $('#rate').val(0);
                    $('#quantity').val(1);
                }else{
                    $('#rate').val($("#item").find("option:selected").attr('data-rate'));
                    $('#quantity').val(1);
                }            
            }
        });

        var items = [];
        var sub_total = 0;
        var discount = 0;
        var grand_total = 0;

        $('#addItem').on('click',function(){
            let item_exist = false;

            items.forEach(element => {
                if(element.item_id == $('#item').val()){
                    item_exist = true;
                }
            });

            if(!item_exist){

                let item = {};
                item.id = $('#item').val();
                console.log(item.id);
                item.name = $("#item").find("option:selected").attr('data-name');
                item.quantity = $('#quantity').val();
                item.rate = $('#rate').val();
                item.amount = item.rate*item.quantity;
                items.push(item);
    
                var table = document.getElementById("itemTable");
                var rowCount = table.rows.length;
                var row = table.insertRow(rowCount);
                row.insertCell(0).innerHTML= rowCount;
                row.insertCell(1).innerHTML= item.name;
                row.insertCell(2).innerHTML= item.rate;
                row.insertCell(3).innerHTML= item.quantity;
                row.insertCell(4).innerHTML= item.amount;
                row.insertCell(5).innerHTML= '<a id="item_'+item.id+'" item-id = '+item.id+' class="delete-icon delete-item" onclick="deleteItem('+item.id+')"><span data-feather="trash-2"></span></a>';
    
                $("#item").val('default').selectpicker("refresh");
                $('#rate').val(0);
                $('#quantity').val(0);
                feather.replace();
                grandCalculation();
            }else{
                alert('Item already exist!');
            }

        });
        
        function deleteItem(id){
            $('#item_'+id).closest("tr").remove();
            items = items.filter(function(item){ return item.id != id })  
           
            var table = document.getElementById("itemTable");
            var rowcountAfterDelete = document.getElementById("itemTable").rows.length;  
            for(var i=1;i<rowcountAfterDelete;i++){    
                table.rows[i].cells[0].innerHTML=i;
            }

            grandCalculation();
            
        }

        function grandCalculation(){
            sub_total = 0;
            grand_total = 0;
            $.each(items, function (i,item) {
                sub_total = sub_total + item.quantity*item.rate;                
            });
            grand_total = sub_total - $('#discount').val();
            
            $('#subTotal').val(sub_total);
            $('#grandTotal').val(grand_total);
            if(grand_total < 0){
                $('#discount').val(0); 
            } 

            discount = $('#discount').val();
        }

        function saveBill(){
            // validation remaining

            var shop_name = $('#shopName').val();
            var bill_type = $('#billType').val();
            var bill_date = $('#billDate').val();
            
            $.ajax({
                url: "/bill",
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    type_id:bill_type,
                    date:bill_date,
                    paid_date:bill_date,
                    shop_name:shop_name,
                    sub_total:sub_total,
                    discount:discount,
                    total_amount:grand_total,
                    description:'',
                    items: items

                },
                dataType: 'JSON',
                success:function(response){
                    window.location = "/bill";
                },
                error: function(response) {
                    var myProp = 'errors';
                    
                    if(response.responseJSON.hasOwnProperty(myProp)){
                        printErrorMsg(response.responseJSON.errors);
                    }
                },
            });
        }

        function updateBill(){
            // validation remaining

            var shop_name = $('#shopName').val();
            var bill_type = $('#billType').val();
            var bill_date = $('#billDate').val();
            
            $.ajax({
                url: "/update-bill",
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    update_bill_id: update_bill_id,
                    type_id:bill_type,
                    date:bill_date,
                    paid_date:bill_date,
                    shop_name:shop_name,
                    sub_total:sub_total,
                    discount:discount,
                    total_amount:grand_total,
                    description:'',
                    items: items

                },
                dataType: 'JSON',
                success:function(response){
                    window.location = "/bill";
                },
                error: function(response) {
                    var myProp = 'errors';
                    
                    if(response.responseJSON.hasOwnProperty(myProp)){
                        printErrorMsg(response.responseJSON.errors);
                    }
                },
            });
        }


    </script>
    
@endsection