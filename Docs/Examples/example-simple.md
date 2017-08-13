Simple Example
==============

Controller
----------

```php
<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Person;
use Sjdeboer\DataTableBundle\ColumnType\PropertyType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

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
            // Create a table builder with the createBuilder method and provide the data_class option with the
            // Fully Qualified Class Name of your entity.
            ->createBuilder([
                'data_class' => Person::class,
            ])
            // Add a column of type ClosureType. You must provide this column type with a closure that
            // receives the entity as the first parameter and should return the contents of the table cell.
            ->add(PropertyType::class, [
                'label' => 'Lastname',
                'property' => 'lastName',
            ])
            // You can continue adding columns to the builder.
            ->add(PropertyType::class, [
                'label' => 'Firstname',
                'property' => 'firstName',
            ])
            // When we are done adding columns, create the DataTable.
            ->getTable()
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