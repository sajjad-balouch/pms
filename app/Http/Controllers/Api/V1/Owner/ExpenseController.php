<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Api\V1\Transform;
use App\Livewire\TownOwner\ManageExpenses;
use App\Models\TownExpense;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseController extends OwnerController
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $query = TownExpense::whereIn('town_id', $this->ownerTowns()->select('id'))
            ->when($request->filled('town_id'), fn ($q) => $q->where('town_id', $request->integer('town_id')))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->query('category')))
            ->when($request->filled('from'), fn ($q) => $q->where('expense_date', '>=', $request->date('from')->toDateString()))
            ->when($request->filled('to'), fn ($q) => $q->where('expense_date', '<=', $request->date('to')->toDateString()))
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('paid_to', 'like', "%{$search}%")));

        $total = (float) (clone $query)->sum('amount');
        $byCategory = (clone $query)->toBase()->selectRaw('category, SUM(amount) AS total')->groupBy('category')->orderByDesc('total')->get()
            ->map(fn ($r) => ['category' => $r->category, 'total' => (float) $r->total]);

        $page = $query->latest('expense_date')->latest('id')->paginate($this->perPage());

        return $this->paginated($page, fn ($e) => $this->present($e), [
            'total' => $total,
            'by_category' => $byCategory,
            'categories' => ManageExpenses::CATEGORIES,
        ]);
    }

    // multipart/form-data: receipt (image/pdf) optional
    public function store(Request $request)
    {
        $data = $this->payload($request);
        $this->ensureTownOwned((int) $data['town_id']);

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $this->storePublicImage($request->file('receipt'), 'expense_receipts', 'receipt');
        }
        unset($data['receipt']);

        return $this->created($this->present(TownExpense::create($data)), __('Expense saved.'));
    }

    public function update(Request $request, $expense)
    {
        $expense = TownExpense::whereIn('town_id', $this->ownerTowns()->select('id'))->findOrFail($expense);
        $data = $this->payload($request);
        $this->ensureTownOwned((int) $data['town_id']);

        if ($request->hasFile('receipt')) {
            $old = $expense->receipt_path;
            $data['receipt_path'] = $this->storePublicImage($request->file('receipt'), 'expense_receipts', 'receipt');
            $this->deletePublicFile($old, 'expense_receipts/');
        }
        unset($data['receipt']);

        $expense->update($data);

        return $this->ok($this->present($expense), __('Expense updated.'));
    }

    public function destroy($expense)
    {
        $expense = TownExpense::whereIn('town_id', $this->ownerTowns()->select('id'))->findOrFail($expense);
        $this->deletePublicFile($expense->receipt_path, 'expense_receipts/');
        $expense->delete();

        return $this->ok(null, __('Expense deleted.'));
    }

    protected function payload(Request $request): array
    {
        return $request->validate([
            'town_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'category' => ['required', Rule::in(array_keys(ManageExpenses::CATEGORIES))],
            'amount' => 'required|numeric|min:1|max:1000000000',
            'expense_date' => 'required|date|before_or_equal:today',
            'paid_to' => 'nullable|string|max:255',
            'details' => 'nullable|string|max:2000',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);
    }

    protected function present(TownExpense $e): array
    {
        return [
            'id' => $e->id,
            'town_id' => $e->town_id,
            'title' => $e->title,
            'category' => $e->category,
            'amount' => (float) $e->amount,
            'expense_date' => Transform::date($e->expense_date),
            'paid_to' => $e->paid_to,
            'details' => $e->details,
            'receipt_url' => self::mediaUrl($e->receipt_path),
        ];
    }
}
