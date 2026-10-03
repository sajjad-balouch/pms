@extends('layouts.app')
@section('slot')
<div class="bg-slate-950 text-slate-300 py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-10">
        
        <!-- Page Header -->
        <div class="border-b border-slate-800 pb-8 text-center sm:text-left">
            <span class="text-xs font-semibold text-amber-400 uppercase tracking-widest">Legal Document</span>
            <h1 class="text-3xl font-extrabold text-white mt-1">Privacy Policy</h1>
            <p class="text-slate-400 text-xs mt-2">Last Updated: {{ date('F d, Y') }}</p>
        </div>

        <div class="space-y-8 text-sm leading-relaxed text-slate-300">
            <!-- Section 1 -->
            <section class="space-y-3">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span> 1. Information We Collect
                </h2>
                <p>To provide transparent real estate transactions and facilitate verified land ownership records, we collect the following types of information:</p>
                <ul class="list-disc list-inside space-y-1.5 pl-4 text-slate-400 text-xs">
                    <li><strong class="text-slate-200">Personal Identification:</strong> Full name, CNIC number, contact details, email address, and postal address.</li>
                    <li><strong class="text-slate-200">Property & Revenue Information:</strong> Land measurement records (Kanal, Marla, Sq Ft), property registration documents, and ownership status (مالکِ اصلی، مالکِ قبضہ، رہن، مرتہن).</li>
                    <li><strong class="text-slate-200">Financial Records:</strong> Transaction logs, payment receipts, token amounts, and bank reference details for verified purchases.</li>
                    <li><strong class="text-slate-200">Technical Data:</strong> IP addresses, device identifiers, and browser logs for security and fraud prevention.</li>
                </ul>
            </section>

            <!-- Section 2 -->
            <section class="space-y-3">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span> 2. How We Use Your Information
                </h2>
                <p>Your data is processed strictly for legal, transactional, and operational compliance within Pakistani property laws and land registration standards:</p>
                <ul class="list-disc list-inside space-y-1.5 pl-4 text-slate-400 text-xs">
                    <li>Verifying plot availability, legal title clearings, and owner credentials.</li>
                    <li>Generating digital transfer deeds, NOC requests, and allotment letters.</li>
                    <li>Notifying buyers, sellers, and agents regarding payment updates and registration schedules.</li>
                    <li>Preventing illegal land occupation, duplicate sales, and fraudulent postings.</li>
                </ul>
            </section>

            <!-- Section 3 -->
            <section class="space-y-3">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span> 3. Data Protection & Sharing
                </h2>
                <p>We strictly protect your identity and property ownership details. We do not sell your personal data to third parties. Information is shared only with:</p>
                <ul class="list-disc list-inside space-y-1.5 pl-4 text-slate-400 text-xs">
                    <li>Authorized Housing Society management bodies (e.g., DHA, Royal Orchard, Citi Housing) for plot transfer approval.</li>
                    <li>Relevant government authorities or courts upon legal sub-poena or official land record verification requests.</li>
                </ul>
            </section>

            <!-- Section 4 -->
            <section class="space-y-3">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span> 4. Security Measures
                </h2>
                <p>All sensitive transactions, identity submissions, and land registry records are stored in encrypted databases (UTF-8 compliant) with restricted access controls and multi-tier authentication protocols.</p>
            </section>
        </div>
    </div>
</div>
@endsection