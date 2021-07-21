<?php

namespace App\Http\Livewire\Panel;

use App\Models\ProductPropertyName;
use App\Models\ProductPropertyValue;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProductVariants extends Component
{
    public $product;
    public $propertyNames = [];
    private $productVariantGroups = [];

    public $idOfNewVariantGroupsPropertyName;
    public $newVariantGroupsPropertyName;

    public $propertyValuesOfSelectedVariantPropertyName = [];
    public $variantsOfNewVariantGroup = [];

    public $propertyValueIdOfNewVariant;
    public $priceOfNewVariant;
    public $stockOfNewVariant;

    public $propertyValueOfNewVariant;


    protected $listeners = ['resetPropertyName' => 'resetPropertyName'];

    public function rules()
    {
        return [
            'propertyValueIdOfNewVariant' => [
                'required',
                'exists:product_property_values,id',
                function ($attribute, $value, $fail) {
                    $propertyValueIds = array_map(function ($item) {
                        return $item['id'];
                    }, $this->newVariantGroupsPropertyName->values->toArray());
                    if (!in_array($value, $propertyValueIds))
                        $fail('Invalid property value');
                },
                function ($attribute, $value, $fail) {
                    foreach ($this->variantsOfNewVariantGroup as $variant) {
                        if ($variant['property_value_id'] == $value)
                            $fail('This property value is already used');
                    }
                }
            ],
            'variantsOfNewVariantGroup' => ['min:2'],
        ];
    }

    public function mount()
    {
        $this->getProductVariantGroups();
        $this->propertyNames = ProductPropertyName::all();
    }

    public function hydrate()
    {
        $this->getProductVariantGroups();
    }

    public function render()
    {
        return view('livewire.panel.product-variants', ['productVariantGroups' => $this->productVariantGroups]);
    }

    public function updatedIdOfNewVariantGroupsPropertyName()
    {
        $this->newVariantGroupsPropertyName = ProductPropertyName::find($this->idOfNewVariantGroupsPropertyName);
        $this->reset('variantsOfNewVariantGroup', 'stockOfNewVariant', 'priceOfNewVariant');
    }

    public function addVariantToNewVariantGroup()
    {
        $this->validateOnly('propertyValueIdOfNewVariant');
        $newVariantDataArray = [
            'product_id' => $this->product->id,
            'property_name_id' => $this->idOfNewVariantGroupsPropertyName,
            'property_value_id' => $this->propertyValueOfNewVariant->id,
            'value' => $this->propertyValueOfNewVariant->value,
            'stock' => $this->stockOfNewVariant,
            'price' => $this->priceOfNewVariant
        ];

        array_push($this->variantsOfNewVariantGroup, $newVariantDataArray);
        $this->reset('propertyValueOfNewVariant', 'propertyValueIdOfNewVariant', 'priceOfNewVariant', 'stockOfNewVariant');
    }

    public function resetPropertyName()
    {
        $this->reset('newVariantGroupsPropertyName', 'idOfNewVariantGroupsPropertyName');
    }

    public function updatedPropertyValueIdOfNewVariant()
    {
        $this->propertyValueOfNewVariant = ProductPropertyValue::find($this->propertyValueIdOfNewVariant);
    }

    public function removeVariantFromNewVariantGroup($valueOfVariantToRemove)
    {
        foreach ($this->variantsOfNewVariantGroup as $key => $variant) {
            if ($variant['value'] == $valueOfVariantToRemove)
                unset($this->variantsOfNewVariantGroup[$key]);
        }
    }

    public function createVariantGroup()
    {
        $this->validateOnly('variantsOfNewVariantGroup');
        DB::transaction(function () {
            foreach ($this->variantsOfNewVariantGroup as $variant) {
                ProductVariant::create([
                    'product_id' => $variant['product_id'],
                    'property_name_id' => $variant['property_name_id'],
                    'property_value_id' => $variant['property_value_id'],
                    'price' => $variant['price'],
                    'stock' => $variant['stock']
                ]);
            }
        });
        $this->getProductVariantGroups();
        $this->reset('variantsOfNewVariantGroup', 'idOfNewVariantGroupsPropertyName', 'newVariantGroupsPropertyName');
    }

    public function getProductVariantGroups()
    {
        $this->productVariantGroups = ProductVariant::with('value', 'name')->where('product_id', $this->product->id)
            ->get()
            ->groupBy('property_name_id')
            ->toBase();
    }

    public function removeVariantGroup($propertyNameIdOfVariantGroupId)
    {
        ProductVariant::where('product_id', $this->product->id)
            ->where('property_name_id', $propertyNameIdOfVariantGroupId)
            ->delete();
        $this->getProductVariantGroups();
    }
}
