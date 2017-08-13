Simple Example
==============

Table class
-----------

_AppBundle/DataTable/PersonTable.php_

```php
<?php
namespace AppBundle\DataTable;

use AppBundle\Entity\Person;
use Sjdeboer\DataTableBundle\Builder\TableBuilderInterface;
use Sjdeboer\DataTableBundle\ColumnType\PropertyType;
use Sjdeboer\DataTableBundle\DataTable\AbstractTable;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PersonTable
 * @package AppBundle\DataTable
 */
class PersonTable extends AbstractTable
{
    /**
     * @inheritdoc
     */
    public function buildTable(TableBuilderInterface $builder, array $options)
    {
        // Add a column of type ClosureType. You must provide this column type with a closure that
        // receives the entity as the first parameter and should return the contents of the table cell.
        $builder->add(PropertyType::class, [
            'label' => 'Lastname',
            'property' => 'lastName',
        ])
        // You can continue adding columns to the builder.
        ->add(PropertyType::class, [
            'label' => 'Firstname',
            'property' => 'firstName',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // You must provide the data_class option with the
        // Fully Qualified Class Name of your entity.
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
```

Controller
----------

```php
<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\DataTable\PersonTable;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     * @return JsonResponse|array
     */
    public function indexAction()
    {
        $table = $this->get('sdeboer_data_table.factory')
            // Create a table with the create method and provide it with the Fully Qualified Class Name of your table class.
            ->create(PersonTable::class)
            // And then convert it into something we can use in our output.
            ->createView();

        // If the TableView contains JSON data, it means an ajax call has been done by the
        // datatables plugin for jquery. We should let the controller return this as a JsonResponse.
        if ($table->getJson() !== null) {
            return new JsonResponse($table->getJson());
        }

        return [
            // Add the TableView to your template.
            'table' => $table,
        ];
    }
}
```

Twig template
-------------

```twig
{% extends '::base.html.twig' %}

{% block stylesheets %}
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
{% endblock %}

{% block body %}
    {# Output the html for our table. Remember to use the raw filter, otherwise Twig will escape special characters. #}
    {{ table.body|raw }}
{% endblock %}

{% block javascripts %}
    {# You must load the necessary assets yourself. This way, you have full control over the way it's loaded. See https://datatables.net/ for documentation #}
    <script type="application/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="application/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    {# Output the javascripts for our table. #}
    {{ table.js|raw }}
{% endblock %}
```