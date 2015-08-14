<?php
/**
 * Created by PhpStorm.
 * User: antonio
 * Date: 14.08.15
 * Time: 12:03
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Config;
use App\Object;

class ObjectController extends Controller{



    public function create(Request $request)
    {

        $data = (array)$request->input('data');
        if(count($data) > Config::get('app.maxObjects')){
            return [ 'error' => 'too much objects' ];
        }
        $ids = [];
        $rules = [
            'name' => 'required|min:3|max:10',
        ];
        DB::beginTransaction();
        foreach($data as $item){
            $validator = Validator::make($item, $rules);
            if ($validator->fails()) {
                DB::rollback();
                return [ 'error' => $validator->errors()->all() ];
            }
            try{
                $model = Object::create($item);
                $ids[] = $model->id;

            }catch(\Exception $e){
                DB::rollback();
                return ['error' => $e->getMessage()];
            }

        }
        DB::commit();
        return $ids;
        // Store the blog post...
    }

    public function getList()
    {
        try{
            return Object::all(['name', 'status']);
        }catch(\Exception $e){
            return ['error' => $e->getMessage()];
        }

    }

    public function delete($id)
    {
        try{
            return Object::destroy($id);
        }catch(\Exception $e){
            return ['error' => $e->getMessage()];
        }

    }
} 