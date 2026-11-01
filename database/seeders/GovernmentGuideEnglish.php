<?php

namespace Database\Seeders;

/** Sarkari guides ka English version. Facts wahi hain jo Urdu version mein (PLRA FAQs). */
class GovernmentGuideEnglish
{
    public static function footer(): string
    {
        return <<<'HTML'

<h2>More help</h2>
<p>Open our <a href="https://www.assanzameen.com/tools/government-links">Government Links Guide</a> to see all the official portals in one place. To contact PLRA or make a complaint, call <strong>(042) 111-22-22-77</strong> (Monday to Saturday, 8:00 AM to 5:00 PM).</p>
<blockquote>This article was written on 1 November 2026 from information on the PLRA website. Assan Zameen is not a government body. Fees and procedures can change, so confirm with the relevant office before paying. This is not legal advice.</blockquote>
HTML;
    }

    public static function posts(): array
    {
        return [
            'fard-kaise-hasil-karein-punjab-online-arc-guide' => [
                'title' => 'How to Get a Fard in Punjab: Online and Arazi Record Centre Guide',
                'summary' => 'Three ways to get a fard in Punjab, the documents you need, the fee, sale fard rules and what to do if a fard is lost. Based on PLRA information.',
                'content' => <<<'HTML'
<p>Whether you are buying land, selling it or simply checking ownership, the first thing you need is a <strong>fard</strong>. This guide explains what a fard is, where to get it and what to bring.</p>
<h2>What is a fard?</h2>
<p>A fard is an official document showing a piece of land's area, location and owner details. It is used as proof of ownership for a sale, court cases, loans and verification.</p>
<h2>Three ways to get a fard</h2>
<ol>
<li><strong>Arazi Record Centre (ARC):</strong> visit the nearest centre, complete biometric verification and collect a printed fard.</li>
<li><strong>Online:</strong> log in to the <a href="https://onlinefard.punjab-zameen.gov.pk/" rel="noopener" target="_blank">PLRA online fard portal</a> and verify the details.</li>
<li><strong>Arazi Moawin:</strong> get it from an Arazi Moawin centre in your area.</li>
</ol>
<p>You can also get one from a Dahi Markaz Mall where a patwari is available.</p>
<h2>What to bring</h2>
<ul>
<li>Your original CNIC</li>
<li>Property details such as khewat, khasra or registry</li>
<li>For a first fard: the registry for urban property, or mutation details for rural land</li>
</ul>
<p>If you do not have the property details, you will be asked some verification questions before the fard is issued.</p>
<h2>How much does a fard cost?</h2>
<p>A fard is not free. According to PLRA, the fee is normally <strong>Rs. 50 per khatooni</strong>, up to <strong>Rs. 150 per khewat</strong>, plus PLRA service charges. You can also pay online.</p>
<h2>Information fard and sale fard</h2>
<ul>
<li><strong>Information fard:</strong> for verification only.</li>
<li><strong>Sale fard:</strong> used for a registry and transfer of ownership. The buyer's name and CNIC are mandatory, because this data goes directly to the e-Registry system.</li>
</ul>
<h2>Can someone else get my fard?</h2>
<p>Yes, with a Mukhtarnama Aam (power of attorney) verified by the Sub-Registrar. To get a relative's fard, you also submit a verified copy of the mukhtarnama and an affidavit confirming the owner is alive.</p>
<h2>How to check if a fard is genuine</h2>
<p>Every fard has a printed QR code. Scan it to verify the document.</p>
<h2>Common problems</h2>
<h3>My fard is lost</h3>
<p>Find your fard ID from the record centre or an old copy, then submit a police report and an affidavit. The old fard is cancelled and a new one is issued.</p>
<h3>My fingerprints do not verify</h3>
<p>First update your fingerprints at NADRA. If verification still fails because of old age, bring a co-owner or the village lumberdar so the Service Centre Incharge can issue a manual verification token.</p>
<h3>My name is not in the record</h3>
<p>Visit the Arazi Record Centre with your original documents and request a correction.</p>
<h2>Does a fard expire?</h2>
<p>PLRA says a fard has no fixed time limit, but transfers and mutations can change the information. So <strong>always get a fresh fard before any deal</strong>, even if the seller shows you an older one.</p>
HTML,
            ],
            'e-stamp-challan-32a-kaise-banayein-punjab-guide' => [
                'title' => 'How to Get an e-Stamp in Punjab: Challan Form 32-A Step by Step',
                'summary' => 'How to create Challan 32-A on the Punjab e-Stamp portal, pay it, verify an e-Stamp and pay a deficiency. A simple step by step guide.',
                'content' => <<<'HTML'
<p>New property deals in Punjab now use <strong>e-Stamps</strong> instead of paper stamp papers. You do not need an account to create a challan, and the portal also works on a phone.</p>
<h2>What is an e-Stamp?</h2>
<p>It is the Government of Punjab's electronic system for generating and paying stamp duty online. There are two types: <strong>Judicial</strong> for court matters and <strong>Non-Judicial</strong> for property and business deeds.</p>
<h2>How to create Challan 32-A</h2>
<ol>
<li>Open the <a href="https://es.punjab-zameen.gov.pk/eStampCitizenPortal/ChallanFormView/HomePage" rel="noopener" target="_blank">e-Stamp portal</a> and click Generate Challan 32-A.</li>
<li>Enter the district, tehsil, deed type, stamp paper type and purpose.</li>
<li>Add the buyer and seller details. You can add more than one party.</li>
<li>Enter the property address, land value and any construction value.</li>
<li>Click Save. The system gives you a 16-digit challan ID, so keep it safe.</li>
<li>Click Print Challan 32-A and print the PDF on plain paper.</li>
</ol>
<p>If a property dealer or lawyer is acting for you, enter their name, CNIC and contact details. For a power of attorney, tick the Power of Attorney box.</p>
<h2>How is stamp duty calculated?</h2>
<p>The system calculates it automatically. The rule is simple: <strong>duty is charged on your declared value or the DC valuation, whichever is higher.</strong> Declaring a lower price will not reduce the duty. See our <a href="https://www.assanzameen.com/blog/dc-valuation-kaise-check-karein-punjab">DC valuation guide</a> to find the rate.</p>
<h2>How to pay</h2>
<p>Pay at any Bank of Punjab branch, or online through PSID and banking apps. Stamp duty, CVT and registration fee can be paid together on one challan.</p>
<h2>Things to watch</h2>
<ul>
<li>You can edit a challan before payment, but not after.</li>
<li>If the district or tehsil is wrong, create a new challan and do not use the old one.</li>
<li>Each challan is valid for one transaction only.</li>
<li>A challan is valid until the expiry shown on it, usually 90 days.</li>
<li>An unpaid challan can be reprinted. A paid one cannot be reprinted from the portal.</li>
</ul>
<h2>How to verify an e-Stamp</h2>
<p>Choose Verification through Web on the portal and enter the serial number, or scan the QR code. You will see the stamp status, amount, property and party details. This check is <strong>free</strong>, so do it before you accept any stamp in a deal.</p>
<h2>If too little was paid</h2>
<p>If less than the actual duty was paid, choose Pay Deficiency/Penalty on the home page and enter the stamp serial number. Deficiency is the unpaid balance, and penalty is an extra charge for late payment or an error. Each stamp needs its own challan.</p>
<h2>Is old paper stamp still valid?</h2>
<p>According to PLRA, only papers issued before e-Stamping began are valid. New transactions need an e-Stamp.</p>
HTML,
            ],
            'dc-valuation-kaise-check-karein-punjab' => [
                'title' => 'What Is DC Valuation and How to Check the DC Rate Online in Punjab',
                'summary' => 'What DC valuation means, how it affects stamp duty, and how to check the official DC rate for your area online on the PLRA portal.',
                'content' => <<<'HTML'
<p>In any plot deal you will hear the question "what is the DC rate?" That is because most official registry costs depend on it. You can now check it yourself online.</p>
<h2>What is DC valuation?</h2>
<p>It is the official property value notified by the District Collector (DC). Stamp duty, registration fees and other government charges are calculated from it.</p>
<h2>Why it matters</h2>
<p>The e-Stamp system charges duty on <strong>your declared value or the DC valuation, whichever is higher</strong>. So even if you write a lower price than the DC rate, duty is still charged on the DC rate. If too little duty was paid, the balance has to be paid on a separate challan.</p>
<h2>How to check the DC rate online</h2>
<ol>
<li>Open the <a href="https://es.punjab-zameen.gov.pk/eStampCitizenPortal/ChallanFormView/RateOfChallanView?name=PropertyDCValuation" rel="noopener" target="_blank">PLRA DC valuation portal</a>.</li>
<li>Select the district, tehsil, revenue circle and area.</li>
<li>Choose the land type: urban or rural, residential or commercial.</li>
<li>Click Find Land Rate.</li>
</ol>
<p>You will see the DC rate per unit and the total value of the area you selected. <strong>Urban areas use marla and rural areas use acre.</strong> Click Reset to start again.</p>
<h2>Do I need any documents?</h2>
<p>No. Basic property details are enough, and the portal works on a phone.</p>
<h2>Is the rate the same everywhere?</h2>
<p>No. It changes with location, land type and property category, so do not assume a nearby area's rate applies to your plot.</p>
<h2>If it says No Record Found</h2>
<p>Check that the district, tehsil and revenue circle are correct. PLRA says some new areas may not be updated yet. In that case, ask your nearest Arazi Record Centre.</p>
<h2>What to do next</h2>
<ul>
<li>Enter the rate in our <a href="https://www.assanzameen.com/tools/registry-tax-calculator">Registry & Tax Calculator</a> to estimate the total cost.</li>
<li>For the final amount, create an <a href="https://www.assanzameen.com/blog/e-stamp-challan-32a-kaise-banayein-punjab-guide">e-Stamp challan</a>, because the system calculates it for you.</li>
</ul>
HTML,
            ],
            'nayi-registry-ka-tareeqa-e-registration-punjab-steps' => [
                'title' => 'How to Register Property in Punjab: e-Registration Steps Explained',
                'summary' => 'The steps of a property registry in Punjab: challan, portal login, party details, statements before the Sub-Registrar and issue of the deed.',
                'content' => <<<'HTML'
<p>A registry is the legal document that records the transfer of property from one party to another. In Punjab this is done digitally, under the supervision of PLRA and the Board of Revenue.</p>
<h2>Prepare first</h2>
<ul>
<li>A <a href="https://www.assanzameen.com/blog/fard-kaise-hasil-karein-punjab-online-arc-guide">fresh sale fard</a> in the seller's name</li>
<li>CNIC details of the buyer, seller and witnesses</li>
<li>The <a href="https://www.assanzameen.com/blog/dc-valuation-kaise-check-karein-punjab">DC rate</a> of the area</li>
</ul>
<h2>e-Registration steps</h2>
<h3>1. Create the challan and pay the fees</h3>
<p>Create an e-Stamp challan and pay stamp duty, FBR taxes, registration fee, mutation fee and related charges. <a href="https://www.assanzameen.com/blog/e-stamp-challan-32a-kaise-banayein-punjab-guide">See how to create a challan</a>.</p>
<h3>2. Create a portal login</h3>
<p>Open the <a href="https://eregistration.punjab-zameen.gov.pk/" rel="noopener" target="_blank">e-Registration portal</a> and create the required user login.</p>
<h3>3. Start the request</h3>
<p>Start a new deed registration in the system.</p>
<h3>4. Verify the challan and enter the parties</h3>
<p>Verify the paid challans and carefully enter the buyer, seller and witness details. A mistake in a name or CNIC can cause problems later.</p>
<h3>5. Sub-Registrar process</h3>
<p>The request goes to the Sub-Registrar, where statements of the parties and witnesses are recorded.</p>
<h3>6. Issue of the document</h3>
<p>The registered document is issued, with the Bahi number and document number assigned automatically.</p>
<h2>What about the mutation?</h2>
<p>According to PLRA, <strong>in online mouzas the mutation of an e-Registry is processed automatically</strong>, so the registry and mutation are approved together. If you have an old registry with a pending mutation, apply to the District Collector. After verification and permission, you can complete the mutation at an Arazi Record Centre.</p>
<h2>If a name is wrong in the registry</h2>
<p>First get the registry corrected at the Sub-Registrar office. It can then be updated in the land record at an Arazi Record Centre.</p>
<h2>Need a copy of an old registry?</h2>
<p>You can find a deed registered earlier on the <a href="https://rod.pulse.gop.pk/" rel="noopener" target="_blank">PLRA registered deeds portal</a>.</p>
<h2>If a party is overseas</h2>
<p>The Sub-Registrar appoints an official at the Pakistan mission abroad as commission to record the statement, with thumb impression, photo and video. The other parties give their statements in person in Punjab.</p>
HTML,
            ],
            'bai-zubani-intiqal-ka-tareeqa-fee-punjab' => [
                'title' => 'Oral Mutation (Bai Zubani) in Punjab: Procedure, Documents and Fees',
                'summary' => 'What a bai zubani mutation is, the documents you need, where to pay the fees, and where to appeal if a mutation is wrong. Based on PLRA information.',
                'content' => <<<'HTML'
<p>After a land deal, the job is not finished until the new owner's name is in the official record. This step is called a <strong>mutation (intiqal)</strong>. This guide explains the oral sale mutation.</p>
<h2>What do these terms mean?</h2>
<p>A mutation transfers ownership rights in land and updates the official record. In a <strong>bai zubani</strong> mutation, the buyer, seller and witnesses give oral statements before the Revenue Officer at an Arazi Record Centre.</p>
<h2>Documents needed</h2>
<ul>
<li>A valid CNIC</li>
<li>Ownership documents, such as a registry, fard or mutation number</li>
</ul>
<h2>The procedure</h2>
<ol>
<li>Go to the relevant Arazi Record Centre with your CNIC and property details.</li>
<li>Get a token after NADRA biometric verification.</li>
<li>Give the property details for the mutation entry.</li>
<li>Pay the required fees at the bank or online.</li>
<li>Appear before the ADLR (Revenue Officer) with the buyer and witnesses.</li>
<li>The officer records the statements and approves the mutation.</li>
</ol>
<p>If you bring the buyer and witnesses with you, PLRA says this can be done <strong>in one visit</strong>. Once approved, the new owner's name appears in the jamabandi straight away.</p>
<h2>Fees and where to pay</h2>
<p>The PLRA website lists four types of fees on an oral sale mutation:</p>
<ul>
<li>Mutation fee: 3%</li>
<li>District Council fee: 1%</li>
<li>FBR advance tax on the buyer (section 236-K)</li>
<li>FBR gain tax on the seller (section 236-C)</li>
</ul>
<p>FBR tax differs for filers and non-filers and changes with the budget. Before making a challan, <a href="https://e.fbr.gov.pk/atl/" rel="noopener" target="_blank">check your filer status</a> and confirm the current rate with the centre or FBR.</p>
<p>Mutation and local government fees are paid at the Bank of Punjab counter at the centre or online through PSID. The FBR challan is generated on the FBR website.</p>
<h2>Can I use any centre?</h2>
<p>The entry can be made at any Arazi Record Centre in Punjab, but the statement is recorded before the Revenue Officer of the area where the land is. A mutation done by the patwari at a Dahi Markaz Mall is just as legally valid.</p>
<h2>How to verify a mutation</h2>
<p>Scan the QR code on the mutation copy. You can also get an attested copy of an approved mutation by paying the required fee.</p>
<h2>If something goes wrong</h2>
<ul>
<li><strong>A mutation was wrongly approved:</strong> appeal to the Assistant Commissioner for inheritance mutations and to the District Collector in other cases. You can also go to the civil court.</li>
<li><strong>Nobody is attesting your mutation:</strong> apply to the Assistant Commissioner, Deputy Commissioner or PLRA.</li>
<li><strong>You paid the fee but the deal was cancelled:</strong> record a cancellation statement before the ADLR, then apply to the Assistant Commissioner for a refund.</li>
</ul>
HTML,
            ],
            'wirasati-intiqal-ka-tareeqa-punjab-kaghzat' => [
                'title' => 'Inheritance Mutation in Punjab: How to Transfer Land to the Legal Heirs',
                'summary' => 'How to transfer land to heirs after a death in Punjab: documents needed, family tree verification, the Arazi Record Centre process and the right to appeal.',
                'content' => <<<'HTML'
<p>After a family member dies, land matters often stay pending for years. Until the inheritance mutation is done, the land remains in the deceased's name, which makes it hard for the heirs to sell or divide it. The process itself is fairly simple.</p>
<h2>Who can apply?</h2>
<p>According to PLRA, <strong>any legal heir</strong> can start the process at an Arazi Record Centre.</p>
<h2>Documents needed</h2>
<ul>
<li>Death certificate</li>
<li>FRC form (Family Registration Certificate)</li>
<li>Affidavit</li>
<li>Property details</li>
<li>CNIC copies of all legal heirs</li>
</ul>
<h2>The procedure</h2>
<ol>
<li>Take all the documents to an Arazi Record Centre.</li>
<li>The file goes to the field staff, who verify the <strong>shajra nasab</strong> (family tree).</li>
<li>After the report, the inheritance mutation is attested at the centre.</li>
</ol>
<h2>Understand the shares first</h2>
<p>Everyone in the family should know their share before the process starts, as this reduces later disputes. Our free <a href="https://www.assanzameen.com/tools/inheritance-calculator">Inheritance Calculator</a> estimates the shares of a spouse, parents, children and full siblings. If grandchildren or other relatives are involved, please ask a scholar or lawyer.</p>
<h2>Can the mutation be verified?</h2>
<p>Yes. An approved mutation copy has a QR code you can scan. After approval the jamabandi changes straight away, and each heir can get a <a href="https://www.assanzameen.com/blog/fard-kaise-hasil-karein-punjab-online-arc-guide">fard</a> showing their share.</p>
<h2>If an heir is left out or a share is wrong</h2>
<p>If an inheritance mutation is wrongly approved, you can <strong>appeal to the Assistant Commissioner</strong> or go to the civil court. Act early, because correction takes longer once a mutation is part of the jamabandi.</p>
<h2>How is the land divided afterwards?</h2>
<p>After an inheritance mutation, land usually stays in a joint holding. If each heir wants a separate piece, you need to apply for <a href="https://www.assanzameen.com/blog/zameen-ki-taqseem-online-darkhwast-punjab">land partition</a>.</p>
HTML,
            ],
            'land-record-durusti-fard-badar-naam-theek-punjab' => [
                'title' => 'How to Correct a Land Record in Punjab: Fard Badar and Name Correction',
                'summary' => 'Wrong name or area on your fard? The four types of land record correction, documents needed, who approves them and when a court order is required.',
                'content' => <<<'HTML'
<p>Sometimes a fard shows a misspelled name or a different area. There is no need to panic. According to PLRA, record correction is <strong>free of cost</strong>, though the route depends on the type of mistake.</p>
<h2>Four types of correction</h2>
<table>
<thead><tr><th>Type</th><th>When it applies</th></tr></thead>
<tbody>
<tr><td>Data entry correction</td><td>The computer record does not match the scanned manual record</td></tr>
<tr><td>Manual record correction</td><td>There is a mistake in the old paper record from before computerization</td></tr>
<tr><td>Mutation correction</td><td>There is a mistake in an approved mutation</td></tr>
<tr><td>Name correction</td><td>There is a minor mistake in a name</td></tr>
</tbody>
</table>
<h2>Where to apply and who approves</h2>
<p>Apply at the relevant Arazi Record Centre or Dahi Markaz Mall, depending on the mistake. The ADLR or Land Record Officer approves corrections. <strong>NADRA biometric verification is mandatory</strong>. If you cannot go yourself, apply through a valid attested Mukhtarnama Aam.</p>
<h2>Documents needed</h2>
<ul>
<li>CNIC (original and copy)</li>
<li>Proof of ownership, such as a registry or mutation</li>
<li>An affidavit explaining the mistake</li>
<li>Supporting evidence, such as the manual record or copies</li>
</ul>
<h2>What is fard badar?</h2>
<p>Fard badar is how mistakes in old paper records are corrected, prepared by the patwari or qanoongo. It covers <strong>only minor mistakes</strong> like spelling, measurement or typing errors that do not affect anyone's ownership. For computer records, a "data entry fard badar" is prepared, and it can be finished in 30 minutes if the documents are in order.</p>
<h2>How to correct a name</h2>
<p>Appear before the ADLR or Land Record Officer with:</p>
<ul>
<li>Two witnesses (a lumberdar and pattidar are preferred)</li>
<li>An affidavit for name correction</li>
<li>Proof of ownership (mutation or registry)</li>
<li>CNIC and other identity documents</li>
</ul>
<p>This normally takes one working day after verification. Only minor corrections are allowed this way. <strong>A complete name change</strong> needs an order from a civil court, the District Collector or the Tehsildar. If the name is wrong in the registry, get the registry corrected at the Sub-Registrar office first.</p>
<h2>When is a court order required?</h2>
<ul>
<li>When the mistake affects someone else's ownership or rights: an order of the District Collector or civil court.</li>
<li>When long-standing entries are wrong: a civil court order.</li>
</ul>
<h2>Correcting an approved mutation</h2>
<p>The revenue officer who approved a mutation can review it. Another officer can do so only with the District Collector's prior permission. This is possible until the mutation is part of the jamabandi. After that, the regular correction process through the District Collector applies.</p>
<h2>If your request is rejected</h2>
<p>Appeal to the Assistant Commissioner or District Collector, depending on the case. If a correction itself is wrong, send a written complaint to the PLRA Complaint Cell or go to the civil court.</p>
HTML,
            ],
            'zameen-ki-taqseem-online-darkhwast-punjab' => [
                'title' => 'Land Partition (Taqseem) in Punjab: How to Apply Online on PULSE',
                'summary' => 'How to separate your share of joint agricultural land: who can apply, the PULSE portal, the Tehsildar\'s role, time, fee and the right to appeal.',
                'content' => <<<'HTML'
<p>Much of Punjab's farmland is held in joint khewats. Your share exists on paper, but which piece on the ground is yours is not settled. This causes disputes and makes selling difficult. The solution is <strong>partition (taqseem)</strong>.</p>
<h2>What does partition mean?</h2>
<p>It separates the shares of co-owners so each gets an independent parcel in their own name. It is done under sections 135, 135-A and 147 of the Land Revenue Act, 1967.</p>
<h2>Who can apply?</h2>
<p>Any joint landholder whose name is in the khewat record. Overseas Pakistanis can apply online and authorize a local representative for field proceedings.</p>
<h2>How to apply online</h2>
<ol>
<li>Open the <a href="https://askfortaqseem.pulse.gop.pk/" rel="noopener" target="_blank">PULSE partition portal</a>.</li>
<li>Select the district, tehsil and mouza.</li>
<li>Enter the khewat number, name, CNIC and mobile number.</li>
<li>Attach a copy of the <a href="https://www.assanzameen.com/blog/fard-kaise-hasil-karein-punjab-online-arc-guide">fard</a> and submit.</li>
<li>Keep the application number. You can track progress with it.</li>
</ol>
<h2>Applying manually</h2>
<p>Submit a written application to your area's Tehsildar with copies of your CNIC and fard.</p>
<h2>Fee and time</h2>
<ul>
<li><strong>Fee:</strong> according to PLRA, there is no fee for filing.</li>
<li><strong>Time:</strong> usually 30 to 60 days, depending on verification and consent.</li>
</ul>
<h2>What happens after you apply?</h2>
<p>The Tehsildar conducts the proceedings and ensures a fair division. If possession on the ground differs from the record, field staff record actual possession and adjust the division with consent. After verification, the Tehsildar or Revenue Officer issues a written order, which goes to the Arazi Record Centre to enter the new ownerships. New khewats are then updated.</p>
<p>An owner normally receives one consolidated parcel unless this is physically impossible.</p>
<h2>When partition cannot go ahead</h2>
<p>If the land is mortgaged or under a court stay, partition is stopped until that is resolved. Check a fresh fard first.</p>
<h2>If you disagree with the order</h2>
<p>You can appeal to the District Collector within the legal time limit.</p>
<h2>Work out your area</h2>
<p>Use our <a href="https://www.assanzameen.com/tools/patwari-land-converter">Land Unit Converter</a> to understand your area in kanal, marla, sarsahi and acre.</p>
HTML,
            ],
            'arazi-moawin-kaise-banein-plra-franchise-punjab' => [
                'title' => 'What Is an Arazi Moawin and How to Become One: PLRA Franchise Guide',
                'summary' => 'The PLRA Arazi Moawin franchise: who can apply, the equipment needed, subscription fees and the services a franchise can offer.',
                'content' => <<<'HTML'
<p>If you understand land record work and want to start a business in your area, PLRA's <strong>Arazi Moawin</strong> model may be an option. It is also useful for citizens to know about, because you no longer always have to visit a large centre for a fard.</p>
<h2>What is an Arazi Moawin?</h2>
<p>It is PLRA's franchise model, where private individuals partner with PLRA and take a subscription to offer land record services. You can also <a href="https://arazimoawin.punjab-zameen.gov.pk/franchisee-locations" rel="noopener" target="_blank">find an Arazi Moawin near you</a>.</p>
<h2>Which services can be offered?</h2>
<ul>
<li>Fard issuance</li>
<li>Mutation (property transfer) processing</li>
</ul>
<p>PLRA says more services, including e-Registration and e-Stamp, will be added in future.</p>
<h2>Who can apply?</h2>
<ul>
<li>Anyone with sufficient knowledge of land record services</li>
<li>A valid CNIC with NADRA biometric verification</li>
<li>A registered mobile number linked to the CNIC</li>
<li>A working email address</li>
</ul>
<h2>How to apply</h2>
<ol>
<li>Complete the online application on the <a href="https://arazimoawin.punjab-zameen.gov.pk/" rel="noopener" target="_blank">Arazi Moawin portal</a>.</li>
<li>Choose a subscription package.</li>
<li>Complete biometric verification.</li>
<li>Upload the front and back of your CNIC and any other required documents.</li>
<li>PLRA reviews and approves the application.</li>
</ol>
<h2>Equipment needed</h2>
<ul>
<li>Seating space</li>
<li>A computer (Core i5, 8GB RAM, SSD) and UPS</li>
<li>Printer, scanner, biometric device and webcam</li>
<li>Windows 10 or 11, antivirus and the required software</li>
<li>Internet of at least 5Mbps</li>
</ul>
<h2>How much does it cost?</h2>
<p>According to PLRA, subscriptions start from Rs. 5,000 for fard issuance and Rs. 5,000 for mutation processing. The fee varies by tehsil category:</p>
<table>
<thead><tr><th>Tehsil category</th><th>Fee (Rs.)</th></tr></thead>
<tbody>
<tr><td>Platinum</td><td>20,000</td></tr>
<tr><td>Gold</td><td>10,000</td></tr>
<tr><td>Silver</td><td>5,000</td></tr>
</tbody>
</table>
<p>Payment is by PSID through online banking or a bank counter. Paying two or more months in advance earns a discount. Auto-renewal can be switched on or off from the dashboard.</p>
<h2>How is income earned?</h2>
<p>Government service charges are fixed, but PLRA says a franchise owner may set additional charges agreed with the customer. Income depends on demand in your area, so estimate it in your tehsil before investing. Confirm current packages and terms on the portal.</p>
<h2>Does PLRA provide support?</h2>
<p>Yes: online training, manuals, customer support, troubleshooting, updates and security checks. Tehsils can be added or removed later. Refunds apply only to future months, not the current one.</p>
HTML,
            ],
            'plot-khareedne-se-pehle-sarkari-checks-punjab' => [
                'title' => '6 Official Checks Before Buying a Plot in Punjab: Fard, Society, DC Rate and Filer Status',
                'summary' => 'Six things to check yourself on official portals before paying bayana for a plot, to avoid fraud, fake stamps and extra tax.',
                'content' => <<<'HTML'
<p>Before putting your life savings into a plot, do not rely only on what a dealer tells you. You can check the following yourself from official sources, and many of them from home.</p>
<h2>1. Get a fresh fard</h2>
<p>Do not rely on an old fard from the seller. PLRA itself advises getting the latest fard before any deal, because a transfer or mutation can change ownership. Scan the QR code on it too. <a href="https://www.assanzameen.com/blog/fard-kaise-hasil-karein-punjab-online-arc-guide">See how to get a fard</a>.</p>
<h2>2. Check the housing society</h2>
<p>If you are buying a plot or file in a society, first check the <a href="https://www.punjab-zameen.gov.pk/approvedSocieties" rel="noopener" target="_blank">official PLRA list</a>. Then confirm the NOC and layout plan with the relevant body, such as the TMA or development authority. The word "approved" on a brochure is not enough.</p>
<h2>3. Find the DC rate</h2>
<p>The official registry cost is linked to DC valuation, even if your deal price is lower. Knowing the rate early prevents budget surprises. <a href="https://www.assanzameen.com/blog/dc-valuation-kaise-check-karein-punjab">See how to check the DC rate</a>.</p>
<h2>4. Check filer status</h2>
<p>FBR tax on property differs for filers and non-filers, and non-filers pay more. Before finalizing, check the <a href="https://e.fbr.gov.pk/atl/" rel="noopener" target="_blank">FBR Active Taxpayers List</a>. To file a return, use the <a href="https://iris.fbr.gov.pk/" rel="noopener" target="_blank">FBR IRIS portal</a>.</p>
<h2>5. Verify stamps and papers</h2>
<p>You can check an e-Stamp for free by entering its serial number on the portal or scanning its QR code. Mutation copies have QR codes too. <a href="https://www.assanzameen.com/blog/e-stamp-challan-32a-kaise-banayein-punjab-guide">Read the e-Stamp guide</a>.</p>
<h2>6. Check possession and boundaries yourself</h2>
<p>No website can do this. Visit the land, see who has possession and whether the measurement matches the papers, and ask the neighbours. In a joint khewat, get the seller's share and boundary made clear, or consider <a href="https://www.assanzameen.com/blog/zameen-ki-taqseem-online-darkhwast-punjab">partition</a>.</p>
<h2>Use our free tools</h2>
<ul>
<li><a href="https://www.assanzameen.com/tools/legal-scanner">Property Document Checker</a>: a step by step list of the checks still open before you pay.</li>
<li><a href="https://www.assanzameen.com/tools/registry-tax-calculator">Registry & Tax Calculator</a>: an estimate of the total cost.</li>
<li><a href="https://www.assanzameen.com/tools/bayana-deed-generator">Bayana Agreement Maker</a>: an Urdu draft to show a lawyer before signing.</li>
</ul>
<h2>Beware of people posing as officials</h2>
<p>If someone claims to represent an office and asks for your CNIC, password or OTP, do not share it. Pay government fees only through an official challan or at the office, and keep the receipt. You can complain to PLRA on its <a href="https://www.punjab-zameen.gov.pk/complaints" rel="noopener" target="_blank">complaints page</a>.</p>
HTML,
            ],
        ];
    }
}
