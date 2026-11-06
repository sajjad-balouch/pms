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

    public function test_shajra_add_remove_and_restore(): void
    {
        $c = Livewire::test(\App\Livewire\Tools\ShajraNasab::class)
            ->call('clearAll')
            ->set('form.relation', 'son')->set('form.name', 'Ali')->set('form.status', 'died_after')
            ->call('addPerson')->assertHasNoErrors()
            ->assertSee('1/1');

        // Fout bete ke neeche uski biwi; ek se zyada maan nahi
        $c->call('addUnder', 'n1')
            ->set('form.relation', 'mother')->set('form.name', 'Zainab')->call('addPerson')
            ->set('form.relation', 'mother')->set('form.name', 'Other')->call('addPerson')
            ->assertHasErrors('form.relation');

        // Beta "talaq yafta" nahi ho sakta: form foran wapas "zinda" kar deta hai
        $c->call('addUnder', 'root')
            ->set('form.relation', 'son')->set('form.name', 'X')->set('form.status', 'divorced')
            ->assertSet('form.status', 'alive');

        // Do biwiyan: bacha doosri biwi se; biwi hatane par bachay ki maan khaali
        $c->call('addUnder', 'root')
            ->set('form.relation', 'wife')->set('form.name', 'W1')->call('addPerson')
            ->set('form.relation', 'wife')->set('form.name', 'W2')->call('addPerson')
            ->set('form.relation', 'son')->set('form.mother', 'n4')->set('form.name', 'Usman')
            ->set('form.minor', true)->set('form.guardian', 'W2')->call('addPerson')
            ->assertHasNoErrors()
            ->assertSet('people.4.mother', 'n4')
            ->assertSee('Born of')
            ->call('removePerson', 'n4')
            ->assertSet('people.3.mother', '');
        $c->call('removePerson', 'n2')->call('removePerson', 'n3')->call('removePerson', 'n5');

        $c->call('removePerson', 'n1')->assertSet('people', []);

        // Ghalat/bahar ke data se kuch nahi tootna; yateem node chhor diye jate hain
        $c->call('restore', ['people' => [
            ['id' => 'n5', 'parent' => 'root', 'relation' => 'daughter', 'name' => 'Sana', 'status' => 'alive'],
            ['id' => 'n6', 'parent' => 'n99', 'relation' => 'son', 'name' => 'Lost'],
            ['id' => 'bad', 'parent' => 'root', 'relation' => 'son'],
            'junk',
        ], 'record' => ['kanal' => '-4', 'marla' => 'abc']])
            ->assertCount('people', 1)
            ->assertSet('record.kanal', 0.0)
            ->assertSet('nextId', 6);
    }

    public function test_shajra_asks_marriage_instead_of_guessing_lawalad(): void
    {
        $c = Livewire::test(\App\Livewire\Tools\ShajraNasab::class)
            ->call('clearAll')
            ->set('form.relation', 'daughter')->set('form.name', 'Amna')->call('addPerson')
            // Bhai pehle fout, shadi shuda: form khud us ke neeche chala jata hai
            ->set('form.relation', 'brother')->set('form.status', 'died_before')->set('form.name', 'Karim')
            ->set('form.married', 'yes')->set('form.issue', 'yes')->call('addPerson')
            ->assertSet('form.parent', 'n2')
            ->assertDontSee('لاولد</span>', false);

        // Us ke bete bhateeje ban kar baqi lete hain
        $c->set('form.relation', 'son')->set('form.name', 'Saqib')->call('addPerson')->assertHasNoErrors()
            ->assertSee('Saqib');
        $this->assertSame('1/2', collect($c->viewData('result')['holders'])->firstWhere('name', 'Saqib')['fraction']);

        // "Shadi nahi hui" chunne par us ke neeche wale hat jate hain aur "ghair shadi shuda" likha jata hai
        $c->set('people.1.married', 'no')
            ->assertCount('people', 2)
            ->assertSet('people.1.issue', '');
        // Ab koi asaba nahi: akeli beti ko 1/2 + radd = poora
        $this->assertSame('1/1', collect($c->viewData('result')['holders'])->firstWhere('name', 'Amna')['fraction']);

        // Zinda bhai par shadi ka sawal hi nahi
        $c->set('people.1.status', 'alive')->assertSet('people.1.married', '');
    }

    public function test_report_box_is_editable_and_can_go_back_to_automatic(): void
    {
        $c = Livewire::test(\App\Livewire\Tools\ShajraNasab::class)
            ->assertSet('report.page', 'a4l') // print hamesha landscape
            ->assertSet('report.custom', false);
        $this->assertStringContainsString('کوئی وارث پوشیدہ نہیں', $c->get('report.text'));

        // Patwari ne box mein khud likha: ab shajra badalne par bhi wahi rahe
        $c->set('report.text', 'میری اپنی رپورٹ')->set('report.title', 'رپورٹ')
            ->assertSet('report.custom', true)
            ->set('deceased.name', 'Naya Naam')
            ->assertSet('report.text', 'میری اپنی رپورٹ')
            ->assertSee('میری اپنی رپورٹ');

        // Wapas khudkaar: shajre se taaza
        $c->call('resetReportText')
            ->assertSet('report.custom', false)
            ->assertSet('report.title', 'رپورٹ پٹواری حلقہ');
        $this->assertStringContainsString('Naya Naam', $c->get('report.text'));

        // Purana portrait wala size bacha ho to landscape ban jata hai
        $c->call('restore', ['people' => [], 'report' => ['page' => 'a4p', 'custom' => true, 'text' => '  ']])
            ->assertSet('report.page', 'a4l')
            ->assertSet('report.custom', false);
    }

    public function test_raqba_calculator_adds_subtracts_and_totals(): void
    {
        $c = Livewire::test(\App\Livewire\Tools\RaqbaCalculator::class)
            ->call('add')->assertHasErrors('kanal') // khaali par kuch nahi
            ->set('kanal', '2')->set('marla', '15')->set('feet', '200')->call('add')
            ->assertHasNoErrors()->assertSet('kanal', '')
            ->set('kanal', '1')->set('marla', '10')->set('feet', '100')->call('add');

        $t = $c->viewData('total');
        $this->assertSame([4, 6, 27.75], [$t['kanal'], $t['marla'], $t['feet']]);
        $this->assertCount(2, $c->get('entries'));

        // Tafreeq: 2 kanal minus
        $c->set('subtract', true)->set('kanal', '2')->call('add');
        $t = $c->viewData('total');
        $this->assertSame([2, 6], [$t['kanal'], $t['marla']]);
        $this->assertSame(1, $c->viewData('subtracted'));

        // Ghalat entry hatao, phir sab saaf
        $c->call('removeEntry', 2);
        $this->assertSame(4, $c->viewData('total')['kanal']);
        $c->call('resetAll')->assertSet('entries', [])->assertSet('subtract', false);

        // Browser se aaya ghalat data
        $c->call('restore', ['standard' => 'x', 'entries' => [['sign' => -1, 'kanal' => '1'], ['kanal' => -5], 'junk']])
            ->assertSet('standard', 'revenue')->assertCount('entries', 1);
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
