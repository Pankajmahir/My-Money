<?php

namespace App\Http\Controllers;

use App\Models\VersionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VersionController extends Controller
{
    //
    public function index(){
      
        $versionData = VersionModel::all();
        // dd($versionData);
        return view('version.showdata',compact('versionData'));
    }
    public function store(Request $request){

    //   dd($request);
      $parameters =[
        "device_type" =>$request->device_type,
        "version_no" =>$request->version_no,
        "status" =>$request->status
      ];

      $deviceType = $request->device_type;
      $versionNo = $request->version_no;
      $versionData = VersionModel::where('device_type','=',$deviceType)->Where('version_no','=',$versionNo)->first();
      // dd($versionData);
      $device_name = strtolower($request->device_type);
      $version = new VersionModel();
      $version->device_type = $device_name;
      
      $version->version_no =$request->version_no;
      $version->status =$request->status;
      
      // dd( $version);

      if(empty($versionData)){
        // echo"ll";
        $version->save();

      }
      else{
     
        return back()->with('error', 'Data is already exist.');
        
      }
      return back()->with('success', 'Data added successfully.');
      // return redirect()->action('VersionController@index');
      // return redirect('/admin/version');
        // dd($version->save());
    }

    public function delete($id){
        $data = VersionModel::where('id',$id)->delete();
        return redirect()->action('VersionController@index');
    }

    public function edit($id){
        $data = VersionModel::where('id',$id)->first();
        // dd($data);
       return view('version.edit-show',compact('data'));
    }

    public function update(Request $request){
        // dd($request);
      $update = VersionModel::where('id',$request->id)->update([
        'device_type' => $request->device_type,
        'version_no' => $request->version_no,
        'status' => $request->status,
      ]);
    //   dd($update);
    return redirect()->action('VersionController@index');
    }

    // 
    public function compareData(Request $request){
        $response = [];
        
        $deviceType = strtolower($request->device_type);
        $versionNo = $request->version_no;
        $versionDataOn = VersionModel::where('device_type','=',$deviceType)->Where('version_no','=',$versionNo)->where('status','=','1')->first();
        $versionDataOff = VersionModel::where('device_type','=',$deviceType)->Where('version_no','=',$versionNo)->where('status','=','0')->first();
        // dd();
        $response['data'] = ['success' => true];
        if($versionDataOn){
            // echo 'sjksjkd';
            $response['msg'] = 'data found successfully';
            $response['success'] = true;
            $response['data'] = ['status' => true];
        } elseif($versionDataOff){
          // echo 'mmmmm';
          // if()
          $response['success'] = true;
          $response['data'] = ['status' => false];
          $response['msg'] = 'data found successfully';
        }else{
            // echo 'xxxxxxxxxxxxxxxxxxx';
            // if()
            $response['success'] = true;
            $response['data'] = ['status' => false];
            $response['msg'] = 'data found successfully';
        }
        
        return response()->json($response);
    }

    function statusUpdate(Request $request){
      // $update = VersionModel::where('id',$request->id)->update([
      //   'status' => $request->status,
      // ]);
      // return response()->json($update);
      $users = VersionModel::findOrFail($request->id);
      $users->status = $request->status;
        if($users->save()){
            return 1;
        }
        return 0;
    }
}
