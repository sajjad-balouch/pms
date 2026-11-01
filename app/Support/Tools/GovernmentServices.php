<?php

namespace App\Support\Tools;

/**
 * Sarkari portals ki fehrist. Steps sirf wahi jo sarkari page par parhe gaye;
 * jahan parh na sake wahan apne se steps nahi likhe, sirf sarkari guide ka link hai.
 */
class GovernmentServices
{
    public const CHECKED_ON = '2026-10-31';

    private const PLRA = 'https://www.punjab-zameen.gov.pk';

    public static function groups(): array
    {
        return [
            'Land records' => [
                [
                    'icon' => 'fa-file-lines',
                    'title' => 'Get a fard',
                    'desc' => 'A fard is the official copy of the ownership record. You can get it online or at an e-Khidmat Markaz.',
                    'steps' => [
                        'At an e-Khidmat Markaz, bring your valid CNIC and property documents. The property owner must come in person.',
                        'The fee depends on the khewat, khatooni and total area. It is calculated by the system and paid in cash at the Bank of Punjab counter.',
                        'The e-Khidmat service says a fard for mutation is delivered within 30 minutes. If the record is not computerized, contact the area patwari.',
                    ],
                    'url' => 'https://onlinefard.punjab-zameen.gov.pk/',
                    'link' => 'Open online fard',
                    'guide' => self::PLRA . '/fardInfo',
                ],
                [
                    'icon' => 'fa-location-dot',
                    'title' => 'Find an Arazi Record Centre',
                    'desc' => 'Arazi Record Centres are one-window offices for fard, land transfer, ownership checks and registration.',
                    'url' => self::PLRA . '/serviceCenters',
                    'link' => 'Find a centre',
                ],
                [
                    'icon' => 'fa-file-pen',
                    'title' => 'Mutation (intiqal) and record correction',
                    'desc' => 'PLRA has separate official guides for getting a copy of a mutation, attesting a mutation and correcting a record.',
                    'url' => self::PLRA . '/mutationsInfo',
                    'link' => 'Copy of a mutation',
                    'guide' => self::PLRA . '/correctionRecordsInfo',
                ],
                [
                    'icon' => 'fa-object-ungroup',
                    'title' => 'Apply for partition (taqseem)',
                    'desc' => 'Apply for the division of jointly owned land through the official PLRA service.',
                    'url' => 'https://askfortaqseem.pulse.gop.pk/',
                    'link' => 'Apply for partition',
                    'guide' => self::PLRA . '/partitionInfo',
                ],
            ],
            'Registry and stamp duty' => [
                [
                    'icon' => 'fa-stamp',
                    'title' => 'Get an e-Stamp',
                    'desc' => 'E-Stamps replace paper stamp papers. PLRA says no account is needed to generate a challan.',
                    'steps' => [
                        'Open the e-Stamp portal or go to a Bank of Punjab e-Stamp counter. Enter your CNIC, transaction type, property details and value.',
                        'The system calculates the stamp duty, CVT and other charges and creates Challan Form 32-A with a reference number.',
                        'Pay the challan at a Bank of Punjab branch or through the online payment options.',
                        'You receive an e-Stamp with a unique number and QR code. Check it by scanning the QR code or on the PLRA verification page.',
                    ],
                    'url' => 'https://es.punjab-zameen.gov.pk/eStampCitizenPortal/ChallanFormView/HomePage',
                    'link' => 'Open e-Stamp portal',
                    'guide' => self::PLRA . '/estampInfo',
                ],
                [
                    'icon' => 'fa-scale-balanced',
                    'title' => 'Check DC valuation',
                    'desc' => 'DC valuation is the officially notified property value. It is the basis for stamp duty, registration fees and other government charges. Enter the location, land type, area and category to see it.',
                    'url' => 'https://es.punjab-zameen.gov.pk/eStampCitizenPortal/ChallanFormView/RateOfChallanView?name=PropertyDCValuation',
                    'link' => 'Calculate DC valuation',
                    'guide' => self::PLRA . '/dcValuationInfo',
                ],
                [
                    'icon' => 'fa-file-signature',
                    'title' => 'Apply for a new registry',
                    'desc' => 'Start a property registry online through the PLRA e-Registration portal.',
                    'url' => 'https://eregistration.punjab-zameen.gov.pk/',
                    'link' => 'Open e-Registration',
                    'guide' => self::PLRA . '/registryInfo',
                ],
                [
                    'icon' => 'fa-download',
                    'title' => 'Download an old e-Registry',
                    'desc' => 'Find a copy of a deed that was registered earlier.',
                    'url' => 'https://rod.pulse.gop.pk/',
                    'link' => 'Open registered deeds',
                    'guide' => self::PLRA . '/oldRegistryInfo',
                ],
            ],
            'Societies and taxes' => [
                [
                    'icon' => 'fa-building-circle-check',
                    'title' => 'Check a housing society',
                    'desc' => 'See the official PLRA list before buying a plot or a file in a society.',
                    'url' => self::PLRA . '/approvedSocieties',
                    'link' => 'Check society status',
                ],
                [
                    'icon' => 'fa-user-check',
                    'title' => 'Check filer status (ATL)',
                    'desc' => 'Property tax depends on filer status, so check the FBR Active Taxpayers List before a sale or purchase.',
                    'url' => 'https://e.fbr.gov.pk/atl/',
                    'link' => 'Open FBR ATL',
                ],
                [
                    'icon' => 'fa-file-invoice-dollar',
                    'title' => 'File your tax return',
                    'desc' => 'File your income tax return with FBR through the IRIS portal.',
                    'url' => 'https://iris.fbr.gov.pk/',
                    'link' => 'Open FBR IRIS',
                ],
            ],
            'Help and complaints' => [
                [
                    'icon' => 'fa-headset',
                    'title' => 'PLRA help and complaints',
                    'desc' => 'Complaints: (042) 111-22-22-77, Monday to Saturday, 8:00 AM to 5:00 PM. Office: (042) 99330111.',
                    'url' => self::PLRA . '/complaints',
                    'link' => 'File a complaint',
                ],
                [
                    'icon' => 'fa-gavel',
                    'title' => 'Fee notifications and laws',
                    'desc' => 'Read the latest PLRA service charges, notifications and laws directly from PLRA.',
                    'url' => self::PLRA . '/guidanceAndRegulation',
                    'link' => 'Open notifications',
                ],
            ],
        ];
    }
}
