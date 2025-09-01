<?php

namespace franciscoblancojn\validator;

use Exception;

/**
 * Clase FValidator_Class
 *
 * Un validador flexible para datos en PHP. Permite definir reglas de validación
 * (string, number, boolean, array, object, regex, enum, etc.) y validarlas sobre
 * datos dinámicos. Se pueden encadenar reglas y personalizar mensajes de error.
 *
 * Ejemplo de uso:
 *
 * ```php
 * use franciscoblancojn\validator\FValidator;
 *
 * $validator = FValidator("email")
 *     ->isRequired()
 *     ->isEmail();
 *
 * try {
 *     $validator->validate("usuario@ejemplo.com"); // ✅ válido
 * } catch (Exception $e) {
 *     echo $e->getMessage(); // ❌ mensaje de error
 * }
 * ```
 */
class FValidator_Class
{
    /** @var string|null Nombre del campo a validar */
    private ?string $name = null;

    /** @var mixed Datos a validar */
    private $data;

    /** @var array Lista de reglas definidas */
    private array $rules = [];

    /** @var array Lista de mensajes de error personalizados */
    private array $messages = [];

    /** @var FValidator_Class|null Esquema para validar arrays */
    private ?FValidator_Class $arraySchema;

    /** @var FValidator_Class|null Esquema para validar objetos */
    private ?array $objectSchema;

    /**
     * Constructor
     *
     * @param string|null $name Nombre opcional del campo
     */
    public function __construct(?string $name = null)
    {
        $this->name = $name;
    }


