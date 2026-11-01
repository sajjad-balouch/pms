-- Assan Zameen: set a new cover image on each of the 22 articles.
-- First upload the images to public/blog_images on the server (blog_images.zip).
-- Safe to run more than once. Changes only the image path; old image files are not deleted.

SET NAMES utf8mb4;
START TRANSACTION;

UPDATE `posts` SET `featured_image` = 'blog_images/punjab-plot-registry-tax-fee-schedule-guide.jpg' WHERE `slug` = 'punjab-plot-registry-tax-fee-schedule-guide';
UPDATE `posts` SET `featured_image` = 'blog_images/online-fard-malkiyat-punjab-arazi-record-center.jpg' WHERE `slug` = 'online-fard-malkiyat-punjab-arazi-record-center';
UPDATE `posts` SET `featured_image` = 'blog_images/land-measurement-units-marla-kanal-sarsahi-karam.jpg' WHERE `slug` = 'land-measurement-units-marla-kanal-sarsahi-karam';
UPDATE `posts` SET `featured_image` = 'blog_images/punjab-e-abiana-online-tax-payment-easypaisa-guide.jpg' WHERE `slug` = 'punjab-e-abiana-online-tax-payment-easypaisa-guide';
UPDATE `posts` SET `featured_image` = 'blog_images/overseas-pakistanis-online-land-record-poa-verification.jpg' WHERE `slug` = 'overseas-pakistanis-online-land-record-poa-verification';
UPDATE `posts` SET `featured_image` = 'blog_images/property-bayana-token-money-cancellation-legal-rights-pakistan.jpg' WHERE `slug` = 'property-bayana-token-money-cancellation-legal-rights-pakistan';
UPDATE `posts` SET `featured_image` = 'blog_images/punjab-green-property-certificate-plra-complete-guide.jpg' WHERE `slug` = 'punjab-green-property-certificate-plra-complete-guide';
UPDATE `posts` SET `featured_image` = 'blog_images/punjab-property-transfer-system-pts-housing-societies-rules.jpg' WHERE `slug` = 'punjab-property-transfer-system-pts-housing-societies-rules';
UPDATE `posts` SET `featured_image` = 'blog_images/punjab-e-registration-biometric-registry-dehi-markaz-maal-guide.jpg' WHERE `slug` = 'punjab-e-registration-biometric-registry-dehi-markaz-maal-guide';
UPDATE `posts` SET `featured_image` = 'blog_images/non-filer-property-purchase-ban-fbr-section-7e-rules.jpg' WHERE `slug` = 'non-filer-property-purchase-ban-fbr-section-7e-rules';
UPDATE `posts` SET `featured_image` = 'blog_images/dc-rate-vs-fbr-rate-market-value-difference-punjab.jpg' WHERE `slug` = 'dc-rate-vs-fbr-rate-market-value-difference-punjab';
UPDATE `posts` SET `featured_image` = 'blog_images/succession-certificate-inheritance-intiqal-guide-punjab.jpg' WHERE `slug` = 'succession-certificate-inheritance-intiqal-guide-punjab';
UPDATE `posts` SET `featured_image` = 'blog_images/fard-kaise-hasil-karein-punjab-online-arc-guide.jpg' WHERE `slug` = 'fard-kaise-hasil-karein-punjab-online-arc-guide';
UPDATE `posts` SET `featured_image` = 'blog_images/e-stamp-challan-32a-kaise-banayein-punjab-guide.jpg' WHERE `slug` = 'e-stamp-challan-32a-kaise-banayein-punjab-guide';
UPDATE `posts` SET `featured_image` = 'blog_images/dc-valuation-kaise-check-karein-punjab.jpg' WHERE `slug` = 'dc-valuation-kaise-check-karein-punjab';
UPDATE `posts` SET `featured_image` = 'blog_images/nayi-registry-ka-tareeqa-e-registration-punjab-steps.jpg' WHERE `slug` = 'nayi-registry-ka-tareeqa-e-registration-punjab-steps';
UPDATE `posts` SET `featured_image` = 'blog_images/bai-zubani-intiqal-ka-tareeqa-fee-punjab.jpg' WHERE `slug` = 'bai-zubani-intiqal-ka-tareeqa-fee-punjab';
UPDATE `posts` SET `featured_image` = 'blog_images/wirasati-intiqal-ka-tareeqa-punjab-kaghzat.jpg' WHERE `slug` = 'wirasati-intiqal-ka-tareeqa-punjab-kaghzat';
UPDATE `posts` SET `featured_image` = 'blog_images/land-record-durusti-fard-badar-naam-theek-punjab.jpg' WHERE `slug` = 'land-record-durusti-fard-badar-naam-theek-punjab';
UPDATE `posts` SET `featured_image` = 'blog_images/zameen-ki-taqseem-online-darkhwast-punjab.jpg' WHERE `slug` = 'zameen-ki-taqseem-online-darkhwast-punjab';
UPDATE `posts` SET `featured_image` = 'blog_images/arazi-moawin-kaise-banein-plra-franchise-punjab.jpg' WHERE `slug` = 'arazi-moawin-kaise-banein-plra-franchise-punjab';
UPDATE `posts` SET `featured_image` = 'blog_images/plot-khareedne-se-pehle-sarkari-checks-punjab.jpg' WHERE `slug` = 'plot-khareedne-se-pehle-sarkari-checks-punjab';

