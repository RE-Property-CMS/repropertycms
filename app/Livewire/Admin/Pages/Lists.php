<?php

namespace App\Livewire\Admin\Pages;

use Livewire\Component;

/**
 * This component is no longer used — the pages list is now a standard
 * Blade view served by PagesController::index().
 * Kept to avoid class-not-found errors from any cached routes.
 */
class Lists extends Component
{
    public function render()
    {
        return view('livewire.admin.pages.lists');
    }
}
