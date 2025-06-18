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
            $roles = ['procurement', 'warehousing', 'contractor'];
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
        $rules = [
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:procurement,warehousing,contractor'],
        ];
        // Contractor-specific validation
        if ($request->role === 'contractor') {
            $rules = array_merge($rules, [
                'company_name' => ['nullable', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:255'],
                'street' => ['required', 'string', 'max:255'],
                'barangay' => ['required', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:255'],
                'state' => ['required', 'string', 'max:255'],
                'postal' => ['required', 'string', 'max:255'],
        ]);
        }
        $request->validate($rules);
        DB::beginTransaction();
        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 'employee',
                'name' => $request->first_name . ' ' . $request->last_name,
                'role' => $request->role,
            ]);
            $employeeData = [
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'username' => $request->username,
                'role' => $request->role,
            ];
            if ($request->role === 'contractor') {
                $employeeData = array_merge($employeeData, [
                    'company_name' => $request->company_name,
                    'phone' => $request->phone,
                    'street' => $request->street,
                    'barangay' => $request->barangay,
                    'city' => $request->city,
                    'state' => $request->state,
                    'postal' => $request->postal,
            ]);
            }
            Employee::create($employeeData);
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
        $rules = [
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $employee->user_id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $employee->user_id],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:procurement,warehousing,contractor'],
        ];
        if ($request->role === 'contractor') {
            $rules = array_merge($rules, [
                'company_name' => ['nullable', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:255'],
                'street' => ['required', 'string', 'max:255'],
                'barangay' => ['required', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:255'],
                'state' => ['required', 'string', 'max:255'],
                'postal' => ['required', 'string', 'max:255'],
        ]);
        }
        $request->validate($rules);
        DB::beginTransaction();
        try {
            $employee->user->update([
                'username' => $request->username,
                'email' => $request->email,
                'name' => $request->first_name . ' ' . $request->last_name,
                'role' => $request->role,
            ]);
            $employeeData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'username' => $request->username,
                'role' => $request->role,
            ];
            if ($request->role === 'contractor') {
                $employeeData = array_merge($employeeData, [
                    'company_name' => $request->company_name,
                    'phone' => $request->phone,
                    'street' => $request->street,
                    'barangay' => $request->barangay,
                    'city' => $request->city,
                    'state' => $request->state,
                    'postal' => $request->postal,
                ]);
            } else {
                // Clear contractor fields if role is not contractor
                $employeeData = array_merge($employeeData, [
                    'company_name' => null,
                    'phone' => null,
                    'street' => null,
                    'barangay' => null,
                    'city' => null,
                    'state' => null,
                    'postal' => null,
                ]);
            }
            $employee->update($employeeData);
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
            'csv_file' => 'required|file|mimes:csv,txt',
            'type' => 'required|in:employee,contractor'
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('csv_file');
            $csvData = array_map('str_getcsv', file($file->getPathname()));
            
            // Remove header row
            $headers = array_shift($csvData);

            $type = $request->input('type');

            foreach ($csvData as $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Unified import: Role, First Name, Last Name, Username, Email, Password, [Company Name, Phone, Street, City, Province, Zip Code]
                $role = $row[0] ?? null;
                $firstName = $row[1] ?? null;
                $lastName = $row[2] ?? null;
                $username = $row[3] ?? null;
                $email = $row[4] ?? null;
                $password = $row[5] ?? null;
                $companyName = $row[6] ?? null;
                $phone = $row[7] ?? null;
                $street = $row[8] ?? null;
                $city = $row[9] ?? null;
                $province = $row[10] ?? null;
                $zipCode = $row[11] ?? null;

                // Basic validation for critical fields
                if (!$firstName || !$lastName || !$email || !$username || !$password || !$role) {
                    \Log::warning('Skipping row due to missing critical data:', $row);
                    continue;
                }

                // Only allow valid roles
                if (!in_array($role, ['procurement', 'warehousing', 'contractor'])) {
                    \Log::warning('Skipping row due to invalid role:', $row);
                    continue;
                }

                // Check if user already exists by email or username
                $existingUser = User::where('email', $email)->orWhere('username', $username)->first();
                if ($existingUser) {
                    \Log::info('Skipping existing user during import:', ['email' => $email, 'username' => $username]);
                    continue;
                }

                // Create user
                $user = User::create([
                    'username' => $username,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'user_type' => 'employee',
                    'name' => $firstName . ' ' . $lastName,
                ]);

                // Prepare employee data
                $employeeData = [
                    'user_id' => $user->id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'role' => $role,
                    'username' => $username,
                    'email' => $email,
                ];
                if ($role === 'contractor') {
                    $employeeData = array_merge($employeeData, [
                        'company_name' => $companyName,
                        'phone' => $phone,
                        'street' => $street,
                        'city' => $city,
                        'state' => $province,
                        'postal' => $zipCode,
                    ]);
                }
                Employee::create($employeeData);
            }

            DB::commit();
            return redirect()->route('information-management.index')
                           ->with('success', 'Data imported successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Import failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to import data: ' . $e->getMessage());
        }
    }

    public function template(Request $request)
    {
        $type = $request->input('type', 'employee'); // Default to employee

        $headers = [];
        $sampleRow = [];
        $filename = '';

        if ($type === 'employee') {
            $headers = [
                'Role',
                'First Name',
                'Last Name',
                'Username',
                'Email',
                'Password',
            ];

            $sampleRow = [
                'procurement',
                'John',
                'Doe',
                'johndoe',
                'john.doe@example.com',
                'password123',
            ];
            $filename = 'employee_template.csv';
        } else if ($type === 'contractor') {
            // Contractor specific headers and sample data will go here
            // For now, it can be empty or have placeholder values
            $headers = [
                'First Name',
                'Last Name',
                'Email',
                'Username',
                'Password',
                'Company Name',
                'Phone',
                'Street',
                'City',
                'Province',
                'Zip Code',
            ];
            $sampleRow = [
                'Jane',
                'Smith',
                'jane.smith@example.com',
                'janesmith',
                'password123',
                'ABC Construction',
                '09123456789',
                '123 Main St',
                'Quezon City',
                'Metro Manila',
                '1100',
            ];
            $filename = 'contractor_template.csv';
        } else {
            // Fallback for unknown types
            return back()->with('error', 'Invalid template type requested.');
        }

        $output = fopen('php://temp', 'w+');
        fputcsv($output, $headers);

        // Add a sample row
        fputcsv($output, $sampleRow);

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
} 