{% extends 'base.tpl' %}

{% block title %}
    {{ 'Edit Package'|title }}
{% endblock %}

{% block head %}
    {% parent %}
    <!--script type="text/javascript" src="/aflexi/javascript/package-edit.js"></script-->
{% endblock %}

{% block breadcrumbs %}
    &nbsp;&gt;&gt;&nbsp;
    <a href="#" class="active">Edit Package</a>
{% endblock %}

{% block content %}
{# TODO [yclian 20100804] Make a macro to write this iframe. #}
{# TODO [yclian 20100804] We will have to address timeout issue by appending app context to forms,
    otherwise, users will see "Aflexi CDN" portal themeing. Adjustment needed too to our mini app's
    theme selection code - also read from request for app name.
 #}
<iframe class="mini-portal" src="{{ iframe_url }}" scrolling="auto" width="100%" height="100%">
    <p>It seems that your user agent does not support frames or is currently configured not to display frames. 
    You may, however, <a href="{{ iframe_url }}">visit this link</a> instead.
    </p>
</iframe>
{% endblock %}