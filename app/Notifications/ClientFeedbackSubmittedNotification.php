<?php

namespace App\Notifications;

use App\Models\ClientFeedback;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientFeedbackSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $feedback;

    /**
     * Create a new notification instance.
     */
    public function __construct(ClientFeedback $feedback)
    {
        $this->feedback = $feedback;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $contract = $this->feedback->contract;
        $clientName = $this->feedback->is_anonymous ? 'Anonymous Client' : ($contract->client->name ?? 'Client');
        
        $ratingText = $this->getRatingText($this->feedback->overall_rating);
        $recommendationText = $this->getRecommendationText($this->feedback->recommendation_likelihood);

        return (new MailMessage)
            ->subject('New Client Feedback Received - ' . $contract->contract_number)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new client feedback has been submitted for contract **' . $contract->contract_number . '**.')
            ->line('**Client:** ' . $clientName)
            ->line('**Contractor:** ' . ($contract->contractor->name ?? 'N/A'))
            ->line('**Overall Rating:** ' . $this->feedback->overall_rating . '/5 (' . $ratingText . ')')
            ->line('**Recommendation:** ' . $this->feedback->recommendation_likelihood . '/10 (' . $recommendationText . ')')
            ->action('View Feedback Details', route('admin.feedback.show', $this->feedback))
            ->line('Please review this feedback to identify areas for improvement.')
            ->salutation('Best regards, GDC System');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $contract = $this->feedback->contract;
        $clientName = $this->feedback->is_anonymous ? 'Anonymous Client' : ($contract->client->name ?? 'Client');
        
        return [
            'type' => 'client_feedback',
            'title' => 'New Client Feedback Received',
            'message' => 'Client feedback submitted for contract ' . $contract->contract_number,
            'feedback_id' => $this->feedback->id,
            'contract_id' => $contract->id,
            'contract_number' => $contract->contract_number,
            'client_name' => $clientName,
            'contractor_name' => $contract->contractor->name ?? 'N/A',
            'overall_rating' => $this->feedback->overall_rating,
            'recommendation_likelihood' => $this->feedback->recommendation_likelihood,
            'is_anonymous' => $this->feedback->is_anonymous,
            'submitted_at' => $this->feedback->submitted_at,
            'action_url' => route('admin.feedback.show', $this->feedback),
            'priority' => $this->getPriorityLevel(),
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'client_feedback';
    }

    /**
     * Get rating text based on rating value
     */
    private function getRatingText($rating): string
    {
        if ($rating >= 4.5) return 'Excellent';
        if ($rating >= 4.0) return 'Very Good';
        if ($rating >= 3.5) return 'Good';
        if ($rating >= 3.0) return 'Satisfactory';
        if ($rating >= 2.5) return 'Fair';
        return 'Poor';
    }

    /**
     * Get recommendation text based on likelihood value
     */
    private function getRecommendationText($likelihood): string
    {
        if ($likelihood >= 9) return 'Definitely';
        if ($likelihood >= 7) return 'Very Likely';
        if ($likelihood >= 5) return 'Likely';
        if ($likelihood >= 3) return 'Maybe';
        return 'Unlikely';
    }

    /**
     * Get priority level based on rating
     */
    private function getPriorityLevel(): string
    {
        $rating = $this->feedback->overall_rating;
        $recommendation = $this->feedback->recommendation_likelihood;

        // High priority for low ratings or low recommendations
        if ($rating <= 2 || $recommendation <= 3) {
            return 'high';
        }
        
        // Medium priority for average ratings
        if ($rating <= 3 || $recommendation <= 5) {
            return 'medium';
        }
        
        // Low priority for good ratings
        return 'low';
    }
} 