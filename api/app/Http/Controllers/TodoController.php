<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use ErlandMuchasaj\LaravelFileUploader\FileUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response(["data" => Todo::latest()->get()], 200)->header("Content-Type", "application/json");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'created_by' => 'required',
                'imageFile' => 'mimes:png,jpg,jpeg'
            ]);

            if (!$validator->fails()) {

                if ($request->hasFile('imageFile')) {
                    $response = FileUploader::store($request->file('imageFile'), ['disk' => 'public']);
                    $path_name = "/storage/" . $response['path'];
                }

                $todo = Todo::create([
                    'title' => $request->title,
                    'created_by' => $request->created_by,
                    'description' => $request->description,
                    'path_name' => $path_name ?? null
                ]);

                return response(["data" => $todo], 201)->header("Content-Type", "application/json");
            } else {
                return response(["error" => $validator->errors()], 400)->header("Content-Type", "application/json");
            }
        } catch (\Throwable $th) {
            return response(["error" => $th->getMessage()], 500)->header("Content-Type", "application/json");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Todo $todo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'created_by' => 'required',
            ]);

            if (!$validator->fails()) {

                if ($request->hasFile('imageFile')) {

                    $path_name = substr($todo->path_name, 9);
                    Storage::disk('public')->delete($path_name);

                    $response = FileUploader::store($request->file('imageFile'), ['disk' => 'public']);
                    $path_name = "/storage/" . $response['path'];
                    $todo->path_name = $path_name;
                }

                $todo->title = $request->title;
                $todo->created_by = $request->created_by;
                $todo->description = $request->description;
                $todo->save();

                return response(["data" => $todo], 200)->header("Content-Type", "application/json");
            } else {
                return response(["error" => $validator->errors()], 400)->header("Content-Type", "application/json");
            }
        } catch (\Throwable $th) {
            return response(["error" => $th->getMessage()], 500)->header("Content-Type", "application/json");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        try {
            $path_name = substr($todo->path_name, 9);
            Storage::disk('public')->delete($path_name);
            $todo->delete();
            return response(["data" => $todo], 200)->header("Content-Type", "application/json");
        } catch (\Throwable $th) {
            return response(["error" => $th->getMessage()], 500)->header("Content-Type", "application/json");
        }
    }
}
