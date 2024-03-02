<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Addresses;
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
            'salary' => $allData['salary'] ?? '10000',
            'hire_date' => $allData['hire_date'] ?? date('Y-m-d'),
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
            'address' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'email' => 'required|email|unique:employees,email',
            'department_id' => [
                'required',
                'exists:departments,id'
            ],
            'address_type' => 'in:primary,secondary'
        ];
        
        // Custom error messages
        $messages = [
            'first_name.required' => 'First name field is required.',
            'last_name.required' => 'Last name field is required.',
            'hire_date.required' => 'Hire date is required.',
            'address.required' => 'Primary address is required.',
            'department_id.required' => 'Pass department id basis your allotment as mentioned like 1 for HR,2-HOD,3-IT,4-FINANCE',
        ];
        
        $department = Department::find($request->input('department_id'));

        if (!$department) {
            // Handle the case where the department doesn't exist
            return response()->json(['error' => 'Department does not exist. please select existing'], 404);
        }
       
        // If email is provided, check for existence
        $check_employee = Employee::where('email', '=', $request->input('email'))->get();
        if (!$check_employee->isEmpty()) {
            // Handle the case where the department doesn't exist
            return response()->json(['error' => 'Employee already exist'], 404);
        }
        if ($request->has('address')) {
            // Create a new address
            $address = Addresses::create([
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

        unset($dbFields['address_type']);
        unset($dbFields['address']);
        // dd($dbFields);
        $employee = Employee::create($dbFields);
        
        // Update the employee's address_id with the address ID
        // $address->update(['employee_id' => $employee->id]);
        DB::table('employee_addresses')->where('id',$address->id)->update(['employee_id' => $employee->id]);

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
