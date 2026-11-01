<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

/**
 * Purane Urdu articles ka English version. Sirf mojooda posts par chalta hai, nayi post nahi banata,
 * aur jis post ka English pehle se likha ho usay nahi chherta.
 *
 * Yeh lafzi tarjuma nahi: jo baat PLRA ke FAQs se nahi milti ya ghalat thi, woh English mein theek likhi hai.
 *
 * Pehle migration chalana zaroori hai. Live par: php artisan db:seed --class=OldPostsEnglishSeeder --force
 */
class OldPostsEnglishSeeder extends Seeder
{
    public function run(): void
    {
        $updated = 0;

        foreach (self::posts() as $slug => $en) {
            $post = Post::where('slug', $slug)->first();
            if (! $post || $post->hasEnglish()) {
                continue;
            }

            // Urdu mehfooz: agar migration ne copy na kiya ho to ab copy
            $post->title_ur = $post->title_ur ?: $post->title;
            $post->summary_ur = $post->summary_ur ?: $post->summary;
            $post->content_ur = $post->content_ur ?: $post->content;

            $post->title = $en['title'];
            $post->summary = $en['summary'];
            $post->content = $en['content'] . GovernmentGuideEnglish::footer();
            $post->save();
            $updated++;
        }

        $this->command?->info("English add hui: {$updated}");
    }

