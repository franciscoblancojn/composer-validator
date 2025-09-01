<?php

namespace franciscoblancojn\validator;

/**
 * Función auxiliar para crear un validador.
 *
 * @param string|null $name
 * @return FValidator_Class
 */
function FValidator(?string $name = null)
{
    return new FValidator_Class($name);
}
