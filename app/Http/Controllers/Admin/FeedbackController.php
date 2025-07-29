<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = ClientFeedback::with(['contract', 'contract.contractor', 'contract.client', 'user'])
            ->whereNotNull('submitted_at')
            ->orderBy('submitted_at', 'desc')
            ->paginate(20);

        // Calculate statistics
        $stats = [
            'total_feedback' => ClientFeedback::whereNotNull('submitted_at')->count(),
            'average_rating' => ClientFeedback::whereNotNull('submitted_at')->avg('overall_rating'),
            'anonymous_count' => ClientFeedback::whereNotNull('submitted_at')->where('is_anonymous', true)->count(),
            'recommendation_avg' => ClientFeedback::whereNotNull('submitted_at')->avg('recommendation_likelihood'),
        ];

        // Get rating distribution
        $ratingDistribution = ClientFeedback::whereNotNull('submitted_at')
            ->select('overall_rating', DB::raw('count(*) as count'))
            ->groupBy('overall_rating')
            ->orderBy('overall_rating', 'desc')
            ->get();

        return view('admin.feedback.index', compact('feedbacks', 'stats', 'ratingDistribution'));
    }

    public function show(ClientFeedback $feedback)
    {
        $feedback->load(['contract', 'contract.contractor', 'contract.client', 'user']);
        
        return view('admin.feedback.show', compact('feedback'));
    }

    public function analytics()
    {
        // Overall statistics
        $stats = [
            'total_feedback' => ClientFeedback::whereNotNull('submitted_at')->count(),
            'average_rating' => round(ClientFeedback::whereNotNull('submitted_at')->avg('overall_rating'), 2),
            'anonymous_count' => ClientFeedback::whereNotNull('submitted_at')->where('is_anonymous', true)->count(),
            'recommendation_avg' => round(ClientFeedback::whereNotNull('submitted_at')->avg('recommendation_likelihood'), 2),
        ];

        // Rating breakdown by category
        $categoryRatings = [
            'communication' => round(ClientFeedback::whereNotNull('submitted_at')->avg('communication_rating'), 2),
            'quality' => round(ClientFeedback::whereNotNull('submitted_at')->avg('quality_rating'), 2),
            'timeliness' => round(ClientFeedback::whereNotNull('submitted_at')->avg('timeliness_rating'), 2),
            'professionalism' => round(ClientFeedback::whereNotNull('submitted_at')->avg('professionalism_rating'), 2),
            'value' => round(ClientFeedback::whereNotNull('submitted_at')->avg('value_rating'), 2),
        ];

        // Monthly feedback trend
        $monthlyTrend = ClientFeedback::whereNotNull('submitted_at')
            ->select(DB::raw('DATE_FORMAT(submitted_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Top contractors by rating
        $topContractors = ClientFeedback::whereNotNull('submitted_at')
            ->join('contracts', 'client_feedback.contract_id', '=', 'contracts.id')
            ->join('parties', 'contracts.contractor_id', '=', 'parties.id')
            ->select('parties.name', 'parties.company_name', DB::raw('avg(client_feedback.overall_rating) as avg_rating'), DB::raw('count(*) as feedback_count'))
            ->groupBy('parties.id', 'parties.name', 'parties.company_name')
            ->having('feedback_count', '>=', 1)
            ->orderBy('avg_rating', 'desc')
            ->limit(10)
            ->get();

        // Recent feedback
        $recentFeedback = ClientFeedback::with(['contract', 'contract.contractor', 'contract.client'])
            ->whereNotNull('submitted_at')
            ->orderBy('submitted_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.feedback.analytics', compact(
            'stats', 
            'categoryRatings', 
            'monthlyTrend', 
            'topContractors', 
            'recentFeedback'
        ));
    }

    public function export()
    {
        $feedbacks = ClientFeedback::with(['contract', 'contract.contractor', 'contract.client', 'user'])
            ->whereNotNull('submitted_at')
            ->orderBy('submitted_at', 'desc')
            ->get();

        $filename = 'client_feedback_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($feedbacks) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'Contract Number', 'Client', 'Contractor', 'Overall Rating',
                'Communication', 'Quality', 'Timeliness', 'Professionalism', 'Value',
                'Recommendation Likelihood', 'Comments', 'Anonymous', 'Submitted Date'
            ]);

            // CSV data
            foreach ($feedbacks as $feedback) {
                fputcsv($file, [
                    $feedback->id,
                    $feedback->contract->contract_number ?? 'N/A',
                    $feedback->contract->client->name ?? 'N/A',
                    $feedback->contract->contractor->name ?? 'N/A',
                    $feedback->overall_rating,
                    $feedback->communication_rating,
                    $feedback->quality_rating,
                    $feedback->timeliness_rating,
                    $feedback->professionalism_rating,
                    $feedback->value_rating,
                    $feedback->recommendation_likelihood,
                    $feedback->comments,
                    $feedback->is_anonymous ? 'Yes' : 'No',
                    $feedback->submitted_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 