{% extends 'base.tpl' %}

{% block title %}
    {{ 'Packages'|title }}
{% endblock %}

{% block head %}
    {% parent %}
    <script type="text/javascript" src="/aflexi/javascript/package-list.js"></script>
{% endblock %}

{% block breadcrumbs %}
    &nbsp;&gt;&gt;&nbsp;
    <a href="#" class="active">CDN Packages</a>
{% endblock %}

{% block content %}

    {% if (feature_packages.unsynced|length) > 0 %}
    <div id="dialog-error" class="dialog">
        <form id="afx-form-syncnow" action="/aflexi/index.php?module=settings&controller=sync" method="post">
            <button type="submit" name="submit" value="1">Sync now!</button>
            <input type=hidden name="sync-type" value="1"/>
                <span>
                   We have detected that you have <strong>{{ feature_packages.unsynced|length }} unsynchronized
                   cPanel packages</strong> with Aflexi CDN. CDN operations may be affected if these packages
                   are not created or updated in Aflexi.
                </span>
        </form>
    </div>
    {% endif %}
    
    <p>
        To enable CDN features to <a href="/aflexi/index.php?module=settings&controller=user">your users</a>, 
        <a href="/scripts/editpkg">packages</a> associated with Content-Delivery-Network-enabled (CDN) 
        <a href="/scripts2/featuremanager">feature lists</a> have to be synchronized with packages in Aflexi.
    </p>
    <p>
        You currently have <strong>{{ (feature_packages.synced|length) + (feature_packages.unsynced|length) }} feature lists with CDN enabled</strong> and {{ feature_packages.unqualified|length }} without.
        Use the form below to enable them or click on their links to edit their settings.
    </p>
    <p>
        <strong>You may click on the package to edit the Aflexi CDN package setting.</strong>
    </p>
    
    <form id="afx-form-packagelist" action="" method="post">
        <fieldset>
            <ol class="field field-rows">
                {% for feature_package_type, feature_packages in feature_packages %}
                    {% for feature_name, packages in feature_packages %}
                        <li>
                            <ol class="field-rows-columns cp-feature-{{ feature_package_type }}">
                                <li>
                                   {% if feature_package_type == 'unqualified' %}
                                       <input type="checkbox" name="feature-lists[]" value="{{ feature_name|url_encode }}"/>
                                   {% else %}
                                        <!--
                                            NOTE [yclian 20100721]
                                            I know this is bad. Otherwise, the CSS background can't be shown
                                        -->
                                        &nbsp;
                                   {% endif %}
                                   <span>{{ feature_package_type }}</span>
                                </li>
                                <li>
                                    <span>
                                        <a href="/scripts2/dofeaturemanager?action=editfeature&feature={{ feature_name|url_encode }}">{{ feature_name }}</a>
                                        {% if feature_package_type == 'synced' %}
                                            is synced
                                        {% elseif feature_package_type == 'unsynced' %}
                                            is not synced yet
                                        {% else %}
                                            doesn't support CDN
                                        {% endif %}
                                    </span>
                                </li>
                                <li>
                                    <ol>
                                        {# This is when I love Twig #}
                                        {% for package_name in packages|keys|sort %}
                                            <li>
                                                {# TODO [yclian 20100728] This is not the ideal way to display unsynced packages. 
                                                                          They shall be handled individually than deactivated by 
                                                                          feature?
                                                #}
                                                {% if feature_package_type == 'synced' %}
                                                    <a href="/aflexi/index.php?module=settings&controller=package&action=edit&id={{ afxPackages[package_name]['id'] }}">{{ package_name }}</a>
                                                {% else %}
                                                    <!-- a href="/scripts/editpkg?pkg={{ package_name|url_encode }}"-->
                                                        {{ package_name }}
                                                    <!--/a-->
                                                {% endif %}
                                            </li>
                                        {% endfor %}
                                    </ol>
                                </li>
                            </ol>
                        </li>
                    {% endfor %}
                {% endfor %}
            </ol>
       </fieldset>
        <hr/>
        <button type="submit" name="submit" value="1">Enable CDN</button>
    </form>
{% endblock %}
