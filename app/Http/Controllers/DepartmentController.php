<?php

namespace App\Http\Controllers;

use App\Models\Department; // Model for Department

use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    //

    public function index()
    {
        return Department::all();
    }

    public function show($id)
    {
        return Department::findOrFail($id);
    }

    public function store(Request $request)
    {
        try{
        // Validation rules
        $rules = [
            'name' => 'required|string|max:255|unique:departments', // Ensure 'name' is unique in the 'departments' table
        ];

        // Custom error messages
        $messages = [
            'name.required' => 'The name field is required.',
            'name.unique' => 'A department with this name already exists.',
        ];

        // Validate the request
        $validator = validator($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        // Create the department
        $department = Department::create([
            'name' => $request->input('name')
        ]);
        }catch(Exception $e){
            dd($e->getMessage());
        }
        // Return a success response
        return response()->json(['message' => 'Department created successfully', 'data' => $department], 201);
    }

    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);
        $department->update($request->all());
        return $department;
    }

    public function destroy(Request $request, $id = null)
    {
        // If no ID is provided, return an error response
        if ($id === null) {
            return response()->json(['error' => 'Department ID is required for deletion.'], 400);
        }

          // Validation rules
          $rules = [
            'id' => 'exists:departments,id', // Check if the department with the given ID exists
        ];

        // Custom error messages
        $messages = [
            'id.exists' => 'The selected department does not exist.',
        ];

        // Validate the request
        $request->validate($rules, $messages);

        // Find the department by ID
        $department = Department::find($id);

        // Check if the department was found
        if (!$department) {
            return response()->json(['error' => 'The selected department does not exist.'], 404);
        }

        // Continue with the department deletion
        $department->delete();

        return response()->json(['message' => 'Department deleted successfully'], 200);
        
    }
}
