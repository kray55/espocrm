<?php

class AfterInstall
{
    protected $container;

    public function run($container)
    {
        $this->container = $container;
    }

    protected function clearCache()
    {
        try {
            $this->container->get('dataManager')->clearCache();
        } catch (\Throwable $e) {
            $this->container->get('log')->warning(
                'AfterInstall: unable to clear cache: ' . $e->getMessage()
            );
        }
    }
}
