<!DOCTYPE html>
<html>
<head>
    <title>Infrared</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">

    <!-- bootstrap -->
    <link href="/assets/admin/css/bootstrap/bootstrap.css" rel="stylesheet" />
    <link href="/assets/admin/css/bootstrap/bootstrap-overrides.css" type="text/css" rel="stylesheet" />

    <!-- libraries -->
    <link href="/assets/admin/css/lib/jquery-ui-1.10.2.custom.css" rel="stylesheet" type="text/css" />
    <link href="/assets/admin/css/lib/font-awesome.css" type="text/css" rel="stylesheet" />

    <!-- global styles -->
    <link rel="stylesheet" type="text/css" href="/assets/admin/css/compiled/layout.css" />
    <link rel="stylesheet" type="text/css" href="/assets/admin/css/compiled/elements.css" />
    <link rel="stylesheet" type="text/css" href="/assets/admin/css/compiled/icons.css" />
    <link rel="stylesheet" type="text/css" href="/assets/admin/css/compiled/skins/dark.css" />

    <!-- this page specific styles -->
    {# <link rel="stylesheet" href="/assets/admin/css/compiled/index.css" type="text/css" media="screen" /> #}
    <link rel="stylesheet" href="/assets/admin/css/overrides.css" type="text/css" media="screen" />

    <!-- open sans font -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css' />

    <!-- lato font -->
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900,300italic,400italic,700italic,900italic' rel='stylesheet' type='text/css' />
    <link href='http://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'>
    {# {% block styles %}{% endblock styles %} #}

    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
    <!-- navbar -->
    <header class="navbar navbar-inverse" role="banner">{% include 'includes/header.volt' %}</header>
    <div id="sidebar-nav">{% include 'includes/sidebar.volt' %}</div>
    <div class="content">
        {% include 'admin.messages.volt' %}
        {% block content %}{% endblock %}
    </div>

    <!-- scripts -->
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script src="/assets/admin/js/bootstrap.min.js"></script>
    <script src="/assets/admin/js/jquery-ui-1.10.2.custom.min.js"></script>
    <!-- knob -->
    <script src="/assets/admin/js/jquery.knob.js"></script>
    <!-- flot charts -->
    <script src="/assets/admin/js/jquery.flot.js"></script>
    <script src="/assets/admin/js/jquery.flot.stack.js"></script>
    <script src="/assets/admin/js/jquery.flot.resize.js"></script>

    <script type="text/javascript">
        $(function () {

            // jQuery Knobs
            $(".knob").knob();

            // jQuery UI Sliders
            $(".slider-sample1").slider({
                value: 100,
                min: 1,
                max: 500
            });
            $(".slider-sample2").slider({
                range: "min",
                value: 130,
                min: 1,
                max: 500
            });
            $(".slider-sample3").slider({
                range: true,
                min: 0,
                max: 500,
                values: [ 40, 170 ],
            });

            

            function showTooltip(x, y, contents) {
                $('<div id="tooltip">' + contents + '</div>').css( {
                    position: 'absolute',
                    display: 'none',
                    top: y - 30,
                    left: x - 50,
                    color: "#fff",
                    padding: '2px 5px',
                    'border-radius': '6px',
                    'background-color': '#000',
                    opacity: 0.80
                }).appendTo("body").fadeIn(200);
            }

            var previousPoint = null;
            $("#statsChart").bind("plothover", function (event, pos, item) {
                if (item) {
                    if (previousPoint != item.dataIndex) {
                        previousPoint = item.dataIndex;

                        $("#tooltip").remove();
                        var x = item.datapoint[0].toFixed(0),
                            y = item.datapoint[1].toFixed(0);

                        var month = item.series.xaxis.ticks[item.dataIndex].label;

                        showTooltip(item.pageX, item.pageY,
                                    item.series.label + " of " + month + ": " + y);
                    }
                }
                else {
                    $("#tooltip").remove();
                    previousPoint = null;
                }
            });
        });
    </script>
    {% block scripts %}{% endblock %}
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-46709630-1', 'useinfrared.com');
      ga('send', 'pageview');

    </script>
</body>
</html>