<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Auth\Login as NewLogin;
use App\Livewire\Auth\Register as NewRegister;
use App\Livewire\Admin\AdminDashboard;
use App\Http\Controllers\ProfileController;
use App\Livewire\TownOwner\TownOwnerDashboard;
use App\Livewire\TownOwner\ManagePlots;
use App\Livewire\TownOwner\ManageTowns;
use App\Livewire\TownOwner\ManageInstallments;
use App\Models\Installment;
use App\Livewire\TownOwner\ManageEmployees;
use App\Livewire\TownOwner\ManageExpenses;
use App\Livewire\Agent\AgentDashboard;
use App\Livewire\Agent\ManageLeads;
use App\Livewire\Agent\ManageProperties;
use App\Models\Town;
use App\Models\Plot;
use App\Models\Page;
use App\Livewire\PropertyDetails;
use App\Livewire\UserDashboard;
use App\Livewire\Admin\TopUpRequests;
use App\Livewire\Admin\ManageUsers;
use App\Livewire\Admin\ManagePaymentMethods;
use App\Livewire\Admin\AdminManageTowns;
use App\Livewire\Admin\PageManagement;
use App\Livewire\Pages\HousingSchemes;
use App\Livewire\Pages\AvailablePlots;
use App\Livewire\Pages\AboutUs;
use App\Livewire\Pages\ContactUs;
use App\Livewire\Pages\DynamicPage;
use App\Livewire\Pages\ShowHousingScheme;
use App\Livewire\Admin\ManageInquiries;
use App\Livewire\Admin\PropertyManagement;
use App\Livewire\Admin\TownSchemeManagement;
use App\Livewire\Admin\CityManagement;
use App\Livewire\Admin\CreateProperty;
use Illuminate\Support\Facades\Session;
use App\Livewire\Admin\PageAnalytics;
use App\Livewire\Admin\WhatsAppMarketing;
use App\Livewire\Agent\LandCalculator;
use App\Livewire\Agent\BayanaGenerator;
use App\Livewire\Agent\AgencyProfile;
use App\Livewire\PublicAgentPortfolio;
use App\Livewire\Agent\DealerExchange;
use App\Livewire\Agent\PosterMaker;
use App\Livewire\Agent\CommissionLedger;
use App\Livewire\Agent\RegistryTaxCalculator;
use App\Livewire\Admin\ManagePosts;
use App\Livewire\Pages\BlogIndex;
use App\Livewire\Pages\BlogShow;
use App\Livewire\Tools\PropertyLegalScanner;
use App\Livewire\Tools\RegistryTaxCalculator as UserTax;
use App\Livewire\Tools\PatwariLandConverter;
use App\Livewire\Tools\BayanaDeedGenerator;
use App\Livewire\Tools\SocietyNocDirectory;
use App\Livewire\Tools\InheritanceCalculator;
use App\Livewire\Tools\RentAgreementGenerator;
use App\Livewire\Tools\ZakatCalculator;
use App\Livewire\Tools\CvMaker;

// Public Routes
Route::get('/', function () {
    return view('welcome', [
        'towns' => Town::all(),
        'plots' => Plot::where('status', 'available')->get(),
    ])->layout('layouts.front-app');
})->name('home');



Route::middleware(['web'])->group(function () {

    Route::get('/lang/{locale}', function ($locale) {
        if (in_array($locale, ['en', 'ur'])) {
            Session::put('locale', $locale);
        }
        return redirect()->back();
    })->name('lang.switch');
    // Public / Frontend Routes
    Route::get('/page/{slug}', DynamicPage::class)->name('dynamic.page');
    Route::get('/properties/{id}/{slug?}', PropertyDetails::class)->name('property.details');

    Route::get('/housing-schemes', HousingSchemes::class)->name('housing-schemes');
    Route::get('/housing-schemes/{id}', ShowHousingScheme::class)->name('housing-schemes.show');

    Route::get('/available-plots', AvailablePlots::class)->name('available-plots');
    Route::get('/about-us', AboutUs::class)->name('about-us');
    Route::get('/contact', ContactUs::class)->name('contact');

    Route::get('/blog', BlogIndex::class)->name('blog.index');
    Route::get('/blog/{slug}', BlogShow::class)->name('blog.show');

    /**
     * Tools
     * 
     * */
    Route::get('/tools/legal-scanner', PropertyLegalScanner::class)->name('tools.legal-scanner');
    Route::get('/tools/registry-tax-calculator', UserTax::class)->name('tools.tax-calculator');
    Route::get('/tools/patwari-land-converter', PatwariLandConverter::class)->name('tools.land-converter');
    Route::get('/tools/bayana-deed-generator', BayanaDeedGenerator::class)->name('tools.bayana-generator');
    Route::get('/tools/society-noc-directory', SocietyNocDirectory::class)->name('tools.society-directory');
    Route::get('/tools/inheritance-calculator', InheritanceCalculator::class)->name('tools.inheritance-calculator');
    Route::get('/tools/rent-agreement-generator', RentAgreementGenerator::class)->name('tools.rent-generator');
    Route::get('/tools/zakat-calculator', ZakatCalculator::class)->name('tools.zakat-calculator');
    Route::get('/tools/cv-maker', CvMaker::class)->name('tools.cv-maker');
    /**
     * Tools end
     * 
     * */

});


