@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-10">
        <div class="card card-malle">
            <div class="card-header-malle">{{__('Manage Merchants')}}</div>
            <div class="card-body">

            <div class="row">
                <div class="col-md-3">
                    <label class="mb-2 font-12">{{__('Merchant')}}</label>
                    <input type="text" name="merchant_name" placeholder="Type Merchant Name" id="merchant_name" class="form-control" required="" value="{{@$current_merchant->merchant_name}}"  data-autocompleturl="{{route('merchants.search')}}"/>

                </div>
            </div>

            @if(isset($locations))
            <br />
            <div class="row">
                <div class="col-md-12"> 

                    <form method="POST" action="{{route('locations.store')}}" id="frm-add-location">
                        <input type="hidden" name="mall_id" id="mall_id">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="hidden" name="merchant_id" value="{{$id}}">
                                    <label class="mb-2 font-12">Mall Name</label>
                                    <input type="text" name="mall_name" placeholder="Mall Name" id="mall_name" class="form-control" required="" list="datalist1" data-autocompleturl="{{route('malls.search')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="mb-2 font-12">Location</label>
                                    <input type="text" name="merchant_location" placeholder="Location" id="location" class="form-control" required="">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="mb-2 font-12">&nbsp;</label>
                                    <select name="level_id" class="form-control" required="">
                                        @if($floors)
                                            <option value="">---- {{__('Select Level')}} ----</option>
                                            @foreach($floors as $floor)
                                             <option value="{{@$floor->level_id}}">{{@$floor->level}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary col-md-12 top-t" id="btnMerchantLocation">Update</button>
                            </div>
                        </div>
                    </form>
                    <table class="table table-striped malle-table " id="location-table" data-sourceurl="{{route('merchants.show',['merchant'=>$id])}}">
                        <tbody>
                        @foreach($locations as $location)
                            <tr class="row-location" data-id="{{$location->merchantLocation_id}}">
                                <td>{{$location->mall->mall_name}}</td>
                                <td>{{$location->merchant_location}}</td>
                                <td>{{@$location->floor->level}}</td>
                                <td>
                                    <a  href="javascript:;" data-href="{{route('locations.destroy',['merchants'=>$location->merchantLocation_id])}}" data-method="DELETE" class="btn-delete" data-id="{{$location->merchantLocation_id}}">
                                        <span class="text-danger">Delete</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deletelocationmodal" tabindex="-1" role="dialog" aria-labelledby="deletemodallocationlabel" aria-hidden="true">
<div class="modal-dialog" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="deletemodallocationlabel">Delete Confirmation</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body ">
      <p class="font-12">Are you sure you want to delete this location?</p>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
      <button type="button" class="btn btn-danger" id="btnDeleteLocation">Yes</button>
    </div>
  </div>
</div>
</div>
@endsection


@section('script')
<script>
  $( function() {

    $("#start_date").datepicker({dateFormat: 'dd/mm/yy'});
            $("#end_date").datepicker({dateFormat: 'dd/mm/yy'});

    // malls
    $( "#merchant_name" ).autocomplete({
        source: function (request, response) {
            $.getJSON($("#merchant_name").attr('data-autocompleturl') +'/' + request.term, function (data) {
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
            window.location.href = '{{route("merchants")}}/'+ui.item.value;

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


  });
  </script>
@endsection