    public static function posts(): array
    {
        return [
            'succession-certificate-inheritance-intiqal-guide-punjab' => [
                'title' => 'Inherited Property in Punjab: Succession Certificate, Family Tree and Mutation',
                'summary' => 'How heirs can transfer inherited property in Punjab: death certificate, FRC, NADRA succession certificate, family tree verification and inheritance mutation.',
                'content' => <<<'HTML'
<p>Transferring a family member's property to the heirs used to mean years in court. Today there are simpler routes through NADRA and the Arazi Record Centres, but you still need the right papers in the right order.</p>
<h2>Step 1: Death certificate and FRC</h2>
<ul>
<li>Get the computerized death certificate from the Union Council.</li>
<li>Get a Family Registration Certificate (FRC) from NADRA showing the legal heirs.</li>
</ul>
<h2>Step 2: Succession certificate, where needed</h2>
<p>NADRA also issues succession certificates and letters of administration through its Succession Facilitation Units, so many families can avoid a court application. Generally all heirs complete biometric verification, a public notice is given so that any other heir can come forward, and the certificate is issued if nobody objects. Check the current requirements and time at NADRA, and ask the Arazi Record Centre whether your case needs this certificate.</p>
<h2>Step 3: Inheritance mutation</h2>
<p>According to PLRA, any legal heir can approach an Arazi Record Centre with:</p>
<ul>
<li>Death certificate</li>
<li>FRC form</li>
<li>Affidavit</li>
<li>Property details</li>
<li>CNIC copies of all legal heirs</li>
</ul>
<p>The file goes to field staff for shajra nasab (family tree) verification, and after the report the inheritance mutation is attested at the centre. Read our full <a href="https://www.assanzameen.com/blog/wirasati-intiqal-ka-tareeqa-punjab-kaghzat">inheritance mutation guide</a>.</p>
<h2>Protect the rights of every heir</h2>
<p>Daughters, widows and other heirs have a legal share. Leaving a woman out of a mutation or hiding her name is against the law and can be challenged. If an inheritance mutation is wrongly approved, PLRA says you can appeal to the Assistant Commissioner or go to the civil court.</p>
<p>Our free <a href="https://www.assanzameen.com/tools/inheritance-calculator">Inheritance Calculator</a> gives an estimate of the shares. Please confirm with a scholar or lawyer.</p>
HTML,
            ],
            'dc-rate-vs-fbr-rate-market-value-difference-punjab' => [
                'title' => 'DC Rate vs FBR Value vs Market Rate: What Is the Difference in Punjab?',
                'summary' => 'The three property values used in Pakistan: DC rate, FBR valuation and market price. Learn which one affects stamp duty and federal tax.',
                'content' => <<<'HTML'
<p>A buyer often agrees a price of 50 lakh, then sees a different figure on the registry challan. That is because three different values are used in a property deal.</p>
<h2>1. DC rate</h2>
<p>This is the official value notified by the District Collector, and it varies by location, land type and category. PLRA says it is the basis for stamp duty, registration fees and other government charges. You can <a href="https://www.assanzameen.com/blog/dc-valuation-kaise-check-karein-punjab">check the DC rate online</a>.</p>
<h2>2. FBR valuation</h2>
<p>FBR publishes its own valuation tables for many cities and housing schemes. They are used for federal income tax on property, such as section 236-C on the seller and section 236-K on the buyer. Confirm with FBR or a tax adviser how the value applies to your deal.</p>
<h2>3. Market rate</h2>
<p>This is the price the buyer actually pays the seller. It depends on demand, road width, commercial value and development, and it is often higher than the official values.</p>
<h2>The rule that matters: the higher value</h2>
<p>PLRA's e-Stamp guidance says stamp duty is calculated on the <strong>declared value or the DC valuation, whichever is higher</strong>. So a low declared price will not reduce the duty, and a declared price above the DC rate means duty on the declared price.</p>
<h2>A simple example</h2>
<table>
<thead><tr><th>Value of a 5 marla house</th><th>Amount</th></tr></thead>
<tbody>
<tr><td>DC rate</td><td>Rs. 20 lakh</td></tr>
<tr><td>FBR valuation</td><td>Rs. 28 lakh</td></tr>
<tr><td>Agreed market price</td><td>Rs. 50 lakh</td></tr>
</tbody>
</table>
<p>If you declare Rs. 50 lakh, the e-Stamp system charges stamp duty on Rs. 50 lakh, as it is higher than the DC rate. If you declared only Rs. 15 lakh, duty would still be charged on Rs. 20 lakh. These figures are examples only.</p>
<p>The fastest way to see the real amount is to <a href="https://www.assanzameen.com/blog/e-stamp-challan-32a-kaise-banayein-punjab-guide">create an e-Stamp challan</a>, because the system calculates it. Check the latest rates before you register, as a wrong value can cause an objection.</p>
HTML,
            ],
            'non-filer-property-purchase-ban-fbr-section-7e-rules' => [
                'title' => 'Non-Filers and Property in Pakistan: Higher Tax, Section 7E and Bank Payments',
                'summary' => 'What non-filers should know before buying or selling property: higher 236-K and 236-C tax, section 7E and why you should pay through a bank.',
                'content' => <<<'HTML'
<p>FBR has made property deals much more expensive for people who are not on the Active Taxpayers List. Before you buy or sell, understand what your status means.</p>
<h2>Filers pay less</h2>
<p>Two federal taxes apply to most sales:</p>
<ul>
<li><strong>Section 236-K:</strong> advance tax on the buyer.</li>
<li><strong>Section 236-C:</strong> tax on the seller.</li>
</ul>
<p>Non-filers pay these at much higher rates than active filers. Rates change with the budget, so confirm the current ones with FBR or a tax adviser. Our <a href="https://www.assanzameen.com/tools/registry-tax-calculator">Registry & Tax Calculator</a> gives an estimate.</p>
<h2>Check your status</h2>
<p>Look up your CNIC on the <a href="https://e.fbr.gov.pk/atl/" rel="noopener" target="_blank">FBR Active Taxpayers List</a>. To become a filer, submit your return through the <a href="https://iris.fbr.gov.pk/" rel="noopener" target="_blank">FBR IRIS portal</a>.</p>
<h2>What is section 7E?</h2>
<p>Section 7E of the Income Tax Ordinance deals with tax on deemed income from certain immovable property, with exemptions in some cases. A seller may be asked for a certificate from FBR showing the tax is paid or an exemption applies. Check with a tax adviser well before your sale, as a missing certificate can delay the transfer.</p>
<h2>Pay through a bank</h2>
<p>Income tax law restricts large cash payments for immovable property. Whatever the amount, a bank payment such as a pay order, demand draft or crossed cheque gives you a clear record, while large cash deals can bring questions from the tax department. Attach the payment record to your agreement.</p>
<h2>Our advice</h2>
<p>If you plan to buy, consider becoming a filer first, because the tax saved on one deal can be significant. Ask a qualified tax adviser about your own case.</p>
HTML,
            ],
            'punjab-e-registration-biometric-registry-dehi-markaz-maal-guide' => [
                'title' => 'Punjab e-Registration: Online Registry, Biometric Verification and Auto Mutation',
                'summary' => 'How the Punjab e-Registration system works, from the online challan to Sub-Registrar statements, biometric checks and automatic mutation in online mouzas.',
                'content' => <<<'HTML'
<p>PLRA and the Board of Revenue have moved property registration to a digital system, which reduces paperwork and improves record keeping for buyers and sellers.</p>
<h2>How e-Registration works</h2>
<ol>
<li><strong>Online challan:</strong> create an <a href="https://www.assanzameen.com/blog/e-stamp-challan-32a-kaise-banayein-punjab-guide">e-Stamp Challan 32-A</a> and pay stamp duty, FBR taxes, registration fee, mutation fee and other charges.</li>
<li><strong>Portal login:</strong> create a user login on the <a href="https://eregistration.punjab-zameen.gov.pk/" rel="noopener" target="_blank">e-Registration portal</a>.</li>
<li><strong>Start the deed:</strong> begin the registration request in the system.</li>
<li><strong>Enter the details:</strong> verify paid challans and enter the buyer, seller and witnesses.</li>
<li><strong>Sub-Registrar:</strong> the request is forwarded, and statements of the parties and witnesses are recorded.</li>
<li><strong>Issue:</strong> the registered document is issued with its Bahi number and document number.</li>
</ol>
<h2>Paying the charges</h2>
<p>Challan amounts can be paid at any Bank of Punjab branch or online through PSID and banking apps.</p>
<h2>Automatic mutation</h2>
<p>According to PLRA, in online mouzas the mutation of an e-Registry is processed automatically, so the registry and mutation are approved together. The owner's name then changes in the record without a separate mutation visit.</p>
<h2>Before you buy</h2>
<p>Get a <a href="https://www.assanzameen.com/blog/fard-kaise-hasil-karein-punjab-online-arc-guide">fresh fard</a> from an official source and verify its QR code. Buying land where the record is unclear can cost you dearly, so check this before paying. Assan Zameen does not verify ownership. Use the official PLRA services and a qualified lawyer.</p>
<p>See our step by step <a href="https://www.assanzameen.com/blog/nayi-registry-ka-tareeqa-e-registration-punjab-steps">new registry guide</a> for more detail.</p>
HTML,
            ],
            'punjab-property-transfer-system-pts-housing-societies-rules' => [
                'title' => 'Punjab Property Transfer System: Linking Housing Society Files to Official Records',
                'summary' => 'What the PLRA property transfer system means for housing society plots and files, the published PLRA charges, and how to protect yourself as a buyer.',
                'content' => <<<'HTML'
<p>Fake files and several allotment letters for one plot have long been a risk in private housing schemes. PLRA now provides systems that link societies and development authorities to its records, which can make transfers easier to trace.</p>
<h2>How it works</h2>
<p>PLRA describes the property transfer process as submitting an application, giving buyer and seller details, paying fees by challan, completing biometric verification and receiving a transfer report. It also offers a <a href="https://hsms.punjab-zameen.gov.pk/" rel="noopener" target="_blank">Housing Societies Management System (HSMS)</a> for approved societies.</p>
<p>Both the buyer and seller must complete biometric verification at an Arazi Record Centre.</p>
<h2>Published PLRA charges</h2>
<p>The PLRA schedule for FY 2025-26 lists:</p>
<ul>
<li>Transfer of properties in cooperative societies and development authorities: Rs. 2,500</li>
<li>HSMS transaction: Rs. 3,000 per transaction</li>
<li>One-time onboarding by approved societies: Rs. 5,000 per property</li>
</ul>
<p>Charges are revised, so check the <a href="https://www.punjab-zameen.gov.pk/guidanceAndRegulation" rel="noopener" target="_blank">latest PLRA notifications</a>.</p>
<h2>How this helps buyers</h2>
<ul>
<li>Transfers go through an official process with biometric checks, not just a society register.</li>
<li>There is an official record to refer to if a dispute arises.</li>
</ul>
<p>No system removes every risk, so a society being on a system does not by itself prove that a particular file is genuine.</p>
<h2>Before you buy a file</h2>
<ol>
<li>Check the <a href="https://www.punjab-zameen.gov.pk/approvedSocieties" rel="noopener" target="_blank">official PLRA society list</a>.</li>
<li>Confirm the NOC and layout plan with the TMA or development authority.</li>
<li>Ask the society to confirm the allotment in writing.</li>
<li>Follow our <a href="https://www.assanzameen.com/blog/plot-khareedne-se-pehle-sarkari-checks-punjab">6 official checks before buying a plot</a>.</li>
</ol>
HTML,
            ],
            'punjab-green-property-certificate-plra-complete-guide' => [
                'title' => 'Green Property Certificate in Punjab: Fee, Process and How to Apply',
                'summary' => 'What the PLRA Green Property Certificate (GPC) is, how to apply at an Arazi Record Centre, the fee, neighbour statements, refusal and appeal.',
                'content' => <<<'HTML'
<p>A fard shows what the record says, but it may not settle who actually holds the land. PLRA's <strong>Green Property Certificate (GPC)</strong> is meant to close that gap.</p>
<h2>What is a GPC?</h2>
<p>According to PLRA, it is an electronically generated ownership document that verifies the legal status, ownership and possession of a specific piece of land.</p>
<h2>How to apply</h2>
<ol>
<li><strong>Visit an Arazi Record Centre:</strong> you must go in person to the nearest centre.</li>
<li><strong>Identity check:</strong> the officer checks your original CNIC, completes NADRA biometric verification and verifies your registered mobile number.</li>
<li><strong>Pay the fee:</strong> the initial processing fee is listed as <strong>Rs. 900</strong>, payable at a Bank of Punjab counter or branch, or through PSID using JazzCash, EasyPaisa or online banking.</li>
<li><strong>Record review:</strong> officials match your details with land records, review the ownership history and check for tax liabilities, bank mortgages and court orders.</li>
<li><strong>Neighbour statements:</strong> at least two neighbouring landowners, whose records are in the computerized system, give statements before the Assistant Director Land Records with biometric verification. This confirms you are in possession.</li>
<li><strong>Final approval:</strong> the ADLR or an authorized officer reviews the case and approves it.</li>
</ol>
<p>Once the process begins, other transactions on the property are temporarily suspended.</p>
<h2>Joint owners</h2>
<p>For agricultural land, all co-owners must consent. Without that consent, a legal <a href="https://www.assanzameen.com/blog/zameen-ki-taqseem-online-darkhwast-punjab">partition</a> must be completed first.</p>
<h2>If your application is refused</h2>
<p>You receive a refusal letter with the reasons. You can appeal to the Assistant Commissioner within 30 days, or apply again once the cause is resolved.</p>
<h2>How to verify a GPC</h2>
<p>A GPC is system-generated and needs no signature or stamp. Verify it by scanning its QR code.</p>
<h2>Should you rely on it when buying?</h2>
<p>A GPC is a strong official record, but still scan its QR code, get a fresh fard and take legal advice. <a href="https://www.punjab-zameen.gov.pk/gpcinfo" rel="noopener" target="_blank">Read the official PLRA guide</a>.</p>
HTML,
            ],
            'property-bayana-token-money-cancellation-legal-rights-pakistan' => [
                'title' => 'Bayana and Token Money in Pakistan: What Happens If a Property Deal Is Cancelled?',
                'summary' => 'Token money vs a bayana agreement, what usually happens if a buyer or seller backs out, and how to protect your advance payment in a property deal.',
                'content' => <<<'HTML'
<p>Many property disputes in Pakistan begin because the deal was never properly written down. A buyer hands over cash, the owner later backs out, and there is no proof. Here is how to protect yourself.</p>
<h2>Token money and a bayana agreement</h2>
<ul>
<li><strong>Token money:</strong> a small initial payment showing serious intent, so the owner stops negotiating with others. It is usually for a few days only.</li>
<li><strong>Bayana agreement:</strong> a written agreement, usually on stamp paper, recording a larger advance payment and a date for the registry. In practice, parties often agree around 10 to 25 percent and 30 to 60 days.</li>
</ul>
<h2>If the deal is cancelled</h2>
<p>What happens depends mostly on <strong>what your agreement says</strong> and the facts of your case, which a court may review under the Contract Act 1872 and related law.</p>
<ul>
<li><strong>If the buyer backs out:</strong> agreements often allow the seller to keep the bayana, so a clear written clause is important.</li>
<li><strong>If the seller backs out:</strong> agreements often require the seller to return the bayana plus an equal amount as a penalty. The buyer may also be able to ask a court to enforce the sale.</li>
</ul>
<p>These are common terms, not automatic rights. Have a lawyer check your clauses before you sign.</p>
<h2>The safest way to pay</h2>
<ol>
<li>Do not pay cash without proof. Use a pay order, crossed cheque or bank transfer.</li>
<li>Attach the payment record to the agreement.</li>
<li>Have two adult witnesses sign with thumb impressions.</li>
<li>Check the <a href="https://www.assanzameen.com/blog/fard-kaise-hasil-karein-punjab-online-arc-guide">latest fard</a> before paying, to be sure the seller is the owner.</li>
</ol>
<p>Our <a href="https://www.assanzameen.com/tools/bayana-deed-generator">Bayana Agreement Maker</a> prepares an Urdu draft to review with your lawyer and print on e-stamp paper.</p>
HTML,
            ],
            'overseas-pakistanis-online-land-record-poa-verification' => [
                'title' => 'Overseas Pakistanis and Property in Punjab: Power of Attorney and Land Records',
                'summary' => 'How overseas Pakistanis can protect property in Punjab: digital power of attorney, MOFA verification, online fard and recording statements from abroad.',
                'content' => <<<'HTML'
<p>Overseas Pakistanis invest heavily in property back home, and their land has often been a target for fake powers of attorney. Digital services now make it easier to manage and watch over property from abroad.</p>
<h2>Digital power of attorney</h2>
<p>If you want a relative or trusted person to act for you, NADRA and the Ministry of Foreign Affairs offer a digital process, so you may not need to travel. It generally involves:</p>
<ol>
<li>An online application with your NICOP, your attorney's CNIC and the property details.</li>
<li>Biometric verification through NADRA's Pak ID mobile app.</li>
<li>An interview with a consular officer at a Pakistan mission.</li>
<li>Verification through MOFA.</li>
</ol>
<p>Check the current steps with NADRA or your nearest mission before starting.</p>
<h2>What PLRA requires</h2>
<p>PLRA says a valid, attested power of attorney must be verified by MOFA and authenticated by the Sub-Registrar in Pakistan. After that, a fard can be issued against it.</p>
<h2>Giving your statement from abroad</h2>
<ul>
<li><strong>Mutation:</strong> a representative at the Pakistan mission records your statement as commission for the Revenue Officer. Parties in Punjab appear before the Revenue Officer.</li>
<li><strong>Registered deed:</strong> the Sub-Registrar appoints the mission official as commission to record your statement, including thumb impression, photo and video.</li>
</ul>
<h2>Other services from abroad</h2>
<ul>
<li>Apply for a <a href="https://www.assanzameen.com/blog/fard-kaise-hasil-karein-punjab-online-arc-guide">fard</a> online.</li>
<li>Generate <a href="https://www.assanzameen.com/blog/e-stamp-challan-32a-kaise-banayein-punjab-guide">e-Stamp challans</a> and pay through authorized channels or a representative.</li>
<li>Apply for <a href="https://www.assanzameen.com/blog/zameen-ki-taqseem-online-darkhwast-punjab">partition</a> and authorize a local representative for field work.</li>
</ul>
<h2>A simple way to stay safe</h2>
<p>If you have empty land or a vacant plot, check a fresh fard regularly, at least twice a year, to make sure nothing has changed in the record. Give a power of attorney only to someone you fully trust, and limit it to what is needed.</p>
HTML,
            ],
            'punjab-e-abiana-online-tax-payment-easypaisa-guide' => [
                'title' => 'Punjab e-Abiana: How to Check and Pay Your Canal Water Bill Online',
                'summary' => 'How farmers in Punjab can check their e-Abiana canal water bill and pay it with a PSID through Easypaisa, JazzCash or a banking app.',
                'content' => <<<'HTML'
<p>The Punjab Irrigation Department has moved abiana, the charge for canal water, to a digital system. Farmers can now check their bill and pay it from a phone instead of relying only on paper slips.</p>
<h2>What is e-Abiana?</h2>
<p>It is the digital abiana system that links canal-irrigated land with the farmer's details. A computerized bill is issued for each crop season.</p>
<h2>How to check your bill</h2>
<ol>
<li>Open the Irrigation Department's e-Abiana portal or the e-Pay Punjab app.</li>
<li>Select your district, tehsil and mouza.</li>
<li>Enter your canal account number or CNIC.</li>
<li>The system shows your area, the amount due and a PSID challan number.</li>
</ol>
<h2>How to pay</h2>
<ol>
<li>Open Easypaisa, JazzCash or your banking app.</li>
<li>Go to government payments or e-Pay Punjab.</li>
<li>Choose e-Abiana if it is listed, and enter your PSID.</li>
<li>Check that the name and amount are correct, then pay.</li>
<li>Save the digital receipt.</li>
</ol>
<p>App menus differ and can change, so if you cannot find the option, ask your bank or the Irrigation Department. You can also check the official <a href="https://epay.punjab.gov.pk/" rel="noopener" target="_blank">e-Pay Punjab website</a>.</p>
<h2>If the bill looks wrong</h2>
<p>If the area or amount looks wrong, do not ignore it. Contact your local Irrigation Department office with your account details and last receipt, and keep all your payment records.</p>
<h2>Why keep your payments up to date?</h2>
<p>Clear records help avoid disputes and surprises when you deal with your land. If you plan to sell, ask your Arazi Record Centre whether any dues need clearing, and get a <a href="https://www.assanzameen.com/blog/fard-kaise-hasil-karein-punjab-online-arc-guide">fresh fard</a>.</p>
HTML,
            ],
            'land-measurement-units-marla-kanal-sarsahi-karam' => [
                'title' => 'Marla, Kanal, Karam and Sarsahi: How Land Is Measured in Punjab',
                'summary' => 'The difference between a 225 sq ft society marla and a 272.25 sq ft revenue marla, plus karam, sarsahi and how to work out your plot size yourself.',
                'content' => <<<'HTML'
<p>In a property deal, a patwari may talk in karam and sarsahi while a housing society uses square feet and square yards. Knowing the basics yourself can save you from an expensive misunderstanding.</p>
<h2>Two different marlas</h2>
<ul>
<li><strong>Society marla (225 sq ft):</strong> many private housing societies and urban authorities treat 1 marla as 225 sq ft, or 25 square yards. A 5 marla plot is then 1,125 sq ft.</li>
<li><strong>Revenue marla (272.25 sq ft):</strong> the revenue department and rural land records use 272.25 sq ft. On this scale, 1 kanal is 5,445 sq ft.</li>
</ul>
<p>Always ask which marla a seller means, because the same "5 marla" can differ by more than 230 sq ft.</p>
<h2>Traditional revenue units</h2>
<table>
<thead><tr><th>Unit</th><th>Equal to</th></tr></thead>
<tbody>
<tr><td>Karam (length)</td><td>5.5 feet (66 inches)</td></tr>
<tr><td>Sarsahi (1 square karam)</td><td>30.25 sq ft</td></tr>
<tr><td>Marla</td><td>9 sarsahi = 272.25 sq ft</td></tr>
<tr><td>Kanal</td><td>20 marla = 5,445 sq ft</td></tr>
<tr><td>Acre</td><td>8 kanal = 43,560 sq ft</td></tr>
<tr><td>Murabba</td><td>25 acres</td></tr>
</tbody>
</table>
<h2>Work out your plot size</h2>
<ol>
<li>Measure the length and width in feet.</li>
<li>Multiply them. For example, 25 ft × 45 ft = 1,125 sq ft.</li>
<li>For a society plot, divide by 225: 1,125 ÷ 225 = 5 marla.</li>
<li>For revenue records, divide by 272.25: 1,125 ÷ 272.25 = about 4.13 marla.</li>
</ol>
<h2>Crooked plots</h2>
<p>This simple method only suits rectangular plots. For an irregular shape, use our free <a href="https://www.assanzameen.com/tools/patwari-land-converter">Land Unit Converter</a>, which converts units and estimates area from the four sides and a diagonal. For a registry or dispute, get the final measurement from the patwari or a licensed surveyor.</p>
HTML,
            ],
            'online-fard-malkiyat-punjab-arazi-record-center' => [
                'title' => 'Online Fard in Punjab: Types of Fard and How to Apply on the PLRA Portal',
                'summary' => 'The types of fard in Punjab and how to apply online through PLRA: login, ownership details, fee payment, QR code verification and status tracking.',
                'content' => <<<'HTML'
<p>Checking a land record no longer has to mean a full day in a queue. PLRA has computerized records, and in many cases you can apply for a fard online.</p>
<h2>Types of fard</h2>
<ul>
<li><strong>Information fard:</strong> for checking and verification only.</li>
<li><strong>Sale fard:</strong> used for a registry and transfer of ownership. The buyer's name and CNIC are mandatory.</li>
<li><strong>Surety (zamanat) fard:</strong> used in court matters. You may also get a sale fard if the court has not restricted the sale.</li>
</ul>
<h2>Applying online</h2>
<ol>
<li>Open the <a href="https://onlinefard.punjab-zameen.gov.pk/" rel="noopener" target="_blank">PLRA online fard portal</a>, or use the Punjab Zameen mobile app.</li>
<li>Log in and verify the ownership details.</li>
<li>Pay the fee online.</li>
<li>Get your fard, and note its fard ID.</li>
</ol>
<p>Your record must be computerized for this to work. If it is not, contact the area patwari.</p>
<h2>What does it cost?</h2>
<p>According to PLRA, the fee is normally Rs. 50 per khatooni, up to Rs. 150 per khewat, plus PLRA service charges. The system shows the final amount, which depends on your record. The service charge for a copy of a record is set out in the <a href="https://www.punjab-zameen.gov.pk/guidanceAndRegulation" rel="noopener" target="_blank">PLRA notifications</a>.</p>
<h2>Verify every fard</h2>
<p>Each fard has a QR code. Scan the seller's fard before buying, to protect yourself from fake papers. PLRA also advises getting the latest fard before any transaction. You can check a request's status online with your fard ID.</p>
<p>See our <a href="https://www.assanzameen.com/blog/fard-kaise-hasil-karein-punjab-online-arc-guide">full fard guide</a> for documents, lost fards and fingerprint problems.</p>
HTML,
            ],
            'punjab-plot-registry-tax-fee-schedule-guide' => [
                'title' => 'Plot Registry Costs in Punjab: Stamp Duty, TMA, FBR Tax and PLRA Charges',
                'summary' => 'The main government charges on a property registry in Punjab: stamp duty, local council fee, FBR 236-K and 236-C tax, PLRA and registration fees.',
                'content' => <<<'HTML'
<p>Many buyers arrive at the registry office without knowing what they will have to pay. Here are the main charges to plan for. Rates change, and some published figures differ, so treat this as a checklist and let the official challan give you the final amount.</p>
<h2>1. Stamp duty</h2>
<p>Stamp duty is paid through an e-Stamp challan. PLRA says it is calculated on the declared value or the DC valuation, whichever is higher. The rate depends on current Punjab law, so see the amount the <a href="https://www.assanzameen.com/blog/e-stamp-challan-32a-kaise-banayein-punjab-guide">e-Stamp system</a> calculates.</p>
<h2>2. Local council fee</h2>
<p>A local government charge may apply, depending on the area. Ask the registry office or Arazi Record Centre what applies to your property.</p>
<h2>3. FBR tax on the buyer (section 236-K)</h2>
<p>Advance tax on the buyer depends on filer status and the property value. Non-filers pay a much higher rate. Check your <a href="https://e.fbr.gov.pk/atl/" rel="noopener" target="_blank">status on the ATL</a>.</p>
<h2>4. FBR tax on the seller (section 236-C)</h2>
<p>The seller also pays tax based on filer status, and non-filers pay much more. Agree early who pays what.</p>
<h2>5. PLRA and registration charges</h2>
<ul>
<li><strong>PLRA service charge:</strong> the FY 2025-26 schedule lists 0.1% of land value on sale and purchase through e-Registration, subject to a minimum of Rs. 3,300.</li>
<li><strong>Registration fee:</strong> paid along with stamp duty and CVT on Challan 32-A.</li>
<li><strong>Mutation:</strong> check the current fee at the centre, and see our <a href="https://www.assanzameen.com/blog/bai-zubani-intiqal-ka-tareeqa-fee-punjab">mutation guide</a>.</li>
</ul>
<h2>Plan your budget</h2>
<ol>
<li>Check the <a href="https://www.assanzameen.com/blog/dc-valuation-kaise-check-karein-punjab">DC rate</a> for your area.</li>
<li>Estimate the total with our <a href="https://www.assanzameen.com/tools/registry-tax-calculator">Registry & Tax Calculator</a>.</li>
<li>Generate the official challan for the exact amount before you pay.</li>
</ol>
HTML,
            ],
        ];
    }
}
