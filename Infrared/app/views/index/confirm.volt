{% extends 'front.base.volt' %}

{% block content %}
<section id="marketing-text">
    <div class="col-md-12 col-sm-12">
        
    </div>
</section>


<section id="features">
    <div >
        <div class="feature-box">
            <i class="icon icon-key"></i>                            
            <h2>{{ message }}</h2>
            <p style="padding-left:200px;padding-right:200px">
                Yup, that's right, no need to remember any passwords! Clicking the link
                in your email will log you in, and keep you logged in until you decide to
                logout. Next time you come back, same thing.
            </p>
            <p>
                <a href="/">got it</a>
            </p>
        </div><!--feature-box-->
    </div><!--/3-->
</section><!--features-->
{% endblock %}