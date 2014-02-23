{% extends 'admin.base.volt' %}

{% block content %}
<div id="pad-wrapper" class="form-page">
    <div class="row header">
        <h3>Confirm domain "<em>{{ domain.domain_name }}</em>"</h3>
    </div>
    <form method="post" action="">
        <div class="form-wrapper">
            <div class="row">
                <div class="col-xs-6">
                    <p>
                        We need to verify you actually own this domain. To do so
                        please copy paste the javascript snippet on your site (see
                        the instructions section). Once this is done, we can check
                        the presence of this code on your site to confirm you're
                        the owner.
                    </p>
                    <p>
                        If your site is accessible on "<em>{{ domain.domain_name }}</em>"
                        all you need to do is click <em>confirm</em>. If you need a subdomain
                        (<em>www.</em>, <em>app.</em>, etc.) please specify it and then
                        click <em>confirm</em>.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    <div class="input-group">
                        <input type="text" name="subdomain" class="form-control">
                        <span class="input-group-addon">{{ domain.domain_name }}</span>
                    </div>
                </div>
                <div class="input-group">
                    <input type="submit" class="btn-flat" value="Confirm">
                </div>
            </div>
        </div>
    </form>
</div>

{% endblock %}