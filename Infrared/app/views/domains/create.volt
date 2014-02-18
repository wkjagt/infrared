{% extends 'admin.base.volt' %}

{% block content %}
<div id="pad-wrapper" class="form-page">
    <div class="row header">
        <h3>Register new domain</h3>
    </div>
    <form method="post" action="">
        <div class="form-wrapper">
            <div class="row">
                <div class="col-xs-6">
                    <p>
                        Please provide your registered domain, not the url of your website. If your website
                        is <i>www.example.com</i>, then put <i>example.com</i>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    <input name="domain_name" placeholder="example.com" type="text" class="form-control ">
                </div>
                <div class="input-group">
                    <input type="submit" class="btn-flat" value="register">
                </div>
            </div>
        </div>
    </form>
</div>

{% endblock %}