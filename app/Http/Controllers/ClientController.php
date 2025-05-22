<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;

        // Check if company has clients enabled
        if (!Auth::user()->company->has_clients) {
            abort(403, 'Your company does not have client management enabled.');
        }

        $clients = Client::where('company_id', $companyId)
            ->where('active', true)
            ->paginate(10);

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        // Check if company has clients enabled
        if (!Auth::user()->company->has_clients) {
            abort(403, 'Your company does not have client management enabled.');
        }

        return view('clients.create');
    }

    public function store(Request $request)
    {
        // Check if company has clients enabled
        if (!Auth::user()->company->has_clients) {
            abort(403, 'Your company does not have client management enabled.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        Client::create([
            'company_id' => Auth::user()->company_id,
            'name' => $request->name,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'active' => $request->has('is_active'),
            'created_by' => Auth::id()
        ]);

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        // Check if client belongs to current user's company
        if ($client->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        // Check if client belongs to current user's company
        if ($client->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        // Check if client belongs to current user's company
        if ($client->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        $client->update([
            'name' => $request->name,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'active' => $request->has('is_active'),
            'updated_by' => Auth::id()
        ]);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        // Check if client belongs to current user's company
        if ($client->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $client->update(['active' => false, 'updated_by' => Auth::id()]);
        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }
}
