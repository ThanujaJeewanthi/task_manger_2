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
        // Get clients where company_id is equal to the current user's company_id and paginate
        $clients = Client::where('company_id', $companyId)
            ->paginate(10);

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $clientData = [
            'name' => $request->name,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'company_id' => Auth::user()->company_id,
            'created_by' => Auth::id()
        ];

        Client::create($clientData);

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        // Update client
        $clientData = [
            'name' => $request->name,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'company_id' => Auth::user()->company_id,
            'updated_by' => Auth::id()
        ];

        // Handle active status
        if ($request->has('active')) {
            $clientData['active'] = true;
        } else {
            $clientData['active'] = false;
        }

        $client->update($clientData);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->update(['active' => false, 'updated_by' => Auth::id()]);

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }
}
