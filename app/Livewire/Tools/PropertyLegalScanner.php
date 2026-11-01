<?php

namespace App\Livewire\Tools;

use App\Support\Tools\DeedHelper;
use App\Support\Tools\ToolCatalog;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use Livewire\WithFileUploads;
use Smalot\PdfParser\Parser;
use Throwable;

/**
 * Document checklist: risk user ke apne jawabon se banta hai.
 * Yeh PLRA ya kisi sarkari record se connected nahi, is liye "verified" kabhi nahi kehta.
 * Upload sirf text parhne ke liye hai aur save nahi hoti.
 */
class PropertyLegalScanner extends Component
{
    use WithFileUploads;

    public $document_type = 'fard';
    public $answers = [];

    public $document;
    public $seller_cnic = '';
    public $pdfReport = null;

    protected function rules()
    {
        return [
            'document' => 'required|file|mimes:pdf|max:10240',
            'seller_cnic' => 'nullable|string|max:15',
        ];
    }

    public static function checks(string $type): array
    {
        $common = [
            'fresh_copy' => ['You or your own representative got a fresh copy from the record office', true],
            'owner_match' => ['Seller name, father name and CNIC match the record exactly', true],
            'no_charge' => ['The record shows no mortgage, lien, court stay or government acquisition', true],
            'area_match' => ['Area and location in the document match the land shown on site', true],
            'possession' => ['You confirmed on site who has possession of the land', false],
            'no_dispute' => ['Neighbours and the local office report no dispute over the land', false],
        ];

        $specific = [
            'fard' => [
                'for_sale' => ['The fard was issued for sale and is still valid', true],
                'joint_share' => ['If the khewat is joint, the seller\'s share and boundary are clear', false],
            ],
            'registry' => [
                'registry_verified' => ['The registry copy was checked at the Sub-Registrar office', true],
                'mutation_done' => ['A mutation (intiqal) was attested after this registry', true],
            ],
            'allotment_letter' => [
                'society_confirmed' => ['The society confirmed this allotment in writing', true],
                'noc_confirmed' => ['The authority confirmed the society NOC and layout plan', true],
                'dues_clear' => ['Society dues are clear and a transfer is allowed', false],
            ],
            'intiqal' => [
                'intiqal_attested' => ['The intiqal is attested by a revenue officer, not only entered', true],
                'jamabandi_match' => ['The owner in the latest record (jamabandi) matches this intiqal', true],
            ],
        ];

        return $common + ($specific[$type] ?? []);
    }

    public function updatedDocumentType()
    {
        $this->document_type = array_key_exists($this->document_type, self::documentTypes()) ? $this->document_type : 'fard';
        $this->answers = array_intersect_key($this->answers, self::checks($this->document_type));
    }

    public static function documentTypes(): array
    {
        return [
            'fard' => 'Fard (ownership record)',
            'registry' => 'Registry (sale deed)',
            'allotment_letter' => 'Allotment letter or society file',
            'intiqal' => 'Intiqal (mutation)',
        ];
    }

    /** Sirf jawabon se nateeja: koi score ghar kar nahi dikhaya jata. */
    private function assessment(): array
    {
        $checks = self::checks($this->document_type);
        $blockers = [];
        $warnings = [];
        $open = [];
        $cleared = 0;

        foreach ($checks as $key => [$label, $critical]) {
            $answer = $this->answers[$key] ?? null;
            if ($answer === 'yes') {
                $cleared++;
            } elseif ($answer === 'no') {
                $critical ? $blockers[] = $label : $warnings[] = $label;
            } else {
                $open[] = $label;
            }
        }

        $level = match (true) {
            count($blockers) > 0 => 'high',
            count($open) > 0 => 'incomplete',
            count($warnings) > 0 => 'medium',
            default => 'clear',
        };

        return ['level' => $level, 'total' => count($checks), 'cleared' => $cleared, 'blockers' => $blockers, 'warnings' => $warnings, 'open' => $open];
    }

    public function readPdf()
    {
        $this->validate();

        $key = 'doc-check:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 20)) {
            $this->addError('document', __('Too many files. Please try again later.'));

            return;
        }
        RateLimiter::hit($key, 3600);

        try {
            $text = (new Parser())->parseFile($this->document->getRealPath())->getText();
        } catch (Throwable) {
            $text = '';
        }

        // File apne paas nahi rakhte
        $this->document->delete();
        $this->document = null;

        $digits = preg_replace('/\D/', '', (string) $this->seller_cnic);
        $found = preg_match_all('/\b\d{5}-?\d{7}-?\d\b/u', $text, $matches) ? array_map(fn ($c) => preg_replace('/\D/', '', $c), $matches[0]) : [];

        preg_match('/(?:کھیوٹ|khewat)\D{0,15}(\d+)/iu', $text, $khewat);
        preg_match('/(?:خسرہ|khasra)\D{0,15}(\d+(?:\/\d+)?)/iu', $text, $khasra);

        $this->pdfReport = [
            'has_text' => mb_strlen(trim($text)) >= 30,
            'khewat' => $khewat[1] ?? null,
            'khasra' => $khasra[1] ?? null,
            'cnic_count' => count(array_unique($found)),
            'cnic_match' => DeedHelper::validCnic($digits) ? in_array($digits, $found, true) : null,
        ];
    }

    public function resetChecklist()
    {
        $this->reset(['answers', 'document', 'seller_cnic', 'pdfReport']);
    }

    public function render()
    {
        return view('livewire.tools.property-legal-scanner', [
            'checks' => self::checks($this->document_type),
            'types' => self::documentTypes(),
            'result' => $this->assessment(),
        ])->layout('layouts.front-app', ToolCatalog::meta('scanner'));
    }
}
