<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiderController extends Controller
{
    public function __construct()
    {
    //    $this->middleware(['auth', 'role:client']);
    }

    public function index()
    {

    }
}
