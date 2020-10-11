<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Post;

class UserController extends Controller
{

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users, 201); 
        //dd($users);
           
    }

        /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         // load the create form (app/views/users/create.blade.php)
        return View::make('users.create');
    }

   /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate
        // read more on validation at http://laravel.com/docs/validation      
     try{
        $this->validate($request, [
            'name' => 'required', 'string', 'max:255',
            'email' => 'required', 'string', 'email:rfc,dns',
            'post_title' => 'required', 'string', 'max:255',
            'post_body' => 'required', 'string', 'max:255',
        ]);

        $request->session()->keep(['name', 'email', 'post_title', 'post_body']);

         DB::transaction(function () use ($request) {
            // store
            $user = (new User)->createUser([
                'name' => $request->post('name'),
                'email' => $request->post('email')
            ]);
    
            $lastUserId = $user->id;

            $post = (new Post)->createPost([
                'user_id' => $lastUserId,
                'title' => $request->post('post_title'),
                'body' => $request->post('post_body'),
            ]);          
         });

         return back()->with('success', 'The user and post were successfully created!');
        }  
        catch(QueryException $e){
         return response()->json($e, 500); 
        }        
    }

    /**
     * Display the specified resource.
     *
     * @param Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        return wrapControllerAction(function() use ($request, $id) {
            // !!!Notice, the $id is validated by Laravel automatically.
            $post = (new User)->searchById($id);
            return response()->json($post, 200);
        });
    }
}
