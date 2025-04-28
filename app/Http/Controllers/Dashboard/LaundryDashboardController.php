<?php




namespace App\Http\Controllers\Dashboard;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
class LaundryDashboardController extends Controller
{


    public function index()
    {
        $user = Auth::user();
        // Get laundry-specific data here

        return view('dashboard.laundry', [
            'user' => $user,
            // Add more laundry-specific data
        ]);
    }
}
