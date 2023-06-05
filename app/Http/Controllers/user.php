<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\users;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\DemoMail;
use DB;


class user extends Controller
{
    public function allUsers(){
        $user = 
        DB::table("users")
        ->select("id")
        ->get();
        return response()->json($user,200);

    }
    public function users($id)
    {
        $user = DB::table('users')
            ->where('id', $id)
            ->get();
        return response()->json($user, 201);
    }
    public function delete($id){

        $user = DB::table('users')->where('id', $id)->exists();
        if ($user) {
            $deleteCustomer = DB::table('users')->where('id', $id)->delete();
            return response()->json(['successfully' => "El usuario fue eliminada" ], 201);
        } else {
            return response()->json(['error' => 'No existe ese id del usuario'], 401, []);
        }
       }


       public function create(Request $request)
    {
        $data = $request->json()->all();
        $email= $data['email'];
        $check=DB::table('users')
        ->select('id')
        ->where('email', $email)
        ->get();

        if ($check->isEmpty()) {
        $create = new users();
        $create->username = $data['username'];
        $create->name = $data['name'];
        $create->lastname = $data['lastname'];
        $create->email = $data['email'];
        $create->phone = $data['phone'];
        $create->age = $data['age'];
        $create->birth = $data['birth'];
        $create->password = $data['password'];
        $create->photo = $data['photo'];
        $create->save();
        // Verificar que el correo electrónico es válido
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['message' => 'Correo electrónico inválido'], 422);
    }
        return response()->json($create, 201);
        } else {
            return response()->json(["message" => "El correo no esta registrado"],204);
        }
        
    }
       public function update(Request $request, $id)
       {
   
           $data = $request->json()->all();
           $update = users::find($id);
           $update->username = $data['username'];
           $update->name = $data['name'];
           $update->lastname = $data['lastname'];
           $update->email = $data['email'];
           $update->phone = $data['phone'];
           $update->age = $data['age'];
           $update->birth = $data['birth'];
           $update->password = $data['password'];
           $update->save();
           return response()->json($update, 201);
       }
       public function login(Request $request){
        $data = $request->json()->all();
        $email= $data['email'];
        $password = $data['password'];
        $check=DB::table('users')
        ->select('photo')
        ->where('email', $email)
        ->where('password', $password)
        ->get();
        if($check->isEmpty()){
            $login=DB::table('users')
        ->select('email','password')
        ->where('email', $email)
        ->where('password', $password)
        ->get();
        }else{
            $login=DB::table('users')
        ->select('email','password','photo')
        ->where('email', $email)
        ->where('password', $password)
        ->get();
        }
        if ($login->isEmpty()) {
            return response()->json(["message" => "El correo o la contraseña son incorrectos."],204);
        } else {
            return response()->json($login,200);
        }
    }
    public function forgotpass(Request $request){
        $data = $request->json()->all();
        $email=$data['email'];
        $recover=DB::table('users')
        ->select('password')
        ->where('email', $email)
        ->get();
        
        if ($recover->isEmpty()) {
            return response()->json(["message" => "El correo ingresado no esta registrado"],204);
        } else {
            
            Mail::to($email)->send(new DemoMail($recover));
            return response()->json($email,200);
        }    
        
        
    }
    
   
}

