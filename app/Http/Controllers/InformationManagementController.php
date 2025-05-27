<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

class InformationManagementController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'employee');
        $role = $request->get('role', 'all');
        $search = $request->get('search', '');

        if ($type === 'employee') {
            $query = Employee::with('user')
                ->when($role !== 'all', function ($query) use ($role) {
                    return $query->where('role', $role);
                })
                ->when($search, function ($query) use ($search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhereHas('user', function ($uq) use ($search) {
                              $uq->where('username', 'like', "%{$search}%");
                          });
                    });
                });

            $items = $query->paginate(10);
            $roles = ['procurement', 'warehousing'];
        } else {
            $query = Company::with('user')
                ->when($search, function ($query) use ($search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('company_name', 'like', "%{$search}%")
                          ->orWhere('contact_person', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhereHas('user', function ($uq) use ($search) {
                              $uq->where('username', 'like', "%{$search}%");
                          });
                    });
                });

            $items = $query->paginate(10);
            $roles = [];
        }

        return view('admin.information-management', compact('items', 'type', 'role', 'search', 'roles'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:procurement,warehousing'],
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 'employee',
                'name' => $request->first_name . ' ' . $request->last_name,
            ]);

            Employee::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'username' => $request->username,
                'role' => $request->role,
            ]);

            DB::commit();
            return redirect()->route('information-management.index')
                           ->with('success', 'Employee created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create employee: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $employee->user_id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $employee->user_id],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:procurement,warehousing'],
        ]);

        DB::beginTransaction();

        try {
            $employee->user->update([
                'username' => $request->username,
                'email' => $request->email,
                'name' => $request->first_name . ' ' . $request->last_name,
            ]);

            $employee->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'username' => $request->username,
                'role' => $request->role,
            ]);

            DB::commit();
            return redirect()->route('information-management.index')
                           ->with('success', 'Employee updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update employee: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $employee->user->delete();
            $employee->delete();
            
            DB::commit();
            return redirect()->route('information-management.index')
                           ->with('success', 'Employee deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete employee: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('csv_file');
            $csvData = array_map('str_getcsv', file($file->getPathname()));
            
            // Remove header row
            $headers = array_shift($csvData);

            foreach ($csvData as $row) {
                // Create user first
                $user = User::create([
                    'name' => $row[0] . ' ' . $row[1], // first_name + last_name
                    'email' => $row[2],
                    'username' => $row[3],
                    'password' => Hash::make($row[4]), // You might want to generate random passwords
                    'user_type' => 'employee',
                ]);

                // Create employee
                Employee::create([
                    'user_id' => $user->id,
                    'first_name' => $row[0],
                    'last_name' => $row[1],
                    'email' => $row[2],
                    'username' => $row[3],
                    'role' => $row[5],
                ]);
            }

            DB::commit();
            return back()->with('success', 'Employees imported successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error importing employees: ' . $e->getMessage());
        }
    }

    public function template()
    {
        $headers = [
            'First Name',
            'Last Name',
            'Email',
            'Username',
            'Password',
            'Role (procurement/warehousing)'
        ];

        $output = fopen('php://temp', 'w+');
        fputcsv($output, $headers);

        // Add a sample row
        fputcsv($output, [
            'John',
            'Doe',
            'john.doe@example.com',
            'johndoe',
            'password123',
            'procurement'
        ]);

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="employee_template.csv"');
    }
} 