Route::middleware('auth')->group(function () {

    Route::get('/dashboard', UserDashboard::class)->name('dashboard');
    // Logout Route
    Route::post('logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');

        Route::get('/properties/create', CreateProperty::class)->name('properties.create');

        Route::get('/users', ManageUsers::class)->name('users');

        Route::get('/towns', AdminManageTowns::class)->name('towns');

        Route::get('/payment-methods', ManagePaymentMethods::class)->name('payment-methods');

        Route::get('/topup-requests', TopUpRequests::class)->name('topup-requests');

        Route::get('/admin/pages', PageManagement::class)->name('pages');

        Route::get('/contact-inquiries', ManageInquiries::class)->name('contact-inquiries');

        Route::get('/properties', PropertyManagement::class)->name('properties');
        Route::get('/town-schemes', TownSchemeManagement::class)->name('town-schemes');

        // Cities Management Route
        Route::get('/cities', CityManagement::class)->name('cities');

        Route::get('/admin/analytics', PageAnalytics::class)->name('analytics');

        Route::get('/whatsapp-marketing', WhatsAppMarketing::class)->name('whatsapp.marketing');

        Route::get('/posts', ManagePosts::class)->name('posts');

    });

    // Town Owner Routes
    Route::middleware(['role:town_owner'])->prefix('town-owner')->name('town_owner.')->group(function () {
        Route::get('/dashboard', TownOwnerDashboard::class)->name('dashboard');
        Route::get('/towns', ManageTowns::class)->name('towns');
        Route::get('/towns/{townId}/plots', ManagePlots::class)->name('plots');
        Route::get('/plots/{plotId}/installments', ManageInstallments::class)->name('installments');
        Route::get('/town-owner/receipt/{installment}', function (Installment $installment) {
            $installment->load('plot.town');
            return view('print.receipt', compact('installment'));
        })->name('receipt.print')->middleware(['auth']);

        Route::get('/employees/{townId?}', ManageEmployees::class)->name('employees');
        Route::get('/expenses/{townId?}', ManageExpenses::class)->name('expenses');
        Route::get('/leads', ManageLeads::class)->name('leads');

    });

    // Agent Routes
    Route::middleware(['role:agent'])->prefix('agent')->name('agent.')->group(function () {
        // Livewire Agent Dashboard Route
        Route::get('/dashboard', AgentDashboard::class)->name('dashboard');
        Route::get('/properties', ManageProperties::class)->name('properties');
        // Livewire Manage Leads Route
        Route::get('/leads', ManageLeads::class)->name('leads');

        Route::get('/calculator', LandCalculator::class)->name('calculator');
        Route::get('/bayana-generator', BayanaGenerator::class)->name('bayana');
        Route::get('/portfolio-settings', AgencyProfile::class)->name('portfolio.settings');
        Route::get('/dealer-network', DealerExchange::class)->name('dealer.network');
        Route::get('/poster-maker/{propertyId?}', PosterMaker::class)->name('poster.maker');
        Route::get('/commission-ledger', CommissionLedger::class)->name('commission.ledger');
        Route::get('/registry-tax-calculator', RegistryTaxCalculator::class)->name('tax.calculator');

    });

    // General User Routes
    Route::middleware(['role:user'])->prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', function () {
            return view('welcome');
        })->name('dashboard');
    });


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


});

// Directly override login and register routes
Route::middleware('guest')->group(function () {
    Route::get('login', NewLogin::class)->name('login');
    Route::get('register', NewRegister::class)->name('register');
});

// 2. Public Facing Digital Card Link
    Route::get('/agent/{username}', PublicAgentPortfolio::class)->name('agent.public.portfolio');

require __DIR__.'/auth.php';
