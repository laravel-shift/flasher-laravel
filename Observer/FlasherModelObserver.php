<?php

namespace Flasher\Laravel\Observer;

use Flasher\Prime\Config\ConfigInterface;
use Flasher\Prime\FlasherInterface;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Model;

final class FlasherModelObserver
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var FlasherInterface
     */
    private $flasher;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @param ConfigInterface  $config
     * @param FlasherInterface $flasher
     * @param Translator       $translator
     */
    public function __construct(ConfigInterface $config, FlasherInterface $flasher, Translator $translator)
    {
        $this->config = $config;
        $this->flasher = $flasher;
        $this->translator = $translator;
    }

    /**
     * Handle the Model "created" event.
     *
     * @param Model $model
     *
     * @return void
     */
    public function created(Model $model)
    {
        $this->addFlash(__FUNCTION__, $model);
    }

    /**
     * Handle the Model "updated" event.
     *
     * @param Model $model
     *
     * @return void
     */
    public function updated(Model $model)
    {
        $this->addFlash(__FUNCTION__, $model);
    }

    /**
     * Handle the Model "deleted" event.
     *
     * @param Model $model
     *
     * @return void
     */
    public function deleted(Model $model)
    {
        $this->addFlash(__FUNCTION__, $model);
    }

    /**
     * Handle the Model "restored" event.
     *
     * @param Model $model
     *
     * @return void
     */
    public function restored(Model $model)
    {
        $this->addFlash(__FUNCTION__, $model);
    }

    /**
     * Handle the Model "force deleted" event.
     *
     * @param Model $model
     *
     * @return void
     */
    public function forceDeleted(Model $model)
    {
        $this->addFlash(__FUNCTION__, $model);
    }

    /**
     * @param string $method
     * @param Model  $model
     */
    private function addFlash($method, Model $model)
    {
        $exludes = $this->config->get('observer_events.exclude', array());
        if (in_array($method, $exludes)) {
            return;
        }

        if (isset($exludes[$method]) && in_array(get_class($model), $exludes[$method])) {
            return;
        }

        if ($this->translator->has(sprintf('flasher::messages.flashable.%s.%s', get_class($model), $method))) {
            $message = $this->translator->get(sprintf('flasher::messages.flashable.%s.%s', get_class($model), $method));
        } else {
            $message = $this->translator->get(sprintf('flasher::messages.flashable.default.%s', $method));
            $message = str_replace('{{ model }}', substr(strrchr(get_class($model), "\\"), 1), $message);
        }

        $this->flasher->addSuccess($message);
    }
}
