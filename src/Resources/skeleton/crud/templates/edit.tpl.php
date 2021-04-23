{% extends 'admin.html.twig' %}

{% block page_title %}

    {{ 'crud.title.edit'|trans({'%entity_class_name%': '<?= $title_singular; ?>'}, 'MicayaelAdminLteMakerBundle') }}

{% endblock %}

{% block breadcrumb %}

    {% embed '@MicayaelAdminLteMaker/Widgets/breadcrumb.html.twig' %}

        {% block content %}
            <li><a href="{{ path('<?= $route_name; ?>_index') }}"><?= $title_plural; ?></a></li>
            <li class="active">{{ <?= $entity_twig_var_singular; ?> }}: {{ 'crud.action.edit'|trans({}, 'MicayaelAdminLteMakerBundle') }}</li>
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
                        {{ create_link('new', '<?= $route_name; ?>_new', {}, 'ROLE_<?= $entity_class_name_upper; ?>_CREATE') }}
                    </li>
                    <li>
                        {{ create_link('show', '<?= $route_name; ?>_show', {'<?= $entity_identifier; ?>': <?= $entity_twig_var_singular; ?>.<?= $entity_identifier; ?>}, 'ROLE_<?= $entity_class_name_upper; ?>_READ') }}
                    </li>
                    <li>
                        {{ create_link('index', '<?= $route_name; ?>_index', {}, 'ROLE_<?= $entity_class_name_upper; ?>_READ') }}
                    </li>
                {% endblock %}

            {% endembed %}

        </div>
    </div>

    <div class="row crud crud-edit crud-form">
        <div class="col-md-12">

            {{ form_start(form) }}

                {{ form_errors(form) }}

                {% embed '@AdminLTE/Widgets/box-widget.html.twig' %}

                    {% block box_body %}
<?php foreach ($entity_form_fields as $fieldName => $options): ?>
<?php if (in_array($fieldName, ['id', 'revision', 'saveAndEdit', 'saveAndList'])): ?>
<?php continue; ?>
<?php endif; ?>
                        {{ form_row(form.<?= $fieldName; ?>) }}
<?php endforeach; ?>

                    {% endblock %}

                    {% block box_footer %}

                        <div class="mandatory_fields_message">{{ 'crud.msg.mandatory_fields'|trans({}, 'MicayaelAdminLteMakerBundle') }}</div>

                        {{ form_widget(form.saveAndEdit) }}
                        {{ form_widget(form.saveAndList) }}

                        <span class="pull-right">
                            {{ create_button('delete', '<?= $route_name; ?>_delete', {'<?= $entity_identifier; ?>': <?= $entity_twig_var_singular; ?>.<?= $entity_identifier; ?>}, 'ROLE_<?= $entity_class_name_upper; ?>_DELETE') }}
                        </span>

                    {% endblock %}

                {% endembed %}

            {{ form_end(form) }}

        </div>
    </div>

{% endblock %}
