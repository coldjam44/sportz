<?php

namespace App\Http\Controllers\Apis;

use App\Models\contactus;
use Illuminate\Http\Request;
use App\Http\Trait\GeneralTrait;
use App\Http\Controllers\Controller;

class ContactusController extends Controller
{
use GeneralTrait;
    public function index()
    {
        $contactuss = contactus::all();
        return $this->returnData('contactuss',$contactuss);
    }

    public function store(Request $request)
    {
        try {
        $request->validate([
            'name'=>'required',
            'email'=>'nullable',
            'message_title'=>'required',
            'message'=>'required',
        ],
        [
            'name.required'=>'يرجى ادخال الاسم',
            'message_title.required'=>'يرجى ادخال عنوان الرسالة',
            'message.required'=>'يرجى ادخال الرسالة',
        ]);
        $contactus = new contactus();
        $contactus->name = $request->name;
        $contactus->email = $request->email;
        $contactus->message_title = $request->message_title;
        $contactus->message = $request->message;
        $contactus->save();
        return $this->returnData('contactus',$contactus);


        } catch (\Exception $e) {
           return $this->returnError('E001','error');
        }
    }

    public function destroy($id)
    {
        $contactus = contactus::find($id);
        $contactus->delete();
        if(!$contactus){
            return $this->returnError('E001','data not found');
        }else{
            return $this->returnSuccessMessage('data deleted');
        }    }

    }

