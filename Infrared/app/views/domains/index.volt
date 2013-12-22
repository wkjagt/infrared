{% extends 'admin.base.volt' %}

{% block content %}
{% set active_menu = 'domains' %}
<div id="pad-wrapper">
    <div class="row header">
        <h3>Registered domains</h3>
        <div class="col-md-10 col-sm-12 col-xs-12 pull-right">
            {% if domains|length < 2 %}
            <a href="domains/new" class="btn-flat success pull-right">
                <span>&#43;</span>
                NEW DOMAIN
            </a>
            {% endif %}
        </div>
    </div>
    <div class="table-products">
        <div class="row filter-block">
            <p>
                <i class="icon-lightbulb"></i>
                While we're in beta, you'll be able to register a maximum of two domains.
                We hope to remove this limitation as soon as possible.
            </p>
        </div>
        {% if domains|length %}
        <div class="row">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="col-md-3">
                            Domain name
                        </th>
                        <th class="col-md-3">
                            <span class="line"></span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {% for domain in domains %}
                    <tr {% if loop.first %}class="first"{% endif %} {% if loop.last %}class="last"{% endif %}>
                        <td>
                            {{ domain.domain_name }}
                        </td>
                        <td>
                            <ul class="actions">
                                <a class="delete" data-name="{{ domain.domain_name }}" href="/domains/{{ domain.id }}">Delete</a>
                            </ul>
                        </td>
                    </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
        {% endif %}
    </div>
    <!-- end table sample -->
</div>
{% endblock %}

{% block scripts %}
<script type="text/javascript">
    $('a.delete').on('click', function(e){
        e.preventDefault();
        if (confirm("Are you sure you want to delete "+$(this).data('name')+"?")) {
            $.ajax({
                url: $(this).attr('href'),
                method : 'delete',
                success : function(){
                    location.reload();
                }
            });
        }
    });
</script>
{% endblock %}