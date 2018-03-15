<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UpdateUserInfoRequest;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Get 10 Users in Page
        $users = User::paginate(10);
        //Page Title
        $title = "Users List";

        return view('admin.users.index', compact(['title', 'users']));
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
        //get user by id
        $user = User::find($id);
        //title for page
        $title = "Edit User Information :" . $user->name;
        return view('admin.users.edit', compact(['user', 'title']));
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserInfoRequest $request, $id)
    {
        //check request is post
        if ($request->isMethod('post')) {

            // init user
            $user = User::find($id);

            if ($request->has('change_info')) {
                //if change information form submitted
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->type = $request->input('type');

                // Update User Information
                $user->save();

                /**
                 * After Update Information Redirect To Users List
                 */
                return redirect(route('users.index'));

            } elseif ($request->has('change_password')) {
                /**
                 * Check Current User Password With Entered Password in Input
                 */
                if (Hash::check($request->input('current_password'), $user->password)) {
                    $user->password = bcrypt($request->input('password_confirmation'));
                    // Update User Password
                    $user->save();

                    /**
                     * After Update Password Redirect To Users List
                     */
                    return redirect(route('users.index'));

                } else {
                    return redirect()->back()->withErrors(['current_password' => 'Entered Password Is Not The Same With Current Password']);
                }
            }
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
        // Get User By ID
        $user = User::find($id);
        //Check If Exists User
        if (!$user) {
            return redirect()->back()->with(['message' => 'No User Founded']);
        }

        //Delete User By ID
        $user->delete();
        return redirect()->back()->with(['message' => 'User Removed Successfully']);
    }
}
