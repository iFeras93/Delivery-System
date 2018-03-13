<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Page Title
        $title = "Site Settings";

        //Get Site Setting
        $settings = Setting::find(1);

        return view('admin.settings', compact(['title', 'settings']));
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * @param UpdateSettingsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * Update Site Settings
     */
    public function update(UpdateSettingsRequest $request)
    {
        //

        $settings = Setting::find(1);
        if ($request->isMethod('post')) {

            $settings->price_of_km = $request->input('price_of_km');
            $settings->main_email = $request->input('main_email');
            $settings->PAYPAL_SANDBOX_CLIENT_ID = $request->input('PAYPAL_SANDBOX_CLIENT_ID');
            $settings->PAYPAL_SANDBOX_SECRET = $request->input('PAYPAL_SANDBOX_SECRET');

            $settings->main_long = $request->input('main_long');
            $settings->main_lat = $request->input('main_lat');
            $settings->save();
            return redirect()->back()->with(['status' => 'Update Successfully']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
