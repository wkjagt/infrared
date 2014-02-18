{% extends 'front.base.volt' %}

{% block content %}
<section id="marketing-text">
    <div class="col-md-12 col-sm-12">
        <h1>Know where your users click... and when.</h1>
        <p>
            Traditional heatmap tools tell you where your users clicked, but not when. Do
            they click a link within a second? Or after ten seconds? The meaning of those
            clicks is very different. Infrared gives you a real understanding of what those
            clicks actually mean.
         </p>
    </div><!--12-->
</section><!--/marketing-text-->  

<section id="subscribe" class="clearfix">
    <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
         <div class="row">
            <form role="form" action="/login" method="post">
                <div class="col-md-7 col-sm-7">
                    <input type="email" id="fieldEmail" name="email" class="form-control input-lg" placeholder="Email Address" required />
                </div><!--7-->
                <div class="col-md-5 col-sm-5 ">
                    <button class="btn btn-block btn-lg btn-theme">Login or signup</button>
                </div><!--5-->
            </form>
        </div><!--row-->
    </div><!--/6-->
</section><!--subscribe-->  


<section id="features">
    <div class="col-md-4 col-sm-4">
        <div class="feature-box">
            <i class="icon icon-play-circle"></i>                            
            <h2>Animated heatmaps</h2>
            <p>
                Infrared shows your user's clicks in a fluidly animated heatmap.
                Clicks that were registered shortly after page load will appear first.
            </p>
        </div><!--feature-box-->
    </div><!--/3-->
    
    <div class="col-md-4 col-sm-4">
        <div class="feature-box">
            <i class="icon icon-flag"></i>                            
            <h2>Right on your site</h2>
            <p>
                We don't believe you should come back here all the time to see your heatmaps.
                Setup your account, install our chrome extension, and your heatmaps are just a
                click away when you're already on your site or application.
            </p>
        </div><!--feature-box-->
    </div><!--/3-->
    
    <div class="col-md-4 col-sm-4">
        <div class="feature-box">
            <i class="hicon icon-info"></i>                            
            <h2>How does it work?</h2>
            <p>
                Easy. Create an account and tell us the domain of your site or application.
                You paste a JavaScript snippet we'll give you onto your site.
                And start watching the first clicks come in from our chrome extension.
            </p>
        </div><!--feature-box-->
    </div><!--/3-->
    
{#     <div class="col-md-3 col-sm-3">
        <div class="feature-box">
            <i class="icon icon-font"></i>                            
            <h2>Google Web Fonts</h2>
            <p>Talk about one of the biggest features that your new website is going to have.</p>
        </div><!--feature-box-->
    </div><!--/3-->
 #}</section><!--features-->
{% endblock %}