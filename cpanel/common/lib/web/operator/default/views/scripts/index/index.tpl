{% extends "base.tpl" %}
 
{% block title %}Aflexi CDN Enabler{% endblock %}

{% block head %}
  {% parent %}
  <style type="text/css">
    .important { color: #336699; }
  </style>
{% endblock %}

{% block breadcrumbs %}
{% endblock %}

{% block content %}
<ul class="icons
">
    <li id="icon-setup">
        <a href="/aflexi/index.php?module=settings&controller=account">
            <div class="icon-container"><span></span></div>
            <span>Initial Setup</span>
        </a>
    </li>
    <li id="icon-packages">
        <a href="/aflexi/index.php?module=settings&controller=package">
            <div class="icon-container"><span></span></div>
            <span>CDN Packages</span>
        </a>
    </li>
    <li id="icon-users">
        <a href="/aflexi/index.php?module=settings&controller=user">
            <div class="icon-container"><span></span></div>
            <span>CDN Users</span>
        </a>
    </li>
    <li id="icon-bandwidth">
        <a href="/aflexi/index.php?module=settings&controller=bandwidth">
            <div class="icon-container"><span></span></div>
            <span>CDN Bandwidth Usage</span>
        </a>
    </li>
    <li id="icon-sync">
        <a href="/aflexi/index.php?module=settings&controller=sync">
            <div class="icon-container"><span></span></div>
            <span>Synchronize and Repair</span>
        </a>
    </li>
</ul>
{% endblock %}