<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\Label;
use App\Http\Requests\StoreTicketRequest;
use App\Mail\TicketAssignedAgent;
use App\Mail\TicketAssignedPartner;
use App\Mail\TicketClosed;
use App\Mail\TicketComment;
use App\Mail\TicketLogged;
use App\Mail\TicketResolved;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    function getTicketStats(Carbon $start, Carbon $end)
    {
        $query = Ticket::whereBetween('created_at', [$start, $end]);
        return [
            'total' => (clone $query)->count(),
            'open' => (clone $query)->open()->count(),
            'closed' => (clone $query)->closed()->count(),
            'resolved' => (clone $query)->resolved()->count(),
        ];
    }

    public function dashboard()
    {
        $baseQuery = Ticket::query();

        $now = Carbon::now();

        $dailyStats = $this->getTicketStats($now->copy()->startOfDay(), $now->copy()->endOfDay());
        $weeklyStats = $this->getTicketStats($now->copy()->startOfWeek(), $now->copy()->endOfWeek());
        $monthlyStats = $this->getTicketStats($now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $yearlyStats = $this->getTicketStats($now->copy()->startOfYear(), $now->copy()->endOfYear());
        $ticketDataOverTime = $baseQuery->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'total' => $item->total,
                ];
            });
        return view('tickets.dashboard', [
            'totalTickets' => (clone $baseQuery)->count(),
            'openTickets' => (clone $baseQuery)->open()->count(),
            'resolvedTickets' => (clone $baseQuery)->resolved()->count(),
            'highPriorityTickets' => (clone $baseQuery)->where('priority', 'high')->count(),
            'lockedTickets' => (clone $baseQuery)->where('is_locked', true)->count(),
            'closedTickets' => (clone $baseQuery)->closed()->count(),

            'ticketDataOverTime' => $ticketDataOverTime,
            'dailyStats' => $dailyStats,
            'weeklyStats' => $weeklyStats,
            'monthlyStats' => $monthlyStats,
            'yearlyStats' => $yearlyStats,
        ]);
    }

    // use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = !auth()->user()->partner_id
            ? Ticket::query()
            : auth()->user()->tickets();

        // Apply relationships
        $query->with(['user', 'categories', 'labels']);

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('message', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Apply filters only if they exist and user is admin
        if (!auth()->user()->partner_id) {
            $filters = request()->only(['status', 'priority', 'category']);

            if (!empty(array_filter($filters))) {
                $query->filter($filters);
            }
        }

        // Get paginated results
        $tickets = $query->latest()->paginate(15);
        $categories = Category::visible()->get();
        $labels = Label::visible()->get();
        return view('tickets.index', compact('tickets', 'categories', 'labels'));
    }

    public function create()
    {
        $categories = Category::visible()->get();
        $labels = Label::visible()->get();

        return view('tickets.create', compact('categories', 'labels'));
    }

    public function store(StoreTicketRequest $request)
    {
        $ticket = auth()->user()->tickets()->create([
            'title' => $request->title,
            'message' => $request->message,
        ]);
        if (app()->isProduction()) {
            $userEmail = $ticket->user->email;
            $email = Mail::to($userEmail);
            $email->send(new TicketLogged($ticket));
        } else {
            Log::info($ticket->status . ' Email sent');
        }
        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket created successfully!');
    }

    public function show(Ticket $ticket)
    {
        $ticket->load([
            'messages' => function ($query) {
                $query->latest();
            },
            'messages.user',
            'categories',
            'labels'
        ]);
        $agents = Agent::latest()->get();
        return view('tickets.show', compact('ticket', 'agents'));
    }

    public function addComment(Request $request, Ticket $ticket)
    {
        $request->validate(['message' => 'required|string']);
        $ticket->messages()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);
        if (app()->isProduction()) {
            $userEmail = $ticket->user->email;
            Mail::to($userEmail)
                ->send(new TicketComment($ticket));

            if ($ticket->agent) {
                $agentEmail = $ticket->agent->email;
                Mail::to($agentEmail)
                    ->send(new TicketComment($ticket));
            }
        } else {
            Log::info('New comment email sent');
        }
        return back()->with('success', 'Comment added!');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);
        $ticket->update($validated);
        if (app()->isProduction()) {
            $userEmail = $ticket->user->email;
            $email = Mail::to($userEmail);
            if ($ticket->status === 'closed') {
                $email->send(new TicketClosed($ticket));
            } elseif ($ticket->status === 'resolved') {
                $email->send(new TicketResolved($ticket));
            }
        } else {
            Log::info($ticket->status . ' Email sent');
        }
        return back()->with('success', 'Ticket status updated!');
    }

    public function assign(Request $request, Ticket $ticket)
    {
        $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'priority' => 'nullable|in:low,medium,high',
        ]);
        $ticket->update([
            'assigned_to' => $request->agent_id,
            'priority' => $request->priority
        ]);
        if (app()->isProduction()) {
            $userEmail = $ticket->user->email;
            Mail::to($userEmail)
                ->send(new TicketAssignedPartner($ticket));

            $agentEmail = $ticket->agent->email;
            Mail::to($agentEmail)
                ->send(new TicketAssignedAgent($ticket));
        } else {
            Log::info('Assignment Email sent');
        }
        return back()->with('success', 'Ticket assigned!');
    }
}
