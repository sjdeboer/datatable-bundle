Options
=======

#### data_class
required: `yes` type: `string` default: `null`

Fully Qualified Class Name of the doctrine entity the table should be build from.

#### id
required: `no` type: `string`

An identifier for the table. Should be used when multiple tables are shown on the same page.

#### query_builder
required: `no` type: `callable`

This option can be used to change the results shown in the table. It expects a closure that
returns a `QueryBuilder`. The closure should have three parameters:

1. type: `EntityRepository` The repository. Should be used to create the query builder.
2. type: `array` Current order. This array contains information on how the data should be ordered.
3. type: `array` Current search query. This array contains the current search query.

Below is an example:

```php
[
    'query_builder' => function(EntityRepository $repo, array $order = null, array $search = null) {
        $qb = $repo->createQueryBuilder('p');

        if (is_array($order)) {
            $fields = [
                0 => 'p.lastName'
                1 => 'p.firstName'
            ];
            foreach ($order as $row) {
                if (!is_array($row) || !array_key_exists('column', $row) || !array_key_exists('dir', $row)) {
                    continue;
                }
                $qb->addOrderBy($fields[$row['column']], $row['dir']);
            }
        }

        if ($search !== null && array_key_exists('value', $search)) {
            $qb->andWhere('p.lastName LIKE :query OR p.firstName LIKE :query')->setParameter('query', '%' . $search['value'] . '%');
        }

        return $qb;
    },
],
```

#### datatables_options
required: `no` type: `array`

This option can be used to set or override the default options for the Datatables for jquery script.
For available options, see: https://datatables.net/reference/option/

#### row_id
required: `no` type: `callable`

This option can be used to set an id -attribute on the rows (`<tr>`) in your table, based on the data for that row.
It expects a closure that returns a string. The closure should have one parameter, namely the data of the current row.
Depending on the query builder, this will be a Doctrine Entity or an `array`.

Below is an example:

```php
[
    'row_id' => function(Person $entity) {
        return 'row_' . $entity->getId();
    },
],
```

#### row_class
required: `no` type: `callable`

This option can be used to set a class -attribute on the rows (`<tr>`) in your table, based on the data for that row.
It expects a closure that returns a string. The closure should have one parameter, namely the data of the current row.
Depending on the query builder, this will be a Doctrine Entity or an `array`.

Below is an example:

```php
[
    'row_class' => function(Person $entity) {
        return $entity->getDebt() > 10000 ? 'danger' : '';
    },
],
```

#### row_attr
required: `no` type: `callable`

This option can be used to set custom attributes on the rows (`<tr>`) in your table, based on the data for that row.
It expects a closure that returns an array with named keys. The closure should have one parameter, namely the data of the current row.
Depending on the query builder, this will be a Doctrine Entity or an `array`.

Below is an example:

```php
[
    'row_attr' => function(Person $entity) {
        return [
            'data-lastname' => $entity->getLastName(),
            'data-email' => $entity->getEmail(),
        ];
    },
],
```

#### row_data
required: `no` type: `callable`

This option can be used to set [jQuery data](https://api.jquery.com/data/) attributes on the rows (`<tr>`) in your table, based on the data for that row.
It expects a closure that returns an array with named keys. The closure should have one parameter, namely the data of the current row.
Depending on the query builder, this will be a Doctrine Entity or an `array`.

Below is an example:

```php
[
    'row_data' => function(Person $entity) {
        return [
            'lastname' => $entity->getLastName(),
            'email' => $entity->getEmail(),
        ];
    },
],
```

#### table_class
required: `no` type: `string` default: `''`

This option can be used to set the `class` -attribute value for the `<table>` element. Example when using Bootstrap:

```php
[
    'table_class' => 'table table-striped',
],
```