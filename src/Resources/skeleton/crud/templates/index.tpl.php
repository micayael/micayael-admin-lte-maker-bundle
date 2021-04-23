{% extends 'admin.html.twig' %}

{% block page_title %}

    {{ 'crud.title.index'|trans({'%entity_class_name_plural%': '<?= $title_plural; ?>'}, 'MicayaelAdminLteMakerBundle') }}

{% endblock %}

{% block breadcrumb %}

    {% embed '@MicayaelAdminLteMaker/Widgets/breadcrumb.html.twig' %}

        {% block content %}
        <li class="active"><?= $title_plural; ?></li>
        {% endblock %}

    {% endembed %}

{% endblock %}

{% block page_content %}

    <div class="row">
        <div class="col-md-12">

            {% embed '@MicayaelAdminLteMaker/Widgets/context_menu.html.twig' %}

                {% block actions %}
                    <li>
                        {{ create_link('new', '<?= $route_name; ?>_new', {}, 'ROLE_<?= $entity_class_name_upper; ?>_CREATE') }}
                    </li>
                {% endblock %}

            {% endembed %}

        </div>
    </div>

    <div class="row crud crud-index">
        <div class="col-md-12">

            {% embed '@AdminLTE/Widgets/box-widget.html.twig' %}

                {% block box_body %}

                    <div class="table-responsive">

                        <table class="table">
                            <thead>
                                <tr>
<?php $fields = 0; ?>
<?php foreach ($entity_display_fields as $field): ?>
<?php if (in_array($field['fieldName'], ['id', 'revision'])) {
    continue;
} ?>
<?php ++$fields; ?>
<?php if (in_array($field['type'], ['datetime_immutable', 'datetime', 'date_immutable', 'date', 'time_immutable', 'time', 'boolean'])): ?>
                                    <th class="text-center">{{ knp_pagination_sortable(pagination, '<?= ucfirst($field['fieldName']); ?>', '<?= $sql_alias; ?>.<?= $field['fieldName']; ?>') }}</th>
<?php else: ?>
                                    <th>{{ knp_pagination_sortable(pagination, '<?= ucfirst($field['fieldName']); ?>', '<?= $sql_alias; ?>.<?= $field['fieldName']; ?>') }}</th>
<?php endif; ?>
<?php endforeach; ?>
<?php foreach ($many_to_one_fields as $fieldName => $metadata): ?>
<?php ++$fields; ?>
                                    <th><?= ucfirst($fieldName); ?></th>
<?php endforeach; ?>
                                    <th class="text-center">{{ 'crud.list.actions'|trans({}, 'MicayaelAdminLteMakerBundle') }}</th>
                                </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <td colspan="<?= $fields + 1; ?>">

                                    {% include '@MicayaelAdminLteMaker/Partials/paginator.html.twig' %}

                                </td>
                            </tr>
                            </tfoot>
                            <tbody>

                            {% for <?= $entity_twig_var_singular; ?> in pagination %}
                                <tr>
<?php foreach ($entity_display_fields as $field): ?>
<?php if (in_array($field['fieldName'], ['id', 'revision'])) {
    continue;
} ?>
<?php if ('boolean' === $field['type']): ?>
                                    <td class="text-center">{{ boolean_value(<?= $entity_twig_var_singular; ?>.<?= $field['fieldName']; ?>) }}</td>
<?php elseif (in_array($field['type'], ['datetime_immutable', 'datetime'])): ?>
                                    <td class="text-center">{{ <?= $entity_twig_var_singular; ?>.<?= $field['fieldName']; ?>|date('d-m-Y H:i:s') }}</td>
<?php elseif (in_array($field['type'], ['date_immutable', 'date'])): ?>
                                    <td class="text-center">{{ <?= $entity_twig_var_singular; ?>.<?= $field['fieldName']; ?>|date('d-m-Y') }}</td>
<?php elseif (in_array($field['type'], ['time_immutable', 'time'])): ?>
                                    <td class="text-center">{{ <?= $entity_twig_var_singular; ?>.<?= $field['fieldName']; ?>|date('H:i:s') }}</td>
<?php else: ?>
                                    <td>{{ <?= $helper->getEntityFieldPrintCode($entity_twig_var_singular, $field); ?> }}</td>
<?php endif; ?>
<?php endforeach; ?>
<?php foreach ($many_to_one_fields as $fieldName => $metadata): ?>
                                    <td>{{ <?= $entity_twig_var_singular; ?>.<?= $fieldName; ?> }}</td>
<?php endforeach; ?>
                                    <td class="text-center">
                                        {{ <?= $entity_twig_var_singular; ?>_actions(<?= $entity_twig_var_singular; ?>) }}
                                    </td>
                                </tr>
                            {% else %}
                                <tr>
                                    <td colspan="<?= $fields + 1; ?>">{{ 'crud.list.no_records_found'|trans({}, 'MicayaelAdminLteMakerBundle') }}</td>
                                </tr>
                            {% endfor %}
                            </tbody>
                        </table>

                    </div>

                {% endblock %}

            {% endembed %}

        </div>
    </div>

{% endblock %}
