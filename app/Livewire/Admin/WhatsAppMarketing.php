<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class WhatsAppMarketing extends Component
{
    use WithPagination;

    // Audience filter: 'agent', 'town_owner', 'user', 'all'
    public $targetRole = 'user'; 
    public $search = '';
    public $message = "السلام علیکم {name} صاحب!\nآسان زمین (AssanZameen.com) پر آپ کو خوش آمدید کہتے ہیں۔ کیا آپ کو کسی پراپرٹی، پلاٹ یا مکان کی تلاش میں رہنمائی درکار ہے؟";

    public function setTemplate($type)
    {
        if ($type === 'user_welcome') {
            $this->message = "السلام علیکم {name} صاحب!\nآسان زمین پورٹل (AssanZameen.com) پر خوش آمدید۔ آپ پاکستان بھر میں تصدیق شدہ پلاٹس اور مکانات باآسانی تلاش کر سکتے ہیں۔ کسی بھی رہنمائی کے لیے ہم حاضر ہیں!";
        } elseif ($type === 'user_custom') {
            $this->message = "السلام علیکم {name} صاحب!\nکیا آپ اپنے پسندیدہ علاقے میں سرمایہ کاری یا رہائش کے لیے پراپرٹی تلاش کر رہے ہیں؟\nابھی وزٹ کریں اور براہِ راست مالکان و ایجنٹس سے رابطہ کریں:\n🌐 assanzameen.com";
        } elseif ($type === 'agent_marketing') {
            $this->message = "🏢 پراپرٹی ڈیلرز کے لیے شاندار موقع! 📢\n{name} صاحب، اپنی تمام پراپرٹیز AssanZameen.com پر آن لائن کریں اور ہزاروں خریداروں تک رسائی حاصل کریں۔ ہیلپ لائن: 03078617447";
        } elseif ($type === 'listing_reminder') {
            $this->message = "السلام علیکم {name} صاحب!\nکیا آپ کے پاس نئی پراپرٹی یا پلاٹ برائے فروخت موجود ہے؟ آسان زمین پر بالکل مفت لسٹ کریں اور خریداروں سے براہِ راست رابطہ کریں۔\nویب سائٹ: assanzameen.com";
        }
    }

    public function updatedTargetRole()
    {
        // Role change hone par default relevant message set karein
        if ($this->targetRole === 'user') {
            $this->setTemplate('user_welcome');
        } elseif ($this->targetRole === 'agent') {
            $this->setTemplate('agent_marketing');
        } elseif ($this->targetRole === 'town_owner') {
            $this->message = "السلام علیکم {name} صاحب!\nآسان زمین پورٹل پر آپ کی سوسائٹی/ٹاؤن کے لیے نئی انکوائریز اور وزیٹرز آرہے ہیں۔ اپنی سوسائٹی کی تفصیلات اپ ڈیٹ رکھیں: assanzameen.com";
        }
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sendWhatsAppMessage($phone, $name)
    {
        if (empty($phone)) {
            $this->dispatch('notify-error', message: 'فون نمبر موجود نہیں ہے۔');
            return;
        }

        // Pakistani phone format (e.g. 03078617447 -> 923078617447)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '92' . substr($cleanPhone, 1);
        }

        $personalizedMessage = str_replace('{name}', $name, $this->message);
        $encodedMessage = urlencode($personalizedMessage);

        $appUrl = "whatsapp://send?phone={$cleanPhone}&text={$encodedMessage}";
        $webUrl = "https://web.whatsapp.com/send?phone={$cleanPhone}&text={$encodedMessage}";

        $this->dispatch('trigger-whatsapp', appUrl: $appUrl, webUrl: $webUrl);
    }

    public function render()
    {
        $query = User::query()->whereNotNull('phone')->where('phone', '!=', '');

        if ($this->targetRole !== 'all') {
            // Normal buyers/users ke role match karein
            if ($this->targetRole === 'user') {
                $query->where(function ($q) {
                    $q->where('role', 'user')->orWhereNull('role');
                });
            } else {
                $query->where('role', $this->targetRole);
            }
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.admin.whats-app-marketing', [
            'users' => $query->latest()->paginate(15),
            'totalNormalUsers' => User::where(function ($q) {
                $q->where('role', 'user')->orWhereNull('role');
            })->whereNotNull('phone')->where('phone', '!=', '')->count(),
            'totalAgents' => User::where('role', 'agent')->whereNotNull('phone')->where('phone', '!=', '')->count(),
            'totalTownOwners' => User::where('role', 'town_owner')->whereNotNull('phone')->where('phone', '!=', '')->count(),
        ])->layout('layouts.admin-app');
    }
}