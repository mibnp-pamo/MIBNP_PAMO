<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PublicSiteContent
{
    public static function home(): array
    {
        $mapPoints = self::mapPoints();
        $publicMapPointCount = count($mapPoints);

        return [
            'pageTitle' => 'Mounts Iglit-Baco Natural Park',
            'metaDescription' => 'Official tourism and park information site for Mounts Iglit-Baco Natural Park, with visitor guidance, biodiversity highlights, geography, and field imagery.',
            'backgroundSlides' => [
                ['id' => 'hero-scene', 'image' => self::optimizedAsset('bckgrndHome/hbg0.jpg')],
                ['id' => 'story-scene', 'image' => self::optimizedAsset('bckgrndHome/hbg2.jpg')],
                ['id' => 'map-scene', 'image' => self::optimizedAsset('bckgrndHome/hbg3.jpeg')],
                ['id' => 'wildlife-scene', 'image' => self::optimizedAsset('bckgrndHome/tamaraw.JPG')],
                ['id' => 'visit-scene', 'image' => self::optimizedAsset('bckgrndHome/hbg4.jpeg')],
                ['id' => 'impact-scene', 'image' => self::optimizedAsset('bckgrndHome/hbg1.jpg')],
            ],
            'galleryCards' => [
                [
                    'image' => self::optimizedAsset('bckgrndHome/hbg2.jpg'),
                    'title' => 'Highland Approach',
                    'body' => 'Sweeping grass ridges open the park to sunrise walks, wide views, and the feeling of arriving somewhere truly remote.',
                ],
                [
                    'image' => self::optimizedAsset('bckgrndHome/hbg3.jpeg'),
                    'title' => 'Forest Transition',
                    'body' => 'As elevation changes, open slopes give way to cooler ravines and forest cover that shelter birds, streams, and quieter trails.',
                ],
                [
                    'image' => self::optimizedAsset('bckgrndHome/hbg4.jpeg'),
                    'title' => 'Quietly Protected',
                    'body' => 'The landscape rewards patient travel with weather breaks, lookout pauses, and the slower rhythm that defines nature-based tourism.',
                ],
            ],
            'tamarawFeatures' => [
                [
                    'kicker' => 'Scientific Name',
                    'title' => 'Bubalus mindorensis',
                    'body' => 'Found only on Mindoro, the Tamaraw is one of the Philippines\' most important endemic mammals and the species most closely tied to the park.',
                ],
                [
                    'kicker' => 'Distinct Features',
                    'title' => 'Compact build and V-shaped horns',
                    'body' => 'Its dark coat, sturdy frame, and short V-shaped horns give the Tamaraw a profile that matches the rugged uplands it still depends on.',
                ],
                [
                    'kicker' => 'Habitat',
                    'title' => 'Grasslands, slopes, and forest edges',
                    'body' => 'The species relies on a mix of open forage areas and nearby cover, which is why Mounts Iglit-Baco remains so central to its survival.',
                ],
                [
                    'kicker' => 'Conservation Status',
                    'title' => 'Critically Endangered flagship species',
                    'body' => 'Every visit to the park is connected to a larger conservation story built around protecting one of the rarest wild buffalo species on Earth.',
                ],
            ],
            'visitSteps' => [
                [
                    'number' => '01',
                    'title' => 'Coordinate your visit early',
                    'body' => 'Reach out to the Protected Area Management Office several days ahead so permits, timing, and access guidance can be arranged properly.',
                ],
                [
                    'number' => '02',
                    'title' => 'Prepare identification and fees',
                    'body' => 'Bring a valid government-issued ID and be ready for environmental or entry fees based on the activity and visit type.',
                ],
                [
                    'number' => '03',
                    'title' => 'Register through field checkpoints',
                    'body' => 'Ranger stations help monitor access, orient visitors, and keep field movement safe and well documented throughout the protected area.',
                ],
                [
                    'number' => '04',
                    'title' => 'Pack for changing upland weather',
                    'body' => 'Conditions can shift quickly, so bring rain protection, trail-ready footwear, water, and basic first aid for a full day outdoors.',
                ],
                [
                    'number' => '05',
                    'title' => 'Travel with care for people and habitat',
                    'body' => 'Stay on approved routes, leave no trace, and respect the land, wildlife, and Mangyan communities connected to the park.',
                ],
            ],
            'impactStats' => [
                ['value' => '106655.62', 'suffix' => ' ha', 'label' => 'of protected landscape', 'decimals' => '2'],
                ['value' => '8', 'suffix' => '', 'label' => 'municipalities linked to the park', 'decimals' => '0'],
                ['value' => (string) $publicMapPointCount, 'suffix' => '', 'label' => 'public field points on the geography map', 'decimals' => '0'],
                ['value' => '2500', 'suffix' => ' masl', 'label' => 'highlighted upper elevation range', 'decimals' => '0'],
            ],
            'footerBgTarget' => 'impact-scene',
            'includeLeaflet' => false,
            'brandHref' => '#top',
        ];
    }

    public static function biodiversity(): array
    {
        $archiveSections = self::biodiversityArchiveSections();
        $birdArchiveSection = collect($archiveSections)->firstWhere('id', 'bird-records');
        $displayArchiveSections = collect($archiveSections)
            ->reject(fn (array $section): bool => ($section['id'] ?? null) === 'bird-records')
            ->values()
            ->all();
        $displayArchiveSections = array_map(function (array $section): array {
            $profiles = array_map(
                fn (array $profile): array => self::withPhotoCredit($profile),
                is_array($section['profiles'] ?? null) ? $section['profiles'] : [],
            );

            $section['profiles'] = $profiles;
            $section['count'] = count($profiles);

            return $section;
        }, $displayArchiveSections);
        $archiveBackgroundSlides = collect($displayArchiveSections)
            ->map(fn (array $section): array => [
                'id' => $section['bg_target'],
                'image' => $section['bg_image'],
            ])
            ->unique('id')
            ->values()
            ->all();
        $faunaProfiles = array_map(
            fn (array $profile): array => self::withPhotoCredit($profile),
            self::mergeBiodiversityProfiles(
                self::biodiversityFaunaProfiles(),
                is_array($birdArchiveSection['profiles'] ?? null) ? $birdArchiveSection['profiles'] : [],
            ),
        );
        $floraProfiles = array_map(
            fn (array $profile): array => self::withPhotoCredit($profile),
            self::biodiversityFloraProfiles(),
        );

        return [
            'pageTitle' => 'Biodiversity | Mounts Iglit-Baco Natural Park',
            'metaDescription' => 'Biodiversity page of Mounts Iglit-Baco Natural Park, featuring wildlife and plant profiles that help visitors understand the park\'s living landscape.',
            'bodyClass' => 'biodiversity-page',
            'backgroundSlides' => [
                ['id' => 'biodiversity-hero', 'image' => self::optimizedAsset('bckgrndHome/pamo.jpg')],
                ['id' => 'biodiversity-fauna', 'image' => self::optimizedAsset('bckgrndHome/hbg2.jpg')],
                ['id' => 'biodiversity-flora', 'image' => self::optimizedAsset('bckgrndHome/biodiv-reference-2.jpg')],
                ...$archiveBackgroundSlides,
            ],
            'faunaProfiles' => $faunaProfiles,
            'floraProfiles' => $floraProfiles,
            'archiveSections' => $displayArchiveSections,
            'footerBgTarget' => 'biodiversity-flora',
        ];
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private static function biodiversityFaunaProfiles(): array
    {
        return [
            [
                'image' => self::optimizedAsset('bckgrndHome/tamaraw.JPG'),
                'alt' => 'Tamaraw in Mounts Iglit-Baco Natural Park',
                'meta' => 'Mindoro Endemic Mammal',
                'philippines_red_list_status' => 'CR',
                'fauna_group' => 'mammals',
                'title' => 'Tamaraw',
                'scientific_name' => 'Bubalus mindorensis',
                'body' => 'is a small, sturdy wild buffalo endemic to the island of Mindoro in the Philippines and is one of the country\'s most iconic and endangered mammals. It is distinguished by its compact body, dark brown to grayish-black coat, short muscular legs, and V-shaped horns that project upward and slightly outward. Adult Tamaraws typically stand 100–105 cm at the shoulder and weigh 180–300 kg.',
            ],
            [
                'image' => null,
                'alt' => '',
                'meta' => 'Philippine Endemic Mammal',
                'philippines_red_list_status' => 'EN',
                'fauna_group' => 'mammals',
                'title' => 'Philippine Brown Deer',
                'scientific_name' => 'Rusa marianna',
                'body' => 'is a medium-sized deer endemic to the Philippines and one of the country\'s most widespread native large mammals. It is characterized by its robust body, short brown to reddish-brown coat, pale underparts, and relatively short tail. Adult males possess three-tined antlers that are shed and regrown annually, while females lack antlers. Mature individuals typically stand 60–70 cm at the shoulder and weigh 40–70 kg, although size may vary among populations and subspecies.',
            ],
            [
                'image' => null,
                'alt' => '',
                'meta' => 'Mindoro Endemic Mammal',
                'philippines_red_list_status' => 'EN',
                'fauna_group' => 'mammals',
                'title' => 'Mindoro Warty Pig',
                'scientific_name' => 'Sus oliveri',
                'body' => 'is a medium-sized wild pig endemic to the island of Mindoro, Philippines. It is one of the island\'s unique mammal species and is distinguished by its dark gray to black coarse hair, elongated snout, and prominent facial warts in adult males, which serve as protection during territorial fights. A mane of longer hair extends from the head to the back, becoming more pronounced during the breeding season. Adult individuals generally weigh 50–80 kg, with males being larger than females.',
            ],
            [
                'image' => null,
                'alt' => '',
                'meta' => 'Mindoro Endemic Mammal',
                'philippines_red_list_status' => 'VU',
                'fauna_group' => 'mammals',
                'title' => 'Mindoro Pallid Flying Fox',
                'scientific_name' => 'Desmalopex Microleucopterus',
                'body' => 'is a large fruit bat endemic to the island of Mindoro, Philippines. It is one of the country\'s least-known flying foxes and is recognized by its pale brown to grayish fur, broad wings, large eyes, and fox-like face adapted for nocturnal life. With a forearm length of approximately 140–155 mm and a wingspan approaching one meter, it is among the larger fruit bats found in the Philippines. Unlike insect-eating bats, this species relies primarily on keen eyesight and a well-developed sense of smell to locate food rather than echolocation.',
            ],
            [
                'image' => null,
                'alt' => '',
                'meta' => 'Mindoro Endemic Mammal',
                'philippines_red_list_status' => 'VU',
                'fauna_group' => 'mammals',
                'title' => 'Mindoro Stripe-faced Fruit Bat',
                'scientific_name' => 'Styloctenium mindorensis',
                'body' => 'is a medium-sized fruit bat endemic to the island of Mindoro, Philippines. It is one of the country\'s rarest bat species and is easily recognized by the distinctive pale stripes running along its face, which give the species its common name. It has soft brown to grayish-brown fur, large eyes adapted for night vision, broad wings for agile flight through forest canopies, and a fox-like muzzle typical of fruit bats. Unlike insectivorous bats, it relies primarily on its excellent eyesight and keen sense of smell to locate food rather than echolocation.',
            ],
            [
                'image' => null,
                'alt' => '',
                'meta' => 'Mindoro Endemic Reptile',
                'philippines_red_list_status' => 'OTS',
                'fauna_group' => 'reptile',
                'title' => 'Bangon Monitor Lizard',
                'scientific_name' => 'Varanus Bangonorum',
                'body' => '',
            ],
            [
                'image' => null,
                'alt' => '',
                'meta' => 'Mindoro Endemic Frog',
                'philippines_red_list_status' => 'OWS',
                'fauna_group' => 'amphibians',
                'title' => 'Mindoro Fanged Frog',
                'scientific_name' => 'Limnonectes beloncioi',
                'body' => '',
            ],
            [
                'image' => null,
                'alt' => '',
                'meta' => 'Mindoro-Endemic Frog',
                'philippines_red_list_status' => 'OTS',
                'fauna_group' => 'amphibians',
                'title' => 'Mindoro Litter Frog',
                'scientific_name' => 'Leptobrachium mangyanorum',
                'body' => '',
            ],
            [
                'image' => null,
                'alt' => '',
                'meta' => 'Mindoro Endemic Frog',
                'philippines_red_list_status' => 'OWS',
                'fauna_group' => 'amphibians',
                'title' => 'Mindoro Variable-backed Frog',
                'scientific_name' => 'Pulchrana mangyanum',
                'body' => '',
            ],
            [
                'image' => null,
                'alt' => '',
                'meta' => 'Mindoro Endemic Frog',
                'philippines_red_list_status' => 'EN',
                'fauna_group' => 'amphibians',
                'title' => 'Mindoro Tree Frog',
                'scientific_name' => 'Philautus schmackeri',
                'body' => '',
            ],
        ];
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private static function biodiversityFloraProfiles(): array
    {
        return [
            [
                'image' => self::optimizedAsset('biodiversity-assets/flora/OWNER J.F. Barcelona Mindoro pine.jpg'),
                'philippines_red_list_status' => 'VU',
                'alt' => 'Mindoro pine documented in Mounts Iglit-Baco Natural Park',
                'credit' => 'J.F. Barcelona from http://herbarium.bh.cornell.edu/',
                'meta' => 'Mindoro Native Flora',
                'title' => 'Mindoro Pine',
                'scientific_name' => 'Pinus merkusii',
                'body' => 'is a medium to large evergreen conifer native to Southeast Asia and one of the few pine species that naturally occur in the Philippines. In the country, it is primarily found in the highlands of Mindoro and parts of Luzon, where it grows on mountainous slopes and well-drained soils. It can reach heights of 25–45 meters, with a straight trunk, thick reddish-brown fissured bark, and long needle-like leaves grouped in pairs.',
            ],
            [
                'image' => self::optimizedAsset('biodiversity-assets/flora/OWNER P.B. Pelser & J.F. Barcelona Narra.jpg'),
                'philippines_red_list_status' => 'VU',
                'alt' => 'Narra documented in Mounts Iglit-Baco Natural Park',
                'credit' => 'P.B. Pelser & J.F. Barcelona from http://herbarium.bh.cornell.edu/',
                'meta' => 'Mindoro Native Flora',
                'title' => 'Narra',
                'scientific_name' => 'Pterocarpus indicus',
                'body' => 'is a large, deciduous to semi-evergreen hardwood tree belonging to the legume family (Fabaceae). It is the national tree of the Philippines and is highly valued for its durable, reddish-brown timber, attractive canopy, and ecological importance. Mature trees typically reach 30–40 meters in height with a broad, spreading crown that provides ample shade.',
            ],
            [
                'image' => self::optimizedAsset('biodiversity-assets/flora/OWNER Joharris Diamante Burke’s PitcherPlant.jpg'),
                'philippines_red_list_status' => 'EN',
                'alt' => '',
                'credit' => 'Joharris Diamante from http://herbarium.bh.cornell.edu/',
                'meta' => 'Mindoro Native Flora',
                'title' => 'Burke’s PitcherPlant',
                'scientific_name' => 'Nepenthes burkei',
                'body' => 'is a tropical carnivorous plant endemic to the Philippines, occurring naturally on the islands of Mindoro and Palawan. It is recognized for its distinctive pitcher-shaped modified leaves that trap and digest insects, allowing the plant to obtain nutrients from nutrient-poor soils. The pitchers are typically green to reddish-brown with a broad, rounded body, a flared peristome (pitcher rim), and a lid that helps prevent excess rainwater from diluting the digestive fluid.',
            ],
            [
                'image' => self::optimizedAsset('biodiversity-assets/flora/OWNER P. B. Pelser Almaciga.jpg'),
                'philippines_red_list_status' => 'VU',
                'alt' => '',
                'credit' => 'P. B. Pelser from http://herbarium.bh.cornell.edu/',
                'meta' => 'Mindoro Native Flora',
                'title' => 'Almaciga',
                'scientific_name' => 'Agathis philippinensis',
                'body' => 'is a large evergreen conifer native to the Philippines and one of the country\'s most economically and ecologically important forest trees. It is characterized by its tall, straight trunk, smooth grayish bark that flakes with age, and broad, leathery leaves that distinguish it from most conifers. Mature trees can grow up to 50–60 meters tall, forming part of the upper canopy of primary tropical forests.',
            ],
            [
                'image' => self::optimizedAsset('biodiversity-assets/flora/OWNER Greg Rule Guisok-guisok.jpg'),
                'philippines_red_list_status' => 'CR',
                'alt' => '',
                'credit' => 'Greg Rule from http://herbarium.bh.cornell.edu/',
                'meta' => 'Philippine Endemic Flora',
                'title' => 'Guisok-guisok',
                'scientific_name' => 'Hopea philippinensis',
                'body' => 'is a medium to large evergreen hardwood tree belonging to the dipterocarp family (Dipterocarpaceae). It is endemic to the Philippines, where it naturally occurs in lowland and lower montane primary forests. The species typically grows 20–35 meters tall with a straight cylindrical trunk, grayish to dark brown bark, and a dense, rounded crown.',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function biodiversityArchiveSections(): array
    {
        $sections = [
            self::buildArchiveSection(
                id: 'bird-records',
                tag: 'Bird Archive',
                title: 'Bird species photographed in and around the park',
                intro: 'These bird images come from the park\'s documentation archive and show part of the avian diversity recorded across the Mounts Iglit-Baco landscape.',
                profiles: self::birdArchiveProfiles(),
                bgTarget: 'biodiversity-bird-records',
            ),
        ];

        return array_values(array_filter($sections));
    }

    /**
     * @param  array<int, array<string, string|null>>  $profiles
     * @return array<string, mixed>|null
     */
    private static function buildArchiveSection(
        string $id,
        string $tag,
        string $title,
        string $intro,
        array $profiles,
        string $bgTarget,
    ): ?array {
        if ($profiles === []) {
            return null;
        }

        $coverImage = $profiles[0]['image'] ?? self::optimizedAsset('bckgrndHome/hbg2.jpg');

        return [
            'id' => $id,
            'tag' => $tag,
            'title' => $title,
            'intro' => $intro,
            'bg_target' => $bgTarget,
            'bg_image' => $coverImage,
            'cover_image' => $coverImage,
            'count' => count($profiles),
            'profiles' => $profiles,
        ];
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private static function birdArchiveProfiles(): array
    {
        $basePath = public_path('biodiversity-assets/birds/Birds');

        if (! File::isDirectory($basePath)) {
            return [];
        }

        $imagesByTitle = collect(File::directories($basePath))
            ->sort()
            ->mapWithKeys(function (string $directory): array {
                $image = collect(File::files($directory))
                    ->filter(fn ($file) => self::isImageFile($file->getFilename()))
                    ->sortBy(fn ($file) => strtolower($file->getFilename()))
                    ->first();

                if ($image === null) {
                    return [];
                }

                return [
                    basename($directory) => self::assetFromPublicPath($image->getPathname()),
                ];
            });

        return collect(self::biodiversityBirdProfiles())
            ->map(function (array $profile) use ($imagesByTitle): ?array {
                $title = $profile['title'] ?? null;

                if (! is_string($title)) {
                    return null;
                }

                $image = $imagesByTitle->get($title);

                if (! is_string($image) || $image === '') {
                    return null;
                }

                $profile['image'] = $image;
                $profile['alt'] = $title.' documented in Mounts Iglit-Baco Natural Park';

                return $profile;
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private static function biodiversityBirdProfiles(): array
    {
        return [
            self::withEbirdAcknowledgement([
                'image' => null,
                'alt' => '',
                'meta' => 'Philippine-endemic bird',
                'philippines_red_list_status' => 'VU',
                'fauna_group' => 'birds',
                'title' => 'Philippine Hawk-Eagle',
                'scientific_name' => 'Nisaetus Philippensis',
                'body' => ' fairly large raptor of lowland and foothill forest in the northern Philippines. Dark brown above and pale brown below with a streaked head and chest and a finely barred lower belly. Note the long hind crest and the chin stripe. In flight, has barred wings and a fairly long tail, both with a dark terminal band. Juveniles are much paler. Remarkably similar to Philippine Honey-buzzard, but head doesn’t project as far forward from the wings. Voice is a fairly high, screeching “week wik!” or single “week!”',
                'credit' => 'Shailesh Pinto from eBird.org',
            ]),
            self::withEbirdAcknowledgement([
                'image' => null,
                'alt' => '',
                'meta' => 'Mindoro-endemic bird',
                'philippines_red_list_status' => 'EN',
                'fauna_group' => 'birds',
                'title' => 'Mindoro Hornbill',
                'scientific_name' => 'Penelopides mindorensis',
                'body' => 'A Mindoro endemic hornbill of forest canopy and edges, notable for its creamy white-and-black plumage and strong bill. Ongoing habitat loss has left it endangered.',
            ]),

            self::withEbirdAcknowledgement([
                'image' => null,
                'alt' => '',
                'meta' => 'Mindoro-endemic bird',
                'philippines_red_list_status' => 'EN',
                'fauna_group' => 'birds',
                'title' => 'Mindoro Racquet Tail',
                'scientific_name' => 'Prioniturus mindorensis',
                'body' => 'Endemic to Mindoro, it lives in tropical moist lowland forest and was once grouped with the blue-crowned racket-tail. Habitat loss and trapping for the cage-bird trade remain major pressures.',
            ]),

            self::withEbirdAcknowledgement([
                'image' => null,
                'alt' => '',
                'meta' => 'Mindoro-endemic bird',
                'philippines_red_list_status' => 'OWS',
                'fauna_group' => 'birds',
                'title' => 'Mindoro Bulbul',
                'scientific_name' => 'Hypsipetes mindorensis',
                'body' => 'A Mindoro endemic usually found in lowland and montane forest, moving through leafy middle and upper levels in search of fruit and insects. Its restricted range makes it one of the island\'s characteristic forest bulbuls.',
            ]),

            self::withEbirdAcknowledgement([
                'image' => null,
                'alt' => '',
                'meta' => 'Mindoro Endemic Bird',
                'philippines_red_list_status' => 'VU',
                'fauna_group' => 'birds',
                'title' => 'Mindoro Scops Owl',
                'scientific_name' => 'Otus mindorensis',
                'body' => 'A small owl endemic to Mindoro\'s higher forests, typically associated with tropical moist montane habitat above about 870 meters. It is one of the island\'s upland specialty birds and is threatened by habitat loss.',
                'credit' => 'Michael Kearns from eBird.org',
            ]),

            self::withEbirdAcknowledgement([
                'image' => null,
                'alt' => '',
                'meta' => 'Mindoro Endemic Bird',
                'philippines_red_list_status' => 'VU',
                'fauna_group' => 'birds',
                'title' => 'Scarlet-collared Flowerpecker',
                'scientific_name' => 'Dicaeum retrocinctum',
                'body' => 'A tiny Mindoro endemic of lowland forest canopy, forest edge, and scattered trees, usually below about 1,000 meters. Its restricted range and reliance on remaining lowland habitat make it one of the island\'s more vulnerable flowerpeckers.',
                'credit' => 'Ramon Quisumbing from eBird.org',
            ]),

            self::withEbirdAcknowledgement([
                'image' => null,
                'alt' => '',
                'meta' => 'Mindoro-endemic bird',
                'philippines_red_list_status' => 'VU',
                'fauna_group' => 'birds',
                'title' => 'Mindoro Boobook',
                'scientific_name' => 'Ninox mindorensis',
                'body' => 'A fairly small owl of lowland and foothill forest and woodland. Rather dark brown on the head and back and reddish-brown on the chest, with bright yellow eyes and thin white eyebrows forming a V. Mindoro Scops-Owl is similar in size and color, but Mindoro Boobook is finely barred all over. Much smaller than Chocolate Boobook, without white on the chest. Song is a fairly high-pitched, descending mournful whistle, “wiiiiuuuuu.”.',
                'credit' => 'Djop Tabaranza from eBird.org',
            ]),

            self::withEbirdAcknowledgement([
                'image' => null,
                'alt' => '',
                'meta' => 'Mindoro-endemic bird',
                'philippines_red_list_status' => 'CR',
                'fauna_group' => 'birds',
                'title' => 'Black-hooded Coucal',
                'scientific_name' => 'Centropus steerii',
                'body' => 'A large, long-tailed bird of lowland primary forest on Mindoro with a brown back and belly, dark wings with brown-edged feathers, a dark tail with a bluish iridescence, and a black hood with some light streaking on the back of the neck. Note the strong curved bill. Similar to Philippine Coucal, but has a brown rather than black belly and is restricted to primary forest rather than more open habitats. Song is a descending series of very deep hoots.',
                'credit' => 'Louis Bevier from eBird.org',
            ]),

        ];
    }

    private static function isImageFile(string $filename): bool
    {
        return in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true);
    }

    public static function optimizedAsset(string $relativePath): string
    {
        $normalizedPath = ltrim(str_replace('\\', '/', rawurldecode($relativePath)), '/');
        $optimizedPath = preg_replace(
            '/\.(?:jpe?g|png|webp)$/i',
            '.webp',
            'generated/optimized/'.$normalizedPath,
        );

        if (is_string($optimizedPath) && File::exists(public_path(
            str_replace('/', DIRECTORY_SEPARATOR, $optimizedPath),
        ))) {
            return self::publicAssetUrl($optimizedPath);
        }

        return self::publicAssetUrl($normalizedPath);
    }

    private static function assetFromPublicPath(string $absolutePath): string
    {
        $relativePath = Str::after($absolutePath, public_path());
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');

        return self::optimizedAsset($relativePath);
    }

    private static function publicAssetUrl(string $relativePath): string
    {
        $encodedPath = collect(explode('/', $relativePath))
            ->map(fn (string $segment) => rawurlencode($segment))
            ->implode('/');

        return asset($encodedPath);
    }

    private static function thumbnailAssetFromPublicPath(string $absolutePath): ?string
    {
        $relativePath = Str::after($absolutePath, public_path());
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');

        if ($relativePath === '') {
            return null;
        }

        $thumbnailAbsolutePath = public_path(
            str_replace('/', DIRECTORY_SEPARATOR, 'generated/gallery-thumbs/'.$relativePath),
        );

        if (! File::exists($thumbnailAbsolutePath)) {
            return null;
        }

        return self::assetFromPublicPath($thumbnailAbsolutePath);
    }

    private static function thumbnailAssetForImage(string $image): ?string
    {
        $imagePath = parse_url($image, PHP_URL_PATH);

        if (! is_string($imagePath) || $imagePath === '') {
            return null;
        }

        $normalizedPath = ltrim(str_replace('\\', '/', rawurldecode($imagePath)), '/');

        if (str_contains($normalizedPath, 'public/')) {
            $normalizedPath = Str::after($normalizedPath, 'public/');
        }

        if ($normalizedPath === '') {
            return null;
        }

        $absolutePath = public_path(str_replace('/', DIRECTORY_SEPARATOR, $normalizedPath));

        if (! File::exists($absolutePath)) {
            return null;
        }

        return self::thumbnailAssetFromPublicPath($absolutePath);
    }

    public static function partners(): array
    {
        return [
            'pageTitle' => 'Partners | Mounts Iglit-Baco Natural Park',
            'metaDescription' => 'Partner directory for Mounts Iglit-Baco Natural Park, highlighting the agencies and institutions that support conservation, management, and visitor access.',
            'backgroundSlides' => [
                ['id' => 'partner-scene', 'image' => self::optimizedAsset('bckgrndHome/hbg4.jpeg')],
                ['id' => 'support-scene', 'image' => self::optimizedAsset('bckgrndHome/hbg1.jpg')],
            ],
            'partners' => [
                [
                    'name' => 'ASEAN Heritage Parks',
                    'role' => 'Regional protected-area network',
                    'description' => 'The ASEAN Heritage Parks programme helps recognize and promote ecologically important protected areas across Southeast Asia.',
                    'website' => 'aseanbiodiversity.org',
                    'url' => 'https://www.aseanbiodiversity.org/the-asean-heritage-parks-programme/',
                    'external' => true,
                    'logo' => self::optimizedAsset('bglogo/ahp.png'),
                    'alt' => 'ASEAN Heritage Parks logo',
                ],
                [
                    'name' => 'Department of Environment and Natural Resources',
                    'role' => 'National oversight and policy',
                    'description' => 'DENR provides national environmental policy direction and institutional support for protected area governance in the Philippines.',
                    'website' => 'denr.gov.ph',
                    'url' => 'https://denr.gov.ph/',
                    'external' => true,
                    'logo' => self::optimizedAsset('bglogo/denr.png'),
                    'alt' => 'Department of Environment and Natural Resources logo',
                ],
                [
                    'name' => 'Biodiversity Management Bureau',
                    'role' => 'Biodiversity and PA management',
                    'description' => 'BMB leads biodiversity conservation work, protected area systems, and technical support tied to species and habitat protection.',
                    'website' => 'bmb.gov.ph',
                    'url' => 'https://bmb.gov.ph/mission-and-vision/',
                    'external' => true,
                    'logo' => self::optimizedAsset('bglogo/bmb.png'),
                    'alt' => 'Biodiversity Management Bureau logo',
                ],
                [
                    'name' => 'Daboville Foundation',
                    'role' => 'Conservation support partner',
                    'description' => 'Supports environmental and community-linked initiatives that complement long-term conservation work.',
                    'website' => 'dabovillefoundation.org',
                    'url' => 'https://www.dabovillefoundation.org/',
                    'external' => true,
                    'logo' => self::optimizedAsset('bglogo/daboville.png'),
                    'alt' => 'Daboville Foundation logo',
                ],
                [
                    'name' => 'Biodiversity Corridors',
                    'role' => 'Landscape connectivity initiative',
                    'description' => 'Highlights corridor sites and broader landscape-scale conservation efforts connected to biodiversity protection.',
                    'website' => 'bdcorridors.wordpress.com',
                    'url' => 'https://bdcorridors.wordpress.com/corridor-sites/',
                    'external' => true,
                    'logo' => self::optimizedAsset('bglogo/bdcorridor.png'),
                    'alt' => 'Biodiversity Corridors logo',
                ],
                [
                    'name' => 'Mindoro Biodiversity Conservation Foundation Inc. (MBCFI)',
                    'role' => 'Mindoro conservation partner',
                    'description' => 'A Mindoro-focused conservation organization working on biodiversity, habitat, and environmental protection initiatives.',
                    'website' => 'mbcfi.org.ph',
                    'url' => 'https://www.mbcfi.org.ph/',
                    'external' => true,
                    'logo' => self::optimizedAsset('bglogo/MBCFI.png'),
                    'alt' => 'Mindoro Biodiversity Conservation Foundation Inc. logo',
                ],
                [
                    'name' => 'Tamaraw DENR',
                    'role' => 'Tamaraw conservation updates',
                    'description' => 'Public updates and outreach related to Tamaraw protection, field work, and related conservation activities.',
                    'website' => 'facebook.com/tamarawdenr',
                    'url' => 'https://www.facebook.com/tamarawdenr/',
                    'external' => true,
                    'logo' => self::optimizedAsset('bglogo/tcp.png'),
                    'alt' => 'Tamaraw DENR logo',
                ],
                [
                    'name' => 'Provincial Government of Occidental Mindoro',
                    'role' => 'Local government partner',
                    'description' => 'Occidental Mindoro supports the wider governance landscape around access, communities, visitor services, and long-term environmental planning.',
                    'website' => 'website.occidentalmindoro.gov.ph',
                    'url' => 'https://website.occidentalmindoro.gov.ph/',
                    'external' => true,
                    'logo' => self::optimizedAsset('bglogo/lgu.png'),
                    'alt' => 'Local government unit logo',
                ],
                [
                    'name' => 'Municipality of Calintaan',
                    'role' => 'Local government partner',
                    'description' => 'Calintaan is one of the municipalities linked to the protected landscape and part of the local coordination network around conservation, access, and community stewardship.',
                    'website' => 'facebook.com/abantebagongcalintaan',
                    'url' => 'https://www.facebook.com/abantebagongcalintaan/',
                    'external' => true,
                    'logo' => self::optimizedAsset('bglogo/Calintaan.png'),
                    'alt' => 'Municipality of Calintaan logo',
                ],
                [
                    'name' => 'Municipality of Rizal, Occidental Mindoro',
                    'role' => 'Local government partner',
                    'description' => 'Rizal connects local governance, nearby communities, and field coordination that support the wider management setting around Mounts Iglit-Baco.',
                    'website' => 'facebook.com/lgurizaloccidentalmindoro',
                    'url' => 'https://www.facebook.com/lgurizaloccidentalmindoro/',
                    'external' => true,
                    'logo' => self::optimizedAsset('bglogo/Rizal.png'),
                    'alt' => 'Municipality of Rizal, Occidental Mindoro logo',
                ],
                [
                    'name' => 'Municipality of Sablayan',
                    'role' => 'Local government partner',
                    'description' => 'Sablayan is a major municipality connected to the park landscape and helps frame local access, visitor movement, and community-facing support around protected area work.',
                    'website' => 'sablayan.gov.ph',
                    'url' => 'https://sablayan.gov.ph/',
                    'external' => true,
                    'logo' => self::optimizedAsset('bglogo/Sablayan.png'),
                    'alt' => 'Municipality of Sablayan logo',
                ],
                [
                    'name' => 'Municipality of San Jose, Occidental Mindoro',
                    'role' => 'Local government partner',
                    'description' => 'San Jose remains an important service and access hub for the province, helping connect travel, logistics, and public information relevant to park visitors and partners.',
                    'website' => 'sanjoseoccimindoro.gov.ph',
                    'url' => 'https://sanjoseoccimindoro.gov.ph/',
                    'external' => true,
                    'logo' => self::optimizedAsset('bglogo/SanJose.png'),
                    'alt' => 'Municipality of San Jose, Occidental Mindoro logo',
                ],
                [
                    'name' => 'Provincial Government of Oriental Mindoro',
                    'role' => 'Local government partner',
                    'description' => 'Oriental Mindoro is part of the broader institutional network connected to communities, access corridors, and conservation coordination around the park.',
                    'website' => 'old.ormindoro.gov.ph',
                    'url' => 'https://old.ormindoro.gov.ph/',
                    'external' => true,
                    'logo' => self::optimizedAsset('bglogo/lguO.png'),
                    'alt' => 'Oriental Mindoro local government unit logo',
                ],
            ],
            'newsItems' => [],

            'footerBgTarget' => 'support-scene',
        ];
    }

    public static function officeProfile(): array
    {
        $officePoint = collect(self::mapPoints())->firstWhere('id', 'pamo') ?? [
            'location' => 'Occidental Mindoro',
            'lat' => '12.642627',
            'lng' => '121.021378',
            'osm_url' => route('geography', [], false),
            'image' => self::optimizedAsset('bckgrndHome/pamo.jpg'),
            'image_alt' => 'Protected Area Management Office building in Occidental Mindoro',
        ];
        $officeLat = (float) ($officePoint['lat'] ?? 12.642627);
        $officeLng = (float) ($officePoint['lng'] ?? 121.021378);
        $officeLatText = number_format($officeLat, 6, '.', '');
        $officeLngText = number_format($officeLng, 6, '.', '');
        $officeMapBbox = implode(',', [
            number_format($officeLng - 0.004, 6, '.', ''),
            number_format($officeLat - 0.004, 6, '.', ''),
            number_format($officeLng + 0.004, 6, '.', ''),
            number_format($officeLat + 0.004, 6, '.', ''),
        ]);

        return [
            'pageTitle' => 'Office Profile | Mounts Iglit-Baco Natural Park',
            'metaDescription' => 'Profile page for the Protected Area Management Office of Mounts Iglit-Baco Natural Park, with visitor coordination guidance, contact channels, and trip-planning notes.',
            'backgroundSlides' => [
                ['id' => 'office-hero', 'image' => self::optimizedAsset('bckgrndHome/pamo.jpg')],
                ['id' => 'office-story', 'image' => self::optimizedAsset('bckgrndHome/hbg3.jpeg')],
                ['id' => 'office-contact', 'image' => self::optimizedAsset('bckgrndHome/hbg4.jpeg')],
            ],
            'officeSummaryParagraphs' => [
                'the lead office responsible for supporting the effective management, protection, and conservation of the 106,655.62-hectare protected area located across Occidental and Oriental Mindoro. It works under the Department of Environment and Natural Resources and supports the implementation of the park’s Protected Area Management Plan.',
                'PAMO plays a central role in coordinating conservation efforts, field operations, monitoring, law enforcement, community engagement, and partnership-building within the park. Its work focuses on protecting MIBNP’s important habitats, especially the core habitat of the critically endangered tamaraw, while also recognizing the cultural and ancestral domain interests of Indigenous communities such as the Tau-Buid and Buhid-Bangon.',
                'To strengthen park management, the MIBNP-PAMO is organized around key technical and operational functions, including Resource Management and Protection, Socio-Economic Management, and Policy, Planning and Knowledge Management. These units support biodiversity conservation, sustainable land and resource management, wildlife crime prevention, ecotourism, information and education activities, and coordination with local communities and partner institutions.',
                'Through its programs and partnerships, PAMO helps ensure that Mounts Iglit-Baco Natural Park remains a protected home for the tamaraw, Indigenous Peoples, endemic wildlife, natural ecosystems, and future generations.',
            ],
            'officePamoMedia' => [
                'title' => $officePoint['title'] ?? 'Protected Area Management Office',
                'location' => $officePoint['location'] ?? 'Occidental Mindoro',
                'lat' => $officeLatText,
                'lng' => $officeLngText,
                'image' => $officePoint['image'] ?? self::optimizedAsset('bckgrndHome/pamo.jpg'),
                'image_alt' => $officePoint['image_alt'] ?? 'Protected Area Management Office building in Occidental Mindoro',
                'map_url' => $officePoint['osm_url'],
                'map_embed_url' => 'https://www.openstreetmap.org/export/embed.html?'.http_build_query([
                    'bbox' => $officeMapBbox,
                    'layer' => 'mapnik',
                    'marker' => $officeLatText.','.$officeLngText,
                ], '', '&', PHP_QUERY_RFC3986),
            ],
            'officeAtGlance' => [
                [
                    'label' => 'Office name',
                    'value' => 'Protected Area Management Office (PAMO)',
                ],
                [
                    'label' => 'Location reference',
                    'value' => $officePoint['location'],
                ],
                [
                    'label' => 'Email',
                    'value' => 'r4b.mibnp@denr.gov.ph',
                    'href' => 'mailto:r4b.mibnp@denr.gov.ph',
                ],
                [
                    'label' => 'Facebook',
                    'value' => 'facebook.com/mibnppamo',
                    'href' => 'https://www.facebook.com/mibnppamo',
                    'external' => true,
                ],
                [
                    'label' => 'Map reference',
                    'value' => 'Open the PAMO field point in OpenStreetMap',
                    'href' => $officePoint['osm_url'],
                    'external' => true,
                ],
                [
                    'label' => 'Visitor note',
                    'value' => 'Current office hours, fees, and permit requirements should be confirmed directly with the office before travel.',
                ],
            ],
            'officeSupportItems' => [
                'Visitor registration and orientation guidance',
                'Permit and activity coordination before entry',
                'Route, access, and field-planning questions',
                'Responsible tourism and conservation reminders',
            ],
            'officeFunctions' => [
                [
                    'tag' => 'Visitor support',
                    'title' => 'Registration and orientation',
                    'body' => 'Visitors can use the office as a first point of contact for practical guidance on entry, preparation, and what to expect before moving into the park landscape.',
                ],
                [
                    'tag' => 'Trip planning',
                    'title' => 'Permits, access, and coordination',
                    'body' => 'Questions about permits, environmental fees, group visits, research activity, filming, camping, or guide needs should be clarified with the office ahead of time.',
                ],
                [
                    'tag' => 'Field context',
                    'title' => 'Routes and station references',
                    'body' => 'The office helps visitors understand how the park is organized on the ground, including reference points such as ranger stations, access timing, and where field coordination may be needed.',
                ],
                [
                    'tag' => 'Protection',
                    'title' => 'Responsible tourism reminders',
                    'body' => 'The office also connects visitor activity to conservation practice by sharing reminders on trail discipline, wildlife respect, waste handling, and site-specific restrictions.',
                ],
            ],
            'planningParagraphs' => [
                'The office traces its mandate to the legal history of Mounts Iglit-Baco. The protected landscape began as a conservation area in 1969, was declared a national park under Republic Act No. 6148 on November 9, 1970, and later became part of the National Integrated Protected Areas System on June 1, 1992.',
                'The Mounts Iglit-Baco Natural Park-Protected Area Management Office (MIBNP-PAMO) was later established in compliance with Section 11-B of Republic Act No. 11038, signed on June 22, 2018, which also recognized the area as a Natural Park.',
            ],
            'planningCards' => [
                [
                    'image' => self::optimizedAsset('bckgrndHome/pamo.jpg'),
                    'title' => '1969 conservation roots',
                    'body' => 'Before the office existed, the area was first protected as a game refuge and bird sanctuary, creating the early conservation basis for later park administration.',
                    'map_x' => 18,
                    'map_y' => 66,
                ],
                [
                    'image' => self::optimizedAsset('bckgrndHome/hbg2.jpg'),
                    'title' => '1970 park declaration',
                    'body' => 'Republic Act No. 6148 formally established Mounts Iglit and Mount Baco and adjoining areas as a national park on November 9, 1970.',
                    'map_x' => 52,
                    'map_y' => 36,
                ],
                [
                    'image' => self::optimizedAsset('bckgrndHome/hbg4.jpeg'),
                    'title' => '2018 PAMO creation',
                    'body' => 'The MIBNP-PAMO was established in compliance with Section 11-B of Republic Act No. 11038, signed on June 22, 2018, to handle day-to-day protected area management.',
                    'map_x' => 80,
                    'map_y' => 64,
                ],
            ],
            'brochureImages' => [
                [
                    'image' => self::optimizedAsset('brochure/01-english.jpg'),
                    'full_image' => asset('brochure/01-english.jpg'),
                    'alt' => 'English brochure page 1 for Mounts Iglit-Baco Natural Park office profile',
                ],
                [
                    'image' => self::optimizedAsset('brochure/02-english.jpg'),
                    'full_image' => asset('brochure/02-english.jpg'),
                    'alt' => 'English brochure page 2 for Mounts Iglit-Baco Natural Park office profile',
                ],
            ],
            'planningChecklist' => [
                'Confirm current office hours before traveling.',
                'Ask about fees, permits, guide requirements, and activity restrictions for your planned visit.',
                'Prepare a valid ID and enough time for registration or orientation steps.',
                'Coordinate early if your visit involves camping, research, filming, or a large organized group.',
            ],
            'footerBgTarget' => 'office-contact',
        ];
    }

    public static function geography(): array
    {
        $mapPoints = self::mapPoints();

        return [
            'pageTitle' => 'Geography | Mounts Iglit-Baco Natural Park',
            'metaDescription' => 'Geography page for Mounts Iglit-Baco Natural Park, showing the park map, ranger stations, and route previews that help visitors understand the landscape.',
            'bodyClass' => 'geography-page',
            'backgroundSlides' => [
                ['id' => 'geography-board', 'image' => self::optimizedAsset('bckgrndHome/hbg2.jpg')],
            ],
            'mapPoints' => $mapPoints,
            'mapRoutes' => self::mapRoutes(),
            'selectedPoint' => $mapPoints[0],
            'footerBgTarget' => '',
            'includeLeaflet' => true,
        ];
    }

    public static function gallery(): array
    {
        $galleryFrames = [
            self::withPhotoCredit([
                'image' => self::optimizedAsset('bckgrndHome/hbg2.jpg'),
                'title' => 'Grassland approach',
                'caption' => 'Open upland slopes and rolling ridgelines create the kind of first impression visitors remember long after the climb.',
                'meta' => 'Landscape',
            ]),
            self::withPhotoCredit([
                'image' => self::optimizedAsset('bckgrndHome/hbg4.jpeg'),
                'title' => 'Quiet valley light',
                'caption' => 'Soft weather and layered terrain reveal the slower, more reflective side of the park experience.',
                'meta' => 'Scenery',
            ]),
            self::withPhotoCredit([
                'image' => self::optimizedAsset('bckgrndHome/hbg1.jpg'),
                'title' => 'Weather over the ridge',
                'caption' => 'Fast-changing clouds are part of the drama of Mounts Iglit-Baco and shape how every walk through the uplands feels.',
                'meta' => 'Atmosphere',
            ]),
            self::withPhotoCredit([
                'image' => self::optimizedAsset('bckgrndHome/tamaraw.JPG'),
                'title' => 'Tamaraw range',
                'caption' => 'Wildlife imagery keeps the gallery rooted in the park\'s biggest story: protecting habitat for a species found nowhere else in the world.',
                'meta' => 'Wildlife',
            ]),
            self::withPhotoCredit([
                'image' => self::optimizedAsset('bckgrndHome/hbg3.jpeg'),
                'title' => 'Field route texture',
                'caption' => 'Trail-side views, vegetation shifts, and distant slopes help visitors understand the terrain before they arrive.',
                'meta' => 'Trail View',
            ]),
            self::withPhotoCredit([
                'image' => self::optimizedAsset('bckgrndHome/hbg0.jpg'),
                'title' => 'Ridge at day\'s end',
                'caption' => 'Wide panoramas like this capture the scale of the protected landscape and why it feels both wild and welcoming.',
                'meta' => 'Vista',
            ]),
        ];

        $archiveGalleryFrames = self::archiveGalleryFrames();
        $biodiversityGalleryFrames = self::biodiversityGalleryFrames();

        if ($archiveGalleryFrames !== []) {
            $galleryFrames = array_merge($galleryFrames, $archiveGalleryFrames);
        }

        if ($biodiversityGalleryFrames !== []) {
            $galleryFrames = array_merge($galleryFrames, $biodiversityGalleryFrames);
        }

        $galleryFrames = self::uniqueGalleryFramesByImage($galleryFrames);
        $galleryGroups = self::groupGalleryFrames($galleryFrames);

        return [
            'pageTitle' => 'Gallery | Mounts Iglit-Baco Natural Park',
            'metaDescription' => 'Official gallery of Mounts Iglit-Baco Natural Park, featuring landscapes, wildlife habitat, and field scenes from across the protected area.',
            'bodyClass' => 'gallery-page',
            'backgroundSlides' => [
                ['id' => 'gallery-hero', 'image' => self::optimizedAsset('bckgrndHome/hbg0.jpg')],
                ['id' => 'gallery-wildlife', 'image' => self::optimizedAsset('bckgrndHome/tamaraw.JPG')],
            ],
            'galleryFrames' => $galleryFrames,
            'galleryGroups' => $galleryGroups,
            'galleryPhotoCount' => count($galleryFrames),
            'footerBgTarget' => 'gallery-wildlife',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function mapPoints(): array
    {
        return [
            [
                'id' => 'pamo',
                'label' => 'PAMO',
                'map_label' => 'Protected Area Management Office',
                'title' => 'Protected Area Management Office',
                'type' => 'Field coordination point',
                'location' => 'Occidental Mindoro',
                'note' => 'Best starting point for permits, orientation, and coordination with park staff before heading deeper into the landscape.',
                'lat' => '12.642627',
                'lng' => '121.021378',
                'marker_color' => '#1f6b52',
                'label_direction' => 'bottom',
                'label_offset_x' => 0,
                'label_offset_y' => 16,
                'osm_url' => 'https://www.openstreetmap.org/?mlat=12.642627&mlon=121.021378#map=18/12.642627/121.021378',
                'image' => self::optimizedAsset('bckgrndHome/pamo.jpg'),
                'image_alt' => 'Protected Area Management Office building in Occidental Mindoro',
            ],
            [
                'id' => 'station-2',
                'label' => 'STN2',
                'map_label' => 'Ranger Station 2',
                'title' => 'Ranger Station 2',
                'type' => 'Ranger station',
                'location' => 'Rizal, Occidental Mindoro',
                'note' => 'Shown farther northeast of PAMO on the map, this station marks an upland field point along the park\'s eastern approach.',
                'lat' => '12.696477',
                'lng' => '121.041244',
                'marker_color' => '#c87d2f',
                'label_direction' => 'top',
                'label_offset_x' => 0,
                'label_offset_y' => -10,
                'osm_url' => 'https://www.openstreetmap.org/?mlat=12.696477&mlon=121.041244#map=18/12.696477/121.041244',
                'image' => self::optimizedAsset('bckgrndHome/hbg2.jpg'),
                'image_alt' => 'Forest and ridge reference for Ranger Station 2',
            ],
            [
                'id' => 'station-1',
                'label' => 'STN1',
                'map_label' => 'Ranger Station 1',
                'title' => 'Ranger Station 1',
                'type' => 'Ranger station',
                'location' => 'Occidental Mindoro',
                'note' => 'Placed just above and slightly east of PAMO on the map, this is the nearest ranger station within the main coordination area.',
                'lat' => '12.643747',
                'lng' => '121.022185',
                'marker_color' => '#8b6c3f',
                'label_direction' => 'top',
                'label_offset_x' => 0,
                'label_offset_y' => -16,
                'osm_url' => 'https://www.openstreetmap.org/?mlat=12.643747&mlon=121.022185#map=18/12.643747/121.022185',
                'image' => self::optimizedAsset('bckgrndHome/hbg3.jpeg'),
                'image_alt' => 'Field landscape reference for Ranger Station 1',
            ],
            [
                'id' => 'magawang',
                'label' => 'MAGA',
                'map_label' => 'Magawang Station',
                'title' => 'Magawang Station',
                'type' => 'Ranger station',
                'location' => 'Mounts Iglit foothills',
                'note' => 'Located near the upper-right side of the map, Magawang Station represents a foothill stop where routes begin opening toward the grasslands.',
                'lat' => '12.699805',
                'lng' => '121.068615',
                'marker_color' => '#a54d4d',
                'label_direction' => 'top',
                'label_offset_x' => 0,
                'label_offset_y' => -16,
                'osm_url' => 'https://www.openstreetmap.org/?mlat=12.699805&mlon=121.068615#map=18/12.699805/121.068615',
                'image' => self::optimizedAsset('stations/MAGAWANG STATION 3.jpg'),
                'image_alt' => 'Magawang ranger station in Mounts Iglit-Baco Natural Park',
            ],
            [
                'id' => 'station-4',
                'label' => 'STN4',
                'map_label' => 'Ranger Station 4',
                'title' => 'Ranger Station 4',
                'type' => 'Ranger station',
                'location' => 'San Jose approach',
                'note' => 'Positioned north of PAMO and west of Magawang on the map, this station marks another upland checkpoint along the interior field network.',
                'lat' => '12.712976',
                'lng' => '121.054025',
                'marker_color' => '#4d7498',
                'label_direction' => 'top',
                'label_offset_x' => 0,
                'label_offset_y' => -16,
                'osm_url' => 'https://www.openstreetmap.org/?mlat=12.712976&mlon=121.054025#map=18/12.712976/121.054025',
                'image' => self::optimizedAsset('stations/LOIBFU STATION 4.jpg'),
                'image_alt' => 'Loibfu ranger station in Mounts Iglit-Baco Natural Park',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function mapRoutes(): array
    {
        return [
            [
                'from' => 'pamo',
                'to' => 'station-1',
                'title' => 'PAMO to Ranger Station 1',
                'note' => 'Approximate route preview between the management office and Ranger Station 1, showing estimated distance and elevation only rather than the exact trekking path on the ground.',
                'profile' => [
                    ['lat' => 12.642627, 'lng' => 121.021378, 'elevation' => 94],
                    ['lat' => 12.642993, 'lng' => 121.021546, 'elevation' => 94],
                    ['lat' => 12.643311, 'lng' => 121.021781, 'elevation' => 95],
                    ['lat' => 12.643747, 'lng' => 121.022185, 'elevation' => 96],
                ],
            ],
            [
                'from' => 'pamo',
                'to' => 'station-2',
                'title' => 'PAMO to Ranger Station 2',
                'note' => 'Approximate route preview from the management office toward Ranger Station 2, showing estimated distance and elevation change rather than the exact trekking path visitors will take.',
                'profile' => [
                    ['lat' => 12.642627, 'lng' => 121.021378, 'elevation' => 118],
                    ['lat' => 12.6511, 'lng' => 121.0247, 'elevation' => 132],
                    ['lat' => 12.6614, 'lng' => 121.0287, 'elevation' => 156],
                    ['lat' => 12.6723, 'lng' => 121.0331, 'elevation' => 190],
                    ['lat' => 12.6848, 'lng' => 121.0376, 'elevation' => 234],
                    ['lat' => 12.696477, 'lng' => 121.041244, 'elevation' => 281],
                ],
            ],
            [
                'from' => 'pamo',
                'to' => 'magawang',
                'title' => 'PAMO to Magawang Station',
                'note' => 'Approximate route preview linking the park office to Magawang Station, showing estimated distance and elevation only, not the exact trekking path toward the grassland routes.',
                'profile' => [
                    ['lat' => 12.642627, 'lng' => 121.021378, 'elevation' => 118],
                    ['lat' => 12.6511, 'lng' => 121.0247, 'elevation' => 132],
                    ['lat' => 12.6623, 'lng' => 121.0292, 'elevation' => 158],
                    ['lat' => 12.6744, 'lng' => 121.0341, 'elevation' => 194],
                    ['lat' => 12.6858, 'lng' => 121.0391, 'elevation' => 236],
                    ['lat' => 12.6939, 'lng' => 121.0456, 'elevation' => 266],
                    ['lat' => 12.6978, 'lng' => 121.0537, 'elevation' => 289],
                    ['lat' => 12.6990, 'lng' => 121.0610, 'elevation' => 303],
                    ['lat' => 12.699805, 'lng' => 121.068615, 'elevation' => 316],
                ],
            ],
            [
                'from' => 'pamo',
                'to' => 'station-4',
                'title' => 'PAMO to Ranger Station 4',
                'note' => 'Approximate route preview toward Ranger Station 4, showing estimated distance and elevation gain rather than the exact trekking path across the uplands.',
                'profile' => [
                    ['lat' => 12.642627, 'lng' => 121.021378, 'elevation' => 118],
                    ['lat' => 12.6508, 'lng' => 121.0245, 'elevation' => 131],
                    ['lat' => 12.6610, 'lng' => 121.0288, 'elevation' => 156],
                    ['lat' => 12.6720, 'lng' => 121.0330, 'elevation' => 191],
                    ['lat' => 12.6836, 'lng' => 121.0378, 'elevation' => 229],
                    ['lat' => 12.6938, 'lng' => 121.0429, 'elevation' => 265],
                    ['lat' => 12.7007, 'lng' => 121.0468, 'elevation' => 294],
                    ['lat' => 12.7070, 'lng' => 121.0502, 'elevation' => 319],
                    ['lat' => 12.712976, 'lng' => 121.054025, 'elevation' => 341],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private static function archiveGalleryFrames(): array
    {
        $archiveGalleryPath = public_path('gallery-assets');
        $creditOverrides = self::archiveGalleryPhotoCredits();

        if (! File::isDirectory($archiveGalleryPath)) {
            return [];
        }

        return collect(File::files($archiveGalleryPath))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true))
            ->sortBy(fn ($file) => strtolower($file->getFilename()))
            ->values()
            ->map(function ($file, $index) use ($creditOverrides) {
                $encodedPath = collect(['gallery-assets', $file->getFilename()])
                    ->map(fn ($segment) => rawurlencode($segment))
                    ->implode('/');

                $number = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);

                return self::withPhotoCredit([
                    'image' => self::optimizedAsset($encodedPath),
                    'title' => "Archive field photo {$number}",
                    'caption' => 'Additional image from the growing park archive, preserved here for visitors who want a broader feel for the landscape.',
                    'meta' => 'Archive',
                    'credit' => $creditOverrides[$file->getFilename()] ?? null,
                ]);
            })
            ->all();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private static function biodiversityGalleryFrames(): array
    {
        $biodiversityContent = self::biodiversity();
        $profileFrames = collect($biodiversityContent['faunaProfiles'] ?? [])
            ->merge($biodiversityContent['floraProfiles'] ?? [])
            ->merge(
                collect($biodiversityContent['archiveSections'] ?? [])
                    ->flatMap(fn (array $section) => $section['profiles'] ?? [])
            )
            ->filter(fn (array $profile) => ! empty($profile['image']))
            ->map(function (array $profile): array {
                return self::withPhotoCredit([
                    'image' => $profile['image'],
                    'alt' => $profile['alt'] ?: $profile['title'],
                    'title' => $profile['title'],
                    'caption' => '',
                    'meta' => $profile['meta'] ?? 'Biodiversity',
                    'credit' => $profile['credit'] ?? null,
                ]);
            });

        return $profileFrames
            ->merge(self::biodiversityAssetGalleryFrames())
            ->unique(fn (array $frame) => $frame['image'])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private static function biodiversityAssetGalleryFrames(): array
    {
        $assetBasePath = public_path('biodiversity-assets');

        if (! File::isDirectory($assetBasePath)) {
            return [];
        }

        return collect(File::allFiles($assetBasePath))
            ->filter(fn ($file) => self::isImageFile($file->getFilename()))
            ->map(function ($file): ?array {
                $relativePath = ltrim(str_replace('\\', '/', Str::after($file->getPathname(), public_path())), '/');
                $relativeSegments = explode('/', $relativePath);

                if (($relativeSegments[1] ?? null) === 'bg') {
                    return null;
                }

                $title = self::biodiversityAssetGalleryTitle($relativeSegments, $file->getFilename());
                $meta = self::biodiversityAssetGalleryMeta($relativeSegments);

                return self::withPhotoCredit([
                    'image' => self::assetFromPublicPath($file->getPathname()),
                    'alt' => $title.' documented in Mounts Iglit-Baco Natural Park',
                    'title' => $title,
                    'caption' => 'Additional biodiversity archive photo of '.$title.' documented in Mounts Iglit-Baco Natural Park.',
                    'meta' => $meta,
                ]);
            })
            ->filter()
            ->sortBy(fn (array $frame) => strtolower(implode(' ', [
                (string) ($frame['meta'] ?? ''),
                (string) ($frame['title'] ?? ''),
                (string) ($frame['image'] ?? ''),
            ])))
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $relativeSegments
     */
    private static function biodiversityAssetGalleryMeta(array $relativeSegments): string
    {
        return match (strtolower((string) ($relativeSegments[1] ?? ''))) {
            'birds' => 'Bird archive',
            'invertebrates' => 'Invertebrate archive',
            default => 'Biodiversity archive',
        };
    }

    /**
     * @param  array<int, string>  $relativeSegments
     */
    private static function biodiversityAssetGalleryTitle(array $relativeSegments, string $filename): string
    {
        $directoryLabel = trim((string) ($relativeSegments[count($relativeSegments) - 2] ?? ''));

        if ($directoryLabel !== '' && ! in_array(strtolower($directoryLabel), ['biodiversity-assets', 'birds', 'invertebrates'], true)) {
            return $directoryLabel;
        }

        return (string) Str::of(pathinfo($filename, PATHINFO_FILENAME))
            ->replace(['_', '-'], ' ')
            ->squish()
            ->headline();
    }

    /**
     * @param  array<int, array<string, mixed>>  $frames
     * @return array<int, array<string, mixed>>
     */
    private static function uniqueGalleryFramesByImage(array $frames): array
    {
        return collect($frames)
            ->filter(fn (array $frame) => ! empty($frame['image']))
            ->unique(fn (array $frame) => $frame['image'])
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, string|null>>  ...$profileSets
     * @return array<int, array<string, string|null>>
     */
    private static function mergeBiodiversityProfiles(array ...$profileSets): array
    {
        $profilesByTitle = [];

        foreach ($profileSets as $profileSet) {
            foreach ($profileSet as $profile) {
                $title = trim((string) ($profile['title'] ?? ''));

                if ($title === '') {
                    continue;
                }

                $profilesByTitle[strtolower($title)] = $profile;
            }
        }

        return array_values($profilesByTitle);
    }

    /**
     * @param  array<int, array<string, mixed>>  $frames
     * @return array<int, array<string, mixed>>
     */
    private static function groupGalleryFrames(array $frames): array
    {
        $groupDefinitions = [
            'scenic-views' => [
                'title' => 'Scenic views',
                'meta' => 'Landscape set',
                'summary' => 'Ridgelines, weather, and wide panoramas that help visitors feel the park before arrival.',
            ],
            'wildlife-habitat' => [
                'title' => 'Wildlife, flora, and habitat',
                'meta' => 'Biodiversity set',
                'summary' => 'Fauna, flora, and habitat-focused photos tied to the park\'s wider conservation story.',
            ],
        ];

        return collect($frames)
            ->groupBy(fn (array $frame) => self::galleryGroupIdForFrame($frame))
            ->map(function ($groupFrames, string $groupId) use ($groupDefinitions): array {
                $definition = $groupDefinitions[$groupId] ?? $groupDefinitions['scenic-views'];
                $framesInGroup = collect($groupFrames)->values()->all();
                $coverFrame = $framesInGroup[0];

                return [
                    'id' => $groupId,
                    'title' => $definition['title'],
                    'meta' => $definition['meta'],
                    'summary' => $definition['summary'],
                    'count' => count($framesInGroup),
                    'coverImage' => $coverFrame['thumbnail'] ?? $coverFrame['image'],
                    'coverAlt' => $coverFrame['alt'] ?? $coverFrame['title'],
                    'frames' => $framesInGroup,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $frame
     */
    private static function galleryGroupIdForFrame(array $frame): string
    {
        $context = strtolower(
            trim(
                implode(' ', [
                    (string) ($frame['meta'] ?? ''),
                    (string) ($frame['title'] ?? ''),
                    (string) ($frame['alt'] ?? ''),
                    (string) ($frame['caption'] ?? ''),
                ])
            )
        );

        if (self::galleryContextContainsAny($context, ['native', 'flora', 'tree', 'vine', 'forest', 'hardwood', 'resin'])) {
            return 'wildlife-habitat';
        }

        if (self::galleryContextContainsAny($context, ['wildlife', 'tamaraw', 'bird', 'owl', 'mammal', 'fauna', 'habitat', 'invertebrate', 'butterfly', 'dragonfly', 'insect'])) {
            return 'wildlife-habitat';
        }

        if (self::galleryContextContainsAny($context, ['trail', 'archive', 'field', 'route'])) {
            return 'scenic-views';
        }

        return 'scenic-views';
    }

    private static function galleryContextContainsAny(string $context, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($context, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private static function officialPhotoCredit(): string
    {
        return 'MIBNP - PAMO staff';
    }

    /**
     * @return array<string, string>
     */
    private static function archiveGalleryPhotoCredits(): array
    {
        return [
            // 'example.jpg' => 'Photographer Name',
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private static function withPhotoCredit(array $item): array
    {
        if (empty($item['image'])) {
            return $item;
        }

        $credit = is_string($item['credit'] ?? null) ? trim($item['credit']) : '';
        $item['credit'] = $credit !== '' ? $credit : self::officialPhotoCredit();
        $thumbnail = is_string($item['thumbnail'] ?? null) ? trim($item['thumbnail']) : '';
        $item['thumbnail'] = $thumbnail !== '' ? $thumbnail : (self::thumbnailAssetForImage((string) $item['image']) ?? $item['image']);

        return $item;
    }

    /**
     * @param  array<string, string|null>  $profile
     * @return array<string, string|null>
     */
    private static function withSourceAcknowledgement(
        array $profile,
        string $sourceName,
        string $sourceUrl,
        string $sourceLabel = 'Description adapted from'
    ): array {
        $profile['source_label'] = $sourceLabel;
        $profile['source_name'] = $sourceName;
        $profile['source_url'] = $sourceUrl;

        return $profile;
    }

    /**
     * @param  array<string, string|null>  $profile
     * @return array<string, string|null>
     */
    private static function withEbirdAcknowledgement(array $profile): array
    {
        return self::withSourceAcknowledgement($profile, 'eBird.org', 'https://ebird.org/explore');
    }
}
