<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    
    public function index()
    {
        $employees = Employee::all();
        return response()->json($employees);
    }

    public function store(Request $request)
    {

        // Get all input data, including extra fields
        $allData = $request->all();

        // Extract only the relevant fields for the database
        $dbFields = [
            'first_name' => $allData['first_name'] ?? null,
            'last_name' => $allData['first_name'] ?? null,
            'email' => $allData['email'] ?? null,
            'department_id' => $allData['department_id'] ?? null,
            'address_type' => $allData['address_type'] ?? 'primary',
            'employee_address_id' => $allData['employee_address_id'] ?? 0,
            'address' => $allData['address'] ?? null
            // Add more fields as needed
        ];

        // Validation rules
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'department_id' => [
                'required',
                'exists:departments,id',
                Rule::unique('employees')->where(function ($query) use ($request) {
                    return $query->where('department_id', $request->input('department_id'));
                }),
            ],
            'address_type' => 'in:primary,secondary',
            'address' => 'nullable|string'
        ];
        
        // Custom error messages
        $messages = [
            'first_name.required' => 'First name field is required.',
            'last_name.required' => 'Last name field is required.',
            'department_id.required' => 'Pass department id basis your allotment as mentioned like 1 for HR,2-HOD,3-IT,4-FINANCE',
        ];
        
        // If department_id is provided, check its existence
        if ($request->has('department_id')) {
            Department::findOrFail($request->input('department_id'));
        }
        
        // If email is provided, check for existence
        if ($request->has('email')) {
            Employee::where('email', $request->input('email'))->firstOrFail();
        }
        dd('4523266754');
        if ($request->has('address')) {
            // Create a new address
            $address = Address::create([
                'address' => $dbFields['address'],
                'address_type' => $dbFields['address_type'],
                'employee_id' => 0
            ]);
            $dbFields['employee_address_id'] = $address->id;
            
        }
        
        

        // Validate the request
        $validator = validator($dbFields, $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $employee = Employee::create($request->all());
        
        // Update the employee's address_id with the address ID
        $address->update(['employee_id' => $employee->id]);

        return response()->json($employee, 201);
    }

    public function show(Employee $employee)
    {
        return response()->json($employee);
    }


    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string',
            'department_id' => [
                'required',
                'exists:departments,id',
                Rule::unique('employees')->where(function ($query) use ($request, $employee) {
                    return $query->where('department_id', $request->input('department_id'))
                        ->where('id', '!=', $employee->id);
                }),
            ],
            'address_type' => 'nullable|in:primary,secondary',
            'address' => 'nullable|string',
        ]);

        $employee->update($request->all());

        return response()->json($employee);
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return response()->json(null, 204);
    }
}
