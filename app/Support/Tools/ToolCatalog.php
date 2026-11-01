<?php

namespace App\Support\Tools;

/** Tools ki ek hi fehrist: hub page, related links, SEO title aur guide yahin se bante hain. */
class ToolCatalog
{
    public static function all(): array
    {
        return [
            'scanner' => [
                'route' => 'tools.legal-scanner', 'icon' => 'fa-file-circle-check',
                'name' => 'Property Document Checker',
                'seo' => 'Property Document Checklist for Fard, Registry and Intiqal',
                'desc' => 'Check a fard, registry, allotment letter or intiqal step by step and see the risks to clear before you pay.',
                'guide' => [
                    'Choose the document you were given and answer each question honestly. Choose Not sure if you have not checked it yet.',
                    'A No on an important check marks the deal as high risk. Clear it with the record office before paying any money.',
                    'This tool does not connect to PLRA or any government record, so it cannot verify ownership by itself.',
                ],
            ],
            'tax' => [
                'route' => 'tools.tax-calculator', 'icon' => 'fa-calculator',
                'name' => 'Registry & Tax Calculator',
                'seo' => 'Punjab Property Registry and FBR 236K 236C Tax Calculator',
                'desc' => 'Estimate stamp duty, TMA tax, registry fees and FBR 236-K and 236-C tax for each buyer and seller.',
                'guide' => [
                    'Use the DC or FBR valuation rate of your area, because tax is charged on the official value when it is higher than the deal price.',
                    'Add every buyer and seller with their share. Shares on each side should total 100%.',
                    'Rates change with each budget. Confirm the final challan at the registry office before paying.',
                ],
            ],
            'land' => [
                'route' => 'tools.land-converter', 'icon' => 'fa-ruler-combined',
                'name' => 'Land Unit Converter',
                'seo' => 'Marla Kanal Acre Converter and Irregular Plot Area Calculator',
                'desc' => 'Convert marla, kanal, acre, karam and sarsahi, and measure the area of an irregular plot.',
                'guide' => [
                    'Select the correct marla first: 225 sq ft is common in societies and 272.25 sq ft in revenue records.',
                    'For a crooked plot, measure all four sides and the diagonal. The diagonal gives a far more accurate area.',
                    'For a registry or a dispute, get the final measurement from the patwari or a licensed surveyor.',
                ],
            ],
            'bayana' => [
                'route' => 'tools.bayana-generator', 'icon' => 'fa-file-signature',
                'name' => 'Bayana Agreement Maker',
                'seo' => 'Bayana Agreement Maker for Property Sale in Pakistan',
                'desc' => 'Prepare an Urdu token money agreement for a property sale, ready to print on e-stamp paper.',
                'guide' => [
                    'Copy names, CNIC numbers and property details exactly from the CNIC and the fard. Your entries are not saved on our server.',
                    'Read every clause, especially the penalty and possession terms, and change the draft if your deal is different.',
                    'Have a lawyer review it before signing. Use the correct e-stamp and two witnesses.',
                ],
            ],
            'society' => [
                'route' => 'tools.society-directory', 'icon' => 'fa-building-circle-check',
                'name' => 'Society NOC Directory',
                'seo' => 'Housing Society NOC Status Directory in Punjab',
                'desc' => 'Look up the recorded approval status of housing societies before buying a plot or a file.',
                'guide' => [
                    'Search by society name, developer or NOC number.',
                    'A status here is only a starting point and may be out of date.',
                    'Always confirm the current NOC and layout plan directly with the TMA, development authority or district council.',
                ],
            ],
            'inheritance' => [
                'route' => 'tools.inheritance-calculator', 'icon' => 'fa-scale-unbalanced-flip',
                'name' => 'Inheritance Calculator',
                'seo' => 'Islamic Inheritance Calculator for Land and Cash in Pakistan',
                'desc' => 'Work out Islamic inheritance shares of land and cash for spouse, parents, children and full siblings.',
                'guide' => [
                    'Debts and funeral costs come first, then a will of up to one third, and the rest goes to the heirs.',
                    'Shares follow the Hanafi method and cover spouse, parents, children and full brothers and sisters only.',
                    'Grandchildren, half siblings and other relatives can change the result. Ask a scholar or lawyer, and in Pakistan a predeceased child\'s children may also have a right under family law.',
                ],
            ],
            'rent' => [
                'route' => 'tools.rent-generator', 'icon' => 'fa-file-contract',
                'name' => 'Rent Agreement Maker',
                'seo' => 'Rent Agreement Maker for Punjab, Pakistan',
                'desc' => 'Prepare an Urdu rent agreement for a house, shop or portion, ready to print on e-stamp paper.',
                'guide' => [
                    'Fill in landlord, tenant, rent, deposit and term details. Your entries are not saved on our server.',
                    'Under the Punjab Rented Premises Act 2009, a tenancy in an urban area should be registered with the Rent Registrar.',
                    'Have a lawyer review the draft, and complete tenant registration with the local police.',
                ],
            ],
            'zakat' => [
                'route' => 'tools.zakat-calculator', 'icon' => 'fa-hand-holding-heart',
                'name' => 'Zakat Calculator',
                'seo' => 'Zakat Calculator for Cash, Gold and Property in Pakistan',
                'desc' => 'Calculate zakat on cash, gold, silver, business stock and plots bought for resale.',
                'guide' => [
                    'Enter today\'s gold and silver rate first, because the nisab and the result depend on them.',
                    'Your own home and a plot kept for personal use are not counted. A plot bought to resell is counted at today\'s value.',
                    'Ask a scholar about your own case, such as long-term loans or joint business.',
                ],
            ],
            'government' => [
                'route' => 'tools.government-links', 'icon' => 'fa-landmark',
                'name' => 'Government Links Guide',
                'seo' => 'Punjab Land Record, e-Stamp and DC Valuation Links Guide',
                'desc' => 'Official portals for fard, e-Stamp, DC valuation, registry and filer status, with the steps for each task.',
                'guide' => [
                    'Check that the address in your browser ends in .gov.pk or .gop.pk before entering any details.',
                    'Use the official guide next to each service for the full requirements and current process.',
                    'Pay fees only through the official challan or at the office. Keep the receipt or the challan number.',
                ],
            ],
            'cv' => [
                'route' => 'tools.cv-maker', 'icon' => 'fa-id-card',
                'name' => 'CV Maker',
                'seo' => 'Free CV Maker with A4 Print and PDF',
                'desc' => 'Build a clean one-page CV with a live preview and print it or save it as a PDF.',
                'guide' => [
                    'Write real numbers in your experience where you can, such as sales made or staff managed.',
                    'Keep it short and put the most recent job first.',
                    'To save a PDF, choose Print and then Save as PDF in the browser.',
                ],
            ],
        ];
    }

    /** Layout ke liye title aur description, mojooda zaban mein. */
    public static function meta(string $key): array
    {
        $tool = self::all()[$key];

        return [
            'title' => __($tool['seo']) . ' | Assan Zameen',
            'description' => __($tool['desc']),
        ];
    }

    public static function keyForRoute(?string $route): ?string
    {
        foreach (self::all() as $key => $tool) {
            if ($tool['route'] === $route) {
                return $key;
            }
        }

        return null;
    }
}
