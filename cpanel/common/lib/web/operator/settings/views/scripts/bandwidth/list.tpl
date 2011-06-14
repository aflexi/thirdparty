{% extends 'base.tpl' %}

{% block title %}
    {{ 'CDN Bandwidth Usage'|title }}
{% endblock %}

{% block head %}
    {% parent %}
    <!-- script type="text/javascript" src="/aflexi/javascript/user-list.js"></script -->
{% endblock %}

{% block breadcrumbs %}
    &nbsp;&gt;&gt;&nbsp;
    <a href="#" class="active">CDN Bandwidth Usage</a>
{% endblock %}

{% block content %}
    {% import 'macros/forms.xml' as forms %}

    {% if (users.unsynced|length) > 0 %}
    <div id="dialog-error" class="dialog">
        <form id="afx-form-syncnow" action="/aflexi/index.php?module=settings&controller=sync" method="post">
               <button type="submit" name="submit" value="1">Sync now!</button>
               <span>
                   We have detected that you have <strong>{{ users.unsynced|length }} unsynchronized
                   cPanel users</strong> with Aflexi CDN. CDN operations may be affected if these users 
                   are not created or updated in Aflexi.
               </span>
        </form>
    </div>
    {% endif %}

    <form id="afx-form-userlist" action="{{ form_action }}" method="post">
        <fieldset>
            <ol class="field field-rows">
                <li>
                    <ol class="field-rows-columns" style="font-weight: bold;">
                        <li></li>
                        <li>CPanel Package</li>
                        <li>Users</li>
                        <li>CDN Package</li>
                        <li>CDN Bandwidth</li>
                        <li>cPanel Bandwidth</li>
                    </ol>
                </li>
                {% for user_type, users in users %}
                {% for user_name, user in users %}
                    {% set package_name = user.cpanel.PLAN %}
                    {% set feature_name = user.cpanel.FEATURELIST %}
                    {% set package_id = cdnPackages[package_name]['id'] %}
                    {% set cdn_bandwidth = cdnPackages[package_name]['bandwidthLimit'] %}
                    {% set cpanel_bandwidth = user.cpanel.BWLIMIT %}

                    <li>
                        <ol class="field-rows-columns">
                            <li></li>
                            <li>
                                <span>
                                    {{ package_name }}
                                    @
                                    <a href="/scripts2/dofeaturemanager?action=editfeature&feature={{ feature_name|url_encode }}">
                                        {{ feature_name }}
                                    </a>
                                </span>
                            </li>

                            <li>
                                <span>
                                        <a href="/scripts/edituser?user={{ user_name|url_encode }}">{{ user_name }}</a><br />
                                </span>
                            </li>
                            
                            <li>
                                <span>
                                    {% if user_type != 'unqualified' %}
                                        {% if package_id %}
                                            <a href="/aflexi/index.php?module=settings&controller=package&action=edit&id={{ package_id }}">{{ package_name }}</a>
                                        {% endif %}
                                    {% endif %}
                                </span>
                            </li>
                            <li>
                                <span>
                                    {% if user_type != 'unqualified' %}
                                        {{ userList[user_name]["cdnBandwidthUsage"]/1024 }} GB
                                        /
                                        {% if cdn_bandwidth != 'unlimited' %}
                                             {{ cdn_bandwidth }} GB
                                        {% else %}
                                              Unlimited
                                        {% endif %}
                                    {% endif %}
                                </span>
                            </li>

                            <li>
                                <span>
                                    {{ userList[user_name]["bandwidthUsage"] }} GB /
                                    {% if cpanel_bandwidth != 'unlimited' %}
                                         {{ cpanel_bandwidth }} GB
                                    {% else %}
                                         Unlimited
                                    {% endif %}
                            </li>
                        </ol>
                    </li>
                {% endfor %}
                {% endfor %}
            </ol>
        </fieldset>
        <hr/>
    </form>
{% endblock %}