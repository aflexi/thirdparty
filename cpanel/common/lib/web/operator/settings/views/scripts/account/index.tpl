{% extends 'base.tpl' %}

{% block title %}
    {{ 'Set-Up'|title }}
{% endblock %}

{% block head %}
    {% parent %}
{% endblock %}

{% block breadcrumbs %}
    &nbsp;&gt;&gt;&nbsp;
    <a href="#" class="active">Set-Up</a>
{% endblock %}

{% block content %}
    {% import 'macros/forms.xml' as forms %}
    {% if first_time %}
        <p>Hello {{ user }}! Thanks for installing the Aflexi CDN cPanel Plugin. You will have to complete the following form in order to start using this plugin.</p>
    {% endif %}
    <form id="afx-form-setup" action="{{ form_action }}" method="post">
        <fieldset>
            <legend>Plugin Configurations</legend>
            <ol>
                {% set id = 'username' %}
                {% set tip =  'Your Aflexi CDN user-name used for signing in.' %}
                {% set input %}
                    {{ forms.input(id, 'text', ['value': params.username], tip) }}
                {% endset %}
                {{ forms.row(id, 'Username', input, true, tip) }}

                {% set id = 'auth_key' %}
                {% set tip = 'Authentication key used for integrating cPanel to Aflexi. Can be generated via <a href="' ~ portal ~ '/security/list" target="_new">Tools &gt; AuthKey Generator</a>.' %}
                {% set input %}
                    {{ forms.input(id, 'text', ['value': params.auth_key], tip) }}
                {% endset %}
                {{ forms.row(id, 'Authentication Key', input, true, tip) }}

                {% set id = 'whm_host' %}
                {% set tip = 'Your own WHM URL.' %}
                {% set input %}
                    {{ forms.input(id, 'text', ['value': params.whm_host], tip) }}
                {% endset %}
                {{ forms.row(id, 'WebHost Manager URL', input, true, tip) }}

                {% set id = 'cpanel_host' %}
                {% set tip = 'Your own CPanel URL.' %}
                {% set input %}
                    {{ forms.input(id, 'text', ['value': params.cpanel_host], tip) }}
                {% endset %}
                {{ forms.row(id, 'cPanel URL', input, true, tip) }}

                {% set id = 'shared_package' %}
                {% set tip = 'Enable to allow all users to have CDN accounts automatically regardless WHM feature list.' %}
                {% set input %}
                    {{ forms.input(id, 'checkbox', ['value': 'enabled', 'checked': params.package], tip) }}
                {% endset %}
                {{ forms.row(id, 'Synchronize All Users', input, true, tip) }}

            </ol>
        </fieldset>
        <fieldset>
                <legend>Advanced Configuration</legend>
                <ol>

                    {% set id = 'cpanel_cname' %}
                    {% set tip = 'CNAME will be auto created if the domain of the CDN resource is existed in "Advance DNS Zone" in cPanel.' %}
                    {% set input %}
                        {{ forms.input(id, 'radio', ['value': 'enabled', 'checked': params.cname.enabled], tip) }}
                    {% endset %}
                    {{ forms.row(id, 'Enable auto CNAME', input, true, tip) }}

                    {% set input %}
                    {% set tip = 'CNAME will be auto created if  (1) the domain of the CDN resource is existed in "Advance DNS Zone" in cPanel (2) the NS of the domain and cPanel server are the same.' %}
                        {{ forms.input(id, 'radio', ['value': 'conditional', 'checked': params.cname.conditional], tip) }}
                    {% endset %}
                    {{ forms.row(id, 'Enable auto CNAME with conditional', input, true, tip) }}

                    {% set input %}
                    {% set tip = 'CNAME will not be created upon resource creation.' %}
                        {{ forms.input(id, 'radio', ['value': 'disabled', 'checked': params.cname.disabled], tip) }}
                    {% endset %}
                    {{ forms.row(id, ' Disable auto CNAME', input, true, tip) }}

                </ol>
        </fieldset>
        <hr/>
        <button type="submit" name="submit" value="1">Save Settings</button>
    </form>
{% endblock %}
