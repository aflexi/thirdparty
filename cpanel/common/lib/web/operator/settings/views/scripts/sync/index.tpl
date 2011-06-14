{% extends 'base.tpl' %}

{% block title %}
    {{ 'Sync'|title }}
{% endblock %}

{% block head %}
    {% parent %}
    <!--script type="text/javascript" src="/aflexi/javascript/sync.js"></script-->
{% endblock %}

{% block breadcrumbs %}
    &nbsp;&gt;&gt;&nbsp;
    <a href="#" class="active">Synchronize and Repair</a>
{% endblock %}

{% block content %}

    {% if sync_results %}
        {% set info %}
            <strong>Sync completed:</strong> 
            {% for sync_target, sync_results_single in sync_results %}
                {% if (sync_results_single.unsynced_created + sync_results_single.updated + sync_results_single.unsynced_unsuspended +  sync_results_single.unsynced_suspended + sync_results_single.unsynced_deleted ) > 0 %}
                    {{ sync_results_single.unsynced_created }} {{ sync_target }}  created, {{ sync_results_single.updated + sync_results_single.unsynced_unsuspended + sync_results_single.unsynced_suspended }} updated, {{ sync_results_single.unsynced_deleted }} deleted.
                {% endif %}
            {% endfor %}
        {% endset %}
        {{ ux.dialog_info(info) }}
    {% endif %}

    <p>If your packages or users are not correctly synchronized, using this 
       page can force an immediate synchronization to repair or update the 
       integration to the latest state.
   </p>
    
    <form id="afx-form-sync" action="{{ form_action }}" method="post">
        <fieldset>
            <ol class="field">
                <li>
                    <ol class="field-radios">
                        <li><input type="radio" name="sync-type" value="1"/><span>Sync packages</span></li>
                        <li><input type="radio" name="sync-type" value="2"/><span>Sync users</span></li>
                        <li><input type="radio" name="sync-type" value="3" checked="checked"/><span>Sync packages + users</span></li>
                    </ol>
                </li>
            </ol>
        </fieldset>
        <hr/>
        <button type="submit" name="submit" value="1">Sync Now!</button>
    </form>
{% endblock %}