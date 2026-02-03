<?php

namespace Database\Seeders;

use App\Models\MapIcon;
use App\Models\Category;
use App\Models\Attraction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AttractionSeeder extends Seeder
{
    public function run(): void
    {
        // A) Ratusz w Kaliszu
        $ratusz = Attraction::create([
            'name'         => 'Ratusz w Kaliszu',
            'location'     => 'Główny Rynek 20, 62-800 Kalisz, Polska',
            'description'  => "Neoklasycystyczny ratusz miejski w Kaliszu, położony w samym centrum Głównego Rynku. "
                            . "Obecny budynek został zaprojektowany przez Sylwestra Pajzderskiego i wybudowany w latach 1920–1925 "
                            . "po zniszczeniu poprzedniej, barokowej ratuszowej konstrukcji podczas I wojny światowej "
                            . "(wysadzona przez Niemców w 1914 roku). Charakterystyczna wieża z zegarem (tarcza o średnicy 3 metrów) "
                            . "oraz figura św. Jana Nepomucena na fasadzie to jedne z najbardziej rozpoznawalnych elementów. "
                            . "Ratusz jest nie tylko siedzibą władz miejskich, ale także ważnym punktem orientacyjnym i popularnym "
                            . "miejscem spotkań mieszkańców oraz turystów.",
            'opening_time' => '08:00:00',
            'closing_time' => '15:30:00',
            'is_active'    => true,
            'rating'       => 4.7,
            'latitude'     => 51.76263214,
            'longitude'    => 18.08993318,
        ]);

        $categoryRatusz = Category::whereIn('name', [
            'Miejsca historyczne'
        ])->first() ?? Category::first();

        if ($categoryRatusz) {
            $ratusz->categories()->attach($categoryRatusz->id);
        }

        $mapIconRatusz = $categoryRatusz
            ? MapIcon::where('category_id', $categoryRatusz->id)->first()
            : null;

        $mapIconRatusz = $mapIconRatusz ?? MapIcon::where('name', 'Domyślny marker')->first();

        $ratusz->update([
            'map_icon' => $mapIconRatusz?->icon_url ?? 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
        ]);

        Log::warning("Локальні фото ратуші не знайдено. Додано якісні фото з мережі.");

        $qualityFallbackPhotosRatusz = [
            'https://kierunkowo.pl/wp-content/uploads/2023/07/DSC_3948-1.jpg',
            'https://latarnikkaliski.pl/wp-content/uploads/2021/06/Glowny-Rynek1.jpg',
            'https://kaliszczasemmalowany.pl/wp-content/uploads/2017/01/ratusz-2017-3.jpg',
        ];

        foreach ($qualityFallbackPhotosRatusz as $url) {
            $ratusz->photos()->create([
                'path'       => $url,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // B) Katedra św. Mikołaja w Kaliszu
        $katedra = Attraction::create([
            'name'         => 'Katedra św. Mikołaja w Kaliszu',
            'location'     => 'Kolegiacka 2, 62-800 Kalisz, Polska',
            'description'  => "Gotycka katedra diecezji kaliskiej, jedna z najstarszych i najcenniejszych świątyń w regionie. "
                            . "Jej początki sięgają XIII wieku, choć obecny kształt uzyskała po licznych przebudowach. "
                            . "Wewnątrz znajdują się cenne zabytki sztuki sakralnej, w tym gotycki poliptyk, renesansowe nagrobki "
                            . "oraz barokowe ołtarze. Wieża katedralna o wysokości 69 metrów góruje nad panoramą miasta. "
                            . "Katedra jest ważnym miejscem pielgrzymkowym - znajduje się tu Sanktuarium św. Józefa.",
            'opening_time' => '07:00:00',
            'closing_time' => '19:00:00',
            'is_active'    => true,
            'rating'       => 4.8,
            'latitude'     => 51.76406185,
            'longitude'    => 18.08921443,
        ]);

        $categoryKatedra = Category::whereIn('name', [
            'Kościoły i sanktuaria',
            'Zabytki UNESCO'
        ])->first() ?? Category::where('name', 'Kościoły i sanktuaria')->first();

        if ($categoryKatedra) {
            $katedra->categories()->attach($categoryKatedra->id);
        }

        $mapIconKatedra = $categoryKatedra
            ? MapIcon::where('category_id', $categoryKatedra->id)->first()
            : null;

        $mapIconKatedra = $mapIconKatedra ?? MapIcon::where('name', 'Domyślny marker')->first();

        $katedra->update([
            'map_icon' => $mapIconKatedra?->icon_url ?? 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
        ]);

        $katedraPhotos = [
            'https://lh3.googleusercontent.com/gps-cs-s/AHVAwepwC-FOW2yCdkIHYVnL67HBkffbkwJLu_Z-DZA1Y72U7n7F5_cB-V9mxsfz-ye1bALSBEIzaPWjYYktIxZHQl7JEM2x7P8D4MIMp0MyHK1nsx8mk3bRx40Oip5gk8leb9zJBFhyWw=s1360-w1360-h1020-rw',
            'https://lh3.googleusercontent.com/gps-cs-s/AHVAweo8Vy8Yh9ySEkuE1wmposa6dhF_MdyGKLDXJI4R-zHp1_Ogd1TTH8IAhPnJZ2bog3uLAjZGt3KkwfXT2OC06SSs6ovf0bxH_DCnJ6m6lR9fnbdXSeS5n1DiMVDAa2m2vBRd5F4c=s1360-w1360-h1020-rw',
            'https://lh3.googleusercontent.com/gps-cs-s/AHVAweqlEpIqHni0tXn-r2VTiiR-E1WUWjzVptdp65a6lmhLP6ibqT-8vmt4SjtCek8nscG2Bpke4ezvBrgUfwcANhZldBvAJSx9ehxCO_av-fxR5n4rWNmoCffyI5QxhEMsol-kzlVT=s1360-w1360-h1020-rw',
        ];

        foreach ($katedraPhotos as $url) {
            $katedra->photos()->create([
                'path'       => $url,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // C) Teatr im. Wojciecha Bogusławskiego w Kaliszu
            $teatr = Attraction::create([
                'name'         => 'Teatr im. Wojciecha Bogusławskiego w Kaliszu',
                'location'     => 'pl. Bogusławskiego 1, 62-800 Kalisz, Polska',
                'description'  => "Jeden z najstarszych teatrów w Polsce, założony w 1800 roku. "
                                . "Obecny budynek w stylu neoklasycystycznym został wzniesiony w latach 1920-1936. "
                                . "Teatr nosi imię Wojciecha Bogusławskiego, ojca polskiego teatru narodowego, "
                                . "który występował tu na początku XIX wieku. Wnętrze zdobią bogate sztukaterie, "
                                . "kryształowe żyrandole i czerwony aksamit. Teatr jest ważnym ośrodkiem kultury "
                                . "w regionie, prezentując zarówno klasyczne, jak i współczesne spektakle.",
                'opening_time' => '10:00:00',
                'closing_time' => '18:00:00',
                'is_active'    => true,
                'rating'       => 4.6,
                'latitude'     => 51.75853247,
                'longitude'    => 18.09362698,
            ]);

            $categoryTeatr = Category::whereIn('name', [
                'Miejsca historyczne',
                'Muzea'
            ])->first() ?? Category::where('name', 'Miejsca historyczne')->first();

            if ($categoryTeatr) {
                $teatr->categories()->attach($categoryTeatr->id);
            }

            $mapIconTeatr = $categoryTeatr
                ? MapIcon::where('category_id', $categoryTeatr->id)->first()
                : null;

            $mapIconTeatr = $mapIconTeatr ?? MapIcon::where('name', 'Domyślny marker')->first();

            $teatr->update([
                'map_icon' => $mapIconTeatr?->icon_url ?? 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
            ]);

            $teatrPhotos = [
                'https://lh3.googleusercontent.com/p/AF1QipOsK0EadchDUI00I9OBCYD9_31JTxXmKoOzddnq=s1360-w1360-h1020-rw',
                'https://lh3.googleusercontent.com/gps-cs-s/AHVAwepkRFxEm8Vv1-Wb5Prr-9eXtX_KlyI0b8yrcodj10tYBw2Q__hpmMjVGmQN7J1vK6mTTcaO2nC_IQqKNdDQaniImLxP8e_vzM2oGaRWLBNMEX8OTGs3AhetfCWun6LdigUFacQp=s1360-w1360-h1020-rw',
                'https://lh3.googleusercontent.com/gps-cs-s/AHVAwerOHV7vA9o9Y61uOEvA-pmgHyNPTjcbj_6-eNgwFNlxOA1GgY1BL_SvpAmswqhNkyVnX6A_u8ZkkMhja2gbMqkAShWCUwU2jqCB6Ec2PvFO2NU6HDmJoncW12geGJOTw1TBHrgn=s1360-w1360-h1020-rw',
            ];

            foreach ($teatrPhotos as $url) {
                $teatr->photos()->create([
                    'path'       => $url,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        Attraction::factory()->count(50)->create();

    }
}