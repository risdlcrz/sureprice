<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login.form');
        }

        $user = Auth::user();
        
        if (!$user || $user->user_type !== 'admin') {
            if ($user && $user->user_type === 'company') {
                return redirect()->route('pending.approval');
            }
            return redirect()->route('login.form')->with('error', 'Admin privileges required');
        }

        // Log every admin page visit
        try {
            date_default_timezone_set('Asia/Manila');
            $route = $request->route();
            $routeName = $route ? $route->getName() : null;
            $method = $request->method();
            $userName = $user->name ?? 'An admin';
            $friendlyDescriptions = [
                'admin.dbadmin' => 'viewed the Admin Dashboard',
                'admin.procurement' => 'opened the Project & Procurement Dashboard',
                'admin.analytics' => 'viewed the Analytics Dashboard',
                'admin.logs' => 'viewed the Administrator Logs',
                'admin.notification' => 'checked the Notification Hub',
                'admin.supplier-rankings' => 'viewed Supplier Rankings',
                'admin.price-analysis' => 'viewed Price Analysis',
                'admin.budget-allocation' => 'viewed Budget Allocation',
                'admin.transactions' => 'viewed Transactions',
                'admin.companies.pending' => 'viewed Pending Companies',
                'admin.companies.show' => 'viewed Company Details',
                'admin.companies.approve' => 'approved a Company',
                'admin.companies.reject' => 'rejected a Company',
                'admin.materials.index' => 'viewed the Materials List',
                'admin.materials.create' => 'started creating a new Material',
                'admin.materials.edit' => 'edited a Material',
                'admin.materials.show' => 'viewed Material Details',
                'admin.materials.suppliers.update' => 'updated Material Suppliers',
                'admin.budgets.index' => 'viewed Budgets',
                'admin.budgets.show' => 'viewed Budget Details',
                'admin.budgets.export' => 'exported a Budget',
                'admin.budgets.alerts' => 'checked Budget Alerts',
                'admin.suppliers.pending-updates' => 'viewed Pending Supplier Updates',
                'admin.suppliers.review-update' => 'reviewed a Supplier Update',
                'admin.suppliers.approve-update' => 'approved a Supplier Update',
                'admin.suppliers.reject-update' => 'rejected a Supplier Update',
                // Add more as needed
            ];
            $description = $routeName && isset($friendlyDescriptions[$routeName])
                ? "$userName {$friendlyDescriptions[$routeName]}"
                : ($routeName ? ("$userName visited the page: " . ucwords(str_replace(['.', '-'], [' ', ' '], $routeName))) : ("$userName visited a page (URL: " . $request->path() . ")"));
            
            \App\Models\Activity::create([
                'user_id' => $user->id,
                'action' => 'viewed',
                'description' => $description,
                'model_type' => null,
                'model_id' => null,
            ]);
        } catch (\Throwable $e) {
            // Don't break the request if logging fails
        }

        return $next($request);
    }
}
