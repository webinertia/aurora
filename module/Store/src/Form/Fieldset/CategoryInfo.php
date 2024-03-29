<?php

declare(strict_types=1);

namespace Store\Form\Fieldset;

use Dojo\Form\Element\Editor;
use Dojo\Form\Element\ComboBox;
use Dojo\Form\Element\TextBox;
use Dojo\Form\Element\ResetButton;
use Dojo\Form\Element\ValidationTextBox;
use Laminas\Filter\HtmlEntities;
use Laminas\Filter\StripTags;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StringToLower;
use Laminas\Filter\Word\SeparatorToDash;
use Laminas\Filter\ToInt;
use Laminas\Filter\ToNull;
use Laminas\Filter\UpperCaseWords;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\Db\NoRecordExists;
use Laminas\Validator\StringLength;
use Store\Model\Category;

final class CategoryInfo extends Fieldset implements InputFilterProviderInterface
{
    /** @var Category $category */
    protected $category;
    /** @var array<mixed> $categoryValueOptions */
    protected $categoryValueOptions;

    public function __construct(Category $category, $name = 'category-data', $options = [])
    {
        parent::__construct($name, $options);
        $this->category = $category;
        $this->categoryValueOptions = $this->category->fetchSelectValueOptions();
    }

    public function init(): void
    {
        $this->add([
            'name' => 'label',
            'type' => ValidationTextBox::class,
            'attributes' => [
                'placeholder'     => 'Category Name:',
                'data-dojo-props' => 'validator:dojox.validate.isText, constraints:{minLength:1, maxLength:255}, invalidMessage:\'Category name must be between 1 and 255 Characters\'',
            ],
        ])->add([
            'id'   => 'parentCategoryId',
            'name' => 'parentId',
            'type' => Select::class,
            'attributes' => [
                'data-dojo-type' => 'dijit/form/Select',
            ],
            'options' => [
                'empty_option'  => 'No Parent',
                'value_options' => $this->categoryValueOptions,
            ],
        ])->add([
            'name' => 'active',
            'type' => Checkbox::class,
            'attributes' => [
                'data-dojo-type' => 'dijit/form/CheckBox',
            ],
            'options' => [
                'label' => 'Active ',
            ],
        ])->add([
            'name' => 'isBundle',
            'type' => Checkbox::class,
            'attributes' => [
                'data-dojo-type' => 'dijit/form/CheckBox',
            ],
            'options' => [
                'label' => 'Product Bundle ',
            ],
        ])->add([
            'name' => 'onHome',
            'type' => Checkbox::class,
            'attributes' => [
                'data-dojo-type' => 'dijit/form/CheckBox',
            ],
            'options' => [
                'label' => 'Show On Home Page? ',
            ],
        ])->add([
            'name' => 'description',
            'type' => Editor::class,
        ]);
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'label' => [
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                    ['name' => UpperCaseWords::class],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 255,
                        ],
                    ],
                    [
                        'name'    => NoRecordExists::class,
                        'options' => [
                            'table' => $this->options['db']['store_categories_table_name'],
                            'field' => 'label',
                            'dbAdapter' => $this->category->getAdapter(),
                            'messages' => [
                                NoRecordExists::ERROR_RECORD_FOUND => 'Category by that name is already in use!',
                            ],
                        ],
                    ],
                ],
            ],
            'parentId' => [
                'required' =>  false,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => [
                            'type' => ToNull::TYPE_ALL,
                        ],
                    ]
                ],
            ],
        ];
    }
}
