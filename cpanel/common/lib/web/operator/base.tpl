{# base.tpl #}
{% import 'macros/ux.xml' as ux %}
<?xml version="1.0" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="/cPanel_magic_revision_0/combined_optimized.css" />
        <link rel="stylesheet" type="text/css" href="/cPanel_magic_revision_0/themes/x/style_optimized.css" />
        <link rel="stylesheet" type="text/css" href="/aflexi/stylesheets/main.css" />
        <title>Web Host Manager - Aflexi CDN Plugin - {% block title %}{% endblock %}</title>
        <script type="text/javascript" src="/aflexi/javascripts/base/vendor/jquery.js"></script>
        <script type="text/javascript" src="/aflexi/javascripts/base/vendor/jquery-ui.js"></script>
        {% block head %}
        {% endblock %}
    </head>
    <body>
        <div id="pageheader">
            <div id="breadcrumbs">
                <p>
                    <a href="/scripts/command?PFILE=main">Main</a>
                    &nbsp;&gt;&gt;&nbsp;
                    <a href="/aflexi/index.php">Aflexi CDN</a>
                    {% block breadcrumbs %}{% endblock %}
                </p>
            </div>
            <div id="doctitle">
                <h1>
                    <span>{% block logo %}{% endblock %}</span>
                    {% display title %}
                </h1>
            </div>
        </div>
        <div id="content">
            {{ ux.dialog_info(info) }}
            {{ ux.dialog_error(errors) }}
            {{ test }}
            {% block content %}
            {% endblock %}
        </div>
        <div id="footer">
            {% block footer %}
            {% endblock %}
        </div>
    </body>
</html>