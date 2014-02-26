
{% set messages = flash.getMessages() %}

{% for type, msgs in messages %}{% for msg in msgs %}
{% if type == 'error' %}
<div class="alert alert-danger"><i class="icon-remove-sign"></i>{{ msg  }}</div>
{% elseif type == 'success' %}
<div class="alert alert-success"><i class="icon-ok-sign"></i>{{ msg }}</div>
{% elseif type == 'notice' %}
<div class="alert alert-info"><i class="icon-alert-sign"></i>{{ msg }}</div>
{% endif %}
{% endfor %}{% endfor %}