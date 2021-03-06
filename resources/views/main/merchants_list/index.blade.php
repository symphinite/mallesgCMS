@extends('layouts.app')
@section('style')
    <style>
        .merch_out .select2-container--default .select2-selection--single .select2-selection__arrow{
            top: 5px !important;
        }
        .merch_out .select2-container .select2-selection--single {
            height: 38px !important;
        }

        .merch_out .select2-container--default .select2-selection--single .select2-selection__rendered{
            line-height: 35px;
        }
        .link_color{
            color: blue;}


    </style>
    @endsection
@section('content')
<div class="row">
    <div class="col-md-10">
        <div class="card card-malle">
            @include('main.merchants_list.merchant_menu')

            <div class="card-body">
                <form method="POST" action="{{ route('merchants.store') }}" id="InsertMerchants">
                    <div class="row merch_out">
                        <div class="col-md-3">
                            <label class="mb-2 font-12">{{__('Merchant')}}</label>
                            <input type="text" name="merchant_name" placeholder="Type Merchant Name" id="merchant_name" class="form-control" required="" value="{{@$current_merchant->merchant_name}}"  data-autocompleturl="{{route('merchants.search')}}"/>

                        </div>
                        @if(!isset($id))
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="mb-2 font-12">{{__('Country')}}</label>
                                <br>
                                <select id="country_select">
                                    @if(!empty($countrys))
                                        @foreach($countrys as $country)
                                            <?php $country_total = \App\CountryMaster::totalCountryMerchant($country->country_id);?>
                                            <option value="{{ $country->country_id }}" title="{{ $country->country_name }}">{{ $country->country_name }} ({{ $country_total }})</option>
                                        @endforeach
                                    @endif
                                        <input type="hidden" name="country_id" class="merchant_country_id" value="">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="mb-2 font-12">{{__('Merchant Type')}}</label>
                                <select id="merchant_type">
                                    @if(!empty($merchant_types))
                                        <option value="all">All ({{ @$total_merchant }})</option>
                                        @foreach($merchant_types as $merchant_type)
                                            <?php $type_total = \App\MerchantType::totalTypeMerchant($merchant_type->mt_id);?>
                                            <option value="{{ $merchant_type->mt_id }}" title="{{ $merchant_type->type }}">{{ $merchant_type->type }} ({{ $type_total }})</option>
                                        @endforeach
                                    @endif
                                    <input type="hidden" name="mt_id" class="merchant_type_id" value="">
                                </select>
                            </div>
                        </div>
                        @endif
                    </div>


                    <div class="col-md-12 row insert_merchant" style="display: none">
                        <div class="form-group">
                            <button class="btn btn-primary" id="out-form">Update</button>
                        </div>
                    </div>
                </form>

            @if(isset($current_merchants))
            <br />
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped malle-table" id="merchant-list-table" @if(isset($id))  data-sourceurl="{{route('merchants.show',['merchant'=>@$id])}}" @else data-sourceurl="{{route('merchants.list')}}" @endif>
                        <thead>
                        <th>Merchant Name</th>
                        <th>City</th>
                        <th>Country</th>
                        <th>Type</th>
                        <th>Beta</th>
                        <th>Active</th>
                        <th>Featured</th>
                        <th>Outlet</th>
                        <th>Action</th>
                        </thead>
                        <tbody>
                        @if(!empty($current_merchants))
                        @foreach($current_merchants as $current_merchant)
                            <tr class="row-location" data-id="{{$current_merchant->merchant_id}}">
                                <td>{{$current_merchant->merchant_name}}
                                <br><br><span class="link_color"><a href="{{ route('merchants.edit',[$current_merchant->merchant_id]) }}"><b> Main Info</b> </a> </span>
                                </td>
                                <td>{{ @$current_merchant->city->city_name }}
                                    <br><br>
                                    <span class="link_color"><a href="{{ route('merchants.images',['merchants'=>$current_merchant->merchant_id]) }}"> <b>Images</b></a></span>
                                </td>
                                <td>{{ $current_merchant->country->country_name }}
                                    <br><br>
                                    <span class="link_color"><a href="{{ route('merchant-contact.show',$current_merchant->merchant_id) }}"> <b>Contacts</b></a></span>
                                </td>
                                <td>{{ @$current_merchant->merchanttype->type }}</td>
                                <td>
                                    <span style="display: none"> {{ $current_merchant->beta }} </span>
                                    <select name="beta" id="" class="merchant_column_update dd-orange" data-href="{{route('merchants.column-update',[$current_merchant->merchant_id])}}" data-method="POST">
                                        <option value="N" @if($current_merchant->beta=='N') selected @endif>No</option>
                                        <option value="Y" @if($current_merchant->beta=='Y') selected @endif>Yes</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="merchant_active" id="" class="merchant_column_update dd-orange" data-href="{{route('merchants.column-update',[$current_merchant->merchant_id])}}" data-method="POST">
                                        <option value="N" @if($current_merchant->merchant_active=='N') selected @endif>No</option>
                                        <option value="Y" @if($current_merchant->merchant_active=='Y') selected @endif>Yes</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="featured" id="" class="merchant_column_update dd-orange" data-href="{{route('merchants.column-update',[$current_merchant->merchant_id])}}" data-method="POST">
                                        <option value="N" @if($current_merchant->featured=='N') selected @endif>No</option>
                                        <option value="Y" @if($current_merchant->featured=='Y') selected @endif>Yes</option>
                                    </select>
                                </td>

                                <td> {{ $outlate_totel  = \App\PromotionOutlet::totalOutlate($current_merchant->merchant_id) }}</td>
                                <td>
                                    {{--<a href="{{ route('merchants.images',['merchants'=>$current_merchant->merchant_id]) }}">
                                        <span class="text-info">Edit</span>
                                    </a>
                                    |--}}
                                    <a  href="javascript:;" data-href="{{route('merchants.destroy',['merchants'=>$current_merchant->merchant_id])}}" data-method="DELETE" class="btn-delete" data-id="{{$current_merchant->merchant_id}}">
                                        <span class="text-danger">Delete</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            </div>
        </div>
    </div>
