<?php

namespace Tests\Feature;

use App\Livewire\Tools\BayanaDeedGenerator;
use App\Livewire\Tools\InheritanceCalculator;
use App\Livewire\Tools\PatwariLandConverter;
use App\Livewire\Tools\PropertyLegalScanner;
use App\Livewire\Tools\RegistryTaxCalculator;
use App\Livewire\Tools\RentAgreementGenerator;
use App\Livewire\Tools\ZakatCalculator;
use Livewire\Livewire;
use Tests\TestCase;

class ToolsPagesTest extends TestCase
{
    public function test_inheritance_handles_string_input_and_huge_counts(): void
    {
        // Form se "1" string aati hai; pehle ek beti ko 2/3 mil jata tha
        Livewire::test(InheritanceCalculator::class)
            ->set('wives_count', '0')->set('has_father', false)->set('has_mother', false)
            ->set('sons_count', '0')->set('daughters_count', '1')
            ->assertSet('heirs_shares.0.percentage', 100.0)
            ->set('sons_count', '999999999')
            ->assertSet('sons_count', 50);
    }

    public function test_tax_uses_configured_slabs_and_clean_rates(): void
    {
        Livewire::test(RegistryTaxCalculator::class)
            ->set('area_size', 10)->set('rate_per_unit', 6000000) // 6 crore
            ->set('buyers.0.is_filer', false)
            ->assertSet('total_declared_value', 60000000.0)
            ->assertSet('buyer_tax_details.0.rate', '14.5%')
            ->assertSet('fbr_buyer_tax_total', 8700000.0)
            ->assertSet('seller_tax_details.0.rate', '2.75%');
    }

    public function test_tax_flags_wrong_share_total(): void
    {
        Livewire::test(RegistryTaxCalculator::class)
            ->call('addBuyer')->set('buyers.1.share', 50)
            ->assertSet('buyer_share_total', 150.0);
    }

    public function test_acre_does_not_change_with_marla_standard(): void
    {
        foreach (['society', 'revenue'] as $standard) {
            $units = Livewire::test(PatwariLandConverter::class)
                ->set('standard', $standard)->set('from_unit', 'acre')->set('input_value', 1)
                ->viewData('convertedUnits');
            $this->assertEquals(43560, $units['sqft']);
        }
    }

    public function test_zakat_ignores_negative_values(): void
    {
        Livewire::test(ZakatCalculator::class)
            ->set('cash_in_hand', -5000)
            ->assertSet('cash_in_hand', 0.0);
    }

    public function test_blank_dates_do_not_crash_deeds_and_issues_are_listed(): void
    {
        Livewire::test(BayanaDeedGenerator::class)
            ->set('deed_date', '')->assertOk()
            ->assertSee('Enter both dates');

        Livewire::test(RentAgreementGenerator::class)
            ->set('start_date', '')->assertOk()
            ->assertSet('end_date', '');
    }

    public function test_bayana_rejects_advance_above_total(): void
    {
        Livewire::test(BayanaDeedGenerator::class)
            ->set('total_amount', 100)->set('bayana_advance_amount', 500)
            ->assertSee('Advance must be more than zero');
    }

    public function test_unreadable_pdf_is_reported_and_not_kept(): void
    {
        Livewire::test(PropertyLegalScanner::class)
            ->set('document', \Illuminate\Http\UploadedFile::fake()->create('fard.pdf', 10, 'application/pdf'))
            ->call('readPdf')
            ->assertHasNoErrors()
            ->assertSet('document', null)
            ->assertSet('pdfReport.has_text', false)
            ->assertSee('No readable text found');
    }

    public function test_government_guide_only_links_to_official_sites(): void
    {
        Livewire::test(\App\Livewire\Tools\GovernmentLinks::class)
            ->assertSee('Get an e-Stamp')
            ->assertSee('not a government website');

        foreach (\App\Support\Tools\GovernmentServices::groups() as $services) {
            foreach ($services as $service) {
                foreach (array_filter([$service['url'], $service['guide'] ?? null]) as $url) {
                    $this->assertMatchesRegularExpression('/\.(gov|gop)\.pk$/', parse_url($url, PHP_URL_HOST), $url);
                    $this->assertSame('https', parse_url($url, PHP_URL_SCHEME), $url);
                }
            }
        }
    }

    public function test_document_checker_never_claims_verification_by_itself(): void
    {
        Livewire::test(PropertyLegalScanner::class)
            ->assertDontSee('Low Risk')
            ->assertDontSee('فیاض احمد')
            ->assertSee('Checks still open')
            ->set('answers.owner_match', 'no')
            ->assertSee('High risk: do not pay yet');
    }
}
