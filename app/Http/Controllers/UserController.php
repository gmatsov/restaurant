<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolesUpdateRequest;
use App\Models\Role;
use App\Models\User;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserStoreRequest;
use App\Models\UsersRoles;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware(['manager'], ['only' => ['index', 'create', 'destroy', 'fired.users']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @param User $users
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::paginate(10);;
        $roles = Role::pluck('name');
        return view('user.index', compact(['users', 'roles']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name');

        return view('user.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request)
    {
        return User::addUser($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view('user.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        dd($request->all());
        $user->update($request->all());

        return redirect()->route('user.edit', $user)->with('success', 'Successful updated user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::destroy($id);

        return redirect()->route('user.index')->with('success', 'Successful fire worker');
    }

    public function exWorkers()
    {
        $users = User::onlyTrashed()->paginate(10);

        return view('user.deleted', compact('users'));

    }

    public function updateRoles(RolesUpdateRequest $request)
    {
        $user = User::find($request->user);
        $roles = $request->roles;
        UsersRoles::where('user_id', $request->user)->delete();

        for ($i = 0; $i < count($roles); $i++) {
            UsersRoles::insert([
                'user_id' => $user->id,
                'role_id' => $roles[$i],
            ]);
        }

        return redirect()->route('user.index')->with('success', 'Successful update worker permission');
    }
}
