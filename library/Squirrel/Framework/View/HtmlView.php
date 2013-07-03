<?php

namespace Squirrel\Framework\View;

use Squirrel\Html\Helper;

/**
 * Abstract view class including HTML helper.
 *
 * @package Squirrel\Framework\View
 * @author Valérian Galliat
 */
abstract class HtmlView extends View
{
    /**
     * @var Squirrel\Html\Helper
     */
    protected $html;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $variables = null)
    {
        parent::__construct($variables);
        $this->html = new Helper();
    }
}
