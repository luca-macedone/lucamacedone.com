<?php

namespace App\Livewire\Admin;

use App\Models\ContactMessage;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContactMessages extends Component
{
    use WithPagination;

    public $search = '';
    public $status = 'all';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 15;
    public $selectedMessages = [];
    public $selectAll = false;
    public $showSpam = false;
    public $dateFrom = '';
    public $dateTo = '';

    // Statistiche
    public $stats = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'all'],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'showSpam' => ['except' => false],
    ];

    protected $listeners = [
        'messageDeleted' => 'refreshList',
        'messageUpdated' => 'refreshList',
        'refreshStats' => 'loadStats'
    ];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->stats = [
            'total' => ContactMessage::count(),
            'unread' => ContactMessage::where('status', 'unread')->where('is_spam', false)->count(),
            'read' => ContactMessage::where('status', 'read')->count(),
            'replied' => ContactMessage::where('status', 'replied')->count(),
            'spam' => ContactMessage::where('is_spam', true)->count(),
            'today' => ContactMessage::whereDate('created_at', today())->where('is_spam', false)->count(),
            'week' => ContactMessage::whereBetween('created_at', [now()->startOfWeek(), now()])->where('is_spam', false)->count(),
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
        $this->selectedMessages = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedMessages = $this->getFilteredQuery()->pluck('id')->toArray();
        } else {
            $this->selectedMessages = [];
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function markAsRead($id = null)
    {
        if ($id) {
            ContactMessage::find($id)->update(['status' => 'read']);
            session()->flash('success', 'Messaggio marcato come letto.');
        } else {
            ContactMessage::whereIn('id', $this->selectedMessages)->update(['status' => 'read']);
            session()->flash('success', count($this->selectedMessages) . ' messaggi marcati come letti.');
        }

        $this->selectedMessages = [];
        $this->selectAll = false;
        $this->loadStats();
    }

    public function markAsUnread($id = null)
    {
        if ($id) {
            ContactMessage::find($id)->update(['status' => 'unread']);
            session()->flash('success', 'Messaggio marcato come non letto.');
        } else {
            ContactMessage::whereIn('id', $this->selectedMessages)->update(['status' => 'unread']);
            session()->flash('success', count($this->selectedMessages) . ' messaggi marcati come non letti.');
        }

        $this->selectedMessages = [];
        $this->selectAll = false;
        $this->loadStats();
    }

    public function markAsSpam($id = null)
    {
        if ($id) {
            ContactMessage::find($id)->update(['is_spam' => true]);
            session()->flash('success', 'Messaggio marcato come spam.');
        } else {
            ContactMessage::whereIn('id', $this->selectedMessages)->update(['is_spam' => true]);
            session()->flash('success', count($this->selectedMessages) . ' messaggi marcati come spam.');
        }

        $this->selectedMessages = [];
        $this->selectAll = false;
        $this->loadStats();
    }

    public function markAsNotSpam($id)
    {
        ContactMessage::find($id)->update(['is_spam' => false]);
        session()->flash('success', 'Messaggio marcato come non spam.');
        $this->loadStats();
    }

    public function deleteMessage($id = null)
    {
        if ($id) {
            ContactMessage::find($id)->delete();
            session()->flash('success', 'Messaggio eliminato.');
        } else {
            $count = count($this->selectedMessages);
            ContactMessage::whereIn('id', $this->selectedMessages)->delete();
            session()->flash('success', $count . ' messaggi eliminati.');
        }

        $this->selectedMessages = [];
        $this->selectAll = false;
        $this->loadStats();
    }

    public function archiveMessages()
    {
        $count = count($this->selectedMessages);
        ContactMessage::whereIn('id', $this->selectedMessages)->update(['status' => 'archived']);
        session()->flash('success', $count . ' messaggi archiviati.');

        $this->selectedMessages = [];
        $this->selectAll = false;
        $this->loadStats();
    }

    public function exportCsv()
    {
        $messages = $this->getFilteredQuery()->get();

        $filename = 'contact_messages_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $columns = ['ID', 'Nome', 'Email', 'Oggetto', 'Messaggio', 'Stato', 'Spam', 'IP', 'Data'];

        $callback = function () use ($messages, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($messages as $message) {
                fputcsv($file, [
                    $message->id,
                    $message->name,
                    $message->email,
                    $message->subject,
                    $message->message,
                    $message->status,
                    $message->is_spam ? 'Sì' : 'No',
                    $message->ip_address,
                    $message->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status = 'all';
        $this->showSpam = false;
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    private function getFilteredQuery()
    {
        $query = ContactMessage::query();

        // Filtro ricerca
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('subject', 'like', '%' . $this->search . '%')
                    ->orWhere('message', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro stato
        if ($this->status !== 'all') {
            if ($this->status === 'spam') {
                $query->where('is_spam', true);
            } else {
                $query->where('status', $this->status)->where('is_spam', false);
            }
        }

        // Filtro spam
        if (!$this->showSpam && $this->status !== 'spam') {
            $query->where('is_spam', false);
        }

        // Filtro date
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return $query;
    }

    public function refreshList()
    {
        $this->loadStats();
    }

    public function render()
    {
        $messages = $this->getFilteredQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.contact-messages', [
            'messages' => $messages
        ])->layout('layouts.app');
    }
}