</div>

@include('partials.delete_model')
@endsection


@section('script')
<script>


    $(document).on('submit','#InsertMerchants', function(e){
        e.preventDefault();
        var data = $(this).serialize();
        var url = $(this).attr('action');
        var type =  $(this).attr('method');

        $.ajax({
            url: url,
            type: type,
            dataType:'json',
            data:data,
            success:function(data){
                if(data.status==='error'){
                    toastr.error(data.message, 'Error');
                }else{
                    //$("#event-table").load( $('#event-table').attr('data-sourceurl') +" #event-table");
                    $("#merchant-list-table").load( $('#merchant-list-table').attr('data-sourceurl') +" #merchant-list-table");
                    toastr.success(data.message);
                }
            },
            error: function(data){
                exeptionReturn(data);
            }
        });
    });


    $(document).ready(function() {
        var dataTables =  $('#merchant-list-table').DataTable({
            responsive: true,
            aaSorting: [],
            paging: false
         }
        );


        '<?php if(!isset($id)) { ?>'
        dataTables.columns(2).search("Singapore").draw();
        '<?php } ?>'

        $('#country_select').on('select2:select', function (e) {

            var val = e.params.data.title;
            var id = e.params.data.id;
            $('.merchant_country_id').val(id);
            dataTables.columns(2).search(val).draw();


        });

        $('#merchant_type').on('select2:select', function (e) {
            // $("#time_dow_id").val(e.params.data.id);
            var val = e.params.data.title;
            var id = e.params.data.id;
            $('.merchant_type_id').val(id);
            //console.log(e.params.data.text);
            if(val=='all'){
                dataTables.columns(3).search("").draw();
            }else{
            dataTables.columns(3).search(val).draw();
            }
        });


    });

  $( function() {


     //$('#country_select').val('');
      $('#country_select,#merchant_type').select2({
          width:200
      });

    $("#start_date").datepicker({dateFormat: 'dd/mm/yy'});
            $("#end_date").datepicker({dateFormat: 'dd/mm/yy'});

    // malls
    $( "#merchant_name" ).autocomplete({
        source: function (request, response) {
            $.getJSON($("#merchant_name").attr('data-autocompleturl') +'/' + request.term , function (data) {
                if(data.length == 0){
                    $('.insert_merchant').show();
                }else{
                    $('.insert_merchant').hide();
                }
                response($.map(data, function (value, key) {
                    return {
                        label: value,
                        value: key
                    };
                }));
            });
        },
          select: function(event, ui) {
            $("#merchant_name").val(ui.item.label);
            window.location.href = '{{route("merchants.list.show")}}/'+ui.item.value;

            return false;
          }
    });

    // malls
    $( "#mall_name" ).autocomplete({
        source: function (request, response) {
            $.getJSON($("#mall_name").attr('data-autocompleturl') +'/' + request.term, function (data) {
                response($.map(data, function (value, key) {
                    return {
                        label: value,
                        value: key
                    };
                }));
            });
        },
          select: function(event, ui) {
             $("#mall_name").val(ui.item.label);
             $("#mall_id").val(ui.item.value);
             return false;
          }
    });

    // store
    $(document).on('submit','#frm-add-location', function(e){
        e.preventDefault();
        var data = $(this).serialize();
        var url = $(this).attr('action');
        var type =  $(this).attr('method');

        $.ajax({
            url: url,
            type: type,
            dataType:'json',
            data:data,
            success:function(data){
                if(data.status==='error'){
                    alert(data.message);
                }else{
                    $('#location-table tbody').remove();
                    $("#location-table").load( $('#location-table').attr('data-sourceurl') +" #location-table tbody");
                    toastr.success("Successfully Added!");
                }
            }
        });

    });

    // delete
    $(document).on('click', '.btn-delete', function(e){
        e.preventDefault();
        var btndelete = $(this);

        $('#deletelocationmodal').modal('show');

        $('#btnDeleteLocation').unbind().click(function(){

            $.ajax({
                url: btndelete.attr('data-href'),
                type: btndelete.attr('data-method'),
                dataType:'json',
                success:function(data){
                    if(data.status==='error'){
                        toastr.error(data.message);
                    }else{
                        $('#deletelocationmodal').modal('hide');
                        $('.row-location[data-id="'+btndelete.attr('data-id')+'"]').remove();
                        toastr.success(data.message);
                    }
                }
            });

        });
    });




      // change promo outlate live, featured and redeem status
      $(document).on('change', '.merchant_column_update', function(e){
          e.preventDefault();
          //debugger;
          var selectOp = $(this);
          var attrName = selectOp.attr("name");

          $.ajax({
              url: selectOp.attr('data-href'),
              type: selectOp.attr('data-method'),
              dataType:'json',
              data: {
                  name : selectOp.attr('name'),
                  value : selectOp.find('option:selected').val()
              },
              success:function(data){
                  console.log(data);
                  if(data.status==='error'){
                      errorReturn(data)
                  }else{
                      //$('#merchant-list-table tbody').remove();
                    //  $("#merchant-list-table").load( $('#merchant-list-table').attr('data-sourceurl') +" #merchant-list-table");
                      toastr.success(data.message);
                  }
              },
              error: function(data){
                  console.log(data);
                  exeptionReturn(data);
              }
          });

      });





  });
  </script>
@endsection
