<?php

namespace App\Livewire\Tools;

use App\Support\Tools\RaqbaMath;
use App\Support\Tools\ToolCatalog;
use Livewire\Component;

/**
 * LRMIS tarz ka raqba calculator: kanal-marla-fut likh kar jama ya tafreeq karte jao,
 * kul raqba kanal-marla-fut, kul marle aur acre mein. Kuch server par save nahi hota.
 */
class RaqbaCalculator extends Component
{
    public const MAX_ENTRIES = 500;

    public bool $subtract = false;

    public string $standard = 'revenue';

    /** @var array<int,array{sign:int,kanal:float,marla:float,feet:float,sqft:float}> */
    public array $entries = [];

    /**
     * Khaane browser mein rehte hain (Enter par foran khaali), qeematein yahan bheji jati hain.
     * Is tarah tez likhne wale ka agla raqba server ke jawab se nahi mit-ta.
     */
    public function addEntry($kanal, $marla, $feet): void
    {
        $k = $this->num($kanal);
        $m = $this->num($marla);
        $f = $this->num($feet);

        if ($k + $m + $f <= 0) {
            $this->addError('kanal', __('Enter kanal, marla or feet first.'));

            return;
        }
        if (count($this->entries) >= self::MAX_ENTRIES) {
            $this->addError('kanal', __('Up to :max entries can be added.', ['max' => self::MAX_ENTRIES]));

            return;
        }

        $this->entries[] = [
            'sign' => $this->subtract ? -1 : 1,
            'kanal' => $k, 'marla' => $m, 'feet' => $f,
        ];
        $this->resetErrorBag();
    }

    public function removeEntry(int $index): void
    {
        unset($this->entries[$index]);
        $this->entries = array_values($this->entries);
    }

    public function resetAll(): void
    {
        $this->resetErrorBag();
        $this->entries = [];
        $this->subtract = false;
    }

    /** Browser mein mehfooz entries wapas lana; har cheez dobara jaanchi jati hai */
    public function restore($state): void
    {
        if (! is_array($state)) {
            return;
        }
        $this->standard = array_key_exists($state['standard'] ?? '', RaqbaMath::MARLA) ? $state['standard'] : 'revenue';
        $this->entries = [];
        foreach (array_slice(is_array($state['entries'] ?? null) ? $state['entries'] : [], 0, self::MAX_ENTRIES) as $e) {
            if (! is_array($e)) {
                continue;
            }
            $row = ['sign' => ($e['sign'] ?? 1) == -1 ? -1 : 1, 'kanal' => $this->num($e['kanal'] ?? 0), 'marla' => $this->num($e['marla'] ?? 0), 'feet' => $this->num($e['feet'] ?? 0)];
            if ($row['kanal'] + $row['marla'] + $row['feet'] > 0) {
                $this->entries[] = $row;
            }
        }
    }

    public function updated($name): void
    {
        if ($name === 'standard' && ! array_key_exists($this->standard, RaqbaMath::MARLA)) {
            $this->standard = 'revenue';
        }
    }

    private function num($v): float
    {
        return is_numeric($v) ? min(10_000_000, max(0.0, round((float) $v, 4))) : 0.0;
    }

    public function render()
    {
        $ms = RaqbaMath::marla($this->standard);
        $total = 0.0;
        $rows = [];
        foreach ($this->entries as $i => $e) {
            $sqft = RaqbaMath::toSqft($e['kanal'], $e['marla'], $e['feet'], $ms) * $e['sign'];
            $total += $sqft;
            $rows[] = $e + ['i' => $i, 'norm' => RaqbaMath::split(abs($sqft), $ms)];
        }

        return view('livewire.tools.raqba-calculator', [
            'ms' => $ms,
            'rows' => $rows,
            'total' => RaqbaMath::split($total, $ms),
            'acre' => RaqbaMath::splitAcre($total, $ms),
            'added' => count(array_filter($this->entries, fn ($e) => $e['sign'] === 1)),
            'subtracted' => count(array_filter($this->entries, fn ($e) => $e['sign'] === -1)),
        ])->layout('layouts.front-app', ToolCatalog::meta('raqba'));
    }
}
