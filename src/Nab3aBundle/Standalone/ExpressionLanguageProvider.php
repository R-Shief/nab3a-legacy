<?php

namespace Nab3aBundle\Standalone;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @return ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        return array(
          new ExpressionFunction('nab3a_paths', function () {
              return '[\Nab3aBundle\Standalone\ParameterProvider::expandHomeDirectory(\'~/.rshief\'), getcwd()]';
          }, function (array $variables) {
              return [ParameterProvider::expandHomeDirectory('~/.rshief'), getcwd()];
          }),
          new ExpressionFunction('nab3a_parameter', function ($arg) {
              return sprintf('$this->get(\'%s\')->get(%s)', 'nab3a.standalone.parameters', $arg);
          }, function (array $variables, $value) {
              return $variables['container']->get('nab3a.standalone.parameters')->get($value);
          }),
        );
    }
}
