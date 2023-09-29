<?php

namespace Lunar\Hub\Http\Livewire\Components;

use Illuminate\Support\Collection;
use Livewire\Component;
use Lunar\Models\Brand;

class BrandSearch extends Component
{
    /**
     * Should the browser be visible?
     */
    public bool $showBrowser = false;

    /**
     * The search term.
     *
     * @var string
     */
    public $searchTerm = null;

    /**
     * Max results we want to show.
     *
     * @var int
     */
    public $maxResults = 50;

    /**
     * Any existing brands to exclude from selecting.
     */
    public Collection $existing;

    /**
     * The currently selected brands.
     */
    public array $selected = [];

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            'searchTerm' => 'required|string|max:255',
        ];
    }

    /**
     * Return the selected collections.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSelectedModelsProperty()
    {
        return Brand::whereIn('id', $this->selected)->get();
    }

    /**
     * Return the existing collection ids.
     *
     * @return array
     */
    public function getExistingIdsProperty()
    {
        return $this->existing->pluck('id');
    }

    /**
     * Listener for when show browser is updated.
     *
     * @return void
     */
    public function updatedShowBrowser()
    {
        $this->selected = [];
        $this->searchTerm = null;
    }

    /**
     * Add the brand to the selected array.
     *
     * @param  string|int  $id
     * @return void
     */
    public function selectBrand($id)
    {
        $this->selected[] = $id;
    }

    /**
     * Remove a brand from the selected brands.
     *
     * @param  string|int  $id
     * @return void
     */
    public function removeBrand($id)
    {
        $index = collect($this->selected)->search($id);
        unset($this->selected[$index]);
        $this->selected = collect($this->selected)->values();
    }

    /**
     * Returns the computed search results.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getResultsProperty()
    {
        if (! $this->searchTerm) {
            return null;
        }

        return Brand::search($this->searchTerm)->paginate($this->maxResults);
    }

    public function triggerSelect()
    {
        $this->emit('brandSearch.selected', $this->selected);

        $this->showBrowser = false;
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('adminhub::livewire.components.brand-search')
            ->layout('adminhub::layouts.base');
    }
}
