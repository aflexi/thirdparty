{% block content %}
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Oops! An Error Has Occurred</title>
        <!--[if IE]>
            <script src="js/base/vendor/html5.js"></script>
        <![endif]-->
        <link rel="stylesheet" type="text/css" media="screen" href="css/base/base.css" />
        <link rel="stylesheet/less" type="text/css" media="screen" href="css/base/error.less" />
        <script type="text/javascript" src="js/base/vendor/less.js"></script>
    </head>
    <body id="error">
        <section>
            <h1>Oops! An error has occurred</h1>
            <p>
                Your request could not be processed at this time due to an error{% if errors %}: <tt>{{ errors[0].message }}</tt>{% endif %}.
                It is likely that the configurations are misplaced or incorrect, please verify them by looking into the <tt>*.yml</tt> files in <tt>/var/cpanel/aflexi</tt>.
            </p>
            <p>
                You may try reinstalling this add-on if this problem persists. <a href="http://support.aflexi.net/" target="_new">Contact support</a> otherwise.
            </p>
        </section>
    </body>
</html>
{% endblock %}