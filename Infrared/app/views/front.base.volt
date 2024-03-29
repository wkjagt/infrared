<!DOCTYPE html>
<html>
  <head>
    <title>Infrared</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <!-- Bootstrap -->
    <link href="/assets/landing/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/assets/landing/css/font-awesome.min.css">
    <!-- Lato Google Font - For Theme -->        
    <link href='http://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
    <!-- Default Theme Style -->
    <link href="/assets/landing/css/theme-dark.css" rel="stylesheet" media="screen" id="skin">
    <!-- Theme Color  options - alizarin.css, belize-hole.css, carrot.css, turquoise.css, wisteria.css-->
    <link href="/assets/landing/css/theme-alizarin.css" rel="stylesheet" media="screen" id="swatches">
    <!-- Theme color Options Viewer - For Demo Purpose only -->    
    <link href='http://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'>


    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="assets/js/html5shiv.js"></script>
      <script src="assets/js/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
   <div class="container">
        <div class="row">
            {% block header %}
            <header id="logo">
                <div class="col-md-12 col-sm-12">
                    <a href="" title="infrared">infrared</a>
                </div><!--/12-->
            </header>
            {% endblock %}
            
            {% block content %}{% endblock %}

            {% block footer %}
            <footer>            
                <div class="col-md-12 col-sm-12">
{#                     <div class="social-icons">
                        <a href="#"><i class="icon-twitter-sign"></i></a>
                        <a href="#"><i class="icon-facebook-sign"></i></a>
                        <a href="#"><i class="icon-google-plus-sign"></i></a>
                        <a href="#"><i class="icon-pinterest-sign"></i></a>
                    </div>
 #}                    <small><i>2014 Infrared. Built with love in Montreal by <a target="_blank" href="https://twitter.com/wkjagt">@wkjagt</a></i></small>
                </div><!--/12-->
            </footer>
            {% endblock %}

        </div><!--/row-->
    </div><!--/container-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/assets/landing//js/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/assets/landing/js/bootstrap.min.js"></script>
    <!-- Below script is for demo purpose only -->
    <script src="/assets/landing/js/jquery.placeholder.js"></script>
    <script type="text/javascript">$(function(){$('input, textarea').placeholder();});</script>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-46709630-1', 'useinfrared.com');
      ga('send', 'pageview');

    </script>
    <script type="text/javascript" src="http://useinfrared.com/plugin/infrared.js"></script>
    <script type="text/javascript" id="8b4b3ac91873082059ef6daf50269211">
        Infrared.init({
            centered:true
        });
    </script>
  </body>
</html>