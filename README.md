# FValidator - PHP Validation Library

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.4-blue.svg)](https://php.net/)
![License](https://img.shields.io/badge/License-MIT-green.svg)

Una librería de validación flexible y fácil de usar para PHP que permite definir reglas de validación de forma fluida y encadenable.

## Características

- ✅ **Validaciones fluidas**: Encadena múltiples reglas de validación
- 🎯 **Mensajes personalizables**: Define tus propios mensajes de error
- 🔧 **Flexible**: Valida strings, números, arrays, objetos y más
- 📧 **Validaciones comunes**: Email, fechas, regex, enum
- 🏗️ **Esquemas complejos**: Valida arrays y objetos anidados
- 🚀 **Fácil de usar**: Sintaxis intuitiva y documentada

## Instalación

### Via Composer (Recomendado)

```bash
composer require franciscoblancojn/validator
```

### Instalación Manual

Descarga el archivo `FValidator.php` e inclúyelo en tu proyecto:

```php
require_once 'path/to/FValidator.php';
```

## Uso Básico

### Validación Simple

```php
<?php

use franciscoblancojn\validator\FValidator;

// Crear un validador
$validator = FValidator("email")
    ->isRequired()
    ->isEmail();

try {
    $validator->validate("usuario@ejemplo.com"); // ✅ Válido
    echo "Email válido!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage(); // ❌ Mostrar error
}
```

### Validación de Números

```php
$ageValidator = FValidator("edad")
    ->isRequired()
    ->isNumber()
    ->isMin(18, "Debe ser mayor de 18 años")
    ->isMax(120, "Edad no válida");

try {
    $ageValidator->validate(25); // ✅ Válido
} catch (Exception $e) {
    echo $e->getMessage();
}
```

### Validación de Strings

```php
$usernameValidator = FValidator("username")
    ->isRequired()
    ->isString()
    ->isLength(5, "El nombre de usuario debe tener exactamente 5 caracteres")
    ->isRegex('/^[a-zA-Z0-9_]+$/', "Solo se permiten letras, números y guiones bajos");

try {
    $usernameValidator->validate("user1"); // ✅ Válido
} catch (Exception $e) {
    echo $e->getMessage();
}
```

## Validaciones Disponibles

### Tipos Básicos

| Método                 | Descripción       | Ejemplo                                   |
| ---------------------- | ----------------- | ----------------------------------------- |
| `isRequired($message)` | Campo obligatorio | `->isRequired("Este campo es requerido")` |
| `isString($message)`   | Debe ser string   | `->isString("Debe ser texto")`            |
| `isNumber($message)`   | Debe ser numérico | `->isNumber("Debe ser un número")`        |
| `isBoolean($message)`  | Debe ser booleano | `->isBoolean("Debe ser true/false")`      |

### Validaciones Específicas

| Método                        | Descripción           | Ejemplo                                       |
| ----------------------------- | --------------------- | --------------------------------------------- |
| `isEmail($message)`           | Email válido          | `->isEmail("Email inválido")`                 |
| `isDate($message)`            | Fecha válida          | `->isDate("Fecha inválida")`                  |
| `isRegex($pattern, $message)` | Coincide con regex    | `->isRegex('/^\d+$/', "Solo números")`        |
| `isEnum($values, $message)`   | Valor dentro del enum | `->isEnum(['S', 'M', 'L'], "Talla inválida")` |

### Validaciones Numéricas

| Método                        | Descripción     | Ejemplo                                      |
| ----------------------------- | --------------- | -------------------------------------------- |
| `isMin($min, $message)`       | Valor mínimo    | `->isMin(0, "Debe ser positivo")`            |
| `isMax($max, $message)`       | Valor máximo    | `->isMax(100, "Máximo 100")`                 |
| `isEqual($value, $message)`   | Igual a valor   | `->isEqual(42, "Debe ser 42")`               |
| `isLength($length, $message)` | Longitud exacta | `->isLength(10, "Debe tener 10 caracteres")` |

## Validaciones Complejas

### Validación de Arrays

```php
// Validar array de números
$numbersValidator = FValidator("numeros")
    ->isArray(
        FValidator()->isNumber()->isMin(1, "Debe ser mayor que 1"),
        "Debe ser un array"
    );

try {
    $numbersValidator->validate([1, 2, 3, 4]); // ✅ Válido
} catch (Exception $e) {
    echo $e->getMessage();
}
```

### Validación de Objetos

```php
// Schema para validar un usuario
$userSchema = [
    'name' => FValidator()->isRequired()->isString(),
    'email' => FValidator()->isRequired()->isEmail(),
    'age' => FValidator()->isRequired()->isNumber()->isMin(18)
];

$userValidator = FValidator("usuario")
    ->isObject($userSchema, "Debe ser un objeto válido");

$userData = [
    'name' => 'Juan Pérez',
    'email' => 'juan@ejemplo.com',
    'age' => 30
];

try {
    $userValidator->validate($userData); // ✅ Válido
} catch (Exception $e) {
    echo $e->getMessage();
}
```

### Validación de Arrays de Objetos

```php
// Validar array de usuarios
$userSchema = [
    'name' => FValidator()->isRequired()->isString(),
    'email' => FValidator()->isRequired()->isEmail()
];

$usersValidator = FValidator("usuarios")
    ->isArray(
        FValidator()->isObject($userSchema),
        "Debe ser un array de usuarios"
    );

$users = [
    ['name' => 'Juan', 'email' => 'juan@test.com'],
    ['name' => 'María', 'email' => 'maria@test.com']
];

try {
    $usersValidator->validate($users); // ✅ Válido
} catch (Exception $e) {
    echo $e->getMessage();
}
```

## Ejemplos Prácticos

### Validación de Formulario de Registro

```php
function validateRegistrationForm($data) {
    $validator = FValidator("Formuario")->isObject([
        'username' => FValidator("nombre de usuario")
            ->isRequired()
            ->isString()
            ->isRegex('/^[a-zA-Z0-9_]{3,20}$/', "3-20 caracteres, solo letras, números y _"),

        'email' => FValidator("email")
            ->isRequired()
            ->isEmail(),

        'password' => FValidator("contraseña")
            ->isRequired()
            ->isString()
            ->isRegex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/',
                      "Mínimo 8 caracteres, incluye mayúscula, minúscula y número"),

        'age' => FValidator("edad")
            ->isRequired()
            ->isNumber()
            ->isMin(13, "Debe ser mayor de 13 años")
            ->isMax(120, "Edad inválida"),

        'gender' => FValidator("género")
            ->isEnum(['M', 'F', 'O'], "Género no válido")
    ]);
            
    return $validator->validate($value);
}

// Uso
$formData = [
    'username' => 'juan123',
    'email' => 'juan@ejemplo.com',
    'password' => 'MiPassword123',
    'age' => 25,
    'gender' => 'M'
];

$result = validateRegistrationForm($formData);
if ($result === true) {
    echo "Formulario válido!";
} else {
    print_r($result); // Array con errores
}
```

### Validación de API Response

```php
function validateApiResponse($response) {
    $schema = [
        'status' => FValidator()->isRequired()->isEnum(['success', 'error']),
        'data' => FValidator()->isRequired()->isArray(
            FValidator()->isObject([
                'id' => FValidator()->isRequired()->isNumber(),
                'title' => FValidator()->isRequired()->isString(),
                'published_at' => FValidator()->isRequired()->isDate()
            ])
        ),
        'meta' => FValidator()->isObject([
            'total' => FValidator()->isNumber(),
            'page' => FValidator()->isNumber()->isMin(1)
        ])
    ];

    $validator = FValidator("response")->isObject($schema);

    try {
        $validator->validate($response);
        return true;
    } catch (Exception $e) {
        throw new Exception("Invalid API response: " . $e->getMessage());
    }
}
```

## API Reference

### Constructor

```php
FValidator(?string $name = null)
```

Crea una nueva instancia del validador con un nombre opcional para el campo.

### Métodos de Configuración

- `setName(string $name): self` - Establece el nombre del campo
- `setData($data): self` - Establece los datos a validar

### Método de Validación

- `validate($data): bool` - Ejecuta todas las validaciones. Retorna `true` si es válido, lanza `Exception` si no.

## Manejo de Errores

La librería lanza excepciones cuando las validaciones fallan. Siempre usa bloques `try-catch`:

```php
try {
    $validator->validate($data);
    // Procesamiento exitoso
} catch (Exception $e) {
    // Manejar error
    error_log("Validation error: " . $e->getMessage());
    // Mostrar mensaje al usuario
}
```

## Contribuir

Las contribuciones son bienvenidas! Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/nueva-validacion`)
3. Commit tus cambios (`git commit -m 'Agregar nueva validación'`)
4. Push a la rama (`git push origin feature/nueva-validacion`)
5. Abre un Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT.

## Autor

**Francisco Blanco JN**

- GitHub: [@franciscoblancojn](https://github.com/franciscoblancojn)

## Changelog

### v1.0.0

- ✨ Primera versión estable
- 🎯 Validaciones básicas (string, number, boolean, array, object)
- 📧 Validaciones específicas (email, date, regex, enum)
- 🔧 Validaciones numéricas (min, max, equal, length)
- 🏗️ Soporte para esquemas complejos

---

¿Encontraste un bug o tienes una sugerencia? [Abre un issue](https://github.com/franciscoblancojn/validator/issues) en GitHub.
