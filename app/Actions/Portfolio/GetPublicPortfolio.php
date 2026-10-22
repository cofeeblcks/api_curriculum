<?php

namespace App\Actions\Portfolio;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\SkillCategory;

final class GetPublicPortfolio
{
    /**
     * @return array{profile: Profile|null, skillCategories: \Illuminate\Support\Collection, experiences: \Illuminate\Support\Collection, educations: \Illuminate\Support\Collection}
     */
    public function execute(): array
    {
        return [
            'profile' => Profile::query()->first(),
            'skillCategories' => SkillCategory::query()
                ->with('skills')
                ->orderBy('sort_order')
                ->get(),
            'experiences' => Experience::query()
                ->orderBy('sort_order')
                ->get(),
            'educations' => Education::query()
                ->orderBy('sort_order')
                ->get(),
        ];
    }
}
