@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-10">
        <div class="card card-malle">
            <div class="card-header-malle">{{__('Manage Malls')}}</div>
            <div class="card-body">

            <div class="row">
                <div class="col-md-3">
                    <label class="mb-2 font-12">Mall Name</label>
                    <input type="text" name="mall_name" placeholder="Enter Mall Name" id="mall_name" class="form-control" required="" list="datalist1" data-autocompleturl="{{route('malls.search')}}" value="{{ @$current_malls->mall_name}}">

                </div>
            </div>

            @if(isset($current_malls))
            <br />
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped malle-table" id="merchant-list-table" data-sourceurl="{{route('merchants.show',['merchant'=>$id])}}">
                        <thead>
                        <th>Mall Name</th>
                        <th>City</th>
                        <th>Country</th>
                        <th>Type</th>
                        <th>Beta</th>
                        <th>Active</th>
                        <th>Featured</th>
                        <th>Merchant</th>
                        <th>Events</th>
                        <th>Promos</th>
                        <th>Action</th>
                        </thead>
                        <tbody>
                        {{--@foreach($locations as $location)--}}
                            <tr class="row-location" data-id="{{$current_malls->mall_id}}">
                                <td>{{ @$current_malls->mall_name }}</td>
                                <td>{{ @$current_malls->city->city_name }}</td>
                                <td>{{ @$current_malls->country->country_name }}</td>
                                <td>{{ @$current_malls->merchanttype->type }}</td>
                                <td>
                                    <select name="beta" id="" class="malls_column_update dd-orange" data-href="{{route('malls.column-update',[$current_malls->mall_id])}}" data-method="POST">
                                        <option value="N" @if($current_malls->beta=='N') selected @endif>No</option>
                                        <option value="Y" @if($current_malls->beta=='Y') selected @endif>Yes</option>

                                    </select>
                                </td>
                                <td>
                                    <select name="mall_active" id="" class="malls_column_update dd-orange" data-href="{{route('malls.column-update',[$current_malls->mall_id])}}" data-method="POST">
                                        <option value="N" @if($current_malls->mall_active=='N') selected @endif>No</option>
                                        <option value="Y" @if($current_malls->mall_active=='Y') selected @endif>Yes</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="featured" id="" class="malls_column_update dd-orange" data-href="{{route('malls.column-update',[$current_malls->mall_id])}}" data-method="POST">
                                        <option value="N" @if($current_malls->featured=='N') selected @endif>No</option>
                                        <option value="Y" @if($current_malls->featured=='Y') selected @endif>Yes</option>
                                    </select>
                                </td>

                                <td> {{ @$total_merchant }}</td>
                                <td> {{ @$total_event }}</td>
                                <td> {{ @$total_promos }}</td>
                                <td>
                                    <a href="javascript:;">
                                        <span class="text-info">Edit</span>
                                    </a>
                                    |
                                    <a  href="javascript:;" data-href="{{route('malls.destroy',['malls'=>$current_malls->mall_id])}}" data-method="DELETE" class="btn-delete" data-id="{{$current_malls->mall_id}}">
                                        <span class="text-danger">Delete</span>
                                    </a>
                                </td>
                            </tr>
                        {{--@endforeach--}}
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
              window.location.href = '{{route("malls")}}/'+ui.item.value;
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
                        window.location.href = '{{ route('malls') }}';
                    }   
                }
            });
                 
        });
    });




      // change promo outlate live, featured and redeem status
      $(document).on('change', '.malls_column_update', function(e){
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
                      //$("#merchant-list-table").load( $('#merchant-list-table').attr('data-sourceurl') +" #merchant-list-table");
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