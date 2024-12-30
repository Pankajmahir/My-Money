<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;


class GeneralSettingController extends Controller
{
    public function index(Request $request)
    {
        $settings = GeneralSetting::first();
        return view('general_settings.index', compact('settings'));
    }

    public function Store(Request $request)
    {
        $settings = GeneralSetting::findOrFail($request->id);
        $settings->faq = $request->faq;
        $settings->about = $request->about;
        $settings->whatsapp_number = $request->whatsapp_number;
        $settings->phone = $request->phone;
        $settings->ref_from_call = $request->ref_from_call;
        $settings->ref_from_sms = $request->ref_from_sms;
        $settings->ref_to_call = $request->ref_to_call;
        $settings->ref_to_sms = $request->ref_to_sms;
        $settings->daily_call_limit = $request->daily_call_limit;
        $settings->daily_sms_limit = $request->daily_sms_limit;
        $settings->daily_email_limit = $request->daily_email_limit;
        if($settings->save()){
            toastr()->success('Setting changed successfully!');
            return back();
        }

    }


}
