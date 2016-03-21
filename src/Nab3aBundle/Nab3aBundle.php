<?php

namespace Nab3aBundle;

use Matthias\BundlePlugins\BundleWithPlugins;
use Nab3aBundle\Core\CorePlugin;

class Nab3aBundle extends BundleWithPlugins
{
    protected function getAlias()
    {
        return 'nab3a';
    }

    protected function alwaysRegisteredPlugins()
    {
        return array(new CorePlugin());
    }
}
