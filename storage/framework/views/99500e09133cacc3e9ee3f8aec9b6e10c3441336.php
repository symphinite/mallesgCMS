<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-10">
            <div class="card card-malle">
                <?php echo $__env->make('main.mall_list.mall_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('mall-type.store')); ?>" id="addMalltype">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="type_name" placeholder="Enter Mall type" id="type_name"
                                       class="form-control" required="" list="datalist1">
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary" id="out-form">Update</button>
                                </div>
                            </div>

                        </div>
                    </form>

                    <?php if(isset($mall_types)): ?>
                        <br/>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped malle-table"  id="mall_type-table"
                                       data-sourceurl="<?php echo e(route('mall-type')); ?>">
                                    <thead>
                                    <th>Mall Type</th>
                                    <th>Action</th>
                                    </thead>
                                    <tbody>
                                    <?php $__currentLoopData = $mall_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mall_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="row-location" data-id="<?php echo e(@$mall_type->mt_id); ?>">
                                            <td><?php echo e(@$mall_type->type_name); ?></td>

                                            <td>
                                                <a href="<?php echo e(route('mall-type.edit',[$mall_type->mt_id])); ?>"><span class="text-info">Edit</span></a>
                                                |
                                                <a href="javascript:;"
                                                   data-href="<?php echo e(route('mall-type.destroy',[$mall_type->mt_id])); ?>"
                                                   data-method="DELETE" class="btn-delete"
                                                   data-id="<?php echo e($mall_type->mt_id); ?>">
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
    <?php echo $__env->make('partials.delete_model', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('script'); ?>
    <script>

        $(document).on('submit','#addMalltype', function(e){
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
                        $("#mall_type-table").load( $('#mall_type-table').attr('data-sourceurl') +" #mall_type-table");
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

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\adminlaravel3\resources\views/main/mall_list/malltype.blade.php ENDPATH**/ ?>