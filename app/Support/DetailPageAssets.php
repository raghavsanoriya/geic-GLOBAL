<?php

namespace App\Support;

class DetailPageAssets
{
    public static function serviceGallery(array $service): array
    {
        return [
            ['src' => $service['image'], 'alt' => $service['image_alt'], 'label' => $service['title']],
            ['src' => 'assets/transglobe/services/services-team.avif', 'alt' => 'Trans Globe advisers discussing study-abroad options', 'label' => 'Expert guidance'],
            ['src' => 'assets/transglobe/services/services-campus.avif', 'alt' => 'International university campus', 'label' => 'Your next campus'],
        ];
    }

    public static function testGallery(array $test): array
    {
        return [
            ['src' => $test['image'], 'alt' => $test['image_alt'], 'label' => $test['title'].' preparation'],
            ['src' => 'assets/transglobe/services/services-team.avif', 'alt' => 'Advisers helping students plan their test preparation', 'label' => 'Focused guidance'],
            ['src' => 'assets/services/university-admissions.jpg', 'alt' => 'Students reviewing university options', 'label' => 'Application ready'],
        ];
    }

    public static function universityNetwork(): array
    {
        return [
            ['name' => 'University of York', 'logo' => 'assets/transglobe/universities/university-of-york.jpg'],
            ['name' => 'Australian National University', 'logo' => 'assets/transglobe/universities/australian-national-university.png'],
            ['name' => 'Queensland University of Technology', 'logo' => 'assets/transglobe/universities/queensland-university-of-technology.png'],
            ['name' => 'Arizona State University', 'logo' => 'assets/transglobe/universities/arizona-state-university.jpg'],
            ['name' => 'Massey University', 'logo' => 'assets/transglobe/universities/massey-university.jpg'],
            ['name' => 'University of Queensland', 'logo' => 'assets/transglobe/universities/university-of-queensland.png'],
            ['name' => 'Monash University', 'logo' => 'assets/transglobe/universities/monash-university.png'],
            ['name' => 'University of Adelaide', 'logo' => 'assets/transglobe/universities/adelaide-university.png'],
            ['name' => 'Auckland University of Technology', 'logo' => 'assets/transglobe/universities/auckland-university-of-technology.png'],
            ['name' => 'Brunel University London', 'logo' => 'assets/transglobe/universities/brunel-university-london.png'],
        ];
    }
}
