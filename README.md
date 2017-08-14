DataTableBundle
===============

DataTableBundle is a Symfony Bundle that lets you combine [Symfony Framework](https://symsfony.com/) and [Doctrine](http://www.doctrine-project.org/) with [DataTables plug-in for jQuery](https://github.com/DataTables/DataTables).

Installation
------------

Install the bundle into your Symfony project via composer:

`composer require sjdeboer/datatable-bundle`

Register the bundle in _app/AppKernel.php_:

```php
// ...

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Sjdeboer\DataTableBundle\SjdeboerDataTableBundle(),
        );

        // ...
    }
}
```

Optionally, you can add the below configuration to _app/config/config.yml_ and adjust it to your needs.

```yaml
sdeboer_data_table:
    # Default class added to the HTML table element
    default_table_class: ''

    # Default Datatables options. For available options, see: https://datatables.net/reference/option/
    default_datatables_options:
        searching: false
```

You should now be ready to start creating tables!

Usage
-----

Creating tables is very similar to creating forms in Symfony. So chances are you'll feel right at home.s

### Examples

* [Simple table](Docs/Examples/example-simple.md)
* [Table defined as a PHP class](Docs/Examples/example-class.md) (best practice)

### Reference

* [Options](Docs/options.md)
* [Column types](Docs/column-types.md)