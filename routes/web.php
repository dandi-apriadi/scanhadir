use App\Livewire\AttendanceScanner;
use App\Livewire\StudentDashboard;
use App\Livewire\StudentLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', StudentLogin::class)->name('login');
Route::get('/scan', AttendanceScanner::class)->name('scan');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', StudentDashboard::class)->name('dashboard');
    
    Route::post('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');
});
