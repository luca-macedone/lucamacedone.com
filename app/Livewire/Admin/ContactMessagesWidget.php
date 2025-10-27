<?php

namespace App\Livewire\Admin;

use App\Models\ContactMessage;
use Livewire\Component;
use Carbon\Carbon;

class ContactMessagesWidget extends Component
{
    public $unreadCount = 0;
    public $todayCount = 0;
    public $weekCount = 0;
    public $recentMessages = [];
    public $chartData = [];

    public function mount()
    {
        $this->loadStats();
        $this->loadRecentMessages();
        $this->loadChartData();
    }

    private function loadStats()
    {
        $this->unreadCount = ContactMessage::where('status', 'unread')
            ->where('is_spam', false)
            ->count();

        $this->todayCount = ContactMessage::whereDate('created_at', today())
            ->where('is_spam', false)
            ->count();

        $this->weekCount = ContactMessage::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()
        ])
            ->where('is_spam', false)
            ->count();
    }

    private function loadRecentMessages()
    {
        $this->recentMessages = ContactMessage::with([])
            ->where('is_spam', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function loadChartData()
    {
        $days = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = ContactMessage::whereDate('created_at', $date)
                ->where('is_spam', false)
                ->count();

            $days->push([
                'date' => $date->format('d/m'),
                'count' => $count
            ]);
        }

        $this->chartData = $days->toArray();
    }

    public function render()
    {
        return view('livewire.admin.contact-messages-widget');
    }
}

// ============================================
// Vista Blade: contact-messages-widget.blade.php
// ============================================
