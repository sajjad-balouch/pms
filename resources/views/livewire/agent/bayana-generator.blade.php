<div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    @if(!$isPreview)
    <!-- ========================================== -->
    <!-- INPUT FORM VIEW -->
    <!-- ========================================== -->
    <div class="space-y-6">
        
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-slate-800/90 p-6 rounded-2xl shadow-md border border-slate-700/80 gap-4">
            <div>
                <h2 class="text-2xl font-black text-white flex items-center gap-2">
                    <i class="fa-solid fa-file-contract text-emerald-400"></i>
                    <span>Bayana Agreement & Token Receipt Generator</span>
                </h2>
                <p class="text-xs text-slate-400 mt-1">Instant print and PDF-ready document with legal terms and conditions</p>
            </div>
            <div>
                <span class="px-3 py-1 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold rounded-full">
                    <i class="fa-solid fa-stamp mr-1"></i> Legal Draft Ready
                </span>
            </div>
        </div>

        <form wire:submit="generateReceipt" class="bg-slate-800/90 rounded-2xl p-6 border border-slate-700/80 shadow-md space-y-6">
            
            <!-- Type and Dates -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-b border-slate-700/80 pb-5">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Document Type</label>
                    <select wire:model="agreementType" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="bayana">Formal Bayana Agreement</option>
                        <option value="token">Token Receipt / Advance Bayana</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Agreement Date</label>
                    <input type="date" wire:model="agreementDate" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Final Payment / Registry Due Date</label>
                    <input type="date" wire:model="finalPaymentDate" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <!-- Seller & Buyer Columns -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-slate-700/80 pb-5">
                
                <!-- First Party: Seller -->
                <div class="space-y-3 bg-slate-900/60 p-4 rounded-xl border border-slate-700/60">
                    <h3 class="text-xs font-bold text-amber-400 uppercase flex items-center gap-1.5 border-b border-slate-800 pb-2">
                        <i class="fa-solid fa-user-tag"></i> First Party (Seller / Owner)
                    </h3>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Seller Full Name *</label>
                        <input type="text" wire:model="sellerName" placeholder="e.g. Muhammad Aslam S/O Abdul Rasheed" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @error('sellerName') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">National Identity Card (CNIC)</label>
                        <input type="text" wire:model="sellerCnic" placeholder="33100-xxxxxxx-x" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Mobile Number</label>
                        <input type="text" wire:model="sellerPhone" placeholder="03001234567" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <!-- Second Party: Buyer -->
                <div class="space-y-3 bg-slate-900/60 p-4 rounded-xl border border-slate-700/60">
                    <h3 class="text-xs font-bold text-emerald-400 uppercase flex items-center gap-1.5 border-b border-slate-800 pb-2">
                        <i class="fa-solid fa-user-check"></i> Second Party (Buyer)
                    </h3>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Buyer Full Name *</label>
                        <input type="text" wire:model="buyerName" placeholder="e.g. Tariq Javed S/O Nazir Ahmed" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @error('buyerName') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">National Identity Card (CNIC)</label>
                        <input type="text" wire:model="buyerCnic" placeholder="33100-xxxxxxx-x" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Mobile Number</label>
                        <input type="text" wire:model="buyerPhone" placeholder="03007654321" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

            </div>

            <!-- Property Description -->
            <div class="space-y-3 border-b border-slate-700/80 pb-5">
                <h3 class="text-xs font-bold text-indigo-400 uppercase flex items-center gap-1.5">
                    <i class="fa-solid fa-map-location-dot"></i> Property & Plot Details
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Property Title / Details *</label>
                        <input type="text" wire:model="propertyTitle" placeholder="e.g. House No. 12, Street No. 4" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @error('propertyTitle') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Society / Mauza / Town</label>
                        <input type="text" wire:model="schemeName" placeholder="e.g. Canal City" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Area & Size *</label>
                        <input type="text" wire:model="areaSize" placeholder="e.g. 5 Marla (1125 Sq Ft)" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @error('areaSize') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Financials -->
            <div class="space-y-3 border-b border-slate-700/80 pb-5">
                <h3 class="text-xs font-bold text-amber-400 uppercase flex items-center gap-1.5">
                    <i class="fa-solid fa-coins"></i> Financial Terms & Amounts (PKR)
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Total Agreed Price *</label>
                        <input type="number" wire:model.live.debounce.300ms="totalDealAmount" placeholder="5000000" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @error('totalDealAmount') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Paid Bayana / Advance Amount *</label>
                        <input type="number" wire:model.live.debounce.300ms="bayanaAmount" placeholder="500000" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-emerald-400 font-bold focus:outline-none focus:border-indigo-500">
                        @error('bayanaAmount') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Remaining Balance Due</label>
                        <input type="text" readonly value="PKR {{ number_format($remainingAmount) }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-400 font-bold font-mono">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Payment Method</label>
                        <select wire:model="paymentMethod" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                            <option value="cash">Cash in Hand</option>
                            <option value="cheque">Bank Cheque / Pay Order</option>
                            <option value="online">Online Transfer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Cheque Number / Bank Details (If applicable)</label>
                        <input type="text" wire:model="chequeDetails" placeholder="e.g. HBL Cheque No. 98765432" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Witnesses -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-slate-300 uppercase flex items-center gap-1.5">
                    <i class="fa-solid fa-users"></i> Witnesses Details (Optional)
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model="witness1Name" placeholder="Witness 1 Name" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                        <input type="text" wire:model="witness1Cnic" placeholder="Witness 1 CNIC" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model="witness2Name" placeholder="Witness 2 Name" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                        <input type="text" wire:model="witness2Cnic" placeholder="Witness 2 CNIC" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-lg shadow-emerald-600/20 cursor-pointer">
                    <i class="fa-solid fa-eye"></i>
                    <span>Generate Agreement & Preview Print</span>
                </button>
            </div>
        </form>

    </div>

    @else
    <!-- ========================================== -->
    <!-- PRINTABLE LEGAL DOCUMENT PREVIEW -->
    <!-- ========================================== -->
    <div class="space-y-4">
        
        <!-- Action Control Bar (Hidden on Print) -->
        <div class="print:hidden flex items-center justify-between bg-slate-800 p-4 rounded-2xl border border-slate-700">
            <button type="button" wire:click="editAgreement" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold rounded-xl flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Edit</span>
            </button>

            <button type="button" onclick="window.print()" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl flex items-center gap-2 shadow-md">
                <i class="fa-solid fa-print"></i>
                <span>Print or Save PDF</span>
            </button>
        </div>

        <!-- The Stamp Paper / Document Body -->
        <div class="bg-white text-black p-8 sm:p-12 rounded-2xl shadow-2xl border border-slate-200 print:border-0 print:shadow-none print:p-4 text-left font-sans" dir="ltr" style="line-height: 1.8;">
            
            <!-- Document Top Header -->
            <div class="text-center border-b-2 border-black pb-4 mb-6">
                <h1 class="text-2xl font-extrabold uppercase tracking-wide mb-1">
                    {{ $agreementType === 'bayana' ? 'Agreement to Sell / Bayana Contract' : 'Earnest Money & Token Receipt' }}
                </h1>
                <p class="text-xs text-gray-700 font-semibold">Dealer / Notary Office: {{ Auth::user()->name }} | Date: {{ \Carbon\Carbon::parse($agreementDate)->format('d-m-Y') }}</p>
            </div>

            <!-- Agreement Text Body -->
            <div class="text-sm text-gray-900 space-y-4 text-justify">
                <p>
                    <strong>First Party (Seller):</strong> Mr./Ms. <strong>{{ $sellerName }}</strong>, 
                    CNIC No.: <span class="font-bold">{{ $sellerCnic ?: '_____________________' }}</span>, 
                    Contact No.: <span>{{ $sellerPhone }}</span>.
                </p>

                <p>
                    <strong>Second Party (Buyer):</strong> Mr./Ms. <strong>{{ $buyerName }}</strong>, 
                    CNIC No.: <span class="font-bold">{{ $buyerCnic ?: '_____________________' }}</span>, 
                    Contact No.: <span>{{ $buyerPhone }}</span>.
                </p>

                <p class="border-t border-gray-300 pt-3">
                    Whereas the First Party is the lawful owner in absolute possession of the property titled <strong>{{ $propertyTitle }}</strong>, 
                    situated at <strong>{{ $schemeName ?: 'Subject Locality' }}</strong>, 
                    measuring an area of <strong>{{ $areaSize }}</strong>, and has willingly agreed to sell the said property to the Second Party for a total agreed consideration of 
                    <strong>PKR {{ number_format($totalDealAmount) }}</strong> (Half of which amounts to PKR {{ number_format($totalDealAmount / 2) }}).
                </p>

                <p>
                    Out of the total consideration, the Second Party has paid an advance token/bayana amount of 
                    <strong>PKR {{ number_format($bayanaAmount) }}</strong> 
                    via <strong>({{ $paymentMethod === 'cash' ? 'Cash in Hand' : ($paymentMethod === 'cheque' ? 'Cheque / Pay Order: ' . $chequeDetails : 'Online Bank Transfer') }})</strong> 
                    in the presence of the undersigned witnesses, receipt of which is hereby acknowledged by the First Party. 
                    The remaining balance of <strong>PKR {{ number_format($remainingAmount) }}</strong> 
                    shall be payable on or before <strong>{{ \Carbon\Carbon::parse($finalPaymentDate)->format('d-m-Y') }}</strong>, 
                    upon which the First Party shall execute the final transfer, registered deed, and handover peaceful physical possession to the Second Party.
                </p>

                <!-- Legal Clauses -->
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 text-xs space-y-2 my-4">
                    <p class="font-bold uppercase tracking-wider text-gray-900">Terms & Conditions:</p>
                    <ol class="list-decimal list-inside space-y-1 text-gray-800">
                        <li>The First Party guarantees that the subject property is free from all legal disputes, encumbrances, bank liens, dues, and litigation prior to final registration/transfer.</li>
                        <li>If the Second Party fails to clear the remaining balance by the agreed date, the bayana/token amount shall stand forfeited. Conversely, if the First Party revokes this agreement, they shall be liable to refund double the bayana amount to the Second Party.</li>
                        <li>This agreement is executed with mutual consent, sound mind, and free will without any undue influence or coercion in the presence of the witnesses named below.</li>
                    </ol>
                </div>
            </div>

            <!-- Signatures Section -->
            <div class="grid grid-cols-2 gap-8 pt-12 text-center text-xs">
                <div class="border-t-2 border-black pt-2">
                    <p class="font-bold text-sm">Signature / Thumb Impression (Seller)</p>
                    <p class="text-gray-600 mt-1">{{ $sellerName }}</p>
                </div>
                <div class="border-t-2 border-black pt-2">
                    <p class="font-bold text-sm">Signature / Thumb Impression (Buyer)</p>
                    <p class="text-gray-600 mt-1">{{ $buyerName }}</p>
                </div>
            </div>

            <!-- Witnesses Signatures -->
            <div class="grid grid-cols-3 gap-6 pt-10 text-center text-xs">
                <div class="border-t border-gray-400 pt-2">
                    <p class="font-bold">Witness 1</p>
                    <p class="text-gray-700">{{ $witness1Name ?: '________________' }}</p>
                    <p class="text-gray-500">{{ $witness1Cnic }}</p>
                </div>
                <div class="border-t border-gray-400 pt-2">
                    <p class="font-bold">Dealer / Notary Stamp & Signature</p>
                    <p class="text-emerald-700 font-bold">{{ Auth::user()->name }}</p>
                    <p class="text-gray-500">{{ Auth::user()->phone }}</p>
                </div>
                <div class="border-t border-gray-400 pt-2">
                    <p class="font-bold">Witness 2</p>
                    <p class="text-gray-700">{{ $witness2Name ?: '________________' }}</p>
                    <p class="text-gray-500">{{ $witness2Cnic }}</p>
                </div>
            </div>

            <!-- Portal Stamp Watermark at Footer -->
            <div class="mt-8 pt-3 border-t border-gray-200 text-center text-[10px] text-gray-400">
                Generated via AssanZameen.com Portal | Official Legal Record for Deal Documentation
            </div>

        </div>

    </div>
    @endif

</div>