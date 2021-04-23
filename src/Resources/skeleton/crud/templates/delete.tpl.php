{% extends 'admin.html.twig' %}

{% block page_title %}

    {{ 'crud.title.delete'|trans({'%entity_class_name%': '<?= $title_singular; ?>'}, 'MicayaelAdminLteMakerBundle') }}

{% endblock %}

{% block breadcrumb %}

    {% embed '@MicayaelAdminLteMaker/Widgets/breadcrumb.html.twig' %}

        {% block content %}
            <li><a href="{{ path('<?= $route_name; ?>_index') }}"><?= $title_plural; ?></a></li>
            <li class="active">{{ <?= $entity_twig_var_singular; ?> }}: {{ 'crud.action.delete'|trans({}, 'MicayaelAdminLteMakerBundle') }}</li>
        {% endblock %}

    {% endembed %}

{% endblock %}

{% block page_content %}

    <div class="row">
        <div class="col-md-12">

            {% embed '@MicayaelAdminLteMaker/Widgets/context_menu.html.twig' %}

                {% block brand %}
                    {{ <?= $entity_twig_var_singular; ?> }}
                {% endblock %}

                {% block actions %}
                    <li>
                        {{ create_link('show', '<?= $route_name; ?>_show', {'<?= $entity_identifier; ?>': <?= $entity_twig_var_singular; ?>.<?= $entity_identifier; ?>}, 'ROLE_<?= $entity_class_name_upper; ?>_READ') }}
                    </li>
                    <li>
                        {{ create_link('edit', '<?= $route_name; ?>_edit', {'<?= $entity_identifier; ?>': <?= $entity_twig_var_singular; ?>.<?= $entity_identifier; ?>}, 'ROLE_<?= $entity_class_name_upper; ?>_UPDATE') }}
                    </li>
                    <li>
                        {{ create_link('index', '<?= $route_name; ?>_index', {}, 'ROLE_<?= $entity_class_name_upper; ?>_READ') }}
                    </li>
                {% endblock %}

            {% endembed %}

        </div>
    </div>

    <div class="row crud crud-delete">
        <div class="col-md-12">

            {% embed '@AdminLTE/Widgets/box-widget.html.twig' with { 'boxtype': 'danger' } %}

                {% block box_title %}
                    {{ 'crud.delete.are_you_sure'|trans({}, 'MicayaelAdminLteMakerBundle') }}
                {% endblock %}

                {% block box_body %}

                    {{ include('<?= $templates_path; ?>/_show_data.html.twig') }}

                {% endblock %}

                {% block box_footer %}

                    <span class="pull-right">
                        {{ include('<?= $templates_path; ?>/_delete_form.html.twig') }}
                    </span>

                {% endblock %}

            {% endembed %}

        </div>
    </div>

{% endblock %}
