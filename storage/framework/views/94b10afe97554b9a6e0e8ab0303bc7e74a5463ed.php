<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-10">
            <div class="card card-malle">
                <div class="card-header-malle">
                   <?php echo e(__('Manage Preference Tags')); ?>

                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('preference-tags.store')); ?>" id="addPreferenceTag">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="preference_name" placeholder="Enter Preference Name" id="preference_name"
                                   class="form-control" required="" list="datalist1" data-autocompleturl="<?php echo e(route('preference.tag.search')); ?>">
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" id="out-form">Update</button>
                            </div>
                        </div>

                    </div>
                    </form>

                    <?php if(isset($preference_tags)): ?>
                        <br/>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped malle-table" id="preference-tag-table"
                                       data-sourceurl="<?php echo e(route('preference-tags')); ?>">
                                    <thead>
                                    <th>Preference Name</th>
                                    <th>Action</th>
                                    </thead>
                                    <tbody>
                                    <?php $__currentLoopData = $preference_tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $preference_tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="row-location" data-id="<?php echo e(@$preference_tag->preference_id); ?>">
                                        <td><?php echo e(@$preference_tag->preference_name); ?></td>

                                        <td>
                                            <a href="<?php echo e(route('preference-tags.edit',[$preference_tag->preference_id])); ?>"><span class="text-info">Edit</span></a>
                                            |
                                            <a href="javascript:;"
                                               data-href="<?php echo e(route('preference-tags.destroy',[$preference_tag->preference_id])); ?>"
                                               data-method="DELETE" class="btn-delete"
                                               data-id="<?php echo e($preference_tag->preference_id); ?>">
                                                <span class="text-danger">Delete</span>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

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
<?php $__env->stopSection(); ?>


<?php $__env->startSection('script'); ?>
    <script>

        $(document).on('submit','#addPreferenceTag', function(e){
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
                        $("#preference-tag-table").load( $('#preference-tag-table').attr('data-sourceurl') +" #preference-tag-table");
                        toastr.success(data.message);
                    }
                },
                error: function(data){
                    exeptionReturn(data);
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

        $( "#preference_name" ).autocomplete({
            source: function (request, response) {
                $.getJSON($("#preference_name").attr('data-autocompleturl') +'/' + request.term, function (data) {
                    response($.map(data, function (value, key) {
                        return {
                            label: value,
                            value: key
                        };
                    }));
                });
            },
            select: function(event, ui) {
                $("#preference_name").val(ui.item.label);
                $("#tag_id").val(ui.item.value);
                // window.location.href = '<?php echo e(route("malls")); ?>/'+ui.item.value;
                return false;
            }
        });

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/malle/public_html/adminlaravel3/resources/views/main/preference_tag/preference_tags.blade.php ENDPATH**/ ?>