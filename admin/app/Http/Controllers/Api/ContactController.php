<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  App\Models\Contact;

class ContactController extends Controller
{

    public function index()
    {

    }

    public function create()
    {

    }

    public function store(Request $request)
    {

            $userId = $request->business_id;
            $contactList = json_decode($request->contactlist,true);
       
            $checkUser  =   Contact::where('business_id',$userId)->exists();
            if(!$checkUser)
            {
                foreach($contactList as $data)
                {

                    $insertdata = [
                        'business_id' => $userId,
                        'name' => $data['name'],
                        'phone' => $data['number']
                    ];

                    try{

                        // Use the create method instead of created
                        Contact::create($insertdata);
                        // Optionally, you can return a response or redirect after creating the contact

                    }catch(\Exception $e)
                    {
                        // Handle the exception and show a custom error message
                        return response()->json(['error' => 'Failed to create contact. ' . $e->getMessage()], 500);
                    }
                }
                return response()->json(['message' => 'Contact created successfully'], 201);
            }else{
                return response()->json(['message' => 'Contact already exist'],200);
            }

    }
}
