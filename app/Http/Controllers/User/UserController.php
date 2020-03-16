<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\User;
use Illuminate\Http\Request;


class UserController extends ApiController
{

    public function index()
    {
        $users = User::all();

        return $this->showAll($users);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];

        $this->validate($request, $rules);

        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token'] = User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;

        $user = User::create($data);

        return $this->showOne($user, 201);
    }


    public function show(User $user)
    {
        return $this->showOne($user);

    }


    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => ['string', 'max:255'],
            'email' => ['string', 'email', 'max:255', 'unique:users' . ',' . $user->id],
            'password' => ['string', 'min:6', 'confirmed'],
            'admin' => ['in:' . User::ADMIN_USER . ',' . User::REGULAR_USER],
        ];

        $this->validate($request, $rules);

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email') && $user->email != $request->email) {
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationCode();
            $user->email = $request->email;
        }
        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }
        if ($request->has('admin')) {
            if (!$user->isVerified()) {
                return $this->errorResponse('Only verified users can modify the admin field', 409);
            }
            $request->admin = $request->admin;
        }
        if (!$user->isDirty()) {
            return $this->errorResponse('you need to specify a different value to update', 422);
        }

        $user->save();

        return $this->showOne($user);
    }


    public function destroy(User $user)
    {


        $user->delete();

        return $this->showOne($user);
    }
}
