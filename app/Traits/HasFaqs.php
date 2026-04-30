<?php

namespace App\Traits;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasFaqs
{
    public function faqs(): MorphMany
    {
        return $this->morphMany(Faq::class, 'faqable')
            ->orderBy('position')
            ->orderBy('id');
    }

    public function activeFaqs(): MorphMany
    {
        return $this->faqs()->active();
    }
}
