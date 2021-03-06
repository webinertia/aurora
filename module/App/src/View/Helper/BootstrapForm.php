<?php

declare(strict_types=1);

namespace App\View\Helper;

use Laminas\Form\FieldsetInterface;
use Laminas\Form\FormInterface;
use Laminas\View\Helper\AbstractHelper;

use function method_exists;
use function str_replace;

class BootstrapForm extends AbstractHelper
{
    public const MODE_INLINE     = 'inline';
    public const MODE_HORIZONTAL = 'horizontal';
    public const MODE_VERTICAL   = 'vertical';
    /** @var AbstractHelper $formHelper */
    protected $formHelper;

    public function __invoke(?FormInterface $form = null, string $mode = self::MODE_VERTICAL): string
    {
        $this->formHelper = $this->getView()->form();
        if (! $form) {
            return $this;
        }
        return $this->render($form, $mode);
    }

    public function render(FormInterface $form, string $mode = self::MODE_VERTICAL): string
    {
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }
        $formContent     = '';
        $existingClasses = $form->getAttribute('class');
        if ($existingClasses === null) {
            $existingClasses = '';
        }
        //let’s make sure that we don’t have bootstrap form classes
        $existingClasses = str_replace('form-horizontal', '', str_replace('form-inline', '', $existingClasses));
        if ($mode === self::MODE_INLINE) {
            $form->setAttribute('class', $existingClasses . ' form-inline');
        } elseif ($mode === self::MODE_HORIZONTAL) {
            $form->setAttribute('class', $existingClasses . ' form-horizontal');
        }

        foreach ($form as $element) {
            if ($element instanceof FieldsetInterface) {
                $formContent .= $this->getView()->bootstrapFormCollection($element);
            } else {
                $formContent .= $this->getView()->bootstrapFormRow($element, $mode);
            }
        }
        return $this->formHelper->openTag($form) . $formContent . $this->formHelper->closeTag();
    }
}
