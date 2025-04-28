<?php


namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function __construct()
    {
    //    $this->middleware(['auth', 'role:client']);
    }

    public function index()
    {

    }
}
