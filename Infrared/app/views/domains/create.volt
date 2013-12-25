{% extends 'admin.base.volt' %}

{% block content %}
<div id="pad-wrapper" class="form-page">
    <div class="row header">
        <h3>Register new domain</h3>
    </div>
    <form method="post" action="">
        <div class="form-wrapper">
            <div class="row">
                <div class="field-box col-xs-6">
                    <label>Domain name:</label>
                    <div class="input-group">
                        <span class="input-group-addon">http://</span>
                        <input name="domain_name" placeholder="www.example.com" type="text" class="form-control">
                    </div>                            
                </div>
            </div>
            <div class="row">
                <div class="input-group col-xs-6">
                    <input type="submit" class="btn-flat" value="register">
                </div>
            </div>
        </div>
    </form>
</div>

{% endblock %}