<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreAdminRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\PaginationResource;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->ressSuccess([
            'message' => 'Successfully get me',
            'data' => [
                'me' => $request->user(),
            ]
        ]);
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeAdmin(UserStoreAdminRequest $request)
    {
        $input = $request->validated();

        $input['image'] = $request->image->store('images/profile', 's3');

        $user = User::create([
            'fullname' => $input['fullname'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'phone' => $input['phone'],
            'image' => $input['image'],
            'status' => 'active',
            'is_blocked' => false,
            'email_verified_at' => now(),
        ]);
        $superAdmin = Role::findByName('Super-Admin', 'web');
        $user->assignRole($superAdmin);

        return $this->ressSuccess([
            'message' => 'Success Create Admin',
            'data' => [
                'user' => $user,
            ]
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $user = User::search($request)->find($id);
        if (empty($user)) {
            return $this->ressError([
                'message' => 'User not found!',
                'data' => []
            ]);
        }
        return $this->ressSuccess([
            'message' => 'Show User',
            'data' => [
                'user' => $user,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::find($id);
        if (empty($user)) {
            return $this->ressError([
                'message' => 'User not found!',
                'data' => []
            ]);
        };

        $input = $request->toArray();
        if ($request->file('image') != null && $request->file('image')->isValid()) {
            $input['image'] = $request->image->store('images/profile', 's3');
        } else {
            $input['image'] = $user->image;
        }

        try {
            $user->update($input);
            return $this->ressSuccess([
                'message' => 'Successfully User Update',
                'data' => [
                    'me' => $user,
                ]
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->ressError([
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function updatePassword(UserRequest $request, $id)
    {
        $user = User::find($id);

        if (!Hash::check($request->oldPassword, $user->password)) {
            return $this->ressError([
                'message' => "Password does not mastch",
                'data' => []
            ]);
        }

        try {
            $user->update(['password' => Hash::make($request->password)]);
            return $this->ressSuccess([
                'message' => 'Successfully Update',
                'data' => [
                    'me' => $user,
                ]
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->ressError([
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function updateIsBlocked(UserRequest $request, $id)
    {
        $user = User::find($id);

        try {
            $user->update($request->all());
            return $this->ressSuccess([
                'message' => 'Successfully Update',
                'data' => [
                    'me' => $user,
                ]
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->ressError([
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $User = User::find($id);
        if (empty($User)) {
            return $this->ressError([
                'message' => 'Cannot find data',
                'data' => []
            ]);
        }
        try {
            $User->delete();
            return $this->ressSuccess([
                'message' => 'Successfully delete User',
                'data' => []
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->ressError([
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function getAllUser(Request $request)
    {
        try {
            $user = new PaginationResource(User::search($request)->paginate($request->limit));
            return $this->ressSuccess([
                'message' => 'Successfully User Update',
                'data' => [
                    'user' => $user,
                ]
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->ressError([
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }
}