    /**
     * Define el nombre del campo.
     *
     * @param string $name
     * @return self
     */

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Asigna los datos a validar.
     *
     * @param mixed $data
     * @return self
     */
    public function setData($data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Valida que el campo no sea nulo o vacío.
     *
     * @param string $message Mensaje de error
     * @return self
     */
    public function isRequired(string $message = "Este campo es obligatorio"): self
    {
        $this->rules['required'] = true;
        $this->messages['required'] = $message;
        return $this;
    }

    /**
     * Valida que el campo sea una cadena.
     *
     * @param string $message
     * @return self
     */
    public function isString(string $message = "Debe ser una cadena de texto"): self
    {
        $this->rules['string'] = true;
        $this->messages['string'] = $message;
        return $this;
    }

    /**
     * Valida que el campo sea un número.
     *
     * @param string $message
     * @return self
     */
    public function isNumber(string $message = "Debe ser un número"): self
    {
        $this->rules['number'] = true;
        $this->messages['number'] = $message;
        return $this;
    }

    /**
     * Valida que el campo sea un valor booleano.
     *
     * @param string $message
     * @return self
     */
    public function isBoolean(string $message = "Debe ser un valor booleano"): self
    {
        $this->rules['boolean'] = true;
        $this->messages['boolean'] = $message;
        return $this;
    }

    /**
     * Valida que el campo sea un array.
     *
     * @param FValidator_Class $arraySchema Esquema de validación de los elementos del array
     * @param string $message
     * @return self
     */
    public function isArray($arraySchema, string $message = "Debe ser un array"): self
    {
        $this->rules['array'] = true;
        $this->arraySchema = $arraySchema;
        $this->messages['array'] = $message;
        return $this;
    }

    /**
     * Valida que el campo sea un objeto.
     *
     * @param array<string,FValidator_Class> $objectSchema Esquema de validación de las propiedades del objeto
     * @param string $message
     * @return self
     */
    public function isObject($objectSchema, string $message = "Debe ser un objeto"): self
    {
        $this->rules['object'] = true;
        $this->objectSchema = $objectSchema;
        $this->messages['object'] = $message;
        return $this;
    }

    /**
     * Valida que el campo sea una fecha válida.
     *
     * @param string $message
     * @return self
     */
    public function isDate(string $message = "Debe ser una fecha válida"): self
    {
        $this->rules['date'] = true;
        $this->messages['date'] = $message;
        return $this;
    }

    /**
     * Valida que el campo sea un email válido.
     *
     * @param string $message
     * @return self
     */
    public function isEmail(string $message = "Debe ser un correo electrónico válido"): self
    {
        $this->rules['email'] = true;
        $this->messages['email'] = $message;
        return $this;
    }

    /**
     * Valida que el campo sea mayor o igual a un valor mínimo.
     *
     * @param int $min
     * @param string $message
     * @return self
     */
    public function isMin(int $min, string $message): self
    {
        $this->rules['min'] = $min;
        $this->messages['min'] = $message ||  "Debe ser mayor que $min";
        return $this;
    }

    /**
     * Valida que el campo sea menor o igual a un valor máximo.
     *
     * @param int $max
     * @param string $message
     * @return self
     */
    public function isMax(int $max, string $message): self
    {
        $this->rules['max'] = $max;
        $this->messages['max'] = $message || "Debe ser menor que $max";
        return $this;
    }

    /**
     * Valida que el campo sea igual a un valor específico.
     *
     * @param mixed $value
     * @param string $message
     * @return self
     */
    public function isEqual($value, string $message): self
    {
        $this->rules['equal'] = $value;
        $this->messages['equal'] = $message || "Debe ser igual a $value";
        return $this;
    }

    /**
     * Valida que el campo tenga una longitud exacta.
     *
     * @param int $length
     * @param string $message
     * @return self
     */
    public function isLength(int $length, string $message): self
    {
        $this->rules['length'] = $length;
        $this->messages['length'] = $message || "Debe tener una longitud de $length caracteres";
        return $this;
    }

    /**
     * Valida el campo usando una expresión regular.
     *
     * @param string $pattern
     * @param string $message
     * @return self
     */
    public function isRegex(string $pattern, string $message = "Formato inválido"): self
    {
        $this->rules['regex'] = $pattern;
        $this->messages['regex'] = $message;
        return $this;
    }

    /**
     * Valida que el campo sea uno de los valores permitidos.
     *
     * @param array $enumValues
     * @param string $message
     * @return self
     */
    public function isEnum(array $enumValues, string $message = "Valor no permitido"): self
    {
        $this->rules['enum'] = $enumValues;
        $this->messages['enum'] = $message;
        return $this;
    }
    /**
     * Ejecuta todas las validaciones definidas sobre los datos.
     *
     * @param mixed $data Datos a validar
     * @return bool true si es válido
     * @throws Exception si alguna validación falla
     */
    public function validate($data)
    {
        try {
            $this->data = $data;
            $this->onRequired();

            if (!empty($this->data)) {
                $this->onString();
                $this->onNumber();
                $this->onBoolean();
                $this->onArray();
                $this->onObject();
                $this->onDate();
                $this->onEmail();
                $this->onMin();
                $this->onMax();
                $this->onEqual();
                $this->onLength();
                $this->onRegex();
                $this->onEnum();
            }

            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function onRequired()
    {
        if (isset($this->rules['required']) && empty($this->data)) {
            throw new Exception($this->messages['required']);
        }
    }

    private function onString()
    {
        if (isset($this->rules['string']) && !is_string($this->data)) {
            throw new Exception($this->messages['string']);
        }
    }

    private function onNumber()
    {
        if (isset($this->rules['number']) && !is_numeric($this->data)) {
            throw new Exception($this->messages['number']);
        }
    }

    private function onBoolean()
    {
        if (isset($this->rules['boolean']) && !is_bool($this->data)) {
            throw new Exception($this->messages['boolean']);
        }
    }

    private function onDate()
    {
        if (isset($this->rules['date']) && !strtotime($this->data)) {
            throw new Exception($this->messages['date']);
        }
    }

    private function onEmail()
    {
        if (isset($this->rules['email']) && !filter_var($this->data, FILTER_VALIDATE_EMAIL)) {
            throw new Exception($this->messages['email']);
        }
    }

    private function onMin()
    {
        if (isset($this->rules['min']) && is_numeric($this->data) && $this->data < $this->rules['min']) {
            throw new Exception($this->messages['min']);
        }
    }

    private function onMax()
    {
        if (isset($this->rules['max']) && is_numeric($this->data) && $this->data > $this->rules['max']) {
            throw new Exception($this->messages['max']);
        }
    }

    private function onEqual()
    {
        if (isset($this->rules['equal']) && $this->data != $this->rules['equal']) {
            throw new Exception($this->messages['equal']);
        }
    }

    private function onLength()
    {
        if (isset($this->rules['length']) && is_string($this->data) && strlen($this->data) != $this->rules['length']) {
            throw new Exception($this->messages['length']);
        }
    }

    private function onRegex()
    {
        if (isset($this->rules['regex']) && !preg_match($this->rules['regex'], $this->data)) {
            throw new Exception($this->messages['regex']);
        }
    }

    private function onEnum()
    {
        if (isset($this->rules['enum']) && !in_array($this->data, $this->rules['enum'])) {
            throw new Exception($this->messages['enum']);
        }
    }

    private function onObject()
    {
        if (!isset($this->rules['object'])) {
            return;
        }
        if (!is_array($this->data) && !is_object($this->data)) {
            throw new Exception($this->messages['object']);
        }

        if ($this->objectSchema) {
            foreach ($this->objectSchema as $key => $validator) {
                if (isset($this->data[$key])) {
                    $value =  $this->data[$key];
                    $validator->validate($value);
                } else {
                    $validator->validate(null);
                }
            }
        }
    }

    private function onArray()
    {
        if (!isset($this->rules['array'])) {
            return;
        }
        if (!is_array($this->data)) {
            throw new Exception($this->messages['array']);
        }

        if ($this->arraySchema) {
            foreach ($this->data as $index => $value) {
                $this->arraySchema->validate($value);
            }
        }
    }
}