COMMIT;

-- To go back to the previous images, run the lines below (remove the leading -- first):
-- UPDATE `posts` SET `featured_image` = 'blog_images/1792515638_6ad79e3638263.jpg' WHERE `slug` = 'punjab-plot-registry-tax-fee-schedule-guide';
-- UPDATE `posts` SET `featured_image` = 'blog_images/1792517774_6ad7a68e00c00.jpg' WHERE `slug` = 'online-fard-malkiyat-punjab-arazi-record-center';
-- UPDATE `posts` SET `featured_image` = 'blog_images/1792517898_6ad7a70aa5566.jpg' WHERE `slug` = 'land-measurement-units-marla-kanal-sarsahi-karam';
-- UPDATE `posts` SET `featured_image` = 'blog_images/1792579602_6ad89812037c3.jpg' WHERE `slug` = 'punjab-e-abiana-online-tax-payment-easypaisa-guide';
-- UPDATE `posts` SET `featured_image` = 'blog_images/1792595103_6ad8d49f7e8e4.jpg' WHERE `slug` = 'overseas-pakistanis-online-land-record-poa-verification';
-- UPDATE `posts` SET `featured_image` = 'blog_images/1792595372_6ad8d5ac1fc47.jpg' WHERE `slug` = 'property-bayana-token-money-cancellation-legal-rights-pakistan';
-- UPDATE `posts` SET `featured_image` = 'blog_images/1792597550_6ad8de2e86018.jpg' WHERE `slug` = 'punjab-green-property-certificate-plra-complete-guide';
-- UPDATE `posts` SET `featured_image` = 'blog_images/1792597706_6ad8deca0cbd3.jpg' WHERE `slug` = 'punjab-property-transfer-system-pts-housing-societies-rules';
-- UPDATE `posts` SET `featured_image` = 'blog_images/1792823881_6adc5249ba4b5.jpg' WHERE `slug` = 'punjab-e-registration-biometric-registry-dehi-markaz-maal-guide';
-- UPDATE `posts` SET `featured_image` = 'blog_images/1792824347_6adc541be03f6.jpg' WHERE `slug` = 'non-filer-property-purchase-ban-fbr-section-7e-rules';
-- UPDATE `posts` SET `featured_image` = 'blog_images/1792824637_6adc553d7b84c.jpg' WHERE `slug` = 'dc-rate-vs-fbr-rate-market-value-difference-punjab';
-- UPDATE `posts` SET `featured_image` = 'blog_images/1792825093_6adc57052ba1f.jpg' WHERE `slug` = 'succession-certificate-inheritance-intiqal-guide-punjab';
