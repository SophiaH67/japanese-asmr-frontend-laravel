<?php

namespace App\View\Components\Downloads;

use Illuminate\View\Component;

class DeleteLabel extends Component
{
    /**
     * @var int
     */
    public $id;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.downloads.delete-label');
    }
}
