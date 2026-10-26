<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class MobileProfileEvaluationControllerTest extends TestCase
{
    public function test_valid_profile_returns_advisory_guidance(): void
    {
        $this->postJson(route('api.mobile.profile-evaluations.store'), [
            'academicPercentage' => 76,
            'studyLevel' => 'Postgraduate',
            'preferredDestinations' => ['Australia', 'United Kingdom'],
            'englishTest' => 'IELTS',
            'englishScore' => '7.0',
            'preferredIntake' => 'September 2027',
            'fullName' => 'Preview Student',
            'intendedCourse' => 'Data Science',
            'workExperienceYears' => 1,
            'originalStudyLevel' => 'Masters',
        ])->assertOk()
            ->assertJsonPath('matches.0.id', 'australia')
            ->assertJsonPath('matches.1.id', 'uk')
            ->assertJsonPath('submittedProfile.fullName', 'Preview Student')
            ->assertJsonPath('submittedProfile.intendedCourse', 'Data Science')
            ->assertJsonPath('submittedProfile.originalStudyLevel', 'Masters')
            ->assertJsonStructure(['readinessScore', 'summary', 'matches', 'strengths', 'actionItems', 'disclaimer']);
    }

    public function test_returns_422_when_the_profile_is_incomplete(): void
    {
        $this->postJson(route('api.mobile.profile-evaluations.store'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['academicPercentage', 'studyLevel', 'preferredDestinations', 'englishTest']);
    }

    public function test_returns_422_when_destination_is_not_in_the_catalog(): void
    {
        $this->postJson(route('api.mobile.profile-evaluations.store'), [
            'academicPercentage' => 70,
            'studyLevel' => 'Undergraduate',
            'preferredDestinations' => ['Atlantis'],
            'englishTest' => 'Planning to take a test',
        ])->assertUnprocessable()
            ->assertJsonPath('errors.preferredDestinations.0', 'The selected destination is not available.');
    }
}
