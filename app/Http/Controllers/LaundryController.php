<?php




namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
class LaundryController extends Controller
{
    public function __construct()
    {
     //   $this->middleware(['auth', 'role:laundry']);
    }

    public function index()
    {

    }
}
