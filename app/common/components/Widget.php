<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 03/03/2017
 * Time: 10:32
 */

namespace app\common\components;


use app\common\traits\TemplateTrait;

abstract class Widget
{
    use TemplateTrait;

    /**
     * @todo widget init event
     * @event Event an event that is triggered when the widget is initialized via [[init()]].
     */
    const EVENT_INIT = 'init';
    /**
     * @todo widget beforeRun event
     * @event WidgetEvent an event raised right before executing a widget.
     * You may set [[WidgetEvent::isValid]] to be false to cancel the widget execution.
     */
    const EVENT_BEFORE_RUN = 'beforeRun';
    /**
     * @todo widget afterRun event
     * @event WidgetEvent an event raised right after executing a widget.
     */
    const EVENT_AFTER_RUN = 'afterRun';


    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (!empty($config)) {
            \YunShop::configure($this, $config);
        }
        $this->init();
    }

    public function init()
    {

    }

    abstract public function run();



}