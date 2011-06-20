{% extends 'base.tpl' %}

{% block title %}
    {{ 'Users'|title }}
{% endblock %}

{% block head %}
    {% parent %}
    <!-- script type="text/javascript" src="/aflexi/javascript/user-list.js"></script -->
{% endblock %}

{% block breadcrumbs %}
    &nbsp;&gt;&gt;&nbsp;
    <a href="#" class="active">CDN Users</a>
{% endblock %}

{% block content %}

    {% if  users.unsynced_create|length + users.unsynced_unsuspend|length > 0 %}
    <div id="dialog-error" class="dialog">
        <form id="afx-form-syncnow" action="/aflexi/index.php?module=settings&controller=sync" method="post">
               <button type="submit" name="submit" value="1">Sync now!</button>
                <input type=hidden name="sync-type" value="2"/>
               <span>
                   We have detected that you have <strong>{{users.unsynced_create|length + users.unsynced_unsuspend|length  }}  unsynchronized
                   cPanel users</strong> with Aflexi CDN. CDN operations may be affected if these users 
                   are not created or updated in Aflexi.
               </span>
        </form>
    </div>
    {% endif %}
    
    <p>The list below shows users with CDN access. In order to grant them access, please <a href="/aflexi/index.php?module=settings&controller=package">manage the features and packages via this page</a>.</p>

    <form id="afx-form-userlist" action="{{ form_action }}" method="get">
        <fieldset>
            <ol class="field field-rows">
                {% for user_type, users in users %}
                    {% for user_name, user in users %}
                        {% set package_name = user.cpanel.PLAN %}
                        {% set feature_name = user.cpanel.FEATURELIST %}
                        <li>
                            <ol class="field-rows-columns cp-user-{{ user_type }}">
                                <li>
                                    &nbsp;
                                    <span>{{ user_type }}</span>
                                </li>
                                <li>
                                    <span>
                                        <a href="/scripts/edituser?user={{ user_name|url_encode }}">{{ user_name }}</a>
                                        {% if user_type == 'synced' %}
                                            has CDN access
                                        {% elseif user_type == 'unsynced_create' %}
                                            needs to be synced
                                        {% elseif user_type == 'unsynced_unsuspend' %}
                                            has been suspended from portal
                                        {% elseif user_type == 'unsynced_suspend' %}
                                            has been suspended from cpanel
                                        {% elseif user_type == 'unsynced_deleted' %}
                                            has been deleted from cpanel
                                        {% else %}
                                            doesn't have CDN access
                                        {% endif %}
                                    </span>
                                </li>
                                <li>
                                    <span>
                                        {{ package_name }}
                                        @
                                        <a href="/scripts2/dofeaturemanager?action=editfeature&feature={{ feature_name|url_encode }}">{{ feature_name }}</a>
                                    </span>
                                </li>
                            </ol>
                        </li>
                        {#
                        {% set id = 'users['~user_name~']' %}
                        {% set feature_set_name = user.FEATURELIST %}
                        {% set labelext %}
                            {% if not users_ext[user_name] %}<em>(not in Aflexi)</em>{% endif %}
                            <ul class="control">
                                <li><a href="/scripts/edituser?user={{ user_name|url_encode }}">cPanel's Account</a></li>
                                <li>
                                    {% if users_ext[user_name] %}
                                        {-# TODO [yclian 20100614] Fill in exact publisher link URL #-}
                                        <a href="{{ config['url']['portal'] }}/publisherLink" target="_new">CDN Settings</a>
                                    {% else %}
                                        <s>CDN Settings</s>
                                    {% endif %}
                                </li>
                            </ul>
                        {% endset %}
                        {% set inputext %}
                            On <a href="/scripts/editpkg?pkg={{ package_name|url_encode }}">{{ package_name }}</a> with 
                               <a href="/scripts2/dofeaturemanager?action=editfeature&feature={{ feature_set_name|url_encode }}">{{ feature_set_name }}</a>
                        {% endset %}
                        {{ forms.row(id, user_name, '', false, '', labelext, inputext) }}
                        #}
                    {% endfor %}
                {% endfor %}
            </ol>
        </fieldset>
        <hr/>
        <button type="submit" name="refresh" value="1">Refresh</button>
    </form>
{% endblock %}