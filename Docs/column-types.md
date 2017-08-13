Column types
============

When adding columns there are a couple of different types you can use.

PropertyType
------------
FQCN: `Sjdeboer\DataTableBundle\ColumnType\PropertyType`

Probably the simplest of the column types. This type builds the content of the table cell `<td>` based on a property of the data for that row.

### Type specific options

#### property
required: `yes` type: `string`

This options expects a string representing the name of the property of the result entity, or the key of the result array to use.

Below is an example:

```php
$builder->add(PropertyType::class, [
    'label' => 'Firstname',
    'property' => 'firstName',
]);
```

ClosureType
-----------
FQCN: `Sjdeboer\DataTableBundle\ColumnType\ClosureType`

This type builds the content of the table cell `<td>` based on a closure.
This is useful when combining multiple properties.

### Type specific options

#### closure
required: `yes` type: `callable`

This option expects a closure that returns a string. The closure should have one parameter, namely the data of the current row.
Depending on the query builder, this will be a Doctrine Entity or an `array`.

Below is an example:

```php
$builder->add(ClosureType::class, [
    'label' => 'Name',
    'closure' => function(Person $entity) {
        return $entity->getFirstName() . ' ' . $entity->getLastName();
    },
]);
```

ActionType
----------
FQCN: `Sjdeboer\DataTableBundle\ColumnType\ActionType`

This type is used to build a column with a list of actions (buttons or links for editing, removing etc.) per row.

### Type specific options

#### closure
required: `yes` type: `callable`

This option expects a closure that returns a multidimensional array. The closure should have two parameters:

1. type: Doctrine Entity of type used in data_class option or `array`. Contains the data of the current row. Depending on the query builder, this will be a Doctrine Entity or an `array`.
2. type: `Router` [Symfony Router](https://symfony.com/doc/current/components/routing.html) object. This can be used to create url's for your routes.

The return value should be a multidimensional array containing action groups (rendered as a block `<div>`) which in turn contain actions (rendered as a hyperlink `<a>`)

Below is an example containing all available options:

```php
$builder->add(ActionType::class, [
    'closure' => function(Person $entity, Router $router) {
        return [
            // An action group.
            [
                // Custom atrributes for the action group <div>.
                'attr' => [
                    'class' => 'btn-group',
                ],
                // Array containing actions.
                'actions' => [
                    [
                        'label' => 'Edit', // Text label for the action, used as the content of the <a> -tag when icon is ommitted, will be used as the content of the title -attribute otherwise.
                        'icon' => 'glyphicon glyphicon-pencil', // Classname for an icon, wil be rendered as <i class="-classname-"></i>.
                        // Custom atrributes for the action <a>.
                        'attr' => [
                            'href' => $router->generate('person_edit', ['id' => $entity->getId()]),
                            'class' => 'btn btn-xs btn-default',
                        ],
                    ],
                    [
                        'label' => 'Remove',
                        'icon' => 'glyphicon glyphicon-trash',
                        'attr' => [
                            'href' => $router->generate('person_delete', ['id' => $entity->getId()]),
                            'class' => 'btn btn-xs btn-danger',
                        ],
                    ],
                    // You could continue adding actions here.
                ],
            ],
            // You could continue adding action groups here.
        ];
    },
]);
```

Non type specific options
-------------------------

The below options can be used with all ColumnTypes

#### label
required: `no` type: `string`

A string that will be used as the header for the column.

#### column_options
required: `no` type: `array`

An array containing column specific options for Datatables for jQuery.
See https://datatables.net/reference/option/columns for available options.