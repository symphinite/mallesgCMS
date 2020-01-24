<?php

namespace App\Http\Controllers;

use App\CategoryMaster;
use App\SubCategoryMaster;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sub_cats = SubCategoryMaster::all();
        $categorys = CategoryMaster::all();
        $data = [
            'sub_cats' => $sub_cats,
            'categorys' => $categorys
        ];

        return view('main.category_tag.category_tags',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [
            'Sub_Category_name.required'    => 'Tag name field is required'
        ];

        // Start Validation
        $validator = \Validator::make($request->all(), [
            'Sub_Category_name' => 'required|unique:sub_category_master',
            'Category_id' => 'required',
        ],$messages);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->messages()->first()
            ],200);
        }


        $tag_master = new SubCategoryMaster();
        $tag_master->Category_id = $request->Category_id;
        $tag_master->Sub_Category_name = $request->Sub_Category_name;
        $tag_master->Created_on = Carbon::now()->format('Y-m-d');
        $tag_master->Created_by = \Auth::user()->user_id;
        $tag_master->image = '';
        $tag_master->save();

        return response()->json([
            'status' => 'success',
            'message' => __('successfully added tags'),
            //'tag_name' => $request->time_name,
            //'id' => $time_master->time_id
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tagMaster = SubCategoryMaster::find($id);
        $categorys = CategoryMaster::all();

        $data = [
            'tagMaster' => $tagMaster,
            'categorys' => $categorys,
            'live_url' => env('LIVE_URL').'images/stock/'
        ];

        return view('main.category_tag.edit_tags',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $messages = [
            'Sub_Category_name.required'    => 'Tag name field is required'
        ];

        // Start Validation
        $validator = \Validator::make($request->all(), [
            'Sub_Category_name' => 'required|unique:sub_category_master,Sub_Category_name,'.$id.',sub_category_id',
            'Category_id' => 'required',
        ],$messages);



        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->messages()->first()
            ],200);
        }

        $tag_master = SubCategoryMaster::find($id);
        $tag_master->Sub_Category_name = $request->Sub_Category_name;
        $tag_master->Category_id = $request->Category_id;
        $tag_master->Created_on = Carbon::now()->format('Y-m-d');
        $tag_master->Created_by = \Auth::user()->user_id;
        $tag_master->save();

        return response()->json([
            'status' => 'success',
            'message' => __('successfully Updated tags'),
            //'tag_name' => $request->time_name,
            //'id' => $time_master->time_id
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tagMaster = SubCategoryMaster::find($id);
        $tagMaster->delete();

        return response()->json([
            'status' => $tagMaster ? 'success' : 'error',
            'message' => $tagMaster ? __('succesfully deleted') : __('error deleting')
        ],200);
    }

    public function search($name){
        return SubCategoryMaster::search($name);
    }

    public function uploadimage(Request $request)
    {
        $file = $request->files->get('image');
        try{

            if($file->getMimeType()!="image/png"){
                throw new \Exception("invalid file", 500);
            }


            $newfilename = md5($request->sub_category_id."_".round(microtime(true))) . '.png';

            if(env('APP_ENV')=='live')
                $file->move('../../admin/images/stock/', $newfilename);
            else
                $file->move('../storage/app/public/', $newfilename);


            $tag = SubCategoryMaster::find($request->sub_category_id);
            $tag->image = $newfilename;
            $tag->save();


        } catch (QueryException $e) {
            throw new \Exception($e->getMessage(), 500, $e);
        }

        return response()->json([
            'status' => 'success' ,
            'message' =>__('succesfully uploaded'),
            'file' => env("LIVE_URL").$newfilename
        ],200);

    }

    public function deleteimage($id){

        $image = SubCategoryMaster::find($id);

        if(env('APP_ENV')=='live')
            unlink('../../admin/images/stock/'.$image->image);
        else
            unlink('../storage/app/public/'.$image->image);

        $image->image = '';
        $image->save();

        return response()->json([
            'status' => $image ? 'success' : 'error',
            'message' => $image ? __('succesfully deleted') : __('error deleting')
        ],200);
    }
}
